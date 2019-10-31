<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRobotRequest;
use App\Http\Requests\UpdateRobotRequest;
use App\Models\Robot;
use App\Repositories\RobotRepository;
use App\Repositories\FriendRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\Friend;
use App\Models\FriendReply;
use DB;
use App\Models\FriendCircle;
class PackageController extends AppBaseController
{
    /** @var  robotRepository */
    private $robotRepository;
    private $friendRepository;

    public function __construct(RobotRepository $robotRepo, FriendRepository $friendRepo)
    {
        $this->robotRepository = $robotRepo;
        $this->friendRepository = $friendRepo;
        $request = Request();
        $this->getCurrentAction($request);
    }

    /**
     * Display a listing of the Game.
     *
     * @param Request $request
     * @return Response
     */
    public function saveCircle(Request $request)
    {
        $data = $request->all();
        if(!isset($data['robot_id'])){
            Flash::error("微信号不能为空！");
            return redirect(url('/circle/circle'));
        }
        $type = $data['type']>0?$data['type']:1;
        //当前登录用户信息
        if (isset($data['type'])) {
            foreach ($data['robot_id'] as $v) {
                $where['type'] = 1;
                $where['robot_id'] = $v;
                $where['user_id'] =  $this->authUserInfo()->id;
                $d['status'] = $data['status'];
                $d['type'] = 1;
                $d['user_id'] =  $this->authUserInfo()->id;
                $d['robot_id'] = $v;
                //获取朋友圈用户
                $circle_url = env('CIRCLE_URL');
                $res = @file_get_contents("$circle_url?area={$data['province']}&type={$data['type']}&sex=0");//todo

                $user = [];
                if ($res) {
                    $ds = json_decode($res);
                    $user = $ds->user_id;
                }
                $config = [ 'start' => $data['start'], 'end' => $data['end'], 'type' => $data['type'], 'sex' => $data['sex'],
                    'interval' => $data['interval'], 'user' => $user,'site'=>$data['city'],'province'=>$data['province']];
                $d['config'] = json_encode($config,JSON_UNESCAPED_UNICODE);
                FriendCircle::updateOrInsert($where, $d);
            }
        }

        Flash::success('更新成功');
        return redirect(url('/circle/circle'));
    }

    /**
     * Display a listing of the Game.
     *
     * @param Request $request
     * @return Response
     */
    public function circle(Request $request){
        $res = isset($_SESSION['robot_ids_6'])?$_SESSION['robot_ids_6']:[] ;
        //通过微信获取微信群
        $tg  = [];
        if($res){
            foreach($res as &$v){
                $tg[$v] = $v;
            }
        }
        $robot = Robot::where($this->condition())->pluck('nickname','id');
        $id = 0;
        if($res){
            $id = current($res);
        }
        $wxData = FriendCircle::where('robot_id',$id)->where($this->condition())->first();
        //获取朋友圈用户
        $ds = [];
        $circle_url = env('CIRCLE_URL');
        $res = @file_get_contents("$circle_url?area=北京&type=1&sex=0");//todo
        $ds = json_decode($res);
        if($ds){
            foreach($ds->data as &$vs){
                $new = preg_replace("/<p.*?>|<\/p>/is","", $vs->content);
                $vs->content = $new;
            }
        }else{
            $ds = new \stdClass();
            $ds->user_num = 0;
            $ds->weibo_num = 0;
            $ds->data = [];
        }


        if($wxData){
//            $circle_url = env('CIRCLE_URL');
//            $res = @file_get_contents('http://192.168.115.128:8802/api/weibousers/count?area=北京&type=' . $wxData->type);//todo
//            $ds = json_decode($res);
//            foreach($ds->data as &$vs){
/*                $new = preg_replace("/<p.*?>|<\/p>/is","", $vs->content);*/
//                $vs->content = $new;
//            }
            $wxData_arr = json_decode($wxData->config,true);
            $wxData->interval = $wxData_arr['interval'];
            $wxData->start = $wxData_arr['start'];
            $wxData->end = $wxData_arr['end'];
            $wxData->sex = $wxData_arr['sex'];
            $wxData->type = $wxData_arr['type'];
            $wxData->province = isset($wxData_arr['province'])?$wxData_arr['province']:'';
            $wxData->city = isset($wxData_arr['site'])?$wxData_arr['site']:1;
        }else{

            $wxData = new \stdClass();
            $wxData->start = 0;
            $wxData->end = 0;
            $wxData->msg = '';
            $wxData->sex = 0;
            $wxData->status = 0;
            $wxData->type = 0;
            $wxData->interval = 0;
            $wxData->content = '';
            $wxData->province = '';
            $wxData->city = [];
//            $ds = new \stdClass();
//            $ds->user_num = 0;
//            $ds->weibo_num = 0;
//            $ds->data[0] = new \stdClass();
//            $ds->data[0]->user_num = 0;
//            $ds->data[0]->weibo_num = 0;
//            $ds->data[0]->content = '';
        }

        $tmp = [];
        if(isset($wxData->province)&&$wxData->province!=''){
           //查询原省下面市的数据
            $origin_province = DB::table('area')->where('name',$wxData->province)->orderBy('id','desc')->first();
            $originCity =  DB::table('area')->where('parent_id',$origin_province->id)->get()->pluck('name','id')->toArray();
            $originCity = array_values($originCity);
            $tmp = [];
            $array_intersect = array_intersect($originCity,$wxData->city);
            foreach ($originCity as $k=> &$vs){
                $stmp = [
                    'name'=>$vs,
                    'status'=>0,
                ];
                if(isset($array_intersect[$k])&&$vs==$array_intersect[$k]){
                    $stmp = [
                        'name'=>$vs,
                        'status'=>1,
                    ];
                }
                $tmp[] = $stmp;
            }
        }

        //获取省
        $province = DB::table('area')->where(['parent_id'=>1])->get();
        $returnData = ['robot'=>$robot,'request'=>$request,'request_url'=>url()->current(),'select'=>$tg,'wxdata'=>$wxData,'ds'=>$ds,'province'=>$province
            ,'originCity'=>$tmp
        ];
        return view('circle.circle')->with($returnData);
    }

    /**
     * Display a listing of the Game.
     *
     * @param Request $request
     * @return Response
     */
    public function getCity(Request $request){
        $province_id = $request->province_id>0?$request->province_id:-99;
        $special_province = ['110000','120000','310000','500000'];
        if(in_array($province_id,$special_province)){
            $next = DB::table('area')->where('parent_id',$province_id)->first();
            $city = DB::table('area')->where('parent_id',$next->id)->get()->toArray();
        }else{
            $city = DB::table('area')->where('parent_id',$province_id)->get()->toArray();
        }


        $return =  [
            'status'=>1,
            'city'=>$city
        ];
        echo  json_encode($return);
    }



}
