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
use App\Models\Package;
use App\Models\Device;
use DB;

class PackageController extends AppBaseController
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
    public function package(Request $request)
    {
        $role = Auth::user()->role;
        $subWhere = [];
        if($role>0){
            $subWhere = ['user_id'=>Auth::user()->id];
        }
        $where = [];
        if(!empty($request->name)){
            $where[] = ['name','like','%'.$request->name.'%'];
        }
        $package = Package::where($where)->where($subWhere)->where('pid','=',0)->paginate(10);
        $package->appends(array(
            'page' => $request->page,
            'name' => $request->name,
        ));
        $server = $_SERVER;
        $domain = $server['REQUEST_SCHEME'].'://'.$server['HTTP_HOST'];
	$domain = $server['SCHEME_URL'];
	print_r($domain);
        $user_id = Auth::user()->id;
        return view('package.index',compact('package','domain','role','user_id'));
    }

    /**
     * Show the form for creating a new Game.
     *
     * @return Response
     */
    public function create(Request $request)
    {
        $role = Auth::user()->role;
        //todo 二维码
        $qrcode = "https://tvax4.sinaimg.cn/crop.0.0.1125.1125.180/49282834ly8fvi8ifbnz4j20v90v9tau.jpg?KID=imgbed,tva&Expires=1567774272&ssig=aGCljOl9Zz";
        $qrcode= "http://47.91.251.232:8892/qr_image.png";
        $pk = DB::table('package')->where(['id'=>$request->package_id])->first();
        $package = new \stdClass();
        if($pk){
            $package->apple_id = $pk->apple_id;
        }
        return view('package.create')->with('qrcode',$qrcode)->with('role', $role)->with('request',$request)->with('package',$package);
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
        if(!isset($data['introduction'])){
            Flash::error("简介不能为空");
            return redirect(url('/package/create'));
        }
       
        $filename = $this->uploadFile($request);
        $icon = $this->uploadIcon($request);
        $input = $request->all();
        if(!$filename){
            $filename = $data['ipa_url'];
        }
        if(!$icon){
            $icon = $data['icon_url'];
        }
        $package = [
                'introduction'=>$data['introduction'],
                'ipa_url'=>$filename,
                'buddle_id'=>$data['buddle_id'],
                'apple_id'=>$data['apple_id'],
                'icon'=>$icon,
                'name'=>$data['name'],
                'is_push'=>isset($data['is_push'])?$data['is_push']:0,
                'version'=>$data['version'],
                'user_id'=>Auth::user()->id,
                'is_binding'=>$data['is_binding'],
                'created_at'=>date('Y-m-d H:i:s')
            ];
        if(isset($data['package_id'])&&isset($data['type'])){
            $package['pid'] = $data['package_id'];
            $_SESSION['packageId'] = $package['pid'];//session package_id
        }
        if($data['id']>0){
            package::where(['id'=>$data['id']])->update($package);
        }else{
            Package::insert($package);
        }
       
      
        Flash::success('苹果IPA包创建成功');
        if(isset($data['type'])&&$data['type']==2){
            return redirect(url('package/sonPackageList?package_id='.$package['pid']));
        }else{
            return redirect(url('package/package'));
        }

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
    public function edit(Request $request)
    {
        $role = Auth::user()->role;
        $package = Package::where(['id'=>$request->id])->first();
        if (empty($package)) {
            Flash::error('安装包未找到');

            return redirect(route('package.index'));
        }
        return view('package.create')->with('package', $package)->with('role', $role)->with('request',$request);
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
        $package = Package::where(['id'=>$id])->first();

        if (empty($package)) {
            Flash::error('Ipa包未找到');

            return redirect(url('package/package'));
        }

        Package::where(['id'=>$id])->delete();

        Flash::success('Package No deleted successfully.');

        return redirect(url('package/package'));
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


    public function sonPackageList(Request $request){
        $_SESSION['packageId'] = $request->package_id;
        $role = Auth::user()->role;
        $subWhere = [];
        if($role>0){
            $subWhere = ['user_id'=>Auth::user()->id];
        }
        $pwhere = [];
        if(isset($_SESSION['packageId'])){
            $pwhere  =['pid'=>$_SESSION['packageId']];
        }
        $where = [];
        if(!empty($request->name)){
            $where[] = ['name','like','%'.$request->name.'%'];
        }
        $package = Package::where($where)->where($subWhere)->where($pwhere)->paginate(10);
        $package->appends(array(
            'page' => $request->page,
            'name' => $request->name,
        ));
        $server = $_SERVER;
        $domain = $server['REQUEST_SCHEME'].'://'.$server['HTTP_HOST'];
        $user_id = Auth::user()->id;
        return view('package.sonPackageList',compact('package','domain','role','user_id','request'));
    }
}
