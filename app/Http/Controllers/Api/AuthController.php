<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\ClientFolder;
use App\Models\User;
use App\Models\UserDeviceToken;
use App\Models\UserOtp;
use App\Traits\ApiResponseTrait;
use App\Traits\GoogleTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

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
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */

     public function register(Request $request)
     {
         $validator = Validator::make($request->all(), [
             'client_name' => 'required|string|max:50',
             'email' => 'required|string|email|max:255|unique:users',
             'phone_number' => 'required|numeric|unique:users',
             // 'agency' => 'required|string|unique:users',
             'agency' => 'required|integer',
             'password' => 'required|string|confirmed|min:8|max:12|regex:/^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]+$/',
         ], ['password.regex' => 'Invalid Format. Password should be 8 characters, with at least 1 number and special characters.',]);
 
         if ($validator->fails()) {
             $errors = $validator->errors();
             $errorMessages = [];
             // $errorMessages = [];
             foreach ($errors->all() as $message) {
                 $errorMessages[] = $message;
             }
 
             return $this->sendErrorResponse(implode("\n ", $errorMessages), 400);
         }

         DB::beginTransaction();
         try {
             $create_user = new User;
             $create_user->client_name = $request->client_name;
             $create_user->email = $request->email;
             $create_user->phone_number  = $request->phone_number;
             $create_user->agency_id = $request->agency;
             // $create_user->industry_id = $request->industry;
             $create_user->password = bcrypt($request->password);
 
             // $folder_name = $create_user->agency.'-'.$create_user->client_name;
             $folder_name = $create_user->client_name . '-' . $create_user->email;
             $create_user->save();
 
 
             $client_folder = new ClientFolder;
             $client_folder->client_id = $create_user->id;
             // $client_folder->folder_name = $folder_name;
             $client_folder->folder_name = $create_user->client_name . '-' . $create_user->id;
             $client_folder->save();
 
             $create_user->save();
 
             $token = auth('api')->attempt(['email' => $create_user->email, 'password' => $request->password]);

             $data = [
                 'user' => [
                     'id' => $create_user->id,
                     'client_name' => $create_user->client_name,
                     'email' => $create_user->email,
                     'phone_number' => $create_user->phone_number,
                     'updated_at' => $create_user->updated_at,
                     'created_at' => $create_user->created_at,
                 ],
                 'token' => $token,
             ];
             DB::commit();
             return $this->sendSuccessResponse('User successfully registered', $data);
         } catch (\Exception $e) {
             return response()->json(['error' => 'Registration Failed: ' . $e->getMessage()], 500);
         }
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
}
