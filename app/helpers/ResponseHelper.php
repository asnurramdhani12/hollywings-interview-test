<?php

namespace App\Helpers;

class ResponseHelper
{
    public static function success($data = [], $message = 'Success', $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'metadata' => [
                'request_id' => request()->header('X-Request-ID'),
            ]
        ], $status);
    }

    public static function error($message = 'Something went wrong', $status = 400, $errors = [])
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'metadata' => [
                'request_id' => request()->header('X-Request-ID'),
            ],
        ], $status);
    }
}
