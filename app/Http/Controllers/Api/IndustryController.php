<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLogHelper;
use App\Http\Controllers\Controller;
use App\Models\Industry;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @group Industry
 */
class IndustryController extends Controller
{
    use ApiResponseTrait;

    /**
     * Get Industries
     */
    public function getIndustries()
    {
        $industries = Industry::get([
            'id',
            'industries',
            'created_at',
            'updated_at',
        ]);

        if ($industries->isEmpty()) {
            return $this->sendErrorResponse('No industries found.', 404);
        }

        $formattedIndustries = [];

        foreach ($industries as $industry) {
            $formattedIndustries[] = [
                'id' => $industry->id,
                'industries' => $industry->industries,
                'created_at' => $industry->created_at ? $industry->created_at->format('Y-m-d H:i:s') : null,
                'updated_at' => $industry->updated_at ? $industry->updated_at->format('Y-m-d H:i:s') : null,
            ];
        }

        $data = [
            'industries' => $formattedIndustries,
        ];

        return $this->sendSuccessResponse('Industries Fetch Successfully', $data);
    }

    /**
     * Update Industry
     *
     * @authenticated
     */
    public function saveIndustry(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'industry_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendErrorResponse(implode("\n", $validator->errors()->all()), 400);
        }

        $user_id = auth('api')->user()->id;
        $user = User::find($user_id);
        if (!$user) {
            return $this->sendErrorResponse('User Not Find.', 401);
        }
        $user->industry_id = $request->industry_id;
        $user->save();

        ActivityLogHelper::save_activity(auth('api')->id(), 'Save Industry', 'User', 'app');

        return $this->sendSuccessResponse('User Industry Save Successfully');
    }
}
