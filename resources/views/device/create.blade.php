@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            创建机器人
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        <div class="box box-primary">
            <div class="box-body">
                <div class="">
                    <p>提示：请允许app插件获得所需权限才可同步微信信息并由机器人接管微信。</p>
                </div>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-body">
                <div class="">
                    <p>消息提示区域：效果跟消息弹窗一个效果。</p>
                </div>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'robots.store','files' => true]) !!}
{{--                    @include('robots.fields')--}}
                        <div class="form-group col-sm-12">
                            {!! Form::label('device_id', '1、输入app里面的设备ID:') !!}
                            {!! Form::text('device_id', null, ['class' => 'form-control']) !!}
                        </div>
                </div>
            </div>

        </div>
        <div class="box box-primary">
            <div class="box-body">
                <div class="">

                    <p>
                        2、安卓手机扫码安装app插件，并允许所有权限。</p>
                    <img class='showimage' width="120" height="120" src="{!! $qrcode !!}">
                    </br>
                    </br>
                    <!-- Submit Field -->
                    <div class="form-group">
                        {!! Form::submit('保存', ['class' => 'btn btn-primary']) !!}
                        <a href="{!! route('robots.index') !!}" class="btn btn-default">取消</a>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
