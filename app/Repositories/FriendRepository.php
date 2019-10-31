<?php

namespace App\Repositories;

use App\Models\Friend;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class FriendRepository
 * @package App\Repositories
 * @version July 16, 2018, 2:30 am UTC
 *
 * @method Friend findWithoutFail($id, $columns = ['*'])
 * @method Friend find($id, $columns = ['*'])
 * @method Friend first($columns = ['*'])
*/
class FriendRepository extends BaseRepository
{
    use RepositoryTriat;
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'id',
        'robot_id',
        'type',
        'status',
        'config',
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Friend::class;
    }

}
