<?php

namespace App\Repositories;

use App\Models\Account;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AccountRepository
 * @package App\Repositories
 * @version July 16, 2018, 5:08 am UTC
 *
 * @method Account findWithoutFail($id, $columns = ['*'])
 * @method Account find($id, $columns = ['*'])
 * @method Account first($columns = ['*'])
*/
class AccountRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'type',
        'platform',
        'deviceno',
        'version',
        'model',
        'mobile',
        'password',
        'token',
        'social_id',
        'avatar',
        'nickname',
        'reg_time',
        'login_time',
        'ip',
        'bind_mobile',
        'id_card',
        'lng',
        'lat'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Account::class;
    }
}
