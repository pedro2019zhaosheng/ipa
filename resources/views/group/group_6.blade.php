@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">话题/定时群发</h1>
        <h1 class="pull-right">
           <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{!! route('robots.create') !!}">添加机器人</a>
        </h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>
        @include('flash::message')
        <div class="clearfix"></div>
        <div class="box box-primary">
        <div class="">   
            <p>
                群发可以基于话题、活动等场景添加内容</p>
        </div>
        {!! Form::open(['url' => '/group/doWithGroupSend','files' => true, 'method'=>'POST']) !!}
          <div class="box-body">
          {{--<input type="checkbox" name="mm[]" id="boxid" onclick="setAllNo()" />全选/全不选--}}
            <input type="hidden" name="robot_type" value="3">
            <input type="hidden" name="request_url" value="{{$request_url}}">
              <input type="hidden" id="check_val" name="check_val">
              <input type="hidden" name="status" value="{{$request->status}}">
              <input type="hidden" name="save_type" value="1">
              {{--<input type="text" id="sampling">--}}

            {{--<div class="box-body">--}}

        </div>

        {{--<div class="text-center">--}}

        {{--</div>--}}

        <div class="box box-primary">
            <div class="box-body">
                <label class="checkbox-inline">
                    <td ><button style="margin-right: 40px"><a id="links" style="color:grey" href ="/group/groupSend?status=1">开启</a></button></td>
                    <td ><button style="margin-right: 40px"><a id="links" style="color:grey" href ="/group/groupSend?status=0">关闭</a></button></td>

                    <td ><button style="margin-right: 40px"><a id="links" style="color:grey" href ="/group/addSend?robot_id={{$wxdata->robot_id}}">新增群发</a></button></td>
                    {{--<a href="#" id="xiazai">下载</a>--}}
                    {{--<td><button style="margin-right: 40px">批量导入词条</button></td>--}}

                </label>
                <label class="checkbox-inline">
                    {!! Form::submit('保存', ['class' => 'btn btn-primary pull-right']) !!}
                </label>
            </div>
            {!! Form::close() !!}
        </div>
        <div class="box box-primary">
            <div class="box-body">
            <style>
                img{
                    width: 60px;
                    height: 60px;}
            </style>
            <table class="table table-responsive" id="games-table">
                <thead>
                <tr>
                    <input type="checkbox" id="boxid" onclick="setAllNo()" />全选/全不选
                    <th>id</th>
                    <th>群发内容</th>
                    <th>要发送群</th>
                    <th>重复时段</th>
                    <th>发送时间</th>
                    <th>是否开启</th>
                    <th colspan="3">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($replys as $reply)
                    <tr>
                        <td>

                            <input type="checkbox" name="love" onclick="single({{$reply['id']}})" value="{{$reply['id']}}"/>
                            {!! $reply['id'] !!}</td>
                        <td>
                            @if($reply->type==1)
                                {!! $reply['msg'] !!}
                            @endif
                            @if($reply->type==2)
                                <img class="showimage"  style="width: 200px;height: 100px;" src="{{$reply->msg}}">
                            @endif
                            @if($reply->type==3)
                                <video width="220" height="100" controls>
                                    <source src="{{$reply->msg}}" type="video/mp4">
                                </video>
                            @endif
{{--                            {!! $reply['msg'] !!}--}}
                        </td>
                        <td>{!! $reply['group_desc'] !!}</td>
                        <td>{!! $reply['week_desc'] !!}</td>
                        <td>{!! $reply['stime'] !!}</td>
                        <td>{!! $reply['status']>0?'开启':'关闭' !!}</td>

                        {{--<td><img class='

                        image' width="60" height="60" src="{!! $robot->img !!}"></td>--}}
                        {{--<td>{!! $robot->sex == 1 ? '男' : '女' !!}</td>--}}
                        {{--<td>{!! $robot->account !!}</td>--}}
                        {{--<td>{!! $robot->login_status == 0 ? '离线' : '在线' !!}</td>--}}
                        {{--<td>{!! $robot->run_status == 0 ? '异常' : '正常' !!}</td>--}}
                        {{--<td>{!! $robot->last_login !!}</td>--}}
                        {{--<td>{!! $robot->last_logout !!}</td>--}}
                        <td>
                            {!! Form::open(['url' => ['group/destroySend?id='.$reply->id ], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                <a href="/group/addSend?robot_id={{$reply->robot_id}}&id={{$reply->id}}" class='btn btn-default btn-xs'>修改</a>
                                {!! Form::button('删除', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                            </div>
                            {!! Form::close() !!}
                        </td>
                    </tr>
                @endforeach

                </tbody>

            </table>
                <div class="text-center">
                    {!! $replys->render() !!}
                </div>
        </div>
        </div>
        </div>
    </div>
    <script type="text/javascript">
        function aa(){

            var obj = $('[name="robot_id[]"]');
            check_val = [];
            key= [];
            i=0;
            obj.attr("checked",false);
            for(k in obj){
                if(obj[k].checked && obj[k].value != "0")
                    check_val.push(obj[k].value);
                if(obj[k].checked==1){
                    check_val = obj[k].value;
                }else{
                    obj[k].checked=false;
                }

                key.push(obj[k].key);
                i++;

            }
            var url = '/group/saveRobotId?robot_id='+check_val+'&save_type=3';
            $("#sampling").val(url);
            if(check_val==''){
                check_val = 0;
            }
            $.ajax({

                type: "post",

                url: url,

                data: {"robot_id":check_val, '_token':'{{csrf_token()}}','key':key},

                dataType: "json",

                success: function(data){
                    location.reload();
                },



            });
            // window.location.href= url;
            document.getElementById("links").href=url;
        }
        function setAllNo(){
            var box = document.getElementById("boxid");
            var loves = document.getElementsByName("love");
            var check_val = [];
            if(box.checked == false){
                for (var i = 0; i < loves.length; i++) {
                     loves[i].checked = false;

                 }
            }else{
                 for (var i = 0; i < loves.length; i++) {
                     loves[i].checked = true;
                     check_val.push(loves[i].value);
                 }
                $("#check_val").val(check_val);
             }
            }

        function single(obj) {
            var loves = document.getElementsByName("love");
            var check_val = [];
            for (var i = 0; i < loves.length; i++) {
                if(loves[i].checked)
                check_val.push(loves[i].value);
            }
            $("#check_val").val(check_val);
        }
    </script>
@endsection

