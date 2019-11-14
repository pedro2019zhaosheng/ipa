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

class AppleController extends Controller
{
    public function __construct()
    {
        
    }

    /**
    *@param device_id ,apple_id,package_id
    */
    public function generatePackage(Request $request){
        ini_set('display_errors', 0);
        error_reporting(E_ALL);
        $udid = isset($request->udid)?$request->udid:0;
        if(!$udid){
             echo json_encode(['status'=>0,'message'=>'缺少参数！','data'=>[]]);die;
        }
        $deviceInfo = Device::where(['udid'=>$udid,'package_id'=>$request['package_id']])->first();
        if($deviceInfo){
            $url = 'itms-services://?action=download-manifest&amp;url='.$deviceInfo->plist_url;//todo
            // $url = "itms-services://?action=download-manifest&amp;url=https://test.daoyuancloud.com/install_ipa/9efa99314d8da5632a37dfa2abad6ac5cedb715e_20191030142348.plist";
//            echo json_encode(['status'=>1,'message'=>'缺少参数！','url'=>$deviceInfo->plist_url]);die;
            header("Location: $url");
            exit(0);
        }
        //获取包信息
        $package = Package::where(['id'=>$request->package_id])->first();
        if($package){
            $ipa_arr = explode('/', $package->ipa_url);
            $ipa_url = "/usr/local/homeroot/ipa/public/storage/".end($ipa_arr);
            $buddle_id = $package->buddle_id;
        }
        //第一步根据苹果账号ID获取个人开发者账号信息
        $appleDeveloperInfo = DB::table('apple')->where(['id'=>$request->apple_id])->first();
        $account = $appleDeveloperInfo->account;
        $secret = $appleDeveloperInfo->secret_key;
        $certificate_id = $appleDeveloperInfo->certificate_id;
        $p12_arr = explode('/', $appleDeveloperInfo->p12_url);
        $p12_url = "/usr/local/homeroot/ipa/public/storage/".end($p12_arr);
        $cmdRoot = "cd /usr/local/homeroot/ipasign-master/nomysql &&";
    	$stepOneCmd = "$cmdRoot sudo  /bin/ruby  /usr/local/homeroot/ipasign-master/nomysql/checkLogin.rb $account $secret";
    	// $stepOne = system($stepOneCmd,$status,$msg);
        try{
             $res = exec($stepOneCmd,$out);

        }catch (Exception $e) {
            echo $e->getMessage();die;
            // die(); // 终止异常
        }
    	// echo $out[0];
    	//第二步生成证书
    	$stepTwoCmd = "$cmdRoot sudo  /bin/ruby saveCert.rb $account $secret $certificate_id $p12_url";

    	exec($stepTwoCmd,$outTwo);
    	if(isset($outTwo[1])){
    		$clientKey = "/applesign/$account/$certificate_id/client_key.pem";
    		$privateKey = "/applesign/$account/$certificate_id/private_key.pem";
    	}
    	//第三步将udid写入开发者账号
    	$stepThreeCmd = "$cmdRoot sudo  /bin/ruby addUUid.rb $account $secret  $udid $buddle_id $certificate_id";

    	exec($stepThreeCmd,$outThree);
    	if(isset($outThree[0])){
    		$mobileprovision  = "/applesign/$account/$certificate_id/sign.$buddle_id.mobileprovision";
    	}
    	//第四步生成ipa包
    	$stepFourCmd = "$cmdRoot sudo /bin/ruby signIpa.rb $account  $udid  $ipa_url $buddle_id $certificate_id /applesign/$account/$certificate_id/sign.$buddle_id.mobileprovision /applesign/$account/$certificate_id/client_key.pem /applesign/$account/$certificate_id/private_key.pem";
    	exec($stepFourCmd,$outFour,$re);
    	if(isset($outFour[0])){
    		$ipa = $outFour[0];
    		$plist = $outFour[1];
    	}
        // print_r($stepFourCmd);die;
        //ipa入库
        // $mvFrom = $ipa;
        // $filename = $request->apple_id.'-'.$udid.'.ipa';
        // $mvTo = "/usr/local/homeroot/ipa/public/storage/".$filename;
        // $mvCmd = "sudo mv $mvFrom $mvTo";
        // exec($mvCmd);
        //plist入库
        // $mvPlistFrom = $plist;
        // $plistName = $request->apple_id.'-'.$udid.'.plist';
        // $mvPlistTo = "/usr/local/homeroot/ipa/public/storage/".$plistName;

        //测试todo
        // $mvPlistTo = "/usr/local/homeroot/install_ipa/".$plistName;
        // $mvCmd = "sudo mv $mvPlistFrom $mvPlistTo";
        // exec($mvCmd);
        //入库
        if(!$deviceInfo){
            // $download_url = 'http://'.$_SERVER['HTTP_HOST'].'/storage/'.$filename;
            // $plistUrl = 'https://'.$_SERVER['HTTP_HOST'].'/storage/'.$plistName;//todo
            // $plistUrl = "https://test.daoyuancloud.com/install_ipa/".$plistName;
            $download_url = "https://test.daoyuancloud.com".$ipa;
            $plistUrl = "https://test.daoyuancloud.com".$plist;
            $data = [
                'apple_id'=>$request->apple_id,
                'package_id'=>$request->package_id,//todo
                'udid'=>$udid,
                'ipa_url'=>$download_url,
                'plist_url'=>$plistUrl,
                'created_at'=>date('Y-m-d H:i:s')
            ];

            Device::insert($data);
        }
        $url = "itms-services://?action=download-manifest&amp;url=$plistUrl";
//        echo json_encode(['status'=>1,'message'=>'缺少参数！','url'=>$plistUrl]);die;
        header("Location: $url");
        exit(0);
    }

    public function init(Request $request){
        $udid = isset($request->udid)?$request->udid:0;
        if(!$udid){
            echo json_encode(['status'=>0,'message'=>'缺少参数！','data'=>[]]);die;
        }
        $package_id = isset($request->package_id)?$request->package_id:0;
        $apple_id = isset($request->apple_id)?$request->apple_id:0;
        $device = DB::table('device')->where(['udid'=>$udid,'package_id'=>$package_id,'apple_id'=>$apple_id])->first();
        $data = [
            'apple_id'=>$request->apple_id,
            'package_id'=>$request->package_id,//todo
            'udid'=>$udid,
            'created_at'=>date('Y-m-d H:i:s')
        ];
        if(!$device&&$apple_id>0&&$package_id>0){
            DB::table('device')->insert($data);
            echo json_encode(['status'=>1]);die;
        }else{
            echo json_encode(['status'=>1]);die;
        }

    }

    public function ipa(Request $request){
        $udid = isset($request->udid)?$request->udid:0;
        if(!$udid){
            echo json_encode(['status'=>0,'message'=>'缺少参数！','data'=>[]]);die;
        }
        $package_id = isset($request->package_id)?$request->package_id:0;
        $apple_id = isset($request->apple_id)?$request->apple_id:0;
        $device = DB::table('device')->where(['udid'=>$udid,'package_id'=>$package_id])->first();
        if($device&&$device->ipa_url!=''){
            $url = "itms-services://?action=download-manifest&url=$device->plist_url";
            echo json_encode(['status'=>1,'url'=>$url]);die;
            header("Location: $url");
            exit(0);
        }else{
            echo json_encode(['status'=>0]);die;
        }

    }

    public  function qrcode(Request $request){
        // 字段验证规则
        $res = $request->all();
        $validator = Validator::make($res, [
            'url' => 'required|active_url',
        ],[
            'required' => ':attribute 为必填项',
            'url.active_url' => '请检查网址是否正确（加上https http）',
        ]);

        $data = QrCode::size(200)->color(0,0,0)->backgroundColor(0,255,0)->generate($res['url']);
        $url = base64_encode($data);
        $qr_url =  base64_decode($url);
        return response()->json(['status'=>1,'url'=>$qr_url]);
    }

   
}
