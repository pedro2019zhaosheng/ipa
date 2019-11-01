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

