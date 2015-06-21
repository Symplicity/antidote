<?php

namespace app\Http\Middleware;

use JWT;
use Illuminate\Support\Facades\Config;
use Closure;
use Illuminate\Contracts\Auth\Guard;

class AuthMiddleware
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->header('Authorization')) {
            $token = explode(' ', $request->header('Authorization'))[1];
            $payload = (array) JWT::decode($token, Config::get('app.token_secret'), array('HS256'));

            if ($payload['exp'] < time()) {
                return response()->json(['message' => 'Token has expired']);
            }

            $request['user'] = $payload;

            return $next($request);
        } else {
            return response()->json(['message' => 'Please make sure your request has an Authorization header'], 401);
        }
    }
}
