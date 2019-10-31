
@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">加好友自动应答</h1>
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
            <p>当有人加你时，自动通过验证请求，并且你可以设置消息自动回复。</p>
            <p>注意：</p>
            <p>1.当机器人微信号好友数超过5000时，该功能将自动关闭，请及时更换或清理微信号！</p>
            <p>2.请保持机器人在线，否则将无法自动通过好友验证！ </p>
        </div>
        {!! Form::open(['url' => '/friend/addauto','files' => true, 'method'=>'POST','enctype' => 'multipart/form-data']) !!}
            <input type="hidden" name="robot_type" value="1">
            <input type="hidden" name="request_url" value="{{$request_url}}">
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
                </label>
                <br />

            </div>
            </div>
            {{ csrf_field() }}

            {{--<input type="hidden" name="_token" value="{{ csrf_token() }}">--}}
            <div class="box box-primary">
                <div class="box-body">
                    控件类型：
                    <select id="control"  name ="control" class="control" style="width:130px;">
                       <option value="1" @if($wxdata->control==1) selected @endif>文字</option>
                       <option value="2" @if($wxdata->control==2) selected @endif>图片</option>
                       <option value="3" @if($wxdata->control==3) selected @endif>视频</option>
                   </select>
                    </br>
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
            var url = '/friend/saveRobotId?robot_id='+check_val+'&save_type=1';
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

