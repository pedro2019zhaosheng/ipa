<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Model;

/**
 * Class Friend
 * @package App\Models
 * @version July 16, 2018, 2:30 am UTC
 *
 * @property \Illuminate\Database\Eloquent\Collection permissionRole
 * @property \Illuminate\Database\Eloquent\Collection userRole
 * @property integer id
 * @property string robot_id
 * @property string type
 * @property integer status
 * @property string config
 */
class GroupSignLog extends Model
{

    public $table = 'robot_group_sign_log';
    public $timestamps = false;
    
    public $fillable = [
        'id',
        'sign_id',
        'wx_account',
        'sign_date',
        'sign_time',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'sign_id' => 'integer',
        'wx_account' => 'string',
        'sign_date' => 'string',
        'sign_time' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
