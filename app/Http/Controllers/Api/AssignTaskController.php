<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Designer;
use App\Models\DesignerImage;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

/**
 * @group Assign Task
 */
class AssignTaskController extends Controller
{
    use ApiResponseTrait;

    public function getDesignersWithImages(Request $request)
    {
        $userId = auth('api')->id();

        $client = User::find($userId);
        if (!$client) {
            return $this->sendErrorResponse('Client not found.', 404);
        }

        try {
            $designers = Designer::all();
            $response = $designers->map(function ($designer) {
                $images = DesignerImage::where('designer_id', $designer->id)
                    ->take(3)
                    ->pluck('image_path')
                    ->map(function ($imagePath) {
                        return url($imagePath);
                    });

                return [
                    'id' => $designer->id,
                    'name' => $designer->name,
                    'desc' => $designer->description,
                    'profile_image' => $designer->profile_image ? url($designer->profile_image) : null, // Profile image ke liye bhi
                    'images' => $images,
                ];
            });

            return response()->json([
                'base_url' => url('/'),
                'data' => $response,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong!',
                'message' => $e->getMessage(),
            ], 500);
        }

    }

    public function getDesignersWithImagestwo(Request $request)
    {
        $userId = auth('api')->id();

        $client = User::find($userId);
        if (!$client) {
            return $this->sendErrorResponse('Client not found.', 404);
        }

        try {
            $id = $request->input('id');
            $page = $request->input('page', 1);
            $perPage = 10;

            $query = Designer::query();
            if ($id) {
                $query->where('id', $id);
            }

            $designers = $query->paginate($perPage, ['*'], 'page', $page);

            $response = $designers->map(function ($designer) {
                $images = DesignerImage::where('designer_id', $designer->id)
                    ->take(3)
                    ->pluck('image_path')
                    ->map(function ($imagePath) {
                        return url($imagePath);
                    });

                return [
                    'id' => $designer->id,
                    'name' => $designer->name,
                    'desc' => $designer->description,
                    'profile_image' => $designer->profile_image ? url($designer->profile_image) : null,
                    'images' => $images,
                ];
            });

            return response()->json([
                'base_url' => url('/'),
                'data' => $response,
                'pagination' => [
                    'current_page' => $designers->currentPage(),
                    'last_page' => $designers->lastPage(),
                    'total' => $designers->total(),
                    'per_page' => $perPage,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong!',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
