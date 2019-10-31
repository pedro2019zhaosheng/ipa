@extends('layouts.app')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <section class="content-header">
        <h1 class="pull-left">新人入群回复</h1>
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
                    当好友添加成功或发送入群关键词时，自动邀请其进入指定群。</p>
                <p>注意：</p>
                <p>1.若你的微信号不是群主，且该群开启了“群聊邀请确认”，则无法自动入群。</p>
                <p>2.拉人进群过于频繁，可能导致一段时间内无法自动入群。 </p>
            </div>
            {!! Form::open(['url' => '/group/addSign','files' => true, 'method'=>'POST']) !!}
            <input type="hidden" name="robot_type" value="6">
            <input type="hidden" name="id" value="{{$request['id']}}">
            <input type="hidden" name="request_url" value="{{$request_url}}">
            <input type="hidden" id="robot_id" value="{{$request['robot_id']}}">
            <div class="box-body">
                <div class="box box-primary">
                    <div class="box-body">
                        {!! Form::label('robot_id', '微信号:  ') !!}
                        @foreach($robot as $k=>$v)
                            <label class="checkbox-inline">
                                <input type="checkbox"  @if(isset($select[$k])&&$k==$select[$k]) checked="checked"@endif onclick="aa()" id="'robot_id" name="robot_id[]" value="{{$k}}" />{{$v}}
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="box box-primary">
                    <div class="box-body">
                        <label class="">
                            {!! Form::label('status', '状态:') !!}
                            {!! Form::radio('status', '1', isset($wxdata->status)&&$wxdata->status==1?true:false) !!} 开启
                            {!! Form::radio('status', '0', isset($wxdata->status)&&$wxdata->status==0?true:false) !!} 关闭

                        </label>
                        {{--<label class="checkbox-inline">--}}
                            {{--{!! Form::label('type', '每天开启时段:') !!}--}}
                            {{--{!! Form::text('start', isset($wxdata->start)&&$wxdata->start>0?$wxdata->start:0, ['placeholder' => '开启整点时间,最小0']) !!}到--}}
                            {{--{!! Form::text('end', isset($wxdata->end)&&$wxdata->end>0?$wxdata->end:0, ['placeholder' => '结束整点时间，最大24']) !!}--}}
                        {{--</label>--}}
                        {{--<label class="">--}}
                            {{--{!! Form::label('sex', '性别:') !!}--}}
                            {{--{!! Form::radio('sex', '0', isset($wxdata->sex)&&$wxdata->sex==0?true:false) !!} 所有--}}
                            {{--{!! Form::radio('sex', '1', isset($wxdata->sex)&&$wxdata->sex==1?true:false) !!} 男--}}
                            {{--{!! Form::radio('sex', '2',isset($wxdata->sex)&&$wxdata->sex==2?true:false) !!} 女--}}
                            {{--{!! Form::label('interval', '间隔时间:') !!}--}}
                            {{--{!! Form::text('interval', $wxdata->interval>0?$wxdata->interval:0, ['placeholder' => '间隔时间']) !!}分钟--}}

                        {{--</label>--}}
                    </div>
                </div>

                <div class="box box-primary">
                    <div class="box-body">
                        选择要发送的群：</br></br>
                        @foreach($group as $k=>$v)
                            <label class="checkbox-inline">
                                <input type="checkbox" @if($v['status']==1) checked @endif  onclick="" id="'group" name="group[]" value="{{$v['name']}}" />{{$v['sname']}}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="box box-primary">
                    <div class="box-body">
                        {!! Form::label('published_at', '关键字:') !!}
                        {!! Form::input('text', 'keyword', isset($wxdata->keyword)?$wxdata->keyword:'') !!}
                        </br></br>
                        {!! Form::label('published_at', '回复:') !!}
                        {!! Form::input('text', 'reply', isset($wxdata->reply)?$wxdata->reply:'') !!}
                    </div>
                </div>

            </div>
            <div class="box box-primary">
                <div class="box-body">
                    控件类型：

                    </br>
                    <input type="hidden" id="default_control" value="{{$wxdata->control}}">
                    <div class="control_1">
                        {{--                @if($wxdata->control==1)--}}
                        {{ Form::textarea('msg',  $wxdata->msg?$wxdata->msg:'哈喽～很高兴认识你呢!如果有什么问题可以留言，我会尽快回复～么么哒', ['class'=>'form-control', 'row'=>'6']) }}
                        {{--@endif--}}
                    </div>
                    <div class="control_2">
                        @if($wxdata->control==2)
                            <img  style="width: 400px;" src="{{$wxdata->msg}}">
                        @endif
                        @if($wxdata->control==3)
                            <video width="320" height="240" controls>
                                <source src="{{$wxdata->msg}}" type="video/mp4">
                            </video>
                        @endif
                        <div class="timg">
                            {!! Form::file('img') !!}
                        </div>
                    </div>

                    <div class="box box-primary">
                        <div class="box-body">
                            </br>
                            <div>签到时间：
                                </br>
                                {!! Form::label('published_at', '签到日期:') !!}
                                {!! Form::input('date', 'start_date', isset($wxdata->start_date)?$wxdata->start_date:date('Y-m-d')) !!}
                                {!! Form::label('published_at', '到:') !!}
                                {!! Form::input('date', 'end_date', isset($wxdata->end_date)?$wxdata->end_date:date('Y-m-d')) !!}
                                </br>
                                {!! Form::label('published_at', '签到时间:') !!}
                                <select id="pro"  name ="start_time" class="pro" style="width:130px;">
                                    <option name ="time[]" value ="-99" pro="0">==请选择==</option>
                                    @foreach($time as $k=>$v)
                                        <option name ="start_time" @if($wxdata->start_time==$v['name'])selected @endif  value="{{$v['name']}}">{{$v['name']}}</option>
                                    @endforeach

                               </select>
                                <select id="pro"  name ="start_minute" class="pro" style="width:130px;">
                                    <option name ="start_minute[]" value ="-99" pro="0">==请选择==</option>
                                    @foreach($minute as $k=>$v)
                                        <option name ="start_minute[]" @if($wxdata->start_minute==$v['name'])selected @endif  value="{{$v['name']}}">{{$v['name']}}</option>
                                    @endforeach
                                </select>
                                {!! Form::label('published_at', '到:') !!}
                                <select id="pro"  name ="end_time" class="pro" style="width:130px;">
                                    <option name ="end_time" value ="-99" pro="0">==请选择==</option>
                                    @foreach($time as $k=>$v)
                                        <option name ="end_time" @if($wxdata->end_time==$v['name'])selected @endif  value="{{$v['name']}}">{{$v['name']}}</option>
                                    @endforeach
                                </select>
                                <select id="pro"  name ="end_minute" class="pro" style="width:130px;">
                                    <option name ="minute[]" value ="-99" pro="0">==请选择==</option>
                                    @foreach($minute as $k=>$v)
                                        <option name ="end_minute[]" @if($wxdata->end_minute==$v['name'])selected @endif  value="{{$v['name']}}">{{$v['name']}}</option>
                                    @endforeach
                                </select>
                            </div>

                        </div>
                    </div>

                    <div class="form-group col-sm-12">
                        {!! Form::submit('保存', ['class' => 'btn btn-primary']) !!}
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <div class="text-center">

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
            var url = '/group/saveRobotId?robot_id='+check_val+'&save_type=7';
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
                $('.timg').hide();
                $('.control_1').show();
                $('.control_2').hide();
            }
            if(control==2||control==3){
                $('.timg').show();
                $('.control_1').hide();
                $('.control_2').show();
            }


        });
    </script>
@endsection

