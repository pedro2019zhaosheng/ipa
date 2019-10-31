@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">私聊自动回复</h1>
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
                当指定好友私聊消息中包含关键词时，微信号将自动回复消息。</p>
            <p>注意：</p>
            <p>1.若回复过于频繁，可能在一段时间内无法自动发送消息。</p>
            <p>2.请保持机器人在线，否则将无法自动回复消息。 </p>
        </div>
        {!! Form::open(['url' => '/friend/addauto','files' => true, 'method'=>'POST']) !!}
          <div class="box-body">
          {{--<input type="checkbox" name="mm[]" id="boxid" onclick="setAllNo()" />全选/全不选--}}
            <input type="hidden" name="robot_type" value="2">
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
        {{--<div class="text-center">--}}

        {{--</div>--}}

        <div class="box box-primary">
            <div class="box-body">
                <label class="checkbox-inline">
                    <label class="checkbox-inline">
                        {!! Form::label('status', '状态:') !!}
                        {!! Form::radio('status', '1', $wxdata->status==1?true:false) !!} 开启
                        {!! Form::radio('status', '0', $wxdata->status==0?true:false) !!} 关闭

                    </label>
                    <td ><button style="margin-right: 40px"><a id="links" style="color:grey" href ="/friend/addreply?robot_id={{$wxdata->robot_id}}">添加词条</a></button></td>
                    <a href="#" id="xiazai">下载</a>
                    <td><button style="margin-right: 40px">批量导入词条</button></td>
                    <td>
                        <label class="checkbox-inline">
                            {!! Form::label('type', '每天开启时段:') !!}
                            {!! Form::text('start', $wxdata->start>0?$wxdata->start:0, ['placeholder' => '开启整点时间,最小0']) !!}到
                            {!! Form::text('end', $wxdata->end>0?$wxdata->end:0, ['placeholder' => '结束整点时间，最大24']) !!}

                            {!! Form::label('type', '回复延迟:') !!}
                            {!! Form::text('delay', $wxdata->delay>0?$wxdata->delay:0, ['placeholder' => '单位秒']) !!}
                        </label>
                    </td>

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
                    <th>问题</th>
                    <th>答案</th>
                    <th>更多问法2</th>
                    <th>更多问法3</th>
                    <th>更多问法4</th>
                    <th>关联问题1</th>
                    <th>关联问题2</th>
                    <th>关联问题3</th>
                    <th>关联问题4</th>
                    <th colspan="3">操作</th>
                </tr>
                </thead>
                <tbody>
                @foreach($replys as $reply)
                    <tr>
                        <td>{!! $reply['id'] !!}</td>
                        <td>{!! $reply['problem_arr']['problem1'] !!}</td>
                        <td>
                            @if($reply->type==1)
                            {!! $reply['answer'] !!}
                            @endif
                            @if($reply->type==2)
                                <img class="showimage" style="width: 80px;height: 40px;" src="{{$reply['answer']}}">
                            @endif
                            @if($reply->type==3)
                                <video width="220" height="140" controls>
                                    <source src="{{$reply['answer']}}" type="video/mp4">
                                </video>
                            @endif
                        </td>
                        <td>{!! $reply['problem_arr']['problem2'] !!}</td>
                        <td>{!! $reply['problem_arr']['problem3'] !!}</td>
                        <td>{!! $reply['problem_arr']['problem4'] !!}</td>

                        <td>{!! isset($reply['relation_arr']['relation1'])?$reply['relation_arr']['relation1']:'' !!}</td>
                        <td>{!! isset($reply['relation_arr']['relation2'])?$reply['relation_arr']['relation2']:'' !!}</td>
                        <td>{!! isset($reply['relation_arr']['relation3'])?$reply['relation_arr']['relation3']:'' !!}</td>
                        <td>{!! isset($reply['relation_arr']['relation4'])?$reply['relation_arr']['relation4']:'' !!}</td>
                        {{--<td><img class='

                        image' width="60" height="60" src="{!! $robot->img !!}"></td>--}}
                        {{--<td>{!! $robot->sex == 1 ? '男' : '女' !!}</td>--}}
                        {{--<td>{!! $robot->account !!}</td>--}}
                        {{--<td>{!! $robot->login_status == 0 ? '离线' : '在线' !!}</td>--}}
                        {{--<td>{!! $robot->run_status == 0 ? '异常' : '正常' !!}</td>--}}
                        {{--<td>{!! $robot->last_login !!}</td>--}}
                        {{--<td>{!! $robot->last_logout !!}</td>--}}
                        <td>
                            {!! Form::open(['route' => ['friends.destroy', $reply->id], 'method' => 'delete']) !!}
                            <div class='btn-group'>
                                <a href="/friend/addreply?robot_id={{$wxdata->robot_id}}&id={{$reply->id}}" class='btn btn-default btn-xs'>修改</a>
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
            var url = '/friend/saveRobotId?robot_id='+check_val+'&save_type=2';
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

