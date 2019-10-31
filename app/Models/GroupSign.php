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
class GroupSign extends Model
{

    public $table = 'robot_config_group_sign';
    public $timestamps = false;
    
    public $fillable = [
        'id',
        'robot_id',
        'user_id',
        'msg',
        'group',
        'status',
        'start_time',
        'end_time',
        'start_date',
        'end_date',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_id' => 'integer',
        'robot_id' => 'integer',
        'msg' => 'string',
        'group' => 'string',
        'status' => 'integer',
        'start_time' => 'string',
        'end_time' => 'string',
        'start_date' => 'string',
        'end_date' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [

    ];

    
}
