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
                    <input type='hidden' name='package_id' value="{{isset($request->package_id)?$request->package_id:''}}">

                    <input type='hidden' name='type' value="{{isset($request->type)?$request->type:0}}">
                    {{--                    @include('robots.fields')--}}
                         <div class="form-group col-sm-12">
                            {!! Form::label('account', 'BuddleId:') !!}
                            {!! Form::text('buddle_id', isset($package->buddle_id)?$package->buddle_id:'' , ['class' => 'form-control']) !!}
                        </div>
                        <div class="form-group col-sm-12">
                            {!! Form::label('account', '简介:') !!}
                            {!! Form::text('introduction', isset($package->introduction)?$package->introduction:'' , ['class' => 'form-control']) !!}
                        </div>
                    <div class="form-group col-sm-12">
                        {!! Form::label('status', '是否推送:') !!}
                        {!! Form::radio('is_push', '1', isset($package->is_push)&&$package->is_push==1?true:false) !!} 开启
                        {!! Form::radio('is_push', '0', isset($package->is_push)&&$package->is_push==0?true:false) !!} 关闭

                    </div>
                    <div class="form-group col-sm-12">
                        {!! Form::label('is_binding', '是否自动打包捆绑包:') !!}
                        {!! Form::radio('is_binding', '1', isset($package->is_binding)&&$package->is_binding==1?true:false) !!} 开启
                        {!! Form::radio('is_binding', '0', isset($package->is_binding)&&$package->is_binding==0?true:false) !!} 关闭

                    </div>
                    <div class="form-group col-sm-12">
                        {!! Form::label('apple_id', '第一次打包苹果账号ID:') !!}
                        {!! Form::text('apple_id', isset($package->apple_id)?$package->apple_id:'' , ['class' => 'form-control']) !!}
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
                        <a href="{!! url('package/package') !!}" class="btn btn-default">取消</a>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
