<?php

namespace App\Http\Controllers\Api;

use App\Common\ErrorCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class AppleController extends Controller
{
    public function __construct()
    {
        
    }

    public function generatePackage(Request $request){
        //根据苹果账号ID获取个人开发者账号信息
        $appleDeveloperInfo = DB::table('apple')->where(['id'=>$request->id])->first();
        print_r($appleDeveloperInfo);die;
    }

   
}
