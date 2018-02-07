@extends('layouts.default')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/iscroll.css"/>
    <link href="{{URL::asset('/')}}/css/act_detail.css?v=1.0.0" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/government_detail.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <div class="containerBox ">
        <!--安装app-->
        <div class="app_install none" id="installapp">
            <i class="l">打开无界商圈APP，体验更多精彩内容 >></i>
            <span class="r" id="openapp"><img src="{{URL::asset('/')}}/images/install_btn.png" alt=""></span>
            <div class="clearfix"></div>
        </div>
        <section class="government_infor block">
            <h1 class="title" id="gov_name">Gdevops全球敏捷运维峰会——杭州站</h1>
            <div>
                <i class="key_word_icon l"></i>
                <div class="key_word l">
                    <span>国家级</span>
                    <span>电子园区</span>
                </div>
                <div class="clearfix"></div>
            </div>
            <div>
                <i class="address_icon l"></i>
                <div class="address l">
                    <p id="maker_subject">杭州OVO路演中心</p>
                    <p class="dark_gray" id="maker_address">浙江杭州下城区体育场路浙江国际大酒店11F</p>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="other">
                <span class="seen"><i class="seen_icon"></i><em id="seenNum"></em></span>
                <span class="collect"><i class="collect_icon"></i><em id="collectNum"></em></span>
                <span class="r industory" id="industory"></span>
            </div>
        </section>

        <section class="government_theme block" style="margin-top:1rem;">
            <p class="title"><span>招商主体</span>杭州拱墅区农业局</p>
            <div class="name l"><i class="head_icon vc"></i><em data-role="name"></em></div>
            <button class="green_bt r JS_ovo">申请OVO跨域对接</button>
            <div class="clearfix"></div>
        </section>

        <section class="tabbox">
            <nav class="column TwoColumn" style="position: static;">
                <span class="green" data-type="projectNews">信息详情</span>
                <span style="width:2%;color:#ddd;">|</span>
                <span data-type="comment">留言区</span>
            </nav>

            <!--信息详情-->
            <section id="act_intro" style="margin-top:0rem;">
                <div class="block project_intro" id='project_intro'>
                    <p class="title"></p>
                    <div class="text JS_alltext">上海市云计算产业促</div>
                    <div class="seen_more tc dark_gray down none">点击查看全文<em class="down_icon"></em></div>
                </div>
                <div class="block project_intro" id="policy">
                    <p class="title"></p>
                    <div class="text JS_alltext">上海市云计算产业促</div>
                    <div class="seen_more tc dark_gray down none">点击查看全文<em class="down_icon"></em></div>
                </div>
                <div class="block project_intro" id="condition">
                    <p class="title">投资者条件</p>
                    <div class="text">投资者条件投资者条件投资者条件投资者条件投资者条件</div>
                </div>
                <nav class="nav dark_gray">
                    <span class="line l"></span><span class="l tc nav_text">相关活动</span><span class="line r"></span>
                </nav>
                <div class="about_act">
                    <div class="actBox"></div>
                    <div class="seen_more" id="act_list" data-act_id="">更多活动<span class="sj_icon"></span></div>
                </div>
            </section>

            <!--评论留言-->
            <section id="comment" class="none">
                <div id="wrapper" class="top35" style="font-family: 'Microsoft YaHei';">
                    <div id="scroller">
                        <div id="pullDown" class="none">
                            <span class="pullDownLabel"></span>
                        </div>
                        <div class="clearfix"></div>
                        <!-- item_list 开始 -->
                        <div id="thelist">
                            <div class="tc none nocomment" id="nocommenttip">暂无评论</div>
                            <div class="comment_tit amazeComment none">精彩评论（<em class="num"></em>）</div>
                            <div class="block none" id="amazeComment"></div>
                            <div class="comment_tit allComment none">全部评论（<em class="num"></em>）</div>
                            <div class="block none" id="allComment"></div>
                        </div>
                        <div id="pullUp" data-pagenow="1">
                            <span class="pullUpLabel"></span>
                        </div>
                        <!--
                        <div class="morecomment none" id="morecm">加载更多</div>
                        -->
                    </div>
                </div>
            </section>

        </section>
        <!--评论按钮-->
        <div class="comment_btn none">
            <button type="button">说说你的看法</button>
        </div>
        <!--评论框-->
        <div class="comment_input full none">
            <input type="text" placeholder="">
            <button id="send_comment">评价</button>
        </div>
        <!--底部btn-->
        <div class="fixed_btn none" id="noShareBtn" data-isshow="no">
            <button class="collect collectbtn">收藏</button>
            <button class="chat" data-chatid=''>群聊</button>
            <button class="ovo JS_ovo" id="applyflag">申请OVO跨域对接</button>
        </div>
        <!--未登录按钮-->
        <div class="fixed_btn share none" id="shareBtn" data-isshow="no">
            <button class="ovo JS_ovo">申请OVO跨域对接</button>
        </div>
        <!--分享出去按钮-->
        <div class="fixed_btn weixin none" id="loadAppBtn">
            <button class="signup" id="loadapp">下载APP</button>
        </div>
        <!--浏览器打开提示-->
        <div class="safari none">
            <img src="{{URL::asset('/')}}/images/safari.png">
        </div>
    </div>
@stop
@section('endjs')
    <script type="text/javascript" src="{{URL::asset('/')}}/js/iscroll.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/iscroll_touch.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/businessdetail.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/comment.js?v=12191934"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/collect.js?v=12191934"></script>
    <script type="text/javascript">
        var pageNow = 1, pageSize = 10;
        Zepto(function () {
            new FastClick(document.body);
            var param = {
                "id": "<?php echo $id;?>",
                "uid": "<?php echo isset($user->uid) && $user->uid > 0 ? $user->uid : $uid;?>",
                "type": '',
                "content": '',
                "upid": '',
                "nickname": labUser.nickname,
                "p_nickname": '',
                "pContent": '',
                "created_at": unix_to_datetime(new Date().getTime()),
                "likes": 0,
                "urlPath": window.location.href,
                "shareStr": 'is_share',
                "commentType": 'Opportunity',
                "section": 1,
                "page": pageNow,
                "page_size": pageSize
            };
            Business.detail(param.id, param.uid);
            Business.aboutAct(param.id);
            Comment.getCommentList(param, 'reload', 'opp');
            /**评论按钮**/
            $(".comment_btn").click(function () {
                $(".comment_input").show();
            });
            /**评论按钮绑定input选中**/
            $(".comment_btn").bind("click", function () {
                $(".fixed_btn").hide();
                $(".comment_input>input").focus();
            });
            /**点击出现评论或删除**/
            if (labUser.uid > 0) {
                $(document).on("click", "#comment dd.contentDd", function () {
                    $(this).siblings('#tips').toggle().parent().siblings("dl").children('#tips').hide();
                });
            }
            /**回复评论**/
            $(document).on("click", ".reply", function () {
                $(".comment_input").show();
                $(".comment_input>input").focus();
                param.upid = $(this).parents("dl").attr("data-commentid");
                param.p_nickname = $(this).parents().siblings("dd").children('.name').html();
                param.pContent = $(this).parents().siblings("dd").children('.comment').html();
                $(".comment_input>input").attr("placeholder", '回复@' + param.p_nickname + ':').focus();
                $('#send_comment').data('replay','yes');
                $('.coperate').hide();
            });

            /**发表/回复评论**/
            $("#send_comment").click(function () {
                param.content = $(".comment_input>input").val();
                if(param.content){
                    Comment.addComment(param,$('#send_comment').data('replay'));
                }
                $(".comment_input>input").val("");
                $(".comment_input>input").attr("placeholder",'');
                $(".comment_input").hide();
//                $('.coperate').hide();
            });

            /**删除评论**/
            $(document).on("tap", ".delete", function () {
                param.commentid = $(this).parents("dl").attr("data-commentid"); //评论的id
                Comment.deleteComment(param);
                $(this).parents("dl").remove();
            });

            //更多活动列表
            $(document).on('tap', '#act_list', function () {
                var act_id = $(this).data('act_id');
                gotoMoreActivityList(act_id);
            });
        });
    </script>
    <script>
        /**app调用web方法****/
        //加载
        var touch = $.extend({}, {
            getAjaxDownData: function () {
                var param = {
                    "id": "<?php echo $id;?>",
                    "uid": labUser.uid,
                    "commentType": 'Opportunity',
                    "page": 1,
                    "section": 1,
                    "page_size": pageSize
                };
                Comment.getCommentList(param, 'reload', 'opp');
                myScroll.refresh();
            },
            getAjaxUpData: function () {
                var page = $('#pullUp').data('pagenow');
                page++;
                var param = {
                    "id": "<?php echo $id;?>",
                    "uid": labUser.uid,
                    "commentType": 'Opportunity',
                    "section": 1,
                    "page": page,
                    "page_size": pageSize
                };
                Comment.getCommentList(param, null, 'opp');
                myScroll.refresh();
            }
        });
        //分享
        function showShare() {
            var title = $('.government_infor .title').text();
            var url = window.location.href;
            var img = "";
            var header = '商机';
            var content = cutString($('#project_intro .text').text(), 18);
            shareOut(title, url, img, header, content);
        }
    </script>
@stop