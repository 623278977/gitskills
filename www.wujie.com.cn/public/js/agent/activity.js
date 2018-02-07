
var pageNow = 1,
    pageSize = 5;
//分享
function shareOut(title, url, img, header, content, begintime, citys, actid,type) {
    var data = {};
        data['title'] = title;
        data['url'] = (url + "&is_share=1").replace(/uid=\d*/g,'uid=0');
        data['img'] = img;
        data['header'] = header;
        data['content'] = content;
        data['begintime'] = begintime;
        data['citys'] = citys;
        data['id'] = actid;
        data['type'] = type;
    if (isAndroid) {
        javascript:myObject.share(JSON.stringify(data));
    } else if (isiOS) {
        window.webkit.messageHandlers.share.postMessage(data);
    }
}
//分享到微信
 function showShare(){
    var  type = 'activity',
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
function reload(){
    location.reload();
}
Zepto(function (){
    new FastClick(document.body);
    var urlPath = window.location.href,
        args = getQueryStringArgs(),
        activity_id = args['id'],
        uid = args['agent_id'] || '0',
        position_id = args['position_id'] || '0',
        maker_id = args['makerid'] || '0'
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
    var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
    var activityDetail = {
        detail: function (activity_id) {
            var param = {};
                param["id"] = activity_id;
                param["agent_id"] = uid;
            var url=labUser.agent_path+'/activity/detail/_v010000';
            ajaxRequest(param, url, function (data) {
            if (data.status) {
            activityData(data.message, shareFlag);
            activityDetail.bannerPic(data.message.banner_img);
            activityDetail.share();
            $('.ui_share li').eq(1).data('ticket_id',data.message.ticket_id);
            }
            });
        },
        bannerPic:function(obj){
            var swiper = new Swiper('.swiper-container', {
                pagination: '.swiper-pagination',
                paginationType: 'custom',
                paginationCustomRender: function (swiper, current, total) {
                    return '<span class="f16">' + current + '</span>' + ' / ' + total;
                }
            });
            var str = '';
            $.each(obj, function (index, item) {
                str += '<div class="swiper-slide"><img src="' + item.src + '" alt="" /></div>';
            });
            $('.swiper-wrapper').append(str);
        },
        share:function(){
            if(shareFlag){
                $('.ui_share').show();
                $('.fixedbottom').hide();
                $('.ui_share li').eq(1).on('click',function(){
                 var ticket_id=$(this).data('ticket_id');
                 window.location.href = labUser.path+'webapp/freecheck/detail/_v020400?id='+activity_id+'&ticket_id='+ticket_id;
                })
            }else{
                $('.ui_share').hide();
                $('.fixedbottom').show();
            }
        }
    };
    activityDetail.detail(activity_id,uid);
    function activityData(result, is_share){
        var selfObj = result;
        commonHTML(selfObj, is_share);
        if (is_share) {
            $('#loadAppBtn').removeClass('none');
            $('#installapp').removeClass('none');
            $('#share').addClass('none');
            var activity='actID'+activity_id;
            if($('#share').data('reward')==1&&(!localStorage.getItem(activity))){
                try {
                    localStorage.setItem(activity,activity_id);
                    return true;
                } catch (error) {
                    alert('隐私/无痕模式效果会不太好哦!请切换到正常模式');
                }
            }
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
                        //分享到朋友圈
                            wx.onMenuShareTimeline({
                                title: selfObj.subject, // 分享标题
                                link: location.href, // 分享链接
                                imgUrl: selfObj.share_image, // 分享图标
                                success: function () {
                                    if($('#share').data('reward')==1){
                                        sencondShare('relay')
                                    }
                                   
                                },
                                cancel: function () {
                                    // 用户取消分享后执行的回调函数
                                }
                            });
                        //分享给朋友
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
                                    if($('#share').data('reward')==1){
                                        sencondShare('relay')
                                    }
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
        $('#act_container').removeClass('none');
    };
    //共同点
    function commonHTML(selfObj, is_share) {
        $('#share_img').data('src', selfObj.share_image); 
        var title=selfObj.title;
        if(title.length>20){
         $('.baoming').css('margin-top','1.1rem')
         }
         $('#act_name').html(title);   
         $('#act_name').data('act_id', selfObj.id);
        var begin_time = unix_to_fulltime(selfObj.begin_time);
        var bt = unix_to_fulltime(selfObj.begin_time);
        var newtime = Math.round(new Date().getTime() / 1000);
        $('#act_des').data('begintime', bt);
        $('#act_time').html(begin_time+'<samp style="padding-left:1rem">开始</samp>');
        $('#citys').html(selfObj.cities);
        $('#actdescription').html(selfObj.detail.replace(/http:/g,'https:'));
        $('#actdescription  img').css('width','100%');
        if(newtime>selfObj.end_time){
            $('#baoming').removeClass('baoming').addClass('baomingover');
            $('.fixedbottom').text('活动已结束').css('background','#ccc');
        }
        $('.fixedbottom').data('title',selfObj.title).data('imgurl',selfObj.share_image);
        if(selfObj.brand.length>0){
                var brandHtml = '';
            $.each(selfObj.brand, function (index, item) {
                var keywordhtml = '';
                    brandHtml += ' <div class="white-bg brand-company pl1-33 fline" data-brand_id="' + item.id + '">';
                    brandHtml += '<img src="' + item.logo + '" alt="" class="company mr1-33 fl">';
                    brandHtml += '<div class="fl width60"><em class="service f12 mr1">'+item.category_name+'</em>';
                    brandHtml += '<span class="f14 b">'+cutString(item.title, 10)+'</span> <div class="brand-desc f12 color999 mb05 ui-nowrap-multi">'+removeHTMLTag(item.brand_summary) + '</div>';
                    brandHtml += '<p class="f12 mb05"><span class="c8a">投资额：</span><span class="color-red">'+item.investment_min+'~'+item.investment_max+'万</span>';
                    brandHtml += '</p>';
                if (item.keywords.length > 0) {
                    $.each(item.keywords, function (index, oneitem){
                       keywordhtml +='<span class="border-8a-radius ui-border-radius-8a" style="margin-right: 0.666666rem;font-size: 1.1rem;color:#999;padding: 0.1rem 0.5rem">' + oneitem + '</span>';
                    });
                    brandHtml += keywordhtml;
                }
                    brandHtml +='</div><div class="clearfix"></div></div>';
            });
                $('#pinpai').append(brandHtml);
        }else{
                $('#pinpai').remove();
                $("#bgbrand").attr("style","display:none");
        }
    }//commonHTML最外层;
    $(document).on('tap', '.brand-company', function(){
        var brand_id = $(this).data('brand_id');
        onAgentEvent('activity_detail','',{'type':'activity','id':brand_id,'userId':uid,'position':'1'});
        if(shareFlag){
            window.location.href = labUser.path + "webapp/agent/brand/detail?id=" + brand_id + "&agent_id=" + uid + "&pagetag=08-9&is_share=1";
         }else if(isiOS){
            pushToBrandDetail(brand_id);
         }else if(isAndroid){
            window.location.href = labUser.path + "webapp/agent/brand/detail?id=" + brand_id + "&agent_id=" + uid + "&pagetag=08-9&is_share=1";
         }
    });
    //联系客户报名
    function contactCustomer(activity_id,agent_id,title,imgUrl){
        if (isAndroid) {
            javascript: myObject.contactCustomer(activity_id,agent_id,title,imgUrl);
         }
        else if (isiOS) {
            var data = {
                'activity_id': activity_id,
                'agent_id':agent_id,
                'title':title,
                'imgUrl':imgUrl
            };
            window.webkit.messageHandlers.contactCustomer.postMessage(data);
        }
    }
    $('.fixedbottom').on('click',function(){
        var title=$(this).data('title'),
            imgUrl=$(this).data('imgurl');
        if($(this).text()=='邀请客户报名'){
          contactCustomer(activity_id, uid,title,imgUrl)  
         }
    
    })
});//zepto外层
