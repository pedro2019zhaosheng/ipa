<?php

namespace App\Repositories;

use App\Models\Robot;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class RobotRepository
 * @package App\Repositories
 * @version July 16, 2018, 2:30 am UTC
 *
 * @method Robot findWithoutFail($id, $columns = ['*'])
 * @method Robot find($id, $columns = ['*'])
 * @method Robot first($columns = ['*'])
*/
class RobotRepository extends BaseRepository
{
    use RepositoryTriat;
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id',
        'user_id',
        'nickname',
        'img',
        'sex',
        'constellation',
        'login_status',
        'run_status',
        'last_login',
        'last_logout'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Robot::class;
    }

//    public function findAndPaginate($param){
//        if(empty($param)){
//            return null;
//        }
//        return Robot::where($param)->paginate(1);
//    }
}
