<?php
/**
 * Created by IntelliJ IDEA.
 * User: new
 * Date: 2018-07-18
 * Time: 10:16
 */

namespace App\Repositories;



trait RepositoryTriat
{
    public function findAndPaginate($table,$where,$limit=1)
    {
        if (empty($where)) {
            return null;
        }
        return $table::where($where)->paginate($limit);
    }

}