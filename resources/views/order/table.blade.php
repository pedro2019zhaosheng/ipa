<style>
    img{
        width: 60px;
        height: 60px;}
</style>
<table class="table table-responsive" id="games-table">
    <thead>
        <tr>
        <th>ID</th>
        <th>订单号</th>
        <th>订单金额</th>
        <th>实际支付金额</th>
        <th>购买人用户名</th>
        <th>购买人手机号</th>
        <th>支付方式</th>
        <th>第三方订单号</th>
        <th>订单类型</th>
        <th>套餐</th>
        <th>套餐描述</th>
        <th>订单状态</th>
        <th>创建时间</th>
        <th>实际支付时间</th>

            <th colspan="3">操作</th>
        </tr>
    </thead>
    <tbody>
    @foreach($order as $data)
        <tr>
            <td>{!! $data->id !!}</td>
            <td>{!! $data->order_id !!}</td>
            <td>{!! $data->amount !!}</td>
            <td>
              {!! $data->pay_amount !!}
            </td>
             <td>{!! $data->nick_name !!}</td>
            <td>{!! $data->mobile !!}</td>
            <td>{!! $data->payment_type==1?'支付宝':'其它' !!}</td>
            <td>
              {!! $data->order_no !!}
            </td>
              <td>
              {!! $data->order_type==1?'分发下载订单':'超级签名订单' !!}
            </td>
            <td>
                {!! $data->product_id !!}
            </td>
            <td>
                {!! isset($data->product_desc)?$data->product_desc:'' !!}
            </td>
            <td>
                <span style="color: red"> {!! $data->status==1?'完成':'未完成' !!}</span>
            </td>
              <td>
                  {!! $data->created_at !!}
            </td>
            <td>
                {!! $data->pay_time !!}
            </td>

            @if($data->user_id == $user_id || $role < 0)
            <td>
                {!! Form::open(['route' => ['order.destroy', $data->id], 'method' => 'get']) !!}
                <div class='btn-group'>
{{--                    <a href="{!! url('package/edit?id='.$data->id) !!}" class='btn btn-default btn-xs'>修改</a>--}}
                    {!! Form::button('删除', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                   
                </div>
                {!! Form::close() !!}

            </td>
            @endif
        </tr>
    @endforeach

    </tbody>

</table>
