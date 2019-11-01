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


    public function uploadFile($request){
        $file = $request->file('file');
        $filename = '';
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
            $bool = $request->file('file')->move(public_path().'/storage/', $filename);
            //$bool = Storage::disk('public')->put($filename,file_get_contents($realPath));
            //判断是否上传成功
            $filename = 'http://'.$_SERVER['HTTP_HOST'].'/storage/'.$filename;
        }
        return $filename;
    }
}