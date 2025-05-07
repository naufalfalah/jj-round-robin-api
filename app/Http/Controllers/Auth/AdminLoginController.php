<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminLoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/web_admin/login';

    public function __construct()
    {
        $this->middleware('guest:admin')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.admin.login');
    }

    public function login(Request $request)
    {
        $a = $this->validate($request, [
            'username' => 'required',
            'password' => 'required'
        ]);

        $usernameOrEmail = $request->input('username');
        $password = $request->input('password');

        // Check if the input is a valid email address
        $credentials = filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)
            ? ['email' => $usernameOrEmail, 'password' => $password]
            : ['username' => $usernameOrEmail, 'password' => $password];

        // Attempt to log in
        if (!Auth::guard('admin')->attempt($credentials, $request->remember)) {
            // Authentication failed
            return redirect()->back()->withInput($request->only('username', 'remember'))
                ->withErrors(['username' => 'These credentials do not match our records.']);
        }
        return redirect()->route('admin.home');
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        Session()->forget('sub_account_id');
        return redirect()->route('admin.login');
    }

    public function change_password()
    {

        $data = [
            'title' => 'Forget Password',
        ];
        return view('auth.admin.email', $data);
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

        $find_email = Admin::where('email', $request->email)->latest()->first();

        if ($find_email) {
            $data = [
                'redirect_route' => route('admin.add_new_pasword', $find_email->hashid),
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
        return view('auth.admin.reset', $data);
    }

    public function save_new_password(Request $request)
    {
        $rules = [
            'password' => 'required|min:8|max:12',
            'confirm_password' => 'required|same:password'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $user_id = hashids_decode($request->user_id);
        $user = Admin::find($user_id);

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('admin.login');
    }
}
