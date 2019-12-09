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
class UserController extends Controller
{
    public function __construct()
    {
    }

    /**
    *@param mobile ,username,password,sms_code
    */
   public function register(Request $request){

        if(!isset($request->mobile)||!$request->mobile){
           fail('缺少参数',new \stdClass());
        }
//        if(!isset($request->username)||!$request->username){
//           fail('缺少参数',new \stdClass());
//        }
        if(!isset($request->password)||!$request->password){
            fail('缺少参数',new \stdClass());
        }
        if(!isset($request->sms_code)||!$request->sms_code){
            fail('缺少参数',new \stdClass());
        }
        if($request->sms_code!=9999){
            fail('验证码错误！',new \stdClass());
        }
        $data = [
            'mobile'=>$request->mobile,
            'token'=>md5($request->mobile),
            'password' => Hash::make($request->password),
            'name'=>$request->username,
            'username'=>$request->mobile,
            'email'=>$request->mobile.'@qq.com',
            'created_at'=>date('Y-m-d H:i:s')
        ];
        //验证手机号是否存在
       $userInfo = DB::table('users')->where(['mobile'=>$request->mobile])->first();
       if($userInfo){
          fail('手机号已存在',new \stdClass());
       }
       $res = DB::table('users')->insert($data);
       if($res){
          success($data);
       }else{
          fail('操作失败',new \stdClass());
       }
   }

    /**
     *@param mobile ,password
     */
    public function login(Request $request){
        if(!isset($request->mobile)||!$request->mobile){
           fail('缺少参数');
        }
        if(!isset($request->password)||!$request->password){
           fail('缺少参数');
        }
        if(!isset($request->sms_code)||!$request->sms_code){
            fail('缺少参数',new \stdClass());
        }
        if($request->sms_code!=9999){
            fail('验证码错误！',new \stdClass());
        }
        $userInfo = DB::table('users')->where(['mobile'=>$request->mobile])->first();
        if(!$userInfo){
           fail('账号密码不匹配');
        }else{
            if(!password_verify($request->password,$userInfo->password)){
               fail('账号密码不正确！');
            }else{
                unset($userInfo->password);
                success($userInfo);
            }
        }
    }
    /**
     *@param token
     * @desc 概述
     */
    public function summary(Request $request){
        if(!isset($request->token)||!$request->token){
            fail('缺少参数');
        }
        $token = $request->token;
        $userInfo = DB::table('users')->where(['token'=>$token])->first();
        $data = new \stdClass();
        if(!$userInfo){
            fail('用户不存在！');
        }else{
            //用户总下载数量
            $data->username = $userInfo->username;
            $data->is_auth = $userInfo->is_auth;
            $user_download_total = 0;
            $data->user_download_total = $user_download_total;
            //已下载的次数
            $downlaod_num = 0;
            $data->downlaod_num = [
                'total_num'=>$downlaod_num,
                'today_num'=>0
            ];
            $limit = $request->limit?$request->limit:10;
            //常用应用
            $userPackage = DB::table('package')->where(['user_id'=>$userInfo->id])->orderBy('download_num','desc')->paginate($limit);
            $data->userPackage = $userPackage;
            success($data);
        }
    }

    /**
     *@param token
     *@param file
     */
    public function upload(Request $request){
        if ($request->isMethod('POST')){
            $file = $request->file('file');
            //判断文件是否上传成功
            if ($file){
                //原文件名
                $originalName = $file->getClientOriginalName();
                //扩展名
                $ext = $file->getClientOriginalExtension();
                //MimeType
                $type = $file->getClientMimeType();
                //临时绝对路径
                $realPath = $file->getRealPath();
                $filename = uniqid().'.'.$ext;
                $bool = $request->file('file')->move(storage_path().'/app/public/', $filename);
                //$bool = Storage::disk('public')->put($filename,file_get_contents($realPath));
                //判断是否上传成功
                $filename = 'https://'.$_SERVER['HTTP_HOST'].'/storage/'.$filename;
                if($bool){
                    success(['file'=>$filename]);
                }else{
                    fail('上传失败');
                }
            }
        }
    }
    /**
     *@param token,avatar_url,nick_name,website
     *@desc  modifyUserInfo
     */
    public function modifyUserInfo(Request $request){
        if(!isset($request->token)||!$request->token){
            fail('缺少参数');
        }
        $token = $request->token;
        $userInfo = DB::table('users')->where(['token'=>$token])->first();
        if(!$userInfo){
            fail('用户不存在！');
        }else{
            $data = [
                'avatar_url'=>$request->avatar_url?$request->avatar_url:$userInfo->avatar_url,
                'nick_name'=>$request->nick_name?$request->nick_name:$userInfo->nick_name,
                'website'=>$request->website?$request->website:$userInfo->website
            ];
            DB::table('users')->where(['id'=>$userInfo->id])->update($data);
            success($data);
        }
    }
    /**
     *@param token,true_name,id_card_no,id_card_front_url,id_card_back_url
     *@desc  realNameAuth
     */
    public function realNameAuth(Request $request){
        if(!isset($request->token)||!$request->token){
            fail('缺少参数');
        }
        $token = $request->token;
        $userInfo = DB::table('users')->where(['token'=>$token])->first();
        if(!$userInfo){
            fail('用户不存在！');
        }else{
            $data = [
                'true_name'=>$request->true_name?$request->true_name:$userInfo->true_name,
                'id_card_no'=>$request->id_card_no?$request->id_card_no:$userInfo->id_card_no,
                'id_card_front_url'=>$request->id_card_front_url?$request->id_card_front_url:$userInfo->id_card_front_url,
                'id_card_back_url'=>$request->id_card_back_url?$request->id_card_back_url:$userInfo->id_card_back_url
            ];
            DB::table('users')->where(['id'=>$userInfo->id])->update($data);
            success($data);
        }
    }
    /**
     *@param token,origin_password,new_password,password
     *@desc  modifyPassword
     */
    public function modifyPassword(Request $request){
        if(!isset($request->token)||!$request->token){
            fail('缺少参数');
        }
        $token = $request->token;
        $userInfo = DB::table('users')->where(['token'=>$token])->first();
        if(!$userInfo){
            fail('用户不存在！');
        }else{
            if(!password_verify($request->origin_password,$userInfo->password)){
                fail('账号密码不正确！');
            }
            if($request->new_password!=$request->password){
                fail('两次输入密码不一致！');
            }
            $data = [
                'password' => Hash::make($request->password)
            ];
            DB::table('users')->where(['id'=>$userInfo->id])->update($data);
            success();
        }
    }

    /**
     *@param token,origin_password,new_password,password,type:1：分发应用 2：超级签
     *@desc  internalPackage 说明先调用upload接口，获取地址传ipa_url调用此接口
     */
    public function uploadPackage(Request $request){

        if(!isset($request->token)||!$request->token){
            fail('缺少参数');
        }
        if(!isset($request->ipa_url)||!$request->ipa_url){
            fail('缺少参数');
        }
        $type = isset($request->type)&&$request->type>1?2:1;
        $token = $request->token;
        $userInfo = DB::table('users')->where(['token'=>$token])->first();
        if(!$userInfo){
            fail('用户不存在！');
        }else{

            //通过url获取文件大小信息
//            $ipa_url = 'http://p14fc.cn/storage/5de0bde5bcd9c.ipa';
            $ipa_url = $request->ipa_url;
            $content = file_get_contents($ipa_url);
            $filesize =  strlen($content );  //获取文件大小
            $size =  round($filesize/1024/1024,2);
//            require_once app_path().'/parse-app/ApkParser.php';
            $ipaArr = explode('/',$ipa_url);
            $ipa_root_path = public_path().'/storage/'.array_pop($ipaArr);
            include app_path().'/parse-app/IpaParser.php';
            $main = new \IpaParser;
            $main->parse($ipa_root_path);
//            echo $main->getPackage();
//            echo $main->getVersion();
//            echo $main->getAppName();
            $result =  $main->getPlist();
            $data = [
                'user_id'=>$userInfo->id,
                'version'=>$result['CFBundleShortVersionString'],
                'build'=>$result['MinimumOSVersion'],
                'name'=>$result['CFBundleDisplayName'],
                'size'=>$size,
                'is_super'=>$type,
                'download_url'=>$ipa_url,
                'buddle_id'=>$result['CFBundleIdentifier'],
                'created_at'=>date('Y-m-d H:i:s')
            ];
            $res = DB::table('package')->insert($data);
            $id = DB::getPdo()->lastInsertId();
            $data['id'] = $id;
            //生成xml mobileconfig
            generateXml($data);
            success(['id'=>$id]);
        }
    }
    /**
     *@param token,type:1：分发应用 2：超级签
     *@desc  获取包列表
     */
    public function packageList(Request $request)
    {
        if(!isset($request->token)||!$request->token){
            fail('缺少参数');
        }
        if(!isset($request->ipa_url)||!$request->ipa_url){
            fail('缺少参数');
        }
        $type = isset($request->type)&&$request->type>1?2:1;
        $token = $request->token;
        $userInfo = DB::table('users')->where(['token'=>$token])->first();
        if(!$userInfo){
            fail('用户不存在！');
        }else{
            $where = [
                'user_id'=>$userInfo->id,
                'is_super'=>$type
            ];
            $res = DB::table('package')->where($where)->paginate(10);
            success($res);
        }
    }

    public function userInfo(Request $request){
        if(!isset($request->token)||!$request->token){
            fail('缺少参数');
        }
        $userInfo = DB::table('users')->where(['token'=>$request->token])->first();
        success($userInfo);
    }

}
