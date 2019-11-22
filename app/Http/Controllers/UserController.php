<?php

namespace App\Http\Controllers;

use App\Common\ErrorCode;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\User;
use Flash;
use DB;
use Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends BaseController
{
    /*
     * construct
     */
   public function __construct(Request $request)
   {

       $controller = $request->route()->getAction();
       $controllerArr = explode('@',$controller['controller']);
       list($c,$a) = $controllerArr;
       //将action放入缓存
       $_SESSION['CurrentAction'] = $a;

   }
   /*
    * @desc userList
    */
   public function user(Request $request){
       $role = Auth::user()->role;
        $where[] = ['role','<>','-9'];//不查询超级管理员
       if(!empty($request->name)){
           $where[] = ['name','like','%'.$request->name.'%'];
       }
        $list = DB::table('users')->where($where)->paginate(20);
        $list->appends(array(
            'page' => $request->page,
            'name' => $request->name,
        ));
        return view('users.index')->with('list', $list) ->with('role', $role);
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
        $where['id'] = $id;
        $user = User::where($where)->first();

        if (empty($user)) {
            Flash::error('账号未找到');

            return redirect(url('user/user'));
        }

        User::where($where)->delete();

        Flash::success('user deleted successfully.');

        return redirect(url('user/user'));
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
        return view('users.create');
    }

    /**
     * Store a newly created Game in storage.
     *
     * @param CreateGameRequest $request
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        if (empty($request['name'])) {
            Flash::error('账号不能为空');
            return view('users.create');
        }
        if($request['password']!=$request['check_password']){
            Flash::error('两次密码不一致');
            return view('users.create');
        }
        if(empty($request['packnum'])){
            Flash::error('最大上传包的数量不能为空');
            return view('users.create');
        }
        if(empty($request['udidnum'])){
            Flash::error('最大上传包的数量不能为空');
            return view('users.create');
        }
        $data = [
            'name'=>$input['name'],
            'password'=>bcrypt($input['check_password']),
            'origin_password'=>$input['check_password'],
            'packnum'=>$input['packnum'],
            'udidnum'=>$input['udidnum'],
            'role'=>$input['role'],
            'email'=>'example@example.com'.'-'.time(),
            'created_at'=>date('Y-m-d H:i:s')
        ];
        User::insert($data);

        Flash::success('账号创建成功');

        return redirect(url('user/user'));
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
        $where['id'] = $id;
        $user = User::where($where)->first();
        if (empty($user)) {
            Flash::error('机器人未找到');
            return redirect(url('user/user'));
        }

        return view('users.edit')->with('user', $user);
    }

    /**
     * Update the specified Game in storage.
     *
     * @param  int              $id
     * @param UpdateGameRequest $request
     *
     * @return Response
     */
    public function update($id, Request $request)
    {
        $where['id'] = $id;
        $user = User::where($where)->first();
        if (empty($user)) {
            Flash::error('机器人未找到');

            return redirect(url('user/user'));
        }
        $data = [
            'name'=>$request->name,
            'password'=>bcrypt($request->origin_password),
            'origin_password'=>$request->origin_password,
            'packnum'=>$request->packnum,
            'udidnum'=>$request->udidnum,
            'role'=>$request->role,

            'created_at'=>date('Y-m-d H:i:s')
        ];

        $robot = User::where($where)->update($data);

        Flash::success('账户更新成功');

        return redirect(url('user/user'));
    }


}
