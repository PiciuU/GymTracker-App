<?php

namespace App\Http\Traits;

trait ResponseTrait {

    /**
     * Return success response for API request
     *
     * @param  string  $message
     * @param  array  $data
     * @param  int  $code
     * @return \Illuminate\Http\Response
     */

    protected function successResponse($message, $data = [], $code = 200) {
        return response()->json([
            'status' => "Success",
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Return error response for API request
     *
     * @param  string  $message
     * @param  int  $status
     * @return \Illuminate\Http\Response
     */

    protected function errorResponse($message, $code = 422) {
        return response()->json([
            'status' => "Error",
            'message' => $message,
        ], $code);
    }
}