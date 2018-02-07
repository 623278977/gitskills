@extends('layouts.default')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/iscroll.css"/>
    <link href="{{URL::asset('/')}}/css/_v020500/livedetail.css?v=03162002" rel="stylesheet" type="text/css"/>
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
    <section class="containerBox" id="containerBox" style="visibility: hidden">
        <!--预告、收费提示-->
        <div class="share_video top0 f12" id="share_video">
        
        </div>
        <div id="video_box" class="share_video none" style="top:3.5rem;"></div>
        <section class="live_detail pt23-2875" id="live_detail">
            <!--直播概况-->
            <section id="live_introduce" class="">
                <!--活动介绍-->
                <div id="bind_activity" class="activity_info f14 none bgwhite">
                </div>
                <!--嘉宾结束-->
                <div id="bind_guest" class="bgwhite pl1-33 mt1-33 guest none">
                    <div class="brand-title fline f16">相关嘉宾</div>
                </div>
                <!--相关品牌-->
                <div id="bind_brand" class="bgwhite pl1-33 mt1-33 none">
                    <div class="brand-title f16">相关品牌</div>
                </div>
                <!--直播介绍-->
                <div class="mt1-33 bgwhite pl1-33">
                    <div class="brand-title fline f16">直播介绍</div>
                    <div class="live_info f12" id="live_info">
                    </div>
                </div>
                <div class="f12 color999 tc pt4 pb4">没有更多了</div>
            </section>
            <!--立即加盟-->
            <section id="barnd_list" class="none">

            </section>
            <!--互动栏-->
            <section id="comment" class="none">
                <div id="wrapper" class="top31-7875" style="padding-bottom: 4rem;">
                    <div id="scroller">
                        <div id="pullDown" style="display: none;">
                            <span class="pullDownLabel" style="display: none;"></span>
                        </div>
                        <div class="clearfix"></div>
                        <div id="thelist" class="bgfont">
                            <div class="tc none nocomment" id="nocommenttip">
                                <img src="{{URL::asset('/')}}/images/nomessage_icon1.png" style="height: 16rem;width: 16rem;margin:0 auto;display: inline-block;">
                            </div>
                            <div class="comment_tit amazeComment none">精彩评论（<em class="num">0</em>）</div>
                            <div class="block none" id="amazeComment"></div>
                            <div class="comment_tit allComment none">全部评论（<em class="num">0</em>）</div>
                            <div class="livecommentblock none" id="allComment">

                            </div>
                        </div>
                        <div id="pullUp" data-pagenow="1" style="display: none;" class="">
                            <span class="pullUpLabel" style="display: none;"></span>
                        </div>
                        <div class="morecomment none" id="morecm">加载更多</div>
                    </div>
                    <div class="refreshpic1"></div>
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
    <script type="text/javascript" src="{{URL::asset('/')}}/js/iscroll.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/iscroll_touch.js"></script>
    <script src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
    <script src="{{URL::asset('/')}}/js/intlTelInput.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/comment.js?v=12290930"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/_v020500/live_020502.js?v=03162003"></script>
    <script> 
        $('#zcphone').intlTelInput();
        $('#yyphone').intlTelInput();
        //加载
        var touch = $.extend({}, {
            getAjaxDownData: function () {
                // myScroll.refresh();
            },
            getAjaxUpData: function () {
                //myScroll.refresh();
            }
        });
        //分享
        function showShare() {
            var title = $('#livesubject').text();//直播标题
            var img = $('#share_img').data('src');
            var header = '直播';
            var content = cutString($('#live_info').text(), 18);//直播介绍
            if($('#livesubject').data('distribution_id') > 0){
                var args = getQueryStringArgs(),
                        live_id = args['id'] || '0';
                var pageUrl = window.location.href + '&share_mark=' + $('#livesubject').data('share_mark');//用来追踪原始分享者
                var share_mark = $('#livesubject').data('share_mark');
                var url = labUser.api_path + '/index/code/_v020500';
                ajaxRequest({}, url, function (data) {
                    var code = data.message;//code
                    pageUrl = pageUrl + '&code=' + code;
                    shareOut(title, pageUrl, img, header, content, '', '', '', '', share_mark, code, 'share', 'live', live_id);//分享
                });
            }
            else{
                var pageUrl = window.location.href;
                shareOut(title, pageUrl, img, header, content,'','','','','','','','','');//分享
            }
        }
        //刷新
        function reload() {
            location.reload();
        }
        //刷新评论
        function Refresh() {
            var parameter = {
                "type": 'Live',
                "id": "<?php echo $id;?>",
                "uid": labUser.uid,
                "fromId": $('#commentflag').data('maxid'),
                "update": "new",
                "fecthSize": 0
            };
            Comment.getFreshList(parameter);
        }
    </script>
@stop