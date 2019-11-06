@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            添加账号
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
         @include('flash::message')
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'apple.store','files' => true]) !!}
                <input type='hidden' name='id' value="{{isset($apple->id)?$apple->id:0}}">
                <input type='hidden' name='p12_url' value="{{isset($apple->p12_url)?$apple->p12_url:''}}">  
{{--                    @include('robots.fields')--}}
                        <div class="form-group col-sm-12">
                            {!! Form::label('account', '苹果账号:') !!}
                            {!! Form::text('account', isset($apple->account)?$apple->account:'' , ['class' => 'form-control']) !!}
                        </div>
                </div>
                 <div class="row">
                    {!! Form::open(['route' => 'robots.store','files' => true]) !!}
{{--                    @include('robots.fields')--}}
                        <div class="form-group col-sm-12">
                            {!! Form::label('secret_key', '苹果账号密码:') !!}
                            {!! Form::text('secret_key', isset($apple->secret_key)?$apple->secret_key:'', ['class' => 'form-control']) !!}
                        </div>
                </div>

                <div class="row">
                    {!! Form::open(['route' => 'robots.store','files' => true]) !!}
{{--                    @include('robots.fields')--}}

                        <div class="form-group col-sm-12">
                            {!! Form::label('device_id', 'P12证书:') !!}
                            {!! Form::file('file') !!}
                        </div>
                </div>

                 <div class="row">
                    {!! Form::open(['route' => 'robots.store','files' => true]) !!}
{{--                    @include('robots.fields')--}}
                        <div class="form-group col-sm-12">
                            {!! Form::label('secret_key', '证书ID:') !!}
                            {!! Form::text('certificate_id', isset($apple->certificate_id)?$apple->certificate_id:'', ['class' => 'form-control']) !!}
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
                        <a href="{!! url('apple/apple') !!}" class="btn btn-default">取消</a>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
