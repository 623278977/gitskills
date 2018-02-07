var headers = {
            "UUID":''
        }
/*
 **根据设备去实现css
 */
var u = navigator.userAgent, app = navigator.appVersion;
var isAndroid = u.indexOf('Android') > -1 || u.indexOf('Linux') > -1; //android终端或者uc浏览器
var isiOS = !!u.match(/\(i[^;]+;( U;)? CPU.+Mac OS X/); //ios终端

var shareFlag = window.location.href.indexOf('is_share') > 0 ? true : false;
//获取设备唯一标识
function getUuid(uuid) {
    if(uuid!==undefined){
        headers.UUID=uuid;
    }else{
        if (isAndroid) {
            javascript:myObject.getUuid('');
        } else if (isiOS) {      
            window.webkit.messageHandlers.getUuid.postMessage('');
        }
    } 
}
if(!shareFlag){
    getUuid();
};
function ajaxRequest(param, requestUrl, successCallback) {
    param['_token'] = labUser.token;   
    $.ajax({
        type: 'POST',
        url: requestUrl,
        data: param,
        timeout: 20000,
        headers: headers,
        dataType: 'json',
        success: function (data) {
            if (successCallback && (successCallback instanceof Function)) {
                successCallback(data);
            }
        },
        error: function (data) {
            console.log('http error');
        },
        complete: function (XMLHttpRequest, status) { //请求完成后最终执行参数
            if (status == 'timeout') {//超时,status还有success,error等值的情况
                console.log('xhr timeout');
            }
        }
    });
};
/*日期转换成时间戳*/
function datetime_to_unix(datetime) {
    var tmp_datetime = datetime.replace(/:/g, '-');
    tmp_datetime = tmp_datetime.replace(/ /g, '-');
    var arr = tmp_datetime.split("-");
    var now = new Date(Date.UTC(arr[0], arr[1] - 1, arr[2], arr[3] - 8, arr[4], arr[5]));
    return parseInt(now.getTime() / 1000);
}
/*时间戳转换成月日时分*/
function unix_to_mdhm(unix) {
    var newDate = new Date();
    newDate.setTime(unix * 1000);
    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
    var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
    var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
    return M + '月' + D + '日 ' + h + ':' + m;
}
function unix_to_datetime(unix) {
    var newDate = new Date();
    newDate.setTime(unix * 1000);
    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
    var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
    var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
    return M + '/' + D + ' ' + h + ':' + m;
}
function unix_to_yeardatetime(unix) {
    var newDate = new Date();
    newDate.setTime(unix * 1000);
    var Y = newDate.getFullYear();
    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
    var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
    var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
    return Y + '-' + M + '-' + D + ' ' + h + ':' + m;
}
function unix_to_yeardate(unix) {
    var newDate = new Date();
    newDate.setTime(unix * 1000);
    var Y = newDate.getFullYear();
    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
    return Y + '-' + M + '-' + D;
}

function unix_to_yeardate2(unix) {
    var newDate = new Date();
    newDate.setTime(unix * 1000);
    var Y = newDate.getFullYear();
    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
    return Y + '/' + M + '/' + D;
}

function unix_to_date(unix) {
    var newDate = new Date();
    newDate.setTime(unix * 1000);
    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
    return M + '/' + D;
}

function unix_YMD(unix) {
    var newDate = new Date();
    newDate.setTime(unix * 1000);
    var Y = newDate.getFullYear();
    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate(); 
    return Y + '年' + M + '月' + D + '日' ;
};

function unix_to_fulltime(unix) {
    var newDate = new Date();
    newDate.setTime(unix * 1000);
    var Y = newDate.getFullYear();
    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
    var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
    var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
    return Y + '年' + M + '月' + D + '日' + ' ' + h + ':' + m;
}

function unix_to_fulltime_s(unix) {
      var newDate = new Date();
      newDate.setTime(unix * 1000);
      var Y = newDate.getFullYear();
      var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
      var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
      var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
      var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
      var s = newDate.getSeconds() < 10 ? '0' + newDate.getSeconds() : newDate.getSeconds();
      return Y + '年' + M + '月' + D + '日' + ' ' + h + ':' + m + ':' +s;
};

function unix_to_fulltime_hms(unix) {
        var newDate = new Date();
        newDate.setTime(unix * 1000);
        var Y = newDate.getFullYear();
        var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
        var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
        var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
        var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
        var s = newDate.getSeconds() < 10 ? '0' + newDate.getSeconds() : newDate.getSeconds();
        return Y + '/' + M + '/' + D  + ' ' + h + ':' + m + ':' +s;
  };
//年月日星期时分秒
function gw_now(unix){
	var newDate = new Date();
    newDate.setTime(unix * 1000);
	var Y = newDate.getFullYear();
	var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
	var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
	var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
	var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
	switch (newDate.getDay()) {
		case 0:week="星期天";break
		case 1:week="星期一";break
		case 2:week="星期二";break
		case 3:week="星期三";break
		case 4:week="星期四";break
		case 5:week="星期五";break
		case 6:week="星期六";break
	};
 	return Y + "-" + M + "-" + D + " " + week + " " + h + ":" + m;
};
//function gw_now_addzero(temp){  
// if(temp<10) return "0" + temp;
// else return temp;
//}


/**
 * 检查是否登陆
 * @param title
 */
function checkLogin() {
    var loginUid = LabUser.uid;
    if (loginUid) {

    } else {
        if (isAndroid) {
            javascript:myObject.userReLogin();
        } else if (isiOS) {
            userReLogin();
        }
    }
}

/**
 * 分享
 * title:活动标题
 * img:
 * header:类型(活动/视频)
 * content:描述
 */
function shareOut(title, url, img, header, content, begintime, citys, id, type, share_mark, relation_id, share_type, share_content, contentid) {
    var data = {};
    data['title'] = title;
    data['url'] = (url + "&is_share=1").replace(/uid=\d*/g,'uid=0').replace(/agent_id=\d*/g,'agent_id=0');
    data['img'] = img;
    data['header'] = header;
    data['content'] = content;
    data['begintime'] = begintime;
    data['citys'] = citys;
    data['id'] = id;
    data['type'] = type;
    data['share_mark'] = share_mark;//1
    data['relation_id'] = relation_id;//接口1、2都用到,详情页的code
    data['share_type'] = share_type;//接口1类型,share:分享，relay：转发， watch：观看，enroll：报名，sign:签到, view：点击， intent:品牌意向
    data['share_content'] = share_content;//接口2、分享的内容activity,live,video,news,brand等
    data['share_contentid'] = contentid;//接口2、分享的内容对应的id
    if (isAndroid) {
        javascript:myObject.share(JSON.stringify(data));
    } else if (isiOS) {
        window.webkit.messageHandlers.share.postMessage(data);
    }
}

/**
 * 过滤首尾空格
 */
function LTrim(str) {
    return str.replace(/^\s+/, "");
}

/**参数说明：
 * 根据长度截取先使用字符串，超长部分追加…
 * str 对象字符串
 * len 目标字节长度
 * 返回值： 处理结果字符串
 */
function cutString(str, len) {
    var str = str || '';
    var str_len = str.length;
    if (str_len <= len) {
        return str;
    } else {
        return str.substring(0, len) + '...';
    }
}

//html转义
function encodeHtml(str) {
    var s = "";
    var str = str || '';
    if (str.length == 0) return "";
    s = str.replace(/&/g, "&gt;");
    s = s.replace(/</g, "&lt;");
    s = s.replace(/>/g, "&gt;");
    s = s.replace(/ /g, "&nbsp;");
    s = s.replace(/\'/g, "&#39;");
    s = s.replace(/\"/g, "&quot;");
    s = s.replace(/\n/g, "<br>");
    return s;
}

//去除html标签及样式
function removeHTMLTag(description) {
    var description = description || '';
    description = description.replace(/(\n)/g, "");
    description = description.replace(/(\t)/g, "");
    description = description.replace(/(\r)/g, "");
    description = description.replace(/<\/?[^>]*>/g, "");
    description = description.replace(/\s*/g, "");
    return description;
}

/**ios 不兼容 fixed**/
//if (isiOS) {
//    $(document).on("focus", "#comment_content", function () {
//        $(".j_send").css("position", "relative");
//    }).on("focusout", "#comment_content", function () {
//        $(".j_send").css("position", "fixed");
//    });
//}

/***导航切换***/
//$(document).on("tap", ".column>span", function () {
//    $(this).addClass('green').siblings().removeClass('green')
//});

/**本地商圈的地址**/
/**展开收起**/
//$(document).on("tap", ".act_intro .up", function () {
//    $(".act_address").css("height", "17rem");
//    $(".act_address").addClass('max');
//    $(".up").hide();
//    $(".down").show();
//});
//$(document).on("tap", ".act_intro .down", function () {
//    $(".act_address").css("height", "auto");
//    $(".act_address").removeClass('max');
//    $(".up").show();
//    $(".down").hide();
//});

/**时间戳转化为日期格式**/
function act_time(time) {
    var date = new Date(parseInt(time * 1000));
    var M = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : date.getMonth() + 1) + '/';
    var D = date.getDate() + ' ';
    var h = date.getHours() + ':';
    var m = date.getMinutes();
    return M + D + h + m;
}

/**返回个人主页**/
/**
 * 个人中心@param  uid
 */
function gotoPersonalCenter(uid) {
    // if (!parseInt(uid))  return false;
    if (isAndroid) {
        javascript:myObject.showUserDetail(uid);
    } else if (isiOS) {
        var data = {
            "uid": uid
        }
        window.webkit.messageHandlers.showUserDetail.postMessage(data);
    }
}
$(document).on('tap', '#publishers', function () {
    var uid = $(this).attr("value");
    gotoPersonalCenter(uid);
});

/**查看更多活动详情**/
function seenmoreActivity(id) {
    window.location.href = labUser.path + "webapp/activity/detaildescription?id=" + id + "&pagetag=02-2-2";
}
$(document).on('tap', '#actMoreDetail', function () {
    var id = $(this).attr("value");
    seenmoreActivity(id);
});
//$(document).on('tap', '#videosMoreDetail', function () {
//    var id = $(this).attr("value");
//    var type = $(this).data(type)||"";
//    moreVideoDes(id,type);
//});
function moreVideoDes(id, type) {
    window.location.href = labUser.path + "webapp/activity/detail-description?id=" + id + "&pagetag=08-8&type=" + type;
}
/**查看更多视频详情**/
//function seenmoreVideo(id) {
//    window.location.href = labUser.path + "webapp/activity/detail-description?id=" + id + "&pagetag=08-8";
//}
//$(document).on('tap', '#videoMoreDetail', function () {
//    var id = $(this).attr("value");
//    seenmoreVideo(id);
//});
/**to video list**/
function seenmoreVideo(id) {
    // if (!parseInt(uid))  return false;
    if (isAndroid) {
        javascript:myObject.gotoMoreLiveList(id);
    } else if (isiOS) {
        var data = {
            "id": id
        }
        window.webkit.messageHandlers.gotoMoreLiveList.postMessage(data);
    }
}
//to live list
function gotoLiveList(id) {
    if (isAndroid) {
        javascript:myObject.gotoLiveList(id);
    } else if (isiOS) {
        var data = {
            "id": id
        }
        window.webkit.messageHandlers.gotoLiveList.postMessage(data);
    }
}


/**购买按钮**/
$(".buy_btn").click(function () {
    $(".apply_box,.mengceng").show();
});
$(".apply_box .close").click(function () {
    $(".apply_box,.mengceng").hide();
});

/**支付**/
$(".apply_box .sure").click(function () {

});


/****与app端交互***/
//ovo中心地图显示
function locationAddress(address, act_name, description) {
    var data = {
        "address": address,
        "act_name": act_name,
        "description": description
    };
    var dataStr = JSON.stringify(data);
    if (isAndroid) {
        javascript:myObject.locationAddress(dataStr);
    } else if (isiOS) {
        window.webkit.messageHandlers.locationAddress.postMessage(data);
    }
}

//转发布者跳主办方
function gotoActivityList(pub_id, is_official) {
    if (isAndroid) {
        javascript:myObject.gotoActivityList(pub_id, is_official);
    } else if (isiOS) {
        var data = {
            "pub_id": pub_id,
            "is_official": is_official
        }
        window.webkit.messageHandlers.gotoActivityList.postMessage(data);
    }
}

//查看更多活动
function gotoMoreActivityList(nowActivityId) {
    if (isAndroid) {
        javascript:myObject.gotoMoreActivityList(nowActivityId);
    } else if (isiOS) {
        var data = {
            "nowActivityId": nowActivityId
        }
        window.webkit.messageHandlers.gotoMoreActivityList.postMessage(nowActivityId);
    }
}
//专版查看更多录播视频
function zbVideoMore(video_id, zb_name) {
    if (isAndroid) {
        javascript:myObject.zbVideoMore(video_id, zb_name);
    } else if (isiOS) {
        var data = {
            "moreId": video_id,
            "moreName": zb_name
        }
        window.webkit.messageHandlers.zbVideoMore.postMessage(data);
    }
}
//专版查看更多直播视频
function zbLiveMore(video_id, zb_name) {
    if (isAndroid) {
        javascript:myObject.zbLiveMore(video_id, zb_name);
    } else if (isiOS) {
        var data = {
            "moreId": video_id,
            "moreName": zb_name
        }
        window.webkit.messageHandlers.zbLiveMore.postMessage(data);
    }
}
//专版查看更多活动
function zbActivityMore(video_id, zb_name, position_id) {
    if (isAndroid) {
        javascript:myObject.zbActivityMore(video_id, zb_name, position_id);
    } else if (isiOS) {
        var data = {
            "moreId": video_id,
            "moreName": zb_name,
            "position_id": position_id
        }
        window.webkit.messageHandlers.zbActivityMore.postMessage(data);
    }
}


//群聊
function GotoGroupChat(groupid, act_name, followflag) {
    var data = {
        "chatid": groupid,
        "act_namei": act_name,
        "followflag": followflag
    };
    var dataStr = JSON.stringify(data);
    if (isAndroid) {
        javascript:myObject.GotoGroupChat(dataStr);
    } else if (isiOS) {
        window.webkit.messageHandlers.GotoGroupChat.postMessage(data);
    }
}


//申请ovo跨域对接
function opportunityDock(opportunityId, flag) {
    var data = {
        "opportunityId": opportunityId,
        "applyFlag": flag
    };
    var dataStr = JSON.stringify(data);
    if (isAndroid) {
        javascript:myObject.opportunityDock(dataStr);
    } else if (isiOS) {
        window.webkit.messageHandlers.opportunityDock.postMessage(data);
    }
}

//设置手机Title栏收藏状态,"1"已收藏，"0"未收藏
function setFavourite(state) {
    if (isAndroid) {
        javascript:myObject.setFavourite(state);
    } else if (isiOS) {
        window.webkit.messageHandlers.setFavourite.postMessage(state);
    }
}

//判断微信内置浏览器打开
function is_weixin() {
    var ua = navigator.userAgent.toLowerCase();
    if (ua.match(/MicroMessenger/i) == "micromessenger") {
        return true;
    } else {
        return false;
    }
}

//门票列表
//is_pay:1 收费；0 免费；
function ActivityApply(activityId, ovoId, act_name, is_pay, type,site_ticket_count) {
    if (isAndroid) {
        javascript:myObject.ActivityApply(activityId, ovoId, act_name, is_pay, type,site_ticket_count);
    } else if (isiOS) {
        var data = {
            "activityId": activityId,
            "ovoId": ovoId,
            "product": act_name,
            "type": type,
            "is_pay": is_pay,
            "site_ticket_count":site_ticket_count
        }
        window.webkit.messageHandlers.ActivityApply.postMessage(data);
    }
}

//提示登陆
function showLogin() {
    if (isAndroid) {
        javascript:myObject.showLogin();
    } else if (isiOS) {
        window.webkit.messageHandlers.showLogin.postMessage('noparam');
    }
}
//购买商品
function buygoods(id, type) {
    if (isAndroid) {
        javascript:myObject.buygoods(id, type);
    } else if (isiOS) {
        var data = {
            "id": id,
            "type": type
        }
        window.webkit.messageHandlers.buygoods.postMessage(data);
    }
}
//购买直播
// function buyTicket(id, ticket_id, type, price) {
//     if (isAndroid) {
//         javascript:myObject.buyTicket(id, ticket_id, type, price);
//     } else if (isiOS) {
//         var data = {
//             "id": id,
//             "ticket_id": ticket_id,
//             "type": type,
//             "price": price
//         }
//         window.webkit.messageHandlers.buyTicket.postMessage(data);
//     }
// }
//2.5版本购买点播
function buyVideo(id, price) {
    if (isAndroid) {
        javascript:myObject.buyVideo(id, price);
    } else if (isiOS) {
        var data = {
            "id": id,
            "price": price
        }
        window.webkit.messageHandlers.buyVideo.postMessage(data);
    }
}

//购买专版会员
function buyVip(vip_id) {
    if (isAndroid) {
        javascript:myObject.buyVip(vip_id);
    } else if (isiOS) {
        var data = {
            "vip_id": vip_id
        }
        window.webkit.messageHandlers.buyVip.postMessage(data);
    }
}
/**直播、点播**/
//function BuyTicket() {
//    window.location.href = labUser.path + "webapp/ticket/list";
//}
//$("#select_price").click(function () {
//    BuyTicket();
//});


//微信二次分享
function regsiterWX(title, imgurl, url, desc, type, dataUrl) {
    alert(title);
    alert(imgurl);
    alert(url);
    alert(desc);
    //type 分享类型,music、video或link，不填默认为link
    //dataUrl 如果type是music或video，则要提供数据链接，默认为空
    wx.ready(function () {
        wx.onMenuShareTimeline({
            title: title, // 分享标题
            link: url, // 分享链接
            imgUrl: imgurl, // 分享图标
            success: function () {
                // 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
        wx.onMenuShareAppMessage({
            title: title,
            desc: desc,
            link: url,
            imgUrl: imgurl,
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
//打赏
function reward(id, type) {
    if (isAndroid) {
        javascript:myObject.reward(id, type);
    } else if (isiOS) {
        var data = {
            "id": id,
            "type": type
        }
        window.webkit.messageHandlers.reward.postMessage(data);
    }
}
//istext=true：仅文字
//istext=false：文字传图
function uploadpic(id, type, istext) {
    if (isAndroid) {
        javascript:myObject.uploadpic(id, type, istext);
    } else if (isiOS) {
        var data = {
            "id": id,
            "type": type,
            "istext": istext           
        }
        window.webkit.messageHandlers.uploadpic.postMessage(data);
    }
}

// 关闭签到成功弹窗
function closeAlert(id, uid, maker_id) {
    if (isAndroid) {
        javascript:myObject.closeAlert(id, uid, maker_id);
    } else if (isiOS) {
        var data = {
            "id": id,
            "uid": uid,
            "maker_id": maker_id
        }
        window.webkit.messageHandlers.closeAlert.postMessage(data);
    }
}
//签到异常后，跳出页面
function pop(id) {
    if (isAndroid) {
        javascript:myObject.pop(id);
    } else if (isiOS) {
        var data = {
            "id": id
        }
        window.webkit.messageHandlers.pop.postMessage(data);
    }
}
//表情UTF-16转UTF-8
function utf16toEntities(str) {
    var patt = /[\ud800-\udbff][\udc00-\udfff]/g;
    // 检测utf16字符正则
    str = str.replace(patt, function (char) {
        var H, L, code;
        if (char.length === 2) {
            H = char.charCodeAt(0);
            // 取出高位
            L = char.charCodeAt(1);
            // 取出低位
            code = (H - 0xD800) * 0x400 + 0x10000 + L - 0xDC00;
            // 转换算法
            return "&#" + code + ";";
        } else {
            return char;
        }
    });
    return str;
}
//表情解码
function entitiestoUtf16(str) {
    // 检测出形如&#12345;形式的字符串
    var strObj = utf16toEntities(str);
    var patt = /&#\d+;/g;
    var H, L, code;
    var arr = strObj.match(patt) || [];
    for (var i = 0; i < arr.length; i++) {
        code = arr[i];
        code = code.replace('&#', '').replace(';', '');
        // 高位
        H = Math.floor((code - 0x10000) / 0x400) + 0xD800;
        // 低位
        L = (code - 0x10000) % 0x400 + 0xDC00;
        code = "&#" + code + ";";
        var s = String.fromCharCode(H, L);
        strObj.replace(code, s);
    }
    return strObj;
}
//BIG PICTRUES
function lookBigPhoto(jsonData) {
    if (isiOS) {
        window.webkit.messageHandlers.sendPhotosToClient.postMessage(jsonData);
    } else if (isAndroid) {
        javascript:myObject.sendPhotosToClient(jsonData);
    }
}
//show player
function showPlayer(url, height, livetitle, liveid, unit, num) {
    if (isAndroid) {
        if(unit && num){
            javascript:myObject.showPlayer(url, height, livetitle, liveid, unit, num);
        }
        else{
            javascript:myObject.showPlayer(url, height, livetitle, liveid);
        }
    } else if (isiOS) {
        var data = {
            "url": url,
            "height": height,
            "title": livetitle,
            "liveid": liveid,
            "unit": unit,
            "num": num
        }
        window.webkit.messageHandlers.showPlayer.postMessage(data);
    }
}
//get query string
function getQueryStringArgs() {
    var qs = (location.search.length > 0 ? location.search.substring(1) : ''),
        arsg = {},
        items = qs.length ? qs.split('&') : [],
        item = null,
        name = null,
        value = null,
        len = items.length;
    for (var i = 0; i < len; i++) {
        item = items[i].split('=');
        name = decodeURIComponent(item[0]);
        value = decodeURIComponent(item[1]);
        if (name.length) {
            arsg[name] = value;
        }
    }
    return arsg;
}
//赞头像
function zanImages(image_array) {
    var imageLength = image_array.length;
    if (imageLength > 0) {
        var zanIamgesHtml = '';
        $.each(image_array, function (index, item) {
            zanIamgesHtml += '<img src="' + item.avatar + '" alt="">';
        });
        $('#zan-images').html(zanIamgesHtml);
        if (imageLength > 27) {
            $('#moremig').removeClass('none');
        }
    }
}
//热度值处理
function getHotNum() {
    var args = getQueryStringArgs(),
        activity_id = args['id'],
        uid = args['uid'] || '0';
    var param = {};
    param["id"] = activity_id;
    param["uid"] = uid;
    var url = labUser.api_path + '/activity/detail/_v020400';
    ajaxRequest(param, url, function (data) {
        if (data.status) {
            var resObj = data.message;
            $('#hotnum').html('热度 ' + resObj.hot_count + '<i></i>');
            $('#seen').html(resObj.view_count + '次');
            $('#dianzan').html(resObj.zan_count + '次');
            $('#plun').html(resObj.comment_count + '次');
            $('#zhuan').html(resObj.share_count + '次');
            $('#zan-number').html(resObj.zan_count);
            $('#commentnum').html(resObj.comment_count);
            zanImages(resObj.zans);
        }
    });
}
//活动详情，品牌详情浏览记录
function viewAdd(uid, relation, relation_id) {
    var param = {};
    param["relation_id"] = relation_id;
    param["uid"] = uid;
    param["relation"] = relation;
    var url = labUser.api_path + '/user/add-browse/_v020400';
    ajaxRequest(param, url, function (data) {
        if (data.status) {
        }
    });
}
//提示安装APP悬浮条的关闭
$(document).on('tap click', '.install-close ', function () {
    $('#installapp').addClass('none');
})

//打电话
function callNum(num) {
    if (isAndroid) {
        javascript:myObject.callNum(num);
    } else if (isiOS) {
        var data = {
            "num": num
        }
        window.webkit.messageHandlers.callNum.postMessage(data);
    }
}
//分享奖励入库
function getReward(share_mark, type, uid, relation_id) {
    var param = {};
    param['share_mark'] = share_mark;
    param['type'] = type;
    param['uid'] = uid;
    param['relation_id'] = relation_id;
    var url = labUser.api_path + '/share/collect-score/_v020500';
    ajaxRequest(param, url, function (data) {
        if (data.status) {

        }
    });
};
//分享奖励二次分享记录
function secShare(uid, content, content_id, source, share_mark) {
    var param = {};
    param['uid'] = uid;
    param['content'] = content;
    param['content_id'] = content_id;
    param['source'] = source;
    param['share_mark'] = share_mark;
    var url = labUser.api_path + 'share/share/_v020500';
    ajaxRequest(param, url, function (data) {
        if (data.status) {
        }
        ;
    })
};

//IOS跳品牌详情页
function pushToBrandDetail(id) {
    if (isiOS) {
        var data = {
            "id": id
        }
        window.webkit.messageHandlers.pushToBrandDetail.postMessage(data);
    }
}
//2.5购买直播票
function buyLiveTicket(activity_id, ticket_id, price) {
    if (isAndroid) {
        javascript:myObject.buyTicket(activity_id, ticket_id, price);
    } else if (isiOS) {
        var data = {
            "activity_id": activity_id,
            "ticket_id": ticket_id,
            "price": price
        }
        window.webkit.messageHandlers.buyTicket.postMessage(data);
    }
}
//2.7 资讯录播 积分购买
function toScore(type,score,id) {
    if (isAndroid) {
        javascript:myObject.toScore(type,score,id);
    } else if (isiOS) {
        var data = {
            'type':type,
            'score':score,
            'id':id
        };
        window.webkit.messageHandlers.toScore.postMessage(data);
    }
}

//品牌视频页，点击了解更多精彩视频
function toBrandwall() {
    if (isAndroid) {
        javascript:myObject.toBrandwall();
    } else if (isiOS) { 
        window.webkit.messageHandlers.toBrandwall.postMessage('noparam');
    }
}

//品牌是否有关联视频，如果没有不展示视频的标签 type:1 展示；0 不展示
function showVideo(type){
    if (isAndroid) {
        javascript:myObject.showVideo(type);
    } else if (isiOS) {  
        var data={
            'type':type
        }
        window.webkit.messageHandlers.showVideo.postMessage(data);
    }
}


//经纪人相关
function showLesson(type){
    if (isAndroid) {
        javascript:myObject.showLesson(type);
    } else if (isiOS) {  
        var data={
            'type':type
        }
        window.webkit.messageHandlers.showLesson.postMessage(data);
    }
}
function unix3(unix){
    var newDate = new Date();
    newDate.setTime(unix * 1000);
    var Y = newDate.getFullYear();
    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
    var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
    var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
    var s = newDate.getSeconds() < 10 ? '0' + newDate.getSeconds() : newDate.getSeconds();
    return  M + '/' + D ;
 }
    // 再次发送
function sendRichMsg(type,uType,uid,id,title,imgUrl,date,store,amount,contract_joinType){
    if (isAndroid) {
        javascript:myObject.sendRichMsg(type,uType,uid,id,title,imgUrl,date,store,amount,contract_joinType);
    }else if(isiOS){
        var data={
            'type':type,
            'uType':uType,
            'uid':uid,
            'id':id,
            'title':title,
            'imgUrl':imgUrl,
            'date':date,
            'store':store,
            'amount':amount,
            'contract_joinType':contract_joinType
                };
        window.webkit.messageHandlers.sendRichMsg.postMessage(data);
    }
   }
  // 提示框
  function alertShow(content){
      $(".common_pops").text(content);
      $(".common_pops").css("display","block");
      setTimeout(function(){$(".common_pops").css("display","none")},2000);
 }; 

  //品牌详情改变移动端标题
    function changeBrandTitle(index) {
        if (isAndroid) {
            javascript:myObject.changeBrandTitle(index);
        } 
        else if (isiOS) {
            var data = {
                'index':index
            }
            window.webkit.messageHandlers.changeBrandTitle.postMessage(data);
        }
    }
//弹出提示语
function tips(e){
    $('.common_pops').text(e).removeClass('none');
    setTimeout(function() {
        $('.common_pops').addClass('none');
    }, 3000);
};

//经纪人埋点
function onAgentEvent(action,str,obj) {
    if (isAndroid) {
        javascript:jsUmsAgent.onEvent(action,str,obj);
    } else if (isiOS) {
        var data = {
            'eventId':action,
            'id':obj.id,
            'type':obj.type,
            'agent_id':obj.userId,
            'position':obj.position
        };
        window.webkit.messageHandlers.onEvent.postMessage(data);
    }
}
//设置标题
 function setPageTitle(title) {
             if (isAndroid) {
                  javascript:myObject.setPageTitle(title);
              }else if (isiOS) {
                var data={
                  "title":title
                };
                window.webkit.messageHandlers.setPageTitle.postMessage(data);
              }
};
//秒转换成天时分秒	
function timeStamp(second_time){  
	var time = parseInt(second_time) + "秒";  
	if( parseInt(second_time )> 60){  
	    var second = parseInt(second_time) % 60;  
	    var min = parseInt(second_time / 60);  
	    time = min + "分" + second + "秒";  
	    if( min > 60 ){  
	        min = parseInt(second_time / 60) % 60;  
	        var hour = parseInt( parseInt(second_time / 60) /60 );  
	        time = hour + "小时" + min + "分" + second + "秒";  
	        if( hour > 24 ){  
	            hour = parseInt( parseInt(second_time / 60) /60 ) % 24;  
	            var day = parseInt( parseInt( parseInt(second_time / 60) /60 ) / 24 );  
	            time = day + "天" + hour + "小时" + min + "分" + second + "秒";  
	        }  
	    }  
	}  
    return time;          
}  

// 复制到粘贴板
function copyToCb(text){
    if (isAndroid) {
        javascript:myObject.copyToCb(text);
    } 
    else if (isiOS) {
        var data = {
            'text':text
        }
        window.webkit.messageHandlers.copyToCb.postMessage(data);
    }
}

//评论列表跳转对应评论所在页面锚点处
function act(className,idName,idNum){
	//className为循环的class名  id上页面跳转的锚点id idNum属性所需要匹配的数值
	var args = getQueryStringArgs(),
		skips = args[idNum];
		console.log(skips);
	$.each($(className), function(i,v) {
    	$(this).attr('mao',i+1);
    	var val = $(this).attr('mao');
    	if(val == skips){
    		$(this).attr('id',idName);
    	}
    });
};     
//经纪人看大图
function sendPhotosToClient(jsonData) {
    if (isiOS) {
           var message = {
                method:'sendPhotosToClient',
                params:jsonData
            }; 
        window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
    } else if (isAndroid) {
        javascript:myObject.sendPhotosToClient(jsonData);
    }
}
  function getpict(element){
                var img = $(element).find('img'),
                    imgarray = [];
                if (img) {
                    $.each(img, function(k, v) {
                        $(v).attr('index', k);
                        imgarray.push(v);
                        return imgarray;
                    })
                    console.log(imgarray);
                }
                if (isiOS || isAndroid) {
                    $(element).find('img').on('click', function() {
                        var this_index = $(this).attr('index');
                        var imgary = $(element).find('img');
                        var photos = [];
                        $.each(imgary, function(index, item) {
                            var photo = {
                                url: $(this).attr('src'),
                                selected: 0
                            };
                            if ($(this).attr('index') == this_index) {
                                photo.selected = 1;
                            }
                            photos.push(photo);
                        });
                        var jsonData = JSON.stringify(photos);
                        sendPhotosToClient(jsonData);
                    });
                }
       };
//禁用长按弹出菜单事件
$('img').bind('contextmenu', function(e) {
  e.preventDefault();
}) 


//上传截图
    function upLoadScreenShot(){
        if (isAndroid) {
            javascript:myObject.upLoadScreenShot();
        } 
        else if (isiOS) {
            var message = {
                method : 'upLoadScreenShot'
            }; 
            window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
        }
    }
//创建活动邀请
    function creatActInvite(){
        if (isAndroid) {
            javascript:myObject.creatActInvite();
        } 
        else if (isiOS) {
            var message = {
                method : 'creatActInvite'
            }; 
            window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
        }
    }
//创建考察邀请
    function creatInvestigate(){
        if (isAndroid) {
            javascript:myObject.creatInvestigate();
        } 
        else if (isiOS) {
            var message = {
                method : 'creatInvestigate'
            }; 
            window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
        }
    }
//邀请投资人  
    function noti_invite(){
        if (isAndroid) {
            javascript:myObject.noti_invite();
        } 
        else if (isiOS) {
            var message = {
                method : 'noti_invite',
                params:{}
            }; 
            window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
        }
    }

//新版本分享（ios交互方法）
function agentShare(title, url, img, header, content,type,id,weibo,wechat) {
    var data = {};
    data['title'] = title;
    data['url'] = (url + "&is_share=1").replace(/uid=\d*/g,'uid=0').replace(/agent_id=\d*/g,'agent_id=0');
    data['img'] = img;
    data['header'] = header;
    data['content'] = content;
    data['type'] = type;
    data['id'] = id;
    data['weibo'] = weibo;
    data['wechat'] = wechat;
    if (isAndroid) {
        javascript:myObject.share(JSON.stringify(data));
    } else if (isiOS) {
        var message = {
                method : 'share',
                params : data
            }; 
        window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
    }
}
function investorShare(title, url, img, header, content, begintime, citys, id, type,weibo,wechat) {
    var data = {};
    data['title'] = title;
    data['url'] = (url + "&is_share=1").replace(/uid=\d*/g,'uid=0').replace(/agent_id=\d*/g,'agent_id=0');
    data['img'] = img;
    data['header'] = header;
    data['content'] = content;
    data['begintime'] = begintime;
    data['citys'] = citys;
    data['id'] = id;
    data['type'] = type;
    data['weibo'] = weibo;
    data['wechat'] = wechat;
    if (isAndroid) {
        javascript:myObject.share(JSON.stringify(data));
    } else if (isiOS) {
        var message = {
                method : 'share',
                params : data
            }; 
        window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
    }
}
//iphonex 底部简易版兼容    需要在页面加<section style="position: fixed;bottom: 0;background: #FFFFFF;height:17px" class="iphone_btn none"></section>      参数为底部按钮的class名
function iphonexBotton(className){
	if(isiOS){
		if (window.screen.height === 812) {
		    $(className).css('bottom', '17px');
		    $('.iphone_btn').removeClass('none');
		  }
		
	}
};
