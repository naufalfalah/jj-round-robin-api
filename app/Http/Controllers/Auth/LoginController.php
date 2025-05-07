<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ClientTour;
use App\Models\Tour;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    // use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:web')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $a = $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if (!Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password], $request->remember)) {
            return redirect()->back()->withInput()->withErrors(['email' => 'These credentials do not match our records.']);
        }

        $route = 'user.wallet.add';
        
        $tour = Tour::firstOrCreate(['code' => 'FINISH_2'], ['name' => 'Finish']);
        $client_tour = ClientTour::where(['client_id' => auth('web')->user()->id, 'tour_id' => $tour->id])->first();
        if (!$client_tour) {
            $route = 'user.ads.all';
        }
        
        $tour = Tour::firstOrCreate(['code' => 'FINISH_1'], ['name' => 'Finish']);
        $client_tour = ClientTour::where(['client_id' => auth('web')->user()->id, 'tour_id' => $tour->id])->first();
        if (!$client_tour) {
            $route = 'user.wallet.transfer_funds';
        }
        
        $tour = Tour::firstOrCreate(['code' => 'AFTER_TOPUP'], ['name' => 'After Topup']);
        $client_tour = ClientTour::where(['client_id' => auth('web')->user()->id, 'tour_id' => $tour->id])->first();
        if (!$client_tour) {
            $route = 'user.wallet.transaction_report';
        }
        
        $tour = Tour::firstOrCreate(['code' => 'START_3'], ['name' => 'Get Started']);
        $client_tour = ClientTour::where(['client_id' => auth('web')->user()->id, 'tour_id' => $tour->id])->first();
        if (!$client_tour) {
            $route = 'user.wallet.add';
        }
        
        $tour = Tour::firstOrCreate(['code' => 'START_2'], ['name' => 'Get Started']);
        $client_tour = ClientTour::where(['client_id' => auth('web')->user()->id, 'tour_id' => $tour->id])->first();
        if (!$client_tour) {
            $route = 'user.ads.add';
        }

        $tour = Tour::firstOrCreate(['code' => 'START_1'], ['name' => 'Get Started']);
        $client_tour = ClientTour::where(['client_id' => auth('web')->user()->id, 'tour_id' => $tour->id])->first();
        if (!$client_tour) {
            $route = 'user.wallet.add';
        }

        return redirect()->route($route);
    }

    public function logout(Request $request)
    {
        auth('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/login');
    }
}
