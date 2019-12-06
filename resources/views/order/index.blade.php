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
        @include('order.search')



        <div class="clearfix"></div>
        <div class="box box-primary">
            <div class="box-body">
                    @include('order.table')
            </div>
        </div>
        <div class="text-center">
            {!! $order->render() !!}
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

