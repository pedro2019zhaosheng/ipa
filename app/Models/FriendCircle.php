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
class FriendCircle extends Model
{

    public $table = 'robot_config_friends_circle';
    public $timestamps = false;
    
    public $fillable = [
        'id',
        'robot_id',
        'type',
        'status',
        'config',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'robot_id' => 'integer',
        'type' => 'integer',
        'status' => 'integer',
        'config' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
