<?php

namespace App\Http\Middleware;

use Illuminate\Support\Facades\Auth;
use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\TokenBlacklistedException;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;

class JWT extends BaseMiddleware
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
            JWTAuth::parseToken()->authenticate(); //check if token is valid
            return  $next($request);
        } catch (TokenBlacklistedException $e) {
            return response()->json(['success' => false, 'msg' => 'token can not be reused'], 401);
        } catch (TokenExpiredException $e) {

            $refreshed = JWTAuth::refresh(JWTAuth::getToken());
            $user = JWTAuth::setToken($refreshed)->toUser();
            header('Authorization: Bearer ' . $refreshed);

        } catch (TokenInvalidException $e) {

            return response()->json(['success' => false, 'msg' => 'invalid token'], 401);
        } catch (JWTException $e) {

            return response()->json(['success' => false, 'msg' => 'No token found'], 401);
        }
        return  $next($request);
    }
}
