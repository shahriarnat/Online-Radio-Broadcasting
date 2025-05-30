<?php

namespace app\Helpers;

use App\Http\Resources\PaginationResource;

class ApiResponse
{
    public static function success($data = null, $message = 'Success', $code = 200)
    {
        return response()->json([
            'message' => $message,
            'status' => 'success',
            'code' => $code,
            'data' => $data,
        ], $code);
    }

    public static function error($message = 'Error', $code = 400)
    {
        return response()->json([
            'message' => $message,
            'status' => 'error',
            'code' => $code,
        ], $code);
    }

    public static function validation($errors, $message = 'Validation Error', $code = 422)
    {
        return response()->json([
            'message' => $message,
            'status' => 'validation',
            'code' => $code,
            'errors' => $errors,
        ], $code);
    }

    public static function paginate($data, $message = 'Success', $code = 200)
    {
        return response()->json([
            'message' => $message,
            'status' => 'success',
            'code' => $code,
            'paginate' => PaginationResource::make($data),
            'data' => $data->items(),
        ], $code);
    }

}
