var pageNow = 1,
    pageSize = 8;
//分享
function showShare() {
    var type = 'Activity',
     title = $('#act_name').text(),
     url = window.location.href,
     img = $('#share_img').data('src'),
     header = '活动',
     content = '我在无界商圈发现了一个不错的活动，想邀请你一起参加！',
     begintime = $('#act_des').data('begintime'),
     citys = $('#citys').text(),
     actid=$('#act_name').data('act_id');
    shareOut(title, url, img, header, content, begintime, citys,actid,type);
}
//刷新
function reload() {
    location.reload();
}

//收藏/取消收藏
function collectActivity() {
    var args = getQueryStringArgs(),
        activity_id = args['id'],
        uid = args['uid'] || '0';
    var param = {};
    param['uid'] = uid;
    param['post_id'] = activity_id;
    param['model'] = 'activity';
    if ($('#act_des').data('collected') == '0') {
        param['type'] = '1';
    }
    else {
        param['type'] = '0';
    }
    var url = labUser.api_path + '/favorite/deal';
    ajaxRequest(param, url, function (data) {
        if (data.status) {
            if (param['type'] == '1') {
                $('#act_des').data('collected', '1');
                setFavourite('1');
            }
            else {
                $('#act_des').data('collected', '0');
                setFavourite('0');
            }
        }
    });
}
//刷新评论
function Refresh() {
    var args = getQueryStringArgs(),
        activity_id = args['id'],
        uid = args['uid'] || '0';
    var parameter = {
        "type": 'Activity',
        "id": activity_id,
        "uid": uid,
        "fromId": $('#commentflag').data('maxid'),
        "update": "new",
        "fecthSize": 0
    };
    Comment.getFreshList(parameter);
}
;Zepto(function () {
    new FastClick(document.body);
    var urlPath = window.location.href,
        args = getQueryStringArgs(),
        activity_id = args['id'],
        uid = args['uid'] || '0',
        position_id = args['position_id'] || '0',
        maker_id = args['makerid'] || '0',
        commentParam = {
            "id": activity_id,
            "uid": uid,
            "section": 0,
            "commentType": 'Activity',
            "type": 'Activity',
            "commentid": '',
            "content": '',
            "nickname": labUser.nickname,
            "avatar": labUser.avatar,
            "page": pageNow,
            "page_size": pageSize,
            "update": "new",
            "fecthSize": 0,
            "use": 'normal'
        };
    viewAdd(uid, 'activity', activity_id);
    var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
    var activityDetail = {
        detail: function (activity_id, uid) {
            var param = {};
            param["id"] = activity_id;
            param["uid"] = uid;
            var url = labUser.api_path + '/activity/detail/_v020400';
            ajaxRequest(param, url, function (data) {
                if (data.status) {
                    activityData(data.message, shareFlag);

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
                    }
                    else {
                        $('.collectbtn').text('收藏');
                        $('#storeNum').html($('#storeNum').text() - 1);
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
    //活动信息
    function activityData(result, is_share) {
        var selfObj = result;
        commonHTML(selfObj, is_share);
        //分享页
        if (is_share) {
            $('#loadAppBtn').removeClass('none');
            $('#installapp').removeClass('none');
            //结束未报名
            var timestamp = Math.round(new Date().getTime() / 1000);
            if (timestamp < selfObj.end_time) {
                $('#signnow').on('click', function () {
                    if ($('#wjbNum').html()=='免费') {
                        window.location.href = labUser.path+'webapp/freecheck/detail/_v020400?id='+activity_id+'&ticket_id='+result.ticket_id+'&is_share=1';
                    }else{
                        window.location.href = labUser.path + 'webapp/ticket/actapply/_v020400?id=' + activity_id + '&is_share=1';
                    }
                });
            }
            else{
                $('#signnow').html('已结束');
            }
            $(document).on('click', '.psrelative', function () {
                var brand_id = $(this).data('brand_id');
                window.location.href = labUser.path + "webapp/brand/detail/_v020400?id=" + brand_id + "&uid=" + uid + "&pagetag=02-1-2&is_share=1";
            });
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
                var nowhitespace = desptStr.replace(/&nbsp;/g, '');
                var despt = cutString(desptStr, 60);
                var nowhitespaceStr = cutString(nowhitespace, 60);
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
                        var strParam = window.location.search.replace(/is_share=1/g, '');
                        var appurl = strPath + strParam;
                        window.location.href = 'openwjsq://welcome' + appurl;
                    });
                }
            }
        }
        else {
            //判断是否已经收藏当前活动
            if (selfObj.is_collect == '1') {
                setFavourite('1');
                //是否收藏
                $('#act_des').data('collected', '1');
            }
            else {
                setFavourite('0');
                $('#act_des').data('collected', '0');
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
                //底部立即报名+门票栏入口
                $(document).on('click', '.signup,.wjbrk', function () {
                    var ovoid = maker_id;
                    var act_id = $('#act_name').data('act_id');
                    var act_name = $('#act_name').text();
                    ActivityApply(act_id, ovoid, act_name, 'Activity');
                });
            }
            $(document).on('click', '.psrelative', function () {
                var brand_id = $(this).data('brand_id');
                window.location.href = labUser.path + "webapp/brand/detail?id=" + brand_id + "&uid=" + uid + "&pagetag=02-1-2";
            });
            //点赞头像
            $('#morezanimg').on('click', function () {
                if ($(this).data('showdown')) {
                    $('#zan-images').css('max-height', 'none');
                    $(this).html('收起 ∧').data('showdown', '0');
                }
                else {
                    $('#zan-images').css('max-height', '13rem');
                    $(this).html('更多 ∨').data('showdown', '1');
                }
            });
            //更多评论
            $('#morecm').on('click', function () {
                var page = $('#pullUp').data('pagenow');
                page++;
                var param = {
                    "id": activity_id,
                    "uid": uid,
                    "commentType": 'Activity',
                    "page": page,
                    "page_size": pageSize
                };
                Comment.getCommentList(param, null, 'Activity');
            });
            //赞-活动
            $('.actzan').on('click', function () {
                var param = {};
                param["activity_id"] = activity_id;
                param["uid"] = uid;
                var url = labUser.api_path + '/activity/zan/_v020400';
                ajaxRequest(param, url, function (data) {
                    if (data.status) {
                        getHotNum();
                    }
                    else{
                        alert(data.message);
                    }
                });
            });
            //转发
            $('.zhuan').on('click', function () {
                showShare();
            });
            //时间
            $('#aty_time').on('click', function () {
                location.href = labUser.path + 'webapp/activity/time/_v020400?id=' + activity_id;
            });
            //地址
            $('#aty_hostcitys').on('click', function () {
                location.href = labUser.path + 'webapp/activity/address/_v020400?id=' + activity_id + '&maker_id=' + maker_id;
            });
            //报名人数
            $('#aty_signs').on('click', function () {
                location.href = labUser.path + 'webapp/activity/enrollment/_v020400?id=' + activity_id;
            });
            //评论
            $('.chat').on('click', function () {
                uploadpic(activity_id, 'Activity', true);
            });
        }
        $('#act_container').removeClass('none');
        swipers();
    }

    //共同点
    function commonHTML(selfObj, is_share) {
        bannerPic(selfObj.detail_img);
        //是专版活动
        if (selfObj.is_vip=='1') {
            $('#act_name').html(cutString(selfObj.subject, 22));
            $('#zbname').html(selfObj.vip_name);
            $('#zbname').data('zbid', selfObj.vip_id);
            $('#zbicon,#zbcontainer').removeClass('none');
            if (is_share) {
                $(document).on('tap', '#zbcontainer', function () {
                    var vip_id = $('#zbname').data('zbid');
                    window.location.href = labUser.path + 'webapp/special/detail?vip_id=' + selfObj.vip_id + '&uid=' + uid + '&is_share=1';
                });
            }
            else {
                $(document).on('tap', '#zbcontainer', function () {
                    var vip_id = $('#zbname').data('zbid');
                    window.location.href = labUser.path + 'webapp/special/detail?pagetag=02-1&vip_id=' + selfObj.vip_id + '&uid=' + uid;
                });
            }
        }
        else {
            $('#act_name').html(selfObj.subject);
            $('#zbicon,#zbcontainer').remove();
        }
        $('#share_img').data('src', selfObj.share_image);
        $('#act_name').data('act_id', selfObj.id);//活动id
        //数据库浏览量加1
        activityDetail.seenPlus(selfObj.id, '1', 'view');
        var begin_time = unix_to_datetime(selfObj.begin_time);//开始时间
        var bt = unix_to_fulltime(selfObj.begin_time);
        $('#act_des').data('begintime', bt);
        $('#act_time').html(begin_time);
        $('#citys').html(selfObj.activity_location);
        $('#wjbNum').html((selfObj.min_ticket_price == '0' ? '0元起' : selfObj.min_ticket_price));
        $('#tickettype').html(selfObj.min_ticket_price_type);
        $('#bmNum').html('共' + selfObj.sign_count + '人已报名');
        //活动详情描述
        $('#actdescription').html(selfObj.description.replace(/http:/g,'https:'));
        //品牌
        if (selfObj.brand.length > 0) {
            var brandHtml = '';
            $.each(selfObj.brand, function (index, item) {
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
        }
        else {
            $('#pinpai').remove();
        }
        //热度
        $('#hotnum').html('热度 ' + selfObj.hot_count + '<i></i>');
        $('#seen').html(selfObj.view_count + '次');
        $('#dianzan').html(selfObj.zan_count + '次');
        $('#plun').html(selfObj.comment_count + '次');
        $('#zhuan').html(selfObj.share_count + '次');

        //分享页没有赞头像、评论
        if (is_share) {
            $('#zancontain,#comment').remove();

        }
        else {
            $('#zan-number').html(selfObj.zan_count);
            $('#commentnum').html(selfObj.comment_count);
            zanImages(selfObj.zans);
            Comment.getCommentList(commentParam, 'reload', 'activity');
        }
        fixedBtn(selfObj.can_buy, is_share, selfObj.end_time);
    }

    //轮播图
    function bannerPic(picarray) {
        var str = '';
        $.each(picarray, function (index, item) {
            str += '<div class="swiper-slide"><img src="' + item + '" alt="" /></div>';
        });
        $('.swiper-wrapper').append(str);
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

    //swiper幻灯片
    function swipers() {
        var swiper = new Swiper('.swiper-container', {
            pagination: '.swiper-pagination',
            paginationType: 'custom',
            // autoplay:'2000',
            paginationCustomRender: function (swiper, current, total) {
                return '<span class="f16">' + current + '</span>' + ' / ' + total;
            }
        });
    }

    //底部按钮
    function fixedBtn(can_buy, is_share, end_time) {
        if (is_share) {
            $('.act_address .sj_icon').addClass('none');
        }
        else {
            var timestamp = Math.round(new Date().getTime() / 1000);
            if (timestamp < end_time) {
                if (can_buy) {
                    //有购买按钮
                    var canBtnHtml = '<div class="fixed-btn" id="notzbBtn">' +
                        '<button class="actzan width20">赞</button>' +
                        '<i class="left20">|</i>' +
                        '<button class="chat width20">评论</button>' +
                        '<i class="left40">|</i>' +
                        '<button class="zhuan width20">转发</button>' +
                        '<i class="left60">|</i>' +
                        '<button class="buyvip width20 colorf63">购买会员</button>' +
                        '<i class="left80">|</i>' +
                        '<button class="signup width20 gre">立即报名</button>' +
                        '</div>';
                    $('#act_container').append(canBtnHtml);
                    //购买专版会员
                    $(document).on('tap', '.buyvip', function () {
                        var vip_id = $('#zbname').data('zbid');
                        buyVip(vip_id);
                    });
                }
                else {
                    //没有购买按钮
                    var btnHtml = '<div class="fixed-btn" id="notzbBtn">' +
                        '<button class="actzan width25">赞</button>' +
                        '<i class="left25">|</i>' +
                        '<button class="chat width25">评论</button>' +
                        '<i class="left50">|</i>' +
                        '<button class="zhuan width25">转发</button>' +
                        '<button class="signup width25 gre">立即报名</button>' +
                        '</div>';
                    $('#act_container').append(btnHtml);
                }
            }
            else {
                var endBtn = '<div class="fixed_btn share">\
                    <button class="">报名已结束</button>\
                    </div>';
                $('#act_container').append(endBtn);
            }
        }
    }

    //浏览量自增
    //function viewAdd(uid, relation, relation_id) {
    //    var param = {};
    //    param["relation_id"] = relation_id;
    //    param["uid"] = uid;
    //    param["relation"] = relation;
    //    var url = labUser.api_path + '/user/add-browse/_v020400';
    //    ajaxRequest(param, url, function (data) {
    //        if (data.status) {
    //        }
    //    });
    //}


});
