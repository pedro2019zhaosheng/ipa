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
        //获取包信息
        $package = DB::table('package')->where(['id'=>$package_id])->first();
        if($package->user_id<1){
            fail('无效的包！');
        }
        //找出上传包的用户信息
        $user = DB::table('users')->where(['id'=>$package->user_id])->first();

        if($package->is_super==1){
            if($user->download_package_num<1){
                fail('下载次数不足，请充值！');
            }else{

                success('',[]);
            }
        }
        if($package->is_super==2){

            if($user->sign_num<1){
                fail('签名次数不足，请充值！');
            }
        }

        $device = DB::table('device')->where(['udid'=>$udid,'package_id'=>$package_id])->first();


        $appleList = DB::table('apple')->where('udid_num','<',99)->where('user_id','=',$package->user_id)->get();
        if($appleList->count()==0){
            $appleList = DB::table('apple')->where('udid_num','<',99)->get();
        }
        $appleIsPushList =  DB::table('apple')->where('udid_num','<',99)->where('user_id','=',$package->user_id)->where(['is_push'=>1])->get();
        //todo
        if($package->is_push>0){
            if($package->apple_id>0){
                //优先获取初始化账号打包
//                $appleDeveloperInfo = $appleIsPushList[0];
//                $apple_id = $appleDeveloperInfo->id;
                $apple_id = $package->apple_id;
                $user_id = $package->user_id;
            }else{
                $appleDeveloperInfo = $appleList[0];
                $apple_id = $appleDeveloperInfo->id;
                $user_id = $appleDeveloperInfo->user_id;
            }
        }else{
            $appleDeveloperInfo = $appleList[0];
            $apple_id = $appleDeveloperInfo->id;
            $user_id = $appleDeveloperInfo->user_id;
        }
//        $apple_id = $appleDeveloperInfo->id;
        $data = [
            'apple_id'=>$apple_id,
            'package_id'=>$request->package_id,//todo
            'udid'=>$udid,
            'user_id'=>$user_id,
            'created_at'=>date('Y-m-d H:i:s')
        ];
//        //每次下载量累加
//        DB::table('package')->where(['id'=>$package_id])->update(['download_num'=>$package->download_num+1]);
        if(!$device&&$apple_id>0&&$package_id>0){
            if($package->is_binding==1){
                DB::table('device')->insert($data);
                //获取绑定包列表
                $sonPackageList = DB::table('package')->where(['pid'=>$package->id])->get();
                foreach($sonPackageList as $v){
                    $device = DB::table('device')->where(['udid'=>$udid,'package_id'=>$v->id])->first();
                    if(!$device){
                        $sonData = [
                            'apple_id'=>$apple_id,
                            'package_id'=>$v->id,//todo
                            'udid'=>$udid,
                            'user_id'=>$user_id,
                            'created_at'=>date('Y-m-d H:i:s')
                        ];
                        DB::table('device')->insert($sonData);
                    }
                }
            }else{

                $sonData = [
                    'apple_id'=>$apple_id,
                    'package_id'=>$package_id,//todo
                    'udid'=>$udid,
                    'user_id'=>$user_id,
                    'created_at'=>date('Y-m-d H:i:s')
                ];
                DB::table('device')->insert($sonData);
            }
            echo json_encode(['status'=>1]);die;
        }else{
            echo json_encode(['status'=>1]);die;
        }

    }

    public function ipa(Request $request){
        //从接口更新ipa下载udid和时间戳
        $scheme_url = $_SERVER['SCHEME_URL'].'/api/apple/generatePlist?plist=';
        $udid = isset($request->udid)?$request->udid:0;

        $package_id = isset($request->package_id)?$request->package_id:0;
        //获取包信息
        $package = DB::table('package')->where(['id'=>$package_id])->first();
        if($package->user_id<1){
            fail('无效的包！');
        }
        //找出上传包的用户信息
        $user = DB::table('users')->where(['id'=>$package->user_id])->first();
        if($package->is_super==1){
            if($user->download_package_num<1){
                fail('下载次数不足，请充值！');
            }else{
                $prefix = 'itms-services://?action=download-manifest&url=';
                $plist = '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
        <key>items</key>
        <array>
                <dict>
                        <key>assets</key>
                        <array>
                                <dict>
                                        <key>kind</key>
                                        <string>software-package</string>
                                        <key>url</key>
                                        <string>'.$package->ipa_url.'</string>
                                </dict>
                        </array>
                        <key>metadata</key>
                        <dict>
                                <key>bundle-identifier</key>
                                <string>com.text.WWW.WeChatTiaoT.01</string>
                                <key>bundle-version</key>
                                <string>1.0</string>
                                <key>kind</key>
                                <string>software</string>
                                <key>title</key>
                                <string>WBF-Exchange</string>
                        </dict>
                </dict>
        </array>
</dict>
</plist>
';
                $file = public_path().'/udid/'.$package_id.'.plist';
                file_put_contents($file,$plist);
                $ipa_url = $_SERVER['SCHEME_URL'].'/udid/'.$package_id.'.plist';
//                $arr[] = $prefix.$scheme_url.$ipa_url.'&udid='.$udid;
                $arr[] = $prefix.$ipa_url;

//                $ipaName = explode('/',$package->ipa_url);
//                $ipaName = end($ipaName);
//                $std = [
//                    'plisturl'=>$prefix.$ipa_url,
//                    'ipaName'=>$ipaName,
//                    'udid'=>$udid
//                ];
//                $arr[] = $std;
                //扣除下载次数
                DB::table('users')->where(['id'=>$user->id])->update(['download_package_num'=>$user->download_package_num-1]);
                //日志
                $log = [
                    'user_id'=>$package->user_id,
                    'package_id'=>$package_id,
                    'udid'=>$udid,
                    'type'=>1,
                    'created_at'=>date('Y-m-d H:i:s')
                ];
                DB::table('log')->insert($log);
                echo json_encode(['status'=>1,'url'=>$arr,'num'=>count($arr)]);die;
            }
        }
        if($package->is_super==2){
            if($user->sign_num<1){
                fail('签名次数不足，请充值！');
            }
        }
        if(!$udid){
            echo json_encode(['status'=>0,'message'=>'缺少参数！','data'=>[]]);die;
        }

        $package_id = isset($request->package_id)?$request->package_id:0;
        $device = DB::table('device')->where(['udid'=>$udid,'package_id'=>$package_id])->first();
        if($device&&$device->ipa_url!=''){
            //获取sonPackageList
            $sonPackageList = DB::table('package')->where(['pid'=>$package_id])->get();
            $sonPackgeIds = [];
            foreach($sonPackageList as $vs){
                $sonPackgeIds[] = $vs->id;
            }
            $where = array_merge([$package_id],$sonPackgeIds);
            $deviceList = DB::table('device')->whereIn('package_id',$where)->where('udid','=',$udid)->get();
            $url = "itms-services://?action=download-manifest&url=$device->plist_url";
            $arr = [];
            $prefix = 'itms-services://?action=download-manifest&'.'&url=';
            foreach($deviceList as $value){
//                $arr[] = $prefix.$scheme_url.$value->plist_url.'&udid='.$udid;
                //安装添加udid和时间戳
                $plist = $value->plist_url;
//                $plistRoot = public_path().str_replace($_SERVER['SCHEME_URL'],'',$plist);
//                $content = file_get_contents($plist);
//                $str = 'ipa?udid='.$request->udid.'&timestamp='.time();
//                $sContent = str_replace( 'ipa',$str,$content);
//                exec("sudo chown -R www:www $plistRoot",$out,$status);
//                file_put_contents($plistRoot,$sContent);
                $ipaName = explode('/',$value->ipa_url);
                $ipaName = end($ipaName);
                $std = [
                    'plisturl'=>$prefix.$value->plist_url,
                    'ipaName'=>$ipaName,
                    'udid'=>$udid
                ];
                $arr[] = $prefix.$value->plist_url;

            }
            //test multi download
//            $prefix = 'itms-services://?action=download-manifest&url=';
//            $arr = [
//                $prefix.'https://www.677677.club//applesign/beng57539113@163.com/TD236HZAM2/0/9efa99314d8da5632a37dfa2abad6ac5cedb715e_20191120164130.plist',
//                $prefix.'https://www.677677.club//applesign/rpaz23@163.com/CYL58XBX6H/0/9efa99314d8da5632a37dfa2abad6ac5cedb715e_20191120170719.plist',
//                $prefix.'https://www.677677.club//applesign/ydoknm@163.com/WJ34XKGF7N/0/fca6d7087e100fa6087cd8c5dab72620559f91fe_20191120163729.plist'
//            ];
            //日志
            //扣除下载次数
            DB::table('users')->where(['id'=>$user->id])->update(['download_package_num'=>$user->download_package_num-1]);
            $log = [
                'user_id'=>$package->user_id,
                'package_id'=>$package_id,
                'udid'=>$udid,
                'type'=>2,
                'created_at'=>date('Y-m-d H:i:s')
            ];
            DB::table('log')->insert($log);
            echo json_encode(['status'=>1,'url'=>$arr,'num'=>count($arr)]);die;
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


    public function savePackageId(Request $request){
        $package_id = $request->package_id>0?$request->package_id:0;
        if($package_id>0){
            $_SESSION['package_id'] = $request->package_id;
        }else{
            $package_id =  isset($_SESSION['package_id'])?$_SESSION['package_id']:0;
        }
        return response()->json(['status'=>1,'package_id'=>$package_id]);
    }

    function create_item($title_data, $title_size, $content_data, $pubdate_data) {
        $item = "<plist version=\"1.0\">\n";
        $item .= "<title size=\"" . $title_size . "\">" . $title_data . "</title>\n";
        $item .= "<content>" . $content_data . "</content>\n";
        $item .= " <pubdate>" . $pubdate_data . "</pubdate>\n";
        $item .= "</item>\n";

        return $item;
    }
    public function packageInfo(Request $request){
//        $data_array = array(
//            array(
//                'title' => 'title1',
//                'content' => 'content1',
//                'pubdate' => '2009-10-11',
//            )
//        );
//        $title_size = 1;
//
/*        $xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";*/
//        $xml .= "<!DOCTYPE plist PUBLIC \"-//Apple//DTD PLIST 1.0//EN\" \"http://www.apple.com/DTDs/PropertyList-1.0.dtd\">";
//
//        foreach ($data_array as $data) {
//            $xml .= $this->create_item($data['title'], $title_size, $data['content'], $data['pubdate']);
//        }
//
//        $xml .= "</article>\n";
//
//        echo $xml;die;
        $package_id = $request->package_id>0?$request->package_id:0;
        if($package_id<1){
            return response()->json(['status'=>0,'msg'=>'缺少参数！']);
        }
        $package = DB::table('package')->where(['id'=>$package_id])->first();

        if($package->user_id<1){
            fail('无效的包！');
        }
        //找出上传包的用户信息
        $user = DB::table('users')->where(['id'=>$package->user_id])->first();
        if($package->is_super==1) {
            if ($user->download_package_num < 1) {
                fail('下载次数不足，请充值！');
            }
        }
        if($package->is_super==2){
            if($user->sign_num<1){
                fail('签名次数不足，请充值！');
            }
        }
        return response()->json(['status'=>1,'data'=>$package]);

    }

    public function downStatistics(Request $request){
        $package_id = isset($request->package_id)?$request->package_id:0;
        if($package_id<1){
            return response()->json(['status'=>0,'msg'=>'缺少参数！']);
        }
        $package = DB::table('package')->where(['id'=>$package_id])->first();
        $package->download_num = empty($package->download_num)?0:$package->download_num;
        $data = array(
            'download_num'=>$package->download_num++
        );
        DB::table('package')->where(['id'=>$package_id])->update($data);
        return response()->json(['status'=>1,'data'=>$package]);
    }

    /**
     * @param Request $request
     * @desc 下载生成最新的plist内容，带下载时udid和timestamp
     */
    public function generatePlist(Request $request){
//        $prefix = 'itms-services://?action=download-manifest&url=';
        $plist = $request->plist;
        $plistRoot = public_path().str_replace($_SERVER['SCHEME_URL'],'',$plist);
        $content = file_get_contents($plist);
        $str = 'ipa?udid='.$request->udid.'&timestamp='.time();
        $sContent = str_replace( 'ipa',$str,$content);
        file_put_contents($plistRoot,$sContent);

//        $file_pointer = fopen($plistRoot,"r+");
//        fwrite($plistRoot,$sContent);
//        fclose($file_pointer);
        echo $plist;die;
    }

    public function generateXml(Request $request){
        print_r($_SERVER['SCHEME_URL']);die;
//        $param['token'] = '333';
//        $ch = curl_init();
//        curl_setopt($ch, CURLOPT_URL, "http://47.244.174.73:9502");
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($ch, CURLOPT_HEADER, 1);
//        curl_setopt($ch, CURLOPT_POST, 1);
//        //设置post数据
//        $post_data = $param;
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
//        $output = curl_exec($ch);
//        curl_close($ch);
        $plist = file_get_contents('https://p14fc.cn//applesign/rpaz23@163.com/CYL58XBX6H/0/00008020-000B590A1129002E_20191206164119.plist');
        strtr($plist, 'ipa','ipa222');
        print_r(str_replace( 'ipa','ipa222',$plist));die;
       $img = QrCode::size(100)->color(0,0,0)->backgroundColor(0,255,0)->generate("www.baidu.com");
        print_r($img);die;

        print_r('22');die;
        $url = 'https://p14fc.cn/udid/receive.php?package_id=82';
        $xml ='<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
    <dict>
        <key>PayloadContent</key>
        <dict>
            <key>URL</key>
            <string>'.$url.'</string>
            <key>DeviceAttributes</key>
            <array>
                <string>UDID</string>
                <string>IMEI</string>
                <string>ICCID</string>
                <string>VERSION</string>
                <string>PRODUCT</string>
            </array>
        </dict>
        <key>PayloadOrganization</key>
        <string>p14fc.cn</string>
        <key>PayloadDisplayName</key>
        <string>查询设备UDID</string>
        <key>PayloadVersion</key>
        <integer>1</integer>
        <key>PayloadUUID</key>
        <string>3C4DC7D2-E475-3375-489C-0BB8D737A653</string>
        <key>PayloadIdentifier</key>
        <string>dev.skyfox.profile-service</string>
        <key>PayloadDescription</key>
        <string>本文件仅用来获取设备ID</string>
        <key>PayloadType</key>
        <string>Profile Service</string>
    </dict>
</plist>
';
        $file = public_path().'/udid/82.mobileconfig';
        file_put_contents($file,$xml);
        print_r($file);die;
    }
}
