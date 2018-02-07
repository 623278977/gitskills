var player;
;var Video = {
    //live
    detail: function (parame, shareFlag,newVersionFlag) {
        var param = parame;
        if (shareFlag) {
            param.uid = 0;
        }
        var id = param.id,
        uid = param.uid,
        params = {};
        params["id"] = id;
        params["uid"] = uid;
        params["platform"] = param.platform;
        var url = labUser.api_path + '/live/detail';
        ajaxRequest(params, url, function (data) {
            if (data.status) {
                var st_out;
                var objVideos = data.message.videos || null;
                var objLive = data.message.live,
                objActivity = data.message.activity,
                arrRecLive = data.message.rec,
                live_url = objLive.live_url,
                ticket_price = parseInt(objLive.ticket),
                timenow = objLive.time_now;
                objLive["id"] = id;
                $('#livesubject').data('begintime', objLive.begin_time);
                $('#livesubject').html(objLive.subject);
                if (objLive.end_time < timenow) {
                    if (shareFlag) {
                        $('#containerBox').remove();///remove include self
                        $("#act_picsrc_s").attr('src', objActivity.detail_img);
                        $("#act_name_s").html(objActivity.subject);
                        var begin_time = unix_to_datetime(objLive.begin_time);
                        var end_time = unix_to_datetime(objLive.end_time);
                        var begin_time_day = begin_time.substring(0, 5);
                        var end_time_day = end_time.substring(0, 5);
                        if (begin_time_day == end_time_day) {
                            end_time = end_time.slice(5);
                        }
                        $("#act_time_s").html(begin_time + ' - ' + end_time);
                        $('#otimeappc').removeClass('none');
                        if (is_weixin()) {
                            //in weixin
                            $(document).on('tap', '#otimeapp', function () {
                                var _height = $(document).height();
                                $('.timeover').css('height', _height);
                                $('.timeover').removeClass('none');
                            });
                            //click remove overburden
                            $(document).on('tap', '.timeover', function () {
                                $(this).addClass('none');
                            });
                            var wxurl = labUser.api_path + '/weixin/js-config';
                            //activity detail description
                            var desptStr = removeHTMLTag(objActivity.description);
                            var despt = cutString(desptStr, 60);
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
                                            title: objActivity.subject,
                                            link: location.href,
                                            imgUrl: objActivity.share_image,
                                            success: function () {
                                                // callback when success
                                            },
                                            cancel: function () {
                                            }
                                        });
                                        wx.onMenuShareAppMessage({
                                            title: objActivity.subject,
                                            desc: despt,
                                            link: location.href,
                                            imgUrl: objActivity.share_image,
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
                                    // regsiterWX(selfObj.v_subject,selfObj.detail_img,location.href,selfObj.v_description,'','');
                                }
                            });
                        }
                        else {
                            if (isiOS) {
                                //open app
                                $(document).on('tap', '#otimeapp', function () {
                                    var strPath = window.location.pathname.substring(1);
                                    var strParam = window.location.search;
                                    var appurl = strPath + strParam;
                                    var share = '&is_share';
                                    var appurl2 = appurl.substring(0, appurl.indexOf(share));
                                    window.location.href = 'openwjsq://' + appurl2;
                                });
                                //download app
                                //$(document).on('tap', '#otimeapp', function () {
                                //    window.location.href = 'https://itunes.apple.com/app/id981501194';
                                //});
                            }
                            else if (isAndroid) {
                                //$(document).on('tap', '#loadapp', function () {
                                //    window.location.href = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.tyrbl.wujiesq';
                                //});
                                $(document).on('tap', '#otimeapp', function () {
                                    var strPath = window.location.pathname;
                                    var strParam = window.location.search.replace(/is_share=1/g, '');
                                    var appurl = strPath + strParam;
                                    window.location.href = 'openwjsq://welcome' + appurl;
                                });
                            }
                        }
                        $('#timeoverbox').removeClass('none');
                    }
                    else {//live finish
                        $('#timeoverbox').remove();//remove including self
                        $('#containerBox').empty();
                        var contentHtml = '<section>';
                        contentHtml += '<div data-src="' + objActivity.share_image + '" class="none" id="share_img"></div>';
                        contentHtml += '<div class="liveover f12 hascolor"><p>很遗憾，本场直播已结束！</p><p>不过，你可以戳下面的录播，直播精彩一个都不落！</p></div>'
                        contentHtml += '<div class="act_intro block">';
                        contentHtml += '<dl><div class="act-block" data-activity_id="' + objActivity.id + '" id="actdetail">';
                        contentHtml += '<dt class="act_pics" style="position: relative;">';
                        contentHtml += '<img src="' + objActivity.detail_img + '" alt="">';
                        contentHtml += '<img style="position: absolute;left:3.3rem;top:2.1rem;width:6.4rem;height:3rem;" src="/images/finished.png">';
                        if (objLive.is_brand_live == '1') {
                            contentHtml += '<span class="businessflag f12 none tc">推介会 / 招商会</span>';
                        }
                        contentHtml += '</dt>';
                        contentHtml += '<dd class="act_name wrap" id="act_name">' + objLive.subject + '</dd>';
                        contentHtml += '<span class="sj_icon top3"></span>';
                        contentHtml += '<div class="clearfix"></div>';
                        contentHtml += '</div>';
                        contentHtml += '<div class="act_address" style="height: auto;">';
                        contentHtml += '<dd class="time">';
                        contentHtml += '<span class="time_icon"></span>';
                        contentHtml += '<div class="infor" id="timetop">';
                        contentHtml += '<p>直播开始时间：' + unix_to_yeardatetime(objLive.begin_time) + '</p>';
                        contentHtml += '</div></dd>';
                        contentHtml += '<div id="address_flag" style="max-height:none;">';
                        contentHtml += '<dd class="address_list">';
                        contentHtml += '<span class="address_icon" style="margin-top: 1.33rem;"></span>';
                        contentHtml += '<div class="infor nobottomborder">';
                        contentHtml += '<p class="nameflag">活动现场：' + objActivity.city.split('@').join('、') + '</p>';
                        contentHtml += '</div></dd>';
                        contentHtml += '</div>';
                        contentHtml += '</div>';
                        contentHtml += '<div class="clearfix"></div>';
                        contentHtml += '</dl>';
                        contentHtml += '</div>';
                        if (objActivity.is_official == '1') {
                            contentHtml += '<div class="block author relative" value="' + objActivity.pub_id + '" data-is_official="1" id="publisher">';
                        }
                        else {
                            contentHtml += '<div class="block author relative" value="' + objActivity.c_uid + '" data-is_official="0" id="publisher">';
                        }
                        contentHtml += '<span class="img"><img src="' + objActivity.avatar + '" alt=""/></span> <i class="author_name">' + objActivity.nickname + '</i>发布<span class="sj_icon"></span></div>';
                        contentHtml += '<div class="block video_detail">';
                        contentHtml += '<div class="f14 text">相关视频录播</div>';
                        if (objVideos) {
                            $.each(objVideos, function (index, item) {
                                contentHtml += '<div class="livevideo" data-videoid="' + item.id + '">';
                                if (item.is_recommend == '1') {
                                    contentHtml += '<div class="recommend"></div>';
                                }
                                contentHtml += '<div><img src="' + item.video_image + '" class="actimg fl"/>';
                                contentHtml += '<div class="title_description fr"><p class="fl f16">' + cutString(item.subject, 30) + '</p><p class="textdesp f12 color999">' + cutString(removeHTMLTag(item.description), 36) + '</p></div>';
                                contentHtml += '<div class="clearfix"></div></div>';
                                contentHtml += '<div class="bottomtip">';
                                contentHtml += '<img src="' + item.small_image + '"/><span class="f14 pl05">' + item.video_type + '</span><div class="viewslikes"><span class="seen"><i class="seen_icon"></i><em class="f12 color999">' + item.view + '</em></span><span class="collect"><i class="collect_icon"></i><em class="f12 color999">' + item.likes + '</em></span></div>';
                                contentHtml += '</div>';
                                contentHtml += '</div>';
                            });
                        }
                        else {
                            contentHtml += '<div class="novideo"><img src="/images/liveflag.png"/><p class="color999">视频还在制作中，请耐心等待 ~ </p></div>';
                        }
                        contentHtml += '</div>';
                        //is brand-live
                        if (objLive.is_brand_live == '1') {
                            var brandHtml = '';
                            contentHtml += '<section class="brandcontain">';
                            contentHtml += '<div class="brandtext f14"><span class="brand_text">相关品牌</span></div>';
                            $.each(objLive.brands, function (index, item) {
                                var keywordhtml = '';
                                brandHtml += '<div class="brandcontent" style="position: relative" data-brand_id="' + item.id + '">';
                                brandHtml += '<img src="' + item.logo + '" alt="">';
                                brandHtml += '<div class="branddetail f14">';
                                brandHtml += '<p class="f16"><span>' + cutString(item.name, 10) + '</span><span class="color666">【' + item.zone_name + '】</span></p>';
                                brandHtml += '<p>';
                                brandHtml += '<em class="brand-sort">' + item.category_name + '</em> <span class="brand-st pl05">' + item.investment_min + ' 万元 - ' + item.investment_max + ' 万元</span>';
                                brandHtml += '</p>';
                                brandHtml += '<p class="brand-keyword">';
                                if (item.keywords.length > 0) {
                                    $.each(item.keywords, function (index, oneitem) {
                                        keywordhtml += '<span>' + oneitem + '</span>';
                                    });
                                    brandHtml += keywordhtml;
                                }
                                brandHtml += '</p>';
                                brandHtml += '</div>';
                                brandHtml += '<div class="clearfix"></div>';
                                brandHtml += '<span class="sj_icon top4-5"></span>';
                                brandHtml += '</div>';
                            });
                            contentHtml += brandHtml;
                            contentHtml += '</section>';
                        }
                        contentHtml += '<div class="block video_detail">';
                        contentHtml += '<div class="livetext"><span class="brand_text">直播介绍</span></div>';
                        contentHtml += '<div class="text topborder" id="video_description">' + cutString(removeHTMLTag(objLive.description), 80) + '</div>';
                        contentHtml += '<div class="seen_more topborder" id="videosMoreDetail" value="' + id + '" data-type="live">更多直播详情<span class="sj_icon"></span></div>';
                        contentHtml += '</div>';
                        contentHtml += '</section>';
                        $('#containerBox').html(contentHtml).removeClass('none');
                        //to publisher
                        $(document).on('click', '#publisher', function () {
                            var pub_id = $(this).attr("value");
                            var is_official = $(this).data('is_official');
                            gotoActivityList(pub_id, is_official);
                        });
                        //to video-detail
                        $(document).on('click', '.livevideo', function () {
                            var vid = $(this).data('videoid');
                            window.location.href = labUser.path + "webapp/vod/detail?id=" + vid + "&pagetag=05-4&uid=" + labUser.uid;
                        });
                        //to live description
                        $(document).on('click', '.seen_more', function () {
                            var id = $(this).attr("value");
                            window.location.href = labUser.path + "webapp/activity/detaildescription?id=" + id + "&pagetag=08-8&type=live";
                        });
                        //version=2.3
                        if(newVersionFlag){
                            // to brand-detail
                            $(document).on('click', '.brandcontent', function () {
                                var brand_id = $(this).data('brand_id');
                                window.location.href = labUser.path + "webapp/brand/detail?id=" + brand_id + "&uid=" + uid + "&pagetag=02-1-2&version=2.3";
                            });
                            //to activity-detail
                            $(document).on('click', '#actdetail', function () {
                                var id = $(this).data('activity_id');
                                window.location.href = labUser.path + "webapp/activity/detail?id=" + id + "&uid=" + uid + "&pagetag=02-2&position_id=0&makerid=0&version=2.3";
                            });
                        }
                        else{
                            // to brand-detail
                            $(document).on('click', '.brandcontent', function () {
                                alert('版本低，请升级至最新版');
                            });
                            //to activity-detail
                            $(document).on('click', '#actdetail', function () {
                                var id = $(this).data('activity_id');
                                window.location.href = labUser.path + "webapp/activity/detail?id=" + id + "&uid=" + uid + "&pagetag=02-2&position_id=0&makerid=0";
                            });
                        }

                    }
                    increaseViewn(id, 'live');
                }
                else {
                    //detail-info
                    liveHtml(objLive, objActivity, shareFlag);
                    if (shareFlag) {
                        if(newVersionFlag){
                            getLiveRecomment(arrRecLive);
                            //to share page of brand-detail
                            $(document).on('click', '.psrelative', function () {
                                var brand_id = $(this).data('brand_id');
                                window.location.href = labUser.path + "webapp/brand/detail?id=" + brand_id + "&uid=" + uid + "&pagetag=02-1-2&is_share=1&version=2.3";
                            });
                            //to share page of activity-detail
                            $(document).on('click', '.act-block', function () {
                                var id = $(this).data('activity_id');
                                window.location.href = labUser.path + "webapp/activity/detail?id=" + id + "&uid=" + uid + "&pagetag=02-2&makerid=0&position_id=0&is_share=1&version=2.3";
                            });
                            //to recommend live
                            $(document).on('click',".recommend-video",function () {
                                var id = $(this).data('liveid');
                                window.location.href = labUser.path + "webapp/live/detail?id=" + id + "&uid=" + labUser.uid + "&pagetag=04-9&is_share=1&version=2.3";
                            });
                        }
                        else{
                            //to share page of activity-detail
                            $(document).on('click', '.act-block', function () {
                                var id = $(this).data('activity_id');
                                window.location.href = labUser.path + "webapp/activity/detail?id=" + id + "&uid=" + uid + "&pagetag=02-2&makerid=0&position_id=0&is_share=1";
                            });
                            //to recommend live
                            //$(document).on('click',".recommend-video",function () {
                            //    var id = $(this).data('liveid');
                            //    window.location.href = labUser.path + "webapp/live/detail?id=" + id + "&uid=" + labUser.uid + "&pagetag=04-9&is_share=1";
                            //});
                        }
                        //close fast register
                        $(document).on('click', '#registerpart .closepic', function () {
                            $(this).parent().parent().addClass('none');
                            $('#zcphone').val('');
                            $('#zcyzm').val('');
                        });
                        //submit fast register
                        $('#registerbtn').on('click', function () {
                            var params = {};
                            params.tel = $('#zcphone').val();
                            params.code = $('#zcyzm').val();
                            params.live_id = id;
                            var url = labUser.api_path + '/live/sharesubscibe';
                            ajaxRequest(params, url, function (data) {
                                if (data.status) {
                                    if (window.localStorage) {
                                        localStorage.setItem('isregister', 'yes');
                                    }
                                    $('#registerpart').addClass('none');
                                    $('#zcphone').val('');
                                    $('#zcyzm').val('');
                                    $('.share_video .share_text').html('');
                                    $(".share_video").hide();
                                    getLive(live_url, objLive.end_time, 0, ticket_price, true, id);
                                }
                            });
                        });
                        //get fast register pincode
                        $('#mescode').on('click', function () {
                            var _this = $(this);
                            var timeout = 59;
                            var params = {};
                            params.tel = $('#zcphone').val();
                            if ((/^1[34578][0-9]\d{8}$/).test(params.tel)) {
                                var url = labUser.api_path + '/live/sendcode';
                                ajaxRequest(params, url, function (data) {
                                });
                                _this.css('backgroundColor', '#999');
                                _this.attr('disabled', 'disabled');
                                var setin = setInterval(function () {
                                    if (timeout == 0) {
                                        _this.removeAttr('disabled');
                                        _this.css('backgroundColor', '#1e8cd4');
                                        _this.html('获取验证码');
                                        clearInterval(setin);
                                    } else {
                                        _this.html('重新获取(' + timeout + ')');
                                        timeout--;
                                    }
                                }, 1000);
                            }
                            else {
                                console.log('phone number is not matched');
                            }
                        });
                        if (objLive.begin_time < timenow) {
                            //live already began
                            $('#reserve').addClass('none');
                            $('#loadapp').css('width', '100%');
                        }
                        else {
                            //set live remind
                            $('#livename').html('直播名称：' + objActivity.subject);
                            var live_begin_time = unix_to_fulltime(objLive.begin_time);
                            $('#livetime').html('开始时间：' + live_begin_time);
                            //close overwindow
                            $(document).on('click', '#liveremind .closepic,#remindsuccess .closepic', function () {
                                if (st_out) {
                                    $('#getcode').removeAttr('disabled');
                                    $('#getcode').css('backgroundColor', '#1e8cd4');
                                    $('#getcode').html('获取验证码');
                                    clearInterval(st_out);
                                }
                                $('#yyphone').val('');
                                $('#yyyzm').val('');
                                $(this).parent().parent().addClass('none');
                            });
                            //not begin
                            $(document).on('click', '#reserve', function () {
                                //设置直播提醒
                                $('#liveremind').removeClass('none');
                            });
                            //直播提醒发送验证码
                            $('#getcode').on('click', function () {
                                var _this = $(this);
                                var timeout = 59;
                                var params = {};
                                params.tel = $('#yyphone').val();
                                if ((/^1[34578][0-9]\d{8}$/).test(params.tel)) {
                                    var url = labUser.api_path + '/live/sendcode';
                                    ajaxRequest(params, url, function (data) {
                                    });
                                    _this.css('backgroundColor', '#999');
                                    _this.attr('disabled', 'disabled');
                                    st_out = setInterval(function () {
                                        if (timeout == 0) {
                                            _this.removeAttr('disabled');
                                            _this.css('backgroundColor', '#1e8cd4');
                                            _this.html('获取验证码');
                                            clearInterval(st_out);
                                        } else {
                                            _this.html('重新获取(' + timeout + ')');
                                            timeout--;
                                        }
                                    }, 1000);
                                }
                                else {
                                    console.log('phone number is not matched');
                                }
                            });

                            //预约直播提交
                            $('#yysubmit').on('click', function () {
                                var params = {};
                                params.tel = $('#yyphone').val();
                                params.code = $('#yyyzm').val();
                                params.live_id = id;
                                var url = labUser.api_path + '/live/sharesubscibe';
                                ajaxRequest(params, url, function (data) {
                                    if (data.status) {//隐藏提醒
                                        $('#liveremind').addClass('none');
                                        $('#yyphone').val('');
                                        $('#yyyzm').val('');
                                        if (data.message == '1') {
                                            $('#remindsuccess').removeClass('none');
                                        }
                                        else if (data.message == '2') {
                                            $('#membertips').html('欢迎成为无界商圈一员，账号、密码为预约所填手机号码。请及时登录无界商圈并对密码进行修改。');
                                            $('#remindsuccess').removeClass('none');
                                        }
                                    }
                                });
                            });
                            //订阅按钮
                            $(document).on("click", "button.order", function () {
                                //设置直播提醒
                                $('#liveremind').removeClass('none');
                            });
                        }
                        if ('vip_id' in objLive) {
                            $('#zbname').html(objLive.vip_name);
                            $('#zbname').data('zbid', objLive.vip_id);
                            //$('#timetop').addClass('bordertop');
                            $('#zbrow').removeClass('none');
                            //查看专版
                            $(document).on('tap', '#zbrow', function () {
                                var vip_id = $('#zbname').data('zbid');
                                window.location.href = labUser.path + 'webapp/special/detail?is_share=1&pagetag=02-1&vip_id=' + objLive.vip_id + '&uid=0';
                            });
                        }
                        $(".videonews_box").css("paddingTop", "26.6rem");
                        //remove upload pictures award
                        $('.uploadpic,.uploadpictext').remove();
                        $('.comment_btn button').css('width', '100%');
                        $("#daojishi").css("top", "3.5rem");
                        $("#video_box").css("top", "3.5rem");
                        $("#livecount").css("top", "23rem");
                        $(".share_video").css("top", "3.5rem");
                        $("#wrapper").css("top", '30.5rem');
                        $("#installapp").show();//打开
                        $('#loadAppBtn').removeClass('none');//下载
                        //下载、打开事件
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
                            var desptStr = removeHTMLTag(objActivity.description);
                            var despt = cutString(desptStr, 60);
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
                                            title: objActivity.subject, // 分享标题
                                            link: location.href, // 分享链接
                                            imgUrl: objActivity.share_image, // 分享图标
                                            success: function () {
                                                // 用户确认分享后执行的回调函数
                                            },
                                            cancel: function () {
                                                // 用户取消分享后执行的回调函数
                                            }
                                        });
                                        wx.onMenuShareAppMessage({
                                            title: objActivity.subject,
                                            desc: despt,
                                            link: location.href,
                                            imgUrl: objActivity.share_image,
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
                                    // regsiterWX(selfObj.v_subject,selfObj.detail_img,location.href,selfObj.v_description,'','');
                                }
                            });
                        }
                        else {
                            if (isiOS) {
                                //打开本地
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
                                    var strParam = window.location.search.replace(/is_share=1/g, '');
                                    var appurl = strPath + strParam;
                                    window.location.href = 'openwjsq://welcome' + appurl;
                                });
                            }
                        }
                        $('.awardpicture').on('click', function () {
                            alert('请登录无界商圈APP');
                        });
                        /**detail-info block**/
                        $("#detail_block").click(function () {
                            $(this).addClass('green').siblings().removeClass('green');
                            $("#barnd_list").hide();
                            $(".comment_btn").hide();
                            $("#comment").hide();
                            $('#loadAppBtn').removeClass('none');
                            $("#act_intro").show();
                        });
                        /**comment-block**/
                        $("#comment_block").click(function () {
                            $(this).addClass('green').siblings().removeClass('green');
                            $('#loadAppBtn').addClass('none');
                            $("#act_intro").hide();
                            $("#barnd_list").hide();
                            $("#comment").show();
                            $(".comment_btn").show();
                        });
                        /*addin-block*/
                        $('#addin_block').click(function () {
                            if (new Date().getTime() / 1000 > objLive.begin_time) {
                                $(this).addClass('green').siblings().removeClass('green');
                                $("#act_intro").hide();
                                $("#comment").hide();
                                $(".comment_btn").hide();
                                $("#barnd_list").show();
                                $('#loadAppBtn').removeClass('none');
                            }
                            else {
                                alert('直播尚未开启,请等待');
                            }
                        });
                        /**submit comments**/
                        $('#subcomments').on('click', function () {
                            param.content = utf16toEntities($("#comtextarea").val());
                            if (param.content) {
                                Comment.addComment(param, 'no');
                            }
                            $("#comtextarea").val("");
                            $("#commentback").addClass('none');
                        });

                        //buy goods
                        $(document).on('click', '.canbuy', function () {
                            alert('请登录APP进行相关操作');
                        });
                        //开始时间、结束时间
                        timeOutShow(objLive.begin_time, objLive.endtime, function () {
                            //实例化直播隐藏一些
                            $('#reserve').addClass('none');
                            $('#loadapp').css('width', '100%');
                            //订阅框
                            $('#liveremind').addClass('none');
                            $('#remindsuccess').addClass('none');
                            playlive(live_url, objLive.endtime, 0, ticket_price, shareFlag, id);
                        });
                    }
                    else {
                        if(newVersionFlag){
                            getLiveRecomment(arrRecLive);
                            //to brand-detail
                            $(document).on('click', '.psrelative', function () {
                                var brand_id = $(this).data('brand_id');
                                window.location.href = labUser.path + "webapp/brand/detail?id=" + brand_id + "&uid=" + uid + "&pagetag=02-1-2";
                            });
                            //to activity detail
                            $(document).on('click', '.act-block', function () {
                                var id = $(this).data('activity_id');
                                window.location.href = labUser.path + "webapp/activity/detail?id=" + id + "&uid=" + uid + "&pagetag=02-2&makerid=0&position_id=0&version=2.3";
                            });
                            //to recommend live
                            $(".recommend-video").tap(function () {
                                var id = $(this).data('liveid');
                                window.location.href = labUser.path + "webapp/live/detail?id=" + id + "&uid=" + uid + "&pagetag=04-9&version=2.3";
                            });
                            //buy goods
                            $(document).on('click', '.canbuy', function () {
                                var goodsid = $(this).data('goodsid');
                                var type = 'live';
                                buygoods(goodsid, type);
                            });
                        }
                        else{
                            //to brand-detail
                            $(document).on('click', '.psrelative', function () {
                                alert('版本低，请升级至最新版');
                            });
                            //to activity detail
                            $(document).on('click', '.act-block', function () {
                                var id = $(this).data('activity_id');
                                window.location.href = labUser.path + "webapp/activity/detail?id=" + id + "&uid=" + uid + "&pagetag=02-2&makerid=0&position_id=0";
                            });
                            //to recommend live
                            $(".recommend-video").tap(function () {
                                var id = $(this).data('liveid');
                                window.location.href = labUser.path + "webapp/live/detail?id=" + id + "&uid=" + uid + "&pagetag=04-9";
                            });
                            //buy goods
                            $(document).on('click', '.canbuy', function () {
                                alert('版本低，请升级至最新版');
                            });
                        }
                        //to live list
                        $('#tolivelist').on('click', function () {
                            gotoLiveList(id);
                        });

                        /**detail-info block**/
                        $("#detail_block").click(function () {
                            $(this).addClass('green').siblings().removeClass('green');
                            var shows = $('#buyBtn').data('isshow');
                            if (shows == 'yes') {
                                $("#buyonline").show();
                            }
                            $("#comment").hide();
                            $(".comment_btn").hide();
                            $("#barnd_list").hide();
                            $("#act_intro").show();
                        });
                        /**comments blcok**/
                        $("#comment_block").click(function () {
                            $(this).addClass('green').siblings().removeClass('green');
                            var shows = $('#buyBtn').data('isshow');
                            if (shows == 'yes') {
                                $("#buyonline").hide();
                            }
                            $("#act_intro").hide();
                            $("#barnd_list").hide();
                            $("#comment").show();
                            $(".comment_btn").show();
                        });
                        /**brand-list**/
                        $("#addin_block").click(function () {
                            if (new Date().getTime() / 1000 > objLive.begin_time) {
                                $(this).addClass('green').siblings().removeClass('green');
                                var shows = $('#buyBtn').data('isshow');
                                if (shows == 'yes') {
                                    $("#buyonline").hide();
                                }
                                $("#act_intro").hide();
                                $(".comment_btn").hide();
                                $("#comment").hide();
                                $("#barnd_list").show();
                            }
                            else {
                                alert('直播尚未开启,请等待');
                            }
                        });
                        //submit comment
                        $('#subcomments').on('click', function () {
                            param.content = utf16toEntities($("#comtextarea").val());
                            if (param.content) {
                                Comment.addComment(param, 'no');
                            }
                            $("#comtextarea").val("");
                            $("#commentback").addClass('none');
                        });
                        //upload pictures
                        $('.uploadpic,.uploadpictext').on('click', function () {
                            uploadpic(param.id, 'Live',false);
                        });
                        //reward
                        $('.awardpicture').on('click', function () {
                            if (uid == '0') {
                                showLogin();
                            }
                            else {
                                reward(param.id, 'live');
                            }
                        });
                        if ('vip_id' in objLive) {
                            //专版
                            $('#zbname').html(objLive.vip_name);
                            $('#zbname').data('zbid', objLive.vip_id);
                            $('#zbrow').removeClass('none');
                            //查看专版
                            $(document).on('tap', '#zbrow', function () {
                                var vip_id = $('#zbname').data('zbid');
                                window.location.href = labUser.path + 'webapp/special/detail?pagetag=02-1&vip_id=' + objLive.vip_id + '&uid=' + uid;
                            });
                            //不是专版会员且没有购买
                            if (objLive.is_authorize == 0 && objLive.is_purchase == 0) {
                                var buyBtnHtml = '';
                                buyBtnHtml += '<div class="buy fixed_btn none" id="buyonline">';
                                buyBtnHtml += '<div class="infor l f12 buyMemBtn">';
                                buyBtnHtml += '<div class="orange mt05">成为专版会员</div>';
                                buyBtnHtml += '<div>会员专享，专版直播劲享免费</div>';
                                buyBtnHtml += '</div>';
                                buyBtnHtml += '<button class="l buy_btn" id="buyBtn" data-isshow="no"></button>';
                                buyBtnHtml += '</div>';
                                $('.containerBox').append(buyBtnHtml);
                                $('#buyBtn').html('¥' + ticket_price + ' ' + '购买直播门票');//票价
                                $('#buyBtn').data('ticket_id', objLive.ticket_id);//门票id
                                $('#buyBtn').data('isshow', 'yes');
                                $('#buyonline').show();//显示购买
                                //开始时间、结束时间
                                timeOutShow(objLive.begin_time, objLive.endtime, function () {
                                    if (uid > 0) {
                                        $("#video_box").hide();
                                        $('.share_video .share_text').html('请点击屏幕底部"购买"按钮,购买本视频');
                                        $(".share_video").show();
                                    }
                                    else {
                                        $("#video_box").hide();
                                        $('.share_video .share_text').html('请登录后购买本视频');
                                        $(".share_video").show();
                                    }
                                    //getLive(live_url, 60, uid, ticket_price);
                                    //playlive(live_url, objLive.endtime, uid, ticket_price, shareFlag);
                                    $('.videotoptip').removeClass('none');
                                    var st = setTimeout(function () {
                                        $('.videotoptip').addClass('none');
                                        clearTimeout(st);
                                    }, 5000);
                                });
                                //直播票购买
                                $(document).on('click', '.buy_btn', function () {
                                    //var liveid = id;
                                    var act_id = $("#acttitle").data("activity_id");
                                    var ticket_id = $(this).data('ticket_id');
                                    var type = 'live';
                                    var ovoid = $('.address_list').eq(0).find('.infor').data('address_id');
                                    buyTicket(act_id, ticket_id, type, ovoid);
                                    //console.log(act_id, ticket_id, type, ovoid);
                                });
                                //注册购买专版会员
                                $(document).on('click', '.buyMemBtn', function () {
                                    var vip_id = $('#zbname').data('zbid');
                                    buyVip(vip_id);
                                });
                            }
                            else {
                                $('.videotoptip').text('已购买该专版直播，点击"订阅"，放入订阅列表并及时提醒直播观看').removeClass('none');
                                var st = setTimeout(function () {
                                    $('.videotoptip').addClass('none');
                                    clearTimeout(st);
                                }, 5000);
                                timeOutShow(objLive.begin_time, objLive.endtime, function () {
                                    getLive(live_url, 0, uid, ticket_price, shareFlag, id);
                                    //横幅,不是会员
                                    if (objLive.is_authorize == 0) {
                                        $('.videotoptip').text('已购买单次会员视频服务，请享受无界商圈为你带来的视频服务').removeClass('none');
                                        var st = setTimeout(function () {
                                            $('.videotoptip').addClass('none');
                                            clearTimeout(st);
                                        }, 5000);
                                    }
                                    $(".column>span[type=comment]").click();
                                });
                            }
                        }
                        else {//非专版
                            if (objLive.is_purchase == 0 && ticket_price > 0) {
                                var buyHtml = '';
                                buyHtml += '<div class="buy fixed_btn none" id="buyonline">';
                                buyHtml += '<div class="infor l">';
                                buyHtml += '<p class="red">本场直播需要购买才能观看</p>';
                                buyHtml += '<p>直播票<span class="orange money online_price"></span></p>';
                                buyHtml += '</div>';
                                buyHtml += '<button class="l buy_btn" id="buyBtn" data-isshow="no">购买</button>';
                                buyHtml += '</div>';
                                $('.containerBox').append(buyHtml);
                                $('.online_price').html(' ¥' + ticket_price);
                                $('#buyBtn').data('ticket_id', objLive.ticket_id);//门票id
                                $('#buyBtn').data('isshow', 'yes');
                                $('#buyonline').show();//显示购买
                                timeOutShow(objLive.begin_time, objLive.endtime, function () {
                                    //getLive(live_url, 60, uid, ticket_price);
                                    if (uid > 0) {
                                        $("#video_box").hide();
                                        $('.share_video .share_text').html('请点击屏幕底部"购买"按钮,购买本视频');
                                        $(".share_video").show();
                                    }
                                    else {
                                        $("#video_box").hide();
                                        $('.share_video .share_text').html('请登录后购买本视频');
                                        $(".share_video").show();
                                    }
                                });
                                //直播票购买
                                $(document).on('click', '.buy_btn', function () {
                                    var act_id = $('#acttitle').data('activity_id');
                                    var ticket_id = $(this).data('ticket_id');
                                    var type = 'live';
                                    var ovoid = $('.address_list').eq(0).find('.infor').data('address_id');
                                    buyTicket(act_id, ticket_id, type, ovoid);
                                });
                            }
                            else {
                                timeOutShow(objLive.begin_time, objLive.endtime, function () {
                                    if (uid == 0) {
                                        //getLive(live_url, 300, 0, ticket_price);
                                        $("#video_box").hide();
                                        $('.share_video .share_text').html('请登录后观看本视频');
                                        $(".share_video").show();
                                    }
                                    else {
                                        getLive(live_url, 0, uid, ticket_price, shareFlag, id);
                                        //切换到互动
                                        $(".column>span[type=comment]").click();
                                    }
                                });
                            }
                        }
                        if (uid == '0') {//未登录
                            /**ding yue**/
                            $(document).on("click", "button.order", function () {
                                showLogin();
                            });
                        } else {
                            /**ding yue**/
                            $(document).on("click", "button.order", function () {
                                var subscribe = $(".order").data("subscribe");
                                if (subscribe == 0) {
                                    subscribe = 1;
                                    $(".order").data("subscribe", 1);
                                } else {
                                    subscribe = 0;
                                    $(".order").data("subscribe", 0);
                                }
                                Video.order(param.id, subscribe);
                            });
                        }
                        //门票入口
                        liveNotFinish(objLive.end_time, ticket_price, 'live');
                        //发布者跳主办方
                        $(document).on('click', '#publisher', function () {
                            var pub_id = $(this).attr("value");
                            var is_official = $(this).data('is_official');
                            gotoActivityList(pub_id, is_official);
                        });
                        //地图
                        //$(document).on("click", ".address_list", function () {
                        //    var ovo_address = $(this).find('.addressflag').text();
                        //    var ovo_name = $(this).find('.nameflag').text();
                        //    var ovo_description = $(this).find('.ovodesp').text();
                        //    var desp = removeHTMLTag(ovo_description);
                        //    desp = cutString(desp, 40);
                        //    locationAddress(ovo_address, ovo_name, desp);
                        //});
                        //是否订阅这个视频
                        $(".order").data("subscribe", objLive.subscribe);
                        if (objLive.subscribe == 1) {
                            $("button.order").html("已订阅")
                        } else {
                            $("button.order").html("订阅")
                        }

                    }
                    //to live description
                    $(document).on('click', '#videosMoreDetail', function () {
                        var id = $(this).attr("value");
                        window.location.href = labUser.path + "webapp/activity/detaildescription?id=" + id + "&pagetag=08-8&type=live";
                    });
                    $(".containerBox").show();
                    increaseViewn(id, 'live');
                }
            }
        });
},
/**video**/
vodDetail: function (parame, shareFlag) {
    var param = parame;
    if (shareFlag) {
        param.uid = 0;
    }
    var id = param.id,
    uid = param.uid,
    params = {};
    params["id"] = id;
    params["uid"] = uid;
    var url = labUser.api_path + '/video/detail';
    ajaxRequest(params, url, function (data) {
        if (data.status) {
            var selfObj = data.message.self,
            recObj = data.message.rec,
            video_url = selfObj.video_url;
                getActivityDetail(selfObj);   //获取活动数据
                getVodRecomment(recObj);     //点播推荐
                if (shareFlag) {
                    
                    if ('vip_id' in selfObj) {
                        $('#zbname').html(selfObj.vip_name);
                        $('#zbname').data('zbid', selfObj.vip_id);
                        $('#timetop').addClass('bordertop');
                        $('#zbrow').removeClass('none');
                        //查看专版
                        $(document).on('tap', '#zbrow', function () {
                            var vip_id = $('#zbname').data('zbid');
                            window.location.href = labUser.path + 'webapp/special/detail?is_share=1&pagetag=02-1&vip_id=' + selfObj.vip_id + '&uid=0';
                        });
                    }
                    //隐藏打赏和上图
                    $('.uploadpic,.awardpic').hide();
                    $('.comment_btn button').css('width', '100%');
                    //$("#video_box").css("top", "3.5rem");
                    //$(".share_video").css("top", "3.5rem");
                    $("#installapp").show();
                    if(selfObj.price>0){
                        $('#loadAppBtn').remove();
                        var buyBtnHtml = '';
                        buyBtnHtml += '<div class="buy fixed_btn buybtns" id="buyonline">';
                        buyBtnHtml += '<div class="infor l">';
                        buyBtnHtml += '<p class="red f12">本场点播需要购买才能观看</p>';
                        buyBtnHtml += '<p class="f16">点播票<span class="red money online_price"></span></p>';
                        buyBtnHtml += '</div>';
                        buyBtnHtml += '<button class="l buy_btn" id="loadapp" data-isshow="no">下载APP</button>';
                        buyBtnHtml += '</div>';
                        $('.containerBox').append(buyBtnHtml);
                        $(".online_price").html(' ¥ ' + selfObj.price);
                    }
                    else{
                        $('#loadAppBtn').removeClass('none');
                    }
                    $("#video_box").show();
                    getVod(video_url, 60, 0, selfObj.price);
                    //下载、打开事件
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
                                        title: selfObj.v_subject, // 分享标题
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
                                        title: selfObj.v_subject,
                                        desc: selfObj.v_description,
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
                                // regsiterWX(selfObj.v_subject,selfObj.detail_img,location.href,selfObj.v_description,'','');
                            }
                        });
                        $(".containerBox").removeClass('none');
                    }
                    else {
                        if (isiOS) {
                            //打开本地app
                            $(document).on('tap', '#openapp', function () {
                                oppenIos();
                            });
                            /**下载app**/
                            $(document).on('tap', '#loadapp', function () {
                                window.location.href = 'https://itunes.apple.com/app/id981501194';
                            });
                            oppenIos();
                        }
                        else if (isAndroid) {
                            $(document).on('tap', '#loadapp', function () {
                                window.location.href = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.tyrbl.wujiesq';
                            });
                            $(document).on('tap', '#openapp', function () {
                               openAndroid();
                           });
                            openAndroid();
                        }
                        $(".containerBox").removeClass('none');
                    }
                    /**活动介绍**/
                    $(".column>span[type=act_intro]").click(function () {
                        $(this).addClass('green').siblings().removeClass('green');
                        $('.buybtns').removeClass('none');
                        $("#act_intro").show();
                        $("#comment").hide();
                        $(".comment_btn,.comment_input").hide();
                    });
                    /**评论留言**/
                    $(".column>span[type=comment]").click(function () {
                        $(this).addClass('green').siblings().removeClass('green');
                        $('.buybtns').addClass('none');
                        $("#act_intro").hide();
                        $("#comment").show();
                        $(".comment_btn").show();
                    });
                    /**回复评论**/
                    $(document).on("click", ".reply", function () {
                        $('#commentback').removeClass('none');
                        $('#comtextarea').focus();
                        param.upid = $(this).parents("dl").attr("data-commentid");
                        param.p_nickname = $(this).parents().siblings("dd").children('.name').html();
                        param.pContent = $(this).parents().siblings("dd").children('.comment').html();
                        $("#comtextarea").attr("placeholder", '回复@' + param.p_nickname + ':');
                        $('#subcomments').data('replay', 'yes');
                    });
                    $('#subcomments').on('click', function () {
                        param.content = utf16toEntities($("#comtextarea").val());
                        if (param.content) {
                            Comment.addComment(param, $('#subcomments').data('replay'));
                        }
                        $("#comtextarea").val("");
                        $("#commentback").addClass('none');
                        $('.coperate').hide();//隐藏回复小气泡
                    });
                }
                else {
                    //是否收藏
                    $(".isFavorite").attr("value", selfObj.is_favorite);
                    //发图
                    $('.uploadpic').on('click', function () {
                        uploadpic(param.id, 'Video',false);
                    });
                    //打赏
                    $('.awardpic').on('click', function () {
                        if (uid == '0') {
                            showLogin();
                        }
                        else {
                            reward(param.id, 'video');
                        }
                    });

                    if (selfObj.is_favorite == 1) {
                        setFavourite("1");
                    } else {
                        setFavourite("0");
                    }
                    //app内--专版
                    if ('vip_id' in selfObj) {
                        $('#zbname').html(selfObj.vip_name);
                        $('#zbname').data('zbid', selfObj.vip_id);
                        $('#timetop').addClass('bordertop');
                        $('#zbrow').removeClass('none');
                        //查看专版
                        $(document).on('tap', '#zbrow', function () {
                            var vip_id = $('#zbname').data('zbid');
                            window.location.href = labUser.path + 'webapp/special/detail?pagetag=02-1&vip_id=' + selfObj.vip_id + '&uid=' + uid;
                        });
                        //不是专版会员且没有购买
                        if (selfObj.is_authorize == 0 && selfObj.is_purchase == 0) {
                            var buyBtnHtml = '';
                            buyBtnHtml += '<div class="buy fixed_btn none" id="buyonline">';
                            buyBtnHtml += '<div class="infor l f12 buyMemBtn">';
                            buyBtnHtml += '<div class="red mt05">成为专版会员</div>';
                            buyBtnHtml += '<div>会员专享，专版视频无限畅看</div>';
                            buyBtnHtml += '</div>';
                            buyBtnHtml += '<button class="l buy_btn" id="buyBtn" data-isshow="no"></button>';
                            buyBtnHtml += '</div>';
                            $('.containerBox').append(buyBtnHtml);
                            $('#buyBtn').html('¥' + selfObj.price + ' ' + '立即购买');//票价
                            $('#buyBtn').data('ticket_id', selfObj.ticket_id);//门票id
                            $('#buyBtn').data('isshow', 'yes');
                            $('#buyonline').show();//显示购买栏

                            $("#video_box").show();
                            getVod(video_url, 60, uid, selfObj.price);
                            $('.videotoptip').removeClass('none');
                            var st = setTimeout(function () {
                                $('.videotoptip').addClass('none');
                                clearTimeout(st);
                            }, 5000);
                            //注册购买
                            $(document).on('click', '.buy_btn', function () {
                                var videoid = id;
                                var ticket_id = $(this).data('ticket_id');
                                var type = 'video';
                                var ovoid = $('.address_list').eq(0).find('.infor').data('address_id');
                                buyTicket(videoid, ticket_id, type, ovoid);
                            });
                            //注册购买专版会员
                            $(document).on('click', '.buyMemBtn', function () {
                                var vip_id = $('#zbname').data('zbid');
                                buyVip(vip_id);
                            });
                        }
                        else {
                            $("#video_box").show();
                            getVod(video_url, 0, uid, selfObj.price);
                            if (selfObj.is_authorize == 0) {
                                $('.videotoptip').text('已购买单次会员视频服务，请享受无界商圈为你带来的视频服务').removeClass('none');
                                var st = setTimeout(function () {
                                    $('.videotoptip').addClass('none');
                                    clearTimeout(st);
                                }, 5000);
                            }
                        }
                    }
                    else {
                        //非专版,未购买且价格大于0
                        if (selfObj.is_purchase == 0 && selfObj.price > 0) {
                            var buyHtml = '';
                            buyHtml += '<div class="buy fixed_btn none" id="buyonline">';
                            buyHtml += '<div class="infor l">';
                            buyHtml += '<p class="red f12">本场点播需要购买才能观看</p>';
                            buyHtml += '<p class="f16">点播票<span class="red money online_price"></span></p>';
                            buyHtml += '</div>';
                            buyHtml += '<button class="l buy_btn" id="buyBtn" data-isshow="no">购买</button>';
                            buyHtml += '</div>';
                            $('.containerBox').append(buyHtml);
                            //点播票价
                            $(".online_price").html(' ¥ ' + selfObj.price);
                            //门票id
                            $('#buyBtn').data('ticket_id', selfObj.ticket_id);
                            //购买按钮是否显示
                            $('#buyBtn').data('isshow', 'yes');
                            $("#buyonline").show();//显示购买栏

                            $("#video_box").show();
                            getVod(video_url, 60, uid, selfObj.price);
                            //注册购买
                            $(document).on('click', '.buy_btn', function () {
                                var videoid = id;
                                var ticket_id = $(this).data('ticket_id');
                                var type = 'video';
                                var ovoid = $('.address_list').eq(0).find('.infor').data('address_id');
                                buyTicket(videoid, ticket_id, type, ovoid);
                            });

                        }
                        else {
                            //已购票或视频免费
                            //未登录
                            if (uid == 0) {
                                $("#video_box").show();
                                getVod(video_url, 60, 0, selfObj.price);
                            }
                            else {
                                $("#video_box").show();
                                getVod(video_url, 0, uid, selfObj.price);
                            }
                        }

                    }
                    /**回复评论**/
                    $(document).on("click", ".reply", function () {
                        $('#commentback').removeClass('none');
                        $('#comtextarea').focus();
                        param.upid = $(this).parents("dl").attr("data-commentid");
                        param.p_nickname = $(this).parents().siblings("dd").children('.name').html();
                        param.pContent = $(this).parents().siblings("dd").children('.comment').html();
                        $("#comtextarea").attr("placeholder", '回复@' + param.p_nickname + ':');
                        $('#subcomments').data('replay', 'yes');
                    });
                    $('#subcomments').on('click', function () {
                        param.content = utf16toEntities($("#comtextarea").val());
                        if (param.content) {
                            Comment.addComment(param, $('#subcomments').data('replay'));
                        }
                        $("#comtextarea").val("");
                        $("#comtextarea").attr("placeholder", '优质评论将会被优先展示');
                        $("#commentback").addClass('none');
                        $('.coperate').hide();//隐藏回复小气泡
                    });

                    /**删除评论**/
                    $(document).on("click", ".delete", function () {
                        param.commentid = $(this).parents("dl").attr("data-commentid"); //自己评论的id
                        Comment.deleteComment(param);
                        $(this).parents("dl").remove();
                    });

                    //地图
                    $(document).on("click", ".address_list", function () {
                        var ovo_address = $(this).find('.addressflag').text();
                        var ovo_name = $(this).find('.nameflag').text();
                        var ovo_description = $(this).find('.ovodesp').text();
                        var desp = removeHTMLTag(ovo_description);
                        desp = cutString(desp, 40);
                        locationAddress(ovo_address, ovo_name, desp);
                    });

                    //更多视频列表
                    $(document).on("click", "#seenmoreVideo", function () {
                        var id = $(this).attr("value");
                        seenmoreVideo(id);
                    });
                    /**活动介绍**/
                    $(".column>span[type=act_intro]").click(function () {
                        $(this).addClass('green').siblings().removeClass('green');
                        var shows = $('#buyBtn').data('isshow');
                        if (shows == 'yes') {
                            $("#buyonline").show();
                        }
                        $("#act_intro").show();
                        $("#comment").hide();
                        $(".comment_btn,.comment_input").hide();
                    });
                    /**评论留言**/
                    $(".column>span[type=comment]").click(function () {
                        $(this).addClass('green').siblings().removeClass('green');
                        var shows = $('#buyBtn').data('isshow');
                        if (shows == 'yes') {
                            $("#buyonline").hide();
                        }
                        $("#act_intro").hide();
                        $("#comment").show();
                        $(".comment_btn").show();
                    });
                    //隐藏回复框
                    $('.contentDd .name,.contentDd .comment').on('click', function () {
                        $('#send_comment').data('replay', 'no');
                        $(".comment_input>input").val("");
                        $(".comment_input>input").attr("placeholder", '');
                        $(".comment_input").hide();
                        $('.coperate').hide();//隐藏回复框
                    });
                    $(".containerBox").removeClass('none');
                }
                increaseViewn(id, 'video');
                $(document).on('click', '#videosMoreDetail', function () {
                    var id = $(this).attr("value");
                    window.location.href = labUser.path + "webapp/activity/detaildescription?id=" + id + "&pagetag=08-8&type=video";
                });
            }
        });
},
/**subscribe**/
order: function (live_id, subscribe) {
    var param = {};
    param["uid"] = labUser.uid;
    param["live_id"] = live_id;
    param["type"] = subscribe;
    var url = labUser.api_path + "/live/subscribe";
    ajaxRequest(param, url, function (data) {
        if (data.status) {
            if (param["type"] == "1") {
                $(".order").html("已订阅");
            } else {
                $(".order").html("订阅");
            }
        }
    });
}
};
//打开本地--Android
function openAndroid(){
    var strPath = window.location.pathname;
    var strParam = window.location.search.replace(/is_share=1/g, '');
    var appurl = strPath + strParam;
    window.location.href = 'openwjsq://welcome' + appurl;
}
function oppenIos(){
    var strPath = window.location.pathname.substring(1);
    var strParam = window.location.search;
    var appurl = strPath + strParam;
    var share = '&is_share';
    var appurl2 = appurl.substring(0, appurl.indexOf(share));
    window.location.href = 'openwjsq://' + appurl2;
}
//直播信息
function liveHtml(objLive, objActivity, shareFlag) {
    onlineuserpic(objLive);
    $('#share_img').data('src', objActivity.share_image);
    $('#acttitle').data('activity_id', objActivity.id);
    $('.act_pics img').attr('src', objActivity.detail_img);
    $('#act_name').html(objActivity.subject);
    var livebegin = unix_to_yeardatetime(objLive.begin_time);
    $('#begin_time').html('直播开始时间：' + livebegin);
    $('#act_positon').html('活动现场：' + objActivity.city.split('@').join('、'));
    //发布者
    var publisherHtml = '';
    publisherHtml += '<span class="img"><img src="' + objActivity.avatar + '"/></span> <i class="author_name">' + objActivity.nickname + '</i>发布<span class="sj_icon"></span>';
    $('#publisher').html(publisherHtml);
    if (objActivity.is_official == '1') {
        $('#publisher').attr('value', objActivity.pub_id);//官方
        $('#publisher').data('is_official', '1');//标志位
    }
    else if (objActivity.is_official == '0') {
        $('#publisher').attr('value', objActivity.c_uid);//城市合伙人
        $('#publisher').data('is_official', '0');
    }
    //视频描述
    var despStr = removeHTMLTag(objLive.description);
    $('#video_description').html(cutString(despStr, 80));
    $("#videosMoreDetail").attr("value", objLive.id);
    if (objLive.is_brand_live == '0') {
        //not brand_live
        $('#sencondsplit,#addin_block').remove();
        $('#detail_block,#comment_block').css('width', '48%');
        $('#pinpai').remove();
    }
    else {
        $('#businessflag').removeClass('none');
        //brands-list
        var brandHtml = '';
        $.each(objLive.brands, function (index, item) {
            var keywordhtml = '';
            brandHtml += '<div class="brandcontent psrelative" data-brand_id="' + item.id + '">';
            brandHtml += '<img src="' + item.logo + '" alt="">';
            brandHtml += '<div class="branddetail f12">';
            brandHtml += '<p class="f14"><span>' + cutString(item.name, 10) + '</span><span class="color666">【' + item.zone_name + '】</span></p>';
            brandHtml += '<p>';
            brandHtml += '<em class="brand-sort">' + item.category_name + '</em> <span class="brand-st pl05">' + item.investment_min + ' 万元 - ' + item.investment_max + ' 万元</span>';
            brandHtml += '</p>';
            brandHtml += '<p class="brand-keyword">';
            if (item.keywords.length > 0) {
                $.each(item.keywords, function (index, oneitem) {
                    keywordhtml += '<span>' + oneitem + '</span>';
                });
                brandHtml += keywordhtml;
            }
            brandHtml += '</p>';
            brandHtml += '</div>';
            brandHtml += '<div class="clearfix"></div>';
            brandHtml += '<span class="sj_icon top4-5"></span>';
            brandHtml += '</div>';
        });
        $('#pinpai').append(brandHtml);
        //goods-list
        if(objLive.goods && objLive.goods.length > 0){
            var goodsHtml = '';
            $.each(objLive.goods, function (index, item) {
                var keyword = '';
                goodsHtml += '<section class="brandcontain">';
                goodsHtml += '<div class="brandtext f14"><span class="brand_text">' + item.goods_title + '</span><span class="fr f12 lht2 color666">商品代号：' + item.code + '</span></div>';
                goodsHtml += '<div class="brandcontent">';
                goodsHtml += '<img src="' + item.logo + '" alt="">';
                goodsHtml += '<div class="branddetail f12">';
                goodsHtml += '<p class="f14"><span>' + cutString(item.name, 10) + '</span><span class="color666">【' + item.zone_name + '】</span></p>';
                goodsHtml += '<p><em class="brand-sort">' + item.category_name + '</em><span class="brand-st pl05">' + item.investment_min + ' 万元 - ' + item.investment_max + ' 万元</span></p>';
                goodsHtml += '<p class="brand-keyword">';
                if (item.keywords.length > 0) {
                    $.each(item.keywords, function (index, oneitem) {
                        keyword += '<span>' + oneitem + '</span>';
                    });
                    goodsHtml += keyword;
                }
                goodsHtml += '</p>';
                goodsHtml += '</div>';
                goodsHtml += '<div class="clearfix"></div>';
                goodsHtml += '</div>';
                goodsHtml += '<div class="brandtext f12 bordertop">';
                if (item.status == 'allow') {
                    if (item.num == '0') {
                        goodsHtml += '<div class="infocon"><span class="color999">如有意向，可以直接下单提交定金</span><span class="fr buybutton tc cannotbuy">已售完</span><span class="pricecolor f14 fr mr2 b">￥' + item.price + '</span></div>';
                        goodsHtml += '<div class="color999"><span>加盟拓展，先行先得！</span><span class="fr mt05">很抱歉，已全部购完</span></div>';
                    }
                    else if (item.num >= 1) {
                        goodsHtml += '<div class="infocon"><span class="color999">如有意向，可以直接下单提交定金</span><span class="fr buybutton tc canbuy" data-goodsid="' + item.id + '">立即购买</span><span class="pricecolor f14 fr mr2 b">￥' + item.price + '</span></div>';
                        goodsHtml += '<div class="color999"><span>加盟拓展，先行先得！</span><span class="fr mt05">数量有限，还剩' + item.num + '份</span></div>';
                    }
                }
                else if (item.status == 'pause') {
                    goodsHtml += '<div class="infocon"><span class="color999">如有意向，可以直接下单提交定金</span><span class="fr buybutton tc cannotbuy">暂未开启</span><span class="pricecolor f14 fr mr2 b">￥' + item.price + '</span></div>';
                    goodsHtml += '<div class="color999"><span>加盟拓展，先行先得！</span><span class="fr mt05">数量有限，还剩' + item.num + '份</span></div>';
                }
                goodsHtml += '<div class="clearfix"></div>';
                goodsHtml += '</div>';
                goodsHtml += '</section>';
            });
            $('#barnd_list').html(goodsHtml);
        }
    }
    $('#act_intro').removeClass('none');
}
//在线头像
function onlineuserpic(objLive) {
    var picHtml = '';
    $.each(objLive.online_users, function (index, item) {
        picHtml += '<img src="' + item + '" alt="">';
    });
    picHtml += '<span class="awardpicture fr"></span>';
    picHtml += '<span class="splitspan fr tc">|</span>';
    picHtml += '<span class="f12 fr">当前人数：' + objLive.online_count + '</span>';
    $('#livecount').html(picHtml);
}
//点播关联活动信息
function getActivityDetail(result) {
    //unix_to_yeardatetime(objLive.begin_time);
    $('#share_img').data('src', result.share_image);
    $(".act_pics img").attr('src', result.detail_img);
    $("#act_name").html(result.subject);//关联的活动的标题
    /**时间**/
    var begin_time = unix_to_datetime(result.begin_time);//开始时间
    var end_time = unix_to_datetime(result.end_time);  //结束时间
    var begin_time_day = begin_time.substring(0, 5);
    var end_time_day = end_time.substring(0, 5);
    if (begin_time_day == end_time_day) {
        end_time = end_time.slice(5);
    }
    $("#act_time").html(begin_time + ' - ' + end_time);
    //ovo主办、协办地址
    var ovodesps = removeHTMLTag(result.descriptions).split('@');
    var cityArray = (result.city).split('@');
    var idsArray = (result.maker_ids == '') ? [] : (result.maker_ids).split('@');
    var addressArray = (result.address).split('@');
    var nameArray = (result.name).split('@');
    var typeArray = (result.type).split('@');
    var htmlOVOaddress = '';
    if (idsArray.length > 0) {
        $.each(nameArray, function (index, item) {
            htmlOVOaddress += '<dd class="address_list">';
            htmlOVOaddress += '<span class="address_icon"></span>';
            if (index == (idsArray.length - 1)) {
                htmlOVOaddress += ' <div class="infor nobottomborder" data-address_id="' + idsArray[index] + '">';
            }
            else {
                htmlOVOaddress += ' <div class="infor" data-address_id="' + idsArray[index] + '">';
            }
            htmlOVOaddress += '<p class="nameflag">' + item + '</p>';
            htmlOVOaddress += '<p class="addressflag">' + addressArray[index] + '</p><span class="sj_icon"></span>';
            htmlOVOaddress += '<div class="none ovodesp">' + ovodesps[index] + '</div>';
            htmlOVOaddress += '</div>';
            htmlOVOaddress += '</dd>';
        });
    }
    $('#address_flag').empty();
    $("#address_flag").html(htmlOVOaddress);
    //点播标题,描述
    $('#video_title_none').html(result.v_subject);
    $('#video_descript_none').html(result.v_description);
    /**点播OVO中心地址栏收放**/
    if (cityArray.length == 1) {
        $('.act_address').css('height', '11rem');
    }
    else if (cityArray.length > 2) {
        $(".act_intro>.seen_more.down").show();
    }
    //浏览量
    $('#seenNum').html(result.view);
    //发布者
    var publisherHtml = '';
    publisherHtml += '<span class="img"><img src="' + result.avatar + '"/></span> <i class="author_name">' + result.nickname + '</i>发布<span class="sj_icon"></span>';
    $('#publisher').html(publisherHtml);
    if (result.is_official == '1') {
        $('#publisher').attr('value', result.pub_id);//官方
        $('#publisher').data('is_official', '1');//标志位
    }
    else if (result.is_official == '0') {
        $('#publisher').attr('value', result.c_uid);//城市合伙人
        $('#publisher').data('is_official', '0');
    }
    //活动详情描述
    var despStr = removeHTMLTag(result.description);
    $('#video_description').html(cutString(despStr, 80));
    $("#videosMoreDetail").attr("value", result.id);
    //活动id
    $("#act_name").attr("data-act_id", result.id);
}
//直播推荐
function getLiveRecomment(result) {
    if ($.isArray(result) && result.length > 0) {
        var recLive = '';
        $.each(result, function (index, item) {
            recLive += '<li data-liveid="' + item.id + '" class="recommend-video"><img src="' + item.list_img + '" alt="livepic"><p>' + item.subject + '</p></li>';
        });
        $('#recommend_live').html(recLive);
        $('.about_video').removeClass('none');
    }
}
//点播推荐
function getVodRecomment(result) {
    //推荐点播视频
    var recVideo = '';
    $.each(result, function (index, item) {
        recVideo += '<li data-vedioid="' + item.id + '" class="recommend-video"><img src="' + item.image + '" alt="videopic"><p>' + item.subject + '</p></li>';
    });
    $('#recommend_video').empty();
    $('#recommend_video').append(recVideo);
    if ((window.location.href).indexOf('is_share') > 0) {
        $(".recommend-video").click(function () {
            var id = $(this).attr('data-vedioid');
            window.location.href = labUser.path + "webapp/vod/detail?id=" + id + "&pagetag=05-4&is_share=1";
        });
    }
    else {
        $(".recommend-video").click(function () {
            var id = $(this).attr('data-vedioid');
            window.location.href = labUser.path + "webapp/vod/detail?id=" + id + "&pagetag=05-4&uid=" + labUser.uid;
        });
    }

}
/**实例化点播**/
function getVod(video_url, stop_time, userid, price) {
    player = new qcVideo.Player(
        //页面放置播放位置的元素 ID
        "video_box",
        {
            "file_id": video_url, //视频 ID (必选参数)
            "app_id": "1251768344", //应用 ID (必选参数)，同一个账户下的视频，该参数是相同的
            "auto_play": "0", //是否自动播放 默认值0 (0: 不自动，1: 自动播放)
            "width": 414, //播放器宽度，单位像素
            "height": 232, //播放器高度，单位像素
            "stop_time": stop_time,
            "disable_full_screen": 0
        },
        {
            //播放状态
            // status 可为 {ready: "播放器已准备就绪",seeking:"搜索",
            // suspended:"暂停", playing:"播放中" , playEnd:"播放结束" , stop:"试看结束触发"
            'playStatus': function (status) {
                if (status == 'playing') { //播放中
                    console.log('playing');
                }
                if (status == "playEnd") { //播放结束
                    console.log('end');
                }
                if (status == "stop") { //试看结束
                    showMessage(userid, price, 'video');
                }
            },
        });
}
/*实例化直播*/
function getLive(live_url, end_time, userid, price, shareflag, liveid) {
    player = new qcVideo.Player(
        //页面放置播放位置的元素 ID
        "video_box", {
            "width": 640,
            "height": 480,
            "live_url": live_url,
            //"live_url2": live_url,
            "h5_start_patch": {
                "url": '../../images/live.png',
                "stretch": true
            }
        },
        {//播放状态
            'playStatus': function (status, type) {
                // status 可为 {ready: "播放器已准备就绪",seeking:"搜索",
                // suspended:"暂停", playing:"播放中" , playEnd:"播放结束" , stop: "试看结束触发"
                // }’
                if (status == 'playing') {
                }
                if (status == "playEnd") {
                }
                if (status == "stop") {
                    showMessage(userid, price, 'live');
                }
                if (status == "error") {
                    var params = {};
                    params["id"] = liveid;
                    params["uid"] = userid;
                    var url = labUser.api_path + '/live/detail';
                    ajaxRequest(params, url, function (data) {
                        if (data.status) {
                            var objLive = data.message.live;
                            if (objLive.end_time < (new Date().getTime() / 1000)) {
                                $("#video_box").empty();
                                $('.share_video .share_text').html('本场直播已经结束');
                                $(".share_video").show();
                            }
                        }
                    });

                }
            },
        }
        );
    //player.play();//PC端Flash播放器支持
}
//点播、直播预览自增
function increaseViewn(id, type) {
    var param = {};
    param["id"] = id;
    param["num"] = 1;
    param['type'] = type;
    var url = labUser.api_path + '/live/incre';
    ajaxRequest(param, url, function (data) {
        if (data.status) {

        }
    });
}
//试看结束
function showMessage(userid, price, type) {
    if (userid == 0) {
        if (price == 0) {
            $("#video_box").hide();
            $('.share_video .share_text').html('<button class="order none" id="loginbtn">登录</button></br>试看已结束,请登录后观看本视频');
            $(".share_video").show();
        }
        else {
            $("#video_box").hide();
            $('.share_video .share_text').html('<button class="order none" id="loginbtn">登录</button></br>试看已结束,请登录后购买本视频');
            $(".share_video").show();
        }
        if (type == 'video') {
            var inapp = (window.location.href).indexOf('is_share') < 0 ? true : false;
            if (inapp) {
                $('#loginbtn').removeClass('none');
                $('#loginbtn').on('click', function () {
                    showLogin();
                });
            }
        }
    }
    else {
        $("#video_box").hide();
        $('.share_video .share_text').html('试看已结束<br>请点击屏幕底部"购买"按钮,购买本视频');
        $(".share_video").show();
    }
}
function timeOutShow(begintime, endtime, fn) {
    /**直播未开始(倒计时)**/
    var time = Math.round(new Date().getTime() / 1000);
    var djs_time = begintime - time;
    if (djs_time <= 0) {
        $("#daojishi").hide();
        $("#video_box").show();
        fn();
    } else {
        //倒计时代码
        $("#video_box").hide();
        $("#daojishi").show();
        var endDate = new Date(parseInt(begintime) + 60);
        var interval = setInterval(function () {
            var now = Math.round(new Date().getTime() / 1000);
            var oft = Math.round((endDate - now));
            var ofd = parseInt(oft / 3600 / 24);
            var ofh = parseInt((oft % (3600 * 24)) / 3600);
            var ofm = parseInt((oft % 3600) / 60);
            $("#daojishi .day").html(ofd + ' 天 ' + ofh + ' 小时 ' + ofm + ' 分钟 ');
            if (ofd == 0 && ofh == 0 && ofm == 0) {
                $("#daojishi").hide();
                $("#video_box").show();
                fn();
                clearInterval(interval);
            }
        }, 1000);
    }
}
/*
 * 开始时间、结束时间
 * */
//直播未结束
function liveNotFinish(endtime, price, type) {
    if (price > 0) {
        var timestamp = Math.round(new Date().getTime() / 1000);
        if (timestamp < endtime) {
            //直播未结束,门票栏入口
            $(document).on('click', '.wjb', function () {
                var ovoid = $('.address_list').eq(0).find('.infor').data('address_id');
                var act_id = $('#act_name').data('act_id');
                var act_name = $('#act_name').text();
                ActivityApply(act_id, ovoid, act_name, type);
            });
        }
    }
}
//暂停播放
function pauseVideo() {
    if (typeof(player.pause) === 'function') {
        player.pause();
    }
}
//直播函数
function playlive(live_url, end_time, userid, price, shareflag, liveid) {
    if (shareflag) {
        //分享页面
        if (price > 0) {
            $("#video_box").hide();
            $('.share_video .share_text').html('本视频为收费视频，请下载"无界商圈APP"并购买门票，谢谢');
            $(".share_video").show();
            //getLive(live_url, end_time, userid, price);
            //var timeout = setTimeout(function () {
            //    $("#video_box").empty();
            //    $('.share_video .share_text').html('本视频为收费视频，请下载"无界商圈APP"并购买门票，谢谢');
            //    $(".share_video").show();
            //    clearTimeout(timeout);
            //}, 60000);
        }
        else {
            if (window.localStorage) {
                var resflag = localStorage.getItem('isregister');
                if (resflag) {
                    $('.share_video .share_text').html('');
                    $(".share_video").hide();
                    getLive(live_url, end_time, 0, price, true, liveid);
                }
                else {
                    //快速注册
                    $('#registerpart').removeClass('none');
                }
            }
            else {
                //快速注册
                $('#registerpart').removeClass('none');
            }
            //getLive(live_url, end_time, userid, price);
            //5分钟提醒
            //var sett = setTimeout(function () {
            //    $("#video_box").empty();
            //    $(document).find('video').empty().attr('src','');
            //    $('.share_video .share_text').html('登录无界商圈APP，更多高清视频等你来观看');
            //    $(".share_video").show();
            //    $('#liveremind').addClass('none');
            //    $('#remindsuccess').addClass('none');
            //    $('#registerpart').removeClass('none');
            //    clearTimeout(sett);
            //}, 60000);
        }
    }
    else {//app内300000


    }
}


