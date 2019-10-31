<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Model;

/**
 * Class Robot
 * @package App\Models
 * @version July 16, 2018, 2:30 am UTC
 *
 * @property \Illuminate\Database\Eloquent\Collection permissionRole
 * @property \Illuminate\Database\Eloquent\Collection userRole
 * @property integer id
 * @property string nickname
 * @property string img
 * @property integer sex
 * @property string constellation
 * @property integer login_status
 * @property integer run_status
 * @property string last_login
 * @property string last_logout
 */
class Robot extends Model
{

    public $table = 'robot';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'id',
        'user_id',
        'nickname',
        'img',
        'sex',
        'constellation',
        'login_status',
        'run_status',
        'last_login',
        'last_logout',
        'device_id',
        'account'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id'=>'integer',
        'nickname' => 'string',
        'img' => 'string',
        'sex' => 'integer',
        'constellation' => 'string',
        'login_status' => 'integer',
        'run_status' => 'integer',
        'last_login' => 'string',
        'last_logout' => 'string',
        'device_id' => 'string',
        'account' => 'string'
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
