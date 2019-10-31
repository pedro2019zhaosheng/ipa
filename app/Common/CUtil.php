<?php
namespace App\Common;
/**
 * desc: 常用小函数(逻辑层)
 *
 *
 *
*/
class CUtil {
  
    static function humanizeNumeric($numeric, $dots=1)
    {
        $numeric = floatval($numeric);
        if($numeric < 0.1)return $numeric;
        $_dot_right = $numeric - floor($numeric);
        if(0 == $_dot_right) return intval($numeric);
        return round($numeric, $dots);
    }
    static function formatPrice($price)
    {
        return self::humanizeNumeric($price);
    }
    /*
    * desc: 将一数组平均分成n段
    *
    */
    static function averageArray($arr, $ses=3)
    {
        if(!is_array($arr) || empty($arr))return $arr;
        $len  = count($arr);
        $num  = round($len/$ses); //每一段的个数
        $retArr = array();
        for($i=0; $i<$ses; $i++){
            if($i == $ses-1){
                $arrt = array_slice($arr, $num*$i, $num*2);
            }else{
                $arrt = array_slice($arr, $num*$i, $num);
            }
            if(empty($arrt)){
                if(isset($last)) $arrt = $last;
            }
            $retArr[] = $arrt;
            $last = $arrt;
        }
        return $retArr;
    }
    static function IsMobile($str, &$mobile=null)
    {
        if(preg_match("/1[3,5,8][0-9]{9}$/i", $str, $pArr)){
            $mobile = $pArr[0];
            return true;
        }
        return false;
    }
    static function IsEmail($str, &$email=null)
    {
        if(preg_match("/^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+((\.[a-zA-Z0-9_-]{2,3}){1,2})$/i", $str, $pArr)){
            $email = $pArr[0];
            return true;
        }
        return false;
    }
    static function IsFalse($v)
    {
        return is_bool($v) && !$v;
    }

    /*
    * desc: 把表单数组转换成db格式的二维数组
    *
    */
    static function formArrayFormatting($dataArr, $default=null)
    {
        if(!is_array($dataArr))return $dataArr;
        $formatedArr = array();
        $fieldArr = array_keys($dataArr);

        $len = 1;
        foreach($dataArr as $row){
            $_l = count($row);
            $len = $_l > $len ? $_l : $len;
        }
        // $len = count(current($dataArr));

        for($i=0; $i<$len; $i++){
            $row = array();
            foreach($fieldArr as $field){
                $row[$field] = isset($dataArr[$field][$i])?$dataArr[$field][$i]:$default;
            }
            $formatedArr[] = $row;
        }
        return $formatedArr;
    }
    
    /* 
    *根据新浪IP查询接口获取IP所在地 
    */ 
   static function getIPLoc_sina($queryIP){
       CLog::WriteLog("getIPLoc_sina call : \r\n".  var_export($queryIP,1),"getAddrByLngLat");
//       $url = 'http://int.dpool.sina.com.cn/iplookup/iplookup.php?format=json&ip='.$queryIP;
       $url = 'http://ip.taobao.com/service/getIpInfo.php?ip='.$queryIP;
       $ch = curl_init($url);
       curl_setopt($ch,CURLOPT_ENCODING ,'utf8');
       curl_setopt($ch, CURLOPT_TIMEOUT, 10);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回
       $ret = curl_exec($ch);
       $location = json_decode($ret,true);
       curl_close($ch);
       CLog::WriteLog("getIPLoc_sina result : \r\n".  var_export($location,1),"getAddrByLngLat");
       if(isset($location["code"]) && intval($location["code"]) === 0){
           return $location['data']["country"]." ".$location['data']["region"]." ".$location['data']["city"].'市';
       }else{
           return "";
       }
   } 
   
   /**
    * 根据字节数算出大小
    * @param type $bytes
    * @param type $decimals
    * @return double
    */
   function getFileSize($bytes, $decimals = 2) {
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor));
    }

    function human_filesize($bytes, $decimals = 2) {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
    }

};

