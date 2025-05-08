<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EBook;
use App\Models\EbookActivity;
use App\Models\User;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group E-Book
 */
class EbookController extends Controller
{
    use ApiResponseTrait;

    /**
     * Fetch EbookActivity records with pagination based on request JSON.
     */
    public function all_ebooks(Request $request): JsonResponse
    {
        $userId = auth('api')->id();
        $client = User::find($userId);

        try {
            $user = User::find($userId);

            if (!$user) {
                return $this->sendErrorResponse('User Not Found. Please Login First', 400);
            }

            // Pagination setup
            $page = $request->input('page', 1);
            $limit = 10;
            $offset = ($page - 1) * $limit;

            // Fetch eBooks with pagination
            $activities = EBook::offset($offset)
                ->limit($limit)
                ->get();

            $totalRecords = EBook::count();

            return response()->json([
                'success' => true,
                'data' => $activities,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total' => $totalRecords,
                    'last_page' => ceil($totalRecords / $limit),
                ],
            ]);
        } catch (\Exception $e) {
            // Handle exception gracefully
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function file_view($slug, $id)
    {
        try {
            $userId = auth('api')->id();
            $client = User::find($userId);

            // Check if client exists
            if (!$client) {
                return response()->json([
                    'success' => false,
                    'message' => 'Client not found.',
                ], 404);
            }

            $rec_id = (int) $id;
            $ebook = EBook::find($rec_id);

            // Check if ebook exists
            if (!$ebook) {
                return response()->json([
                    'success' => false,
                    'message' => 'EBook not found.',
                ], 404);
            }

            $localIp = gethostbyname(gethostname());

            // Check for existing activity
            $existingActivity = EbookActivity::where('ebook_id', $ebook->id)
                ->where('ip_address', $localIp)
                ->first();

            if ($existingActivity) {
                $lastOpenTime = $existingActivity->last_open;

                // Update activity if 5 minutes have passed since last open
                if (now()->diffInMinutes($lastOpenTime) >= 5) {
                    $existingActivity->last_open = now();
                    $existingActivity->total_views += 1;
                    $existingActivity->save();
                }
            } else {
                // Create new activity record
                $newActivity = new EbookActivity;
                $newActivity->ebook_id = $ebook->id;
                $newActivity->date_time = now();
                $newActivity->last_open = now();
                $newActivity->total_views = 1;
                $newActivity->activity_route = $slug;
                $newActivity->ip_address = $localIp;
                $newActivity->save();
            }

            // Prepare response data
            return response()->json([
                'success' => true,
                'data' => $ebook,
            ]);
        } catch (\Exception $e) {
            // Handle any exceptions
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
