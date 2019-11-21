@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">苹果账号</h1>
        <h1 class="pull-right">
           
        </h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')
        @include('apple.search')

        <div class="box box-primary">
            <div class="box-body">
                {!! Form::open(['url' => 'package/package','method'=>'GET']) !!}
                <div class="form-group col-sm-100">
                <span style="color: red">注：设置了推送证书，脚本自动打包时会自动切换到可以配置推送证书的账号</span></br>
            </div>


            {!! Form::close() !!}

        </div>
    </div>

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                    @include('apple.table')
            </div>
        </div>
        <div class="text-center">
            {!! $apple->render() !!}
        </div>
    </div>
@endsection

