<aside class="main-sidebar">

    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
        <!-- Sidebar Menu -->
        <ul class="sidebar-menu" data-widget="tree">
            <!--            <li class="header">HEADER</li>-->
            <!-- Optionally, you can add icons to the links -->
            <!--            <li class="active"><a href="{{url('/home')}}"><i class="fa fa-link"></i> <span>游戏管理</span></a></li>-->
            <!--            <li><a href="#"><i class="fa fa-link"></i> <span>Another Link</span></a></li>-->
            <li class="active ">
                <!-- <a href="#"><i class="fa fa-user"></i> <span>苹果账号</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a> -->
                <ul class="treeview-menu">
                    <li class="@if($_SESSION['CurrentAction']=='apple') active @endif"><a href="{{url('apple/apple')}}">苹果账号</a></li>
                    <li class="@if($_SESSION['CurrentAction']=='device') active @endif"><a href="{{url('device/device')}}">设备管理</a></li>
                    <li class="@if($_SESSION['CurrentAction']=='package') active @endif"><a href="{{url('package/package')}}">安装包管理</a></li>
                   
                </ul>
            </li>
           <!--  <li class="active treeview">
                <a href="#"><i class="fa fa-link"></i> <span>好友管理</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="@if($_SESSION['CurrentAction']=='auto') active @endif"><a href="{{url('friend/auto')}}">加好友自动应答</a></li>
                    <li class="@if($_SESSION['CurrentAction']=='autoreply') active @endif"><a href="{{url('friend/autoreply')}}">私聊自动回复</a></li>
                    <li class="@if($_SESSION['CurrentAction']=='response') active @endif"><a href="{{url('friend/response')}}">自动加群成员成为好友</a></li>
                    <li class="@if($_SESSION['CurrentAction']=='nearby') active @endif"><a href="{{url('friend/nearby')}}">加附近的好友</a></li>
                    <li class="@if($_SESSION['CurrentAction']=='batchSearch') active @endif"><a href="{{url('friend/batchSearch')}}">批量搜索加好友</a></li>
                </ul>
            </li> -->


           <!--  <li class="active treeview">
                <a href="#"><i class="fa fa-link"></i> <span>社群管理</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
                    <li class="@if($_SESSION['CurrentAction']=='group') active @endif"><a href="{{url('group/group')}}">自动拉好友入群</a></li>
                    <li class="@if($_SESSION['CurrentAction']=='comeGroup') active @endif"><a href="{{url('group/comeGroup')}}">新人入群回复</a></li>
                    <li class="@if($_SESSION['CurrentAction']=='groupKeywordReply') active @endif"><a href="{{url('group/groupKeywordReply')}}">关键词自动回复</a></li>
                    <li class="@if($_SESSION['CurrentAction']=='wallet') active @endif"><a href="{{url('group/wallet')}}">红包自动回复</a></li>
                    <li class="@if($_SESSION['CurrentAction']=='link') active @endif"><a href="{{url('group/link')}}">链接自动回复</a></li>
                    <li class="@if($_SESSION['CurrentAction']=='groupSend') active @endif"><a href="{{url('group/groupSend')}}">话题/定时群发</a>
                        {{--<ul class="treeview-menu">--}}
                            {{--<li class="@if($_SESSION['CurrentAction']=='auto') active @endif"><a href="{{url('friend/auto')}}">自动拉好友入群</a></li>--}}
                        {{--</ul>--}}
                    </li>
                    <li class="@if($_SESSION['CurrentAction']=='groupSign') active @endif"><a href="{{url('group/groupSign')}}">群签到</a>

                    </li>
                    <li class="@if($_SESSION['CurrentAction']=='groupMember') active @endif"><a href="{{url('group/groupMember')}}">群成员管理</a>
                        <li class="active treeview">
                        <ul class="treeview-menu">
                            <li class="@if($_SESSION['CurrentAction']=='groupKick') active @endif"><a href="{{url('group/groupKick')}}">自动踢人</a></li>
                            <li class="@if($_SESSION['CurrentAction']=='groupComplain') active @endif"><a href="{{url('group/groupComplain')}}">投诉处理</a></li>

                        </ul>
                        </li>


                    </li>
                </ul>
            </li> -->

          
        </ul>
        <!-- /.sidebar-menu -->
    </section>
    <!-- /.sidebar -->
</aside>