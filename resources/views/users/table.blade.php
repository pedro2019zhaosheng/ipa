<style>
    img{
        width: 60px;
        height: 60px;}
</style>
<table class="table table-responsive" id="games-table">
    <thead>
        <tr>
        <th>ID</th>
        <th>账号</th>
        <th>角色</th>

        <th>最后登录时间</th>
        <th>最后注销时间</th>
            <th colspan="3">操作</th>
        </tr>
    </thead>
    <tbody>
    @foreach($list as $user)
        <tr>
            <td>{!! $user->id !!}</td>
            <td>{!! $user->name !!}</td>
            {{--<td>{!! $user->role !!}</td>--}}
             @if($user->role == -9)<td>超级管理员</td>@endif
             @if($user->role == 1) <td>普通用户（自己提供开发者账号）</td>@endif
             @if($user->role == 2) <td>普通用户（我们提供开发者账号）</td>@endif
             @if($user->role == 3) <td>开发者用户</td>@endif

            <td>{!! $user->created_at !!}</td>
            <td>{!! $user->updated_at !!}</td>
            <td>
                {!! Form::open(['route' => ['users.destroy', $user->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('users.edit', [$user->id]) !!}" class='btn btn-default btn-xs'>修改</a>
                    {!! Form::button('删除', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}

            </td>
        </tr>
    @endforeach

    </tbody>

</table>