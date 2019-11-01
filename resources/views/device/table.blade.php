<style>
    img{
        width: 60px;
        height: 60px;}
</style>
<table class="table table-responsive" id="games-table">
    <thead>
        <tr>
        <th>ID</th>
        <th>包ID</th>
        <th>苹果账号ID</th>
        <th>设备ID</th>
        <th>创建时间</th>

            <th colspan="3">操作</th>
        </tr>
    </thead>
    <tbody>
    @foreach($device as $data)
        <tr>
            <td>{!! $data->id !!}</td>
            <td>{!! $data->package_id !!}</td>
            <td>{!! $data->apple_id !!}</td>
            <td>
              {!! $data->udid !!}
            </td>

            <td>{!! $data->created_at !!}</td>
            
            <td>
                {!! Form::open(['route' => ['device.destroy', $data->id], 'method' => 'get']) !!}
                <div class='btn-group'>
                    <a href="{!! route('device.edit', [$data->id]) !!}" class='btn btn-default btn-xs'>修改</a>
                    {!! Form::button('删除', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                   
                </div>
                {!! Form::close() !!}

            </td>
        </tr>
    @endforeach

    </tbody>

</table>