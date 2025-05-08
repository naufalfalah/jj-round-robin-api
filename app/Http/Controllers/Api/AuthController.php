<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Models\UserDeviceToken;
use App\Traits\ApiResponseTrait;
use App\Traits\GoogleTrait;
use App\Traits\PackageTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

/**
 * @group Authentication
 */
class AuthController extends Controller
{
    use ApiResponseTrait;
    use GoogleTrait;
    use PackageTrait;

    /**
     * Login
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();
        $email = $credentials['email'];

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            ActivityLogHelper::save_activity(null, "Failed Login Attempt [$email]", 'Auth', 'app');

            return $this->sendErrorResponse('User not found. Please register with your email.', 401);
        }

        $user = Auth::guard('api')->user();

        if (is_null($user->email_verified_at)) {
            Auth::guard('api')->logout();

            ActivityLogHelper::save_activity($user->id, "Login Attempt by Unverified User [$email]", 'Auth', 'app');

            return $this->sendErrorResponse('User is not verified. Please verify with your OTP first.', 401);
        }

        ActivityLogHelper::save_activity($user->id, 'User Login', 'Auth', 'app');

        $data = $this->createNewToken($token);

        ActivityLogHelper::save_activity($user->id, 'Token Created', 'Auth', 'app');

        return $this->sendSuccessResponse('User login successfully.', $data);
    }

    /**
     * Logout (Invalidate the token).
     */
    public function logout(Request $request): JsonResponse
    {
        if (isset($request->device_token) && !empty($request->device_token)) {
            $check_device_token = UserDeviceToken::where('device_token', $request->device_token)->where('user_id', auth('api')->id())->first();
            if ($check_device_token) {
                $check_device_token->delete();
            }
        }
        $user_id = auth('api')->id();

        ActivityLogHelper::save_activity($user_id, 'User Logout', 'Auth', 'app');
        auth('api')->logout();

        return $this->sendSuccessResponse('User successfully signed out');
    }

    /**
     * Refresh a token
     */
    public function refresh(): JsonResponse
    {
        try {
            $message = 'JWT Token Refresh Successfully';

            $data = [
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
                'token' => auth('api')->refresh(),
            ];

            $user_id = auth('api')->id();
            ActivityLogHelper::save_activity($user_id, 'Token Refreshed', 'Auth', 'app');

            return $this->sendSuccessResponse($message, $data);
        } catch (TokenExpiredException $e) {
            ActivityLogHelper::save_activity(null, 'Token Refresh Failed', 'Auth', 'app');

            return response()->json(['status' => 'Error', 'message' => 'Token Is Expired', 'need_refresh_token' => true], 403);
        } catch (TokenInvalidException $e) {
            ActivityLogHelper::save_activity(null, 'Token Refresh Failed', 'Auth', 'app');

            return response()->json(['status' => 'Error', 'message' => 'Token Is Invalid'], 403);
        } catch (JWTException $e) {
            ActivityLogHelper::save_activity(null, 'Token Refresh Failed', 'Auth', 'app');

            return response()->json(['status' => 'Error', 'message' => 'Token could not be refreshed'], 403);
        }
    }

    /**
     * Get the token array structure.
     */
    protected function createNewToken(string $token): array
    {
        $user = auth('api')->user();
        $industry = '';
        $agency = '';

        if (!empty($user->user_industry->industries)) {
            $industry = $user->user_industry->industries;
        }

        if (!empty($user->user_agency->name)) {
            $agency = $user->user_agency->name;
        }

        $data = [
            'user' => [
                'id' => $user->id,
                'client_name' => $user->client_name,
                'email' => $user->email,
                'phone_number' => $user->phone_number,
                'agency' => $agency,
                'agency_id' => $user->agency_id,
                'package' => $user->package,
                'industry_id' => $user->industry_id,
                'industry' => $industry,
                'image' => $user->image,
                'updated_at' => $user->updated_at,
                'created_at' => $user->created_at,
            ],
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'token' => $token,
        ];

        return $data;
    }

    /**
     * Redirect to Google login
     */
    public function redirectToGoogle()
    {
        return $this->sendSuccessResponse('Redirect to Google', [
            'url' => Socialite::driver('google')->redirectUrl('google'),
        ]);
    }

    /**
     * Handle Google callback
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to login with Google'], 400);
        }

        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            ['name' => $googleUser->getName(), 'password' => Hash::make(Str::random(16))]
        );

        Auth::login($user);

        // Generate token for the user
        $token = $user->createToken('MobileApp')->accessToken;

        return $this->sendSuccessResponse('Login from Google success', [
            'token' => $token,
            'user' => $user,
        ]);
    }

    /**
     * Redirect to Facebook login
     */
    public function redirectToFacebook()
    {
        return $this->sendSuccessResponse('Redirect to Facebook', [
            'url' => Socialite::driver('facebook')->redirectUrl('facebook'),
        ]);
    }

    /**
     * Handle Facebook callback
     */
    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->stateless()->user();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to login with Facebook'], 400);
        }

        $user = User::firstOrCreate(
            ['email' => $facebookUser->getEmail()],
            ['name' => $facebookUser->getName(), 'password' => Hash::make(Str::random(16))]
        );

        Auth::login($user);

        // Generate token for the user
        $token = $user->createToken('MobileApp')->accessToken;

        return $this->sendSuccessResponse('Login from Facebook success', [
            'token' => $token,
            'user' => $user,
        ]);
    }
}
