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
        @include('package.search')

        <div class="box box-primary">
            <div class="box-body">
                {!! Form::open(['url' => 'package/package','method'=>'GET']) !!}
                <div class="form-group col-sm-100">
                    {!! Form::label('name', 'app下载地址:') !!}</div>
                    <span style="color: red">下载地址规则参数:http://49.235.90.84:8893/api/apple/generatePackage?package_id=安装包Id&$apple_id=苹果账号ID</span></br>
                    <span style="color: red">示例:https://test.daoyuancloud.com/udid?apple_id=3&package_id=2；此链接仅供测试，正式上线只需要传package_id即可</span>
                    <span style="color: red">{!! Form::text('version', "{$domain}/udid?apple_id=3&package_id=2" , ['id'=>'qr_code','placeholder'=>'http://test.daoyuancloud.com/udid?apple_id=3&package_id=2','class' => 'form-control']) !!}</span>
                <span><div id="show_qr"></div></span>
                </br><span class="margin-right:-2200px"><input type="button" value="生成二维码" onclick="qrcode()"></span>
                </div>


                {!! Form::close() !!}

            </div>
        </div>

        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                    @include('package.table')
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

