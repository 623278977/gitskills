@extends('layouts.default')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/iscroll.css"/>
    <link href="{{URL::asset('/')}}/css/act_detail.css?v=1.0.0" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section class="containerBox">
      
     
        <section class="videonews_box">
          
            <!-- 活动介绍 -->
            <section id="act_intro">
                <div class="act_intro block">
                    <dl>
                        <dt class="act_pics"><img src="" alt="" id="act_picsrc"></dt>
                        <dd class="act_name" id="act_name">活动标题</dd>
                        <div class="clearfix"></div>
                        <div class="zbrow none" id="zbrow">
                            <span class="zbflag r3">专版活动</span>
                            <span class="pl1">查看<i id="zbname" class="green pl05" data-zbid="0">中美教育</i>专版</span>
                            <span class="sj_icon top105"></span>
                        </div>
                        <div class="act_address">
                            <dd class="time">
                                <span class="time_icon"></span>
                                <div class="infor" id="timetop">
                                    <p id="act_time"></p>
                                </div>
                            </dd>
                            <div id="address_flag"></div>
                        </div>
                        <div class="clearfix"></div>
                    </dl>
                    <div class="seen_more tc down none">查看更多地址 &or;</div>
                    <div class="seen_more tc up none">收起 &and;</div>
                </div>
                <!-- 视频详情 -->
                <div class="block video_detail">
                    <div class="text" id="video_description">活动详情描述</div>
                    <div class="seen_more topborder" id="videosMoreDetail">更多详情 <span class="sj_icon"></span></div>
                </div>
                <!-- 相关视频 -->
                <div class="about_video">
                    <nav class="nav">
                        <span class="line l"></span><span class="l tc nav_text">相关视频</span><span class="line r"></span>
                    </nav>
                    <div class="block">
                        <ul id="recommend_video">

                        </ul>
                        <div class="seen_more topborder" id="seenmoreVideo">更多视频<span class="sj_icon"></span></div>
                    </div>
                </div>
            </section>
           
        </section>
       
        <!--分享出去按钮-->
        <div class="fixed_btn weixin none" id="loadAppBtn">
            <button class="signup" id="loadapp">下APP</button>
        </div>
        <!--浏览器打开提示-->
        <div class="safari none">
            <img src="{{URL::asset('/')}}/images/safari.png">
        </div>
        <div class="none" id="video_title_none"></div>
        <div class="none" id="video_descript_none"></div>
        <div class="none" id="endtime_none"></div>
        <div class="isFavorite"></div>
    </section>
@stop

@section('endjs')
    <script src="http://qzonestyle.gtimg.cn/open/qcloud/video/h5/h5connect.js" charset="utf-8"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/iscroll.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/iscroll_touch.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/comment.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/collect.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/videodetail.js"></script>
    
@stop