<?php
namespace App\Common;

/**
 * desc: 常用静态方法
 *
 *
 *
 */
class CCommon {

    //挑选图片base64的位置码,客户端用同样的码重新拼接base64串
    static $char_indexs = '22,92,157,71,241,147,143,205,84,259,191,28,258,251,52,142,76,266,190,127,162,168,51,115,103,197,250,241,40,74,138,137'; 
    //新存放发型图+海报图的目录名称
    static $rand_folder = "NF_U163YQVI4CJA82U";

    /**
     * 极光推送
     * 有时候会出现发送了但是收不到的情况, 解决方案： 极光推送的配置 apns_production 字段 区分正式环境与测试环境
     * @param type $parameters
     */
    static function JPUSH($parameters) {
        $jpush_key = '502e564428e85cf7866a6394';
        $jpush_secret = '06413bd71d1786e085002fca';
        self::_JPUSH_COMMON($parameters, $jpush_key, $jpush_secret);
    }
    /**
     * 店铺端+造型师端推送
     * @param type $parameters
     */
    static function JPUSH_SHOP($parameters) {
        $jpush_key = '4dc550e57ee49293992072e5';
        $jpush_secret = '0238cf7dbe3b7e76aee3cb6a';
        self::_JPUSH_COMMON($parameters, $jpush_key, $jpush_secret);
    }
    static function _JPUSH_COMMON($parameters,$jpush_key,$jpush_secret){
        CLog::WriteLog("JPUSH params : " . var_export($parameters, 1), "JPUSH");
        
        $userids = isset($parameters['userids']) ? $parameters['userids'] : 0;
        $userids = (string) $userids;
        $tags = isset($parameters['tags']) ? $parameters['tags'] : null;
        $title = isset($parameters['title']) ? $parameters['title'] : '新消息';
        $message = isset($parameters['message']) ? $parameters['message'] : '';
        //推送的分类： 认证，点赞，评论
        $kind = isset($parameters['kind']) ? $parameters['kind'] : 'publish';
        $id = isset($parameters['id']) ? $parameters['id'] : 0;
        $item_id = isset($parameters['item_id']) ? $parameters['item_id'] : 0; //评论的时候多加一个作品id参数，兼容老版本
        if (empty($userids) && empty($tags)) {
            CLog::WriteLog("JPUSH error : " . var_export($parameters, 1), "JPUSH");
            exit;
        }

        $pusher = new CJPush($jpush_key, $jpush_secret);
        $params = array(
            'kind' => $kind,
            'id' => $id,
            'item_id' => $item_id,
            );
        if(isset($parameters['shop_name'])){
            $params['shop_name'] = $parameters['shop_name'];
        }
        if(isset($parameters['shop_id'])){
            $params['shop_id'] = $parameters['shop_id'];
        }
        if(isset($parameters['orderStatus'])){
            $params['orderStatus'] = $parameters['orderStatus'];
        }
        $ret = $pusher->sendMessage($userids, $tags, $message, $title, $params);
        CLog::WriteLog("JPUSH result : " . var_export($ret, 1), "JPUSH");
    }
    
    /**
     * 把图片转换成base64字符串
     * @param type $filename
     * @return string
     */
    static function BASE64IMG($filename){
        $imageData = "";
        $imageDetails = getimagesize($filename);
        if ($picture = file_get_contents($filename)) {
                // base64 encode the binary data, then break it
                // into chunks according to RFC 2045 semantics
                $base64 = (base64_encode($picture));
//                $imageData = 'data:'.$imageDetails['mime'].';base64,' . $base64;
                $imageData = $base64;
        }
        return $imageData;
    }
    
    /**
     * 把base64字符串分割成两部分
     */
    static function SECURE_IMG($filename){
        $sub = [];
        $char_index_arr = explode(",", self::$char_indexs);
        $base64_str = self::BASE64IMG($filename);
        if(!empty($base64_str)){
            foreach ($char_index_arr as $index) {
                $sub[] = $base64_str[$index];
                $base64_str[$index] = "P"; //不能使用空格，空格的16进制会暴露位置，随便用一个字符
            }
        }
        return array("main"=>$base64_str,"sub"=>  implode("|", $sub));
    }
    
    /**
     * 生成32位随机数
     * @return type
     */
    static function RAND_INDEX(){
        $arr = [];
        for ($i=0;$i<32;$i++) {
            $arr[] =  mt_rand(10, 300);
        }
        RETURN (implode(",", $arr));
    }
    
    /**
     * 生成随机目录
     * @return type
     */
    static function RAND_FOLER(){
        $Caracteres = 'ABCDEFGHIJKLMOPQRSTUVXWYZ0123456789_'; 
        $QuantidadeCaracteres = strlen($Caracteres); 
        $arr = [];
        for ($i=0;$i<15;$i++) {
            $arr[] =  $Caracteres[mt_rand(0, $QuantidadeCaracteres-1)];
        }
        RETURN "NF_".implode("", $arr);
    }
    
    /**
     * 生成根据shopid产生的shop用户名
     * @param type $shopid
     * @return type
     */
    static function MK_SHOP_ID($shopid){
        return empty($shopid) ? "" : str_pad($shopid, 6, "0", STR_PAD_LEFT);
    }
    
    /**
     * 根据shop用户名返回真实shopid
     * @param type $shop_username
     * @return type
     */
    static function REVERT_SHOP_ID($shop_username){
        preg_match('/[^0].*/', $shop_username, $matches);
        if(!empty($matches) && isset($matches[0])){
            return $matches[0];
        }
    }
    static function array_under_reset($array, $key, $type=1){
        if (is_array($array)){
            $tmp = array();
            foreach ($array as $v) {
                if ($type === 1){
                    $tmp[$v[$key]] = $v;
                }elseif($type === 2){
                    $tmp[$v[$key]][] = $v;
                }
            }
            return $tmp;
        }else{
            return $array;
        }
    }

    /**
     * @example
     * test.php
     * @return void
     * 检查必须参数是否有
     */
    static function check_request_parameter($check_param){
        foreach($check_param as $value) {
            $return['error'] = -1;
            $return['msg'] = '缺少参数'.$value;
            if (!isset($_REQUEST[$value])) exit(json_encode($return));
        }

    }
    
    /*
     * desc: 按照时间来生成一个唯一订单号id
     * 连前缀19位(前缀1位 后面18位)
     * 线上$mlen=18 测试服 $mlen=17
     * 备注：同一时间下单并发1000肯定没有问题
     */
    static function realPayId()
    {
        
        $mlen=18;
        $prex = mt_rand(1, 8);//只能1-8
        $id  = str_replace('.', '', microtime(true)).mt_rand(100000,999999);
        $id = substr($id, 0, $mlen);
        $id = $prex.$id;
        return intval($id);
    }
    
    /**
     * 生成32位第三方交易id
     * @param $userid 用户id
     * @param type=1 支付订单  type=2即时交易转账
     * @return number
     */
    static function generatePayId($userid,$type=1)
    {
    
        $mlen=18;
        if($type==2){
            $prex = 9;//退款-即时提现
        }else{
            $prex = mt_rand(1, 8);//只能1-8
        }
        
        $id  = str_replace('.', '', microtime(true)).mt_rand(100000,999999);
        $id = substr($id, 0, $mlen);
        $userid = substr(str_pad($userid,12,'0',STR_PAD_LEFT),0,12);
        $dealNo = $prex.$id.'_'.$userid;
        return $dealNo;
    }
    
    static function get_ip(){
        //判断服务器是否允许$_SERVER
        if(isset($_SERVER)){
            if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
                $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }elseif(isset($_SERVER['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            }else{
                $realip = $_SERVER['REMOTE_ADDR'];
            }
        }else{
            //不允许就使用getenv获取
            if(getenv("HTTP_X_FORWARDED_FOR")){
                $realip = getenv( "HTTP_X_FORWARDED_FOR");
            }elseif(getenv("HTTP_CLIENT_IP")) {
                $realip = getenv("HTTP_CLIENT_IP");
            }else{
                $realip = getenv("REMOTE_ADDR");
            }
        }
    
        return $realip;
    }
    static function getAddrByLngLat($lnglat){
//        $lnglat = "116.31450000000000,39.95097000000000";
//        $address = "120.72482000000000,31.26054900000000";
//        $address = "121.4888110000,31.2340770000";
//        $lnglat = "87.5454300000,43.8754370000";
        if(empty($lnglat)){
            return [];
        }
        $url="http://restapi.amap.com/v3/geocode/regeo?output=json&location=".$lnglat."&key=954f748035f8e75c219e67b23c0a7389";
        $result=file_get_contents($url);
        CLog::WriteLog("file_get_contents result : \r\n".  var_export($result,1),"getAddrByLngLat");
        if($result)
        {
//            var_dump($result);die;
            $result = json_decode($result,true);
            CLog::WriteLog("json_decode result : \r\n".  var_export($result,1),"getAddrByLngLat");
            if(!empty($result['status'])&&$result['status']==1){
                $province = isset($result['regeocode']['addressComponent']['province']) ? $result['regeocode']['addressComponent']['province'] : '';
                $city = isset($result['regeocode']['addressComponent']['city']) ? $result['regeocode']['addressComponent']['city'] : '';
                $address = isset($result['regeocode']['formatted_address']) ? $result['regeocode']['formatted_address'] : '';
                if(empty($province) && empty($city)){
                    return [];
                }elseif(empty($province) && !empty($city)){
                    $province = $city;
                }else if(empty($city) && !empty($province)){
                    $city = $province;
                }
                return ['province'=>$province,'city'=>$city,'address'=>$address];
            }else{
                return [];
            }
        }
        return [];
    }
    static function getDetailAddrByLngLat($lnglat){
//        $lnglat = "116.31450000000000,39.95097000000000";
//        $address = "120.72482000000000,31.26054900000000";
//        $address = "121.4888110000,31.2340770000";
//        $lnglat = "87.5454300000,43.8754370000";
        if(empty($lnglat)){
            return '';
        }
        $url="http://restapi.amap.com/v3/geocode/regeo?output=json&location=".$lnglat."&key=954f748035f8e75c219e67b23c0a7389";
        if($result=file_get_contents($url))
        {
//            var_dump($result);die;
            $result = json_decode($result,true);
            if(!empty($result['status'])&&$result['status']==1){
                return isset($result['regeocode']['formatted_address']) ? $result['regeocode']['formatted_address'] : '';
            }else{
                return '';
            }
        }
        return '';
    }

}
