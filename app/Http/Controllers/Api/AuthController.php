<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\User;
use App\Models\UserOtp;
use Illuminate\Http\Request;
use App\Models\UserDeviceToken;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Traits\GoogleTrait;

class AuthController extends Controller
{
    use ApiResponseTrait;
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    use GoogleTrait;

    public function login(Request $request){

    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);


        if ($validator->fails()) {
            $errors = $validator->errors();

            $errorMessages = [];
            foreach ($errors->all() as $message) {
                $errorMessages[] = $message;
            }

            return $this->sendErrorResponse(implode("\n ", $errorMessages),400);
        }

        if (! $token = auth('api')->attempt($validator->validated())) {
            return $this->sendErrorResponse('User Is Not Found In My Record. Please Register With Your Email',401);
        }

        $message = "User Login Successfully";
        return $this->createNewToken($token,$message);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request) {

        if(isset($request->device_token) && !empty($request->device_token)){
            $check_device_token = UserDeviceToken::where('device_token', $request->device_token)->where('user_id', auth('api')->id())->first();
            if($check_device_token){
                $check_device_token->delete();
            }
        }

        auth('api')->logout();

        return $this->sendSuccessResponse('User successfully signed out');
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        try {
            $message = "JWT Token Refresh Successfully";

            $data = [
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
                'token' => auth('api')->refresh(),
            ];

            return $this->sendSuccessResponse($message,$data);
        } catch (TokenExpiredException $e) {
            return response()->json(['status' => 'Error', 'message' => 'Token Is Expired', 'need_refresh_token' => true],403);
        } catch (TokenInvalidException $e) {
            return response()->json(['status' => 'Error', 'message' => 'Token Is Invalid'],403);
        } catch (JWTException $e) {
            return response()->json(['status' => 'Error', 'message' => 'Token could not be refreshed'],403);
        }
    }
    
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token, $message){

        $user = auth('api')->user();
        $industry = '';
        $agency = '';
        if(!empty($user->user_industry->industries)){
            $industry = $user->user_industry->industries;
        }

        if(!empty($user->user_agency->name)){
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
                'industry'  => $industry,
                'image'  => $user->image,
                'updated_at' => $user->updated_at,
                'created_at' => $user->created_at,
            ],
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'token' => $token,
        ];

        return $this->sendSuccessResponse($message,$data);
    }

    public function resendOtp()
    {
        $user_id = auth('api')->id();
        $user = User::find($user_id);
        $otpcode = UserOtp::where('user_id', $user_id);
        if ($otpcode->count() == 0) {
            $newOpt = new UserOtp();
            $newOpt->user_id = $user->id;
            $newOpt->is_expired = 0;
            $newOpt->otp = mt_rand(1111, 9999);
            $newOpt->save();

            $data = array(
                'code' => $newOpt->otp,
            );

            send_email('verify_otp', $user->email, 'Verification Code', $data);

            return $this->sendSuccessResponse('Verification Code Resent Successfully',[]);

        } else {
            $otpcode = $otpcode->latest()->first();
            $otpcode->is_expired = 0;
            $otpcode->otp = mt_rand(1111, 9999);
            $otpcode->save();

            $data = array(
                'code' => $otpcode->otp,
            );

            send_email('verify_otp', $user->email, 'Verification Code', $data);

            return $this->sendSuccessResponse('Verification Code Resent Successfully',[]);
        }
    }
}
