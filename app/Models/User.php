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
class User extends Model
{

    public $table = 'users';
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';



    public $fillable = [
        'id',
        'role',
        'name',
        'password',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'role' => 'integer',
        'name' => 'string',
        'password' => 'string',
        'created_at' => 'string',
        'updated_at' => 'string',
    ];

    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        
    ];

    
}
