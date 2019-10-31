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
use App\Models\RobotGroup;
use DB;
class FriendController extends AppBaseController
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
    public function auto(Request $request)
    {
        $res = isset($_SESSION['robot_ids_1'])?$_SESSION['robot_ids_1']:[] ;
        //通过微信获取微信群
        $tg  = [];
        if($res){
            foreach($res as &$v){
                $tg[$v] = $v;
            }
        }

        $id = 0;
        if($res){
            $id = current($res);
        }
        $wxData = Friend::where('robot_id',$id)->where('type',1)->where($this->condition())->first();
        if($wxData){
            $wxData_arr = json_decode($wxData->config,true);
            $wxData->start = $wxData_arr['start'];
            $wxData->end = $wxData_arr['end'];
            $wxData->msg = $wxData_arr['msg'];
            $wxData->control = isset($wxData_arr['type'])?$wxData_arr['type']:0;
        }else{
            $wxData = new \stdClass();
            $wxData->start = 0;
            $wxData->end = 24;
            $wxData->status = 0;
            $wxData->control = 1;
            $wxData->msg = '';
        }

        $robot = Robot::where($this->condition())->pluck('nickname','id');

        $returnData = ['select'=>$tg,'wxdata'=>$wxData,'robot'=>$robot,'request_url'=>url()->current()];
        return view('friend.auto')->with($returnData);
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

                    $config = ['msg'=>$msg, 'start'=>$data['start'], 'end'=>$data['end'],'type'=>$data['control']];
                }
                if($type==2){
                    $config = ['start'=>$data['start'], 'end'=>$data['end'],'delay'=>$data['delay']];
                }
                if($type==3){
                    if(!isset($data['group'])){
                        $data['group'] = [];
                    }
                    $config = ['msg'=>$data['msg'], 'start'=>$data['start'], 'end'=>$data['end'],'group'=>$data['group'],'sex'=>$data['sex'],'interval'=>$data['interval']];

                }
                if($type==4){
                    $config = ['msg'=>$data['msg'], 'start'=>$data['start'], 'end'=>$data['end'],'sex'=>$data['sex'],'province'=>$data['province'],'site'=>$data['city'],'interval'=>$data['interval']];
                }
                if($type==5){
                    $config = ['status'=>$data['status'], 'start'=>$data['start'], 'end'=>$data['end'],'search'=>$data['keyword'],
                        'msg'=>$data['verify'],'sex'=>$data['sex'],'interval'=>$data['interval']
                        ];
                }
                $d['config'] = json_encode($config,JSON_UNESCAPED_UNICODE);
                Friend::updateOrInsert($where, $d);

            }
        }

        Flash::success('更新成功');
        switch ($type){
            case 1:
                $url = 'friend/auto';
                break;
            case 2:
                $url = 'friend/autoreply';
                break;
            case 3:
                $url = 'friend/response';
                break;
            case 4:
                $url = 'friend/nearby';
                break;
            case 5:
                $url = 'friend/batchSearch';
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
    public function autoreply(Request $request)
    {
        $robot = Robot::where($this->condition())->pluck('nickname','id');

        $reply = FriendReply::where($this->condition())->paginate(10);
        $p_tmp = $r_tmp =[];
        foreach($reply as $v){
            $problem_json = $v->problem;
            $problem_arr = json_decode($problem_json,1);
            if(is_array($problem_arr)){
                $problem_arr = array_values($problem_arr);
                for($i = 0;$i<count($problem_arr);$i++){
                    $k = 'problem'.($i+1);
                    $p_tmp[$k] = $problem_arr[$i];
                }
            }


            //关联问题
            $relation_json = $v->relation;
            $relation_arr = json_decode($relation_json,1);
            if(is_array($relation_arr)){
                $relation_arr = array_values($relation_arr);
                for($i = 0;$i<count($relation_arr);$i++){
                    $k = 'relation'.($i+1);
                    $r_tmp[$k] = $relation_arr[$i];
                }
            }


           $v->relation_arr = $r_tmp;
            $v->problem_arr = $p_tmp;
        }
        $reply->appends(array(
            'page' => $request->page,
            'name' => $request->name,
        ));

        $res = isset($_SESSION['robot_ids_2'])?$_SESSION['robot_ids_2']:[] ;
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
        $wxData = Friend::where('robot_id',$id)->where('type',2)->where($this->condition())->first();
        if($wxData){
            $wxData_arr = json_decode($wxData->config,true);
            $wxData->start = $wxData_arr['start'];
            $wxData->end = $wxData_arr['end'];
            $wxData->delay = isset($wxData_arr['delay'])?$wxData_arr['delay']:0;
        }else{
            $wxData = new \stdClass();
            $wxData->start = 0;
            $wxData->end = 0;
            $wxData->status = 0;
            $wxData->delay = 0;
            $wxData->robot_id = current($tg);
            $wxData->msg = '';
        }

        $returnData = ['replys'=>$reply,'robot'=>$robot,'request'=>$request,'request_url'=>url()->current(),'select'=>$tg,'wxdata'=>$wxData];
        return view('friend.auto_reply')->with($returnData);
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

                foreach ($robot_ids as $v) {
                    if($data['id']>0){
                        $where['id'] = $data['id'];
                    }else{
                        $where['id'] = 'k';
                    }
                    if(!isset($data['relation'])){
                        $data['relation'] = $data['problem'];
                    }
                    $where['robot_id'] = $d['robot_id'] = $v;
                    $d['problem'] = json_encode($data['problem'],JSON_UNESCAPED_UNICODE);
                    $d['answer'] = $msg;
                    $d['user_id'] = $this->authUserInfo()->id;
                    $d['type'] = $data['control'];
                    $d['relation'] = json_encode($data['relation'],JSON_UNESCAPED_UNICODE);
                    $reply = FriendReply::where($where)->first();
                    if(!$reply){
                        FriendReply::insert($d);
                    }else{
                        FriendReply::where($where)->update($d);
                    }

                }

            }

            Flash::success('更新成功');
            return redirect(url('friend/autoreply'));
        }
        $problem = $relation = [];
        if($request->robot_id>0){
            //页面显示
            $reply = [];
            if($request->id){
                $where[] = ['id','=',$request->id];
//                $where[] = ['robot_id','=',$request->robot_id];
                $reply = FriendReply::where($where)->first();
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
                $reply->msg = '';
            }
        }
        $originProblem = FriendReply::where($this->condition())->get();

        foreach ($originProblem as &$vs){
            $arr  = json_decode($vs->problem,1);
            $vs->origin_problem = $arr[0];
        }
        if(empty($problem)){
            $problem = array_fill(0,4,'');
        }
        if(empty($relation)){
            $relation = array_fill(0,4,'');
        }
        $returnData = ['originProblem'=>$originProblem,'request'=>$request,'problem'=>$problem,'relation'=>$relation,'reply'=>$reply];
        return view('friend.add_reply')->with($returnData);
    }

    /**
     * Display a listing of the Game.
     *
     * @param Request $request
     * @return Response
     */
    public function response(Request $request){
        $robot = Robot::where($this->condition())->pluck('nickname','id');
        $request = $request->all();
//        $wx_group = ['微信群1','微信群2','微信群3','微信群4'];//todo;
        $res = isset($_SESSION['robot_ids_3'])?$_SESSION['robot_ids_3']:[] ;
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
        $wxData = Friend::where('robot_id',$id)->where('type',3)->where($this->condition())->first();
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
        $returnData = ['robot'=>$robot,'group'=>$tmp,'request'=>$request,'request_url'=>url()->current(),'select'=>$tg,'wxdata'=>$wxData];
        return view('friend.response')->with('robots', [])->with($returnData);
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
            $key = 'robot_ids_1';//自动加载成员成为好友
        }
        if($request['save_type']==2){
            $key = 'robot_ids_2';
        }
        if($request['save_type']==3){
            $key = 'robot_ids_3';//自动应答
        }
        if($request['save_type']==4){
            $key = 'robot_ids_4';//私聊自动回复
        }
        if($request['save_type']==5){
            $key = 'robot_ids_5';//加附近好友
        }
        if($request['save_type']==6){
            $key = 'robot_ids_6';//批量搜索加好友
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
    public function nearby(Request $request){

        $robot = Robot::where($this->condition())->pluck('nickname','id');
        $res = isset($_SESSION['robot_ids_4'])?$_SESSION['robot_ids_4']:[] ;
        $tg = [];
        $id= 0;
        if($res){
            foreach($res as &$v){
                $tg[$v] = $v;
            }
        }
        if($res){
            $id = current($res);
        }
        $wxData = Friend::where('robot_id',$id)->where('type',4)->where($this->condition())->first();
        if($wxData){
            $wxData_arr = json_decode($wxData->config,true);
            $wxData->start = $wxData_arr['start'];
            $wxData->end = $wxData_arr['end'];
            $wxData->msg = $wxData_arr['msg'];
            $wxData->province = isset($wxData_arr['province'])?$wxData_arr['province']:'';
            $wxData->city = $wxData_arr['site'];
            $wxData->sex = isset($wxData_arr['sex'])?$wxData_arr['sex']:0;
            $wxData->interval = isset($wxData_arr['interval'])?$wxData_arr['interval']:0;
        }else{
            $wxData = new \stdClass();
            $wxData->start = 0;
            $wxData->end = 0;
            $wxData->msg = '';
            $wxData->province = 0;
            $wxData->sex = 0;
            $wxData->status = 0;
            $wxData->interval = 0;
        }
        //获取省
        $province = DB::table('area')->where(['parent_id'=>1])->get();
        $tmp = [];
        if(isset($wxData->province)&&$wxData->province){
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

        $returnData = ['robot'=>$robot,'request'=>$request,'request_url'=>url()->current(),'select'=>$tg,'wxdata'=>$wxData,'province'=>$province,'originCity'=>$tmp];
        return view('friend.nearby')->with($returnData);
    }

    /**
     * Display a listing of the Game.
     *
     * @param Request $request
     * @return Response
     */
    public function batchSearch(Request $request){
        $robot = Robot::where($this->condition())->pluck('nickname','id');
        $res = isset($_SESSION['robot_ids_5'])?$_SESSION['robot_ids_5']:[] ;
        $tg = [];
        $id= 0;
        if($res){
            foreach($res as &$v){
                $tg[$v] = $v;
            }
        }
        if($res){
            $id = current($res);
        }
        $wxData = Friend::where('robot_id',$id)->where($this->condition())->where('type',5)->first();
        if($wxData){
            $wxData_arr = json_decode($wxData->config,true);
            $wxData->start = $wxData_arr['start'];
            $wxData->end = $wxData_arr['end'];
            $wxData->sex = isset($wxData_arr['sex'])?$wxData_arr['sex']:0;
            $wxData->keyword = isset($wxData_arr['search'])?$wxData_arr['search']:0;
            $wxData->verify = isset($wxData_arr['msg'])?$wxData_arr['msg']:0;
            $wxData->interval = isset($wxData_arr['interval'])?$wxData_arr['interval']:0;
        }else{
            $wxData = new \stdClass();
            $wxData->start = 0;
            $wxData->end = 0;
            $wxData->msg = '';
            $wxData->sex = 0;
            $wxData->keyword = '';
            $wxData->verify = '';
            $wxData->interval = 0;
        }

        $returnData = ['robot'=>$robot,'request'=>$request,'request_url'=>url()->current(),'select'=>$tg,'wxdata'=>$wxData];
        return view('friend.batch')->with($returnData);
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
        $reply = FriendReply::where('id',$id)->first();
        if (empty($reply)) {
            Flash::error('记录不存在');

            return redirect('friend/autoreply');
        }

        FriendReply::where('id',$id)->delete();

        Flash::success('reply deleted successfully.');

        return redirect('friend/autoreply');
    }

}
