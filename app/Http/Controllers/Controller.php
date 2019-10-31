<?php

namespace App\Http\Controllers;

use App\Common\ErrorCode;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function success($data = [])
    {
        return response()->json([
            'status'  => 1,
//            'code'    => 200,
            'message' => '成功',
            'data'    => $data,
        ]);
    }

    public function fail($message,$data = [])
    {
        return response()->json([
            'status'  => 0,
//            'code'    => $code['code'],
            'message' => $message,
            'data'    => $data,
        ]);
    }
}
