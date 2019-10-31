<?php
/**
 * Created by IntelliJ IDEA.
 * User: new
 * Date: 2018-07-12
 * Time: 17:05
 */

namespace App\Common;


class ErrorCode
{
    //可以对状态信息进行归类，如1--为用户端错误，2--位服务器端错误， 。。。。。。。
    static $token_missing = ['code'=>10000,'message'=>'token不存在'];

    static $mulu_missing = ['code'=>20000,'message'=>'上传目录不存在'];
    static $forbidden_missing = ['code'=>20001,'message'=>'上传目录没有写权限'];
    static $_missing = ['code'=>20002,'message'=>'There isnt key Filedata'];

}