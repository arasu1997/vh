<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtAuthMiddleware
{
    public $error, $message;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (TokenExpiredException $e) {
            return response()->json($this->generateErrorResponse('EXPIRED', "Given token is expired"), 419);
        } catch (TokenInvalidException $e) {
            return response()->json($this->generateErrorResponse('INVALID', "Given token is invalid"), 419);
        } catch (JWTException $e) {
            return response()->json($this->generateErrorResponse('ABSENT', "Please provide a token"), 500);
        }

        return $next($request);
    }

    /**
     * @param $code
     * @param $message
     * @return array
     */
    public function generateErrorResponse($code, $message)
    {
        return [
            "status" => 'authentication error',
            "auth_error" => [
                [
                    "code" => strtoupper(Route::currentRouteName()) . '-TOKEN-' . $code,
                    "message" => $message
                ]
            ]
        ];
    }
}
