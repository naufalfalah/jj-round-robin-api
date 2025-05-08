<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Traits\ApiResponseTrait;

/**
 * @group Agency
 */
class AgencyController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get Agencies
     */
    public function getAgencies()
    {
        $agencies = Agency::where('status', 1)
            ->get(['id', 'name']);

        if ($agencies->isEmpty()) {
            return $this->sendErrorResponse('No agencies found.', 404);
        }

        $data = [
            'agencies' => $agencies,
        ];

        return $this->sendSuccessResponse('Agencies Fetch Successfully', $data);
    }
}
