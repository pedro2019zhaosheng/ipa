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
use App\Models\Order;
use App\Utils\Tools;
class OrderController extends Controller
{
    private $prompt,$chargeConfig;
    public function __construct()
    {
        $this->chargeConfig =[
                'download_config'=> [
                    'buy'=>[[
                        'id'=>1,
                        'num'=>100,
                        'desc'=>'',
                        'give_num'=>0,
                        'price'=>10
                    ],
                    [
                        'id'=>2,
                        'num'=>10000,
                        'give_num'=>1000,
                        'desc'=>'赠 1000 次',
                        'price'=>100
                    ],
                    [
                        'id'=>3,
                        'num'=>100000,
                        'give_num'=>10000,
                        'desc'=>'赠 10000 次',
                        'price'=>900
                    ],
                    [
                        'id'=>4,
                        'num'=>200000,
                        'give_num'=>20000,
                        'desc'=>'赠 20000 次',
                        'price'=>1500
                    ]],
                    'payment_type'=>[
                        [
                            'id'=>1,
                            'name'=>'支付宝',
                        ],
                        [
                            'id'=>2,
                            'name'=>'银联',
                        ]
                    ]
                ],
                'sign_config'=>[
                'product'=>'ios专属签名',
                'sign_type'=>1,
                'sign_type_desc'=>'公有池',
                'buy'=>[
                    [
                        'id'=>5,
                        'num'=>1,
                        'desc'=>'1次',
                        'price'=>1*15
                    ],
                    [
                        'id'=>6,
                        'num'=>10,
                        'desc'=>'10次',
                        'price'=>10*15
                    ],
                    [
                        'id'=>7,
                        'num'=>100,
                        'desc'=>'100次',
                        'price'=>100*15
                    ],
                    [
                        'id'=>8,
                        'num'=>1000,
                        'desc'=>'1000次',
                        'price'=>1000*15
                    ]
                ],
                'payment_type'=>[
                    [
                        'id'=>1,
                        'name'=>'支付宝',
                    ],
                    [
                        'id'=>2,
                        'name'=>'银联',
                    ]

                ],
//                'payment_type_desc'=>'支付宝支付',
            ]
        ];
    }

    /**
    *@param token
    */
   public function buyConfig(Request $request){

        if(!isset($request->token)||!$request->token){
           fail('缺少参数',new \stdClass());
        }
       if(!isset($request->type)||!$request->type){
           fail('缺少参数',new \stdClass());
       }
       if($request->type==1){
           $data = $this->chargeConfig['download_config'];
       }else{
           $data = $this->chargeConfig['sign_config'];
       }
       success($data);
   }

    /*
     * @desc 生成订单
     * @param amount,payment_type,user_id,product_id
     */
    public function makeOrder(Request $request){
        if(!isset($request->token)||!$request->token){
            fail('缺少参数',new \stdClass());
        }
        if(!isset($request->type)||!$request->type){
            fail('缺少参数',new \stdClass());
        }
        if(!isset($request->product_id)||!$request->product_id){
            fail('缺少参数',new \stdClass());
        }
        $token = $request->token; //当前登录者user_id;
        $user = DB::table('users')->where('token', '=', $token)->first();
        $payment_type = $request->payment_type;
        if(!in_array($payment_type,[1,2])){
            fail('无效的支付方式',new \stdClass());
        }
        $amount =$request->amount;
        $product_id = $request->product_id;
        $type = $request->type;
        if(in_array($type,[1,2])){
            if(!in_array($product_id,[1,2,3,4])){
                fail('套餐不存在！');
            }
            $config = $type==1?$this->chargeConfig['download_config']:$this->chargeConfig['sign_config'];
            if($type==1){
                $product = $config['buy'][$product_id-1];
                $product_json = json_encode($product);
                $product_num = $product['num']+$product['give_num'];
                $amount = $product['price'];
            }
            if($type==2){
                $product = $config['buy'][$product_id-1];
                $product_json = json_encode($product);
                $product_num = $product['num'];
                $amount = $product['price'];
            }

        }else{
            fail('生成订单异常');
        }


        if (empty($user)) {
           fail('用户不存在！');
        }
        if(!$user->mobile){
            fail('手机号未绑定，不能下单！');
        }
        $userId  = $user->id;
        $folder = 'order.lock';
        is_dir($folder) OR mkdir($folder, 0777, true);
        $fp = fopen("order.lock", "r");
        if(flock($fp,LOCK_EX)){
            //生成订单
            $order_id = 'cjq'.time().rand(000000,999999);
            $orderModel = new Order();
            $orderModel->order_id = $order_id;
            $orderModel->product_id = $product_id;
            $orderModel->user_id = $userId;
            $orderModel->order_type = $type;
            $orderModel->amount = $amount;
            $orderModel->payment_type = $payment_type;
            $orderModel->product_json = $product_json;
            $orderModel->product_num = $product_num;
            $orderModel->status = 0;
            $orderModel->mobile = $user->mobile;
            $orderModel->nick_name = $user->nick_name;
            $orderModel->ctime = date('Y-m-d H:i:s');

            $total_amount = 1/100;
            $ip = Tools::getIp();

            if($payment_type==1){
                $config = ['merchantId'=>123463, // ÓÃ»§id
                    'privateKey'=>'b280c2a39971229c6badb3c036828f39',  // ÓÃ»§ÃØÔ¿
                    'url'=>'http://47.75.164.152:18002/wxqr/', //ÏÂµ¥µØÖ·
                ];
                $data['merchantId'] = $config['merchantId'];
                $data['notifyUrl'] = $_SERVER['HTTP_HOST'].'/order/vacallback';  //»Øµ÷µØÖ·
                $data['outTradeNo'] = $order_id;  // ÓÃ»§¶©µ¥ºÅ
                $data['price'] = $amount;       // ½ð¶î£¬µ¥Î»Ôª
                $data['subject'] = '365超级签充值';    // ±êÌâ
                $data['info'] = '365超级签充值';   // ÏêÏ¸ÐÅÏ¢
                $data['userid'] = $userId;
                $data['ip'] = $ip;
                $data['type'] = 1;
                $data['time'] = time();
                $data['sig'] = md5($config['privateKey'].$data['price'].$data['time'].$config['merchantId'].$data['outTradeNo']);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $config['url']);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'X-AjaxPro-Method:ShowList',
                    'User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36'
                ));
                $arr['data'] = base64_encode(json_encode($data));
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
                curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //²»ÑéÖ¤Ö¤Êé
//curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //²»ÑéÖ¤Ö¤Êé

                $output = curl_exec($ch);
                $error = curl_error($ch);
                if ($error) {
                    var_dump(error);
                }
                curl_close($ch);
                if($output){
                    $output =json_decode($output);
                    if($output->status<0){
                        $url = '';
                    }else{
                        $url = $output->url;
                    }
                }
            }

            if($payment_type==2){
                $config = ['merchantId'=>123463, // ÓÃ»§id
                    'privateKey'=>'b280c2a39971229c6badb3c036828f39',  // ÓÃ»§ÃØÔ¿
                    'url'=>'http://47.75.164.152:18002/bankpay/', //ÏÂµ¥µØÖ·
                ];
                $data['merchantId'] = $config['merchantId'];
                $data['notifyUrl'] = $_SERVER['HTTP_HOST'].'/order/vacallback';  //回调地址
                $data['userid'] = $userId;//用户id
                $data['outTradeNo'] = time();  // 用户订单号
                $data['price'] = $amount;       // 金额，单位元
                $data['subject'] = '365超级签充值';    // 标题
                $data['info'] = '365超级签充值';   // 详细信息
                $data['time'] = time();
                $data['sig'] = md5($config['privateKey'].$data['price'].$data['time'].$config['merchantId'].$data['outTradeNo']);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $config['url']);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'X-AjaxPro-Method:ShowList',
                    'User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.154 Safari/537.36'
                ));
                $arr['data'] = base64_encode(json_encode($data));
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
                curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不验证证书
                $output = curl_exec($ch);

                $error = curl_error($ch);
                if ($error) {
                    var_dump(error);
                }
                curl_close($ch);
                if($output){
                    $output =json_decode($output);
                    if($output->status<0){
                        $url = '';
                    }else{
                        $url = $output->url;
                    }
                }

            }



            try{
                DB::beginTransaction();
                $orderModel->save();
                DB::commit();
            }catch(\Exception $e){
                DB::rollBack();
            }

            if($orderModel->id>0){
                $orderModel->url = $url;
                success($orderModel);
            }else{
                fail('订单生成失败！');
            }
            flock($fp,LOCK_UN);
        }
        fclose($fp);

    }

    public function getOrderList(Request $request){
        if(!isset($request->token)||!$request->token){
            fail('缺少参数',new \stdClass());
        }
        $token = $request->token;
        $user = DB::table('users')->where('token', '=', $token)->first();
        $userId = $user->id; //当前登录者user_id;
        if (empty($user)) {
            fail('没找到用户');
        }
        $limit = $request->limit?$request->limit:10;
        $page = $request->page?$request->page:1;
        $orderModel = new Order();
        $orderList = $orderModel->where('user_id',$userId)->where('is_del',0)->orderBy('id', 'desc')->paginate($limit, ['*'], 'page', $page)->toArray();
        if($orderList['data']){
            foreach($orderList['data'] as &$v){
//                $charge = $this->chargeConfig[$v['product_id']];
//                $product_desc = $day.'天VIP服务';
//                $v['product_desc'] = $product_desc;
            }
        }
        success($orderList);
    }
    /**
     * @desc 支付宝回调地址
     * @param order_id
     */
    public function vacallback(Request $request){
        $config = ['merchantId'=>123463, // ÓÃ»§id
            'privateKey'=>'b280c2a39971229c6badb3c036828f39',  // ÓÃ»§ÃØÔ¿
            'url'=>'http://www.xxxx.com/index', //ÏÂµ¥µØÖ·
        ];
        $data['status'] = $_POST['status'];
        $data['time'] = $_POST['time'];
        $data['outTradeNo'] = $_POST['outTradeNo'];  // ÉÌ»§µ¥ºÅ
        $data['amount'] = $_POST['price'];   // ³äÖµ½ð¶î
        $data['sig'] = $_POST['sig'];       // ÑéÖ¤Âë
        $data['tradeNo'] = $_POST['tradeNo'];     // ¹Ù·½µ¥ºÅ
//        file_put_contents('order.txt',json_encode($data));
        //支付成功给vip用户加天数
        $orderModel = new Order();
        if ($data['sig'] == md5($config['privateKey'].$data['amount'].$data['time'].$config['merchantId'].$data['outTradeNo'])) {
            $order = $orderModel::where('order_id',$data['outTradeNo'])->first();
            if(!$order){
                fail('无效订单');
            }
            $user = DB::table('users')->where('id', '=', $order->user_id)->first();
            if (empty($user)) {
                fail('没找到用户');
            }

            $price = $order->price;
            if($price!=$_POST['price']){
                $order->pay_amount = $_POST['price'];
                $order->utime = date('Y-m-d H:i:s');
                $order->ok_time = date('Y-m-d H:i:s');
                $order->pay_time = date('Y-m-d H:i:s');
                $order->status = 2;
                $order->order_no =  $data['tradeNo'];
                $order->save();
                echo 1;die;
            }
            if($order){
                $orderType = $order->order_type;
                if(in_array($orderType,[1,2])){
                    if($orderType==1){
                        $userUpdateData = [
                            'download_package_num'=>$order->product_num
                        ];
                    }
                    if($orderType==2){
                        $userUpdateData = [
                            'sign_num'=>$order->product_num
                        ];
                    }
                    DB::table('users')->where(['id'=>$order->user_id])->update($userUpdateData);
                }
            }

            //实际支付金额
            $order->pay_amount = $_POST['price'];
            $order->utime = date('Y-m-d H:i:s');
            $order->ok_time = date('Y-m-d H:i:s');
            $order->pay_time = date('Y-m-d H:i:s');
            $order->status = 1;
            $order->order_no =  $data['tradeNo'];
            $order->save();
            $status = 1;
        } else {
            $status = 0;
        }
        echo $status;die;
    }

    /**
     * @desc 银联回调地址
     * @param order_id
     */
    public function bacallback(Request $request){
        $config = ['merchantId'=>123463, // ÓÃ»§id
            'privateKey'=>'b280c2a39971229c6badb3c036828f39',  // ÓÃ»§ÃØÔ¿
            'url'=>'http://www.xxxx.com/index', //ÏÂµ¥µØÖ·
        ];
        $data['status'] = $_POST['status'];
        $data['time'] = $_POST['time'];
        $data['outTradeNo'] = $_POST['outTradeNo'];  // 商户单号
        $data['amount'] = $_POST['price'];   // 充值金额
        $data['sig'] = $_POST['sig'];       // 验证码
        $data['tradeNo'] = $_POST['tradeNo'];     // 官方单号
        $data['realprice'] = $_POST['realprice'];//真实支付金额，商家需要根据真实支付金额给用户回调上分
//        file_put_contents('order.txt',json_encode($data));
        //支付成功给vip用户加天数
        $orderModel = new Order();
        if ($data['sig'] == md5($config['privateKey'].$data['amount'].$data['time'].$config['merchantId'].$data['outTradeNo'].'0'.$data['realprice'])) {
            $order = $orderModel::where('order_id',$data['outTradeNo'])->first();
            if(!$order){
                fail('无效订单');
            }
            $user = DB::table('users')->where('id', '=', $order->user_id)->first();
            if (empty($user)) {
                fail('没找到用户');
            }

            $price = $order->price;
            if($price!=$_POST['price']){
                $order->pay_amount = $_POST['price'];
                $order->utime = date('Y-m-d H:i:s');
                $order->ok_time = date('Y-m-d H:i:s');
                $order->pay_time = date('Y-m-d H:i:s');
                $order->status = 2;
                $order->order_no =  $data['tradeNo'];
                $order->save();
                echo 1;die;
            }
            if($order){
                $orderType = $order->order_type;
                if(in_array($orderType,[1,2])){
                    if($orderType==1){
                        $userUpdateData = [
                            'download_package_num'=>$order->product_num
                        ];
                    }
                    if($orderType==2){
                        $userUpdateData = [
                            'sign_num'=>$order->product_num
                        ];
                    }
                    DB::table('users')->where(['id'=>$order->user_id])->update($userUpdateData);
                }
            }

            //实际支付金额
            $order->pay_amount = $_POST['price'];
            $order->utime = date('Y-m-d H:i:s');
            $order->ok_time = date('Y-m-d H:i:s');
            $order->pay_time = date('Y-m-d H:i:s');
            $order->status = 1;
            $order->order_no =  $data['tradeNo'];
            $order->save();
            $status = 1;
        } else {
            $status = 0;
        }
        echo $status;die;
    }
    /**
     * @desc 处理订单
     * @param order_id
     */
    public function getOrderPayStatus($request,$response,$args){
        $userId = $request->getParam('userId', 0); //当前登录者user_id;
        $orderId = $request->getParam('orderId', 0);
        $orderModel = new Order();
        $order = $orderModel::where('order_id',$orderId)->where('mobile_user_id',$userId)->first();
        if(!$order){
            $res['ret'] = 0;
            $res['msg'] = "无效订单";
            $res['data'] = new \stdClass();
            return $this->echoJson($response, $res);
        }
        $user = MobileUser::where('id', '=', $order->mobile_user_id)->first();
        if (empty($user)) {
            $res['ret'] = 0;
            $res['msg'] = "没找到用户";
            return $this->echoJson($response, $res);
        }
        $h5_user = User::where('id', '=', $user->h5_user_id)->first();
        if (empty($h5_user)) {
            $res['ret'] = 0;
            $res['msg'] = "没找到freevpn用户";
            return $this->echoJson($response, $res);
        }
        $status = 0;
        if($order->status==1){
            $status = 1;
        }
        $res['ret'] = 1;
        $res['msg'] = "操作成功";
        $res['data'] = ['status'=>$status];
        return $this->echoJson($response, $res);
    }
}
