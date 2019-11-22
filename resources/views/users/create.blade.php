@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            创建机器人
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        @include('flash::message')
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'users.store','files' => true]) !!}
{{--                    @include('robots.fields')--}}
                        <div class="form-group col-sm-12">
                            {!! Form::label('name', '用户名:') !!}
                            {!! Form::text('name', null, ['class' => 'form-control']) !!}
                            {!! Form::label('password', '密码:') !!}
                            {!! Form::text('password', null, ['class' => 'form-control']) !!}
                            {!! Form::label('check_password', '确认密码:') !!}
                            {!! Form::text('check_password', null, ['class' => 'form-control']) !!}

                            {!! Form::label('packnum', '最大上传包的数量:') !!}
                            {!! Form::text('packnum', null, ['class' => 'form-control']) !!}
                            {!! Form::label('udidnum', '最大新增udid数量:') !!}
                            {!! Form::text('udidnum', null, ['class' => 'form-control']) !!}
                            {!! Form::label('usertype', '用户类型:') !!}
                            {!! Form::radio('role', '1') !!} 普通用户（自己提供开发者账号）
                            {!! Form::radio('role', '2') !!} 普通用户（我们提供开发者账号）
                            {!! Form::radio('role', '3') !!} 开发者

                        </div>


                </div>
            </div>

        </div>
        <div class="box box-primary">
            <div class="box-body">
                <div class="">
                    <!-- Submit Field -->
                    <div class="form-group">
                        {!! Form::submit('保存', ['class' => 'btn btn-primary']) !!}
                        <a href="{!! url('user/user') !!}" class="btn btn-default">取消</a>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
