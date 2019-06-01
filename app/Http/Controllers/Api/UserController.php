<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Api;
use App\Helpers\Helper;
use App\Helpers\ApiCache;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;

class UserController extends Controller
{
    public function auth(Request $request)
    {
        // This token is the "plain text" credentials,
        // We are going to encrypt them using AES-256-CBC
        $token = rawurldecode($request->get('token'));

        if (!Helper::tokenValid($token)) {
            return response()->json(['status' => 401, 'msg' => 'Unauthorized'], 401);
        }

        $cache = new ApiCache($token, storage_path(env('API_CACHE')));

        if ($data = $cache->getFile('auth.json', '48 hours')) {
            $data['cached'] = true;
            return response()->json($data, $data['status']);
        }

        $api = new Api($token);

        $data = $api->auth();
        $data['cached'] = false;

        if ($data['status'] == 200) {
            // Cache response

            // Disabled for now
            // $cache->cacheFile('auth.json', $data);
        }

        $data['data'] = [
            'token' => Crypt::encrypt($token)
        ];

        $request->session()->put('token', $data['data']['token']);

        return response()->json($data, $data['status']);
    }

    public function delete()
    {
        try {
            $token = $request->get('token');
            $startOfWeek = Helper::startOfWeek($timestamp);

            $cache = new ApiCache($token, storage_path(env('API_CACHE')));

            //User requested deletion
            $deleted = $cache->deleteUserDir();

            $res = [
                'status'    => $deleted ? 200 : 422,
                'cached'    => false,
                'msg'       => $deleted ? 'Success dine data blev slettet.' : 'Fejl, prøv igen senere.',
                'data'      => []
            ];

            return $response->withJson($res, $res['status']);
        } catch (\Exception $e) {
            return response()->json(['status' => 401, 'msg' => 'Wrong token'], 401);
        }
    }
}
