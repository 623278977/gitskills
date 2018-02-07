@extends('layouts.default')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/iscroll.css"/>
    <link href="{{URL::asset('/')}}/css/act_detail.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section class="containerBox none">
        <div class="videotoptip none f12">本视频为专版收费视频，建议购买专版会员，享受更多优惠</div>
        <!--打开app-->
        <div class="install none" id="installapp">
            <p class="l">打开无界商圈APP，观看完整高清视频 >> </p>
            <span class="r" id="openapp"><img src="{{URL::asset('/')}}/images/install_btn.png" alt=""></span>
            <div class="clearfix"></div>
        </div>
        <!--直播分享-->
        <div class="share_video none">
            <img src="{{URL::asset('/')}}/images/live.png" alt="">
            <p class="share_text">
                <button class="order none" id="loginbtn">登录</button>
                </br>
                本场点播为收费点播
                请登录无界商圈购买并观看
            </p>
        </div>
        <div id="video_box"></div>
        <section class="videonews_box">
            <!--介绍、评论分栏-->
            <nav class="column TwoColumn">
                <span class="green" type="act_intro">活动介绍</span>
                <span style="width:2%;color:#ddd;">|</span>
                <span type="comment">评论留言</span>
            </nav>
            <!-- 活动介绍 -->
            <section id="act_intro">
                <div class="act_intro block">
                    <dl>
                        <dt class="act_pics"><img src="" alt="" id="act_picsrc"></dt>
                        <dd class="act_name" style="height:7.4rem;">
                            <p id="act_name" data-act_id=""></p>
                            <div class="dark_gray" style="position:absolute;bottom:0;">
                                <span class="seen"><i class="seen_icon"></i><em id="seenNum">22</em></span>
                            </div>
                        </dd>
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
                    <div class="seen_more topborder" id="videosMoreDetail" data-type="video" value="">更多详情 <span
                                class="sj_icon"></span></div>
                </div>
                <!-- 相关视频 -->
                <div class="about_video">
                    <nav class="nav">
                        <span class="line l"></span><span class="l tc nav_text">相关视频</span><span class="line r"></span>
                    </nav>
                    <div class="block">
                        <ul id="recommend_video">

                        </ul>
                        <div class="seen_more topborder" id="seenmoreVideo" value="">更多视频<span class="sj_icon"></span>
                        </div>
                    </div>
                </div>
            </section>
            <!--留言评论-->
            <section id="comment" class="none">
                <div id="wrapper" class="top27">
                    <div id="scroller">
                        <div id="pullDown" class="none" style="display: none;">
                            <span class="pullDownLabel" style="display: none;"></span>
                        </div>
                        <div class="clearfix"></div>
                        <div id="thelist" class="bgfont">
                            <div class="tc none nocomment" id="nocommenttip">暂无评论</div>
                            <div class="comment_tit amazeComment none">精彩评论（<em class="num"></em>）</div>
                            <div class="block none" id="amazeComment"></div>
                            <div class="comment_tit allComment none">全部评论（<em class="num">0</em>）</div>
                            <div class="block none" id="allComment"></div>
                        </div>
                        <div id="pullUp" data-pagenow="1" style="display: none;">
                            <span class="pullUpLabel" style="display: none;"></span>
                        </div>
                        <div class="morecomment none" id="morecm">加载更多</div>
                    </div>
                </div>
            </section>
        </section>
        <!--评论按钮-->
        <div class="comment_btn none">
            <button type="button" class="tl">及时互动，分享智慧...</button>
            <span class="uploadpic"></span>
            <span class="awardpic"></span>
        </div>
        <!--评论框-->
        <div class="comment_input full none" id="commentcontain">
            <input type="" placeholder="">
            <button id="send_comment">评价</button>
        </div>
        <!--评论框-->
        <div class="commentback none" id="commentback">
            <div style="width: 100%;height:100%;position:absolute;bottom:15rem;left:0;" id="tapdiv"></div>
            <div class="textareacon">
        <textarea class="f12" name="comment" id="comtextarea" cols="30" rows="10" style="resize: none;"
                  placeholder="优质评论将会被优先展示"></textarea>
                <button class="fr subcomment f16" id="subcomments">发表</button>
            </div>
        </div>
        <!--分享出去按钮-->
        <div class="fixed_btn weixin none" id="loadAppBtn">
            <button class="signup" id="loadapp">下载APP</button>
        </div>
        <!--浏览器打开提示-->
        <div class="safari none">
            <img src="{{URL::asset('/')}}/images/safari.png">
        </div>
        <input type="hidden" data-src="" id="share_img">
        <div class="none" id="video_title_none"></div>
        <div class="none" id="video_descript_none"></div>
        <div class="none" id="endtime_none"></div>
        <div class="isFavorite"></div>
    </section>
    <div class="bigimg none" id="bigimg">
        <img alt="" src="" id="showimg">
    </div>
@stop

@section('endjs')
    <script src="https://qzonestyle.gtimg.cn/open/qcloud/video/h5/h5connect.js" charset="utf-8"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/iscroll.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/iscroll_touch.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/comment.js?v=12221931"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/collect.js?v=12191931"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/videodetail.js?v=12291126"></script>
    <script type="text/javascript">
        var pageNow = 1,
                pageSize = 10;
        Zepto(function () {
            new FastClick(document.body);
            var shareFlag = (window.location.href).indexOf('is_share') > 0 ? true : false;
            var param = {
                "id": "<?php echo $id;?>",
                "uid": "<?php echo isset($user->uid) && $user->uid > 0 ? $user->uid : $uid;?>",
                "section": 1,
                "commentType": 'Video',
                "commentid": '',
                "content": '',
                "upid": '',
                "nickname": labUser.nickname,
                "p_nickname": '',
                "pContent": '',
                "created_at": unix_to_datetime(new Date().getTime()),
                "likes": 0,
                "urlPath": window.location.href,
                "shareStr": 'is_share',
                "page": pageNow,
                "page_size": pageSize
            };
            if (shareFlag) {
                param.uid = '0';
            }
            //加载更多评论
            $('#morecm').on('click', function () {
                var page = $('#pullUp').data('pagenow');
                page++;
                var param = {
                    "id": "<?php echo $id;?>",
                    "uid": labUser.uid,
                    "commentType": 'Video',
                    "page": page,
                    "page_size": pageSize
                };
                Comment.getCommentList(param, null, 'video');
//                $('#morecm').css('transform', 'rotate(360deg)');
//                $('#morecm').css('transform','rotate(0deg)');
            });
            $("#seenmoreVideo").attr("value", param.id);//点播视频id
            /**展开收起**/
            $(".act_intro .up").click(function () {
                $(".act_address").css("height", "17rem");
                $(".up").hide();
                $(".down").show();
            });
            $(".act_intro .down").click(function () {
                $(".act_address").css("height", "auto");
                $(".up").show();
                $(".down").hide();
            });
            $('#comtextarea').on('focus', function () {
                setTimeout(function () {
                    var c = window.document.body.scrollHeight;
                    window.scroll(0, c);
                }, 500);
                return false;
            });
            //评论按钮绑定textare选中
            $(".comment_btn>button").bind("click", function () {
//              $(".comment_input").show();
//              $(".comment_input>input").focus();
                $('#commentback').removeClass('none');
                $('#comtextarea').focus();
                $('#tapdiv').one('click', function () {
                $('#comtextarea').val('');
                $('#commentback').addClass('none');
                $('#subcomments').data('replay', 'no');
                $('.coperate').hide();//回复小气泡
                });
            });
            Comment.getCommentList(param, 'reload', 'video');
            Video.vodDetail(param, shareFlag);
            /**点击出现评论或删除**/
            if (param.uid > 0) {
                $(document).on("click", "#thelist .contentDd .comment", function () {
                    $(this).parent().siblings('#tips').toggle().parent().siblings("dl").children('#tips').hide();
                });
            }
        });
    </script>
    <script type="text/javascript">
        //上拉、下拉
        var touch = $.extend({}, {
            getAjaxDownData: function () {
                //myScroll.refresh();
            },
            getAjaxUpData: function () {
                //myScroll.refresh();
            }
        });
        //刷新评论
        function Refresh() {
            var param = {
                "id": "<?php echo $id;?>",
                "uid": labUser.uid,
                "commentType": 'Video',
                "section": 1,
                "page": 1,
                "page_size": pageSize
            };
            Comment.getCommentList(param, 'reload', 'activity');
        }
        /**app调用web方法****/
        //分享
        function showShare() {
            var title = $('#video_title_none').text();//点播的标题
            var url = window.location.href;
            var img = $('#share_img').data('src').replace(/https:/g, 'http:');
            var header = '点播';
            var content = cutString($('#video_descript_none').text(), 18);//点播的描述
            var viewnum = '播放量:' + $('#seenNum').text() + '\n';
            shareOut(title, url, img, header, viewnum + content);
        }
        //刷新
        function reload() {
            location.reload();
        }
        //收藏/取消收藏
        function favourite() {
            var isFavorite = $(".isFavorite").attr("value");
            var id = <?php echo $id;?>;
            if (isFavorite == "1") {
                setFavourite('0');
                isFavorite = 0;
            } else if (isFavorite == "0") {
                setFavourite('1');
                isFavorite = 1;
            }
            Collect.getCollect(id, "video", isFavorite);
        }
    </script>

@stop