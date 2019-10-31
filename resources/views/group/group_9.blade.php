@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">
            开启后，管理员@被踢人+%踢，机器人会收到指令提出该用户。</h1>
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

        </div>
        {!! Form::open(['url' => '/group/addauto','files' => true, 'method'=>'POST']) !!}
          <div class="box-body">
          {{--<input type="checkbox" name="mm[]" id="boxid" onclick="setAllNo()" />全选/全不选--}}
            <input type="hidden" name="robot_type" value="6">
            <input type="hidden" name="request_url" value="{{$request_url}}">
              {{--<input type="text" id="sampling">--}}
            {!! Form::label('robot_id', '微信号:  ') !!}
            @foreach($robot as $k=>$v)
            <label class="checkbox-inline">
                <input type="checkbox"  @if(isset($select[$k])&&$k==$select[$k]) checked="checked"@endif onclick="aa()" id="'robot_id" name="robot_id[]" value="{{$k}}" />{{$v}}

                {{--<a href="/friend/autoreply?robot_id={{$k}}">{!! Form::checkbox('robot_id[]', $k, false); !!}{{$v}}</a>--}}
            </label>
            @endforeach
            </div>
            {{--<div class="box-body">--}}

        </div>

        <div class="box box-primary">
            <div class="box-body">
                选择加好友的群： 已添加0人，共X人。</br></br>
                @foreach($group as $k=>$v)
                    <label class="checkbox-inline">
                        <input onclick="group()" type="checkbox" @if($v['status']==1) checked @endif  onclick="" id="'group" name="group[]" value="{{$v['id']}}" />{{$v['name']}}
                    </label>
                @endforeach
            </div>
        </div>
        {{--<div class="text-center">--}}

        {{--</div>--}}

        <div class="box box-primary">
            <div class="box-body">
                <label class="checkbox-inline">
                    {{--<td ><button style="margin-right: 40px"><a id="links" style="color:grey" href ="/group/groupKick?status=1">开启</a></button></td>--}}
                    {{--<td ><button style="margin-right: 40px"><a id="links" style="color:grey" href ="/group/groupKick?status=0">关闭</a></button></td>--}}
                    {{--<a href="#" id="xiazai">下载</a>--}}
                    {{--<td><button style="margin-right: 40px">批量导入词条</button></td>--}}

                    <label class="">
                        {!! Form::label('status', '状态:') !!}
                        {!! Form::radio('status', '1', isset($wxdata->status)&&$wxdata->status==1?true:false) !!} 开启
                        {!! Form::radio('status', '0', isset($wxdata->status)&&$wxdata->status==0?true:false) !!} 关闭

                    </label>

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
                    <th>id</th>
                    <th>被踢昵称</th>
                    <th>所在群</th>
                    <th>踢出时间</th>
                    <th>踢出方式</th>
                    {{--<th colspan="3">操作</th>--}}
                </tr>
                </thead>
                <tbody>
                @foreach($replys as $reply)
                    <tr>
                        <td>{!! $reply['id'] !!}</td>
                        <td>{!! $reply['nickname'] !!}</td>
                        <td>{!! $reply['group_desc'] !!}</td>
                        <td>{!! $reply['kick_date'] !!}</td>

                        <td>{!! $reply['kick_method'] !!}</td>

                        {{--<td><img class='

                        image' width="60" height="60" src="{!! $robot->img !!}"></td>--}}
                        {{--<td>{!! $robot->sex == 1 ? '男' : '女' !!}</td>--}}
                        {{--<td>{!! $robot->account !!}</td>--}}
                        {{--<td>{!! $robot->login_status == 0 ? '离线' : '在线' !!}</td>--}}
                        {{--<td>{!! $robot->run_status == 0 ? '异常' : '正常' !!}</td>--}}
                        {{--<td>{!! $robot->last_login !!}</td>--}}
                        {{--<td>{!! $robot->last_logout !!}</td>--}}
                        {{--<td>--}}
                            {{--{!! Form::open(['route' => ['groups.destroy', $reply->id], 'method' => 'delete']) !!}--}}
                            {{--<div class='btn-group'>--}}
                                {{--<a href="/group/groupMember?is_kick=1&id={{$reply->id}}" class='btn btn-default btn-xs'>踢出</a>--}}
                                {{--<a href="/group/groupMember?is_block=1&id={{$reply->id}}" class='btn btn-default btn-xs'>踢出并拉黑</a>--}}
                                {{--<a href="/group/groupMember?is_admin=1&id={{$reply->id}}" onclick = "return confirm('Are you sure?')" class='btn btn-default btn-xs'>设置管理员</a>--}}
                            {{--</div>--}}
                            {{--{!! Form::close() !!}--}}
                        {{--</td>--}}
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
            var url = '/group/saveRobotId?robot_id='+check_val+'&save_type=9';
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

        function group(){
            var obj = $('[name="group[]"]');
            check_val = [];
            key= [];
            i=0;
            // obj.attr("checked",false);
            var ms=[];
            for(k in obj){
                if(obj[k].checked && obj[k].value != "0") {
                    check_val.push(obj[k].value);

                }else{
                    ms.push(obj[k].value);

                }
                // if(obj[k].checked==1){
                //     check_val = obj[k].value;
                // }else{
                //     obj[k].checked=false;
                // }

                // key.push(obj[k].key);
                i++;

            }
            var url = '/group/saveGroupId?robot_id='+check_val+'&group_type=1&no_check_id='+ms;
            $("#sampling").val(url);
            if(check_val==''){
                check_val = 0;
            }
            $.ajax({

                type: "post",

                url: url,

                data: {"group_id":check_val, '_token':'{{csrf_token()}}','key':key,'group_type':2,'no_check_id':ms},

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
            if(box.checked == false){
                for (var i = 0; i < loves.length; i++) {
                     loves[i].checked = false;
                 }
            }else{
                 for (var i = 0; i < loves.length; i++) {
                     loves[i].checked = true;
                 }
             }
            }
    </script>
@endsection

