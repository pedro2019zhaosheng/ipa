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
        <th>创建时间</th>

            <th colspan="3">操作</th>
        </tr>
    </thead>
    <tbody>
    @foreach($apple as $robot)
        <tr>
            <td>{!! $robot->id !!}</td>
            <td>{!! $robot->device_id !!}</td>
            <td>{!! $robot->nickname !!}</td>
            <td>
                @if($robot->sex==0)
                    未知
                @endif
                @if($robot->sex==1)
                    男
                @endif
                @if($robot->sex==2)
                    女
                @endif

            </td>
            <td>{!! $robot->wx_account !!}</td>
            <td><img class='showimage' width="60" height="60" src="{!! $robot->img !!}"></td>


            <td>{!! $robot->login_status == 0 ? '离线' : '在线' !!}</td>
            
            <td>
                {!! Form::open(['route' => ['robots.destroy', $robot->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('robots.edit', [$robot->id]) !!}" class='btn btn-default btn-xs'>修改</a>
                    {!! Form::button('删除', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                    @if($robot->run_status==1)
                        <a style="color:green" href="/robot/updateRobot?id={{$robot->id}}&run_status=0" class='btn btn-default btn-xs'>停用</a>
                    @endif
                    @if($robot->run_status==0)
                        <a style="color:green" href="/robot/updateRobot?id={{$robot->id}}&run_status=1" class='btn btn-default btn-xs'>启用</a>
                    @endif
                </div>
                {!! Form::close() !!}

            </td>
        </tr>
    @endforeach

    </tbody>

</table>