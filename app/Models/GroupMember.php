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
class GroupMember extends Model
{

    public $table = 'robot_config_group_member';
    public $timestamps = false;
    
    public $fillable = [
        'id',
        'user_id',
        'robot_id',
        'group_id',
        'nickanme',
        'off_days',
        'join_group_date',
        'last_msg_date',
        'invite_num',
        'invite_retain_num',
        'msg_num',
        'complaints_num',
        'is_kick',
        'is_block',
        'is_admin',
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
        'group_id' => 'integer',
        'nickanme' => 'string',
        'off_days' => 'integer',
        'join_group_date' => 'string',
        'last_msg_date' => 'string',
        'invite_num' => 'integer',
        'invite_retain_num' => 'integer',
        'msg_num' => 'integer',
        'complaints_num' => 'integer',
        'is_kick' => 'integer',
        'is_block' => 'integer',
        'is_admin' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
