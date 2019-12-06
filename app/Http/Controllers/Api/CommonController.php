<?php

namespace App\Http\Controllers\Api;

use App\Common\ErrorCode;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use App\Models\Device;
use App\Models\Package;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Hash;
use App\Models\Sms;
class CommonController extends Controller
{
    public function __construct()
    {
    }

    /**
    *@param mobile ,username,password,sms_code
    */
   public function sendSms(Request $request){

        if(!isset($request->mobile)||!$request->mobile){
           fail('缺少参数',new \stdClass());
        }
        $smsModel = new Sms();
        $mobile = $request->mobile;
        $sms = $smsModel->where(['mobile'=>$mobile])->first();
        $code =  rand(100000, 999999);
        $data = [
            'mobile'=>$request->mobile,
            'code'=>$code,
            'expire_date'=>date('Y-m-d H:i:s',(time()+10*60)),
            'created_at'=>date('Y-m-d H:i:s'),
            'updated_at'=>date('Y-m-d H:i:s')
        ];
        $status = 0;
        if($sms){
            if(strtotime($sms->expire_date)>time()){
                fail('验证码已发送，请注意查收！');
            }
            $smsModel->where(['mobile'=>$mobile])->update($data);
            $status = 1;
        }else{
            $smsModel->insert($data);
            $status = 1;
        }
        if($status==1){
            $content = "【365超级签】你的验证码是$code,请在10分钟内验证完毕";
            $smsModel->send($mobile,$content);
        }
        success();
   }
}
