<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateRobotRequest;
use App\Http\Requests\UpdateRobotRequest;
use App\Models\Robot;
use App\Repositories\RobotRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\Order;
use App\Models\Device;
use DB;

class OrderController extends AppBaseController
{
    /** @var  robotRepository */
    private $robotRepository;

    public function __construct(RobotRepository $robotRepo,Request $request)
    {

        $this->robotRepository = $robotRepo;
        $this->getCurrentAction($request);

    }

    /**
     * Display a listing of the Game.
     *
     * @param Request $request
     * @return Response
     */
    public function order(Request $request)
    {
        $role = Auth::user()->role;
        $keyword  = $request->keyword;
        $order = DB::table('order')->where('is_del','=',0)
            ->where(function($query) use($keyword){
                $query->where('order_id', 'like', '%'.$keyword . '%')
                    ->orWhere(function($query) use($keyword){
                        $query->where('order_no', 'like','%'. $keyword . '%');
                    })->orWhere(function($query) use($keyword){
                        $query->where('nick_name', 'like', '%'.$keyword . '%');
                    })->orWhere(function($query) use($keyword){
                        $query->where('mobile', 'like', '%'.$keyword . '%');
                    });;
            })->paginate(10);
        $order->appends(array(
            'page' => $request->page,
            'name' => $request->name,
        ));
        $server = $_SERVER;
//        $domain = $server['REQUEST_SCHEME'].'://'.$server['HTTP_HOST'];
        $user_id = Auth::user()->id;
        return view('order.index',compact('order','domain','role','user_id'));
    }

    /**
     * Remove the specified Game from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $package = Order::where(['id'=>$id])->first();

        if (empty($package)) {
            Flash::error('无效订单');

            return redirect(url('order/order'));
        }

        Order::where(['id'=>$id])->update(['is_del'=>1]);

        Flash::success('order deleted successfully.');

        return redirect(url('order/order'));
    }
}
