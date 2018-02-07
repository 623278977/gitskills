@extends('layouts.default')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/iscroll.css"/>
    <link href="{{URL::asset('/')}}/css/_v020700/livedetail.css?v=03162002" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/agent/live.css?v=03162002" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/intlTelInput.css" rel="stylesheet" type="text/css"/>
    <style>
        .intl-tel-input.inside input[type="text"], .intl-tel-input.inside input[type="tel"] {
            padding-left: 7.5rem;
        }
        .intl-tel-input {
            position: relative;
            display: block;
            height: 100%;
        }
        .intl-tel-input .flag-dropdown {
            left: 3rem;
        }
    </style>
@stop
@section('main')

    <section class="containerBox bgcolor" id="containerBox" >
        <!--预告、收费提示-->
        <div class="share_video top0 f12" id="share_video">  
              
        </div>
        <!-- 视频播放的盒子 -->
        <div id="video_box" class="share_video none" style="top:3.5rem;"></div>
        <!-- <div class="c2873 navbar tc" style='top:23.2875rem;'>详情</div> -->
        <!-- <div style='height:1.33rem;background-color: #f2f2f2;position: fixed;'></div> -->
        <section class="live_detail pt23-2875"  id="live_detail">
            <!--直播概况-->
            <section id="live_introduce" class="">
                 <!--活动信息-->
                <div id="bind_activity" class="activity_info f14 none bgwhite">
                    <div class="brand-title f16">活动信息</div>
                </div>   
                <!-- 直播信息-->
                <div class="mt1-33 bgwhite pl1-33">
                    <div class="brand-title fline f16 mb1">直播信息</div>
                    <div class=" f12 color666 pr1-33">
                        <div class="l live_img "><img src="http://www.wjsq3.com/images/default/small-pro.jpg" alt="" id="basic_liveimg"></div>
                        <div class="live_intro" id="basic_liveinfo">
                            <!-- <p class="f16 mb02 h5-2 color333">这里是标题</p><p class="f12 c8a">直播时间：<span></span></p> -->
                        </div>
                        <div class="clearfix"></div>
                        <p style="margin-top: 1rem; height:1px;background: #e5e5e5;transform: scale(1,0.5);"></p>
                        <div class="f12 color999">
                            <p class=" mb05 color333">详情</p>
                            <div class="live_info f12 " id="live_info" style='padding-top:0;'></div>
                        </div>
                    </div>
                    
                </div>
                <!--品牌信息-->
                <div id="bind_brand" class="bgwhite pl1-33 mt1-33 none">
                    <div class="brand-title f16">品牌信息</div>
                </div>
            </section>
           
            <!--直播回放栏-->
            <section id="live_video" class="mt1-33 none">
                <!--相关视频列表-->
                <section class="bgwhite pl1-33">
                    <div class="brand-title f16">相关视频</div>
                    <ul id="relativevideo" class="more_video">
                        <li class="ui-border-t" style="padding-top:0;padding-bottom: 0;">
                            <div class="novideo"><img src="/images/liveflag.png"/>
                                <p class="color999">视频还在制作中，请耐心等待 ~ </p></div>
                        </li>
                    </ul>
                </section>
                <!--相关资讯-->
                <section class="bgwhite pl1-33 mt1-33 none" id="messagecont">
                    <div class="brand-title f16">相关资讯</div>
                    <ul id="relativemessage" class="relativemessage">

                    </ul>
                </section>
                <section>
                    <div class="f12 color999 tc pt4 pb4">没有更多了</div>
                </section>
            </section>

            <!--分享图片-->
            <input type="hidden" data-src="" id="share_img"/>
            <input type="hidden" data-maxid="0" data-minid="0" id="commentflag"/>
            <div class="none" id="livesubject" data-begintime="" data-livenum="0" data-share_mark=""
                 data-relation_id=""></div>

            <!-- 分享页快速注册 -->
            <div class="remindpart none" id="registerpart" style="z-index:197;">
                <div class="content">
                    <div class="tiptitle f18 tc remindfontcolor">快速登录/注册,观看直播：</div>
                    <div class="userinput remindcolor">
                        <div class="putdiv remindcolor f12 successtitle">登录无界商圈APP，更多高清视频等你来观看</div>
                        <div class="putdiv remindcolor height06"><input type="text" name="phonenumber" value="+86 " placeholder="手机号" id="zcphone"/></div>
                        <div class="putdiv remindcolor height06"><input type="text" name="mescode" placeholder="短信验证码" id="zcyzm"/><button class="ident_code" id="mescode">获取验证码</button></div>
                        <div class="putdiv remindcolor tc"><button class="subbtn f16" id="registerbtn">提交</button></div>
                    </div>
                    <div class="closepic"></div>
                </div>
            </div>
            <div class="remindpart none" id="liveremind" style="z-index:196;">
                    <div class="content">
                        <div class="tiptitle f18 tc remindfontcolor">设置直播提醒</div>
                        <div class="userinput remindcolor">
                            <div class="f12 putdiv">
                                <div class="pdiv">
                                    <p id="livename"></p>
                                    <p id="livetime"></p>
                                </div>
                            </div>
                            <div class="putdiv remindcolor f12 tiptexts">
                                可以订阅该场直播，我们将在 直播开始前30分钟 以短信发送直播提醒消息
                            </div>
                            <div class="putdiv remindcolor height06">
                                <input type="text" name="phonenumber" value="+86 " placeholder="手机号" id="yyphone"/>
                            </div>
                            <div class="putdiv remindcolor height06">
                                <input type="text" name="mescode" placeholder="短信验证码" id="yyyzm"/>
                                <button class="ident_code" id="getcode">获取验证码</button>
                            </div>
                            <div class="putdiv remindcolor tc">
                                <button class="subbtn f16" style="margin-top: 1rem;" id="yysubmit">提交</button>
                            </div>
                        </div>
                        <div class="closepic"></div>
                    </div>
            </div>
        </section>
    </section>
    @stop
    @section('endjs')
    <script src="https://qzonestyle.gtimg.cn/open/qcloud/video/live/h5/live_connect.js"></script>
    <script src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
    <script src="{{URL::asset('/')}}/js/intlTelInput.js"></script>
    <!-- <script type="text/javascript" src="{{URL::asset('/')}}/js/comment.js?v=12290930"></script> -->
    <script type="text/javascript" src="{{URL::asset('/')}}/js/agent/liveDetail.js?v=20170814"></script>
    <script> 
        var $body = $('body');
        document.title = "直播详情";
          // hack在微信等webview中无法修改document.title的情况
          var $iframe = $('<iframe ></iframe>').on('load', function() {
          setTimeout(function() {
          $iframe.off('load').remove()
          }, 0)
        }).appendTo($body);

        $('#zcphone').intlTelInput();
        $('#yyphone').intlTelInput();
        //分享
        function showShare() {
            var args = getQueryStringArgs(),
                id = args['id'] || '0';
            var title = $('#livesubject').text();//直播标题
            var img = $('#share_img').data('src');
            var header = '直播';
            var content = cutString(removeHTMLTag($('#live_info').text()), 18);//直播介绍         
            var pageUrl = labUser.path + 'webapp/live/detail/_v020800?id='+id+'&uid=0&is_share=1';
            var sharecontent=$('#share_img').data('sharecontent');
            var contain=sharecontent.length<20?sharecontent:sharecontent.substr(0,20)+'…';
            if(sharecontent){
                shareOut(title, pageUrl, img, header,contain,'','','','live','','','','','');//分享 
              }else{
                shareOut(title, pageUrl, img, header, content,'','','','live','','','','','');//分享 
              }
            
        };
        //分享给客户
        $(document).on('click','.shareTocus',function(){
            showShare();
        })
      
    </script>
@stop