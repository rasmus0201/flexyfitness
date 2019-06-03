<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use App\Support\Token;

class TokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            $token = Token::decrypt($this->findToken($request));
        } catch (\Exception $e) {
            return $this->unauthorized();
        }

        if (!$user = User::authByToken($token)) {
            return $this->unauthorized();
        }

        // Add user to request
        $request->setUserResolver(function () use ($user) {
            return $user;
        });

        return $next($request);
    }

    /**
     * Find a token in the request
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return string
     *
     * @throws \Exception
     */
    private function findToken($request)
    {
        if ($token = $request->get('token')) {
            return $token;
        }

        throw new \Exception('Token not found in request');
    }

    /**
     * Return unauthorized response
     *
     * @return \Illuminate\Http\Response
     */
    private function unauthorized()
    {
        return response()->json(['status' => 401, 'msg' => 'Unauthorized'], 401);
    }
}
