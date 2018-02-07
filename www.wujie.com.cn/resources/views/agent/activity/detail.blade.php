@extends('layouts.default')
@section('css')
    <!-- <link href="{{URL::asset('/')}}/css/_v020700/brand.css" rel="stylesheet" type="text/css"/> -->
    <link rel="stylesheet" href="{{URL::asset('/')}}/css/swiper.min.css">
    <link href="{{URL::asset('/')}}/css/_v020700/actdetail.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/_v020700/act020700.css" rel="stylesheet" type="text/css"/> 
    <link href="{{URL::asset('/')}}/css/v010000/activity.css" rel="stylesheet" type="text/css"/> 
@stop
@section('main')
    <section id="act_container" class="none">
        <!--安装app-->
        <div class="app_install fixed none" id="installapp">
            <i class="l">打开无界商圈APP，体验更多精彩内容 >></i>
            <span class="r" id="openapp"><img src="{{URL::asset('/')}}/images/yaoqing.png" alt=""></span>
            <div class="clearfix"></div>
        </div>
        <!--邀请好友-->
        <div class="app_install fixed none" id="yaoqing">
            <i class="l">邀请好友注册无界商圈，获得免费门票</i>
            <span class="r" id="yaoqingbtn"><img src="{{URL::asset('/')}}/images/yaoqing.png" alt=""></span>
            <div class="clearfix"></div>
        </div>

        <div id="share">
            <!-- <p class="f12 l">分享活动，立即获得100积分</p>
            <button class="c00a0ff l f12 understand"><img src="{{URL::asset('/')}}/images/020700/notice.png" alt="">了解更多分享机制</button>
            <span class="fff f16 r close_share"><img src="{{URL::asset('/')}}/images/share_close.png" alt=""></span> -->
        </div>
       <!--  活动图片 -->
        <section>
            <div class="swiper-container">
                <div class="swiper-wrapper"></div>
                <div class="swiper-pagination swiper-pagination-fraction"></div>
               <!--  <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div> -->
            </div>
        </section>
        <!-- 活动名称 -->
        <section id="act_intro" class="mt0" style="padding-bottom: 7rem;">
           <!--  活动标题 -->
            <div class="actitle f16 fline color333" style="margin-bottom:1rem;height:">
                    <!-- <span id="act_name"  class="b "></span>
                    <div id="baoming" class="baoming fr"></div> -->
                    <ul class="ui_title">
                        <li><p id="act_name" class="b margin0 ui-nowrap-multi"></p></li>
                        <li><div id="baoming" class="baoming"></div></li>
                    </ul>
                    <div style="width:100%;height:0.01rem;clear:both"></div>
            </div>
            <!-- 活动时间及地点 -->
            <div class="act_intro pl1-33 fline bgwhite" style="margin-top:1.2rem;">     
                <div class="act_address" style="height: auto;">
                    <div class="time" id="aty_time">
                        <span class="mt2-25 time_icon_" style="margin-top: 1.7rem"></span>
                        <div class="infor fline">
                           <!--  <p id="act_time">12/14 10:00 - 15:00</p> -->
                            <p class="c8a" style="width:100%;padding-right: 1.33rem">
                                <span style="margin-top:0.5rem;color:#333">活动时间</span>
                                <span id="act_time" style="float:right;margin-top:0.5rem">
                                 12/14 10:00 - 15:00
                                </span>
                            </p>
                           <!--  <span class="sj_icon mt2-25"></span> -->
                    
                        </div>
                    </div>
                    <div class="wjb" id="aty_hostcitys">
                        <span class="city_icon_" style="margin-top: 1.7rem"></span>
                        <div class="infor fline">
                       <!--  <p id="citys">北京、上海、杭州、温州</p> -->
                            <p class="c8a" style="width:100%;padding-right: 1.33rem">
                                 <span style="margin-top:0.5rem;color:#333">活动地点</span>
                                <span id="citys" style="float:right;margin-top:0.5rem">
                                北京、上海、杭州、温州
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div style="width:100%;height:1rem;background:#f2f2f2 "></div>
            <!-- 活动介绍 -->
            <section class="bgwhite act_desp tline" id="actdescription" style="width:100%;padding-right: 1.33rem"></section>
            <!-- 品牌展示 -->
            <section id="pinpai" class="brandcontain">
                <div class="brandtext f16 fline b">
                   相关品牌
                </div>
            </section>
            <input type="hidden" data-src="" id="share_img">
            <div id="act_des" class="none" data-begintime="" data-collected="0"></div>
            <input type="hidden" data-maxid="0" data-minid="0" id="commentflag">
            <input type="hidden" id="sharemark">
        </section>
       <!--  底部按钮 -->
        <button class="fixedbottom">邀请客户报名</button>
        <!--浏览器打开提示-->
        <div class="safari none">
            <img src="{{URL::asset('/')}}/images/safari.png">
        </div>
        <ul class="ui_share " style="display:none">
            <li><img src="/images/downapp.png"></li>
            <li>立即报名</li>
        </ul>
    </section>
@stop
@section('endjs')
    <script src="{{URL::asset('/')}}/js/swiper.min.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/agent/activity.js"></script>
@stop