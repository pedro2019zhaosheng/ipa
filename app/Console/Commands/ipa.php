<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class ipa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:ipa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $deviceList = DB::table('device')->get();
        foreach($deviceList as $k=>$v){
            if($v->ipa_url){
                continue;
            }
            $udid = $v->udid;
            $package = DB::table('package')->where(['id'=>$v->package_id])->first();
            if($package){
                $ipa_arr = explode('/', $package->ipa_url);
                $ipa_url = "/usr/local/homeroot/ipa/public/storage/".end($ipa_arr);
                $buddle_id = $package->buddle_id;
                if($package->is_push==0){
                    $buddle_id = $buddle_id.'.dfc_wx_'.$v->apple_id;
                }
                //包设置了推送而且包的原始账号id和自动分配的不一致需要建新的buddleID
                if($package->is_push==1&&$package->apple_id!=$v->apple_id){
                    $buddle_id = $buddle_id.'.dfc_wx_'.$v->apple_id;
                }
                $packageName = $package->name?$package->name:$package->buddle_id;
            }

            //第一步根据苹果账号ID获取个人开发者账号信息
            //处理apple账号数量超过99
            $appleId = $v->apple_id;
            if($v->apple_id>0){
                if($package->apple_id>0){
                    $appleId = $package->apple_id;
                }else{
                    $appleId =  $v->apple_id;
                }
                $appleDeveloperInfo = DB::table('apple')->where(['id'=>$appleId])->first();
            }

//            else{
//                $appleList = DB::table('apple')->where('udid_num','<',99)->get();
//                $appleDeveloperInfo = $appleList[0];
//                $appleId = $appleDeveloperInfo->id;
//            }
//            $buddle_id = $appleDeveloperInfo->buddle_id;//获取buddleID

            $account = $appleDeveloperInfo->account;
            $secret = $appleDeveloperInfo->secret_key;
            $certificate_id = $appleDeveloperInfo->certificate_id;
            $p12_arr = explode('/', $appleDeveloperInfo->p12_url);
            if($v->apple_id!=$package->apple_id&&$package->apple_id>0){
                //分配的账号不是第一次上传的账号，为了打出的包正常推送小心，需要用初始账号的p12证书，buddelID同理
                $appleDeveloperInfo = DB::table('apple')->where(['id'=>$package->apple_id])->first();
                $p12_arr = explode('/', $appleDeveloperInfo->p12_url);
            }
            $p12_url = "/usr/local/homeroot/ipa/public/storage/".end($p12_arr);
            $cmdRoot = "cd /usr/local/homeroot/ipasign-master/nomysql &&";
            $stepOneCmd = "$cmdRoot sudo  /bin/ruby  /usr/local/homeroot/ipasign-master/nomysql/checkLogin.rb $account $secret";
            exec($stepOneCmd,$out,$status);
            if($status!=0){
                //标识账号需要重新登录
                DB::table('apple')->where(['id'=>$v->apple_id])->update(['status'=>2]);
            }
            // $stepOne = system($stepOneCmd,$status,$msg);
//            try{
//                $res = exec($stepOneCmd,$out);
//
//            }catch (Exception $e) {
//                echo $e->getMessage();die;
//                // die(); // 终止异常
//            }

            //第二步生成证书
            $stepTwoCmd = "$cmdRoot sudo  /bin/ruby saveCert.rb $account $secret $certificate_id $p12_url";
            exec($stepTwoCmd,$outTwo,$statusTwo);
            if($statusTwo!=0){
                $stepOneCmd = "$cmdRoot sudo  /bin/ruby  /usr/local/homeroot/ipasign-master/nomysql/checkLogin.rb $account $secret";
                exec($stepOneCmd,$out,$status);
            }

            if(isset($outTwo[1])){
                $clientKey = "/applesign/$account/$certificate_id/client_key.pem";
                $privateKey = "/applesign/$account/$certificate_id/private_key.pem";
            }
            //第三步将udid写入开发者账号
            $stepThreeCmd = "$cmdRoot sudo  /bin/ruby addUUid.rb $account $secret  $udid $buddle_id $certificate_id";
            exec($stepThreeCmd,$outThree,$statusThree);


            if($statusThree!=0){
                exec($stepThreeCmd,$outThree,$forOut);
            }
            if(isset($outThree[0])){
                $mobileprovision  = "/applesign/$account/$certificate_id/sign.$buddle_id.mobileprovision";
            }
            $outFour = [];//重新赋值
            //第四步生成ipa包
            $stepFourCmd = "$cmdRoot sudo /bin/ruby signIpa.rb $account  $udid  $ipa_url $buddle_id $certificate_id /applesign/$account/$certificate_id/sign.$buddle_id.mobileprovision /applesign/$account/$certificate_id/client_key.pem /applesign/$account/$certificate_id/private_key.pem $packageName";
            exec($stepFourCmd,$outFour,$re);
            $plist = '';
            $ipa = '';
            if(isset($outFour[0])){
                $ipa = $outFour[0];
                $plist = $outFour[1];
            }
            //入库
            $scheme_url = env('SCHEME_URL');
            if($re!=1){
                file_put_contents('/tmp/ipa.txt',$stepOneCmd.PHP_EOL.$stepTwoCmd.PHP_EOL.$stepThreeCmd.PHP_EOL.$stepFourCmd.PHP_EOL
                    .$plist.PHP_EOL,FILE_APPEND);

                // $download_url = 'http://'.$_SERVER['HTTP_HOST'].'/storage/'.$filename;
                // $plistUrl = 'https://'.$_SERVER['HTTP_HOST'].'/storage/'.$plistName;//todo
                // $plistUrl = "https://test.daoyuancloud.com/install_ipa/".$plistName;
                $download_url = $scheme_url.$ipa;
                $plistUrl = $scheme_url.$plist;
                //扣除打包次数
                $user_id = $package->user_id;
                $userInfo = DB::table('users')->where(['id'=>$user_id])->first();
                //获取已打包设备数量
                $packge_num = DB::table('device')->where('package_id','=',$package->id)->where('is_reduce','=',0)->count();
                DB::table('users')->where(['id'=>$user_id])->update(['sign_num'=>$userInfo->sign_num-$packge_num]);
                $data = [
                    'apple_id'=>$appleId,
                    'user_id'=>$package->user_id,
                    'buddle_id'=>$buddle_id,
                    'package_id'=>$v->package_id,//todo
                    'udid'=>$udid,
                    'is_reduce'=>1,
                    'ipa_url'=>$download_url,
                    'plist_url'=>$plistUrl,
                    'created_at'=>date('Y-m-d H:i:s')
                ];
                DB::table('device')->where(['id'=>$v->id])->update($data);
                //每次下载量累加
//                DB::table('package')->where(['id'=>$v->package_id])->update(['download_num'=>$package->download_num+1]);



                //日志
                $log = [
                    'user_id'=>$package->user_id,
                    'udid'=>$udid,
                    'type'=>2,
                    'created_at'=>date('Y-m-d H:i:s')
                ];
                DB::table('log')->insert($log);
            }
            echo 'success';

        }
//        if($deviceInfo){
//            $url = 'itms-services://?action=download-manifest&amp;url='.$deviceInfo->plist_url;//todo
//            // $url = "itms-services://?action=download-manifest&amp;url=https://test.daoyuancloud.com/install_ipa/9efa99314d8da5632a37dfa2abad6ac5cedb715e_20191030142348.plist";
////            echo json_encode(['status'=>1,'message'=>'缺少参数！','url'=>$deviceInfo->plist_url]);die;
//            header("Location: $url");
//            exit(0);
//        }
        //获取包信息

        // echo $out[0];

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



    }
}
