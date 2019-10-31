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

