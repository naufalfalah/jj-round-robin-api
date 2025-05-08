<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\TourRequest;
use App\Models\ClientTour;
use App\Models\Tour;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

/**
 * @group Tour
 *
 * @authenticated
 */
class TourController extends Controller
{
    use ApiResponseTrait;

    /**
     * Finish tour
     */
    public function store(TourRequest $request)
    {
        $userId = auth('api')->id() ?? $request->client_id;
        $tour_id = $request->tour_id;

        $tour = Tour::find($tour_id);
        if (!$tour) {
            return $this->sendErrorResponse('Tour not found', 404);
        }

        ClientTour::firstOrCreate([
            'client_id' => $userId,
            'tour_id' => $tour->id,
        ]);

        ActivityLogHelper::save_activity($userId, "Client tour stored successfully. Tour ID: $tour_id", 'TourController', 'app');

        return $this->sendSuccessResponse('Client tour stored successfully');
    }

    /**
     * Restart client tours
     */
    public function restart(Request $request)
    {
        $userId = auth('api')->id() ?? $request->client_id;

        ClientTour::where('client_id', $userId)->delete();

        ActivityLogHelper::save_activity($userId, 'Client tour restarted successfully.', 'TourController', 'app');

        return $this->sendSuccessResponse('Client tour restarted successfully');
    }
}
