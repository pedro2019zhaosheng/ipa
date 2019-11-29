@extends('layouts.app')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">安装包管理</h1>
        <h1 class="pull-right">
           
        </h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>

        @include('flash::message')
        <div class="box box-primary">
            <div class="box-body">
                {!! Form::open(['url' => 'package/package','method'=>'GET']) !!}
                <div class="form-group col-sm-5">
                    {!! Form::label('name', '包名:') !!}
                    <label class="checkbox-inline">
                        {!! Form::text('name', null, ['class' => 'form-control']) !!}
                    </label>
                </div>

            <!--<div class="form-group col-sm-3">
            {!! Form::label('is_hot', 'Is Hot:') !!}
                    <label class="checkbox-inline">
{!! Form::select('is_hot', ['1'=>'是','0'=>'否']); !!}
                    </label>
                </div>-->
                <div class="form-group col-sm-5">
                    <!--            class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px"-->
                    <label class="checkbox-inline">
                        {!! Form::submit('搜索', ['class' => 'btn btn-primary pull-right']) !!}
                    </label>
                    <a class="btn btn-primary pull-right" style="margin-right:400px;margin-top: 0px;margin-bottom: 5px" href="{!! url('package/create') !!}">添加
                    </a>

                </div>

                {!! Form::close() !!}

            </div>
        </div>

        <div class="box box-primary">
            <div class="box-body">
                {!! Form::open(['url' => 'package/package','method'=>'GET']) !!}
                <div class="form-group col-sm-100">
                    {!! Form::label('name', 'app下载地址:') !!}</div>
                    <span style="color: red">下载地址规则参数:http://49.235.90.84:8893/api/apple/generatePackage?package_id=安装包Id&$apple_id=苹果账号ID</span></br>
                    <span style="color: red">示例:https://test.daoyuancloud.com/ipa?apple_id=3&package_id=2；此链接仅供测试，正式上线只需要传package_id即可</span>
                    <span style="color: red">{!! Form::text('version', "{$domain}/udid?package_id=2" , ['id'=>'qr_code','placeholder'=>'http://test.daoyuancloud.com/udid?apple_id=3&package_id=2','class' => 'form-control']) !!}</span>
                <span><div id="show_qr"></div></span>
                </br><span class="margin-right:-2200px"><input type="button" value="生成二维码" onclick="qrcode()"></span>
                </div>


                {!! Form::close() !!}

            </div>
        </div>

        <div class="clearfix"></div>
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
                        <th>ID</th>
                        <th>包名</th>
                        <th>图标</th>
                        <th>版本</th>
                        <th>BuddleID</th>
                        <th>IPA地址</th>
                        <th>证书</th>
                        <th>下载地址</th>
                        <th>简介</th>
                        <th>下载量</th>
                        <th>下载二维码</th>
                        <th>是否推送</th>
                        <th>创建时间</th>

                        <th colspan="3">操作</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($package as $data)
                        <tr>
                            <td>{!! $data->id !!}</td>
                            <td>{!! $data->name !!}</td>
                            <td> @if($data->icon!='')
                                    <img class="showimage" style="width: 100px;height: 100px" src="{{$data->icon}}">
                                @endif</td>
                            <td>
                                {!! $data->version !!}
                            </td>
                            <td>{!! $data->buddle_id !!}</td>
                            <td>{!! $data->ipa_url !!}</td>
                            <td>{!! $data->certificate_url !!}</td>
                            <td>
                                {!! $data->download_url !!}
                            </td>
                            <td>
                                {!! $data->introduction !!}
                            </td>
                            <td>
                                {!! $data->download_num !!}
                            </td>
                            <td>
                                {{--                {!! $domain."/udid?package_id=$data->id" !!}--}}
                                {!! QrCode::size(100)->color(0,0,0)->backgroundColor(0,255,0)->generate("$domain/udid?package_id=$data->id")!!}
                            </td>
                            <td>
                                {!! $data->is_push==0?'否':'是' !!}
                            </td>
                            <td>{!! $data->created_at !!}</td>
                            @if($data->user_id == $user_id || $role < 0)
                                <td>
                                    {!! Form::open(['route' => ['package.destroy', $data->id], 'method' => 'get']) !!}
                                    <div class='btn-group'>
                                        <a href="{!! url('package/edit?id='.$data->id) !!}" class='btn btn-default btn-xs'>修改</a>
                                        {!! Form::button('删除', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                                        <a href="{!! url('package/sonPackageList?package_id='.$data->id.'&type=2') !!}" class='btn btn-default btn-xs'>捆绑包</a>
                                    </div>
                                    {!! Form::close() !!}

                                </td>
                            @endif
                        </tr>
                    @endforeach

                    </tbody>

                </table>

            </div>
        </div>
        <div class="text-center">
            {!! $package->render() !!}
        </div>
    </div>
    <script>
        function qrcode(){
            var request_url = '/api/apple/qrcode';
            var qr_url = $('#qr_code').val();
            $.ajax({
                //请求方式
                type : "GET",
                //请求的媒体类型
                dataType: "json",
                //请求地址
                url : request_url,
                //数据，json字符串
                data : {url:qr_url},
                //请求成功
                success : function(data) {

                    if(data.status==1){
                        $('#show_qr').html(data.url);
                        //window.location.href= hostname+'/udid/udid.mobileconfig';
                    }else{
                        // alert(data.message);
                    }
                },
            });
        }

    </script>
@endsection

