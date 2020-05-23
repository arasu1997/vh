<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function authenticate(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        try {
            $user = User::where('email', $request->email)->firstOrFail();
            $expiration = empty($request->remember_until) ? 120 : $request->remember_until;

            JWTAuth::factory()->setTTL($expiration);
            if (!$token = JWTAuth::attempt($credentials)) {
                return controllerErrorCodes('AuthController', currentRouteName(), 'invalid', UNAUTHORIZED);
            }
            return ($user->getDetailsForLoginResponse())->response()
                ->cookie('token', $token, empty($request->remember_until) ? 120 : $request->remember_until, '/', '.kwickmetrics.com', env('SECURE_COOKIE'), env('HTTP_ONLY_COOKIE'), false, env('SAME_SITE_COOKIE'));
        } catch (ModelNotFoundException $exception) {
            return notFoundErrorMessage('AuthController');
        } catch (Throwable $throwable) {
            return exceptionErrorMessage($throwable, 'AuthController');
        }
    }

    /**a
     * $token provides array of information such as user's id ,creation time and expiration time etc
     * $expiration is given as custom claims to set expiry time for the new token
     * @param Request $request
     * @return JsonResponse
     */
    public function refreshAuthToken(Request $request)
    {
        try {
            $token = JWTAuth::getPayload(JWTAuth::getToken())->toArray();
            $user = User::find($token["sub"]);
            $expiration = now()->addMinutes(empty($request->remember_until) ? 60 : $request->remember_until)->timestamp;
            $newToken = JWTAuth::fromUser($user, ['exp' => $expiration]);

            return response()->json(['message' => 'success', 'action' => 'Token refreshed'])->header('Authorization', 'Bearer ' . $newToken);
        } catch (\Exception $exception) {
            return exceptionErrorMessage($exception, 'AuthController');
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function signOut(Request $request)
    {
        try {
            JWTAuth::invalidate();
            return response()->json(['message' => 'success', 'action' => 'user logged out'])
                ->cookie('token', null, -1, '/', 'kwickmetrics.com', env('SECURE_COOKIE'), env('HTTP_ONLY_COOKIE'), false, env('SAME_SITE_COOKIE'));
        } catch (\Exception $exception) {
            return exceptionErrorMessage($exception, 'AuthController');
        }
    }
}
