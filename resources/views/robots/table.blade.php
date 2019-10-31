<style>
    img{
        width: 60px;
        height: 60px;}
</style>
<table class="table table-responsive" id="games-table">
    <thead>
        <tr>
        <th>ID</th>
        <th>设备号</th>
        <th>微信昵称</th>
        <th>性别</th>
        <th>微信账号</th>
        <th>头像</th>
        <th>登录状态</th>
        <th>运行状态</th>

        <th>加好友</th>
        <th>发朋友圈</th>
        <th>群管理</th>
        <th>机器人配置</th>

        <th>好友数量</th>
        <th>朋友圈数量</th>
        <th>管理群数量</th>

        <th>最后登录时间</th>
        <th>最后注销时间</th>
            <th colspan="3">操作</th>
        </tr>
    </thead>
    <tbody>
    @foreach($robots as $robot)
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
            <td>{!! $robot->run_status == 0 ? '异常' : '正常' !!}</td>

            <td>{!! $robot->friend_config == 1 ? '已启用' : '未启用' !!}</td>
            <td>{!! $robot->circle_config == 1 ? '已启用' : '未启用' !!}</td>
            <td>{!! $robot->group_config == 1 ? '已启用' : '未启用' !!}</td>
            <td>{!! $robot->robot_config == 1 ? '已启用' : '已启用' !!}</td>

            <td>{!! $robot->friend_num !!}</td>
            <td>{!! $robot->circle_num !!}</td>
            <td>{!! $robot->group_num !!}</td>

            <td>{!! $robot->last_login !!}</td>
            <td>{!! $robot->last_logout !!}</td>
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