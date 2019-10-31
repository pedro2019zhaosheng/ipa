<!-- Name Field -->
<div class="form-group col-sm-12">
    {!! Form::label('name', '用户名:') !!}
    {!! Form::text('name', null, ['class' => 'form-control']) !!}
    {!! Form::label('password', '密码:') !!}
    {!! Form::text('origin_password', null, ['class' => 'form-control']) !!}
</div>

<!-- Submit Field -->
<div class="form-group col-sm-12">
    {!! Form::submit('保存', ['class' => 'btn btn-primary']) !!}
    <a href="{!! url('user/user') !!}" class="btn btn-default">取消</a>
</div>
