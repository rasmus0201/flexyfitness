<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\Helper;

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
            $token = $this->findToken($request);
        } catch (\Exception $e) {
            return response()->json(['status' => 401, 'msg' => 'Unauthorized.'], 401);
        }

        // Add decoded token to request
        $request->request->add(['token' => $token]);

        return $next($request);
    }

    private function findToken($request)
    {
        if ($token = $request->get('token')) {
            return Helper::decryptToken($token);
        }

        throw new \Exception('Token not found in request');
    }
}
