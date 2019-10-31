@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">添加词条</h1>
        <h1 class="pull-right">
           <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('robots.create') !!}">添加机器人</a>
        </h1>
    </section>

    <div class="content">
        {!! Form::open(['url' => "/group/addreply?robot_id=1",'files' => true, 'method'=>'POST']) !!}
        <div class="clearfix"></div>
        @include('flash::message')
        <div class="clearfix"></div>
        <div class="box box-primary">
        <input type="hidden" name="robot_id" value="{{$request['robot_id']}}">
            <input type="hidden" name="id" value="{{$request['id']}}">
        <div class="">

            <p>回复信息： <input name="reply" value="{{$reply->reply}}"></input></p>
            <p>直接关键词：<input name="keyword" value="{{$reply->keyword}}"></input></p>
            <p>模糊关键词：<input name="like_keyword" value="{{$reply->like_keyword}}"></input></p>
        </div>
        </div>


        <div class="form-group col-sm-12">
            {!! Form::submit('保存', ['class' => 'btn btn-primary']) !!}
        </div>
         {!! Form::close() !!}
        </div>
        <div class="text-center">
            
        </div>
    </div>
    <script src="/robot/js/jquery.min.js"></script>
    <script type="text/javascript">
        var default_control = $('#default_control').val();
        if(default_control==1){
            $('.control_2').hide();
        }
        if(default_control==2||default_control==3){
            $('.control_1').hide();
        }
        $(".control").change(function(){
            key= [];
            var control = $('.control option:selected').val();//获取id
            if(control==1){
                $('.control_1').show();
                $('.control_2').hide();
            }
            if(control==2||control==3){
                $('.control_1').hide();
                $('.control_2').show();
            }


        });
    </script>
@endsection

