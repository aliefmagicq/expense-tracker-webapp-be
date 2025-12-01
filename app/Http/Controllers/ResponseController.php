<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResponseController extends Controller
{
    public static function fails ($message, $reason, $data = null, $statusCode = 422)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'reason' => $reason,
            'data' => $data,
            'status_code' => $statusCode
        ], $statusCode);
    }

    public static function success ($message, $data = null, $statusCode = 201)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'status_code' => $statusCode
        ], $statusCode);
    }
}
