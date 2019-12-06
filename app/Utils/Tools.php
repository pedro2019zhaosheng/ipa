<?php

namespace App\Utils;

use App\Models\User;
use App\Models\Node;
use App\Models\Relay;
use App\Services\Config;
use DateTime;
use App\Utils\QQWry;

class Tools
{
    static $download_url = "http://192.168.115.200:8093/index1/index.html";

    /**
     * 根据流量值自动转换单位输出
     */
    public static function flowAutoShow($value = 0)
    {
        $kb = 1024;
        $mb = 1048576;
        $gb = 1073741824;
        $tb = $gb * 1024;
        $pb = $tb * 1024;
        if (abs($value) > $pb) {
            return round($value / $pb, 2) . "PB";
        } elseif (abs($value) > $tb) {
            return round($value / $tb, 2) . "TB";
        } elseif (abs($value) > $gb) {
            return round($value / $gb, 2) . "GB";
        } elseif (abs($value) > $mb) {
            return round($value / $mb, 2) . "MB";
        } elseif (abs($value) > $kb) {
            return round($value / $kb, 2) . "KB";
        } else {
            return round($value, 2) . "B";
        }
    }

    //虽然名字是toMB，但是实际上功能是from MB to B
    public static function toMB($traffic)
    {
        $mb = 1048576;
        return $traffic * $mb;
    }

    //虽然名字是toGB，但是实际上功能是from GB to B
    public static function toGB($traffic)
    {
        $gb = 1048576 * 1024;
        return $traffic * $gb;
    }


    /**
     * @param $traffic
     * @return float
     */
    public static function flowToGB($traffic)
    {
        $gb = 1048576 * 1024;
        return $traffic / $gb;
    }

    /**
     * @param $traffic
     * @return float
     */
    public static function flowToMB($traffic)
    {
        $gb = 1048576;
        return $traffic / $gb;
    }

    //获取随机字符串

    public static function genRandomNum($length = 8)
    {
        // 来自Miku的 6位随机数 注册验证码 生成方案
        $chars = '0123456789';
        $char = '';
        for ($i = 0; $i < $length; $i++) {
            $char .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $char;
    }

    public static function genRandomChar($length = 8)
    {
        // 密码字符集，可任意添加你需要的字符
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $char = '';
        for ($i = 0; $i < $length; $i++) {
            $char .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $char;
    }

    public static function genToken()
    {
        return self::genRandomChar(64);
    }

    public static function is_ip($a)
    {
        return preg_match("/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/", $a);
    }


    // Unix time to Date Time
    public static function toDateTime($time)
    {
        return date('Y-m-d H:i:s', $time);
    }

    public static function secondsToTime($seconds)
    {
        $dtF = new DateTime("@0");
        $dtT = new DateTime("@$seconds");
        return $dtF->diff($dtT)->format('%a 天, %h 小时, %i 分 + %s 秒');
    }

    public static function genSID()
    {
        $unid = uniqid(Config::get('key'));
        return Hash::sha256WithSalt($unid);
    }

    public static function genUUID()
    {
        // @TODO
        return self::genSID();
    }

    public static function getLastPort()
    {
        $user = User::orderBy('id', 'desc')->first();
        if ($user == null) {
            return 1024; // @todo
        }
        return $user->port;
    }

    public static function getAvPort()
    {
        //检索User数据表现有port
        $det = User::pluck('port')->toArray();
        $port = array_diff(range(Config::get('min_port'), Config::get('max_port')), $det);
        shuffle($port);
        return $port[0];
    }

    public static function getAllAvPort()
    {
        //检索User数据表现有port
        $det = User::pluck('port')->toArray();
        $port = array_diff(range(Config::get('min_port'), Config::get('max_port')), $det);
        shuffle($port);
        return $port;
    }

    public static function base64_url_encode($input)
    {
        return strtr(base64_encode($input), array('+' => '-', '/' => '_', '=' => ''));
    }

    public static function base64_url_decode($input)
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }

    public static function getDir($dir)
    {
        $dirArray[] = null;
        if (false != ($handle = opendir($dir))) {
            $i = 0;
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && !strpos($file, ".")) {
                    $dirArray[$i] = $file;
                    $i++;
                }
            }
            closedir($handle);
        }
        return $dirArray;
    }


    public static function is_validate($str)
    {
        $pattern = "/[^A-Za-z0-9\-_\.]/";
        return !preg_match($pattern, $str);
    }

    public static function is_relay_rule_avaliable($rule, $ruleset, $node_id)
    {
        $cur_id = $rule->id;

        foreach ($ruleset as $single_rule) {
            if (($rule->port == $single_rule->port || $single_rule->port == 0) && ($node_id == $single_rule->source_node_id || $single_rule->source_node_id == 0) && (($rule->id != $single_rule->id && $rule->priority < $single_rule->priority) || ($rule->id < $single_rule->id && $rule->priority == $single_rule->priority))) {
                $cur_id = $single_rule->id;
            }
        }

        if ($cur_id != $rule->id) {
            return false;
        }

        return true;
    }

    public static function pick_out_relay_rule($relay_node_id, $port, $ruleset)
    {

        /*
        for id in self.relay_rule_list:
            if ((self.relay_rule_list[id]['user_id'] == user_id or self.relay_rule_list[id]['user_id'] == 0) or row['is_multi_user'] != 0) and (self.relay_rule_list[id]['port'] == 0 or self.relay_rule_list[id]['port'] == port):
                has_higher_priority = False
                for priority_id in self.relay_rule_list:
                    if ((self.relay_rule_list[priority_id]['priority'] > self.relay_rule_list[id]['priority'] and self.relay_rule_list[id]['id'] != self.relay_rule_list[priority_id]['id']) or (self.relay_rule_list[priority_id]['priority'] == self.relay_rule_list[id]['priority'] and self.relay_rule_list[id]['id'] > self.relay_rule_list[priority_id]['id'])) and (self.relay_rule_list[id]['user_id'] == self.relay_rule_list[priority_id]['user_id'] or self.relay_rule_list[priority_id]['user_id'] == 0) and (self.relay_rule_list[id]['port'] == self.relay_rule_list[priority_id]['port'] or self.relay_rule_list[priority_id]['port'] == 0):
                        has_higher_priority = True
                        continue

                if has_higher_priority:
                    continue

            temp_relay_rules[id] = self.relay_rule_list[id]
        */

        $match_rule = null;

        foreach ($ruleset as $single_rule) {
            if (($single_rule->port == $port || $single_rule->port == 0) && ($single_rule->source_node_id == 0 || $single_rule->source_node_id == $relay_node_id)) {
                $has_higher_priority = false;
                foreach ($ruleset as $priority_rule) {
                    if (($priority_rule->port == $port || $priority_rule->port == 0) && ($priority_rule->source_node_id == 0 || $priority_rule->source_node_id == $relay_node_id)) {
                        if (($priority_rule->priority > $single_rule->priority && $priority_rule->id != $single_rule->id) || ($priority_rule->priority == $single_rule->priority && $priority_rule->id < $single_rule->id)) {
                            $has_higher_priority = true;
                            continue;
                        }
                    }
                }

                if ($has_higher_priority) {
                    continue;
                }

                $match_rule = $single_rule;
            }
        }

        if ($match_rule != null) {
            if ($match_rule->dist_node_id == -1) {
                return null;
            }
        }

        return $match_rule;
    }

    public static function get_middle_text($origin_text, $begin_text, $end_text)
    {
        $begin_pos = strpos($origin_text, $begin_text);
        if ($begin_pos == false) {
            return null;
        }

        $end_pos = strpos($origin_text, $end_text, $begin_pos + strlen($begin_text));
        if ($end_pos == false) {
            return null;
        }

        return substr($origin_text, $begin_pos + strlen($begin_text), $end_pos - $begin_pos - strlen($begin_text));
    }

    public static function is_param_validate($type, $str)
    {
        $list = Config::getSupportParam($type);
        if (in_array($str, $list)) {
            return true;
        }
        return false;
    }

    public static function is_protocol_relay($user)
    {
        return true;

        $relay_able_list = Config::getSupportParam('relay_able_protocol');

        if (in_array($user->protocol, $relay_able_list) || Config::get('relay_insecure_mode') == 'true') {
            return true;
        }

        return false;
    }

    public static function has_conflict_rule($input_rule, $ruleset, $edit_rule_id = 0, $origin_node_id = 0, $user_id = 0)
    {
        foreach ($ruleset as $rule) {
            if (($rule->source_node_id == $input_rule->dist_node_id) && (($rule->port == $input_rule->port || $input_rule->port == 0) || $rule->port == 0)) {
                if ($rule->dist_node_id == $origin_node_id && $rule->id != $edit_rule_id) {
                    return $rule->id;
                }

                //递归处理这个节点
                $maybe_rule_id = Tools::has_conflict_rule($rule, $ruleset, $edit_rule_id, $origin_node_id, $rule->user_id);
                if ($maybe_rule_id != 0) {
                    return $maybe_rule_id;
                }
            }
        }

        if (($input_rule->id == $edit_rule_id || $edit_rule_id == 0) && $input_rule->dist_node_id != -1) {
            $dist_node = Node::find($input_rule->dist_node_id);
            if ($input_rule->source_node_id == 0 && $dist_node->sort == 10) {
                return -1;
            }

            if ($input_rule->dist_node_id == $input_rule->source_node_id) {
                return -1;
            }
        }

        return 0;
    }

    public static function insertPathRule($single_rule, $pathset, $port)
    {
        /* path
          path pathtext
          begin_node_id id
          end_node id
          port port
        */

        if ($single_rule->dist_node_id == -1) {
            return $pathset;
        }

        foreach ($pathset as &$path) {
            if ($path->port == $port) {
                if ($single_rule->dist_node_id == $path->begin_node->id) {
                    $path->begin_node = $single_rule->Source_Node();
                    if ($path->begin_node->isNodeAccessable() == false) {
                        $path->path = '<font color="#FF0000">' . $single_rule->Source_Node()->name . '</font>' . " → " . $path->path;
                        $path->status = "阻断";
                    } else {
                        $path->path = $single_rule->Source_Node()->name . " → " . $path->path;
                        $path->status = "通畅";
                    }
                    return $pathset;
                }

                if ($path->end_node->id == $single_rule->source_node_id) {
                    $path->end_node = $single_rule->Dist_Node();
                    if ($path->end_node->isNodeAccessable() == false) {
                        $path->path = $path->path . " → " . '<font color="#FF0000">' . $single_rule->Dist_Node()->name . '</font>';
                        $path->status = "阻断";
                    } else {
                        $path->path = $path->path . " → " . $single_rule->Dist_Node()->name;
                    }
                    return $pathset;
                }
            }
        }

        $new_path = new \stdClass();
        $new_path->begin_node = $single_rule->Source_Node();
        if ($new_path->begin_node->isNodeAccessable() == false) {
            $new_path->path = '<font color="#FF0000">' . $single_rule->Source_Node()->name . '</font>';
            $new_path->status = "阻断";
        } else {
            $new_path->path = $single_rule->Source_Node()->name;
            $new_path->status = "通畅";
        }

        $new_path->end_node = $single_rule->Dist_Node();
        if ($new_path->end_node->isNodeAccessable() == false) {
            $new_path->path .= " -> " . '<font color="#FF0000">' . $single_rule->Dist_Node()->name . '</font>';
            $new_path->status = "阻断";
        } else {
            $new_path->path .= " -> " . $single_rule->Dist_Node()->name;
        }

        $new_path->port = $port;
        $pathset->append($new_path);

        return $pathset;
    }

    public static function keyFilter($object, $filter_array)
    {
        foreach ($object['attributes'] as $key => $value) {
            if (!in_array($key, $filter_array)) {
                unset($object->$key);
            }
        }
        return $object;
    }

    public static function getRelayNodeIp($source_node, $dist_node)
    {
        $dist_ip_str = $dist_node->node_ip;
        $dist_ip_array = explode(',', $dist_ip_str);
        $return_ip = NULL;
        foreach ($dist_ip_array as $single_dist_ip_str) {
            $child1_array = explode('#', $single_dist_ip_str);
            if ($child1_array[0] == $single_dist_ip_str) {
                $return_ip = $child1_array[0];
            } else {
                if (isset($child1_array[1])) {
                    $node_id_array = explode('|', $child1_array[1]);
                    if (in_array($source_node->id, $node_id_array)) {
                        $return_ip = $child1_array[0];
                    }
                }
            }
        }

        return $return_ip;
    }

    public static function updateRelayRuleIp($dist_node)
    {
        $rules = Relay::where('dist_node_id', $dist_node->id)->get();

        foreach ($rules as $rule) {
            $source_node = Node::where('id', $rule->source_node_id)->first();

            $rule->dist_ip = Tools::getRelayNodeIp($source_node, $dist_node);
            $rule->save();
        }
    }

    public static function checkNoneProtocol($user)
    {
        if ($user->method == 'none' && !in_array($user->protocol, Config::getSupportParam('allow_none_protocol'))) {
            return false;
        }

        return true;
    }

    public static function getRealIp($rawIp)
    {
        return str_replace("::ffff:", "", $rawIp);
    }

    public static function isInt($str)
    {
        if ($str[0] == '-') {
            $str = substr($str, 1);
        }

        return ctype_digit($str);
    }

    public static function getIp()
    {
//        //判断服务器是否允许$_SERVER
//        if (isset($_SERVER)) {
//            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
//                $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
//            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
//                $realip = $_SERVER['HTTP_CLIENT_IP'];
//            } else {
//                $realip = $_SERVER['REMOTE_ADDR'];
//            }
//        } else {
//            //不允许就使用getenv获取
//            if (getenv("HTTP_X_FORWARDED_FOR")) {
//                $realip = getenv("HTTP_X_FORWARDED_FOR");
//            } elseif (getenv("HTTP_CLIENT_IP")) {
//                $realip = getenv("HTTP_CLIENT_IP");
//            } else {
//                $realip = getenv("REMOTE_ADDR");
//            }
//        }
//        return $realip;
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * 调用新浪接口将长链接转为短链接
     * @param  string $source 申请应用的AppKey
     * @param  array|string $url_long 长链接，支持多个转换（需要先执行urlencode)
     * @return array
     */
    public static function getSinaShortUrl($source, $url_long)
    {
        $source = '2235665753';
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
        if (isset($result[0]['url_short'])) {
            return $result[0]['url_short'];
        } else {
            return '';
        }
    }

    /*
     * 根据ip获取运营商
     */
    public static function getIpServiceName($origin_ip)
    {
        if (empty($origin_ip)) {
            return '';
        }
        $ip = '';
        //排除ipv6
        $ip_arr = explode(",", $origin_ip);
        if (is_array($ip_arr)) {
            foreach ($ip_arr as $item) {
                if (stristr($item, ":")) {
                    continue;
                } else {
                    $ip = $item;
                    break;
                }
            }
        }
        if (empty($ip)) {
            return '';
        }
        $isp = self::qqwryIpService($ip);
        if (empty($isp) || $isp == "IANA保留地址") {
            $isp = self::baiduIpService($ip);
            if (empty($isp)) {
                $isp = self::taobaoIpService($ip);
            }
        }
        return $isp;
    }

    /**
     * baidu ip定位
     * @param $ip
     * @return string
     */
    static function baiduIpService($ip)
    {
        //API控制台申请得到的ak（此处ak值仅供验证参考使用）
        $ak = 'tY1okcqF3XXEaIkQGt9qt5XtKLG2DWdS';

        //应用类型为for server, 请求校验方式为sn校验方式时，系统会自动生成sk，可以在应用配置-设置中选择Security Key显示进行查看（此处sk值仅供验证参考使用）
        $sk = 'o5MlzmbA8pdUnh8u0pkjw4v1UeMZvfP6';

        //以Geocoding服务为例，地理编码的请求url，参数待填
        $url = "http://api.map.baidu.com/location/ip?ip=%s&ak=%s&coor=bd09ll";

        //请求参数中有中文、特殊字符等需要进行urlencode，确保请求串与sn对应
        $target = sprintf($url, $ip, $ak);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $target);
        $data = curl_exec($ch);
//        var_dump($data);
//        die;
        if ($error = curl_errno($ch)) {
            return '';
        }
        curl_close($ch);
        $result = @json_decode($data, true);
        if (isset($result['status']) && intval($result['status']) === 0) {
            $iparr = explode("|", $result['address']);
            switch ($iparr[4]) {
                case "CHINANET":
                    $dx = "中国电信";
                    break;
                case "UNICOM":
                    $dx = "中国联通";
                    break;
                case "CMNET":
                    $dx = "中国移动";
                    break;
                case "CRTC":
                    $dx = "中国铁通";
                    break;
                case "COLNET":
                    $dx = "有线通";
                    break;
                case "CERNET":
                    $dx = "教育网";
                    break;
                case "CNCGROUP":
                    $dx = "网通";
                    break;
                default:
                    $dx = "";
                    break;
            }
            return $dx;
        } else {
            return '';
        }
    }

    /**
     * 淘宝获取运营商
     * @param $ip
     * @return string
     */
    static function taobaoIpService($ip)
    {
        //根据ip地址获取运营商
        $resData = @file_get_contents("http://ip.taobao.com/service/getIpInfo.php?ip={$ip}");
        if ($resData) {
            $resData = json_decode($resData, true);
            $serviceProvider = $resData['data']['isp'];
        } else {
            $serviceProvider = "";
        }
        return $serviceProvider;
    }

    /**
     * 纯真ip数据库
     * @param $ip
     * @return bool|string
     */
    static function qqwryIpService($ip)
    {
        $iplocation = new QQWry();
        $location = $iplocation->getlocation(Tools::getRealIp($ip));
        return iconv('gbk', 'utf-8//IGNORE', $location['country'] . $location['area']);
    }

    /**
     * 找isp关键字，用关键字去节点描述里去匹配
     * @param $isp_name
     * @return mixed|string
     */
    static function isp_keyword($isp_name)
    {
        $keywords = [
            '电信',
            '联通',
            '移动',
            '铁通',
            '有线通',
            '教育网',
            '网通'
        ];
        //三网 === 电信，移动，联通，网通
        //以外的都丢默认节点
        $bingo = "";
        foreach ($keywords as $keyword) {
            if (stristr($isp_name, $keyword)) {
                $bingo = $keyword;
                break;
            }
        }
        return $bingo;
    }

}
