<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRobotRequest;
use App\Http\Requests\UpdateRobotRequest;
use App\Models\GroupMember;
use App\Models\Robot;
use App\Models\GroupKick;
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
use App\Models\RobotGroup;
use App\Models\GroupConfig;
use App\Models\GroupReply;
use App\Models\GroupSend;
use App\Models\GroupSign;
use App\Models\GroupComplain;
use App\Models\GroupSignLog;
class GroupController extends AppBaseController
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
    public function group(Request $request){
        $robot = Robot::where($this->condition())->pluck('nickname','id');
        $request = $request->all();
//        $wx_group = ['微信群1','微信群2','微信群3','微信群4'];//todo;
        $res = isset($_SESSION['group_robot_ids_1'])?$_SESSION['group_robot_ids_1']:[] ;
        //通过微信获取微信群
        $wx_group = RobotGroup::whereIn('robot_id',$res)->pluck('wx_group_id','id')->toArray();
        $i = 0;
        $tg = [];
        $id= 0;
        if($res){
            foreach($res as &$v){
                $tg[$v] = $v;
                $i++;
            }
        }
        if($res){
            $id = current($res);
        }
        $wxData = GroupConfig::where('robot_id',$id)->where('type',1)->where($this->condition())->first();
        if($wxData){
            $wxData_arr = json_decode($wxData->config,true);
            $wxData->start = $wxData_arr['start'];
            $wxData->end = $wxData_arr['end'];
            $wxData->sex = $wxData_arr['sex'];
            $wxData->group = $wxData_arr['group'];
            $wxData->msg = $wxData_arr['msg'];
            $wxData->interval = isset($wxData_arr['interval'])?$wxData_arr['interval']:0;
        }else{
            $wxData = new \stdClass();
            $wxData->start = 0;
            $wxData->end = 0;
            $wxData->sex = 0;
            $wxData->group = [];
            $wxData->msg = '';
            $wxData->interval =0;
        }
        $tmp = [];
        $wx_group = array_values($wx_group);
        $array_intersect = array_intersect($wx_group,$wxData->group);
        foreach ($wx_group as $k=> &$vs){
            $groupInfo = RobotGroup::where('wx_group_id',$vs)->first();
            $stmp = [
                'name'=>$vs,
                'sname'=>isset($groupInfo->name)?$groupInfo->name:'暂无',
                'status'=>0,
            ];
            if(isset($array_intersect[$k])&&$vs==$array_intersect[$k]){
                $stmp = [
                    'name'=>$vs,
                    'sname'=>isset($groupInfo->name)?$groupInfo->name:'暂无',
                    'status'=>1,
                ];
            }
            $tmp[] = $stmp;
        }
        $returnData = ['robot'=>$robot,'group'=>$tmp,'request'=>$request,'request_url'=>url()->current(),'select'=>$tg,'wxdata'=>$wxData];
        return view('group.group')->with('robots', [])->with($returnData);
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

    /**
     * Display a listing of the Game.
     *
     * @param Request $request
     * @return Response
     */
    public function saveRobotId(Request $request){
        $robot = $this->robotRepository->all()->pluck('nickname','id');

        $request = $request->all();
        if($request['save_type']==1){
            $key = 'group_robot_ids_1';//自动加载成员成为好友
        }
        if($request['save_type']==2){
            $key = 'group_robot_ids_2';
        }
        if($request['save_type']==3){
            $key = 'group_robot_ids_3';//自动应答
        }
        if($request['save_type']==4){
            $key = 'group_robot_ids_4';//私聊自动回复
        }
        if($request['save_type']==5){
            $key = 'group_robot_ids_5';//加附近好友
        }
        if($request['save_type']==6){
            $key = 'group_robot_ids_6';//批量搜索加好友
        }
        if($request['save_type']==7){
            $key = 'group_robot_ids_7';//批量搜索加好友
        }
        if($request['save_type']==8){
            $_SESSION['group_ids_1'] = $_SESSION['group_ids_2'] = $_SESSION['group_ids_3'] = [];
            $key = 'group_robot_ids_8';//批量搜索加好友
        }
        if($request['save_type']==9){
            $_SESSION['group_ids_1'] = $_SESSION['group_ids_2'] = $_SESSION['group_ids_3'] = [];
            $key = 'group_robot_ids_9';//批量搜索加好友
        }
        if($request['save_type']==10){
            $_SESSION['group_ids_1'] = $_SESSION['group_ids_2'] = $_SESSION['group_ids_3'] = [];
            $key = 'group_robot_ids_10';//批量搜索加好友
        }
        //通过微信获取微信群
        $_SESSION["{$key}"]  = [];
        if(isset($request['robot_id'])&&$request['robot_id']>0){
//            $tmp = !empty($_SESSION["{$key}"])?$_SESSION["$key"]:[];


//            $arr = array_merge( $tmp,$request['robot_id']);
//            foreach($arr as $k=> &$v){
//                if(!in_array($v,$request['robot_id'])){
//                    $v = $request['robot_id'][0];
//                }
//            }
            $_SESSION["{$key}"] = [($request['robot_id'])];
        }else{
            $_SESSION["{$key}"] = [];
        }

        echo  json_encode(['status'=>1,'robot_id'=>$request['robot_id']]);die;
    }

    /**
     * Display a listing of the Game.
     *
     * @param Request $request
     * @return Response
     */
    public function addauto(Request $request)
    {
        $data = $request->all();
        if(!isset($data['robot_id'])){
            Flash::error("微信号不能为空！");
            return redirect($data['request_url']);
        }
        if(!isset($data['status'])){
            Flash::error("请选择状态！");
            return redirect($data['request_url']);
        }

        $data = $request->all();
        $type = $data['robot_type']>0?$data['robot_type']:1;
        if (isset($data['robot_id'])) {
            foreach ($data['robot_id'] as $v) {
                $where['robot_id'] = $d['robot_id'] = $v;
                $where['type'] =  $type;
                $d['status'] = $data['status'];
                $d['type'] = $type;
                $d['user_id'] = $this->authUserInfo()->id;
                if($type==1){
                    if(!isset($data['group'])){
                        $data['group'] = [];
                    }
                    $config = ['msg'=>$data['msg'],'sex'=>$data['sex'], 'start'=>$data['start'], 'end'=>$data['end'],'interval'=>$data['interval'],'group'=>$data['group'],];
                }
                if($type==2){
                    $has_img = 0;
                    if ($request->isMethod('POST')&&in_array($data['control'],[2,3])) {
                        $file = $request->file('img');
                        //判断文件是否上传成功
                        if ($file) {

                            //原文件名
                            $originalName = $file->getClientOriginalName();
                            //扩展名
                            $ext = $file->getClientOriginalExtension();
                            //MimeType
                            $file_type = $file->getClientMimeType();
                            //临时绝对路径
                            $realPath = $file->getRealPath();
                            $filename = uniqid().'.'.$ext;
                            $fileDir = public_path().'/storage';
                            if (!is_dir($fileDir)) {
                                mkdir($fileDir, 0777, true);
                            }
                            $bool = $request->file('img')->move(public_path().'/storage/', $filename);
                            //$bool = Storage::disk('public')->put($filename,file_get_contents($realPath));
                            //判断是否上传成功
                            $filename = 'http://'.$_SERVER['HTTP_HOST'].'/storage/'.$filename;
                            $msg = $filename;
                            $has_img = 1;
                        }
                    }
                    if($has_img==0){
                        $msg = $data['msg'];
                    }

                    $config = ['msg'=>$msg,'sex'=>$data['sex'], 'start'=>$data['start'], 'end'=>$data['end'],'interval'=>$data['interval'],'group'=>$data['group'],'type'=>$data['control']];
                }
                if($type==3){
                    if(!isset($data['group'])){
                        $data['group'] = [];
                    }
                    $config = ['start'=>$data['start'], 'end'=>$data['end'],'group'=>$data['group'],'interval'=>$data['interval']];
                }
                if($type==4){
                    $has_img = 0;
                    if ($request->isMethod('POST')&&in_array($data['control'],[2,3])) {
                        $file = $request->file('img');
                        //判断文件是否上传成功
                        if ($file) {

                            //原文件名
                            $originalName = $file->getClientOriginalName();
                            //扩展名
                            $ext = $file->getClientOriginalExtension();
                            //MimeType
                            $file_type = $file->getClientMimeType();
                            //临时绝对路径
                            $realPath = $file->getRealPath();
                            $filename = uniqid().'.'.$ext;
                            $fileDir = public_path().'/storage';
                            if (!is_dir($fileDir)) {
                                mkdir($fileDir, 0777, true);
                            }
                            $bool = $request->file('img')->move(public_path().'/storage/', $filename);
                            //$bool = Storage::disk('public')->put($filename,file_get_contents($realPath));
                            //判断是否上传成功
                            $filename = 'http://'.$_SERVER['HTTP_HOST'].'/storage/'.$filename;
                            $msg = $filename;
                            $has_img = 1;
                        }
                    }
                    if($has_img==0){
                        $msg = $data['msg'];
                    }

                    $config = ['msg'=>$msg,'sex'=>$data['sex'], 'start'=>$data['start'], 'end'=>$data['end'],'interval'=>$data['interval'],'group'=>$data['group'],'type'=>$data['control']];
                }
                if($type==5){
                    $has_img = 0;
                    if ($request->isMethod('POST')&&in_array($data['control'],[2,3])) {
                        $file = $request->file('img');
                        //判断文件是否上传成功
                        if ($file) {

                            //原文件名
                            $originalName = $file->getClientOriginalName();
                            //扩展名
                            $ext = $file->getClientOriginalExtension();
                            //MimeType
                            $file_type = $file->getClientMimeType();
                            //临时绝对路径
                            $realPath = $file->getRealPath();
                            $filename = uniqid().'.'.$ext;
                            $fileDir = public_path().'/storage';
                            if (!is_dir($fileDir)) {
                                mkdir($fileDir, 0777, true);
                            }
                            $bool = $request->file('img')->move(public_path().'/storage/', $filename);
                            //$bool = Storage::disk('public')->put($filename,file_get_contents($realPath));
                            //判断是否上传成功
                            $filename = 'http://'.$_SERVER['HTTP_HOST'].'/storage/'.$filename;
                            $msg = $filename;
                            $has_img = 1;
                        }
                    }
                    if($has_img==0){
                        $msg = $data['msg'];
                    }

                    $config = ['msg'=>$msg,'sex'=>$data['sex'], 'start'=>$data['start'], 'end'=>$data['end'],'interval'=>$data['interval'],'group'=>$data['group'],'type'=>$data['control']];
                }
                if($type==6){
                    if(!isset($data['group'])){
                        Flash::error("请选择群组！");
                        return redirect($data['request_url']);
                    }

                    $config = ['group'=>$data['group']];
                }
                if($type==7){
                    if(!isset($data['group'])){
                        Flash::error("请选择群组！");
                        return redirect($data['request_url']);
                    }

                    $config = ['group'=>$data['group']];
                }
                $d['config'] = json_encode($config,JSON_UNESCAPED_UNICODE);
                GroupConfig::updateOrInsert($where, $d);

            }
        }

        Flash::success('更新成功');
        switch ($type){
            case 1:
                $url = 'group/group';
                break;
            case 2:
                $url = 'group/comeGroup';
                break;
            case 3:
                $url = 'group/groupKeywordReply';
                break;
            case 4:
                $url = 'group/wallet';
                break;
            case 5:
                $url = 'group/link';
                break;
            case 6:
                $url = 'group/groupKick';
                break;
            case 7:
                $url = 'group/groupComplain';
                break;
            default:
                $url = '';
                break;
        }




        return redirect(url($url));
    }
    /**
     * Display a listing of the Game.
     *
     * @param Request $request
     * @return Response
     */
    public function comeGroup(Request $request){
        $robot = Robot::where($this->condition())->pluck('nickname','id');
        $request = $request->all();
//        $wx_group = ['微信群1','微信群2','微信群3','微信群4'];//todo;
        $res = isset($_SESSION['group_robot_ids_2'])?$_SESSION['group_robot_ids_2']:[] ;
        //通过微信获取微信群
        $wx_group = RobotGroup::whereIn('robot_id',$res)->pluck('wx_group_id','id')->toArray();
        $i = 0;
        $tg = [];
        $id= 0;
        if($res){
            foreach($res as &$v){
                $tg[$v] = $v;
                $i++;
            }
        }
        if($res){
            $id = current($res);
        }
        $wxData = GroupConfig::where('robot_id',$id)->where('type',2)->where($this->condition())->first();
        if($wxData){
            $wxData_arr = json_decode($wxData->config,true);
            $wxData->start = $wxData_arr['start'];
            $wxData->end = $wxData_arr['end'];
            $wxData->sex = $wxData_arr['sex'];
            $wxData->group = $wxData_arr['group'];
            $wxData->msg = $wxData_arr['msg'];
            $wxData->interval = isset($wxData_arr['interval'])?$wxData_arr['interval']:0;
            $wxData->control = isset($wxData_arr['type'])?$wxData_arr['type']:0;
        }else{
            $wxData = new \stdClass();
            $wxData->start = 0;
            $wxData->end = 0;
            $wxData->sex = 0;
            $wxData->group = [];
            $wxData->msg = '';
            $wxData->interval =0;
            $wxData->control = 1;
        }
        $tmp = [];
        $wx_group = array_values($wx_group);
        $array_intersect = array_intersect($wx_group,$wxData->group);
        foreach ($wx_group as $k=> &$vs){
            $groupInfo = RobotGroup::where('wx_group_id',$vs)->first();
            $stmp = [
                'name'=>$vs,
                'sname'=>isset($groupInfo->name)?$groupInfo->name:'暂无',
                'status'=>0,
            ];
            if(isset($array_intersect[$k])&&$vs==$array_intersect[$k]){
                $stmp = [
                    'name'=>$vs,
                    'sname'=>isset($groupInfo->name)?$groupInfo->name:'暂无',
                    'status'=>1,
                ];
            }
            $tmp[] = $stmp;
        }

        $returnData = ['robot'=>$robot,'group'=>$tmp,'request'=>$request,'request_url'=>url()->current(),'select'=>$tg,'wxdata'=>$wxData];
        return view('group.group_2')->with('robots', [])->with($returnData);
    }

    /**
     * Display a listing of the Game.
     *
     * @param Request $request
     * @return Response
     */
    public function groupKeywordReply(Request $request)
    {
        $robot = Robot::where($this->condition())->pluck('nickname','id');

        $reply = GroupReply::where($this->condition())->paginate(10);

        $reply->appends(array(
            'page' => $request->page,
            'name' => $request->name,
        ));

        $res = isset($_SESSION['group_robot_ids_3'])?$_SESSION['group_robot_ids_3']:[] ;
        //通过微信获取微信群
        $tg  = [];
        $id = 0;
        if($res){
            foreach($res as &$v){
                $tg[$v] = $v;
            }
        }
        if($res){
            $id = current($res);
        }
        $wxData = GroupConfig::where('robot_id',$id)->where('type',3)->where($this->condition())->first();
        if($wxData){
            $wxData_arr = json_decode($wxData->config,true);
            $wxData->start = $wxData_arr['start'];
            $wxData->end = $wxData_arr['end'];
            $wxData->interval = isset($wxData_arr['interval'])?$wxData_arr['interval']:0;
            $wxData->group = $wxData_arr['group'];
        }else{
            $wxData = new \stdClass();
            $wxData->start = 0;
            $wxData->end = 0;
            $wxData->status = 0;
            $wxData->interval = 0;
            $wxData->robot_id = current($tg);
            $wxData->group = [];
            $wxData->msg = '';
        }

        //通过微信获取微信群
        $wx_group = RobotGroup::whereIn('robot_id',$res)->pluck('wx_group_id','id')->toArray();
        $tmp = [];
        $wx_group = array_values($wx_group);
        $array_intersect = array_intersect($wx_group,$wxData->group);
        foreach ($wx_group as $k=> &$vs){
            $groupInfo = RobotGroup::where('wx_group_id',$vs)->first();
            $stmp = [
                'name'=>$vs,
                'sname'=>isset($groupInfo->name)?$groupInfo->name:'暂无',
                'status'=>0,
            ];
            if(isset($array_intersect[$k])&&$vs==$array_intersect[$k]){
                $stmp = [
                    'name'=>$vs,
                    'sname'=>isset($groupInfo->name)?$groupInfo->name:'暂无',
                    'status'=>1,
                ];
            }
            $tmp[] = $stmp;
        }
        $returnData = ['replys'=>$reply,'robot'=>$robot,'request'=>$request,'request_url'=>url()->current(),'select'=>$tg,'wxdata'=>$wxData,'group'=>$tmp];
        return view('group.group_3')->with($returnData);
    }
    /**
     * Display a listing of the Game.
     *
     * @param Request $request
     * @return Response
     */
    public function wallet(Request $request){
        $robot = Robot::where($this->condition())->pluck('nickname','id');
        $request = $request->all();
//        $wx_group = ['微信群1','微信群2','微信群3','微信群4'];//todo;
        $res = isset($_SESSION['group_robot_ids_4'])?$_SESSION['group_robot_ids_4']:[] ;
        //通过微信获取微信群
        $wx_group = RobotGroup::whereIn('robot_id',$res)->pluck('wx_group_id','id')->toArray();
        $i = 0;
        $tg = [];
        $id= 0;
        if($res){
            foreach($res as &$v){
                $tg[$v] = $v;
                $i++;
            }
        }
        if($res){
            $id = current($res);
        }
        $wxData = GroupConfig::where('robot_id',$id)->where('type',4)->where($this->condition())->first();
        if($wxData){
            $wxData_arr = json_decode($wxData->config,true);
            $wxData->start = $wxData_arr['start'];
            $wxData->end = $wxData_arr['end'];
            $wxData->sex = isset($wxData_arr['sex'])?$wxData_arr['sex']:0;
            $wxData->group = $wxData_arr['group'];
            $wxData->msg = $wxData_arr['msg'];
            $wxData->interval = isset($wxData_arr['interval'])?$wxData_arr['interval']:0;
            $wxData->control = isset($wxData_arr['type'])?$wxData_arr['type']:0;
        }else{
            $wxData = new \stdClass();
            $wxData->start = 0;
            $wxData->end = 0;
            $wxData->sex = 0;
            $wxData->group = [];
            $wxData->msg = '';
            $wxData->interval =0;
            $wxData->control = 1;
        }
        $tmp = [];
        $wx_group = array_values($wx_group);
        $array_intersect = array_intersect($wx_group,$wxData->group);
        foreach ($wx_group as $k=> &$vs){
            $groupInfo = RobotGroup::where('wx_group_id',$vs)->first();
            $stmp = [
                'name'=>$vs,
                'sname'=>isset($groupInfo->name)?$groupInfo->name:'暂无',
                'status'=>0,
            ];
            if(isset($array_intersect[$k])&&$vs==$array_intersect[$k]){
                $stmp = [
                    'name'=>$vs,
                    'sname'=>isset($groupInfo->name)?$groupInfo->name:'暂无',
                    'status'=>1,
                ];
            }
            $tmp[] = $stmp;
        }
        $returnData = ['robot'=>$robot,'group'=>$tmp,'request'=>$request,'request_url'=>url()->current(),'select'=>$tg,'wxdata'=>$wxData];
        return view('group.group_4')->with('robots', [])->with($returnData);
    }
    /**
     * Display a listing of the Game.
     *
     * @param Request $request
     * @return Response
     */
    public function link(Request $request){
        $robot = Robot::where($this->condition())->pluck('nickname','id');
        $request = $request->all();
//        $wx_group = ['微信群1','微信群2','微信群3','微信群4'];//todo;
        $res = isset($_SESSION['group_robot_ids_5'])?$_SESSION['group_robot_ids_5']:[] ;
        //通过微信获取微信群
        $wx_group = RobotGroup::whereIn('robot_id',$res)->pluck('wx_group_id','id')->toArray();
        $i = 0;
        $tg = [];
        $id= 0;
        if($res){
            foreach($res as &$v){
                $tg[$v] = $v;
                $i++;
            }
        }
        if($res){
            $id = current($res);
        }
        $wxData = GroupConfig::where('robot_id',$id)->where('type',5)->where($this->condition())->first();
        if($wxData){
            $wxData_arr = json_decode($wxData->config,true);
            $wxData->start = $wxData_arr['start'];
            $wxData->end = $wxData_arr['end'];
            $wxData->sex = $wxData_arr['sex'];
            $wxData->group = $wxData_arr['group'];
            $wxData->msg = $wxData_arr['msg'];
            $wxData->interval = isset($wxData_arr['interval'])?$wxData_arr['interval']:0;
            $wxData->control = isset($wxData_arr['type'])?$wxData_arr['type']:0;
        }else{
            $wxData = new \stdClass();
            $wxData->start = 0;
            $wxData->end = 0;
            $wxData->sex = 0;
            $wxData->group = [];
            $wxData->msg = '';
            $wxData->interval =0;
            $wxData->control = 1;
        }
        $tmp = [];
        $wx_group = array_values($wx_group);
        $array_intersect = array_intersect($wx_group,$wxData->group);
        foreach ($wx_group as $k=> &$vs){
            $groupInfo = RobotGroup::where('wx_group_id',$vs)->first();
            $stmp = [
                'name'=>$vs,
                'sname'=>isset($groupInfo->name)?$groupInfo->name:'暂无',
                'status'=>0,
            ];
            if(isset($array_intersect[$k])&&$vs==$array_intersect[$k]){
                $stmp = [
                    'name'=>$vs,
                    'sname'=>isset($groupInfo->name)?$groupInfo->name:'暂无',
                    'status'=>1,
                ];
            }
            $tmp[] = $stmp;
        }
        $returnData = ['robot'=>$robot,'group'=>$tmp,'request'=>$request,'request_url'=>url()->current(),'select'=>$tg,'wxdata'=>$wxData];
        return view('group.group_5')->with('robots', [])->with($returnData);
    }

    /**
     * Display a listing of the Game.
     *
     * @param Request $request
     * @return Response
     */
    public function addreply(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            if (isset($data['robot_id'])) {

                $robot_ids = explode(',',$data['robot_id']);
                foreach ($robot_ids as $v) {
                    if($data['id']>0){
                        $where['id'] = $data['id'];
                    }else{
                        $where['id'] = 'k';
                    }
                    $where['robot_id'] = $d['robot_id'] = $v;
                    $d['reply'] = $data['reply'];
                    $d['keyword'] = $data['keyword'];
                    $d['like_keyword'] = $data['like_keyword'];
                    $d['user_id'] = $this->authUserInfo()->id;
                    $reply = GroupReply::where($where)->first();
                    if(!$reply){
                        GroupReply::insert($d);
                    }else{
                        GroupReply::where($where)->update($d);
                    }
                }

            }

            Flash::success('更新成功');
            return redirect(url('group/groupKeywordReply'));
        }
        $problem = $relation = [];
        if($request->robot_id>0){
            //页面显示
            $reply = [];
            if($request->id){
                $where[] = ['id','=',$request->id];
//                $where[] = ['robot_id','=',$request->robot_id];
                $reply = GroupReply::where($where)->first();
            }else{
                $where[] = ['robot_id','=',$request->robot_id];
            }


            if($reply){
                $problem = json_decode($reply->problem,true);
                $relation = json_decode($reply->relation,true);
                $reply->control = isset($reply->type)?$reply->type:1;
                $reply->msg = $reply->answer;
            }else{
                $reply = new \stdClass();
                $reply->control = 1;
                $reply->reply = '';
                $reply->keyword = '';
                $reply->like_keyword = '';
            }
        }
        $returnData = ['request'=>$request,'reply'=>$reply,'request_url'=>url()->current()];
        return view('group.add_reply')->with($returnData);
    }
    /**
     * Remove the specified Game from storage.
     * @desc 删除自动回复
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $reply = GroupReply::where('id',$id)->first();
        if (empty($reply)) {
            Flash::error('记录不存在');

            return redirect('group/groupKeywordReply');
        }

        GroupReply::where('id',$id)->delete();

        Flash::success('reply deleted successfully.');

        return redirect('group/groupKeywordReply');
    }


    /**
     * Remove the specified Game from storage.
     * @desc 删除自动回复
     * @param  int $id
     *
     * @return Response
     */
    public function destroySend(Request $request)
    {
        $id = $request->id;
        $reply = GroupSend::where('id',$id)->first();
        if (empty($reply)) {
            Flash::error('记录不存在');

            return redirect('group/groupSend');
        }

        GroupSend::where('id',$id)->delete();

        Flash::success('reply deleted successfully.');

        return redirect('group/groupSend');
    }

    /**
     * Remove the specified Game from storage.
     * @desc 删除自动回复
     * @param  int $id
     *
     * @return Response
     */
    public function destroySign(Request $request)
    {
        $id = $request->id;
        $reply = GroupSign::where('id',$id)->first();
        if (empty($reply)) {
            Flash::error('记录不存在');

            return redirect('group/groupSign');
        }

        GroupSign::where('id',$id)->delete();

        Flash::success('reply deleted successfully.');

        return redirect('group/groupSign');
    }
    /**
     * Display a listing of the Game.
     *
     * @param Request $request
     * @return Response
     */
    public function groupSend(Request $request)
    {
        $_SESSION['group_robot_ids_6'] = [];
        $robot = Robot::where($this->condition())->pluck('nickname','id');

        $reply = GroupSend::where($this->condition())->paginate(10);
        if($reply){
            foreach($reply as &$v){
                $v->group_desc = json_decode($v->group,true);
                if($v->group_desc){
                    $groupArr = RobotGroup::whereIn('wx_group_id',$v->group_desc)->pluck('name')->toArray();
                }

                if($groupArr){
                    $v->group_desc = implode(',',$groupArr);
                }else{
                    $v->group_desc = '';
                }
//                $week_desc = implode(',',json_decode($v->week,true));
                $week_arr = [];
                $arr = json_decode($v->week,true);
                if(is_array(($arr))){
                    foreach(json_decode($v->week,true) as $k=>$vs){
                        $week_arr[$k] = $vs>0?'周'.date("{$vs}"):'周日';
                    }
                }

                $v->week_desc = implode(',',($week_arr));
            }
        }


        $reply->appends(array(
            'page' => $request->page,
            'name' => $request->name,
        ));

        $res = isset($_SESSION['group_robot_ids_3'])?$_SESSION['group_robot_ids_3']:[] ;
        //通过微信获取微信群
        $tg  = [];
        $id = 0;
        if($res){
            foreach($res as &$v){
                $tg[$v] = $v;
            }
        }
        if($res){
            $id = current($res);
        }
        $wxData = GroupConfig::where('robot_id',$id)->where('type',3)->where($this->condition())->first();
        if($wxData){
            $wxData_arr = json_decode($wxData->config,true);
            $wxData->start = $wxData_arr['start'];
            $wxData->end = $wxData_arr['end'];
            $wxData->interval = isset($wxData_arr['interval'])?$wxData_arr['interval']:0;
            $wxData->group = $wxData_arr['group'];
        }else{
            $wxData = new \stdClass();
            $wxData->start = 0;
            $wxData->end = 0;
            $wxData->status = 0;
            $wxData->interval = 0;
            $wxData->robot_id = current($tg);
            $wxData->group = [];
            $wxData->msg = '';
        }

        //通过微信获取微信群
        $wx_group = RobotGroup::whereIn('robot_id',$res)->pluck('wx_group_id','id')->toArray();
        $tmp = [];
        $wx_group = array_values($wx_group);
        $array_intersect = array_intersect($wx_group,$wxData->group);
        foreach ($wx_group as $k=> &$vs){
            $groupInfo = RobotGroup::where('wx_group_id',$vs)->first();
            $stmp = [
                'name'=>$vs,
                'sname'=>isset($groupInfo->name)?$groupInfo->name:'暂无',
                'status'=>0,
            ];
            if(isset($array_intersect[$k])&&$vs==$array_intersect[$k]){
                $stmp = [
                    'name'=>$vs,
                    'sname'=>isset($groupInfo->name)?$groupInfo->name:'暂无',
                    'status'=>1,
                ];
            }
            $tmp[] = $stmp;
        }

        $returnData = ['replys'=>$reply,'robot'=>$robot,'request'=>$request,'request_url'=>url()->current(),'select'=>$tg,'wxdata'=>$wxData,'group'=>$tmp];
        return view('group.group_6')->with($returnData);
    }

    /**
     * Display a listing of the Game.
     *
     * @param Request $request
     * @return Response
     */
    public function addSend(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            if (isset($data['robot_id'])) {
                $robot_ids =$data['robot_id'];
                if(!isset($data['status'])){
                    Flash::error("请选择状态！");
                    return redirect($data['request_url']);
                }
                if(!isset($data['group'])){
                    Flash::error("请选择群组！");
                    return redirect($data['request_url']);
                }
                if(!isset($data['week'])){
                    Flash::error("请选择周！");
                    return redirect($data['request_url']);
                }
                foreach ($robot_ids as $v) {

                    $has_img = 0;
                    if ($request->isMethod('POST')&&in_array($data['control'],[2,3])) {
                        $file = $request->file('img');
                        //判断文件是否上传成功
                        if ($file) {

                            //原文件名
                            $originalName = $file->getClientOriginalName();
                            //扩展名
                            $ext = $file->getClientOriginalExtension();
                            //MimeType
                            $file_type = $file->getClientMimeType();
                            //临时绝对路径
                            $realPath = $file->getRealPath();
                            $filename = uniqid().'.'.$ext;
                            $fileDir = public_path().'/storage';
                            if (!is_dir($fileDir)) {
                                mkdir($fileDir, 0777, true);
                            }
                            $bool = $request->file('img')->move(public_path().'/storage/', $filename);
                            //$bool = Storage::disk('public')->put($filename,file_get_contents($realPath));
                            //判断是否上传成功
                            $filename = 'http://'.$_SERVER['HTTP_HOST'].'/storage/'.$filename;
                            $msg = $filename;
                            $has_img = 1;
                        }
                    }
                    if($has_img==0){
                        $msg = $data['msg'];
                    }
                    if($data['id']>0){
                        $where['id'] = $data['id'];
                    }else{
                        $where['id'] = 'k';
                    }
                    $where['robot_id'] = $d['robot_id'] = $v;
                    $where['user_id'] = $d['user_id'] = $this->authUserInfo()->id;
                    $d['msg'] = $msg;
                    $d['type'] = $data['control'];
                    $d['status'] = $data['status'];
                    $d['is_repeat'] = $data['is_repeat'];
                    $d['stime'] = $data['time'].':'.$data['minute'];
                    $d['week'] = json_encode($data['week'],JSON_UNESCAPED_UNICODE);
                    $d['group'] = json_encode($data['group'],JSON_UNESCAPED_UNICODE);
                    $reply = GroupSend::where($where)->first();
                    if(!$reply){
                        GroupSend::insert($d);
                    }else{
                        GroupSend::where($where)->update($d);
                    }
                }

            }

            Flash::success('更新成功');
            return redirect(url('group/groupSend'));
        }
        $robot = Robot::where($this->condition())->pluck('nickname','id');
        $request = $request->all();
//        $wx_group = ['微信群1','微信群2','微信群3','微信群4'];//todo;
        $robotId = isset($request['robot_id'])?$request['robot_id']:0;
        $res = isset($_SESSION['group_robot_ids_6'])&&!empty($_SESSION['group_robot_ids_6'])?$_SESSION['group_robot_ids_6']:[$robotId] ;
        //通过微信获取微信群
        $wx_group = RobotGroup::whereIn('robot_id',$res)->pluck('wx_group_id','id')->toArray();
        $i = 0;
        $tg = [];
        $id= 0;
        if($res){
            foreach($res as &$v){
                $tg[$v] = $v;
                $i++;
            }
        }
        if(isset($request['id'])){
            $id = $request['id'];
        }else{
            $id = 0;
            $request['id'] = 0;
        }
        $wxData = GroupSend::where('robot_id',$res)->where('id',$id)->where($this->condition())->first();
        if($wxData){
            $wxData_arr = json_decode($wxData->config,true);
            $wxData->start = $wxData_arr['start'];
            $wxData->end = $wxData_arr['end'];
            $wxData->sex = $wxData_arr['sex'];
            $wxData->group_arr = json_decode($wxData->group,true);
            $wxData->msg = $wxData->msg;
            $wxData->interval = isset($wxData_arr['interval'])?$wxData_arr['interval']:0;
            $wxData->control = $wxData->type;
            $stime = explode(':',$wxData->stime);
            $wxData->time = isset($stime[0])?$stime[0]:00;
            $wxData->minute = isset($stime[1])?$stime[1]:00;
        }else{
            $wxData = new \stdClass();
            $wxData->id = 0;
            $wxData->start = 0;
            $wxData->end = 0;
            $wxData->sex = 0;
            $wxData->group = [];
            $wxData->msg = '';
            $wxData->interval =0;
            $wxData->control =1;
            $wxData->group_arr =[];
            $wxData->week = [];
            $wxData->is_repeat = 0;
            $wxData->time = 0;
            $wxData->time = 00;
            $wxData->minute = 00;
        }
        $tmp = [];
        $wx_group = array_values($wx_group);
        $array_intersect = array_intersect($wx_group,$wxData->group_arr);
        foreach ($wx_group as $k=> &$vs){
            $groupInfo = RobotGroup::where('wx_group_id',$vs)->first();
            $stmp = [
                'name'=>$vs,
                'sname'=>isset($groupInfo->name)?$groupInfo->name:'暂无',
                'status'=>0,
            ];
            if(isset($array_intersect[$k])&&$vs==$array_intersect[$k]){
                $stmp = [
                    'name'=>$vs,
                    'sname'=>isset($groupInfo->name)?$groupInfo->name:'暂无',
                    'status'=>1,
                ];
            }
            $tmp[] = $stmp;
        }
        //周一-周日
        for($i=0;$i<=6;$i++){
            $val = [
                'id'=>$i,
                'name'=>$i==0?'周日':"周".($i),
                'status'=>0
            ];
            if($wxData->id>0){
                $week_arr = json_decode($wxData->week);
                foreach($week_arr as $vv){
                    if($vv==$val['id']){
                        $val = [
                            'id'=>$val['id'],
                            'name'=>$val['name'],
                            'status'=>1
                        ];
                    }

                }
            }
            $week[$i] = $val ;
        }
        //0-24
        for($i=0;$i<=23;$i++){
            $val = [
                'name'=>strlen($i)<2?'0'.$i:$i,
                'status'=>0
            ];
            $time[$i] = $val;
        }
        //60
        for($i=0;$i<=59;$i++){
            $val = [
                'name'=>strlen($i)<2?'0'.$i:$i,
                'status'=>0
            ];
            $minute[$i] = $val;
        }
        $returnData = ['minute'=>$minute,'robot'=>$robot,'group'=>$tmp,'request'=>$request,'request_url'=>url()->current(),'select'=>$tg,'wxdata'=>$wxData,'week'=>$week,'time'=>$time];
        return view('group.add_send')->with($returnData);
    }
    /**
     * Display a listing of the Game.
     *
     * @param Request $request
     * @return Response
     */
    public function groupSign(Request $request)
    {
        $_SESSION['group_robot_ids_7'] = [];
        $robot = Robot::where($this->condition())->pluck('nickname','id');

        $reply = GroupSign::where($this->condition())->paginate(10);
        if($reply){
            foreach($reply as &$v){
                $v->group_desc = json_decode($v->group,true);
                if($v->group_desc){
                    $groupArr = RobotGroup::whereIn('wx_group_id',$v->group_desc)->pluck('name')->toArray();
                }

                if($groupArr){
                    $v->group_desc = implode(',',$groupArr);
                }else{
                    $v->group_desc = '';
                }
                //获取今日签到人数
                $sign_count = GroupSignLog::where('sign_date','>=',date('Y-m-d'))->where('sign_id','=',$v->id)->count();
                $v->sign_count = $sign_count;
            }
        }


        $reply->appends(array(
            'page' => $request->page,
            'name' => $request->name,
        ));

        $res = isset($_SESSION['group_robot_ids_3'])?$_SESSION['group_robot_ids_3']:[] ;
        //通过微信获取微信群
        $tg  = [];
        $id = 0;
        if($res){
            foreach($res as &$v){
                $tg[$v] = $v;
            }
        }
        if($res){
            $id = current($res);
        }
        $wxData = GroupConfig::where('robot_id',$id)->where('type',3)->where($this->condition())->first();
        if($wxData){
            $wxData_arr = json_decode($wxData->config,true);
            $wxData->start = $wxData_arr['start'];
            $wxData->end = $wxData_arr['end'];
            $wxData->interval = isset($wxData_arr['interval'])?$wxData_arr['interval']:0;
            $wxData->group = $wxData_arr['group'];
        }else{
            $wxData = new \stdClass();
            $wxData->start = 0;
            $wxData->end = 0;
            $wxData->status = 0;
            $wxData->interval = 0;
            $wxData->robot_id = current($tg);
            $wxData->group = [];
            $wxData->msg = '';
        }

        //通过微信获取微信群
        $wx_group = RobotGroup::whereIn('robot_id',$res)->pluck('wx_group_id','id')->toArray();
        $tmp = [];
        $wx_group = array_values($wx_group);
        $array_intersect = array_intersect($wx_group,$wxData->group);
        foreach ($wx_group as $k=> &$vs){
            $groupInfo = RobotGroup::where('wx_group_id',$vs)->first();
            $stmp = [
                'name'=>$vs,
                'sname'=>isset($groupInfo->name)?$groupInfo->name:'暂无',
                'status'=>0,
            ];
            if(isset($array_intersect[$k])&&$vs==$array_intersect[$k]){
                $stmp = [
                    'name'=>$vs,
                    'sname'=>isset($groupInfo->name)?$groupInfo->name:'暂无',
                    'status'=>1,
                ];
            }
            $tmp[] = $stmp;
        }
        $returnData = ['replys'=>$reply,'robot'=>$robot,'request'=>$request,'request_url'=>url()->current(),'select'=>$tg,'wxdata'=>$wxData,'group'=>$tmp];
        return view('group.group_7')->with($returnData);
    }

    /**
     * Display a listing of the Game.
     *
     * @param Request $request
     * @return Response
     */
    public function addSign(Request $request){
        if($request->isMethod('post')){
            $data = $request->all();
            if (isset($data['robot_id'])) {

                $robot_ids =$data['robot_id'];
                foreach ($robot_ids as $v) {
                    if(!isset($data['status'])){
                        Flash::error("请选择状态！");
                        return redirect($data['request_url']);
                    }
                    if(!isset($data['group'])){
                        Flash::error("请选择发送的群组！");
                        return redirect($data['request_url']);
                    }
                    if($data['id']>0){
                        $where['id'] = $data['id'];
                    }else{
                        $where['id'] = 'k';
                    }
                    $where['robot_id'] = $d['robot_id'] = $v;
                    $where['user_id'] = $d['user_id'] = $this->authUserInfo()->id;
                    $d['msg'] = $data['msg'];
                    $d['status'] = isset($data['status'])?$data['status']:1;
                    $d['start_date'] = $data['start_date'];
                    $d['end_date'] = $data['end_date'];
                    $d['start_time'] = $data['start_time'].':'.$data['start_minute'];
                    $d['end_time'] = $data['end_time'].':'.$data['end_minute'];
                    $d['keyword'] = $data['keyword'];
                    $d['reply'] = $data['reply'];
                    $d['group'] = json_encode($data['group'],JSON_UNESCAPED_UNICODE);
                    //如果结束时间小于当前时间提示
                    $end_time = $data['end_date'].' '.$data['end_time'].':'.$data['end_minute'];
                    if($end_time<date('Y-m-d H:i:s')){
                            Flash::error("结束时间不能小于当前时间！");
                            return redirect($data['request_url']);
                    }
                    $reply = GroupSign::where($where)->first();
                    if(!$reply){
                        GroupSign::insert($d);
                    }else{
                        GroupSign::where($where)->update($d);
                    }
                }

            }

            Flash::success('更新成功');
            return redirect(url('group/groupSign'));
        }
        $robot = Robot::where($this->condition())->pluck('nickname','id');
        $request = $request->all();
//        $wx_group = ['微信群1','微信群2','微信群3','微信群4'];//todo;
        $res = isset($_SESSION['group_robot_ids_7'])&&!empty($_SESSION['group_robot_ids_7'])?$_SESSION['group_robot_ids_7']:[$request['robot_id']] ;

        //通过微信获取微信群
        $wx_group = RobotGroup::whereIn('robot_id',$res)->pluck('wx_group_id','id')->toArray();
        $i = 0;
        $tg = [];
        $id= 0;
        if($res){
            foreach($res as &$v){
                $tg[$v] = $v;
                $i++;
            }
        }
        if(isset($request['id'])){
            $id = $request['id'];
        }else{
            $id = 0;
            $request['id'] = 0;
        }
        $wxData = GroupSign::where('robot_id',$request['robot_id'])->where('id',$id)->where($this->condition())->first();
        if($wxData){
            $wxData_arr = json_decode($wxData->config,true);
            $wxData->start = $wxData_arr['start'];
            $wxData->end = $wxData_arr['end'];
            $wxData->sex = $wxData_arr['sex'];
            $wxData->group_arr = json_decode($wxData->group,true);
            $wxData->msg = $wxData->msg;
            $wxData->interval = isset($wxData_arr['interval'])?$wxData_arr['interval']:0;
            $wxData->control = $wxData->type;
            list($s,$e) = explode(':',$wxData->start_time);
            $wxData->start_time = $s;
            $wxData->start_minute =$e;

            list($s,$e) = explode(':',$wxData->end_time);
            $wxData->end_time = $s;
            $wxData->end_minute = $e;

        }else{
            $wxData = new \stdClass();
            $wxData->id = 0;
            $wxData->start = 0;
            $wxData->end = 0;
            $wxData->sex = 0;
            $wxData->group = [];
            $wxData->msg = '';
            $wxData->interval =0;
            $wxData->control =1;
            $wxData->group_arr =[];
            $wxData->start_time = '';
            $wxData->end_time = '';
            $wxData->start_time = 0;
            $wxData->start_minute = 00;
            $wxData->end_time = 00;
            $wxData->end_minute = 00;
            $wxData->status = 1;
        }
        $tmp = [];
        $wx_group = array_values($wx_group);
        $array_intersect = array_intersect($wx_group,$wxData->group_arr);
        foreach ($wx_group as $k=> &$vs){
            $groupInfo = RobotGroup::where('wx_group_id',$vs)->first();
            $stmp = [
                'name'=>$vs,
                'sname'=>isset($groupInfo->name)?$groupInfo->name:'暂无',
                'status'=>0,
            ];
            if(isset($array_intersect[$k])&&$vs==$array_intersect[$k]){
                $stmp = [
                    'name'=>$vs,
                    'sname'=>isset($groupInfo->name)?$groupInfo->name:'暂无',
                    'status'=>1,
                ];
            }
            $tmp[] = $stmp;
        }
        //周一-周日
        for($i=1;$i<=7;$i++){
            $val = [
                'name'=>$i==7?'周日':"周$i",
                'status'=>0
            ];
            $week[$i] = $val ;
        }
        //0-23
        for($i=0;$i<=23;$i++){
            $val = [
                'name'=>strlen($i)<2?'0'.$i:$i,
                'status'=>0
            ];
            $time[$i] = $val;
        }
        for($i=0;$i<=59;$i++){
            $val = [
                'name'=>strlen($i)<2?'0'.$i:$i,
                'status'=>0
            ];
            $minute[$i] = $val;
        }
        $returnData = ['minute'=>$minute,'robot'=>$robot,'group'=>$tmp,'request'=>$request,'request_url'=>url()->full(),'select'=>$tg,'wxdata'=>$wxData,'week'=>$week,'time'=>$time];
        return view('group.add_sign')->with($returnData);
    }
    /**
     * Display a listing of the Game.
     *
     * @param Request $request
     * @return Response
     */
    public function groupMember(Request $request)
    {

        $robot = Robot::where($this->condition())->pluck('nickname','id');
        $groupId = isset($_SESSION['group_ids_1'])?$_SESSION['group_ids_1']:[] ;
        $group = GroupMember::where($this->condition())->whereIn('group_id',$groupId)->paginate(10);
        $group->appends(array(
            'page' => $request->page,
            'name' => $request->name,
        ));
        $returnUrl = '/group/groupMember';
        if($request->is_kick==1){
            $ids = GroupMember::where($this->condition())->whereIn('group_id',$groupId)->pluck('id')->toArray();
            if($request->id){
                $ids = [$request->id];
            }
            GroupMember::whereIn('id',$ids)->update(['is_kick'=>1]);
            Flash::success('更新成功');
            return redirect($returnUrl);
        }
        if($request->is_block==1){
            $ids = GroupMember::where($this->condition())->whereIn('group_id',$groupId)->pluck('id')->toArray();
            if($request->id){
                $ids = [$request->id];
            }
            GroupMember::whereIn('id',$ids)->update(['is_kick'=>1,'is_block'=>1]);
            Flash::success('更新成功');
            return redirect($returnUrl);
        }
        if($request->is_admin==1){
            $ids = GroupMember::where($this->condition())->whereIn('group_id',$groupId)->pluck('id')->toArray();
            if($request->id){
                $ids = [$request->id];
            }
            GroupMember::whereIn('id',$ids)->update(['is_admin'=>1]);
            Flash::success('更新成功');
            return redirect($returnUrl);
        }
        $res = isset($_SESSION['group_robot_ids_8'])?$_SESSION['group_robot_ids_8']:[] ;
        //通过微信获取微信群
        $tg  = [];
        $id = 0;
        if($res){
            foreach($res as &$v){
                $tg[$v] = $v;
            }
        }
        if($res){
            $id = current($res);
        }


        $wxData = GroupConfig::where('robot_id',$id)->where('type',3)->where($this->condition())->first();
        if($wxData){
            $wxData_arr = json_decode($wxData->config,true);
            $wxData->start = $wxData_arr['start'];
            $wxData->end = $wxData_arr['end'];
            $wxData->interval = isset($wxData_arr['interval'])?$wxData_arr['interval']:0;
            $wxData->group = $wxData_arr['group'];
        }else{
            $wxData = new \stdClass();
            $wxData->start = 0;
            $wxData->end = 0;
            $wxData->status = 0;
            $wxData->interval = 0;
            $wxData->robot_id = current($tg);
            $wxData->group = [];
            $wxData->msg = '';
        }

        //通过微信获取微信群
        $wx_group = RobotGroup::whereIn('robot_id',$res)->pluck('id','name')->toArray();
        $gTmp = [];
        if($group){
            foreach($group as $value){
                $gTmp[] = $value->group_id;
            }
        }
        $tmp = [];
        $wx_group = array_values($wx_group);
        $array_intersect = array_intersect($wx_group,$groupId);
        foreach ($wx_group as $k=> &$vs){
            $groupInfo = RobotGroup::where('id',$vs)->first();
            $stmp = [
                'name'=>isset($groupInfo->name)?$groupInfo->name:'暂无',
                'id'=>isset($groupInfo->id)?$groupInfo->id:0,
                'sname'=>isset($groupInfo->name)?$groupInfo->name:'暂无',
                'status'=>0,
            ];
            if(isset($array_intersect[$k])&&$vs==$array_intersect[$k]){
                $stmp = [
                    'name'=>isset($groupInfo->name)?$groupInfo->name:'暂无',
                    'id'=>isset($groupInfo->id)?$groupInfo->id:0,
                    'sname'=>isset($groupInfo->name)?$groupInfo->name:'暂无',
                    'status'=>1,
                ];
            }
            $tmp[] = $stmp;
        }
        $returnData = ['replys'=>$group,'robot'=>$robot,'request'=>$request,'request_url'=>url()->current(),'select'=>$tg,'wxdata'=>$wxData,'group'=>$tmp];
        return view('group.group_8')->with($returnData);
    }

    /**
     * Display a listing of the Game.
     *
     * @param Request $request
     * @return Response
     */
    public function saveGroupId(Request $request){
        $request = $request->all();
        if($request['group_type']==1){
            $key = 'group_ids_1';//自动加载成员成为好友
        }
        if($request['group_type']==2){
            $key = 'group_ids_2';
        }
        if($request['group_type']==3){
            $key = 'group_ids_3';//自动应答
        }

        //通过微信获取微信群
        $_SESSION["{$key}"]  = [];
        if(isset($request['group_id'])&&$request['group_id']>0){
//            $tmp = !empty($_SESSION["{$key}"])?$_SESSION["$key"]:[];


//            $arr = array_merge( $tmp,$request['robot_id']);
//            foreach($arr as $k=> &$v){
//                if(!in_array($v,$request['robot_id'])){
//                    $v = $request['robot_id'][0];
//                }
//            }
            $_SESSION["{$key}"] = ($request['group_id']);
        }else{
            $_SESSION["{$key}"] = [];
        }

        echo  json_encode(['status'=>1,'group_id'=>$request['group_id']]);die;
    }
    /**
     * Display a listing of the Game.
     *
     * @param Request $request
     * @return Response
     */
    public function doWithGroupSend(Request $request){
        if (empty($request['check_val'])) {
            Flash::error('请选择处理的数据');

            return redirect($request->request_url);
        }
        if (!in_array($request['status'],[0,1])) {
            Flash::error('请确认开启还是关闭');

            return redirect($request->request_url);
        }
        $status = $request->status;
        $check_val = explode(',',$request->check_val);
        if($request->save_type==1){
            GroupSend::whereIn('id',$check_val)->update(['status'=>$status]);
        }
        if($request->save_type==2){
            GroupSign::whereIn('id',$check_val)->update(['status'=>$status]);
        }
        Flash::success('更新成功');
        return redirect($request->request_url);
    }

    /**
     * Display a listing of the Game.
     *
     * @param Request $request
     * @return Response
     */
    public function groupKick(Request $request)
    {

        $robot = Robot::where($this->condition())->pluck('nickname','id');
        $groupId = isset($_SESSION['group_ids_2'])?$_SESSION['group_ids_2']:[] ;
        $group = GroupKick::where($this->condition())->whereIn('group_id',$groupId)->paginate(10);
        if($group){
            foreach ($group as &$val){
                $group_info = RobotGroup::where('id',$val->group_id)->pluck('name')->first();
                $val->group_desc = $group_info;
            }
        }
        $group->appends(array(
            'page' => $request->page,
            'name' => $request->name,
        ));
        $returnUrl = '/group/groupKick';
        if(in_array($request->status,[0,1])&&isset($request->status)){
            $ids = GroupMember::where($this->condition())->whereIn('group_id',$groupId)->pluck('id')->toArray();
            GroupKick::whereIn('id',$ids)->update(['status'=>$request->status]);
            Flash::success('更新成功');
            return redirect($returnUrl);
        }
        $res = isset($_SESSION['group_robot_ids_9'])?$_SESSION['group_robot_ids_9']:[] ;
        //通过微信获取微信群
        $tg  = [];
        $id = 0;
        if($res){
            foreach($res as &$v){
                $tg[$v] = $v;
            }
        }
        if($res){
            $id = current($res);
        }


        $wxData = GroupConfig::where('robot_id',$id)->where('type',6)->where($this->condition())->first();
        if($wxData){
            $wxData_arr = json_decode($wxData->config,true);
            $wxData->group = $wxData_arr['group'];
        }else{
            $wxData = new \stdClass();
            $wxData->start = 0;
            $wxData->end = 0;
            $wxData->status = 0;
            $wxData->interval = 0;
            $wxData->robot_id = current($tg);
            $wxData->group = [];
            $wxData->msg = '';
        }

        //通过微信获取微信群
        $wx_group = RobotGroup::whereIn('robot_id',$res)->pluck('id','name')->toArray();
        $gTmp = [];
        if($group){
            foreach($group as $value){
                $gTmp[] = $value->group_id;
            }
        }
        $tmp = [];
        $wx_group = array_values($wx_group);
        $array_intersect = array_intersect($wx_group,$groupId);
        foreach ($wx_group as $k=> &$vs){
            $groupInfo = RobotGroup::where('id',$vs)->first();
            $stmp = [
                'name'=>isset($groupInfo->name)?$groupInfo->name:'暂无',
                'id'=>isset($groupInfo->id)?$groupInfo->id:0,
                'sname'=>isset($groupInfo->name)?$groupInfo->name:'暂无',
                'status'=>0,
            ];
            if(isset($array_intersect[$k])&&$vs==$array_intersect[$k]){
                $stmp = [
                    'name'=>isset($groupInfo->name)?$groupInfo->name:'暂无',
                    'id'=>isset($groupInfo->id)?$groupInfo->id:0,
                    'sname'=>isset($groupInfo->name)?$groupInfo->name:'暂无',
                    'status'=>1,
                ];
            }
            $tmp[] = $stmp;
        }
        $returnData = ['replys'=>$group,'robot'=>$robot,'request'=>$request,'request_url'=>url()->current(),'select'=>$tg,'wxdata'=>$wxData,'group'=>$tmp];
        return view('group.group_9')->with($returnData);
    }
    /**
     * Display a listing of the Game.
     *
     * @param Request $request
     * @return Response
     */
    public function groupComplain(Request $request)
    {
        $robot = Robot::where($this->condition())->pluck('nickname','id');
        $groupId = isset($_SESSION['group_ids_3'])?$_SESSION['group_ids_3']:[] ;
        $group = GroupComplain::where($this->condition())->whereIn('group_id',$groupId)->paginate(10);
        if($group){
            foreach ($group as &$val){
                $group_info = RobotGroup::where('id',$val->group_id)->pluck('name')->first();
                $val->group_desc = $group_info;
            }
        }
        $group->appends(array(
            'page' => $request->page,
            'name' => $request->name,
        ));
        $returnUrl = '/group/groupComplain';
        if(in_array($request->status,[0,1])&&isset($request->status)){
            $ids = GroupComplain::where($this->condition())->whereIn('group_id',$groupId)->pluck('id')->toArray();
            GroupComplain::whereIn('id',$ids)->update(['status'=>$request->status]);
            Flash::success('更新成功');
            return redirect($returnUrl);
        }
        $res = isset($_SESSION['group_robot_ids_10'])?$_SESSION['group_robot_ids_10']:[] ;
        //通过微信获取微信群
        $tg  = [];
        $id = 0;
        if($res){
            foreach($res as &$v){
                $tg[$v] = $v;
            }
        }
        if($res){
            $id = current($res);
        }


        $wxData = GroupConfig::where('robot_id',$id)->where('type',7)->where($this->condition())->first();
        if($wxData){
            $wxData_arr = json_decode($wxData->config,true);
            $wxData->group = $wxData_arr['group'];
        }else{
            $wxData = new \stdClass();
            $wxData->start = 0;
            $wxData->end = 0;
            $wxData->status = 0;
            $wxData->interval = 0;
            $wxData->robot_id = current($tg);
            $wxData->group = [];
            $wxData->msg = '';
        }

        //通过微信获取微信群
        $wx_group = RobotGroup::whereIn('robot_id',$res)->pluck('id','name')->toArray();
        $gTmp = [];
        if($group){
            foreach($group as $value){
                $gTmp[] = $value->group_id;
            }
        }
        $tmp = [];
        $wx_group = array_values($wx_group);
        $array_intersect = array_intersect($wx_group,$groupId);
        foreach ($wx_group as $k=> &$vs){
            $groupInfo = RobotGroup::where('id',$vs)->first();
            $stmp = [
                'name'=>isset($groupInfo->name)?$groupInfo->name:'暂无',
                'id'=>isset($groupInfo->id)?$groupInfo->id:0,
                'sname'=>isset($groupInfo->name)?$groupInfo->name:'暂无',
                'status'=>0,
            ];
            if(isset($array_intersect[$k])&&$vs==$array_intersect[$k]){
                $stmp = [
                    'name'=>isset($groupInfo->name)?$groupInfo->name:'暂无',
                    'id'=>isset($groupInfo->id)?$groupInfo->id:0,
                    'sname'=>isset($groupInfo->name)?$groupInfo->name:'暂无',
                    'status'=>1,
                ];
            }
            $tmp[] = $stmp;
        }
        $returnData = ['replys'=>$group,'robot'=>$robot,'request'=>$request,'request_url'=>url()->current(),'select'=>$tg,'wxdata'=>$wxData,'group'=>$tmp];
        return view('group.group_10')->with($returnData);
    }
}

