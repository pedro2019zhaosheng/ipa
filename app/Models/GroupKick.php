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
class GroupKick extends Model
{

    public $table = 'robot_config_group_kick';
    public $timestamps = false;
    
    public $fillable = [
        'id',
        'nickname',
        'group_id',
        'user_id',
        'kick_date',
        'kick_method',
        'status',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'nickname' => 'string',
        'group_id' => 'integer',
        'user_id' => 'integer',
        'kick_date' => 'string',
        'kick_method' => 'integer',
        'status' => 'integer',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
