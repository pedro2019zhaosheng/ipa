@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">添加词条</h1>
        <h1 class="pull-right">
           <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('robots.create') !!}">添加机器人</a>
        </h1>
    </section>

    <div class="content">
        {!! Form::open(['url' => "/friend/addreply?robot_id=1",'files' => true, 'method'=>'POST']) !!}
        <div class="clearfix"></div>
        @include('flash::message')
        <div class="clearfix"></div>
        <div class="box box-primary">
        <input type="hidden" name="robot_id" value="{{$request['robot_id']}}">
            <input type="hidden" name="id" value="{{$request['id']}}">
        <div class="">

            <p>问题1： <input name="problem[]" value="{{$problem[0]}}"></input></p>
            <p>问题2：<input name="problem[]" value="{{$problem[1]}}"></input></p>
            <p>问题3：<input name="problem[]" value="{{$problem[2]}}"></input></p>
            <p>问题4：<input name="problem[]" value="{{$problem[3]}}"></input></p>
        </div>
        </div>

    <div class="box box-primary">
        <div class="box-body">
            控件类型：
            <select id="control"  name ="control" class="control" style="width:130px;">
                <option value="1" @if($reply->control==1) selected @endif>文字</option>
                <option value="2" @if($reply->control==2) selected @endif>图片</option>
                <option value="3" @if($reply->control==3) selected @endif>视频</option>
            </select>
            </br>
            </br>
            <input type="hidden" id="default_control" value="{{$reply->control}}">
            <div class="control_1">
                {{--                @if($wxdata->control==1)--}}
                <p>答案： {{ Form::textarea('msg',  $reply->msg?$reply->msg:'哈喽～很高兴认识你呢!如果有什么问题可以留言，我会尽快回复～么么哒', ['class'=>'form-control', 'row'=>'6']) }}</p>
                {{--@endif--}}
            </div>
            <div class="control_2">
                @if($reply->control==2)
                    <img  style="width: 400px;" src="{{$reply->msg}}">
                @endif
                @if($reply->control==3)
                    <video width="320" height="240" controls>
                        <source src="{{$reply->msg}}" type="video/mp4">
                    </video>
                @endif
                <input type="file" name="img">
            </div>



           {{--<p>答案： {{ Form::textarea('msg', '哈喽～很高兴认识你呢--}}
{{--如果有什么问题可以留言，我会尽快回复～么么哒', ['class'=>'form-control', 'row'=>'6']) }}</p>--}}
        </div>
    </div>
        <div class="box box-primary">
            <div class="box-body">
                <p>关联问题1：

                    <select name="relation[]">
                        @foreach($originProblem as $k=>$v)
                        <option value="{{$v->origin_problem}}">{{$v->origin_problem}}</option>
                        @endforeach
                    </select>

                </p>

                <p>关联问题2：
                    <select name="relation[]">
                        @foreach($originProblem as $k=>$v)
                            <option value="{{$v->origin_problem}}">{{$v->origin_problem}}</option>
                        @endforeach
                    </select></p>
                <p>关联问题3：
                    <select name="relation[]">
                        @foreach($originProblem as $k=>$v)
                            <option value="{{$v->origin_problem}}">{{$v->origin_problem}}</option>
                        @endforeach
                    </select></p>
                <p>关联问题4：
                    <select name="relation[]">
                        @foreach($originProblem as $k=>$v)
                            <option value="{{$v->origin_problem}}">{{$v->origin_problem}}</option>
                        @endforeach
                    </select>
                </p>
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

