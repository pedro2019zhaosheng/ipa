@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">加附近的好友</h1>
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
                    开启后，机器人会根据系统素材根据设置时段随机发朋友圈，平均每2小时一条。</p>
            </div>
            {!! Form::open(['url' => '/circle/saveCircle','files' => true, 'method'=>'POST']) !!}
            <input type="hidden" name="robot_type" value="1">
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
                            {!! Form::radio('status', '1', $wxdata->status==1?true:false) !!} 开启
                            {!! Form::radio('status', '0', $wxdata->status==0?true:false) !!} 关闭

                        </label>
                        <label class="checkbox-inline">
                            {!! Form::label('type', '每天开启时段:') !!}
                            {!! Form::text('start', $wxdata->start>0?$wxdata->start:0, ['placeholder' => '开启整点时间,最小0']) !!}到
                            {!! Form::text('end', $wxdata->end>0?$wxdata->end:0, ['placeholder' => '结束整点时间，最大24']) !!}
                            {!! Form::label('interval', '间隔时间:') !!}
                            {!! Form::text('interval', $wxdata->interval>0?$wxdata->interval:0, ['placeholder' => '间隔时间']) !!}小时
                        </label>

                    </div>
                </div>

                <div class="box box-primary">
                    <div class="box-body">
                        <div>城市选择器</div>
                        <select id="pro"  name ="province" class="pro" style="width:130px;">
                            <option name ="province" value ="0" pro="0">==请选择==</option>
                            @foreach($province as $k=>$v)
                                <option name ="province" @if($v->name==$wxdata->province) selected @endif value ="{{$v->name}}" pro="{{$v->id}}">{{$v->name}}</option>
                            @endforeach
                        </select>

                        <div id="citydiv">
                            </br>
                            @foreach($originCity as $k=>$v)
                                     <input type="checkbox" name="city[]" @if($v['status']==1) checked @endif value="{{$v['name']}}">{{$v['name']}}
                            @endforeach
                        </div>
                    </div>
                </div>

            <div class="box box-primary">
                <div class="box-body">
                    素材所属用户：</br>
                    <label class="">
                        {!! Form::label('sex', '性别:') !!}
                        {!! Form::radio('sex', '0', isset($wxdata->sex)&&$wxdata->sex==0?true:false) !!} 所有
                        {!! Form::radio('sex', '1', $wxdata->sex==1?true:false) !!} 男
                        {!! Form::radio('sex', '2', $wxdata->sex==2?true:false) !!} 女

                    </label>

                </br>
                    <label class="">
                        {!! Form::radio('type', 1, $wxdata->type==1?true:false) !!} 单用户
                        {!! Form::radio('type', 2, $wxdata->type==2?true:false) !!} 多用户

                    </label>
                </div>
            </div>

            </div>
            <div class="box box-primary">
                <div class="box-body">
                    共{{$ds->user_num}}名用户，{{$ds->weibo_num}}条朋友圈数据
                    </br>
                    </br>
                    @foreach($ds->data as $k=>$v)
                        <label class="checkbox-inline">
                            {!! Form::label('', ($k+1).'.', false); !!}{{$v->content}}
                        </label>
                        </br>
                    @endforeach
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-body">
                    {{--<div class="form-group ">--}}
                        {{--{{ Form::textarea('msg', '哈喽～很高兴认识你呢--}}
                {{--如果有什么问题可以留言，我会尽快回复～么么哒', ['class'=>'form-control', 'row'=>'6']) }}--}}
                    {{--</div>--}}
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
    <script src="/robot/js/jquery.min.js"></script>
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
            var url = '/friend/saveRobotId?robot_id='+check_val+'&save_type=6';
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
        $(".pro").change(function(){
            key= [];
            var province_name = $('#pro option:selected').val();//获取选中值
            var province_id = $('#pro option:selected').attr('pro');//获取id
            var url = '/circle/getCity?province_id='+province_id;
            $('#citydiv').html("");
            $.ajax({
                type: "post",
                url: url,
                data: {"province_id":province_id, '_token':'{{csrf_token()}}','key':key},
                dataType: "json",
                success: function(data){
                    var count = data.city.length;
                    for( i=0;i<count;i++)
                    {
                        $("#citydiv").append("<input type='checkbox' name='city[]'"+ "value="+data.city[i]['name']+">"+data.city[i]['name']+"</checkbox>");
                    }
                },
            });
        });
    </script>
@endsection

