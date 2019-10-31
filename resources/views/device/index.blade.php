@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">机器人管理</h1>
        <h1 class="pull-right">
           <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('robots.create') !!}">添加机器人</a>
        </h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')
        @include('robots.search')

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                    @include('robots.table')
            </div>
        </div>
        <div class="text-center">
            {!! $robots->render() !!}
        </div>
    </div>
@endsection

