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
                            {!! Form::label('name', '包名:') !!}
                            {!! Form::text('name', isset($package->name)?$package->name:'' , ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group col-sm-12">
                            {!! Form::label('version', '版本:') !!}
                            {!! Form::text('version', isset($package->version)?$package->version:'' , ['class' => 'form-control']) !!}
                        </div>
                        @if(isset($packge->icon)&&$package->icon!='')
                            <img  style="width: 200px;" src="{{$package->icon}}">
                        @endif
                         <div class="form-group col-sm-12">
                            {!! Form::label('device_id', 'Icon:') !!}
                            {!! Form::file('icon') !!}
                        </div>

                        <div class="form-group col-sm-12">
                            {!! Form::label('device_id', 'IPA文件:') !!}
                            {!! Form::file('file') !!}
                        </div>
                </div>
                


                <div class="row">
                    {!! Form::open(['route' => 'package.store','files' => true]) !!}
                <input type='hidden' name='id' value="{{isset($package->id)?$package->id:0}}">
                <input type='hidden' name='ipa_url' value="{{isset($package->ipa_url)?$package->ipa_url:''}}">  
                <input type='hidden' name='icon_url' value="{{isset($package->icon)?$package->icon:''}}">  
{{--                    @include('robots.fields')--}}
                         <div class="form-group col-sm-12">
                            {!! Form::label('account', 'BuddleId:') !!}
                            {!! Form::text('buddle_id', isset($package->buddle_id)?$package->buddle_id:'' , ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group col-sm-12">
                            {!! Form::label('account', '简介:') !!}
                            {!! Form::text('introduction', isset($package->introduction)?$package->introduction:'' , ['class' => 'form-control']) !!}
                        </div>
                    <label class="checkbox-inline">
                        {!! Form::label('status', '是否推送:') !!}
                        {!! Form::radio('is_push', '1', isset($package->is_push)&&$package->is_push==1?true:false) !!} 开启
                        {!! Form::radio('is_push', '0', isset($package->is_push)&&$package->is_push==0?true:false) !!} 关闭

                    </label>
                </div>


                
            </div>

        </div>
        <div class="box box-primary">
            <div class="box-body">
                <div class="">

                  
                    <!-- Submit Field -->
                    <div class="form-group">
                        {!! Form::submit('保存', ['class' => 'btn btn-primary']) !!}
                        <a href="{!! url('package/package') !!}" class="btn btn-default">取消</a>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
