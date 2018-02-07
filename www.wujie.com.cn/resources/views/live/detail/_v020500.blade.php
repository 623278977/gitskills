@extends('layouts.default')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/iscroll.css"/>
    <link href="{{URL::asset('/')}}/css/_v020500/livedetail.css?v=01120954" rel="stylesheet" type="text/css"/>
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
                            <div class="tc none nocomment" id="nocommenttip">暂无评论</div>
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
                    <div class="refreshpic"></div>
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
        </section>
    </section>
@stop
@section('endjs')
    <!--
    <script src="https://qzonestyle.gtimg.cn/open/qcloud/video/h5/h5connect.js" charset="utf-8"></script>
    -->
    <script src="https://qzonestyle.gtimg.cn/open/qcloud/video/live/h5/live_connect.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/iscroll.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/iscroll_touch.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/comment.js?v=12290930"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/_v020500/livedetail.js"></script>
    <script>
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