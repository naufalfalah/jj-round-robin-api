<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ClientTour;

class ClientTourController extends Controller
{
    public function restart()
    {
        ClientTour::where('client_id', auth('web')->id())->delete();

        return redirect()->route('user.wallet.add');
    }
}
