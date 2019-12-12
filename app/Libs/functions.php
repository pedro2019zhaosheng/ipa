<?php
use Illuminate\Support\Facades\DB;
/**
 * 公用的方法  返回json数据，进行信息的提示
 * @param $status 状态
 * @param string $message 提示信息
 * @param array $data 返回数据
 */
function showMsg($type,$data = array(),$msg=''){
    if($msg==''&&$type==1){
        $msg = '操作成功！';
    }else{
        $msg = $msg?$msg:'操作失败！';
    }
    $result = array(
        'status' => $type==1?1:0,
        'message' =>$msg,
        'data' =>$data
    );
    exit(json_encode($result));
}

function getSql($param){
    DB::connection()->enableQueryLog();
    $execute = $param;
    $sql = DB::getQueryLog();
    return $sql;
}

function getUserInfo($token){
   $info = DB::table('users')->where('token',$token)->first();
   if($info){
       return $info;
   }else{
       showMsg(2,new \stdclass,'无效的token！');
   }
}

function scanFile($path) {
    global $result;
    $files = scandir($path);
    $arr = [];
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            if (is_dir($path . '/' . $file)) {
                scanFile($path . '/' . $file);
            } else {
                $pathinfo = pathinfo($path);
                $filename = $pathinfo['filename'];
                $arr[] = basename($file);
                array_push($arr,$filename);
                $arr = array_unique($arr);
                //获取文件内容
                $intro = '';
                foreach($arr as $vs){
                    if($vs=='intro.txt'){
                        $intro = $vs;
                    }
                }

                $content = file_get_contents($path.'/'.$intro);
                array_push($arr,$content);
                $arr = array_filter($arr);
                $result[$filename] = $arr;
            }
        }

    }
    return $result;
}


function mb_str_splits ($string, $len=1) {
	$start = 0;
	$strlen = mb_strlen($string);
	while ($strlen) {
		$array[] = mb_substr($string,$start,$len,"utf-8");
		$string = mb_substr($string, $len, $strlen,"utf-8");
		$strlen = mb_strlen($string);
	}
	return $array;
}


function filterArr($data,$arr){
    foreach($arr as $v){
        if($v['to']==$data['from']){
            return true;
        }
        return false;
    }
}


function assoc_unique($arr, $key) {

    $tmp_arr = array();

    foreach ($arr as $k => $v) {

        if (in_array($v[$key], $tmp_arr)) {//搜索$v[$key]是否在$tmp_arr数组中存在，若存在返回true

            unset($arr[$k]);

        } else {

            $tmp_arr[] = $v[$key];

        }

    }

    // sort($arr); //sort函数对数组进行排序

    return $arr;

}
function test($a=0,&$result=array()){
    $a++;
    if ($a<10) {
        $result[]=$a;
        test($a,$result);
    }
    print_r($a);die;
    echo $a;
    return $result;

}

function generateTree($array){
    $items = [];
    foreach($array as $key=>$value){
        $items[$value['id']]=$value;
    }
    $trees = [];
    foreach($items as $k=>$v){
        if(isset($items[$v['pid']])){
            $items[$v['pid']]['son'][] = &$items[$k];
        }else{
            $trees[] = &$items[$k];
        }
    }
    return $trees;
}


 function success($msg = '成功',$data = [])
{
    $result = [
        'status'  => 1,
//            'code'    => 200,
        'message' => $msg?$msg:'成功',
        'data'    => $data,
    ];
    exit(json_encode($result));
}

 function fail($message,$data = [])
{

    $result = [
        'status'  => 0,
//            'code'    => $code['code'],
        'message' => $message,
        'data'    => $data,
    ];
    exit(json_encode($result));
}


function generateXml($data){
    $package_id = $data['id'];
    $url = env('SCHEME_URL').'/udid/receive.php?package_id='.$package_id;
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
        <string>授权安装APP进入下一步</string>
        <key>PayloadDisplayName</key>
        <string>'.$data['name'].'</string>
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
    $file = public_path().'/udid/'.$package_id.'.sign.mobileconfig';
    file_put_contents($file,$xml);
    $cmdRoot = "sudo su && cd /usr/local/homeroot/ipasign-master/nomysql &&";
    $cmd = "$cmdRoot   /usr/local/ruby-2.6.4/bin/ruby signMobileConfig.rb $file $package_id";
    @exec($cmd,$out,$status);
    sleep(2);
    //删除sign.mobileconfig
    exec("sudo rm -rf $file");
    return 1;
}

function generatePlist($package){
    $package_id = $package['id'];
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
                                        <string>'.$package['ipa_url'].'</string>
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
    return $ipa_url;
}



function shortUrl($source, $url_long) {
    $source = '1323983993';
    // 参数检查
    if (empty($source) || !$url_long) {
        return false;
    }
    // 参数处理，字符串转为数组
    if (!is_array($url_long)) {
        $url_long = array($url_long);
    }

    // 拼接url_long参数请求格式
    $url_param = array_map(function ($value) {
        return '&url_long=' . urlencode($value);
    }, $url_long);
    $url_param = implode('', $url_param);

    // 新浪生成短链接接口
    $api = 'http://api.t.sina.com.cn/short_url/shorten.json';    // 请求url
    $request_url = sprintf($api . '?source=%s%s', $source, $url_param);
    $result = array();    // 执行请求
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $request_url);
    $data = curl_exec($ch);
    if ($error = curl_errno($ch)) {

        return false;
    }
    curl_close($ch);
    $result = json_decode($data, true);
    print_r($result);die;

    if (isset($result[0]['url_short'])) {
        return $result[0]['url_short'];
    } else {
        return '';
    }
}