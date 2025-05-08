<?php

namespace App\Http\Controllers\Api;

use App\Constants\OtpConstant;
use App\Helpers\ActivityLogHelper;
use App\Helpers\TextHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\OtpRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\ClientFolder;
use App\Models\User;
use App\Models\UserOtp;
use App\Traits\ApiResponseTrait;
use App\Traits\PackageTrait;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * @group Register
 */
class RegisterController extends Controller
{
    use ApiResponseTrait;
    use PackageTrait;

    /**
     * Register
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $create_user = User::create([
                'client_name' => $request->client_name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'agency_id' => $request->agency,
                'industry_id' => $request->industry,
                'password' => Hash::make($request->password),
            ]);

            // TEMP: Add default package
            $this->setDefaultPackage($create_user->id);

            ClientFolder::create([
                'client_id' => $create_user->id,
                'folder_name' => $create_user->client_name.'-'.$create_user->id,
            ]);

            $token = auth('api')->attempt([
                'email' => $create_user->email,
                'password' => $request->password,
            ]);

            $this->sendOtp($create_user);

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

            ActivityLogHelper::save_activity($create_user->id, 'User Registered', 'Auth', 'app');

            return $this->sendSuccessResponse('User successfully registered', $data);
        } catch (\Exception $e) {
            DB::rollBack();

            ActivityLogHelper::save_activity(null, 'Registration Failed', 'Auth', 'app');

            return response()->json(['error' => 'Registration Failed: '.$e->getMessage()], 500);
        }
    }

    /**
     * Resend OTP
     */
    public function resendOtp(Request $request): JsonResponse
    {
        $email = $request->email;
        $user = User::where('email', $email)->first();

        // Check user
        if (!$user) {
            ActivityLogHelper::save_activity(null, "Resend OTP Failed - Email Not Found [$email]", 'Auth', 'app');

            // TODO: Change to sendErrorResponse
            return response()->json(['error' => 'User Email Not Found'], 404);
        }

        // Check if user has verified
        if ($user->email_verified_at) {
            return $this->sendErrorResponse('User already verified.', 401);
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

        $this->sendOtp($user);

        ActivityLogHelper::save_activity($user->id, 'OTP Resent', 'Auth', 'app');

        // TODO: Change to sendSuccessResponse
        return response()->json(['success' => 'OTP has been resent to your email.']);
    }

    public function sendOtp($user): void
    {
        $userOtp = UserOtp::firstOrCreate(
            [
                'user_id' => $user->id,
                'type' => OtpConstant::TYPES['REGISTRATION']['code'],
                'is_verified' => 0,
                'is_expired' => 0,
            ],
            [
                'otp' => TextHelper::generateOtp(),
            ]
        );

        Log::info('Sending email to: '.$user->email);
        $data = [
            'otp' => $userOtp->otp,
            'name' => $user->client_name,
        ];

        Mail::send('emails.otp_verification', $data, function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Verify Your Email to Complete Registration');
        });

        Log::info('Email sent to: '.$user->email);
        Log::info('Email Subject: Verify Your Email to Complete Registration');
    }

    /**
     * Verify OTP
     *
     * @description Verifies the OTP for user authentication and email verification.
     *
     * This function has been enhanced for improved readability, error handling,
     * and reduced redundancy. Activity logging is integrated to track.
     * However, the `verifyOtp` function contains some bugs
     * that need to be addressed for optimal performance and reliability.
     * significant user actions. Authored by Muhammad Wajahat.
     */
    public function verifyOtp(OtpRequest $request): JsonResponse
    {
        $userId = $request->user_id;
        $user = User::find($userId)->first();

        // Check user
        if (!$user->exists()) {
            ActivityLogHelper::save_activity(null, "OTP Verification Failed. User Not Found With The Given Email Address [$user->email]", 'Auth', 'app');

            return $this->sendErrorResponse('User Not Found With The Given ID.', 401);
        }

        $userOtp = UserOtp::where([
            'user_id' => $user->id,
            'type' => OtpConstant::TYPES['REGISTRATION']['code'],
            'is_verified' => 0,
            'is_expired' => 0,
        ])->latest()->first();

        // Check if REGISTRATION attempt exist
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

            $user->email_verified_at = now();
            $user->save();

            DB::commit();

            ActivityLogHelper::save_activity($user->id, 'Email Verified', 'Auth', 'app');

            return $this->sendSuccessResponse('Email verified successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            ActivityLogHelper::save_activity(null, 'Password Reset Failed', 'Auth', 'app');

            // TODO: Change to sendErrorResponse
            return response()->json(['error' => 'Password Reset Failed: '.$e->getMessage()], 500);
        }
    }
}
