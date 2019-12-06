<?php
/**
 * Created by 傲慢与偏见.
 * OSUser: D-L
 * Date: 2017/10/22
 * Time: 21:09
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Model;


class Order extends Model
{
    protected $table = "order";
    protected $primaryKey = 'id';
}