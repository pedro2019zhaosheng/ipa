@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1>
            添加IPA包
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
         @include('flash::message')
        <div class="box box-primary">
            <div class="box-body">
                 <div class="row">
                    {!! Form::open(['route' => 'package.store','files' => true]) !!}
{{--                    @include('robots.fields')--}}

                        <div class="form-group col-sm-12">
                            {!! Form::label('device_id', 'IPA文件:') !!}
                            {!! Form::file('file') !!}
                        </div>
                </div>


                <div class="row">
                    {!! Form::open(['route' => 'package.store','files' => true]) !!}
                <input type='hidden' name='id' value="{{isset($package->id)?$package->id:0}}">
                <input type='hidden' name='ipa_url' value="{{isset($package->ipa_url)?$package->ipa_url:''}}">  
{{--                    @include('robots.fields')--}}
                        <div class="form-group col-sm-12">
                            {!! Form::label('account', '简介:') !!}
                            {!! Form::text('introduction', isset($package->introduction)?$package->introduction:'' , ['class' => 'form-control']) !!}
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
                        <a href="{!! route('robots.index') !!}" class="btn btn-default">取消</a>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
