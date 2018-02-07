@extends('layouts.default')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/iscroll.css"/>
    <link href="{{URL::asset('/')}}/css/act_detail.css??v=11242123" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section class="containerBox none" id="containerBox">
        <div class="videotoptip none f12">本直播为专版直播，建议购买专版会员，享受更多优惠</div>
        <div class="flowers f14 none" id="giftdiv">
            <img src="" alt="" class="avarimg"><span class="nickname" id="nickname">昵称</span>：送了一朵花
            <img src="{{URL::asset('/')}}/images/flowerpic.png" alt="" class="flowerpic">
            <div class="nums">×1</div>
        </div>
        <!--安装app-->
        <div class="install none" id="installapp">
            <p class="l">打开无界商圈APP，观看完整高清直播 &gt;&gt;</p>
            <span class="r" id="openapp"><img src="{{URL::asset('/')}}/images/install_btn.png" alt=""></span>
            <div class="clearfix"></div>
        </div>
        <!--直播分享-->
        <div class="share_video none">
            <img src="{{URL::asset('/')}}/images/live.png" alt="">
            <p class="share_text"><img src="{{URL::asset('/')}}/images/warning.png" alt=""><br>该直播需要购买才能观看</p>
        </div>
        <div id="video_box" style="height: 19.5rem;"></div>
        <div id="livecount" class="livecount"></div>
        <!--直播倒计时-->
        <div class="live_time none" id="daojishi">
            <img src="{{URL::asset('/')}}/images/live.png" alt="timecounter">
            <div class="times">
                <p><span class="live_icon"></span>直播倒计时</p>
                <p class="day">0天0小时0分钟</p>
                <p>
                    <button class="order" data-subscribe="">订阅</button>
                </p>
                <p class="gray">订阅该直播，我们会以短信、站内信届时通知您</p>
            </div>
        </div>
        <section class="videonews_box">
            <nav class="column TwoColumn" id="">
                <span class="green" type="act_intro" id="detail_block" style="width:32%;">详情</span>
                <span style="width:2%;color:#ddd;">|</span>
                <span style="width:32%" id="addin_block">立即加盟</span>
                <span style="width:2%;color:#ddd;" id="sencondsplit">|</span>
                <span type="comment" class="" style="width:32%;" id="comment_block">互动</span>
            </nav>
            <!-- 活动介绍 -->
            <section id="act_intro" class="none">
                <div class="act_intro block">
                    <dl>
                        <div class="act-block" data-activity_id="" id="acttitle">
                            <dt class="act_pics" style="position: relative;"><img
                                        src="http://mt.wujie.com.cn/attached/image/20160530/20160530190835_17525.jpg"
                                        alt=""><span class="businessflag f12 none tc" id="businessflag">推介会 / 招商会</span>
                            </dt>
                            <dd class="act_name wrap" id="act_name">活动标题</dd>
                            <span class="sj_icon top3"></span>
                            <div class="clearfix"></div>
                        </div>
                        <div class="act_address" style="height: auto;">
                            <dd class="time"><span class="time_icon"></span>
                                <div class="infor" id="timetop"><p id="begin_time">直播开始时间：2016-11-09 10:00</p></div>
                            </dd>
                            <div id="address_flag" style="max-height:none;">
                                <dd class="address_list"><span class="address_icon" style="margin-top: 1.33rem;"></span>
                                    <div class="infor" style="border: none;"><p class="nameflag" id="act_positon">
                                            活动现场：杭州</p></div>
                                </dd>
                            </div>
                        </div>
                        <div class="zbrow none bordertop" id="zbrow">
                            <span class="zbflag r3">专版活动</span>
                            <span class="pl1">查看<i id="zbname" class="green pl05" data-zbid="0"></i>专版</span>
                            <span class="sj_icon top105"></span>
                        </div>
                        <div class="clearfix"></div>
                    </dl>
                </div>
                <!-- 发布者 -->
                <div class="block author relative" id="publisher" value=''>
                    <span class="img"></span> <i class="author_name">Rena</i>发布<span class="sj_icon"></span>
                </div>
                <!--相关品牌-->
                <section id="pinpai" class="brandcontain">
                    <div class="brandtext f14"><span class="brand_text">相关品牌</span></div>
                </section>
                <!-- 直播内容介绍 -->
                <div class="block video_detail">
                    <div class="livetext"><span class="brand_text">直播介绍</span></div>
                    <div class="text topborder" id="video_description">直播描述文字</div>
                    <div class="seen_more topborder" id="videosMoreDetail" value="" data-type="live">更多直播详情<span
                                class="sj_icon"></span>
                    </div>
                </div>
                <!-- 相关视频 -->
                <div class="about_video none">
                    <nav class="nav">
                        <span class="line l"></span><span class="l tc nav_text">你可能感兴趣</span><span
                                class="line r"></span>
                    </nav>
                    <div class="block">
                        <ul id="recommend_live">

                        </ul>
                        <div class="seen_more topborder" id="tolivelist">更多直播<span class="sj_icon"></span></div>
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
                    <div class="refreshpic"></div>
                </div>
            </section>
            <!--商品列表-->
            <section id="barnd_list" class="none block-brandlist">
            </section>
            <!--评论按钮-->
            <div class="comment_btn none">
                <button type="button" class="tl" style="width: 30rem;">及时互动，分享智慧...</button>
                <span class="uploadpic"></span><i class="uploadpictext f12">发表图片</i>
            </div>
            <!--评论框-->
            <div class="comment_input full none" id="commentcontain">
                <input type="" placeholder="">
                <button id="send_comment">评价</button>
            </div>
            <!--分享页按钮-->
            <div class="fixed_btn weixin none" id="loadAppBtn">
                <button class="reserve" style="width:50%;background-color: white;color:#6bc24b;" id="reserve">设置直播提醒
                </button>
                <button class="signup" id="loadapp" style="width:50%;float: right;">下载APP</button>
            </div>
            <!--提示浏览器打开提示-->
            <div class="safari none">
                <img src="{{URL::asset('/')}}/images/safari.png">
            </div>
            <input type="hidden" data-maxid="0" data-minid="0" id="commentflag">
            <div class="none" id="livesubject" data-begintime="" data-livenum="0"></div>
            <!--快速注册-->
            <div class="remindpart none" id="registerpart" style="z-index:197;">
                <div class="content">
                    <div class="tiptitle f18 tc remindfontcolor">快速登录/注册,观看直播：</div>
                    <div class="userinput remindcolor">
                        <div class="putdiv remindcolor f12" style="color:#fff;height:4rem;line-height: 3rem;">
                            登录无界商圈APP，更多高清视频等你来观看
                        </div>
                        <div class="putdiv remindcolor height06"><input type="text" name="phonenumber" placeholder="手机号"
                                                                        id="zcphone"/></div>
                        <div class="putdiv remindcolor height06"><input type="text" name="mescode" placeholder="短信验证码"
                                                                        id="zcyzm"/>
                            <button class="ident_code" id="mescode">获取验证码</button>
                        </div>
                        <div class="putdiv remindcolor tc">
                            <button class="subbtn f16" id="registerbtn">提交</button>
                        </div>
                    </div>
                    <div class="closepic"></div>
                </div>
            </div>
            <!--预约-->
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
                        <div class="putdiv remindcolor f12" style="color:#fff;padding-top: 0.3rem;padding-bottom: 0;">
                            可以订阅该场直播，我们将在 直播开始前30分钟 以短信发送直播提醒消息
                        </div>
                        <div class="putdiv remindcolor height06"><input type="text" name="phonenumber" placeholder="手机号"
                                                                        id="yyphone"/></div>
                        <div class="putdiv remindcolor height06"><input type="text" name="mescode" placeholder="短信验证码"
                                                                        id="yyyzm"/>
                            <button class="ident_code" id="getcode">获取验证码</button>
                        </div>
                        <div class="putdiv remindcolor tc">
                            <button class="subbtn f16" style="margin-top: 1rem;" id="yysubmit">提交</button>
                        </div>
                    </div>
                    <div class="closepic"></div>
                </div>

            </div>
            <!--预约成功-->
            <div class="remindpart none" id="remindsuccess" style="z-index:195;">
                <div class="content">
                    <div class="tiptitle f18 tc remindfontcolor">订阅成功</div>
                    <div class="userinput remindcolor" style="">
                        <div class="putdiv remindcolor f12" style="color:#fff;height:4rem;line-height: 3rem;">直播订阅成功
                        </div>
                        <div class="putdiv remindcolor f12" style="color:#fff;line-height: 1.5rem;" id="membertips">
                            欢迎订阅本直播，更多高清视频请打开无界商圈APP观看！
                        </div>
                        <div class="putdiv remindcolor tc" style="">
                            <div class="f12" style="margin-top:0; width:100%;color:#fff;">温馨提示：点击底部链接下载无界商圈APP</div>
                        </div>
                    </div>
                    <div class="closepic"></div>
                </div>
            </div>
        </section>
        <div class="bigimg none" id="bigimg">
            <img alt="" src="" id="showimg">
        </div>
        <!--评论-->
        <div class="commentback none" id="commentback">
            <div style="width: 100%;height:100%;position:absolute;bottom:15rem;left:0;" id="tapdiv"></div>
            <div class="textareacon">
                <textarea class="f12" name="comment" id="comtextarea" cols="30" rows="10" style="resize: none;"
                          placeholder=""></textarea>
                <button class="fr subcomment f16" id="subcomments">发表</button>
            </div>
        </div>
        <!--分享图片-->
        <input type="hidden" data-src="" id="share_img">
    </section>
    <div class="none timeoverbox" id="timeoverbox">
        <!-- 打开APP -->
        <div class="app_install none" id="otimeappc">
            <i class="l">打开无界商圈APP，体验更多精彩内容 >></i>
            <span class="r" id="otimeapp"><img src="{{URL::asset('/')}}/images/install_btn.png" alt=""></span>
            <div class="clearfix"></div>
        </div>
        <div class="app_install" style="padding-top:0;padding-bottom:0;">
            <div class="tc" style="padding:1rem 1.33rem;border-bottom: 1px solid #ddd;border-top: 1px solid #ddd;">
                该场直播已结束，关注更多精彩直播，快 下载无界商圈 应用！
            </div>
        </div>
        <section class="mt0">
            <div class="act_intro block" style="margin-top:0;">
                <dl>
                    <dt class="act_pics"><img src="" alt="actpic" id="act_picsrc_s"></dt>
                    <dd class="act_name" style="height:auto;">
                        <p id="act_name_s" style="padding-left:0.5rem;"></p>
                    </dd>
                    <div class="clearfix"></div>
                    <div class="act_address" style="height: auto;">
                        <dd class="time">
                            <span class="time_icon"></span>
                            <div class="infor" style="border:none">
                                <p id="act_time_s"></p>
                            </div>
                        </dd>
                    </div>
                    <div class="clearfix"></div>
                </dl>
            </div>
            <div class="applysuccess"></div>
            <!--浏览器打开提示-->
            <div class="safari none timeover">
                <img src="{{URL::asset('/')}}/images/safari.png">
            </div>
        </section>
    </div>
@stop
@section('endjs')
    <script src="https://qzonestyle.gtimg.cn/open/qcloud/video/live/h5/live_connect.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/iscroll.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/iscroll_touch.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/comment.js?v=12290933"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/collect.js?v=12191934"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/videodetail.js?v=12281640"></script>
    <script type="text/javascript">
        var pageNow = 1,
                pageSize = 10;
        Zepto(function () {
            new FastClick(document.body);
            var shareFlag = (window.location.href).indexOf('is_share') > 0 ? true : false;
            var newVersionFlag = (window.location.href).indexOf('version=2.3') > 0 ? true : false;
            var param = {
                "id": "<?php echo $id;?>",
                "uid": "<?php echo isset($user->uid) && $user->uid > 0 ? $user->uid : $uid;?>",
                "section": 0,
                "commentType": 'Live',
                "type": 'Live',
                "commentid": '',
                "content": '',
                "upid": '',
                "nickname": labUser.nickname,
                "avatar": labUser.avatar,
                "p_nickname": '',
                "pContent": '',
                "created_at": unix_to_datetime(new Date().getTime()),
                "likes": 0,
                "urlPath": window.location.href,
                "shareStr": 'is_share',
                "page": pageNow,
                "page_size": pageSize,
                "update": "new",
                "fecthSize": 0
            };
            if (shareFlag) {
                param.uid = '0';
            }
            if (is_weixin()) {
                param.platform = 'weixin';
            }
            else {
                if (isiOS) {
                    param.platform = 'ios';
                }
                else if (isAndroid) {
                    param.platform = 'android';
                }
                else {
                    param.platform = 'other';
                }
            }
            $('.avarimg').attr('src', param.avatar);
            $('#nickname').html(param.nickname);
            //视频id
            $("#seenmoreVideo").attr("value", param.id);
            //加载更多评论
            $('#morecm').on('click', function () {
                var page = $('#pullUp').data('pagenow');
                page++;
                var param = {
                    "id": "<?php echo $id;?>",
                    "uid": labUser.uid,
                    "commentType": 'Live',
                    "page": page,
                    "page_size": pageSize
                };
                Comment.getCommentList(param, null, 'live');
            });

            //更多视频列表
            $(document).on("click", "#seenmoreVideo", function () {
                var id = $(this).attr("value");
                seenmoreVideo(id);
            });
            //输入框
            $('#comtextarea').on('focus', function () {
                setTimeout(function () {
                    var c = window.document.body.scrollHeight;
                    window.scroll(0, c);
                }, 500);
                return false;
            });
            var inputtext = document.getElementById('comtextarea');
            var submitbtn = document.getElementById('subcomments');
            inputtext.oninput = function () {
                var text = this.value;
                if(text.length>0){
                    submitbtn.style.backgroundColor = '#1e8cd4';
                }
                else{
                    submitbtn.style.backgroundColor = '#999';
                }
            }
            /**评论按钮绑定input选中**/
            $(".comment_btn>button").bind("click", function () {
                $('#commentback').removeClass('none');
                $('.textareacon textarea').focus();
                $('#tapdiv').one('click', function () {
                    $('#subcomments').css('backgroundColor','#999');
                    $('#comtextarea').val('');
                    $('#commentback').addClass('none');
                });
            });
            $('.refreshpic').on('click', function () {
                $(this).css('transform', 'rotate(90deg)');
                var parameter = {
                    "type": param.type,
                    "uid": param.uid,
                    "id": param.id,
                    "fromId": $('#commentflag').data('maxid'),
                    "update": "new",
                    "fecthSize": 0
                };
                Comment.getFreshList(parameter);
                setTimeout(function () {
                    $('.refreshpic').css('transform', 'rotate(0deg)');
                }, 1000);

            });
            //详情
            Video.detail(param, shareFlag, newVersionFlag);
            //评论列表
            Comment.getCommentList(param, 'reload', 'live');
        });
    </script>
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
        ;
        function showShare() {
            var title = $('#act_name').text();
            var url = window.location.href;
            var img = $('#share_img').data('src').replace(/https:/g,'http:');
            var header = '直播';
            var content = cutString($('#video_description').text(), 18);
            shareOut(title, url, img, header, content);
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
        //送花提示
        function showFlower(num) {
            var number = num || 1;
            $('#giftdiv .nums').html('×' + number);
            $('#giftdiv').removeClass('none');
            setTimeout(function () {
                $('#giftdiv').addClass('none');
            }, 5000);
        }
    </script>
@stop