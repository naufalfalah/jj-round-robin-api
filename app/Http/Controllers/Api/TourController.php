<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClientTour;
use App\Models\Tour;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function store(Request $request)
    {
        $tour = Tour::find($request->tour_id);
        if (!$tour) {
            return false;
        }

        ClientTour::firstOrCreate([
            'client_id' => $request->client_id,
            'tour_id' => $tour->id,
        ]);

        return true;
    }
}
