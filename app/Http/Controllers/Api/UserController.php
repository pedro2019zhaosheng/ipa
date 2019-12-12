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
        $smsModel = new Sms();
        $mobile = $request->mobile;
        $sms = $smsModel->where(['mobile'=>$mobile])->first();
//        if(strtotime($sms->expire_date)<time()){
//            fail('验证码已过期，请重新发送！');
//        }
        $data = [
            'mobile'=>$request->mobile,
            'token'=>md5($request->mobile),
            'password' => Hash::make($request->password),
            'name'=>$request->mobile,
            'username'=>$request->mobile,
            'nick_name'=>$request->mobile,
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
          success('',$data);
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
//        if(!isset($request->sms_code)||!$request->sms_code){
//            fail('缺少参数',new \stdClass());
//        }

        $userInfo = DB::table('users')->where(['mobile'=>$request->mobile])->first();
        if(!$userInfo){
           fail('账号密码不匹配');
        }else{
            if(!password_verify($request->password,$userInfo->password)){
               fail('账号密码不正确！');
            }else{
                unset($userInfo->password);
                success('',$userInfo);
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
            $data->nick_name = $userInfo->nick_name;
            $data->is_auth = $userInfo->is_auth;
            $user_download_total = 0;
            $data->user_download_total = $userInfo->download_package_num;
            //已下载的次数
            $downlaod_num = DB::table('log')->where(['user_id'=>$userInfo->id])->count();
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:59');
            $data->downlaod_num = [
                'total_num'=>$downlaod_num,
                'today_num'=>DB::table('log')->where(['user_id'=>$userInfo->id,'type'=>1])->where('created_at','>=',$start)->where('created_at','<=',$end)->count(),
            ];
            $limit = $request->limit?$request->limit:10;
            //常用应用
            $userPackage = DB::table('package')->where(['user_id'=>$userInfo->id])->orderBy('download_num','desc')->paginate($limit);
            $data->userPackage = $userPackage;
            success('',$data);
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
                if($request->type==1){
                    if($ext!='ipa'){
                        fail('请上传.ipa包',[]);
                    }
                }
                //MimeType
                $type = $file->getClientMimeType();
                //临时绝对路径
                $realPath = $file->getRealPath();
              
                $filename = uniqid().'.'.$ext;
                $ipa = $filename;//文件名
                $bool = $request->file('file')->move(storage_path().'/app/public/', $filename);
                //$bool = Storage::disk('public')->put($filename,file_get_contents($realPath));
                //判断是否上传成功
                $filename = 'https://'.$_SERVER['HTTP_HOST'].'/storage/'.$filename;
                //校验ipa包是否有效
                if($request->type==1){
                    $ipa_root_path = public_path().'/storage/tmp';
                    if (!file_exists($ipa_root_path)){
                        mkdir ($ipa_root_path,0777,true);
                    } else {
                    }
                    $storageRoot = "cd /usr/local/homeroot/ipa/public/storage";
                    $tmp = "cd /usr/local/homeroot/ipa/public/storage && cp $ipa tmp/. && cd tmp/";
                    $cmd = "$tmp && unzip $ipa";

                    exec($cmd,$out,$status);
                    $payloadRoot = "/usr/local/homeroot/ipa/public/storage/tmp/Payload";
                    //是否存在Payload
                    if (!file_exists($payloadRoot)){
                        fail('无效的ipa包',[]);
                    }else{
                        //删除tmp
                        exec($tmp.' && rm -rf Payload',$ot,$st);
                        //删除无效ipa包
                        exec($storageRoot." && rm -rf $ipa",$out,$status);
                    }
                }

                if($bool){
                    success('',['file'=>$filename]);
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
            if($request->website){
                $url = $request->website;
                $ch = curl_init();
                $timeout = 10;
                curl_setopt ($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HEADER, 1);
                curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $contents = curl_exec($ch);
                if(false===$contents){
                    fail('网站不合法！',[]);
                }
            }
            $data = [
                'avatar_url'=>$request->avatar_url?$request->avatar_url:$userInfo->avatar_url,
                'nick_name'=>$request->nick_name?$request->nick_name:$userInfo->nick_name,
                'website'=>$request->website?$request->website:$userInfo->website
            ];
            DB::table('users')->where(['id'=>$userInfo->id])->update($data);
            success('',$data);
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
            success('已提交，等待审核！',$data);
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
            success('',[]);
        }
    }

    /**
     *@param token,origin_password,new_password,password,type:1：分发应用 2：超级签 icon
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
            $ipa =  array_pop($ipaArr);

            $ipa_root_path = public_path().'/storage/'.$ipa;
            //获取icon地址
            $unzipName =explode('.',$ipa)[0];
            $cmd = "cd /usr/local/homeroot/ipa/public/storage && rm -rf Payload && mv $unzipName.ipa $unzipName.zip && unzip $unzipName.zip";

            exec($cmd,$out,$status);
            //获取文件夹
            $scanDir = '/usr/local/homeroot/ipa/public/storage/Payload';
            $ret = scandir($scanDir);
            //获取app名字
            $app_name = end($ret);
            //获取icon
            $iconRet = scandir($scanDir.'/'.$app_name);
//            foreach($iconRet as $vs){
//                if(file_exists($scanDir.'/'.$app_name.'/AppIcon60x60@3x')){
//                    $icon = $vs;
//                }
//            }

            $resRoot = $scanDir.'/'.$app_name.'/AppIcon40x40@2x.png';
            exec("cd /usr/local/homeroot/ipa/public/storage && sudo mv $resRoot $unzipName.png",$res,$status);
            $icon = $_SERVER['SCHEME_URL'].'/storage/'."$unzipName.png";
            if($request->s_type==1){
//                print_r("cd /usr/local/homeroot/ipa/public/storage && mv $resRoot $unzipName.png");die;
                print_r($icon);die;

            }
            include app_path().'/parse-app/IpaParser.php';
            $main = new \IpaParser;
            $main->parse($ipa_root_path);
//            echo $main->getPackage();
//            echo $main->getVersion();
//            echo $main->getAppName();
            $result =  $main->getPlist();
            $data = [
                'icon'=>isset($request->icon)?$request->icon:'',
                'version_desc'=>$request->version_desc?$request->version_desc:'',
                'introduction'=>$request->introduction?$request->introduction:'',
                'user_id'=>$userInfo->id,
                'version'=>$result['CFBundleShortVersionString'],
                'build'=>$result['MinimumOSVersion'],
                'name'=>isset($request->name)?$request->name:$result['CFBundleDisplayName'],
                'size'=>$size,
                'is_super'=>$type,
                'is_push'=>1,
                'download_url'=>$ipa_url,
                'ipa_url'=>$ipa_url,
                'buddle_id'=>$result['CFBundleIdentifier'],
                'created_at'=>date('Y-m-d H:i:s')
            ];
            if($request->id){
                $id = $request->id;
                $res = DB::table('package')->where(['id'=>$id])->update($data);
            }else{
                $res = DB::table('package')->insert($data);
                $id = DB::getPdo()->lastInsertId();
            }
            if($id>0){
                //更新下载地址
                $download_url = $_SERVER['SCHEME_URL'].'/ipa/?package_id='.$id;
                DB::table('package')->where(['id'=>$id])->update(['download_url'=>$download_url]);
            }

            $data['id'] = $id;
            //生成xml mobileconfig
            generateXml($data);
            success(['','id'=>$id]);
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
        if(!isset($request->type)||!$request->type){
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
            $subWhere = [];
            if(!empty($request->keyword)){
                $subWhere[] = ['name','like','%'.$request->name.'%'];
            }
            $res = DB::table('package')->where($where)->where($subWhere)->paginate(10)->toArray();
            foreach($res['data'] as &$v){
                $url = $_SERVER['SCHEME_URL'].'/ipa/?package_id='.$v->id;
                $qrcodeName = '/storage/qr_'.$v->id.'.png';
                $root =public_path($qrcodeName);
                QrCode::format('png')->size(200)->generate($url,$root);
                $v->qrcode_url = $_SERVER['SCHEME_URL'].$qrcodeName;
            }
            if($type==2){
                //已下载的次数
                $download_num = DB::table('device')->where(['user_id'=>$userInfo->id])->count();
                $res['user_download_total'] = $userInfo->sign_num+$download_num;

                $start = date('Y-m-d 00:00:00');
                $end = date('Y-m-d 23:59:59');
                $res['download_num'] =  $res['user_download_total']-$download_num;
            }
            success('',$res);
        }
    }

    public function userInfo(Request $request){
        if(!isset($request->token)||!$request->token){
            fail('缺少参数');
        }
        $userInfo = DB::table('users')->where(['token'=>$request->token])->first();
        success('',$userInfo);
    }

    /**
     *@param token,id
     *@desc  获取包信息
     */
    public function packageInfo(Request $request){
        if(!isset($request->token)||!$request->token){
            fail('缺少参数');
        }
        if(!isset($request->id)||!$request->id){
            fail('缺少参数');
        }
        $info = DB::table('package')->where(['id'=>$request->id])->first();
        $info->tt = shortUrl('', $info->download_url);
        if($request->s_type==1){
            $api = 'http://api.t.sina.com.cn/short_url/shorten.json'; // json
// $api = 'http://api.t.sina.com.cn/short_url/shorten.xml'; // xml
            $source = '1323983993';
            $url_long = 'https://www.jb51.net/';
            $request_url = sprintf($api.'?source=%s&url_long=%s', $source, $url_long);
            print_r($request_url);die;
            $data = file_get_contents($request_url);
            echo $data;
        }
        success('',$info);
    }

    /**
     *@param token,id
     *@desc  获取包信息
     */
    public function logout(Request $request)
    {
        if(!isset($request->token)||!$request->token){
            fail('缺少参数');
        }
        success('',[]);
    }

    /**
     *@param token,id
     *@desc  获取包信息
     */
    public function delPackage(Request $request){
        if(!isset($request->token)||!$request->token){
            fail('缺少参数');
        }
        if(!isset($request->id)||!$request->id){
            fail('缺少参数');
        }
        DB::table('package')->where(['id'=>$request->id])->delete();
        success('',[]);
    }

}