<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRobotRequest;
use App\Http\Requests\UpdateRobotRequest;
use App\Models\Robot;
use App\Repositories\RobotRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\Friend;
use App\Models\Device;

class DeviceController extends AppBaseController
{
    /** @var  robotRepository */
    private $robotRepository;

    public function __construct(RobotRepository $robotRepo,Request $request)
    {
        $this->robotRepository = $robotRepo;
        $this->getCurrentAction($request);
    }

    /**
     * Display a listing of the Game.
     *
     * @param Request $request
     * @return Response
     */
    public function device(Request $request)
    {
        $role = Auth::user()->role;
        $subWhere = [];
        if($role>0){
            $subWhere = ['user_id'=>Auth::user()->id];
        }
        $where = [];
        if(!empty($request->name)){
            $where[] = ['udid','like','%'.$request->name.'%'];
        }
        $robots = Device::where($where)->where($subWhere)->paginate(10);
        $robots->appends(array(
            'page' => $request->page,
            'name' => $request->name,
        ));
        return view('device.index')
            ->with('device', $robots);
    }

    /**
     * Show the form for creating a new Game.
     *
     * @return Response
     */
    public function create()
    {
        //todo 二维码
        $qrcode = "https://tvax4.sinaimg.cn/crop.0.0.1125.1125.180/49282834ly8fvi8ifbnz4j20v90v9tau.jpg?KID=imgbed,tva&Expires=1567774272&ssig=aGCljOl9Zz";
        $qrcode= "http://47.91.251.232:8892/qr_image.png";
        return view('device.create')->with('qrcode',$qrcode);
    }

    /**
     * Store a newly created Game in storage.
     *
     * @param CreateGameRequest $request
     *
     * @return Response
     */
    public function store(CreateRobotRequest $request)
    {

        $data = $request->all();
      
        if(!isset($data['udid'])){
            Flash::error("苹果设备udid不能为空");
            return redirect(url('/device/create'));
        }
      
        $device = [
                'udid'=>$data['udid'],
            ];
        if($data['id']>0){
            Device::where(['id'=>$data['id']])->update($device);
        }else{
            Device::insert($device);
        }
       
      
        Flash::success('设备创建成功');

        return redirect(url('device/device'));
    }

    /**
     * Display the specified Game.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $game = $this->robotRepository->findWithoutFail($id);

        if (empty($game)) {

            return redirect(route('robots.index'));
        }

        return view('robots.show')->with('game', $game);
    }

    /**
     * Show the form for editing the specified Game.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $device = Device::where(['id'=>$id])->first();

        if (empty($device)) {
            Flash::error('苹果设备号未找到');

            return redirect(route('device.index'));
        }
        return view('device.create')->with('device', $device);
    }

    /**
     * Update the specified Game in storage.
     *
     * @param  int              $id
     * @param UpdateGameRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateRobotRequest $request)
    {
        $robot = $this->robotRepository->findWithoutFail($id);

        if (empty($robot)) {
            Flash::error('机器人没找到');

            return redirect(route('robots.index'));
        }

        $robot = $this->robotRepository->update($request->all(), $id);

        Flash::success('机器人更新成功');

        return redirect(route('robots.index'));
    }

    /**
     * Remove the specified Game from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $device = Device::where(['id'=>$id])->first();

        if (empty($device)) {
            Flash::error('apple设备未找到');

            return redirect(url('device/device'));
        }

        Device::where(['id'=>$id])->delete();

        Flash::success('Device No deleted successfully.');

        return redirect(url('device/device'));
    }

//    public function search(Request $request)
//    {
//        $page = $request->get('page');
//        dump($page);die;
//        $where = [];
//        if(!empty($request->name)){
//            $where[] = ['name','like','%'.$request->name.'%'];
//        }
//        if(!empty($request->type)){
//            $where[] = ['type','=',$request->type];
//        }
//        $robots = $this->robotRepository->findAndPaginate($where);
////        $this->robotRepository->paginate(1);
//        dump($robots);die;
////        $robots->appends(array(
////            'search' => $search,
////            'customer_type' => $customer_type,
////            'perPage' => $perPage,
////        ));
//
//        return view('robots.index')
//            ->with('robots', $robots)->with('gameTypes',$this->gameTypes);
//
//    }

    /**
     * Remove the specified Game from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function updateRobot(Request $request)
    {
        $data = $request->all();
        $game = $this->robotRepository->findWithoutFail($data['id']);

        if (empty($game)) {
            Flash::error('机器人未找到');

            return redirect(route('robots.index'));
        }

        $this->robotRepository->update(['run_status'=>$data['run_status']],$data['id']);

        Flash::success('update successfully.');

        return redirect(route('robots.index'));
    }

}
