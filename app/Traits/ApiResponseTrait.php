<?php

namespace App\Traits;

use App\Models\ApiLog;
use Illuminate\Http\JsonResponse;

trait ApiResponseTrait
{
    /**
     * Send a success response with a message and data.
     *
     * @param string $message
     * @param array $data
     * @param int $status
     * @return JsonResponse
     */
    protected function sendSuccessResponse($message = 'Success', $data = [], $statusCode = 200)
    {
        $currentRoute = request()->route()->getName() ?? request()->path();
        $clientId = auth('api')->user()->id ?? null;

        $response = response()->json([
            'status' => 'Success',
            'message' => $message,
            'data' => $data,
        ], $statusCode);

        ApiLog::create([
            'route' => $currentRoute,
            'client_id' => $clientId,
            'status_code' => $statusCode,
            'response' => json_encode($response),
        ]);

        return $response;
    }

    /**
     * Send an error response with a message and optional error code.
     *
     * @param string $message
     * @param int $statusCode
     * @param int|null $errorCode
     * @return JsonResponse
     */
    protected function sendErrorResponse($message, $statusCode = 400)
    {
        $currentRoute = request()->route()->getName() ?? request()->path();
        $clientId = auth('api')->user()->id ?? null;

        $response = response()->json([
            'status' => 'Error',
            'message' => $message,
        ], $statusCode);

        ApiLog::create([
            'route' => $currentRoute,
            'client_id' => $clientId,
            'status_code' => $statusCode,
            'response' => json_encode($response),
        ]);

        return $response;
    }

    /**
     * Send a validation error response with errors and optional error code.
     *
     * @param array $errors
     * @param int|null $errorCode
     * @return JsonResponse
     */
    protected function sendValidationError($errors)
    {
        return $this->sendErrorResponse('Validation Error', 422)->with('errors', $errors);
    }
}
