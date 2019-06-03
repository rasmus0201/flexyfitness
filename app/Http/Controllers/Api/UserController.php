<?php

namespace App\Http\Controllers\Api;

use App\User;
use App\Support\Token;
use Illuminate\Http\Request;
use App\Flexybox\FlexyfitnessApi;

class UserController extends Controller
{
    /**
     * Log user in. Create new user if not existing
     * Returns a token for further api requests
     *
     * @param  Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function auth(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required'
        ]);

        $user = new User;
        $user->username = $request->get('username');
        $user->setApiPasswordAttribute($request->get('password'));

        $api = new FlexyfitnessApi($user);

        $data = $api->auth();

        if ($data['status'] !== 200) {
            return $this->unauthorized();
        }

        $token = $user->getApiCredentials();

        User::updateByToken($token);

        // Encrypt token for client storage
        $data['data'] = [
            'token' => Token::encrypt($token)
        ];

        return $this->response($data);
    }

    /**
     * Delete a user an all associated data
     *
     * @param  Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function mydata(Request $request)
    {
        $user = $request->user();
        $data = [
            'data' => [
                'user' => $user->makeHidden(['id']),
                'bookings' => $user->bookings()->get()->makeHidden(['id', 'user_id']),
                'calendars' => $user->calendarWeeks()->get()->makeHidden(['id', 'user_id'])
            ]
        ];

        return $this->response($data);
    }

    /**
     * Delete a user an all associated data
     *
     * @param  Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        // Delete everything related to user
        $request->user()->nuke();

        return $this->response();
    }
}
