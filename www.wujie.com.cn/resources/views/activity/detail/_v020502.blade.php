@extends('layouts.default')
@section('css')
    <link rel="stylesheet" href="{{URL::asset('/')}}/css/swiper.min.css">
    <link href="{{URL::asset('/')}}/css/_v020500/actdetail.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/_v020500/actdetail_02.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section id="act_container" class="none">
        <!--安装app-->
        <div class="app_install fixed none" id="installapp">
            <i class="l">打开无界商圈APP，体验更多精彩内容 >></i>
            <span class="r" id="openapp"><img src="{{URL::asset('/')}}/images/020502/install_btn.png" alt=""></span>
            <div class="clearfix"></div>
        </div>
        <!--邀请好友-->
        <div class="app_install fixed none" id="yaoqing">
            <i class="l">邀请好友注册无界商圈，获得免费门票</i>
            <span class="r" id="yaoqingbtn"><img src="{{URL::asset('/')}}/images/020502/yaoqing.png" alt=""></span>
            <div class="clearfix"></div>
        </div>

        <div class="share pl1-33 pr1-33 a_share" id="share" style='background-color:rgba(255,90,0,0.7) '>
            <p class="f12 l">分享活动，立即获得100积分</p>
            <button class="ff5 l f12 understand"><img src="{{URL::asset('/')}}/images/020502/notice.png" alt="">了解分享规则介绍</button>
            <span class="fff f16 r close_share"><img src="{{URL::asset('/')}}/images/share_close.png" alt=""></span>
        </div>
        <section>
            <div class="swiper-container">
                <div class="swiper-wrapper"></div>
                <div class="swiper-pagination swiper-pagination-fraction"></div>
            </div>
            <!-- <div class="actitle f14"><span id="act_name">活动标题</span><span class="fr zb f12 none" id="zbicon">专版活动</span></div> -->
            <!-- <div class="f10 zbcontainer none" id="zbcontainer">
                <div class="fl zbname" id="zbname">专版的名字</div>
                <div class="color666 fl">购买专版会员，活动门票免费获得<br><i class="color999">*一切解释权归无界商圈所有</i></div>
                <div class="clearfix"></div>
            </div> -->
        </section>
        <section id="act_intro" class="mt0" style="padding-bottom: 7rem;">
            <div class="actitle f16 fline color333"><span id="act_name" class="b">活动标题</span></div>
            <div class="act_intro pl1-33 fline bgwhite" style="padding-top:0;">     
                <div class="act_address" style="height: auto;">
                    <div class="time" id="aty_time">
                        <span class="time_icon_v5 mt2-25"></span>
                        <div class="infor fline">
                            <p id="act_time">12/14 10:00 - 15:00</p>
                            <p class="c8a">活动时间</p>
                            <span class="sj_icon mt2-25"></span>
                        </div>
                    </div>
                    <div class="wjb" id="aty_hostcitys">
                        <span class="city_icon_v5"></span>
                        <div class="infor fline"><p id="citys">北京、上海、杭州、温州</p>
                            <p class="c8a">活动地点</p><span class="sj_icon"></span></div>
                    </div>
                    <div class="wjb wjbrk" id="aty_ticket">
                        <span class="wjb_icon_v5"></span>
                        <div class="infor fline"><p id="wjbNum">20元起</p>
                            <p id="tickettype" class="c8a">购买直播、现场门票</p><span class="sj_icon"></span></div>
                    </div>
                    <div class="wjb" id="aty_signs">
                        <span class="head_icon_li_v5"></span>
                        <div class="infor no-bottom"><p id="bmNum">共57人已报名</p>
                            <p class="c8a overNum">报名人数</p><span class="sj_icon"></span></div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <!-- 活动介绍 -->
            <section class="bgwhite act_desp tline" id="actdescription">
            </section>
            <!-- 品牌展示 -->
            <section id="pinpai" class="brandcontain">
                <div class="brandtext f16 fline b">
                   相关品牌
                </div>
            </section>
            
            <!--热度-->
            <section class="hotness f14 mt1 color666" id="hotness">
                <div class="hotnum color333 fline b" id="hotnum">热度</div>
                <div class="seen fline">浏览<span id="seen" class="color999">22次</span></div>
                <div class="dianzan fline">点赞<span id="dianzan" class="color999">33次</span></div>
                <div class="plun fline ">评论<span id="plun" class="color999">43条</span></div>
                <div class="zhuan">转发<span id="zhuan" class="color999">234次</span></div>
            </section>
            <!--赞-->
            <section class="hotness f14 mt1 color666" id="zancontain">
                <div class="hotnum fline color333 b">赞&nbsp;<em id="zan-number" class="ff5 "></em></div>
                <div class="headicon" id="zan-images">
                </div>
                <div class="moremig none" id="moremig">
                    <div class="fr color999 f12" id="morezanimg" data-showdown="1">更多 ∨</div>
                </div>
            </section>
            <!--留言评论-->
            <section id="comment" class="mt1 pl1-33 bgwhite fline" style='padding:0 0 0 1.333rem;'>
                <div class="commentnum f14 color333 b fline">评论&nbsp;<span id="commentnum" class="ff5 ">1029</span></div>
                <div id="thelist" class="bgfont">
                    <ul class="pr1-33" id="allComment" style="margin-top: 0;">

                    </ul>
                </div>
                <div id="pullUp" data-pagenow="1" style="display: none;">
                    <span class="pullUpLabel" style="display: none;"></span>
                </div>
                
            </section>
            <button class="getMore f12 c8a">点击加载更多</button>
            <!-- 评论框 -->
             <div class="commentback none" id="commentback">
                <div style="width: 100%;height:100%;position:absolute;bottom:15rem;left:0;" id="tapdiv"></div>
                <div class="textareacon">
                    <textarea class="f12" name="comment" id="comtextarea" cols="30" rows="10" style="resize: none;" placeholder="请输入评论内容"></textarea>
                    <button class="fr subcomment f16" id="subcomments">发表</button>
                </div>
            </div>
            <input type="hidden" data-src="" id="share_img">
            <div id="act_des" class="none" data-begintime="" data-collected="0"></div>
            <input type="hidden" data-maxid="0" data-minid="0" id="commentflag">
            <input type="hidden" id="sharemark">
        </section>

        <!--分享按钮-->
        <div class="fixed_btn weixin none" id="loadAppBtn">
            <span class="downsapp width60 fl" id="loadapp"><img src="{{URL::asset('/')}}/images/020502/downapp.png" alt=""></span>
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
    <script type="text/javascript" src="{{URL::asset('/')}}/js/_v020500/actydetail_02.js"></script>
    <script type='text/javascript'>
        //评论按钮颜色变化
            $('#comtextarea').on('keyup',function(){
                $('#subcomments').css('backgroundColor','#ff5a00');
                if($('#comtextarea').val()==''){
                     $('#subcomments').css('backgroundColor','#999');
                }
            });
         // 点击灰框评论消失
            $(document).on('tap','#tapdiv',function(){
                $('#commentback').addClass('none');
            });
        //关闭分享机制提醒
             $(document).on('tap','.close_share',function(){
                $('.share').addClass('none');
             });
        //了解更多分享机制
            $(document).on('tap','.understand',function(){
                window.location.href=labUser.path+'webapp/protocol/moreshare/_v020500?pagetag=025-4';
            })
        //输入框
            $('#comtextarea').on('focus', function () {
                setTimeout(function () {
                    var c = window.document.body.scrollHeight;
                    window.scroll(0, c);
                }, 500);
                return false;
            });
    </script>

@stop