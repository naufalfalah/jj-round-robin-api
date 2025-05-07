<?php

namespace App\Traits;

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
    protected function sendSuccessResponse($message = 'Success', $data = [], $status = 200)
    {
        return response()->json([
            'status' => 'Success',
            'message' => $message,
            'data' => $data,
        ], $status);
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
        return response()->json([
            'status' => 'Error',
            'message' => $message,
        ], $statusCode);
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
