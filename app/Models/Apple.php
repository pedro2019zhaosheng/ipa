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
class Apple extends Model
{

    public $table = 'apple';
    public $timestamps = false;
    
    public $fillable = [
        'id',
        'account',
        'secret_key',
        'p12_url',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'account' => 'string',
        'secret_key' => 'string',
        'p12_url' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
