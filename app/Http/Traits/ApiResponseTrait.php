<?php

namespace App\Http\Traits;

trait ApiResponseTrait
{
    protected function success($data = null, $message = 'Success', $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function error($message = 'Error', $code = 400, $errors = null)
    {
        return response()->json([
            'status' => 'error',
            'errors' => $message
        ], $code);
    }
}
