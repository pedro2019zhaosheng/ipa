@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            添加设备
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
         @include('flash::message')
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'device.store','files' => true]) !!}
                <input type='hidden' name='id' value="{{isset($device->id)?$device->id:0}}">
                <input type='hidden' name='p12_url' value="{{isset($device->p12_url)?$device->p12_url:''}}">  
{{--                    @include('robots.fields')--}}
                        <div class="form-group col-sm-12">
                            {!! Form::label('account', '设备UDID:') !!}
                            {!! Form::text('udid', isset($device->udid)?$device->udid:'' , ['class' => 'form-control']) !!}
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
                        <a href="{!! url('device/device') !!}" class="btn btn-default">取消</a>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
