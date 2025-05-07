<?php

namespace App\Http\Controllers\Administrator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\GoogleTrait;
use App\Models\Admin;
use App\Models\GoogleAccount;
use Illuminate\Support\Facades\Auth;

class GoogleAccountController extends Controller
{
    use GoogleTrait;

    public function index()
    {
        if (Auth::user('admin')->role_name != 'super_admin') {
            abort(403, 'Unauthorized action.');
        }

        $data = [
            'breadcrumb_main' => 'Settings',
            'breadcrumb' => 'Google Account Connectivity',
            'title' => 'Google Account Connectivity',
            'google_accounts' => GoogleAccount::all(),
        ];

        return view('admin.google_connect')->with($data);
    }

    public function oauth(Request $request)
    {
        /**
         * Get authcode from the query string
         */
        $authCode = urldecode($request->input('code'));
        /**
         * Google client
         */
        $client = $this->getClient($user = false);

        /**
         * Exchange auth code for access token
         * Note: if we set 'access type' to 'force' and our access is 'offline', we get a refresh token. we want that.
         */
        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);

        /**
         * Set the access token with google. nb json
         */
        $client->setAccessToken(json_encode($accessToken));

        /**
         * Get user's data from google
         */
        $service = new \Google\Service\Oauth2($client);
        $userFromGoogle = $service->userinfo->get();
        
        if (auth('admin')->check()) {
            $admin = Admin::find(auth('admin')->id());
            $admin->provider_id = $userFromGoogle->id;
            $admin->provider_name = 'google';
            $admin->google_access_token = json_encode($accessToken);
            $admin->save();

            // Save into google accounts table
            GoogleAccount::updateOrCreate([
                'email' => $userFromGoogle->email,
            ], [
                'name' => $userFromGoogle->name,
                'google_id' => $userFromGoogle->id,
                'access_token' => json_encode($accessToken),
            ]);
        }

        return redirect(route('admin.setting.google_account'));
    }

    public function getAuthUrl()
    {
        $client = $this->getClient($user = false);

        /**
         * Generate the url at google we redirect to
         */
        $authUrl = $client->createAuthUrl();

        return redirect($authUrl);
    }

    public function refresh_token($id)
    {
        $googleAccount = GoogleAccount::find($id);
        $refreshToken = $this->checkRefreshTokenNew($googleAccount);
        
        if (!$refreshToken) {
            return redirect()->back();
        }

        return redirect()->back();
    }

    public function disconnect()
    {
        $admin = Admin::find(auth('admin')->id());
        $admin->provider_id = null;
        $admin->provider_name = null;
        $admin->google_access_token = null;
        $admin->save();

        return redirect(route('admin.setting.google_account'));
    }
}
