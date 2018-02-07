@extends('layouts.default')
@section('css')
    <link rel="stylesheet" href="{{URL::asset('/')}}/css/swiper.min.css">
    <link href="{{URL::asset('/')}}/css/dist/act_detail.css?v=01171145" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section id="act_container" class="none">
        <!--安装app-->
        <div class="app_install fixed none" id="installapp">
            <i class="l">打开无界商圈APP，体验更多精彩内容 >></i>
            <span class="r" id="openapp"><img src="{{URL::asset('/')}}/images/install_btn.png" alt=""></span>
            <div class="clearfix"></div>
        </div>
        <!--邀请好友-->
        <div class="app_install fixed none" id="yaoqing">
            <i class="l">邀请好友注册无界商圈，获得免费门票</i>
            <span class="r" id="yaoqingbtn"><img src="{{URL::asset('/')}}/images/yaoqing.png" alt=""></span>
            <div class="clearfix"></div>
        </div>
        <section>
            <div class="swiper-container">
                <div class="swiper-wrapper"></div>
                <div class="swiper-pagination swiper-pagination-fraction"></div>
            </div>
            <div class="actitle f14"><span id="act_name">活动标题</span><span class="fr zb f12 none" id="zbicon">专版活动</span></div>
            <div class="f10 zbcontainer none" id="zbcontainer">
                <div class="fl zbname" id="zbname">专版的名字</div>
                <div class="color666 fl">购买专版会员，活动门票免费获得<br><i class="color999">*一切解释权归无界商圈所有</i></div>
                <div class="clearfix"></div>
            </div>
        </section>
        <section id="act_intro" class="mt0" style="padding-bottom: 7rem;">
            <div class="act_intro block" style="padding-top:0;">
                <div class="act_address" style="height: auto;">
                    <div class="time" id="aty_time">
                        <span class="time_icon mt1-25"></span>
                        <div class="infor">
                            <p id="act_time">12/14 10:00 - 15:00</p><span class="sj_icon mt1-25"></span>
                        </div>
                    </div>
                    <div class="wjb" id="aty_hostcitys">
                        <span class="city_icon"></span>
                        <div class="infor"><p id="citys">北京、上海、杭州、温州</p>
                            <p>活动现场</p><span class="sj_icon"></span></div>
                    </div>
                    <div class="wjb wjbrk" id="aty_ticket">
                        <span class="wjb_icon"></span>
                        <div class="infor"><p id="wjbNum">20元起</p>
                            <p id="tickettype">购买直播、现场门票</p><span class="sj_icon"></span></div>
                    </div>
                    <div class="wjb" id="aty_signs">
                        <span class="head_icon_li"></span>
                        <div class="infor no-bottom"><p id="bmNum">共57人已报名</p>
                            <p>报名人数</p><span class="sj_icon"></span></div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <!-- 活动介绍 -->
            <section class="block act_desp" id="actdescription">
            </section>
            <!-- 品牌展示 -->
            <section id="pinpai" class="brandcontain">
                <div class="brandtext f14">
                    <div class="brand_img"></div>
                    <div class="tc brand-text">相关品牌</div>
                </div>
            </section>
            <!--热度-->
            <section class="hotness f14 mt1 color666" id="hotness">
                <div class="hotnum color999" id="hotnum">热度<i></i></div>
                <div class="seen">浏览<span id="seen" class="color999">22次</span></div>
                <div class="dianzan topborder">点赞<span id="dianzan" class="color999">33次</span></div>
                <div class="plun topborder">评论<span id="plun" class="color999">43条</span></div>
                <div class="zhuan topborder">转发<span id="zhuan" class="color999">234次</span></div>
            </section>
            <!--赞-->
            <section class="hotness f14 mt1 color666" id="zancontain">
                <div class="hotnum">赞&nbsp;<em id="zan-number"></em><i></i></div>
                <div class="headicon" id="zan-images">
                </div>
                <div class="moremig none" id="moremig">
                    <div class="fr color999 f12" id="morezanimg" data-showdown="1">更多 ∨</div>
                </div>
            </section>
            <!--留言评论-->
            <section id="comment" class="mt1">
                <div class="commentnum f14 color666">评论&nbsp;<span id="commentnum">1029</span><i></i></div>
                <div id="thelist" class="bgfont">
                    <div class="block" id="allComment" style="margin-top: 0;">

                    </div>
                </div>
                <div id="pullUp" data-pagenow="1" style="display: none;">
                    <span class="pullUpLabel" style="display: none;"></span>
                </div>
                <div class="morecomment none" id="morecm" style="position: static;">加载更多</div>
            </section>
            <input type="hidden" data-src="" id="share_img">
            <div id="act_des" class="none" data-begintime="" data-collected="0"></div>
            <input type="hidden" data-maxid="0" data-minid="0" id="commentflag">
        </section>

        <!--分享按钮-->
        <div class="fixed_btn weixin none" id="loadAppBtn">
            <span class="downsapp width60 fl" id="loadapp"><img src="{{URL::asset('/')}}/images/downapp.png" alt=""></span>
            <span class="downsapp width40 f16 greenbc r" id="signnow">立即报名</span>
        </div>
        <!--浏览器打开提示-->
        <div class="safari none">
            <img src="{{URL::asset('/')}}/images/safari.png">
        </div>
    </section>
@stop
@section('endjs')
    <script src="{{URL::asset('/')}}/js/swiper.min.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/comment.js?v=12211738"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/v0240/src/actydetail.js?v=01131123"></script>
@stop