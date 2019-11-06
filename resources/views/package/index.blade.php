@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">安装包管理</h1>
        <h1 class="pull-right">
           
        </h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')
        @include('package.search')

        <div class="box box-primary">
            <div class="box-body">
                {!! Form::open(['url' => 'package/package','method'=>'GET']) !!}
                <div class="form-group col-sm-100">
                    {!! Form::label('name', '包名:') !!}
                    <span style="color: red">下载地址规则参数:http://49.235.90.84:8893/api/apple/generatePackage?package_id=安装包Id&$apple_id=苹果账号ID</span>
                </div>


                {!! Form::close() !!}

            </div>
        </div>

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                    @include('package.table')
            </div>
        </div>
        <div class="text-center">
            {!! $package->render() !!}
        </div>
    </div>
@endsection

