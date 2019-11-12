<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
    <script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>IOS自动打包平台</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link rel="stylesheet" href="/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/bower_components/font-awesome/css/font-awesome.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="/bower_components/Ionicons/css/ionicons.min.css">
    <!-- datetimepicker -->
    <link rel="stylesheet" href="/bower_components/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
          page. However, you can choose any other skin. Make sure you
          apply the skin class to the body tag so the changes take effect. -->
    <link rel="stylesheet" href="/dist/css/skins/skin-blue.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->


    <!-- Google Font -->
    <!--  <link rel="stylesheet"-->
    <!--        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">-->
</head>
<!--
BODY TAG OPTIONS:
=================
Apply one or more of the following classes to get the
desired effect
|---------------------------------------------------------|
| SKINS         | skin-blue                               |
|               | skin-black                              |
|               | skin-purple                             |
|               | skin-yellow                             |
|               | skin-red                                |
|               | skin-green                              |
|---------------------------------------------------------|
|LAYOUT OPTIONS | fixed                                   |
|               | layout-boxed                            |
|               | layout-top-nav                          |
|               | sidebar-collapse                        |
|               | sidebar-mini                            |
|---------------------------------------------------------|
-->
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">


    <!-- Main Header -->
    @include('layouts.header')
    <!-- Left side column. contains the logo and sidebar -->
    @include('layouts.sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        @yield('content')
    </div>


    <!-- /.content-wrapper -->

    <!-- Main Footer -->
    @include('layouts.footer')

    <!-- Control Sidebar -->
    @include('layouts.tip')
    <!-- /.control-sidebar -->
    <!-- Add the sidebar's background. This div must be placed
    immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>

    <div id="imgtest" style="position:fixed; top:100px;  left:400px; z-index:10; "></div>

</div>
<!-- ./wrapper -->

<!-- REQUIRED JS SCRIPTS -->

<!-- jQuery 3 -->
<script src="/bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="/bower_components/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<!-- AdminLTE App -->
<script src="/dist/js/adminlte.min.js"></script>
<script src="/js/anyupload.js"></script>
<script>
    $("[any-upload]").Anyupload({
        csrf_token: '{{ csrf_token() }}',
        cb: function (url, name) {
            $("input[name='scheme']").val(name);
        }
    });

    $(function () {
        var size = 3.0 * $('.showimage').width();
        $(".showimage").click(function (event) {
            var $target = $(event.target);
            if ($target.is('img')) {

                $("#tip").remove();
                /*移除元素*/
                var img = new Image();
                img.src = $(this).attr('src');

                var ww = $(window).width();
                var wh = $(window).height();

                var w = img.width;
                var h = img.height;

                var pt = Math.floor((wh - h) / 2 * 0.8);
                var pf = Math.floor((ww - w) / 2);


                $('#imgtest').css({
                    'top': pt,
                    'left': pf,
                });

                $("<img id='tip' src='" + $target.attr("src") + "'>").css({
                    "height": h,
                    "width": w,
                }).appendTo($("#imgtest"));

                /*将当前所有匹配元素追加到指定元素内部的末尾位置。*/
            }
        });

        $('#imgtest').click(function () {
            $("#tip").remove();
            /*移除元素*/
        });



    });

    // $('.datepicker').datepicker({
    //     format: 'yyyy-mm-dd hh:ii:ss'
    // });
    // $(".default_datetimepicker").datetimepicker({
    //     format:'yyyy/mm/dd hh:ii',
    //     language:'zh-CN',
    //     minView: "month",
    //     todayBtn:  1,
    //     autoclose: 1,
    // })

    $('.default_datetimepicker').datetimepicker({
        format:'yyyy/mm/dd hh:ii',
        language:'zh-CN',
        //defaultDate:'8.12.1986', // it's my birthday
        // defaultDate:'+03.01.1970', // it's my birthday
        defaultTime:'00:00',
        timepickerScrollbar:false
    });

</script>

<!-- Optionally, you can add Slimscroll and FastClick plugins.
     Both of these plugins are recommended to enhance the
     user experience. -->
</body>
</html>