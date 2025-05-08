<?php

namespace App\Http\Controllers\Api;

use App\Constants\NotificationConstant;
use App\Constants\OtpConstant;
use App\Helpers\ActivityLogHelper;
use App\Helpers\TextHelper;
use App\Http\Controllers\Controller;
use App\Models\ClientFolder;
use App\Models\User;
use App\Models\UserDeviceToken;
use App\Models\UserNotification;
use App\Models\UserOtp;
use App\Traits\ApiResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * @group Profile
 *
 * @authenticated
 */
class ProfileController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get User Profile
     */
    public function userProfile(Request $request)
    {
        $user_id = auth('api')->id();
        $validator = Validator::make($request->all(), [
            'client_name' => 'required|string|max:50',
            'phone_number' => 'required|unique:users,phone_number,'.$user_id,
            'email' => 'required|email|unique:users,email,'.$user_id,
            'address' => 'string',
            'agency' => 'integer',
            'industry' => 'integer',
            'package' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse(implode("\n", $validator->errors()->all()), 400);
        }

        try {
            DB::beginTransaction();
            $user = User::find($user_id);

            if (!$user) {
                return $this->sendErrorResponse('User Not Found. Please Login First', 400);
            }

            if ($request->hasFile('profile_image')) {
                $profile_image = uploadSingleFile($request->file('profile_image'), 'uploads/user_images/profile/', 'png,jpeg,jpg');
                if (is_array($profile_image)) {
                    return response()->json($profile_image);
                }
                if (file_exists($user->image)) {
                    @unlink($user->image);
                }
                $user->image = $profile_image;
            }

            $folder_new_name = "{$user->client_name}-{$user->id}";
            $get_local_folder = ClientFolder::where('client_id', $user_id)
                ->whereNull('parent_folder_id')
                ->latest()
                ->first();

            if ($get_local_folder) {
                $get_local_folder->update(['folder_name' => $folder_new_name]);
            }

            $user->client_name = $request->client_name;
            $user->phone_number = $request->phone_number;
            $user->email = $request->email;

            if ($request->address) {
                $user->address = $request->address;
            }

            if ($request->agency) {
                $user->agency_id = $request->agency;
            }

            if ($request->industry) {
                $user->industry_id = $request->industry;
            }

            $user->package = $request->package;
            $user->save();

            ActivityLogHelper::save_activity($user_id, 'Update User Profile', 'User', 'app');

            DB::commit();

            $data = [
                'user' => $user->only([
                    'id',
                    'client_name',
                    'email',
                    'phone_number',
                    'agency_id',
                    'package',
                    'address',
                    'industry_id',
                    'image',
                    'updated_at',
                    'created_at',
                ]) + [
                    'agency' => $user->user_agency->name ?? '',
                    'industry' => $user->user_industry->industries ?? '',
                ],
            ];

            return $this->sendSuccessResponse('User Profile Updated successfully', $data);
        } catch (\Exception $e) {
            DB::rollBack();

            // TODO: Change to sendErrorResponse
            return response()->json(['error' => 'Update Profile: '.$e->getMessage()], 500);
        }
    }

    /**
     * Change Password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // HOLD: old password verification
            // 'old_password' => 'required|string',
            'password' => 'required|string|min:8|max:12|regex:/^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]+$/',
            'confirm_password' => 'required|same:password',
        ], [
            'password.regex' => 'Invalid Format. Password should be 8 characters, with at least 1 number and special characters.',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse(implode("\n", $validator->errors()->all()), 400);
        }

        $userId = auth('api')->id();
        $user = User::find($userId);

        $userOtp = UserOtp::where([
            'user_id' => $user->id,
            'type' => OtpConstant::TYPES['CHANGE_PASSWORD']['code'],
            'is_verified' => 0,
            'is_expired' => 0,
        ])->latest()->first();

        // HOLD: Check if OTP not expired yet
        // if ($userOtp && $userOtp->created_at->diffInMinutes(now()) < 15) {
        //     return $this->sendErrorResponse('OTP not expired yet.', 401);
        // }

        // HOLD: old password verification
        // if (!Hash::check($request->old_password, $user->password)) {
        //     return $this->sendErrorResponse('Please Enter Correct Current Password', 400);
        // }

        // HOLD: same password verification
        // if ($request->old_password === $request->password) {
        //     return $this->sendErrorResponse('Old password & password are the same', 400);
        // }

        $metadata = [
            'new_password' => Hash::make($request->password),
        ];
        $this->sendOtp($user, $metadata);

        ActivityLogHelper::save_activity($user->id, "OTP Sent [$user->email]", 'Auth', 'app');

        return $this->sendSuccessResponse('Code Send Successfully');
    }

    /**
     * Resend OTP
     */
    public function resendOtp()
    {
        $userId = auth('api')->id();
        $user = User::find($userId);

        // Check if CHANGE_PASSWORD attempt exist
        $userOtp = UserOtp::where([
            'user_id' => $user->id,
            'type' => OtpConstant::TYPES['CHANGE_PASSWORD']['code'],
            'is_verified' => 0,
        ])->latest()->first();

        // Check if OTP not expired yet
        if ($userOtp && !$userOtp->is_expired) {
            // HOLD: Check if OTP not expired yet
            // if (Carbon::parse($userOtp->created_at)->diffInMinutes(now()) < 15) {
            //     return $this->sendErrorResponse('OTP not expired yet.', 401);
            // }

            if (Carbon::parse($userOtp->created_at)->diffInMinutes(now()) >= 15) {
                $userOtp->is_expired = true;
                $userOtp->save();
            }
        }

        $this->sendOtp($user, json_decode($userOtp->metadata));

        ActivityLogHelper::save_activity($user->id, "OTP Resent [$user->email]", 'Auth', 'app');

        return $this->sendSuccessResponse('OTP has been resent to your email.', ['email' => $user->email]);
    }

    public function sendOtp($user, $metadata = [])
    {
        $userOtp = UserOtp::updateOrCreate(
            [
                'user_id' => $user->id,
                'type' => OtpConstant::TYPES['CHANGE_PASSWORD']['code'],
                'is_verified' => 0,
                'is_expired' => 0,
            ],
            [
                'metadata' => json_encode($metadata),
                'otp' => TextHelper::generateOtp(),
            ]
        );

        Log::info('Sending email to: '.$user->email);
        send_email('verify_otp', $user->email, 'Change Password Verification Code', ['code' => $userOtp->otp]);

        Log::info('Email sent to: '.$user->email);
        Log::info('Email Subject: Change Password Verification Code');
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'otp' => 'required|numeric|digits:5',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse(implode("\n", $validator->errors()->all()), 400);
        }

        $userId = auth('api')->id();
        $user = User::find($userId);

        $userOtp = UserOtp::where([
            'user_id' => $user->id,
            'type' => OtpConstant::TYPES['CHANGE_PASSWORD']['code'],
            'is_verified' => 0,
            'is_expired' => 0,
        ])->latest()->first();

        // Check if CHANGE_PASSWORD attempt exist
        if (!$userOtp) {
            // TODO: Change to sendErrorResponse
            return response()->json([
                'errors' => ['otp' => ['No valid OTP found. Please request a new one.']],
            ], 404);
        }

        // Check if OTP expired
        if (Carbon::parse($userOtp->created_at)->diffInMinutes(now()) >= 15) {
            $userOtp->is_expired = true;
            $userOtp->save();

            return $this->sendErrorResponse('OTP has expired.', 401);
        }

        // Check if OTP not matches
        if ($userOtp->otp != $request->otp) {
            ActivityLogHelper::save_activity($user->id, 'Invalid OTP Verification Attempt', 'Auth', 'app');

            // TODO: Change to sendErrorResponse
            return response()->json([
                'errors' => ['otp' => ['Invalid OTP. Please try again.']],
            ], 400);
        }

        DB::beginTransaction();
        try {
            $userOtp->is_verified = true;
            $userOtp->save();

            // Change password
            $metadata = json_decode($userOtp->metadata);
            $newPassword = $metadata->new_password;
            $user->password = $newPassword;
            $user->save();

            DB::commit();

            ActivityLogHelper::save_activity($user->id, 'Password Change', 'Auth', 'app');

            return $this->sendSuccessResponse('Password Change Successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            ActivityLogHelper::save_activity(null, 'Password Change Failed', 'Auth', 'app');

            return response()->json(['error' => 'Password Change Failed: '.$e->getMessage()], 500);
        }
    }

    public function deviceToken(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_token' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse($validator->errors()->first('device_token'), 400);
        }

        $user_id = auth('api')->id();
        $device_token = $request->device_token;

        $check_device_token = UserDeviceToken::firstOrNew(
            [
                'user_id' => $user_id,
                'device_token' => $device_token,
            ]
        );

        $check_device_token->save();

        $message = $check_device_token->wasRecentlyCreated ? 'User Device Token Saved Successfully' : 'User Device Token Updated Successfully';
        ActivityLogHelper::save_activity($user_id, $message, 'UserDeviceToken', 'app');

        return $this->sendSuccessResponse($message, $check_device_token);
    }

    public function userNotification(Request $request)
    {
        $userId = auth('api')->id() ?? auth('web')->id();
        $userNotification = UserNotification::firstOrCreate(
            [
                'client_id' => $userId,
            ],
        );

        return $this->sendSuccessResponse('User Notification Fetched Successfully');
    }

    public function userNotificationStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'types' => 'array|required',
            'types.*' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            // TODO: Change to sendErrorResponse
            return ['errors' => $validator->errors()];
        }

        $types = $request->types ?? [];
        $notificationTypes = NotificationConstant::getTypes();

        $invalidTypes = array_diff($types, $notificationTypes);
        if (!empty($invalidTypes)) {
            return $this->sendErrorResponse('Invalid notification types: '.implode(', ', $invalidTypes));
        }

        $userId = auth('api')->id() ?? auth('web')->id();

        UserNotification::updateOrCreate(
            [
                'client_id' => $userId,
            ],
            [
                'notification_types' => implode(',', $types),
            ],
        );

        return $this->sendSuccessResponse('User Notification Updated Successfully');
    }
}
