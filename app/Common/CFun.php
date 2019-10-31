<?php
namespace App\Common;
/**
 * desc: 一系列的小函数
 *       一个函数就是一个独立的功能
 *
 *
*/
class CFun {

    /*
    * desc abc_def_gh -> AbcDefGh
    *
    *
    */
    static public function formatUnderLine2ConcaveConvex($name)
    {
        $name = trim(preg_replace("/[^0-9a-z]/si", ' ', $name));
        $name = preg_replace("/\s{2,}/si", ' ', $name);
        $name = ucwords(strtolower($name));
        $name = str_replace(' ', '', $name);
        return $name;
    }
    /*
    * desc: 合并两个值(主要是对array_merge的改进)
    *
    */
    static public function arrayMergeOverwrite()
    {
        $args = func_get_args();
        if(empty($args))return null;
        
        $merged = array_shift($args);
        $merged = is_array($merged)?$merged:array($merged);
     
        if($args){
            foreach($args as $arg){
                $arg = is_array($arg)?$arg:array($arg);
                foreach($arg as $k => $v){
                     $merged[$k] = $v;
                }
            }
        }
        // print_r($merged);
        return $merged;
    }
    /*
    * 数组递归合并(覆盖方式, 非array_merge_recursive的追加方式)
    *
    * @param array $arr1   数组一
    * @param array $arr2   数组二
    * @param array $arr..  数组...
    * @return array
    */
    static function arrayMergeOverwriteRecursive($arr1, $arr2)
    {
        $rs = $arr1;
        foreach(func_get_args() as $arr){
            if(!is_array($arr)){
                return false;
            }
            foreach($arr as $key=>$val){
                $rs[$key] = isset($rs[$key]) ? $rs[$key] : array();
                $rs[$key] = is_array($val) ? self::array_merge_recursive_overwrite($rs[$key], $val) : $val;
            }
        }
        return $rs;
    }
    /*
    * desc: 获取多维数组的组合
    *
    *@arrays --- array(多维数组)
    *   $a = array('1', '2'); 
        $b = array('1', '2', '3'); 
        $c = array('11', '22'); 
        result = combinationArrays(array($a,$b,$c))
        restut 为:
            Array
            (
                [0] => 1:1:11
                [1] => 1:1:22
                [2] => 1:2:11
                [3] => 1:2:22
                [4] => 1:3:11
                [5] => 1:3:22
                [6] => 2:1:11
                [7] => 2:1:22
                [8] => 2:2:11
                [9] => 2:2:22
                [10] => 2:3:11
                [11] => 2:3:22
            )
    *
    */
    static function combinationArrays($arrays) 
    {
        if(1 == count($arrays))return $arrays[0];
        $arr = array();
        $ar1 = array_shift($arrays);
        $ar2 = array_shift($arrays);
        foreach ($ar1 as $v1){
            foreach ($ar2 as $v2){ 
                $arr[] = "$v1:$v2"; 
            }
        }
        if(empty($arrays)){
            return $arr;
        }else{
            array_unshift($arrays, $arr);
            return self::combinationArrays($arrays);
        }
    }

    /*
    * desc: unset所有值为null的项
    *
    *
    *
    */
    static function removeArrayNull(&$array, $emptystring=false, $zero=false, $spval=null)
    {
        if(!is_array($array))return $array;
        foreach ($array as $k=>&$vs){
            self::removeArrayNull($vs, $emptystring, $zero, $spval);
            if(is_null($vs)){
                unset($array[$k]);
            }else if(''===$vs){
                if($emptystring)unset($array[$k]);
            }else if(0===$vs){
                if($zero)unset($array[$k]);
            }else if($spval===$vs){
                unset($array[$k]);
            }
        }
        return $array;
    }

    /*
    * desc: 二维数组排序
    * call: arraySorting(arr, f1,SORT_ASC|SORT_DESC, f2,SORT_ASC|SORT_DESC)
    *
    *return the $arr that was sorted
    */
    static function arraySorting($arr)
    {
        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row){
                    $tmp[$key] = isset($row[$field])?$row[$field]:null;
                }
                $args[$n] = $tmp;
            }
        }
        $args[] = &$data;
        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }
    /*
    * desc: 返回数组的维度
    * @param  [type] $arr [description]
    * @return [type]      [description]
    */
    static function arrayLevel($arr)
    {
        $al = array(0);
        if(!function_exists('__aL')){
            function __aL($arr,&$al,$level=0){
                if(is_array($arr)){
                    $level++;
                    $al[] = $level;
                    foreach($arr as $v){
                        __aL($v,$al,$level);
                    }
                }
            }
        }
        __aL($arr,$al);
        return max($al);
    }
    /*
    * desc: 根据二维数组的一某几个字段去重
    */
    static function distinctArray($arr, $fields)
    {
        if(empty($arr) || empty($fields))return $arr;
        $grr = self::groupBy($arr, $fields);
        if(!$grr)return $arr;

        $rArr = array();
        foreach($grr as $field => $list){
            $rArr[] = current($list);
        }
        return $rArr;
    }
    /*
    * desc: crc的字符表现形式
    *
    *
    */
    static function crcLetter($str)
    {
        $crc32   = sprintf("%u", crc32($str));
        $letters = '';
        for($i=0,$len=strlen($crc32); $i<$len; $i++){
            $letters .= chr(ord(substr($crc32,$i,1)) + 17);
        }
        return $letters;
    }
    /*
    * desc: crc的无符号表现形式
    *
    *
    */
    static function crcU32($str)
    {
        $c = crc32($str);
        return $c < 0 ? $c += 4294967296 : $c;
    }
    /*
    * desc: 将n个数字编码成一个唯一的编码
    *
    *@args --- 任意参数
    *
    */
    static function touniqueidArgs()
    {
        $args = func_get_args();
        sort($args);
        return self::crcU32(implode('-',$args));
    }

    /**
    * desc: 将一个mysql数据库表记录按照某字段组装成tree格式(广度遍历)
    * call: table2tree($dataArr, 'f1,f2')
    *@fields  --- str 可支持多个字段
    *@dataArr --- array (
    *                       array(id=>1, fid=>10, gid=20, name=>...),
    *                       array(id=>2, fid=>10, gid=30, name=>...),
    *                   )
    *eg.
    *array(10=>array(
    *                   array(id=>1, fid=>10, gid=20, name=>...),
    *                   array(id=>2, fid=>10, gid=30, name=>...),
    *               ))
    *sortby  --- int [11:key升序,12:key降序,21:val升序,22:val降序]
    *return void
    */
    static function groupBy($dataArr, $fields, $sortby=null)
    {
        if(!is_array($dataArr)) return null;
        $fArr   = explode(',', $fields);
        $field  = array_shift($fArr); //当前字段
        $fields = implode(',', $fArr);
        $keyArr = array();

        $groupedArr = array();

        //dataArr分组后存放到临时数组中
        foreach($dataArr as $row){
            if(!isset($row[$field])) continue;
            $key = $row[$field];
            $keyArr[] = $key;
            $groupedArr[$key][] = $row;
        }
        //end dataArr分组后存放到临时数组中

        $keyArr = array_unique($keyArr);
        if(count($fArr) > 0){
            foreach($keyArr as $key){
                //广度遍历
                $groupedArr[$key] = self::groupBy($groupedArr[$key], $fields);
            }
        }
        if($sortby){
            switch ($sortby) {
                case 11:
                    ksort($groupedArr);  break;
                case 12:
                    krsort($groupedArr); break;
                case 21:
                    asort($groupedArr);  break;
                case 22:
                    arsort($groupedArr); break;                
            }
        }
        return $groupedArr;
    }
    /*
    * desc: 编辑转换
    * 如果ArrOr是array那返回亦是array,反之，如果是scalar那返回亦是scalar
    *@Arror --- mix 
    *
    */
    static function encodeAny($ArrOr, $to='gbk', $from='utf-8')
    {
        if(!$ArrOr)return $ArrOr;
        if(!function_exists('_ec_arr')){
            function _ec_arr(&$arr, $to, $from){
                foreach($arr as &$vs){
                    if(is_array($vs)){
                        _ec_arr($vs, $to, $from);
                    }else{
                        $ok = preg_match("/^[a-z0-9\x01-\x7f]+$/i", $vs);//128以下字符
                        if(!$ok){
                            $vs = iconv($from, "{$to}//TRANSLIT//IGNORE", $vs);
                        }
                    }
                }
            }
        }
        if(is_array($ArrOr)){
            _ec_arr($ArrOr, $to, $from);
            return $ArrOr;
        }else{
            $arr = array($ArrOr);
            _ec_arr($arr, $to, $from);
            return $arr[0];
        }
    }
    static function isWindows()
    {
        return 'WINNT' == PHP_OS ? true : false;
    }
    /*
    * desc: 异步执行命令(windows下只能半异步)
    *
    *
    */
    static function Process($cmd, $asyn=true)
    {
        if('windows' == strtolower(substr(php_uname(), 0, 7))){
            pclose(popen("start /B ". $cmd, "r"));  
        }else{ 
            exec($cmd . " > /dev/null &");//这是一种方式
            return;
            /*
            $descriptorspec  = array(   
                0 => array( 'pipe' , 'r' ) ,  #输入  
                1 => array( 'pipe' , 'output' , 'w' ) , #输出，可以为管道或文件  
                2 => array( 'pipe' , 'w' )   #错误日志，可以为管道或文件  
            );
            $proc = proc_open($cmd, $descriptorspec, $pipes);
            if($asyn){
                proc_close($proc);
                return true;
            }
            //未完成
            //在此接收数据...
            proc_close($proc);
            */
        } 
    }
};