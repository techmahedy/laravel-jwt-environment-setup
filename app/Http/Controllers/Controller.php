<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function jsonResponse(string $boolean, string $message, $data = [], int $status_code)
    {
        return response()->json([
            'isSuccess' => $boolean,
            'message'     => $message,
            'data'      => $data,
            'headers' => [
                "Content-Type" => "application/json"
            ],
            "status" => $status_code
        ]);
    }

    protected function respondWithToken($token, $user)
    {
        return response()->json([
           'isSuccess'         => true,
           'message'           => 'Authentication successful',
           'data'              => $user,
            'headers' => [
                "Content-Type" => "application/json",
                "token"        => $token
            ],
            "status" => 200
        ]);
    }

    protected function user()
    {
        return auth()->guard('api')->user();
    }
  
}
