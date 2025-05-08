<?php

namespace App\Http\Controllers\Api;

use App\Constants\OtpConstant;
use App\Helpers\ActivityLogHelper;
use App\Helpers\TextHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserOtp;
use App\Traits\ApiResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * @group Forgot Password
 */
class ForgotPasswordController extends Controller
{
    use ApiResponseTrait;

    /**
     * Verify and send email for the forget password
     */
    public function checkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse(implode("\n", $validator->errors()->all()), 400);
        }

        // Check user
        $user = User::whereEmail($request->email)->latest()->first();
        if (!$user) {
            $email = $request->email;

            ActivityLogHelper::save_activity(null, "Email Check Failed. User Not Found With The Given Email Address [$email]", 'Auth', 'app');

            return $this->sendErrorResponse('User Not Found With The Given Email Address.', 401);
        }

        ActivityLogHelper::save_activity($user->id, "OTP Sent [$user->email]", 'Auth', 'app');

        return $this->sendSuccessResponse('Code Send Successfully', ['email' => $user->email]);
    }

    /**
     * Reset Password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|max:12|regex:/^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]+$/',
            'confirm_password' => 'required|same:password',
        ], [
            'password.regex' => 'Invalid Format. Password should be 8 characters, with at least 1 number and special characters.',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse(implode("\n", $validator->errors()->all()), 400);
        }

        $email = $request->email;
        $user = User::where('email', $email)->first();

        // Check user
        if (!$user->exists()) {
            ActivityLogHelper::save_activity(null, "Password Reset Failed. User Not Found With The Given Email: [$email]", 'Auth', 'app');

            return $this->sendErrorResponse('User Not Found With The Given Email Address.', 401);
        }

        $userOtp = UserOtp::where([
            'user_id' => $user->id,
            'type' => OtpConstant::TYPES['RESET_PASSWORD']['code'],
            'is_verified' => 0,
            'is_expired' => 0,
        ])->latest()->first();

        // HOLD: Check if OTP not expired yet
        // if ($userOtp && $userOtp->created_at->diffInMinutes(now()) < 15) {
        //     return $this->sendErrorResponse('OTP not expired yet.', 401);
        // }

        $metadata = [
            'new_password' => Hash::make($request->password),
        ];
        $this->sendOtp($user, $metadata);

        ActivityLogHelper::save_activity($user->id, "OTP Sent [$user->email]", 'Auth', 'app');

        return $this->sendSuccessResponse('Code Send Successfully', ['email' => $user->email]);
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request)
    {
        $email = $request->email;
        $user = User::where('email', $email)->first();

        // Check user
        if (!$user) {
            ActivityLogHelper::save_activity(null, "Resend OTP Failed - Email Not Found [$email]", 'Auth', 'app');

            return response()->json(['error' => 'User Email Not Found'], 404);
        }

        // Check if RESET_PASSWORD attempt exist
        $userOtp = UserOtp::where([
            'user_id' => $user->id,
            'type' => OtpConstant::TYPES['RESET_PASSWORD']['code'],
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
                'type' => OtpConstant::TYPES['RESET_PASSWORD']['code'],
                'is_verified' => 0,
                'is_expired' => 0,
            ],
            [
                'metadata' => json_encode($metadata),
                'otp' => TextHelper::generateOtp(),
            ]
        );

        Log::info('Sending email to: '.$user->email);
        send_email('verify_otp', $user->email, 'Reset Password Verification Code', ['code' => $userOtp->otp]);

        Log::info('Email sent to: '.$user->email);
        Log::info('Email Subject: Reset Password Verification Code');
    }

    /**
     * Verify OTP
     */
    public function verifyOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'otp' => 'required|numeric|digits:5',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse(implode("\n", $validator->errors()->all()), 400);
        }

        $email = $request->email;
        $user = User::where('email', $email)->first();

        // Check user
        if (!$user->exists()) {
            ActivityLogHelper::save_activity(null, "OTP Verification Failed. User Not Found With The Given Email Address [$email]", 'Auth', 'app');

            return $this->sendErrorResponse('User Not Found With The Given Email Address.', 401);
        }

        $userOtp = UserOtp::where([
            'user_id' => $user->id,
            'type' => OtpConstant::TYPES['RESET_PASSWORD']['code'],
            'is_verified' => 0,
            'is_expired' => 0,
        ])->latest()->first();

        // Check if RESET_PASSWORD attempt exist
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

            ActivityLogHelper::save_activity($user->id, 'Password Reset', 'Auth', 'app');

            return $this->sendSuccessResponse('Password Reset Successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            ActivityLogHelper::save_activity(null, 'Password Reset Failed', 'Auth', 'app');

            // TODO: Change to sendErrorResponse
            return response()->json(['error' => 'Password Reset Failed: '.$e->getMessage()], 500);
        }
    }
}
