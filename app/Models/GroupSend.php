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
class GroupSend extends Model
{

    public $table = 'robot_config_group_send';
    public $timestamps = false;
    
    public $fillable = [
        'id',
        'user_id',
        'robot_id',
        'msg',
        'group',
        'is_repeat',
        'status',
        'week',
        'time',
        'type',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'robot_id' => 'integer',
        'user_id' => 'integer',
        'msg' => 'string',
        'group' => 'string',
        'is_repeat' => 'integer',
        'status' => 'integer',
        'week' => 'string',
        'time' => 'integer',
        'type' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
