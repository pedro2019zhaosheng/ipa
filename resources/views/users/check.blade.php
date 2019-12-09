@extends('layouts.app')
<style>
    img{
        width: 60px;
        height: 60px;}
</style>
@section('content')
    <section class="content-header">
        <h1>
            用户审核
        </h1>
    </section>
    <div class="content">
        @include('adminlte-templates::common.errors')
        @include('flash::message')
        <div class="box box-primary">
            <div class="box-body">
                <div class="row">
                    {!! Form::open(['route' => 'apple.store','files' => true]) !!}
                    <input type='hidden' name='id' value="{{isset($user->id)?$user->id:0}}">
                    {{--                    @include('robots.fields')--}}
                    <div class="form-group col-sm-12">
                        {!! Form::label('account', '用户名:') !!}
                        {!! Form::text('username', isset($user->username)?$user->username:'' , ['class' => 'form-control','readonly'=>true]) !!}
                    </div>
                </div>
                <div class="row">
                    {!! Form::open(['route' => 'robots.store','files' => true]) !!}
                    {{--                    @include('robots.fields')--}}
                    <div class="form-group col-sm-12">
                        {!! Form::label('secret_key', '身份号码:') !!}
                        {!! Form::text('certificate_id', isset($user->id_card_no)?$user->id_card_no:'', ['class' => 'form-control','readonly'=>true]) !!}
                    </div>

                </div>
                <div class="row">
                    {!! Form::open(['route' => 'robots.store','files' => true]) !!}
                    {{--                    @include('robots.fields')--}}
                    <div class="form-group col-sm-12">
                        {!! Form::label('secret_key', '头像:') !!}
                        <img class="showimage" style="width: 100px;height: 100px" src="{{$user->avatar_url}}">
                    </div>
                </div>
                <div class="row">
                    {!! Form::open(['route' => 'robots.store','files' => true]) !!}
                    {{--                    @include('robots.fields')--}}
                    <div class="form-group col-sm-12">
                        {!! Form::label('secret_key', '身份证正面:') !!}
                        <img class="showimage" style="width: 100px;height: 100px" src="{{$user->id_card_front_url}}">
                    </div>
                </div>

                <div class="row">
                    {!! Form::open(['route' => 'robots.store','files' => true]) !!}
                    {{--                    @include('robots.fields')--}}
                    <div class="form-group col-sm-12">
                        {!! Form::label('secret_key', '身份证背面:') !!}
                        <img class="showimage" style="width: 100px;height: 100px" src="{{$user->id_card_back_url}}">
                    </div>
                </div>

            </div>

        </div>
        <div class="box box-primary">
            <div class="box-body">
                <div class="">


                    <!-- Submit Field -->
                    <div class="form-group">
                        <a class="btn btn-default" href="{!! url('user/check?is_pass=1&id='.$user->id) !!}" class='btn btn-default btn-xs'><span style="color:green">通过</span></a>
                        <a class="btn btn-default" href="{!! url('user/check?is_pass=0&id='.$user->id) !!}" class='btn btn-default btn-xs'><span style="color:red">驳回</span></a>
                        <a href="{!! url('user/user') !!}" class="btn btn-default">取消</a>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection
