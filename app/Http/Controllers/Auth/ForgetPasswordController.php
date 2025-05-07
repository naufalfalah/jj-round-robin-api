<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class ForgetPasswordController extends Controller
{
    public function forget_password()
    {

        $data = [
            'title' => 'Forget Password',
        ];
        view('auth.passwords.email', $data);
    }

    public function send_email(Request $request)
    {
        $rules = [
            'email' => 'required|string|email|max:255',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $find_email = User::where('email', $request->email)->latest()->first();

        if ($find_email) {
            $data = [
                'redirect_route' => route('auth.add_new_pasword', $find_email->hashid),
            ];
            send_email('verify', $find_email->email, 'Chenge Password Request', $data);
            return redirect()->back()->with('success', 'Password reset link sent successfully. Check your email.');
        } else {
            return redirect()->back()->with('error', 'User with this email does not exist.');
        }
    }

    public function password_screen($id)
    {
        $data = [
            'title' => 'Set New Password',
            'user_id' => $id,
        ];
        return view('auth.passwords.reset', $data);
    }

    public function save_new_password(Request $request)
    {
        $rules = [
            'password' => 'required|min:8|max:12|regex:/^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]+$/',
            'confirm_password' => 'required|same:password'
        ];

        $messages = [
            'password.regex' => 'Invalid Format. Password should be 8 characters, with at least 1 number and special characters.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $user_id = hashids_decode($request->user_id);
        $user = User::find($user_id);

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('auth.login');
    }
}
