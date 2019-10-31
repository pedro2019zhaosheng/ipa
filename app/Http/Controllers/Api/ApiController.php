<?php

namespace App\Http\Controllers\Api;

use App\Common\ErrorCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;

class ApiController extends Controller
{
    public function userinfo()
    {
        return $this->fail(ErrorCode::$token_missing);
    }


    public function mlogin()
    {
        echo 'mlogin';
    }

    public function tlogin()
    {
        echo 'tlogin';
    }

    public function getArea(Request $request){
        $data = DB::table('area')->where('parent_id',1)->get();
        if($request->prov_id){
            $data = DB::table('area')->where('parent_id',$request->prov_id)->get();
        }
        return json_encode(['status'=>1,'message'=>'操作成功','data'=>$data]);
    }
}
