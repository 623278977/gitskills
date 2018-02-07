 $('html').addClass('bgColor');
 new FastClick(document.body);
 $(document).ready(function(){
  $('title').text('活动详情')  
 })  
var pageNow = 1,
    pageSize = 5;
//分享
var shareFlag = urlPath.indexOf('is_share=1') > 0 ? true : false;
function showShare(){
    var type = 'Activity',
     title = $('#act_name').text(),
     img = $('#share_img').data('src'),
     header = '活动',
     summary = cutString($('#act_container').attr('summary'), 18),
     content = '我在无界商圈发现了一个不错的活动，想邀请你一起参加！',
     begintime = $('#act_des').data('begintime'),
     citys = $('#citys').text(),
     actid=$('#act_name').data('act_id'),
     share_mark=$('#share_img').data('share_mark'),
     relation_id=$('#share_img').data('code');
    if(summary!=''){
    	content = summary;
    };
    var url = window.location.href+'&share_mark='+share_mark;
    var args = getQueryStringArgs(),
        activity_id = args['id']||'0';
    var p_url = labUser.api_path + '/index/code/_v020600';
        ajaxRequest({},p_url,function(data){
            if(data.status){
                var code=data.message;
                url+="&code="+code;
                if($('#share').data('reward')==0){
                    shareOut(title, url, img, header, content,begintime,citys,actid,'','','','share','activity',activity_id);
                }else if($('#share').data('reward')==1){
                    shareOut(title, url, img, header, content,begintime,citys,actid,type,share_mark,code,'share','activity',activity_id);
                }     
            }
        })
    
};
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

//刷新
function reload() {
    location.reload();
}
Zepto(function () {
    new FastClick(document.body);
    var urlPath = window.location.href,
        args = getQueryStringArgs(),
        activity_id = args['id'],
        uid = args['uid'] || '0',
        position_id = args['position_id'] || '0',
        maker_id = args['makerid'] || '0',
        origin_mark=args['share_mark']||'0',
        u_code=args['code']||'0',
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
    var shareFlag = urlPath.indexOf('is_share=1') > 0 ? true : false;
    var Tagflag = urlPath.indexOf('is_tag') > 0 ? true : false;
    var activityDetail = {
        detail: function(activity_id, uid) {
            var param = {};
            param["id"] = activity_id;
            param["uid"] = uid;
            var url = labUser.api_path + '/activity/detail/_v020700';
            ajaxRequest(param, url, function(data) {
                if (data.status) {
                	$('#act_container').attr('summary',data.message.share_summary);
                    activityDetail.bannerPic(data.message.banners);
                    activityData(data.message, shareFlag);
                }
            });
        },
        collect: function(obj) {
            var param = obj;
            var url = labUser.api_path + '/favorite/deal';
            ajaxRequest(param, url, function(data) {
                if (data.status) {
                    var act_id = $('#act_name').data('act_id');
                    if (param['type'] == '1') {
                        $('.collectbtn').text('取消收藏');
                        $('#storeNum').html($('#storeNum').text() - 1 + 2);
                    } else {
                        $('.collectbtn').text('收藏');
                        $('#storeNum').html($('#storeNum').text() - 1);
                    }
                }
            });
        },
        seenPlus: function(actid, type, col) {
            var param = {};
                param["id"] = actid;
                param["type"] = type;
                param["col"] = col;
            var url = labUser.api_path + '/activity/incre';
            ajaxRequest(param, url, function(data) {
                if (data.status) {
                    if (col == 'likes' && type == '1') {
                        $('#storeNum').html($('#storeNum').text() - 1 + 2);
                    } else if (col == 'likes' && type == '-1') {
                        $('#storeNum').html($('#storeNum').text() - 1);
                    }
                }
            });
        },
        activityPrise:function(){
                      var param={};
                          param['uid']=uid,
                          param['id']=activity_id,
                          param['relation']='activity',
                          url=labUser.api_path + '/userpraise/zan';
                       ajaxRequest(param, url, function(data){
                            var tag=$('.actzan').hasClass('yizan');
                                if(!tag){
                                    $('.actzan').addClass('yizan').removeClass('weizan');
                                }
                       });    
        },
        share:function(){
            if(shareFlag){
              activityDetail.activityPrise();  
            }
        },
        getImage:function(){
            $(document).on('click','.actzan',function(){
              hotChange();
              $('#zan-images').css('height','auto');
            })
        },
        bannerPic:function(obj){
            var swiper = new Swiper('.swiper-container', {
                pagination: '.swiper-pagination',
                paginationType: 'custom',
                //autoplay:'2000',
                observer:true,//修改swiper自己或子元素时，自动初始化swiper
                bserveParents:true,      
                paginationCustomRender: function(swiper, current, total) {
                    return '<span class="f16">' + current + '</span>' + ' / ' + total;
                }
            });
            $.each(obj,function(index, item) {
                var str='';
                    str+= '<div class="swiper-slide"><img src="' + item.src + '" alt="" /></div>';
                    $('.swiper-wrapper').append(str);
            });
            
        }
    };
    activityDetail.detail(activity_id, uid, position_id, maker_id);
    activityDetail.share();
    activityDetail.getImage();
    //活动信息
    function activityData(result, is_share) {
        var selfObj = result;
        commonHTML(selfObj, is_share);
        if(selfObj.distribution_id==0){
                $('#share').addClass('none');
                $('#share').data('reward',0);
            }else{
                $('#share').data('reward',1);
            }       
        //分享页
        if (is_share) {
            $('#loadAppBtn').removeClass('none');
            $('#installapp').removeClass('none');
            $('#share').addClass('none');
            var activity='actID'+activity_id;
            if($('#share').data('reward')==1&&(!localStorage.getItem(activity))){
                 getReward(origin_mark,'view',0,u_code);
                 setTimeout(reload(),1000);
                try {
                    localStorage.setItem(activity,activity_id);
                    return true;
                } catch (error) {
                    alert('隐私/无痕模式效果会不太好哦!请切换到正常模式');
                }
            }
            //结束未报名
            var timestamp = Math.round(new Date().getTime() / 1000);
            if (timestamp < selfObj.end_time) {
                $('#signnow').on('click', function () {
                    var sharemark=$('#share_img').data('share_mark');
                    var code=$('#share_img').data('code');
                    // getReward(share_mark,'enroll',0,activity_id);
                    if ($('#wjbNum').html()=='免费'&&result.ticket_id!=0){
                        window.location.href = labUser.path+'webapp/freecheck/detail/_v020800?id='+activity_id+'&ticket_id='+result.ticket_id+'&is_share=1&share_mark='+ origin_mark+'&code='+u_code;
                    }else{
                        window.location.href = labUser.path + 'webapp/ticket/actapply/_v020400?id=' + activity_id + '&is_share=1&share_mark='+origin_mark+'&code='+u_code;
                    }
                });
            }
            else{
                $('#signnow').html('已结束').css('background-color','#ccc');

            };
            if(is_share){
                $('.signup').on('click',function(){
                     if ($('#wjbNum').html()=='免费'){
                        window.location.href = labUser.path+'webapp/freecheck/detail/_v020400?id='+activity_id+'&ticket_id='+result.ticket_id+'&is_share=1&share_mark='+ origin_mark+'&code='+u_code;
                    }else{
                        window.location.href = labUser.path + 'webapp/ticket/actapply/_v020400?id=' + activity_id + '&is_share=1&share_mark='+origin_mark+'&code='+u_code;
                    }
                })
            }
            $(document).on('click', '.chakan', function () {
                var brand_id = $(this).data('brand_id');
                if(is_share){
                     window.location.href = labUser.path + "webapp/brand/detail/_v020700?id=" + brand_id + "&uid=" + uid + "&pagetag=08-9&is_share=1" 
                }else{
                if(isiOS){
                    pushToBrandDetail(brand_id);
                }else if(isAndroid){
                    window.location.href = labUser.path + "webapp/brand/detail/_v020700?id=" + brand_id + "&uid=" + uid + "&pagetag=08-9&is_share=1";
                } 
                }
               
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
        else {
            //结束未报名
            var timestamp = Math.round(new Date().getTime() / 1000);
            if (timestamp < selfObj.end_time) {
                //邀请
                if($('#share').hasClass('none')){
                   if (selfObj.is_shareable == '1') {
                        $('#yaoqing').removeClass('none');
                        var setout = setTimeout(function () {
                            $('#yaoqing').addClass('none');
                            clearTimeout(setout);
                        }, 8000);       
                    } 
                }
                //邀请
                $(document).on('tap', '#yaoqingbtn', function () {
                        var act_id = $('#act_name').data('act_id');
                        invites(act_id, maker_id);
                    });
                if(Tagflag){
                    $('.signup').attr('disabled',true).addClass('ccc')
                }else{
                    $(document).on('click', '.signup,.wjbrk', function () {
                        var ovoid = maker_id,
                            act_id = $('#act_name').data('act_id'),
                            act_name = $('#act_name').text(),
                            is_pay=$('#act_name').data('is_pay'),
                            share_mark=$('#share_img').data('share_mark'),
                            site_ticket_count=$('#act_name').data('site_ticket_count'),
                            surplus=$('.signup').attr('disabled'),
                            content=$('.signup').text();
                        if(surplus==true){ 
                        }else{
                           ActivityApply(act_id, ovoid, act_name, is_pay,'Activity',site_ticket_count);  
                        }
                        activityDetail.activityPrise() 
                    });
                }
            }
            $(document).on('click', '.chakan', function () {
                var brand_id = $(this).data('brand_id');
                window.location.href = labUser.path + "webapp/brand/detail/_v020700?id=" + brand_id + "&uid=" + uid + "&pagetag=08-9";
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
               getComment(commentParam,shareFlag);//获取更多评论
            });
            //赞-活动
            $('.actzan').on('click', function () {
                var param = {};
                param["activity_id"] = activity_id;
                param["uid"] = uid;
                var url = labUser.api_path + '/activity/zan/_v020400';
                ajaxRequest(param, url, function (data) {
                    if (data.status) {
                        hotChange();
                        $('.actzan').attr('disabled',true).removeClass('weizan').addClass('yizan');
                    }
                    
                });
            });
            $('#knowdetail').on('click', function() {
                window.location.href = labUser.path + 'webapp/protocol/moreshare/_v020700';
            })
            $('.getcoin').on('click', function() {
                if (shareFlag) {
                    tips('请至App查看');
                } else {
                    showShare();
                }
            });
            //时间
            $('#aty_time').on('click', function () {
                window.location.href = labUser.path + 'webapp/activity/time/_v020400?id=' + activity_id;
            });
            //地址
            $('#aty_hostcitys').on('click', function () {
                window.location.href = labUser.path + 'webapp/activity/address/_v020700?id=' + activity_id + '&maker_id=' + maker_id;
            });

            //评论
            $('.chat').on('click', function() {
                $('#commentback').removeClass('none');
                $('#comtextarea').focus();
                if ($('#comtextarea').val() == '') {
                    $('#subcomments').css('backgroundColor', '#999');
                }
            });
        }
        $('#act_container').removeClass('none');
    }

    //共同点
    function commonHTML(selfObj, is_share) {
        //是专版活动
        if (selfObj.is_vip=='1') {
            $('#act_name').html(selfObj.subject.substring(0,16)+'…');
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
            $('#act_name').html(selfObj.subject.substring(0,16)+'…')
            $('#zbicon,#zbcontainer').remove();
        }
        //获取相关信息
        $('#share_img').data('src', selfObj.share_image);  
        $('#share_img').data('share_mark',selfObj.share_mark);
        $('#share_img').data('code',selfObj.code);
        $('#act_name').data('act_id', selfObj.id);//活动id
        $('#act_name').data('is_pay',selfObj.site_need_pay);
        $('#act_name').data('site_ticket_count',selfObj.site_ticket_count);

        //数据库浏览量加1
        activityDetail.seenPlus(selfObj.id, '1', 'view');
        var begin_time = unix_to_datetime(selfObj.begin_time);//开始时间
        var bt = unix_to_fulltime(selfObj.begin_time);
        var newtime = Math.round(new Date().getTime() / 1000);
        $('#act_des').data('begintime', bt);
        $('#act_des').data('collected',selfObj.is_collect);
        var fenxiang=$('#act_des').data('collected');

        $('#act_time').html(begin_time);
        $('#livephoto').data('id',selfObj.live_id);
        $('#citys').html(selfObj.activity_location);
        if(selfObj.site_ticket_count==1){
          $('#wjbNum').html((selfObj.min_ticket_score == '0' ? '免费' : selfObj.min_ticket_score+'积分'));
          $('#tickettype').html(selfObj.min_ticket_price_type);  
        }else{
          $('#wjbNum').html((selfObj.min_ticket_score == '0' ? '0积分起' : selfObj.min_ticket_score+'积分起'));
          $('#tickettype').html(selfObj.min_ticket_price_type); 
        }
        if (newtime < selfObj.end_time){
            $('#bmNum').html('共' + selfObj.sign_count + '人已报名');
            if(selfObj.islive==1){
            $('#livephoto').removeClass('none');
            }
        }else{
            $('#bmNum').html('活动参与人数');
            $('.overNum').html('共<em class="ff5">'+selfObj.sign_count+'</em>人参与活动');
        }
        $('#actdescription').html(selfObj.content.replace(/http:/g,'https:'));
        //品牌
        if (selfObj.brand.length > 0) {
                var brandHtml = '';
            $.each(selfObj.brand, function (index, item) {
                var keywordhtml = '';
                    brandHtml += '<div class="addbackgd">'; //我加的
                    brandHtml += ' <div class="white-bg brand-company pl1-33 fline position2" >';
                    brandHtml += '<img src="' + item.logo + '" alt="" class="company mr1-33 fl">';
                    brandHtml += '<div class="fl width70"><em class="service f12 mr1">' + item.category_name + '</em>';
                    brandHtml += '<span class="f14 b">' + cutString(item.name, 10) + '</span> <div class="brand-desc f12 color999 mb05 ui-nowrap-multi">' + removeHTMLTag(item.details) + '</div>';
                    brandHtml += '<p class="f12 mb05"><span class="c8a">投资额：</span><span class="color-red">' + item.investment_min + '~' + item.investment_max + '万</span>';
                    brandHtml += '</p>';
                    if (item.keywords.length > 0) {
                        $.each(item.keywords, function(index, oneitem) {
                            keywordhtml += '<a class="tags-key border-8a-radius">' + oneitem + '</a>';
                        });
                        brandHtml += keywordhtml;
                    }
                    brandHtml += '</div><div class="clearfix"></div></div>';
                    if (item.is_collect == 1) {
                    brandHtml += '<div id="back" class="back none"><div data-brand_id="' + item.id + '" data-subscribe="do" id="shoucang" class="shoucang shou"></div><span class="shoucangson">已收藏</span>';
                    } else {
                    brandHtml += '<div id="back" class="back none"><div data-brand_id="' + item.id + '"  data-subscribe="undo" id="shoucang" class="shoucang cang"></div><span class="shoucangson">收藏</span>';
                    }
                    brandHtml += '<div id="tijiao" class="tijiao"></div><span id="tijiaoson">提交意向</span>';
                    brandHtml += '<div id="chakan" class="chakan" data-brand_id="' + item.id + '"></div><span id="chakanson">查看详情</span>';
                    brandHtml += '</div>';
                    brandHtml += '</div>'; //我加的
            });
            $('#pinpai').append(brandHtml);
            $('.addbackgd').on('click', function() {
                $(this).siblings().find('.back').addClass('none');
                $(this).find('.back').removeClass('none');
                $('.brandcontain').css('padding-left', '0');
            });
            $('.tijiao').on('click', function() {
                $('#brand-mes').removeClass('none').addClass('a-fadeinT');
                $('.fixed-bg').removeClass('none');
            });
            $('.fixed-bg').on('click', function() {
                $('#brand-mes').addClass('none');
                $(this).addClass('none');
            })
            function tips(e) {
                $('.tips').text(e).removeClass('none');
                setTimeout(function() {
                    $('.tips').addClass('none ');
                }, 1500);

            };
        }
        else {
            $('#pinpai').remove();
        }
        //热度
        $('#hotnum').html('热度&nbsp; <em class="ff5 " style="color:#ff5a00">'+selfObj.hot_count+'</em>');
        $('#seen').html(selfObj.view_count + '次');
        $('#dianzan').html(selfObj.zan_count + '次');
        $('#plun').html(selfObj.comment_count + '次');
        $('#zhuan').html(selfObj.share_count + '次');
        //分享页没有赞头像、评论
        if (is_share) {
            $('#zancontain,#comment').remove();
            $('.getMore').remove();

        }
        else {
            $('#zan-number').html(selfObj.zan_count);
            if(selfObj.zans.length<0){
                $('#zan-images').addClass('none')
            }else{
                $('#zan-images').removeClass('none')
            }
            $('#commentnum').html(selfObj.comment_count);
            zanImages(selfObj.zans);
        }

        fixedBtn(selfObj.can_buy, is_share, selfObj.end_time,selfObj.is_praise,selfObj.surplus);
        if(!shareFlag){
         setFavourite(selfObj.is_collect);   
          }    
    }
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
    //底部按钮
    function fixedBtn(can_buy, is_share, end_time,is_praise,surplus) {
        if (is_share) {
            $('.act_address .sj_icon').addClass('none');
             var timestamp = Math.round(new Date().getTime() / 1000);
            if (timestamp < end_time) {
                $('#loadAppBtn').show();
                var btnHtml = '<div class="fixed_btn none" id="notzbBtn">' ;
                    if(is_praise==1){
                        btnHtml+='<button class="actzan width20 yizan" disabled></button>';
                    }else{
                        btnHtml+='<button class="actzan width20 weizan" ></button>';
                    }
                    btnHtml+=  '<button class="chat width20"></button>' +
                    '<button  class="zhuan width20"></button>' ;
                    if(surplus>0){
                       btnHtml+='<button class="signup width40 cff4">立即报名<br/><span id="surplus" data-surplus=" '+surplus+' " style="color:#ffac00;font-size:1rem">还剩'+surplus+'个席位，赶快报名</span></button>';  
                    }else{
                       btnHtml+='<button class="signup width40 ccc" disabled>名额已满，无法报名</button>';
                    }
                     btnHtml+='</div>';   
                $('#act_container').append(btnHtml);  
                $('#video').attr('style','display:none');
                $('#baoming').addClass('baoming').removeClass('baomingover');       
            }
            else {
                $('#loadAppBtn').show();
                 var str='<span class="downsapp width40 f16 ccc r" id="signnow">活动已结束，报名截止</span>';
                 $('#loadAppBtn').append(str);
                var endBtn = '<div class="fixed_btn none" id="notzbBtn">' ;
                    if(is_praise==1){
                        endBtn+='<button class="actzan width30 yizan" disabled></button>';
                    }else{
                        endBtn+='<button class="actzan width30 weizan" ></button>';
                    };
                    endBtn+= '<button class="chat width30"></button>' +
                    '<button class="signup width40 ccc" disabled>活动已结束，报名截止</button>' +
                    '</div>';
                 $('#baoming').addClass('baomingover').removeClass('baoming');    
                $('#act_container').append(endBtn);
                
            }
        }
        else {
            var timestamp = Math.round(new Date().getTime() / 1000);
            if (timestamp < end_time) {
                var btnHtml = '<div class="fixed_btn" id="notzbBtn">' ;
                    if(is_praise==1){
                        btnHtml+='<button class="actzan width20 yizan" disabled></button>';
                    }else{
                        btnHtml+='<button class="actzan width20 weizan" ></button>';
                    }
                    btnHtml+=  '<button class="chat width20"></button>' +
                    '<button  class="zhuan width20"></button>' ;
                    if(surplus>0){
                       btnHtml+='<button class="signup width40 cff4">立即报名<br/><span id="surplus" data-surplus=" '+surplus+' " style="color:#ffac00;font-size:1rem">还剩'+surplus+'个席位，赶快报名</span></button>';  
                    }else{
                       btnHtml+='<button class="signup width40 ccc" disabled>名额已满，无法报名</button>';
                    }
                     btnHtml+='</div>';   
                $('#act_container').append(btnHtml);  
                $('#video').attr('style','display:none');
                $('#baoming').addClass('baoming').removeClass('baomingover');       
            }
            else {
                var endBtn = '<div class="fixed_btn " id="notzbBtn">' ;
                    if(is_praise==1){
                        endBtn+='<button class="actzan width30 yizan" disabled></button>';
                    }else{
                        endBtn+='<button class="actzan width30 weizan" ></button>';
                    };
                    endBtn+= '<button class="chat width30"></button>' +
                    '<button class="signup width40 ccc" disabled>活动已结束，报名截止</button>' +
                    '</div>';
                 $('#baoming').addClass('baomingover').removeClass('baoming');    
                $('#act_container').append(endBtn);
                
            }
        }
        $('.zhuan').on('click', function (){
                showShare();
                hotChange();
            });
    }
     function getComment(param,shareFlag){
        var params={};
            params['id']=param.id;
            params['uid']=param.uid;
            params['type']=param.commentType;
            params['page']=param.page;
            params['page_size']=param.page_size;
            params['section']=param.section;
        var url=labUser.api_path+'/comment/list';
        ajaxRequest(params,url,function(data){
            if(data.status){
                var comHtml='';
                var obj=data.message.data;
                $('#com_num').text(data.message.all_count); 
                $.each(obj,function(i,item){
                    comHtml+='<li><img src="'+item.avatar+'" alt="header" class="l"><div class="publisher r">';
                    comHtml+='<p class="f16 color666 b lh3-3 m0">'+item.c_nickname+'<span class="r time lh3-3">'+item.created_at+'</span></p>';
                    comHtml+='<p class="c8a f12">'+item.content+'</p></div><div class="clearfix"></div></li>';
                });
                if(param.page==1){
                  $("#allComment").html(comHtml);  
                }else{
                   $("#allComment").append(comHtml);  
                }
                if(data.message.all_count<=5){
                    $('.getMore').addClass('none');
                    $("#allComment").css('margin-bottom','0rem');
                }else{
                    if(obj.length < 5){
                        $('.getMore').text('没有更多了...').attr('disabled','true');
                    } else {
                        $('.getMore').removeClass('none').text('点击加载更多').removeAttr('disabled');
                        $("#allComment").css('margin-bottom','0');
                    }
                }
            }else{
                if($('#allComment>li').length==0){
                    $('#allComment').html('<p style="padding:1rem 0 2rem" class="c8a">暂无评论</p>').css('margin-bottom','0rem');
                    $('.com_num').remove();
                    $('.getMore').addClass('none');
                }else if($('#allComment>li').length>5){
                    $('.getMore').text('没有更多了...').attr('disabled','true');

                }
                
            }
        })
    };
    getComment(commentParam,shareFlag);
    function addComment(param,shareFlag){
        var params={};
            params['post_id']=param.id;
            params['uid']=param.uid;
            params['type']=param.commentType;
            params['content']=param.content;
        var url=labUser.api_path+'/comment/add';
        ajaxRequest(params,url,function(data){
            if(data.status){
                getComment(commentParam,shareFlag);
                $('#commentback').addClass('none');
                $('#comtextarea').val('');
                hotChange();
                $('#hotnum em').css('color','#ff5a00');
            }else{
                alertShow('请填写评论内容');
            }
        })

    };   
    //评论
        $(document).on('click','#subcomments',function(){
            commentParam.content=$('#comtextarea').val();
            console.log(commentParam.content);
            commentParam.page=1;
            addComment(commentParam,shareFlag);

        })
    //点击加载更多
        $(document).on('click','.getMore',function(){
            // var pageNow = 1,
            //     pageSize = 5;
            commentParam.page++;
            getComment(commentParam,shareFlag);
        })
    //热度值处理
    function hotChange(){
        var args = getQueryStringArgs(),
            activity_id = args['id'],
            uid = args['uid'] || '0';
        var param = {};
            param["id"] = activity_id;
            param["uid"] = uid;
        var url = labUser.api_path + '/activity/detail/_v020700';
        ajaxRequest(param, url, function (data) {
            if (data.status) {
                var resObj = data.message;
                $('#hotnum em').html(resObj.hot_count);
                $('#seen').html(resObj.view_count + '次');
                $('#dianzan').html(resObj.zan_count + '次');
                $('#plun').html(resObj.comment_count + '次');
                $('#zhuan').html(resObj.share_count + '次');
                $('#zan-number').html(resObj.zan_count);
                $('#commentnum').html(resObj.comment_count);
                zanImages(resObj.zans);
            }
        });
    };
    // 浏览量自增
    function viewAdd(uid, relation, relation_id) {
       var param = {};
       param["relation_id"] = relation_id;
       param["uid"] = uid;
       param["relation"] = relation;
       var url = labUser.api_path + '/user/add-browse/_v020400';
       ajaxRequest(param, url, function (data) {
           if (data.status) {
           }
       })
    };
    //二次分享先记录后奖励
    function sencondShare(type){
        var getcodeurl = labUser.api_path + '/index/code/_v020700';
        ajaxRequest({}, getcodeurl, function (data) {
            var newcode = data.message;//code
            var logsurl = labUser.api_path + "/share/share/_v020700";
            ajaxRequest({
                uid: '0',
                content: 'activity',
                content_id: activity_id,
                source: 'weixin',
                code:newcode,
                share_mark: origin_mark
            }, logsurl, function (data) {
                getReward(origin_mark, type, 0, newcode);
            });
        });

    };
    var fenxiang=$('#act_des').data('collected'); 
    console.log(fenxiang);
    setFavourite(fenxiang);

});

function onEvent(action,str,obj) {
    if (isAndroid) {
        javascript:jsUmsAgent.onEvent(action,str,obj);
    } else if (isiOS) {
        var data = {
            'eventId':action,
            'id':obj.id,
            'type':obj.type
        };
        window.webkit.messageHandlers.onEvent.postMessage(data);
    }
}
function changestyle(){
    $('#hotness,.act_intro,#zancontain,#comment,.brandtext,#actdescription').on('click',function(){
        $('.back').addClass('none');
    })
}
 changestyle();