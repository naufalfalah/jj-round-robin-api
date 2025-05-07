<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Industry;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Traits\GoogleTrait;
use Illuminate\Support\Facades\Auth;
use App\Models\SubAccount;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;
    use GoogleTrait;
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */

    public function register(Request $request)
    {
        $rules = [
            'client_name' => 'required|string|max:50',
            'phone_number' => 'required|numeric|digits:8',
            'agency' => 'required',
            'industry' => 'required',
            'package' => 'required',
            'address' => 'required',
            'email' => 'required|string|email|max:255|unique:users|regex:/^[^@]+@[^@]+\.[^@]+$/',
            'image' => 'required|image|mimes:png,jpg,max:2048',
            // 'password' => 'required|min:8|max:12|regex:/^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]+$/',
            'password' => 'required|min:8',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }
        try {
            // Start a database transaction
            DB::beginTransaction();

            $create_user = new User;
            $create_user->client_name = $request->client_name;
            $create_user->phone_number = $request->phone_number;
            if ($request->agency == 'other') {
                $agency = new Agency();
                $agency->name = $request->other_agency_name;
                $agency->status = 1;
                $agency->save();
                $create_user->agency_id = $agency->id;
            } else {
                $create_user->agency_id = $request->agency;
            }
            
            $create_user->industry_id = $request->industry;
            $create_user->package = $request->package;
            $create_user->email = $request->email;
            $create_user->address = $request->address;
            $create_user->password = Hash::make($request->password);
            $create_user->email_verified_at = now();
            if ($request->hasFile('image')) {
                $profile_img = uploadSingleFile($request->file('image'), 'uploads/client/profile_images/');
                $create_user->image = $profile_img;
            }
            $create_user->save();

            // $check_admin_account = Admin::where('user_type', 'admin')->where('role_name', 'super_admin')->whereNotNull('google_access_token')->count();
            // if($check_admin_account > 0){
            //     $create_client_sheet = $this->createNewSpreadsheet($request->client_name, $create_user->id);
            //     $create_user->spreadsheet_id = $create_client_sheet;
            //     $create_user->save();
            // }

            $create_sub_account = new SubAccount;
            $create_sub_account->sub_account_name = $create_user->client_name . '(' .  $create_user->email  . ')';
            $create_sub_account->sub_account_url = '';
            $create_sub_account->status = 'Active';
            $create_sub_account->save();

            $create_user->sub_account_id = $create_sub_account->id;
            $create_user->save();
            DB::commit();
            if (Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
                $msg = [
                    'success' => 'Registration successful! Thank you for registering.',
                    'redirect' => route('user.wallet.add'),
                ];
                return response()->json($msg);
            }
            $msg = [
                'success' => 'Registration successful! Thank you for registering.',
                'redirect' => route('client.dashboard'),
            ];
            return response()->json($msg);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors(['error' => $e->getMessage()])->withStatus(500);
        }

    }
    public function check_email(Request $reques)
    {
        $check = User::where('email', $reques->all())->count();
        if ($check) {
            return true;
        } else {
            return false;
        }
    }
    public function success_message()
    {
        $data = [
            'title' => 'Success Message',
        ];
        return view('auth.success_message', $data);
    }

    public function password_screen($id)
    {
        $data = [
            'title' => 'Set Password',
            'user_id' => $id,
        ];
        return view('auth.set_password', $data);
    }

    public function showRegistrationForm()
    {
        $data = [
            'agencies' => Agency::where('status', 1)->where('added_by_id', '!=', 0)->latest()->get(['id','name']),
            'industries' => Industry::latest()->get(['id','industries']),
        ];
        return view('auth.register', $data);
    }
}
