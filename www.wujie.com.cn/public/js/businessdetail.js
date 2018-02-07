/*
 * 商机详情页
 * */
var Business = $.extend({}, {
    detail: function (id, uid) {
        var param = {};
        param["id"] = id;
        param["uid"] = uid;
        var url = labUser.api_path + "/opportunity/detail";
        ajaxRequest(param, url, function (data) {
            if (data.status) {
                getGovermentInfor(data.message);
                $(".containerBox").show();
            }
        });
    },

    //相关活动
    aboutAct: function (id) {
        var param = {};
        param["id"] = id;
        var url = labUser.api_path + "/opportunity/activitys";
        ajaxRequest(param, url, function (data) {
            if (data.status) {
                var datas = data.message;
                act(datas);
                $(".containerBox").show();
            }
        })
    },

    //申请对接
    applyDocking: function (id) {
        var params = {};
        params["uid"] = labUser.uid;
        params["opportunity_id"] = id;
        params["nickname "] = labUser.nickname;
        var url = labUser.api_path + '/userapply/store';
        ajaxRequest(params, url, function () {
        });
    },


});

function getGovermentInfor(datas) {
    //商机id
    $("#project_intro .title").data('busid', datas.id);
    if (datas.type == "park") {
        $("#project_intro .title").html("园区介绍");
        $("#policy .title").html("园区优惠条件与政策");
    } else if (datas.type == "goverment") {
        $("#project_intro .title").html("项目介绍");
        $("#policy .title").html("招商优惠政策");
    }
    $("#gov_name").html(datas.subject); //商机标题
    if (datas.type == "goverment") {
        $(".key_word").html("<span>" + datas.attract_invest_way + "</span>");  //招商融资方式
    } else {
        $(".key_word").html("<span>" + datas.park_level + "</span><span>" + datas.park_type + "</span><span>" + datas.park_attr + "</span>");  //园区级别  类型 属性
    }

    $("#maker_subject").html(datas.dapartment);  //招商主题
    $("#maker_address").html(datas.address);  //ovo地址
    $("#seenNum").html(datas.view); //访问量
    $("#collectNum").html(datas.favorite_count);   //收藏量
    $("#industory").html(datas.industry); //行业

    $(".government_theme .title").html("<span>招商主体</span>" + datas.dapartment + "");  //招商主体
    $(".government_theme .head_icon>img").attr("src", datas.user.avatar);
    $(".government_theme .name>em").text(datas.name);

    /**项目介绍**/
    $("#project_intro .text").html(datas.intro);
    $("#policy .text").html(datas.policy);
    $("#condition .text").html(datas.condition);
    /**项目介绍截取**/
    if ($(".JS_alltext").length > 50) {
        $(".JS_alltext").text(cutString($(".JS_alltext").text(), 50));
        $(".JS_alltext").siblings('.seen_more').show();
    }
    $("#act_list").data("act_id", datas.id);
    $(".JS_ovo").data("opportunityId", datas.id);
    //分享页取消收藏判断
    if ((window.location.href).indexOf('is_share') > 0) {
        $('#loadAppBtn').removeClass('none');
        $('#installapp').removeClass('none');
        //游客不能删除+回复
        $("#tips").hide();
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
    } else {
        //群聊id
        $('.chat').data('groupid', datas.groupid);
        if (labUser.uid == '0') {
            //未登录
            $('#shareBtn').show();
            $('#noShareBtn').hide();
            //游客不能删、回复
            $("#tips").hide();
        } else {
            $('#noShareBtn').show();
            $('#shareBtn').hide();
            if (datas.is_favorite == '1') {
                $('.collectbtn').text('取消收藏');
            }
            else {
                $('.collectbtn').text('收藏');
            }
            if (datas.is_apply) {
                $('.JS_ovo').text('已发送申请');
                $('.JS_ovo').data('is_apply', '1');
            }
            else {
                $('.JS_ovo').data('is_apply', '0');
            }
        }
        //跨域对接
        $(".JS_ovo").click(function () {
            var opportunityId = $(this).data('opportunityId');
            var applyFlag = $(this).data('is_apply');
            opportunityDock(opportunityId, applyFlag);
        });
        //收藏/取消收藏
        $(document).on('click', '.collectbtn', function () {
            var bus_id = $("#project_intro .title").data('busid');
            if ($(this).text() == '收藏') {
                Collect.getCollect(bus_id, "opportunity", '1');
            }
            else {
                Collect.getCollect(bus_id, "opportunity", '0');
            }
        });

        //群聊
        $(document).on('tap', '.chat', function () {
            var groupid = $(this).data('groupid');
            var gov_name = $('#gov_name').text();
            GotoGroupChat(groupid, gov_name, '1');
        });
        /***导航切换***/
        $(document).on("tap", ".column>span", function () {
            $(this).addClass('green').siblings().removeClass('green');
            var _type = $(this).data('type');
            if (_type === 'projectNews') {
                $("#act_intro").show();
                $("#comment").hide();
                $(".comment_input").hide();
                $(".comment_btn").hide();
                if (labUser.uid !== '0') {
                    $("#noShareBtn").show();
                    $("#shareBtn").hide();
                }
                else {
                    $("#shareBtn").show();
                    $("#noShareBtn").hide();
                }
            }
            else if (_type === 'comment') {
                $("#shareBtn").hide();
                $("#noShareBtn").hide();
                $("#comment").show();
                $("#act_intro").hide();
                $(".comment_btn").show();
            }
        });
    }
    ;


}

function act(datas) {
    /**相关活动**/
    var aboutActHtml = '';
    $.each(datas, function (index, item) {
        aboutActHtml += '<dl data-ovoid=' + item.now_maker_id + ' data-actid=' + item.id + '>';
        if('vip_id' in item){
            aboutActHtml+='<span class="zbflag f12">专版活动</span>';
        }
        aboutActHtml += '<dt class="act_pics l"><img src="' + item.list_img + '"></dt>';
        aboutActHtml += '			<dd>';
        aboutActHtml += '				<p class="act_name">' + cutString(item.subject,27) + '</p>';
        //var arrs =['杭州','温州','宁波','上海','北京','内蒙古','拉萨'];
        //var zonestr = (item.zone).join(' ');
        aboutActHtml += '				<p class="dark_gray f12">' + unix_to_date(item.begin_time) + '&nbsp;' + cutString(item.zone.join(','),10)  + '</p>';
        aboutActHtml += '				<div class="money_collect">';
        if (item.price == "-1") {
            aboutActHtml += '					<span class="money green">免费</span>';
        } else {
            aboutActHtml += '					<span class="money orange">￥<em class="f20">' + (item.price).split("~")[0] + '</em>起</span>';
        }
        aboutActHtml += '					<span class="seen"><i class="seen_icon"></i><em class="dark_gray">' + item.views + '</em></span>';
        aboutActHtml += '				</div>';
        if (item.is_recommend == 1) {
            aboutActHtml += '<div class="recommend"></div>';
        }
        aboutActHtml += '			</dd>';
        aboutActHtml += '			<div class="clearfix"></div>';
        aboutActHtml += '</dl>';
    });
    $(".actBox").html(aboutActHtml);

    /**查看活动详情**/
    $(".actBox dl").click(function () {
        var id = $(this).data("actid"),
            ovoid = $(this).data('ovoid');
        if ((window.location.href).indexOf('is_share') > 0) {
            window.location.href = labUser.path + "webapp/activity/detail?id=" + id + "&makerid=" + ovoid + "&is_share=1&pagetag=02-2";
        }
        else {
            window.location.href = labUser.path + "webapp/activity/detail?id=" + id + "&makerid=" + ovoid + "&pagetag=02-2";
        }
        //var views = $(this).children('.seen em').text();
        //views++;
        //$(this).children('.seen em').html(views);
    });
}
