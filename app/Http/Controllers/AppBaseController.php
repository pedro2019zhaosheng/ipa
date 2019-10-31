<?php
/**
 * Created by IntelliJ IDEA.
 * User: new
 * Date: 2018-07-16
 * Time: 10:18
 */

namespace App\Http\Controllers;


use App\Http\Requests\Request;
use Illuminate\Support\Facades\Auth;

class AppBaseController extends Controller
{
    /*
     * @desc get auth userInfo
     */
    public function authUserInfo(){
        return Auth::user();
    }

    public function condition(){
        $authInfo = Auth::user();
        if($authInfo->role==1){
            $where[] = ['id','>',0];
        }else{
            $where['user_id'] = $authInfo->id;
        }
        return $where;
    }

    public function getCurrentAction($request){
        $controller = $request->route()->getAction();
        $controllerArr = explode('@',$controller['controller']);
        list($c,$a) = $controllerArr;
        //将action放入缓存
        $_SESSION['CurrentAction'] = $a;
        return $a;
    }
}