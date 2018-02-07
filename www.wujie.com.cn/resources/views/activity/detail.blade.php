@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/act_detail.css?v=12191826" rel="stylesheet" type="text/css"/>
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
        <div class="act_banner"><img src="" alt="bannerPic" id="bannerPic">
        </div>
        <!-- 活动介绍 -->
        <section id="act_intro" class="mt0" style="padding-bottom: 7rem;">
            <div class="act_intro block">
                <dl>
                    <dt class="act_pics"><img src="" alt="actpic" id="act_picsrc"></dt>
                    <dd class="act_name" style="height:7.4rem;">
                        <p id="act_name" data-act_id=""></p>
                        <div class="dark_gray viewnlike">
                            <span class="seen"><i class="seen_icon"></i><em id="seenNum"></em></span>
                            <span class="collect"><i class="collect_icon"></i><em id="storeNum"></em></span>
                        </div>
                        <div class="intro_meeting none">
                            招商会/推介会
                        </div>
                    </dd>
                    <div class="clearfix"></div>
                    <div class="zbrow none" id="zbrow">
                        <span class="zbflag r3">专版活动</span>
                        <span class="pl1">查看<i id="zbname" class="green pl05" data-zbid="0"></i>专版</span>
                        <span class="sj_icon top105"></span>
                    </div>
                    <div class="act_address" style="height: auto;">
                        <dd class="time">
                            <span class="time_icon"></span>
                            <div class="infor " id="timetop">
                                <p id="act_time"></p>
                            </div>
                        </dd>
                        <div id="address_flag">
                        </div>
                        <dd class="wjb">
                            <span class="wjb_icon"></span>
                            <div class="infor no-bottom"><p id="wjbNum"></p>
                                <p>购买直播、现场门票</p><span class="sj_icon"></span></div>
                        </dd>
                    </div>
                    <div class="clearfix"></div>
                </dl>
            </div>
            <!-- 发布者 -->
            <div class="block author relative" id="pubid">
            </div>
            <!-- 品牌展示 -->
            <div class="block brand none" id='brand' >
            </div>
            <!-- 活动详情 -->
            <div class="block video_detail">
             <div class="act_title none"><p>活动详情</p></div>
                <div class="text" id="video_description"></div>
                <div class="seen_more topborder" id="actMoreDetail"><a href="javascript:;">更多详情</a><span
                            class="sj_icon"></span>
                </div>
            </div>
            <!-- 你可能感兴趣 推荐活动-->
            <div class="about_video">
                <nav class="nav">
                    <span class="line l"></span><span class="l tc nav_text">你可能感兴趣</span><span class="line r"></span>
                </nav>
                <div class="about_act">
                    <div id="recommendact">

                    </div>
                    <div class="seen_more" id="act_list">更多活动<span class="sj_icon"></span></div>
                </div>
            </div>
            <div class="none" data-src="" id="share_img"></div>
        </section>
        <!--未登录时按钮-->
        <div class="fixed_btn share none" id="shareBtn">
            <button class="signup">立即报名</button>
        </div>
        <!--登陆时-->
        <div class="fixed_btn none" id="noShareBtn">
            <button class="collect collectbtn">收藏</button>
            <button class="chat">群聊</button>
            <button class="ovo signup">立即报名</button>
        </div>
        <!--专版会员-->
        <div class="fixed_btn none" id="zbBtn">
            <button class="collect collectbtn">收藏</button>
            <button class="chat">群聊</button>
            <button class="ovo signup"><i style="font-size:1.2rem;">享受专版会员优惠</i><br>立即报名</button>
        </div>
        <!--非专版会员-->
        <div class="fixed_btn none" id="notzbBtn">
            <button class="collectbtn" style="width:20%;font-size:1.4rem;">收藏</button>
            <span style="position: absolute;left:20%;color:#999;font-size:1.2rem;line-height: 4.8rem;">|</span>
            <button class="chat" style="width:20%;font-size:1.4rem;">群聊</button>
            <span style="position: absolute;left:40%;color:#999;font-size:1.2rem;line-height: 4.8rem">|</span>
            <button class="buyvip" style="width:30%;font-size: 1.4rem;"><i style="color:#f63;font-size: 1.2rem;">会员专享，免费入场</i><br>成为专版会员
            </button>
            <button class="signup" style="float:right;width:25%;background-color: #6bc24b;color:#fff;font-size:1.6rem;">
                立即报名
            </button>
        </div>
        <!--分享出去按钮-->
        <div class="fixed_btn weixin none" id="loadAppBtn">
            <span class="downsapp width60 fl" id="loadapp"><img src="{{URL::asset('/')}}/images/downapp.png"
                                                                alt=""></span>
            <span class="downsapp width40 f16 greenbc r" id="signnow">立即报名</span>
        </div>
        <!--浏览器打开提示-->
        <div class="safari none">
            <img src="{{URL::asset('/')}}/images/safari.png">
        </div>
    </section>
@stop

@section('endjs')
    <script type="text/javascript">
        Zepto(function () {
            var urlPath = window.location.href,
                    activity_id ={{$id}},
                    uid = {{$uid}},
                    position_id ={{$position_id}},
                    maker_id ={{$makerid}};
            var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
            var oldVersion= urlPath.indexOf('version=2.3') > 0 ? true : false;
            var activityDetail = {
                detail: function (activity_id, uid, position_id, maker_id) {
                    var param = {};
                    param["id"] = activity_id;
                    param["uid"] = uid;
                    param["position_id"] = position_id;
                    param["maker_id"] = maker_id;
                    var url = labUser.api_path + '/activity/detail';
                    ajaxRequest(param, url, function (data) {
                        if (data.status) {
                            activityData(data.message, shareFlag);
                    //判断是否参加推介会       
                            if(data.message.self.is_brand_activity=='1'){
                                $('.intro_meeting').removeClass('none');
                                $('#brand').removeClass('none');
                                $('.act_title').removeClass('none');
                                getBrand(data.message.self,shareFlag);

                            }else if(data.message.self.is_brand_activity=='0'){
                                $('.intro_meeting').addClass('none');
                                $('#brand').addClass('none');
                                $('.act_title').addClass('none');
                            }
                        }
                    });
                },
                collect: function (obj) {
                    var param = obj;
                    var url = labUser.api_path + '/favorite/deal';
                    ajaxRequest(param, url, function (data) {
                        if (data.status) {
                            var act_id = $('#act_name').data('act_id');
                            if (param['type'] == '1') {
                                $('.collectbtn').text('取消收藏');
                                $('#storeNum').html($('#storeNum').text() - 1 + 2);
                                //activityDetail.seenPlus(act_id, '1', 'likes');
                            }
                            else {
                                $('.collectbtn').text('收藏');
                                $('#storeNum').html($('#storeNum').text() - 1);
                                // activityDetail.seenPlus(act_id, '-1', 'likes');
                            }
                        }
                    });
                },
                seenPlus: function (actid, type, col) {
                    var param = {};
                    param["id"] = actid;
                    param["type"] = type;
                    param["col"] = col;
                    var url = labUser.api_path + '/activity/incre';
                    ajaxRequest(param, url, function (data) {
                        if (data.status) {
                            if (col == 'likes' && type == '1') {
                                $('#storeNum').html($('#storeNum').text() - 1 + 2);
                            }
                            else if (col == 'likes' && type == '-1') {
                                $('#storeNum').html($('#storeNum').text() - 1);
                            }
                        }
                    });
                }
            };
            activityDetail.detail(activity_id, uid, position_id, maker_id);

            //活动详细信息
            function activityData(result, is_share) {
                var selfObj = result.self,
                        recObj = result.rec,
                        is_vip = 'vip_id' in selfObj;
                commonHTML(selfObj);
                //分享页
                if (is_share) {
                    $('#loadAppBtn').removeClass('none');
                    $('#installapp').removeClass('none');
                    $('.act_address').css('height', 'auto');
                    $('#signnow,#wjbNum').on('click', function () {
                        window.location.href = labUser.path + 'webapp/ticket/actapply?id=' + activity_id;
                    });
                    //OVO地址
                    shareOvoAddress(selfObj.descriptions, selfObj.maker_ids, selfObj.city, selfObj.address, selfObj.name, selfObj.type);
                    //推荐活动
                    recActiviy(recObj, true, selfObj.current_maker_subject, selfObj.current_maker_city);
                    //浏览器判断
                    if (is_weixin()) {
                        /**微信内置浏览器**/
                        $(document).on('tap', '#loadapp,#openapp', function () {
                            var _height = $(document).height();
                            $('.safari').css('height', _height);
                            $('.safari').removeClass('none');
                        });
                        //点击隐藏蒙层
                        $(document).on('tap', '.safari', function () {
                            $(this).addClass('none');
                        });
                        var wxurl = labUser.api_path + '/weixin/js-config';
                        //活动详情描述
                        var desptStr = removeHTMLTag(selfObj.description);
                        var nowhitespace = desptStr.replace(/&nbsp;/g,'');
                        var despt = cutString(desptStr, 60);
                        var nowhitespaceStr =cutString(nowhitespace, 60);
                        ajaxRequest({url: location.href}, wxurl, function (data) {
                            if (data.status) {
                                wx.config({
                                    debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                                    appId: data.message.appId, // 必填，公众号的唯一标识
                                    timestamp: data.message.timestamp, // 必填，生成签名的时间戳
                                    nonceStr: data.message.nonceStr, // 必填，生成签名的随机串
                                    signature: data.message.signature, // 必填，签名，见附录1
                                    jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
                                });
                                wx.ready(function () {
                                    wx.onMenuShareTimeline({
                                        title: selfObj.subject, // 分享标题
                                        link: location.href, // 分享链接
                                        imgUrl: selfObj.share_image, // 分享图标
                                        success: function () {
                                            // 用户确认分享后执行的回调函数
                                        },
                                        cancel: function () {
                                            // 用户取消分享后执行的回调函数
                                        }
                                    });
                                    wx.onMenuShareAppMessage({
                                        title: selfObj.subject,
                                        desc: nowhitespaceStr,
                                        link: location.href,
                                        imgUrl: selfObj.share_image,
                                        trigger: function (res) {
                                            // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
                                            console.log('用户点击发送给朋友');
                                        },
                                        success: function (res) {
                                            console.log('已分享');
                                        },
                                        cancel: function (res) {
                                            console.log('已取消');
                                        },
                                        fail: function (res) {
                                            console.log(JSON.stringify(res));
                                        }
                                    });
                                });
                            }
                        });
                    }
                    else {
                        if (isiOS) {
                            //打开本地app
                            $(document).on('tap', '#openapp', function () {
                                var strPath = window.location.pathname.substring(1);
                                var strParam = window.location.search;
                                var appurl = strPath + strParam;
                                var share = '&is_share';
                                var appurl2 = appurl.substring(0, appurl.indexOf(share));
                                window.location.href = 'openwjsq://' + appurl2;
                            });
                            /**下载app**/
                            $(document).on('tap', '#loadapp', function () {
                                window.location.href = 'https://itunes.apple.com/app/id981501194';
                            });
                        }
                        else if (isAndroid) {
                            $(document).on('tap', '#loadapp', function () {
                                window.location.href = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.tyrbl.wujiesq';
                            });
                            $(document).on('tap', '#openapp', function () {
                                var strPath = window.location.pathname;
                                var strParam = window.location.search.replace(/is_share=1/g,'');
                                var appurl = strPath + strParam;
                                window.location.href = 'openwjsq://welcome' + appurl;
                            });
                        }
                    }
                    if (is_vip) {
                        //查看专版
                        $(document).on('tap', '#zbrow', function () {
                            var vip_id = $('#zbname').data('zbid');
                            window.location.href = labUser.path + 'webapp/special/detail?is_share=1&pagetag=02-1&vip_id=' + vip_id + '&uid=' + uid;
                        });
                    }
                    //推荐活动
                    $(document).on('tap', '.recact', function () {
                        var act_id = $(this).data('actid');
                        var ovoid = $(this).data('ovoid');
                        window.location.href = labUser.path + '/webapp/activity/detail?is_share=1&uid=0&position_id=0&makerid=' + ovoid + '&pagetag=02-2&id=' + act_id;
                    });


                }
                else {
                    innerOvoAddress(selfObj.city, maker_id, selfObj.current_maker_subject, selfObj.current_maker_address, selfObj.current_maker_city, selfObj.current_maker_description);
                    recActiviy(recObj, false, selfObj.makerOfUser, selfObj.current_maker_city);

                    //ovo中心地址查看
                    $(document).on('tap', '.address_list', function () {
                        var ovo_address = $(this).find('.addressflag').text();
                        var ovo_name = $(this).find('.nameflag').text();
                        var ovo_description = $(this).find('.ovodesp').text();
                        var desp = removeHTMLTag(ovo_description);
                        desp = cutString(desp, 40);
                        locationAddress(ovo_address, ovo_name, desp);
                    });
                    //发布者跳主办方
                    $(document).on('tap', '#pubid', function () {
                        var pub_id = $(this).attr('value');
                        var is_official = $(this).data('is_official');
                        gotoActivityList(pub_id, is_official);
                    });
                    //推荐活动
                    $(document).on('tap', '.recact', function () {
                        var act_id = $(this).data('actid');
                        var ovoid = $(this).data('ovoid');
                        window.location.href = labUser.path + '/webapp/activity/detail?makerid=' + ovoid + '&id=' + act_id + '&uid=' + uid + '&position_id=' + position_id + '&pagetag=02-2';
                    });
                    //更多活动,跳转到活动列表
                    $(document).on('tap', '#act_list', function () {
                        var act_id = $(this).data('act_id');
                        gotoMoreActivityList(act_id);
                    });
                    if (uid == '0') {
                        if (is_vip) {
                            //查看专版
                            $(document).on('tap', '#zbrow', function () {
                                var vip_id = $('#zbname').data('zbid');
                                window.location.href = labUser.path + 'webapp/special/detail?pagetag=02-1&vip_id=' + vip_id + '&uid=' + uid;
                            });
                        }
                        //未登录
                        $('#shareBtn').removeClass('none');
                        //结束报名,当前时间戳大于开始时间戳
                        var timestamp = Math.round(new Date().getTime() / 1000);
                        if (timestamp > selfObj.end_time) {
                            //报名结束
                            $('#shareBtn .signup').css('background-color', '#ccc').text('报名结束');
                        }
                        else {
                            //底部购买
                            $(document).on('tap', '#shareBtn .signup', function () {
                                var ovoid = maker_id;
                                var act_id = $('#act_name').data('act_id');
                                var act_name = $('#act_name').text();
                                ActivityApply(act_id, ovoid, act_name, 'activity');
                            });
                            //价格-->门票列表入口
                            $(document).on('tap', '.wjb', function () {
                                var ovoid = maker_id;
                                var act_id = $('#act_name').data('act_id');
                                var act_name = $('#act_name').text();
                                ActivityApply(act_id, ovoid, act_name, 'activity');
                            });
                        }
                    }
                    else {
                        if (is_vip) {
                            //查看专版
                            $(document).on('tap', '#zbrow', function () {
                                var vip_id = $('#zbname').data('zbid');
                                window.location.href = labUser.path + 'webapp/special/detail?pagetag=02-1&vip_id=' + vip_id + '&uid=' + uid;
                            });
                        }
                        //结束未报名
                        var timestamp = Math.round(new Date().getTime() / 1000);
                        if (timestamp < selfObj.end_time) {
                            //邀请
                            if (selfObj.is_shareable == '1') {
                                $('#yaoqing').removeClass('none');
                                var setout = setTimeout(function () {
                                    $('#yaoqing').addClass('none');
                                    clearTimeout(setout);
                                }, 8000);
                                //邀请
                                $(document).on('tap', '#yaoqingbtn', function () {
                                    var act_id = $('#act_name').data('act_id');
                                    invites(act_id, maker_id);
                                });
                            }
                            //判断是否已经收藏当前活动
                            if (selfObj.favorite == '1') {
                                $('.collectbtn').text('取消收藏');
                                //setFavourite('1');
                            }
                            else {
                                $('.collectbtn').text('收藏');
                                //setFavourite('0');
                            }
                            //群聊id
                            $('.chat').data('chatid', selfObj.chat);
                            if (is_vip) {
                                if (selfObj.belong_same_vip == 0) {
                                    //不是会员
                                    $('#notzbBtn').removeClass('none');
                                    $(document).on('tap', '.buyvip', function () {
                                        var vip_id = $('#zbname').data('zbid');
                                        buyVip(vip_id);
                                    });
                                }
                                else {
                                    $('#zbBtn').removeClass('none');
                                }
//                                //查看专版
//                                $(document).on('tap', '#zbrow', function () {
//                                    var vip_id = $('#zbname').data('zbid');
//                                    window.location.href = labUser.path + 'webapp/special/detail?pagetag=02-1&vip_id=' + vip_id + '&uid=' + uid;
//                                });
                            }
                            else {
                                $('#noShareBtn').removeClass('none');
                            }
                            //底部立即报名
                            $(document).on('tap', '.signup', function () {
                                var ovoid = maker_id;
                                var act_id = $('#act_name').data('act_id');
                                var act_name = $('#act_name').text();
                                ActivityApply(act_id, ovoid, act_name, 'activity');
                            });
                            //收藏/取消收藏
                            $(document).on('tap', '.collectbtn', function () {
                                var param = {};
                                param['uid'] = uid;
                                param['post_id'] = activity_id;
                                param['model'] = 'activity';
                                if ($(this).text() == '收藏') {
                                    param['type'] = '1';
                                }
                                else {
                                    param['type'] = '0';
                                }
                                activityDetail.collect(param);
                            });
                            //群聊
                            $(document).on('tap', '.chat', function () {
                                var chatid = $(this).data('chatid') + '';//群聊id
                                var act_name = $('#act_name').text();//活动名
                                var followFlag = $('#act_name').data('act_follow');
                                GotoGroupChat(chatid, act_name, followFlag);
                            });
                            //价格-->门票列表入口
                            $(document).on('tap', '.wjb', function () {
                                var ovoid = maker_id;
                                var act_id = $('#act_name').data('act_id');
                                var act_name = $('#act_name').text();
                                ActivityApply(act_id, ovoid, act_name, 'activity');
                            });
                        }
                    }
                }
                $('#act_container').removeClass('none');
            }

            //共同点
            function commonHTML(selfObj) {
                $('#share_img').data('src',selfObj.share_image);
                $('#bannerPic').attr('src', selfObj.detail_img);//海报图
                $('#act_picsrc').attr('src', selfObj.list_img);//小图
                $('#act_name').html(selfObj.subject);//名称
                $('#act_name').data('act_id', selfObj.id);//活动id
                $('#act_name').data('act_follow', selfObj.follow);//是否报名
                $('#seenNum').html(selfObj.view - 1 + 2);//浏览量
                $('#storeNum').html(selfObj.likes);//收藏
                //数据库浏览量加1
                activityDetail.seenPlus(selfObj.id, '1', 'view');
                /**时间判断**/
                var begin_time = unix_to_datetime(selfObj.begin_time);//开始时间
                var end_time = unix_to_datetime(selfObj.end_time);  //结束时间
                var begin_time_day = begin_time.substring(0, 4);
                var end_time_day = end_time.substring(0, 4);
                if (begin_time_day == end_time_day) {
                    end_time = end_time.slice(5);
                }
                $('#act_time').html(begin_time + ' - ' + end_time);
                /*价格判断*/
                var priceArray = (selfObj.price).split('@');
                //定义sort的比较函数
                priceArray = priceArray.sort(function (a, b) {
                    return a - b;
                });
                if (priceArray[priceArray.length - 1] == 0) {
                    $('#wjbNum').html('免费');//免费
                }
                else if (priceArray[priceArray.length - 1] != 0 && priceArray[0] == priceArray[priceArray.length - 1]) {
                    $('#wjbNum').html(priceArray[priceArray.length - 1] + '元');//一档非免费
                }
                else if (priceArray[0] != priceArray[priceArray.length - 1]) {
                    $('#wjbNum').html(priceArray[0] + '~' + priceArray[priceArray.length - 1] + '元');//多档
                }
                //发布者
                var publisherHtml = '';
                publisherHtml += '<span class="img"><img src="' + selfObj.avatar + '"/></span> <i class="author_name">' + selfObj.nickname + '</i>发布<span class="sj_icon"></span>';
                $('#pubid').html(publisherHtml);
                if (selfObj.is_official == '1') {
                    $('#pubid').attr('value', selfObj.pub_id);//官方
                    $('#pubid').data('is_official', '1');//标志位
                }
                else if (selfObj.is_official == '0') {
                    $('#pubid').attr('value', selfObj.c_uid);//城市合伙人
                    $('#pubid').data('is_official', '0');
                }
               

                //活动详情描述
                var despStr = removeHTMLTag(selfObj.description);
                $('#video_description').html(cutString(despStr, 80));
                //更多详情 value=id,事件在commonjs里
                $('#actMoreDetail').attr('value', selfObj.id);
                //是否专版活动
                if ('vip_id' in selfObj) {
                    $('#zbname').html(selfObj.vip_name);
                    $('#zbname').data('zbid', selfObj.vip_id);
                    $('#timetop').addClass('bordertop');
                    $('#zbrow').removeClass('none');
                }
            }

            //推荐活动格式和是否是分享页有关
            function recActiviy(recObj, is_share, userovoname, current_maker_city) {
                var recAct = '';
                if (is_share) {
                    $.each(recObj, function (index, item) {
                        recAct += '<div class="recact" data-ovoid="' + item.maker_id + '" data-actid="' + item.id + '">';
                        if (item.is_recommend == '1') {
                            recAct += '<div class="recommend"></div>';
                        }
                        recAct += '<div class="reccontent">';
                        recAct += '<img src="' + item.list_img + '" alt="image" class="l">';
                        recAct += '<div class="rightinfo">';
                        recAct += '<p class="act_name">' + item.subject + '</p>';
                        recAct += '<p class="dark_gray f12">' + item.begin_time + ' ' + item.host_cities.join(' ') + '</p>';
                        recAct += '</div>';
                        recAct += '<div class="money_collect">';
                        if (item.price == '0') {
                            recAct += '<span class="money green">免费</span>';
                        }
                        else {
                            recAct += '<span class="money orange">￥<em>' + item.price + '</em>起</span>';
                        }
                        recAct += '<span class="collect"><i class="seen_icon"></i><em>' + item.view + '</em></span>';
                        recAct += '</div>';
                        recAct += '<div class="clearfix"></div>';
                        recAct += '</div>';
                        recAct += '</div>';
                    });
                }
                else {
                    $.each(recObj, function (index, item) {
                        if (item.category == 'A') {
                            recAct += '<div class="recact" data-ovoid="' + item.maker_id + '"  data-actid="' + item.id + '">';
                            if (item.is_recommend == '1') {
                                recAct += '<div class="recommend"></div>';
                            }
                            recAct += '<div class="reccontent">';
                            if (item.is_vip == 1) {
                                recAct += '<span class="reczbflag f12">专版活动</span>';
                            }
                            recAct += '<img src="' + item.list_img + '" alt="image" class="l">';
                            recAct += '<div class="rightinfo">';
                            recAct += '<p class="act_name">' + item.subject + '</p>';
                            var time_hostcity_str = item.begin_time + ' ' + item.host_cities.join(' ');
                            var cutstr = cutString(time_hostcity_str, 20);
                            recAct += '<p class="dark_gray f12">' + cutstr + '</p>';
                            recAct += '</div>';
                            recAct += '<div class="money_collect">';
                            if (item.price == '0') {
                                recAct += '<span class="money green">免费</span>';
                            }
                            else {
                                recAct += '<span class="money orange">￥<em>' + item.price + '</em>起</span>';
                            }
                            recAct += '<span class="collect"><i class="seen_icon"></i><em>' + item.view + '</em></span>';
                            recAct += '</div>';
                            recAct += '<div class="clearfix"></div>';
                            recAct += '</div>';
                            recAct += '<div class="classAB">';
                            recAct += '<img src="{{URL::asset('/')}}/images/ovopic.png"><span>活动举办地有你入驻的本地商圈</span>';

                            recAct += '<span class="green pl05"> ' + userovoname + ' </span>';
                            recAct += '</div>';
                            recAct += '</div>';

                        }
                        else if (item.category == 'B') {
                            recAct += '<div class="recact" data-ovoid="' + item.maker_id + '" data-actid="' + item.id + '">';
                            if (item.is_recommend == '1') {
                                recAct += '<div class="recommend"></div>';
                            }
                            recAct += '<div class="reccontent">';
                            if (item.is_vip == 1) {
                                recAct += '<span class="reczbflag f12">专版活动</span>';
                            }
                            recAct += '<img src="' + item.list_img + '" alt="image" class="l">';
                            recAct += '<div class="rightinfo">';
                            recAct += '<p class="act_name">' + item.subject + '</p>';
                            var time_hostcity_str = item.begin_time + ' ' + item.host_cities.join(' ');
                            var cutstr = cutString(time_hostcity_str, 20);
                            recAct += '<p class="dark_gray f12">' + cutstr + '</p>';
                            recAct += '</div>';
                            recAct += '<div class="money_collect">';
                            if (item.price == '0') {
                                recAct += '<span class="money green">免费</span>';
                            }
                            else {
                                recAct += '<span class="money orange">￥<em>' + item.price + '</em>起</span>';
                            }
                            recAct += '<span class="collect"><i class="seen_icon"></i><em>' + item.view + '</em></span>';
                            recAct += '</div>';
                            recAct += '<div class="clearfix"></div>';
                            recAct += '</div>';
                            recAct += '<div class="classAB">';
                            recAct += '<img src="{{URL::asset('/')}}/images/ovopic.png"><span>活动举办地有你的城市</span>';
                            recAct += '<span class="green pl05"> ' + item.location + ' </span>';
                            recAct += '</div>';
                            recAct += '</div>';
                        }
                        else if (item.category == 'C') {
                            recAct += '<div class="recact" data-ovoid="' + item.maker_id + '" data-actid="' + item.id + '">';
                            if (item.is_recommend == '1') {
                                recAct += '<div class="recommend"></div>';
                            }
                            recAct += '<div class="reccontent">';
                            if (item.is_vip == 1) {
                                recAct += '<span class="reczbflag f12">专版活动</span>';
                            }
                            recAct += '<img src="' + item.list_img + '" alt="image" class="l">';
                            recAct += '<div class="rightinfo">';
                            recAct += '<p class="act_name">' + item.subject + '</p>';
                            var time_hostcity_str = item.begin_time + ' ' + item.host_cities.join(' ');
                            var cutstr = cutString(time_hostcity_str, 20);
                            recAct += '<p class="dark_gray f12">' + cutstr + '</p>';
                            recAct += '</div>';
                            recAct += '<div class="money_collect">';
                            if (item.price == '0') {
                                recAct += '<span class="money green">免费</span>';
                            }
                            else {
                                recAct += '<span class="money orange">￥<em>' + item.price + '</em>起</span>';
                            }
                            recAct += '<span class="collect"><i class="seen_icon"></i><em>' + item.view + '</em></span>';
                            recAct += '</div>';
                            recAct += '<div class="clearfix"></div>';
                            recAct += '</div>';
                            recAct += '</div>';
                        }
                    });
                }
                $('#recommendact').empty();
                $('#recommendact').append(recAct);
            }

            //分享页地址
            function shareOvoAddress(descriptions, maker_ids, city, address, name, type) {
                //var ovodesps = removeHTMLTag(descriptions).split('@');
                var cityArray = city.split('@');
                var idsArray = (maker_ids == '') ? [] : (maker_ids).split('@');
                var addressArray = address.split('@');
                var nameArray = name.split('@');
                //var typeArray = type.split('@');
                var htmlOVOaddress = '';
                if (idsArray.length > 0) {
                    $.each(idsArray, function (index, item) {
                        htmlOVOaddress += '<dd class="address_list">';
                        htmlOVOaddress += '<span class="address_icon"></span>';
                        htmlOVOaddress += ' <div class="infor " data-address_id="' + idsArray[index] + '">';
                        htmlOVOaddress += '<p class="nameflag">' + nameArray[index] + '</p>';
                        htmlOVOaddress += '<p class="addressflag">' + addressArray[index] + '</p><span class="sj_icon"></span>';
//                        htmlOVOaddress += '<div class="none ovodesp">' + ovodesps[index] + '</div>';
                        htmlOVOaddress += '</div>';
                        htmlOVOaddress += '</dd>';
                    });
                }
                $('#address_flag').empty();
                $('#address_flag').html(htmlOVOaddress);
            }

            //app内部举办地
            function innerOvoAddress(cityarray, maker_id, ovoname, ovoaddress, currentcity, desp) {
                var htmlOVOaddress = '';
                var cityArray = cityarray.split('@');
                htmlOVOaddress += '<dd class="address_list">';
                htmlOVOaddress += '<span class="address_icon" data-address_id="' + maker_id + '"></span>';
                htmlOVOaddress += '<div class="infor "><p class="nameflag">' + ovoname + '</p>';
                htmlOVOaddress += '<p class="addressflag">' + ovoaddress + '</p>';
                var cityindex = cityArrIndex(currentcity, cityArray);
                cityArray.splice(cityindex, 1);
                if (cityArray.length > 0) {
                    cityArray.join(' ');
                    htmlOVOaddress += '<p class="f12 colorc8">本活动还包括<i class="pl05">' + cityArray + '</i></p>';
                }
                htmlOVOaddress += '<span class="sj_icon"></span>';
                var desOvo = removeHTMLTag(desp);
                htmlOVOaddress += '<div class="none ovodesp">' + desOvo + '</div>';
                htmlOVOaddress += '</div>';
                htmlOVOaddress += '</dd>';
                $('#address_flag').empty();
                $('#address_flag').html(htmlOVOaddress);
            }
            //城市、城市数组 返回位置索引
            function cityArrIndex(cityname, cityarray) {
                return $.inArray(cityname, cityarray);
            }
            //邀请
            function invites(act_id, maker_id) {
                if (isAndroid) {
                    javascript:myObject.invites(act_id, maker_id);
                } else if (isiOS) {
                    var data = {
                        "act_id": act_id,
                        "maker_id": maker_id
                    }
                    window.webkit.messageHandlers.invites.postMessage(data);
                }
            }
            //uid、maker_id
            function changeid(uid) {
                if (uid == 0) {
                    maker_id = 0;
                }
            }
            var brandHtml='';
            function getBrand(obj,is_share){
                var brandHtml='<div class="act_title "><p>相关品牌</p></div>';
                $.each(obj.brands,function(index,item){
                     brandHtml+='<div class="brand_intro" data="'+item.id+'"><img src="'+item.logo+'" alt="logo">';
                     brandHtml+='<div><p class="brand_name color3 f16">'+item.name+'<span class="color6">【'+item.zone_name+'】</span></p>';
                     brandHtml+='<p class="industry"><span>'+item.category_name+'</span>'+item.investment_min+'万元-'+item.investment_max+'万元</p><p class="keywords">';
                     $.each(item.keywords,function(i,j){
                    brandHtml+='<span>'+cutString(j,5)+'</span>';     
                    });
                     brandHtml+='</p></div></div>'; 
                });
                $('#brand').html(brandHtml);
                $('.brand_intro').click(function(){
                    var id=$(this).attr('data');
                   if(oldVersion){
                        if(is_share){
                             window.location.href=labUser.path+'webapp/brand/detail?id='+id+'&is_share=1&pagetag=02-1-2&uid='+uid;
                        }else{
                             window.location.href=labUser.path+'webapp/brand/detail?id='+id+'&pagetag=02-1-2&uid='+uid;
                        }
                   }else{
                        alert('版本低,请更新至最新版本！')
                   }
                })
            };
        });
    </script>
    <script>
        //分享
        function showShare() {
            var title = $('#act_name').text();
            var url = window.location.href;
            var img = $('#share_img').data('src').replace(/https:/g,'http:');
            var header = '活动';
            var content = cutString($('#video_description').text(), 25);
            shareOut(title, url, img, header, content);
        }
        //刷新
        function reload() {
            location.reload();
        }

        //收藏/取消收藏
        function favourite() {
            $('.collectbtn').trigger('click');
            var colText = $('.collectbtn').text();
            if (colText == '收藏') {
                setFavourite('0');
            }
            else {
                setFavourite('1');
            }
        }
    </script>
@stop