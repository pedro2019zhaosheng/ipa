<!-- Name Field -->
<div class="form-group col-sm-12">
    {!! Form::label('name', '用户名:') !!}
    {!! Form::text('name', null, ['class' => 'form-control']) !!}
    {!! Form::label('password', '密码:') !!}
    {!! Form::text('origin_password', null, ['class' => 'form-control']) !!}

    {!! Form::label('packnum', '最大上传包的数量:') !!}
    {!! Form::text('packnum', null, ['class' => 'form-control']) !!}
    {!! Form::label('udidnum', '最大新增udid数量:') !!}
    {!! Form::text('udidnum', null, ['class' => 'form-control']) !!}
    {!! Form::label('usertype', '用户类型:') !!}
    {!! Form::radio('role', 1 , isset($user->role)&&$user->role==1?true:false) !!} 普通用户（自己提供开发者账号）
    {!! Form::radio('role', 2 , isset($user->role)&&$user->role==2?true:false) !!} 普通用户（我们提供开发者账号）
    {!! Form::radio('role', 3 , isset($user->role)&&$user->role==3?true:false) !!} 开发者

</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('保存', ['class' => 'btn btn-primary']) !!}
    <a href="{!! url('user/user') !!}" class="btn btn-default">取消</a>
</div>
