<style>
    img{
        width: 60px;
        height: 60px;}
</style>
<table class="table table-responsive" id="games-table">
    <thead>
        <tr>
        <th>ID</th>
        <th>Apple开发者账号</th>
        <th>剩余设备数量</th>
        <th>P8密钥</th>
        <th>P12</th>
        <th>证书ID</th>
        <th>是否是推送证书</th>
        <th>创建时间</th>

            <th colspan="3">操作</th>
        </tr>
    </thead>
    <tbody>
    @foreach($apple as $data)
        <tr>
            <td>{!! $data->id !!}</td>
            <td>{!! $data->account !!}</td>
            <td>{!! $data->udid_num !!}</td>
            <td>
              {!! $data->secret_key !!}

            </td>
            <td>{!! $data->p12_url !!}</td>


            <td>{!! $data->certificate_id !!}</td>
            <td>{!! $data->is_push==0?'否':'是' !!}</td>
            <td>{!! $data->created_at !!}</td>
            @if($data->user_id == $user_id || $role < 0)
            <td>
                {!! Form::open(['route' => ['apple.destroy', $data->id], 'method' => 'get']) !!}
                <div class='btn-group'>
                    <a href="{!! route('apple.edit', [$data->id]) !!}" class='btn btn-default btn-xs'>修改</a>
                    {!! Form::button('删除', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                   
                </div>
                {!! Form::close() !!}

            </td>
            @endif
        </tr>
    @endforeach

    </tbody>

</table>