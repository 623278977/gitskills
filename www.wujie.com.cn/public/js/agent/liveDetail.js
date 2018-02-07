
    new FastClick(document.body);
    var pageNow = 1,
        pageSize = 10,
        st_out = null,
        videoPlayer = null, //直播预告
        livePlayer = null, //直播预告
        watchTime = 600000, //观看直播10分钟
        is_fx = false, //是否分销
        args = getQueryStringArgs(),
        id = args['id'] || '0',
        uid = args['agent_id'] || '0',
        // origin_mark = args['share_mark'] || 0, //分销参数，分享页用
        // origin_code = args['code'] || 0,
        // share_mark = null,
        // code = null, //分销参数，APP内转发用
        videoExist = false, //直播结束页，是否有录播
        isbindActivity = false,
        activityImage = '',
        live_state = 'future',
        dataMessage = null;

    new FastClick(document.body);
    var shareFlag = (window.location.href).indexOf('is_share') > 0 ? true : false;
  
    var param = {
            "id": id,
            "uid": uid,
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
            "fecthSize": 0,
            "mid": "0"
        },
        Live = {
            detail: function(parame, shareFlag) {
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
                var url = labUser.agent_path + '/live/detail/_v010000';
                ajaxRequest(params, url, function(data) {
                    if (data.status) {
                        dataMessage=data.message;
                        var objVideos = data.message.videos || null,
                            liveNews = data.message.news || null,
                            // objLive = data.message.live,
                            objLive = data.message,
                            objActivity = data.message.activity || {};//,
                            live_state = objLive.situation;
                            var score_price=data.message.score_price;
                            var is_purchase=objLive.is_purchase;
                            $('#share_img').data('sharecontent',data.message.share_summary);
                        // share_mark = data.message.share_mark;
                        objLive["id"] = id;
                        // watchTime = (objLive.watch_reward_long > 0) ? (objLive.watch_reward_long * 60000) : 0;
                        if (live_state == 'future') {
                            //预告
                            livePreview(objLive, objActivity, shareFlag);
                   
                        } else if (live_state == 'is_living') {
                            //直播中
                            //请求互动列表
                            living(objLive, objActivity, shareFlag);
                            //评论列表
                            // Comment.getCommentList(param, 'reload', 'live');
                        } else if (live_state == 'past') {
                            //直播结束
                            liveEnd(objLive, objActivity, shareFlag, objVideos, liveNews);
                            if (videoExist) {
                                $("#livevideo_block").click();
                            }
                            if (isbindActivity) {
                                console.log(activityImage);
                                $('#share_video').html('');
                                $('#share_video').append('<img src="' + activityImage + '" height="100%">');
                            } else {
                                if (!shareFlag) {
                                    $('#share_video').hide();
                                    $('#live_detail').css('paddingTop', '4.5rem');
                                    $('.navbar').css('top', '0');
                                }
                            }
                        }
                        $('#containerBox').css('visibility', 'visible');
                        // increaseViewn(id, 'live');
                    }
                });
            }   

        };
        if (shareFlag) {
            param.uid = '0';
            uid = 0;
        }
        if (is_weixin()) {
            param.platform = 'weixin';
        } else {
            if (isiOS) {
                param.platform = 'ios';
            } else if (isAndroid) {
                param.platform = 'android';
            } else {
                param.platform = 'other';
            }
        }

    //详情
    Live.detail(param, shareFlag);

    //type:预览preview，直播中living，liveend直播结束
    //直播预告
    function livePreview(objLive, objActivity, shareFlag, code) {
        // $("#nav_every_say").removeClass('none');
        // $("#comment").addClass('none');
        createNavBar(objLive, 'preview', shareFlag);
        addTips(objLive, 'preview', shareFlag);
        liveCommonHtml(objLive, objActivity, shareFlag);
        if (shareFlag) {
            adjustSharePage('preview', objActivity, objLive, code);
        } else {
            adjustInnerPage('preview',objLive);
            bottomBtn(objLive, 'preview');
            bottomBtnEvent(objLive, objActivity, uid, 'preview');
        }
        //有预告片
        if (objLive.foreshow_url) {
        //直播预览的HTML
        createPreviewHtml();
        //直播预览事件
        previewEvent(objLive.foreshow_url, 'preview');
        }
    }

    //直播中
    function living(objLive, objActivity, shareFlag, code) {
        addTips(objLive, 'living', shareFlag);
        liveCommonHtml(objLive, objActivity, shareFlag);
        livingHtml(objLive, shareFlag);
        if (shareFlag) {
            adjustSharePage('living', objActivity, objLive, code);
        } else {
            bottomBtn(objLive, 'living');
            bottomBtnEvent(objLive, objActivity, uid, 'living');
            //APP内部----此处页面元素已经添加完成，调整位置
            //含视频播放逻辑
            adjustInnerPage('living', objLive);
        }
    }
    //直播结束
    function liveEnd(objLive, objActivity, shareFlag, objVideos, liveNews, code) {
        
        addTips(objLive, 'liveend', shareFlag);
        //直播概况
        liveCommonHtml(objLive, objActivity, shareFlag);
        //直播回放、资讯
        liveend(objLive, objVideos, liveNews, shareFlag);
        if (shareFlag) {
            adjustSharePage('liveend', objActivity, objLive, code);
        } else {
            adjustInnerPage('liveend', objLive);
        }
    }

    //直播概况
    function liveCommonHtml(objLive, objActivity, shareFlag) {
        var objLive = objLive,
            objActivity = objActivity;
        // if (objLive.with_activity == '1') {
        //     // bindActivity(objActivity);
        //     isbindActivity = true;
        //     activityImage = objActivity.detail_img;
        // }
        // if (objLive.is_brand_live == '1') {
        //     bindBrand(objLive);
        // }
        // if (objLive.with_guest == '1') {
        //     bindGuests(objLive);
        // }
        if (objLive.brands) {
            bindBrand(objLive);
        }
        //分享用的图片
        $('#share_img').data('src', objLive.live_img);
        //直播介绍   
       $('#basic_liveimg').attr('src',objLive.live_img); 
       $('#basic_liveinfo').html('<p class="f14 mb02 b h5-2 color333">'+objLive.title+'</p><p class="f12 color999">直播时间：'+unix_to_mdhm(objLive.begin_time)+'</p>');
        $('#live_info').html(objLive.detail);
        $('#livesubject').html(objLive.title);
       
    }

    //直播中---立即加盟和互动栏目
    function livingHtml(objLive, shareFlag) {
        //在线人数
        // onlineuserpic(objLive, shareFlag);
        //创建navbar
        createNavBar(objLive, 'living', shareFlag);
        //创建互动栏评论按钮
        // createCommentBtn(shareFlag);
        //分享页时创建评论框HTML
        // createCommentDiv(shareFlag);
        //注册事件
        navBarEvent('living', shareFlag);
        // commentBtnEvent(shareFlag);
        //加载下一页评论、最新评论
        // commentsEvent();
        //购买商品
        // buyGoodsEvent(shareFlag);
        //有预告片
        //if (objLive.live_url) {
        //直播预览的HTML
        //createPreviewHtml();
        //直播预览事件
        //previewEvent(objLive.live_url, 'living');
        //}
    }
   //预告评论框
   
    //直播结束
    function liveend(objLive, objVideos, liveNews, shareFlag) {
        liveVideoHtml(objVideos, liveNews);
        //创建navbar
        createNavBar(objLive, 'liveend', shareFlag);
        //直播回放和资讯详情页
        liveEndEvent(shareFlag);
        //注册navbar事件
        navBarEvent('liveend', shareFlag);
    }

    //直播回放和资讯HTML
    function liveVideoHtml(objVideos, liveNews) {
        var videoHtml = '',
            messageHtml = '';
        if (objVideos && objVideos.length > 0) {
            $.each(objVideos, function(index, item) {
                var keywordhtml = '';
                videoHtml += '<li class="ui-border-t livevideo" data-livevideo_id="' + item.id + '">';
                videoHtml += '<div class="l video_img">';
                if (item.duration != '00:00') {
                    videoHtml += '<p class="f14 videotime ui-border-radius"><span class="whitepoint"></span>' + item.duration + '</p>';
                }
                videoHtml += '<img src="' + item.video_image + '" alt="">';
                videoHtml += '</div>';
                videoHtml += '<div class="video_intro">';
                videoHtml += '<p class="f16 mb02 text_black">' + item.subject + '</p>';
                videoHtml += '<p class="f14 color999">录制于<span>' + unix_to_yeardate(item.created_at) + '</span></p>';
                if (item.keywords && item.keywords.length > 0) {
                    videoHtml += '<div class="f12 keywords">';
                    $.each(item.keywords, function(index, oneitem) {
                        keywordhtml += '<span >' + oneitem + '</span>';
                    });
                    videoHtml += keywordhtml;
                    videoHtml += '</div>'
                }
                videoHtml += '</div>'
                videoHtml += '<div class="clearfix"></div>';
                videoHtml += '</li>';
            });
            $('#relativevideo').html(videoHtml);
            videoExist = true; //全局字段
        }
        if (liveNews && liveNews.length > 0) {
            $.each(liveNews, function(index, item) {
                messageHtml += '<li class="ui-border-t livemessage" data-message_id="' + item.id + '">';
                messageHtml += '<div class="video_img r">';
                messageHtml += '<img src="' + item.logo + '" alt="">';
                messageHtml += '</div>';
                messageHtml += '<div class="video_intro">';
                messageHtml += '<p class="f16 mb02 text_black">' + item.title + '</p>';
                messageHtml += '<div class="f14 color999 mscontent">' + cutString(item.detail, 30) + '</div>';
                messageHtml += '<div class="splitline ui-border-t"></div>';
                messageHtml += '<div class="c8a author">作者：' + item.author + '</div>';
                messageHtml += '</div>';
                messageHtml += '<div class="clearfix"></div>';
                messageHtml += '</li>';
            });
            $('#messagecont').removeClass('none');
            $('#relativemessage').html(messageHtml);
        }
       
    }

    //创建navbar
    function createNavBar(objLive, type, shareFlag) {
        var objLive = objLive,
            // show_type = objLive.show_type,
            navHtml = '';
        if (type == 'living' || type == 'preview') {
            $('.share_need').hide();
            $('#brand-mes').css('top','27.7875rem');
            // if (objLive.with_brand == '1') {
            //     // 绑定品牌，增加【立即加盟】一栏
            //     if (shareFlag) {
            //         navHtml += '<div class="navbar color999 top26-7875">';
            //     } else {
            //         navHtml += '<div class="navbar color999 top23-2875">';
            //     }
            //     navHtml += '   <span class="threecol live-orange" id="detail_block">直播概况</span>\
            //                    <i class="left33">|</i>\
            //                    <span class="threecol" id="addin_block">立即加盟</span>\
            //                    <i class="left66">|</i>\
            //                    <span class="threecol" id="comment_block">互动</span>\
            //                </div>';
            // } else {
                //【直播概况】栏和【互动】栏
                if (shareFlag) {
                    navHtml = '<div class="navbar color999 top26-7875 tc">';
                } else {
                    navHtml = '<div class="navbar color999 top23-2875 tc">';
                }
                navHtml += '<span id="detail_block" class="c2873">详情</span></div>';
            // }
        } else if (type == 'liveend') {
            if (shareFlag) {
                navHtml = '<div class="navbar color999 top26-7875 tc">';
            } else {
                navHtml = '<div class="navbar color999 top23-2875 tc">';
            }
            navHtml += '<span class="twocol c2873" id="detail_block">直播概况</span>\
                        <i class="left50">|</i>\
                        <span class="twocol " id="livevideo_block">直播回放</span>\
                       </div>';
        }
        $('#containerBox').append(navHtml);
    }

    //navbar事件
    function navBarEvent(type, shareFlag) {
        if (type == 'living') {
            //直播中
            $('.live_yugao').addClass('none');
            if (shareFlag) {
                //分享页面
                // directclick();
                $("#comment").hide();
                $('#bind_activity').css('border-top','1rem solid#f2f2f2');
                $("#detail_block").click(function() {   
                    $(this).addClass('live-orange').siblings().removeClass('live-orange');
                    $("#barnd_list").hide(); //立即加盟栏内容
                    // $("#comment").hide(); //互动栏内容
                    // $("#comment_btn").hide(); //底部评论按钮
                    $("#loadAppBtn").show(); //下载APP按钮
                    $("#live_introduce").show(); //直播概况
                });
               
            } else {
                //app内部
                /**直播概况**/
                $("#comment").hide();
            }

        } else if (type == 'liveend') {
             // directclick();
            $('.refreshpic1').addClass('none');
             // fenxiao();
            /**直播概况**/
            $("#detail_block").click(function() {
                $(this).addClass('c2873').siblings().removeClass('c2873');
                $("#live_video").hide(); //直播回放
                $("#live_introduce").show();
                // $('#distribution').show();

            });
            //直播回放
            $("#livevideo_block").click(function() {
                $(this).addClass('c2873').siblings().removeClass('c2873');
                $("#live_video").show();
                $('#distribution').hide();
                $("#live_introduce").hide();
            });
        }
    }





    //直播中--创建互动评论按钮  
    //预告中的创建互动评论按钮  
    //请求点赞总数
    //直播中--创建分享页的评论框
    //评论按钮(发表图片文字)、打赏事件

    function FreshTime(){
            var attr_e = $('.dark_white_').attr('data-time');
            var startTime = new Date().getTime();
            var endTime = parseInt(attr_e*1000);
            var differTime = endTime - startTime; //两时间差
            var d = parseInt(differTime/(24*60*60*1000));
            var h = parseInt(differTime/(60*60*1000)%24);
            var m = parseInt(differTime/(60*1000)%60);
            var s = parseInt(differTime/1000%60);
            if(d<10){
                d="0"+d;
            }
            if(h<10){
                h="0"+h;
            }
            if(m<10){
                m="0"+m;
            }
            if(s<10){
                s="0"+s;
            }
            if( differTime>0){
            var t_html ='<span style="color:#ffac00;font-size:2.5rem">'+d+'</span>'+'<span style="color:#767676">天</span>'+'<span style="color:#ffac00;font-size:2.5rem;padding-left:1rem;">'+h+'</span>'+'<span style="color:#767676">时</span>'+'<span style="color:#ffac00;font-size:2.5rem;padding-left:1rem;">'+m+'</span>'+'<span style="color:#767676">分</span>'+'<span style="color:#ffac00;font-size:2.5rem;padding-left:1rem;">'+s+'</span>'+'<span style="color:#767676">秒</span>';
            $("#time_dao").html(t_html);
        }else{
            var str='<span style="color:#ffac00;font-size:2.5rem">直播已开始</span>';
            $("#time_dao").html(str);  
        }
    }

        FreshTime();
        var timer = setInterval(FreshTime, 1000);

   //分销数据获取
    
   //获取点赞数据
    // function getzannumber(){
    //        // alert(dataMessage.count_zan);
    //     if(dataMessage){
    //             //dataMessage = data.message;
    //             var count_zan=dataMessage.count_zan;
    //             var is_zan=dataMessage.is_zan;
             
    //             if(is_zan==1){
    //                 $('#_zan_').addClass('dian_zan_');
    //             }else{
    //                 $('#_zan_').addClass('wei_zan_');
    //             }

    //             var score_price=dataMessage.score_price;
    //             $('#dian_zan_number').text(count_zan);
         
    //             $('.fee').html(score_price);
    //     }
       
    // }
   // getzannumber(id,uid);
    //提示框文字[并非播放视频的容器]
    function addTips(objLive, type, shareFlag) {
        var objLive = objLive,
            ticket_price = objLive.ticket,
            distribution_id=objLive.distribution_id
        if (type == 'preview') {        
                $('#share_video').append('<p class="dark_white_color">本次直播将于</span> ' + unix_to_mdhm(objLive.begin_time) + ' 开始</p><p class="dao_ji_shi">倒计时</p>');
                $('#share_video').append('<p class="dark_white_" data-time="'+objLive.begin_time+'"><span id="time_dao"></span></p>');
                $('#share_video').append('<p class="tc"><button class="shareTocus">分享给客户</button></p>');
            if (!shareFlag) {
                //APP内,如果有预告
                if (objLive.foreshow_url) {
                   $('#share_video').append('<div class="live_preview white"><span class="showvideo mr1 ui-border-radius">观看预告片</span><span class="preview_time">' + objLive.foreshow_duration + '</span></div>');
                }
              
            }
        } else if (type == 'living') {         
            //如果有预告
            if (objLive.live_url) {
               $('#share_video').append('<div class="live_preview white"><span class="showvideo mr1 ui-border-radius">观看预告片</span><span class="preview_time">05:00</span></div>');
            }       
        } else if (type == 'liveend') {
            $('#share_video').html('<div class="share_text"><p class="liveendpic"></p><p class="white">来晚了一步，本次直播已经结束啦！</p><p class="dark_yellow">观看本次直播回放，不错过精彩每一秒！</p></div>');
        }
    }

    //分销相关事件
    // function fx() {
    //     //关闭分享机制提醒
    //     $(document).on('tap', '.close_share', function() {
    //         $('.fx_share1').addClass('none');
    //     });
    //     //了解更多分享机制
    //     $(document).on('tap', '.understand', function() {
    //         window.location.href = labUser.path + 'webapp/protocol/moreshare/_v020500?pagetag=025-4';
    //     })
    // }

    //创建直播预览HTML元素
    function createPreviewHtml() {
        var html = '<div class="preview_cover none" id="preview_cover">\
            <div class="videobox" id="preview_box"></div>\
            <div class="close f14">×</div>\
            </div>';
        $('#containerBox').append(html);
    }

    //直播预览事件
    function previewEvent(live_url, type) {
        var previewid = null;
        console.log(live_url);
        if (type == 'preview') {
            //直播预告(是个点播视频)
            $(document).on('click', '.live_preview', function() {
                $('#preview_cover').removeClass('none');
                getVod(live_url);
            });
        } else if (type == 'living') {
            //直播预告(直播链接)
            $(document).on('click', '.live_preview', function() {
                $('#preview_cover').removeClass('none');
                getLivePreview(live_url);
                previewid = setTimeout(function() {
                    $('#preview_cover').addClass('none');
                    $('#preview_box').empty();
                    livePlayer = null;
                    videoPlayer = null;
                    clearTimeout(previewid);
                }, watchTime);
            });
        }
        $(document).on('click', '.preview_cover .close', function() {
            $('#preview_cover').addClass('none');
            $('#preview_box').empty();
            videoPlayer = null;
            livePlayer = null;
            clearTimeout(previewid);
        });
    }

    //绑定活动
    // function bindActivity(objActivity) {
    //     var objActivity = objActivity,
    //         actHtml = '',
    //         keywords = '';
    //     actHtml += '<div class="bgrightjt activity" data-activity_id="' + objActivity.id + '">';
    //     actHtml += '<p class="f16 mb05 b text-ellipsis">' + objActivity.subject + '</p>';
    //     actHtml += '<p class="color999 mb0">活动开始时间：' + unix_to_datetime(objActivity.begin_time) + '</p>';
    //     if (objActivity.keywords && objActivity.keywords.length > 0) {
    //         actHtml += '<p class="color999 mb1">活动场地：' + objActivity.city.split('@').join('、') + '</p>';
    //         actHtml += '<p class="keyword f12 mb05">';
    //         $.each(objActivity.keywords, function(index, item) {
    //             keywords += '<span class="border-8a-radius">' + item + '</span>';
    //         });
    //         actHtml += keywords;
    //         actHtml += '</p>';
    //     } else {
    //         actHtml += '<p class="color999 mb0">活动场地：' + objActivity.city.split('@').join('、') + '</p>';
    //     }
    //     actHtml += '</div>';
    //     $('#bind_activity').append(actHtml);
    //     $('#bind_activity').removeClass('none');
    // }

    //绑定品牌,商品
   function bindBrand(objLive) {
        var brandHtml = '',
            objLive = objLive;
        if(objLive.brands && objLive.brands.length >0){
            $.each(objLive.brands, function(index, item) {
                var keywordhtml = '';
                brandHtml += '<div class="brand-detail ui-border-t psrelative" data-brand_id="' + item.id + '">';
                brandHtml += '<img src="' + item.logo + '" alt="" class="fl brandlogo">';
                brandHtml += '<div class="fr width100">';
                brandHtml += '<div style="margin-left:10.73rem;">';
                brandHtml += '<div><em class="service f12 mr1">' + item.category_name + '</em><span class="f14 text_black b">' + item.title + '</span></div>';
                brandHtml += '<div class="f12 color999 mb05 mt05 ui-nowrap-multi">' + removeHTMLTag(item.brand_summary) + '</div>';
                brandHtml += '<p class="f12 mb1"><span class="color666">投资额：</span><span class="colorfe">' + item.investment_min + ' ~ ' + item.investment_max + '万</span></p>';
                brandHtml += '<div class="f12 keywords">';
                if (item.keywords.length > 0) {
                    $.each(item.keywords, function(index, oneitem) {
                        keywordhtml += '<span >' + oneitem + '</span>';
                    });
                    brandHtml += keywordhtml;
                }
                brandHtml += '</div>';
                brandHtml += '</div>';
                brandHtml += '</div>';
                brandHtml += '<div class="clearfix"></div>';
                brandHtml += '</div>';
            });
            $('#bind_brand').append(brandHtml);
            $('#bind_brand').removeClass('none');
        }
        
        
        if (objLive.goods && objLive.goods.length > 0) {
            //goods-list
            var goodsHtml = '';
            $.each(objLive.goods, function(index, item) {
                var keyword = '';
                goodsHtml += '<section class="brandcontain">';
                goodsHtml += '<div class="brandtext f14"><span class="brand_text">' + item.goods_title + '</span><span class="fr f12 lht2 color666">商品代号：' + item.code + '</span></div>';
                goodsHtml += '<div class="brand-detail ui-border-t">';
                goodsHtml += '<img src="' + item.logo + '" alt="" class="fl brandlogo">';
                goodsHtml += '<div class="fl width100">';
                goodsHtml += '<div style="margin-left:10.73rem;">';
                goodsHtml += '<div><em class="service f12 mr1">' + item.category_name + '</em><span class="f14 text_black b">' + item.name + '</span></div>';
                goodsHtml += '<div class="f12 color999 mb05 mt05 ui-nowrap-multi">' + removeHTMLTag(item.summary) + '</div>';
                goodsHtml += '<p class="f12 mb1"><span class="color666">投资额：</span><span class="colorfe">' + item.investment_min + ' ~ ' + item.investment_max + '万</span></p>';
                goodsHtml += '<div class="f12 keywords">';
                if (item.keywords.length > 0) {
                    $.each(item.keywords, function(index, oneitem) {
                        keyword += '<span >' + oneitem + '</span>';
                    });
                    goodsHtml += keyword;
                }
                goodsHtml += '</div>';
                goodsHtml += '</div>';
                goodsHtml += '</div>';
                goodsHtml += '<div class="clearfix"></div>';
                goodsHtml += '</div>';
                goodsHtml += '<div class="brandtext f12 ui-border-t">';
                if (item.status == 'allow') {
                    if (item.num == '0') {
                        goodsHtml += '<div class="infocon"><span class="color999">如有意向，可以直接下单提交预付金</span><span class="fr buybutton tc cannotbuy">已售完</span><span class="pricecolor f14 fr mr2 b">￥' + item.price + '</span></div>';
                        goodsHtml += '<div class="color999"><span>加盟拓展，先行先得！</span><span class="fr mt05">很抱歉，已全部购完</span></div>';
                    } else if (item.num >= 1) {
                        goodsHtml += '<div class="infocon"><span class="color999">如有意向，可以直接下单提交预付金</span><span class="fr buybutton tc canbuy" data-goodsid="' + item.id + '">立即购买</span><span class="pricecolor f14 fr mr2 b">￥' + item.price + '</span></div>';
                        goodsHtml += '<div class="color999"><span>加盟拓展，先行先得！</span><span class="fr mt05">数量有限，还剩' + item.num + '份</span></div>';
                    }
                } else if (item.status == 'pause') {
                    goodsHtml += '<div class="infocon"><span class="color999">如有意向，可以直接下单提交预付金</span><span class="fr buybutton tc cannotbuy">暂未开启</span><span class="pricecolor f14 fr mr2 b">￥' + item.price + '</span></div>';
                    goodsHtml += '<div class="color999"><span>加盟拓展，先行先得！</span><span class="fr mt05">数量有限，还剩' + item.num + '份</span></div>';
                }
                goodsHtml += '<div class="clearfix"></div>';
                goodsHtml += '</div>';
                goodsHtml += '</section>';
            });
            $('#barnd_list').html(goodsHtml);
        }
    }

    //绑定嘉宾
    // function bindGuests(objLive) {
    //     var objLive = objLive,
    //         guestsHtml = '';
    //     if (objLive.guests && objLive.guests.length > 0) {
    //         $.each(objLive.guests, function(index, item) {
    //             guestsHtml += '<div class="pt1-5 pb1-5">';
    //             guestsHtml += '<img class="guest_img" src="' + item.image + '" alt="">';
    //             guestsHtml += '<p class="guest_name f14">' + item.name + '</p>';
    //             guestsHtml += '<p class="guest_intro f12 c8a">' + item.brief + '</p>';
    //             guestsHtml += '</div>';
    //         });
    //         $('#bind_guest').append(guestsHtml);
    //     }
    //     $('#bind_guest').removeClass('none');
    // }
    //APP内，底部按钮
    function bottomBtn(objLive, type) {
        var bottomBtn = '';
        if (type == 'preview') { 
            if (objLive.ticket > 0) {
                if (objLive.is_purchase == '1') {
                    //已购买
                    // if (objLive.subscribe == '1') {
                    //     //已订阅
                    //     dhtml='<div class="fix_btn_meet border-8a-radius border2">\
                    //      <button class="subscribe l border-8a-radius" data-subscribe="1">取消订阅</button>\
                    //      </div>';
                    //      $('#share_video').append(dhtml);
                    // } else {
                    //       dhtml='<div class="fix_btn_meet border-8a-radius border2">\
                    //      <button class="subscribe l border-8a-radius" data-subscribe="0">+订阅</button>\
                    //      </div>';
                    //      $('#share_video').append(dhtml);
                    // }
                } else {
                    //未购买
                    // if (objLive.subscribe == '1') {
                    //     //已订阅
                    //      bottomBt = '<div class="fixed_btn_meet border1">\
                    //   <button class="buy_btn l ">购买</button>\
                    //     </div>';
                    //      dhtml='<div class="fix_btn_meet border-8a-radius border2">\
                    //      <button class="subscribe l border-8a-radius" data-subscribe="1">取消订阅</button>\
                    //      </div>';
                    //     $('.share_need').append(bottomBt);
                    //     $('#share_video').append(dhtml);
                    // } else {
                    //     bottomBt = '<div class="fixed_btn_meet  border1">\
                    //   <button class="buy_btn l ">购买</button>\
                    //     </div>';
                    //     dhtml='<div class="fix_btn_meet border-8a-radius border2">\
                    //      <button class="subscribe l border-8a-radius" data-subscribe="0">+订阅</button>\
                    //      </div>';
                    //     $('.share_need').append(bottomBt);
                    //     $('#share_video').append(dhtml);
                    // }

                }
            } else {
                // if (objLive.subscribe == '1') {
                //     //已订阅
                //     bottomBt = '<div class="fixed_btn_meet border1">\
                //       <button class="buy_btn l ">购买</button>\
                //         </div>';
                //     dhtml='<div class="fix_btn_meet border-8a-radius border2">\
                //          <button class="subscribe l border-8a-radius" data-subscribe="1">取消订阅</button>\
                //          </div>';
                //     $('.share_need').append(bottomBt);
                //     $('#share_video').append(dhtml);
                //     $('#share_need').addClass('none');
                //     $('#nav_every_say').css('margin-top','1.5rem');

                // } else {
                //      bottomBt = '<div class="fixed_btn_meet  border1">\
                //       <button class="buy_btn l ">购买</button>\
                //         </div>';
                //     dhtml='<div class="fix_btn_meet border-8a-radius border2">\
                //          <button class="subscribe l border-8a-radius" data-subscribe="0">+订阅</button>\
                //          </div>';
                //      $('.share_need').append(bottomBt);
                //      $('#share_video').append(dhtml);
                //      $('#share_need').addClass('none');
                // }
            }
        } else if (type == 'living') {
            if (objLive.ticket > 0) {
                if (objLive.is_purchase == '0') {
                    bottomBtn = '<div class="fixed_btn" id="buyonline" data-isshow="1">\
                        <button class="buy_btn l width100">购买本场直播</button>\
                        </div>';
                }
            }
        };
       
    
        $("#containerBox").append(bottomBtn);
        //购买直播
     
    }

    //APP内，底部按钮事件
    function bottomBtnEvent(objLive, objActivity, uid, type) {
        var live_id = objLive.id,
            activity_id = objActivity.id || null,
            ticket_id = objLive.ticket_id,
            price = objLive.ticket;
        if (type == 'preview') {
            if (uid == '0') { //未登录
                $(document).on("click", "button.subscribe", function() {
                    showLogin();
                });
            } else {
                /**订阅直播、取消订阅**/
                $(document).on("click", "button.subscribe", function() {
                    var args = getQueryStringArgs();
                    var id = args['id'] || 0;   
                    var subscribe = $(this).data("subscribe");
                    if (subscribe == '0') {
                        subscribe = '1';
                    } else {
                        subscribe = '0';
                    }
                    Live.order(id, subscribe);
                });
                // 底部边框点赞
               
                //评论点站
             
            }
        } else if (type == 'living') {
           
        }
    }

    //APP内部调整元素元素位置
    function adjustInnerPage(type, objLive) {
        if(type == 'preview'){
            $('#live_detail').addClass('pt27-7875');
        }else if (type == 'living') {
            $('#distribution').hide();
            var canPlay = playLiveVideo(objLive, false); //能否播放直播
            if (canPlay) {
                $('#share_video').empty();
                var v_height = $('#share_video').height();
                //切换到互动
                // $("#comment_block").click();
                //调用移动端播放器
                // showPlayer(objLive.live_url, v_height, objLive.subject, id, objLive.share_reward_unit, objLive.share_reward_num);
                showPlayer(objLive.live_url, v_height, objLive.title, id);
            }
            $('#live_detail').addClass('pt27-7875');
        } else if (type == 'liveend') {
            $('#live_detail').addClass('pt27-7875');
        }
    }

    //分享页，增加打开APP和下载APP功能、调整元素位置,微信二次分享功能
    //type='living'时含播放视频逻辑
    function adjustSharePage(type, objActivity, objLive, code) {
        // 打开app
        
        var openApp = '<div class="install " id="openAppBtn">\
                            <p class="l">打开无界商圈APP，观看完整高清直播 &gt;&gt;</p>\
                            <span class="r" id="openapp"><img src="/images/install_btn.png" alt=""></span>\
                            <div class="clearfix"></div>\
                            </div>';
        $("#containerBox").append(openApp);
        //调整位置
        if (type == 'preview') {
            creatLoadApp(false); //底部2个按钮['设置直播提醒','下载APP']
            //设置直播提醒
            remindLive(objLive);
            remindEvent();
            $('#share_video').addClass('top3-5');
            $("#live_detail").addClass('pt31-2875');
        } else if (type == 'living') {
            creatLoadApp(true); //底部1个按钮['下载APP']
            var canPlay = playLiveVideo(objLive, true);
            if (canPlay) {
                $('#share_video').addClass('none');
                $('#video_box').removeClass('none');
                if (localStorage.getItem('isregister') == 'yes') {
                    getLive(objLive.live_url);
                    $('#video_box').removeClass('none');
                    // fxLiveWatch(); //分享页观看分销直播送积分
                } else {
                    //快速注册后播放直播
                    fastRegister(); //html
                    fastRegisterEvent(objLive); //注册事件 
                }
                // $('#wrapper').addClass('top35-2875'); //评论列表
                $("#live_detail").addClass('pt31-2875');
            } else {
                $('#share_video').addClass('top3-5');
                $("#live_detail").addClass('pt31-2875');
                // $('#wrapper').addClass('top35-2875'); //评论列表
            }
        } else if (type == 'liveend') {
            creatLoadApp(true);
            $('#share_video').addClass('top3-5');
            $("#live_detail").addClass('pt31-2875');
        }
        if (is_weixin()) {
            var tips = '<div class="safari none"><img src="/images/safari.png"></div>';
            $('#containerBox').append(tips);
            weinxinShare(objLive, code);
            //下载APP、打开APP事件
            inWeChat(true);
        } else {
            if (isiOS) {
                inWeChat(false, true);
            } else if (isAndroid) {
                inWeChat(false, false);
            }
        }
    }

    //分享页快速注册HTML
    function fastRegister() {
         $('#registerpart').removeClass('none');
    }

    //分享页快速注册事件
    function fastRegisterEvent(objLive) {
        var objLive = objLive,
            live_url = objLive.live_url;
        //关闭快速注册
        $(document).on('click', '#registerpart .closepic', function() {
            $(this).parent().parent().addClass('none');
            $('#zcphone').val('+86 ');
            $('#zcyzm').val('');
        });
        //提交快速注册
        $('#registerbtn').on('click', function() {
            var params = {};
            params.tel = $('#zcphone').val().split(' ')[1];
            params.code = $('#zcyzm').val();
            params.nation_code =$('#zcphone').val().split(' ')[0];
            params.live_id = id;
            var url = labUser.api_path + '/live/sharesubscibe';
            ajaxRequest(params, url, function(data) {
                if (data.status) {
                    if (window.localStorage) {
                        try {
                            localStorage.setItem('isregister', 'yes');
                        } catch (error) {
                            console.log('error in incognito mode');
                        }
                    }
                    $('#registerpart').addClass('none');
                    $('#zcphone').val('');
                    $('#zcyzm').val('');
                    $('.share_video .share_text').html('');
                    $(".share_video").hide();
                    getLive(live_url);
                    $('#video_box').removeClass('none');
                    // fxLiveWatch(); //看直播送积分
                    window.location.reload();
                }
            });
        });
        //获取快速注册验证码
        $('#mescode').on('click', function() {
            var _this = $(this);
            var timeout = 59;
            var params = {};
            params.tel = $('#zcphone').val().split(' ')[1];
            params.nation_code=$('#zcphone').val().split(' ')[0];
            if ((/^\d{10,11}$/).test(params.tel)) {
                var url = labUser.api_path + '/live/sendcode';
                ajaxRequest(params, url, function(data) {});
                _this.css('backgroundColor', '#999');
                _this.attr('disabled', 'disabled');
                var setin = setInterval(function() {
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
            } else {
                console.log('phone number is not matched');
            }
        });
    }
    //跳活动详情页，品牌详情页
        if (shareFlag) {
            //to share page of brand-detail
            $(document).on('click', '.psrelative', function() {
               var brand_id =$(this).data('brand_id');

                window.location.href = labUser.path + "webapp/agent/brand/detail?id=" + brand_id + "&agent_id=" + uid + "&pagetag=08-9&is_share=1";
            });
            //to share page of activity-detail
            // $(document).on('click', '.activity', function() {
            //     var id = $(this).data('activity_id');
            //     window.location.href = labUser.path + "webapp/agent/activity/detail?id=" + id + "&uid=" + uid + "&makerid=0&position_id=0&is_share=1";
            // });
        } else {
            if (isiOS) {
                //to brand-detail
                $(document).on('click', '.psrelative', function(){
                   var brand_id =$(this).data('brand_id');
                   onAgentEvent('brand_detail','',{'type':'brand','id':brand_id,'userId':uid,'position':'6'});
                    pushToBrandDetail(brand_id);
                });
            } 
            else {
                //to brand-detail
                $(document).on('click', '.psrelative', function() {
                    var brand_id =$(this).data('brand_id');
                    onAgentEvent('brand_detail','',{'type':'brand','id':brand_id,'userId':uid,'position':'6'});
                    window.location.href = labUser.path + "webapp/agent/brand/detail?id=" + brand_id + "&agent_id=" + uid + "&pagetag=08-9";
                });
            }

            //to activity detail
            // $(document).on('click', '.activity', function() {
            //     var id = $(this).data('activity_id');
            //     window.location.href = labUser.path + "webapp/agent/activity/detail?id=" + id + "&uid=" + uid + "&pagetag=02-2&makerid=0&position_id=0";
            // });
        }



    //购买商品事件
    function buyGoodsEvent(shareFlag) {
        if (shareFlag) {
            //buy goods
            $(document).on('click', '.canbuy', function() {
                alert('请登录APP进行相关操作');
            });
        } else {
            //buy goods
            $(document).on('click', '.canbuy', function() {
                var goodsid = $(this).data('goodsid');
                var type = 'brand';
                buygoods(goodsid, type);
            });
        }
    }

    //直播回放、资讯详情页
    function liveEndEvent(shareFlag) {
        if (shareFlag) {
            //直播回放
            $(document).on('click', '.livevideo', function() {
                var livevideo_id = $(this).data('livevideo_id');
                window.location.href = labUser.path + "webapp/agent/vod/detail?id=" + livevideo_id + "&agent_id=" + uid + "&pagetag=05-4&is_share=1";
            });
            //资讯
            $(document).on('click', '.livemessage', function() {
                var message_id = $(this).data('message_id');
                window.location.href = labUser.path + "webapp/agent/headline/detail?id=" + message_id + "&pagetag=02-4&is_share=1";
            });
        } else {
            //直播回放
            $(document).on('click', '.livevideo', function() {
                var livevideo_id = $(this).data('livevideo_id');
                window.location.href = labUser.path + "webapp/agent/vod/detail?id=" + livevideo_id + "&agent_id=" + uid + "&pagetag=05-4";
            });
            //资讯
            $(document).on('click', '.livemessage', function() {
                var message_id = $(this).data('message_id');
                window.location.href = labUser.path + "webapp/agent/headline/detail?id=" + message_id + "&pagetag=02-4";
            });
        }
    }

    //分享页，【直播中】时'onebtn=true'，只有下载按钮；直播预告时多一个【设置直播预约】按钮
    function creatLoadApp(onebtn) {
        var loadApp = '';
        if (onebtn) {
            loadApp = '<div class="fixed_btn" id="loadAppBtn">\
                          <button class="signup" id="loadapp" style="width:100%;float: right;background-color:#2873ff;color:#fff;">下载APP</button>\
                       </div>';
        } else {
            loadApp = '<div class="fixed_btn" id="loadAppBtn">\
                          <button class="reserve"  id="reserve" style="width:50%;background-color: white;color:#6bc24b;">设置直播提醒</button>\
                          <button class="signup" id="loadapp" style="width:50%;float: right;background-color: #2873ff;color:#fff;">下载APP</button>\
                       </div>';
        }
        $("#containerBox").append(loadApp); //下载APP
    }

    //分享页，预约直播\预约成功
    function remindLive(objLive) {
        $('#livename').html('直播名称：' + objLive.subject );
        $('#livetime').html('直播开始时间：' + unix_to_fulltime(objLive.begin_time) );
       
        var successHtml = '<div class="remindpart none" id="remindsuccess" style="z-index:195;">\
            <div class="content">\
            <div class="tiptitle f18 tc remindfontcolor">订阅成功</div>\
            <div class="userinput remindcolor">\
            <div class="putdiv remindcolor f12 successtitle">直播订阅成功</div>\
            <div class="putdiv remindcolor f12 successmessage" id="membertips">欢迎订阅本直播，更多高清视频请打开无界商圈APP观看！</div>\
            <div class="putdiv remindcolor tc">\
            <div class="f12 successtips" style="">温馨提示：点击底部链接下载无界商圈APP</div>\
            </div>\
            </div>\
            <div class="closepic"></div>\
            </div>\
            </div>';
        // $('#containerBox').append(remindHtml);
        $('#containerBox').append(successHtml);
    }

    //分享页，预约事件
    function remindEvent() {
        //close overwindow
        $(document).on('click', '#liveremind .closepic,#remindsuccess .closepic', function() {
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
        $(document).on('click', '#reserve', function() {
            //设置直播提醒
            $('#liveremind').removeClass('none');
        });
        //直播提醒发送验证码
        $('#getcode').on('click', function() {
            var _this = $(this);
            var timeout = 59;
            var params = {};
            params.tel = $('#yyphone').val().split(' ')[1];
            params.nation_code =$('#yyphone').val().split(' ')[0];
            if ((/^\d{10,11}$/).test(params.tel)) {
                var url = labUser.api_path + '/live/sendcode';
                ajaxRequest(params, url, function(data) {});
                _this.css('backgroundColor', '#999');
                _this.attr('disabled', 'disabled');
                st_out = setInterval(function() {
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
            } else {
                console.log('phone number is not matched');
            }
        });
        //预约直播提交
        // $('#yysubmit').on('click', function() {
        //     var params = {};
        //     params.tel = $('#yyphone').val().split(' ')[1];
        //     params.code = $('#yyyzm').val();
        //     param.nation_code = $('#yyphone').val().split(' ')[0];
        //     params.live_id = id;
        //     var url = labUser.api_path + '/live/sharesubscibe';
        //     ajaxRequest(params, url, function(data) {
        //         if (data.status) { //隐藏提醒
        //             $('#liveremind').addClass('none');
        //             $('#yyphone').val('');
        //             $('#yyyzm').val('');
        //             if (data.message == '1') {
        //                 $('#remindsuccess').removeClass('none');
        //             } else if (data.message == '2') {
        //                 $('#membertips').html('欢迎成为无界商圈一员，账号、密码为预约所填手机号码。请及时登录无界商圈并对密码进行修改。');
        //                 $('#remindsuccess').removeClass('none');
        //             }
        //         }
        //     });
        // });
    }


    //微信内二次分享
    function weinxinShare(objLive, code) {
        var objLive = objLive,
            wxurl = labUser.api_path + '/weixin/js-config',
            desptStr = removeHTMLTag(objLive.description),
            despt = cutString(desptStr, 60);
        ajaxRequest({ url: location.href }, wxurl, function(data) {
            if (data.status) {
                wx.config({
                    debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                    appId: data.message.appId, // 必填，公众号的唯一标识
                    timestamp: data.message.timestamp, // 必填，生成签名的时间戳
                    nonceStr: data.message.nonceStr, // 必填，生成签名的随机串
                    signature: data.message.signature, // 必填，签名，见附录1
                    jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
                });
                wx.ready(function() {
                    wx.onMenuShareTimeline({
                        title: objLive.subject,
                        link: location.href,
                        imgUrl: objLive.share_image,
                        success: function() {
                            // callback when success
                            sencondShare('relay');
                        },
                        cancel: function() {}
                    });
                    wx.onMenuShareAppMessage({
                        title: objLive.subject,
                        desc: despt,
                        link: location.href,
                        imgUrl: objLive.share_image,
                        trigger: function(res) {
                            // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
                            //console.log('用户点击发送给朋友');
                        },
                        success: function(res) {
                            //console.log('已分享');
                            sencondShare('relay');
                        },
                        cancel: function(res) {
                            //console.log('已取消');
                        },
                        fail: function(res) {
                            //console.log(JSON.stringify(res));
                        }
                    });
                });
            }
        });
    }

    //下载APP、打开APP事件
    function inWeChat(iswx, inIOS) {
        if (iswx) {
            /**微信内置浏览器**/
            $(document).on('tap', '#loadapp,#openapp', function() {
                var _height = $(document).height();
                $('.safari').css('height', _height);
                $('.safari').removeClass('none');
            });
            //点击隐藏蒙层
            $(document).on('tap', '.safari', function() {
                $(this).addClass('none');
            });
        } else {
            if (inIOS) {
                /**download app**/
                $(document).on('tap', '#loadapp', function() {
                    window.location.href = 'https://itunes.apple.com/app/id981501194';
                });
                //open add
                $(document).on('tap', '#openapp', function() {
                    var strPath = window.location.pathname.substring(1);
                    var strParam = window.location.search;
                    var appurl = strPath + strParam;
                    var share = '&is_share';
                    var appurl2 = appurl.substring(0, appurl.indexOf(share));
                    window.location.href = 'openwjsq://' + appurl2;
                });
            } else {
                $(document).on('tap', '#loadapp', function() {
                    window.location.href = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.tyrbl.wujiesq';
                });
                $(document).on('tap', '#openapp', function() {
                    var strPath = window.location.pathname;
                    var strParam = window.location.search.replace(/is_share=1/g, '');
                    var appurl = strPath + strParam;
                    window.location.href = 'openwjsq://welcome' + appurl;
                });
            }
        }
    }

    //能否播放直播
    function playLiveVideo(objLive, shareFlag) {
        if (shareFlag) {
            if (objLive.ticket > 0) {
                return false;
            } else {
                return true;
            }
        } else {
            if (objLive.is_purchase == '1') {
                return true;
            } else {
                if (objLive.ticket > 0) {
                    return false;
                } else {
                    return true;
                }
            }
        }
    }

    //在线头像
    function onlineuserpic(objLive, shareFlag) {
        var picHtml = '';
        if (shareFlag) {
            picHtml += '<div class="livecount top26-7875">';
        } else {
            picHtml += '<div class="livecount top23-2875">';
        }
        $.each(objLive.online_users, function(index, item) {
            picHtml += '<img src="' + item + '" alt="">';
        });
        // picHtml += '<span class="awardpicture fr"></span>';
        picHtml += '<span class="splitspan fr tc">|</span>';
        picHtml += '<span class="f12 fr">当前人数：' + objLive.online_count + '</span>';
        picHtml += '</div>';
        $('#containerBox').append(picHtml);
    }

    /*实例化直播*/
    function getLive(live_url) {
        console.log(live_url);
        var player = new qcVideo.Player(
            //页面放置播放位置的元素 ID
            "video_box", {
                "width": 414,
                "height": 232,
                "live_url": live_url,
                //"live_url2": live_url,
                "h5_start_patch": {
                    "url": '../../images/live.png',
                    "stretch": true
                }
            }, { //播放状态
                'playStatus': function(status, type) {
                    // status 可为 {ready: "播放器已准备就绪",seeking:"搜索",
                    // suspended:"暂停", playing:"播放中" , playEnd:"播放结束" , stop: "试看结束触发" }
                    if (status == 'playing') {}
                    if (status == "playEnd") {}
                    if (status == "stop") {}
                    if (status == "error") {}
                },
            }
        );
        //player.play();//PC端Flash播放器支持
    }

    /*实例化直播预览*/
    function getLivePreview(live_url) {
        livePlayer = new qcVideo.Player(
            //页面放置播放位置的元素 ID
            "preview_box", {
                "width": 414,
                "height": 232,
                "live_url": live_url,
                //"live_url2": live_url,
                "h5_start_patch": {
                    "url": '../../images/live.png',
                    "stretch": true
                }
            }, { //播放状态
                'playStatus': function(status, type) {
                    // status 可为 {ready: "播放器已准备就绪",seeking:"搜索",
                    // suspended:"暂停", playing:"播放中" , playEnd:"播放结束" , stop: "试看结束触发" }
                    if (status == 'playing') {}
                    if (status == "playEnd") {}
                    if (status == "stop") {}
                    if (status == "error") {}
                },
            }
        );
    }

    /**实例化点播**/
    function getVod(vid) {
        videoPlayer = new qcVideo.Player(
            //页面放置播放位置的元素 ID
            "preview_box", {
                "file_id": vid,
                "app_id": "1251768344",
                "auto_play": "1", //是否自动播放 默认值0 (0: 不自动，1: 自动播放)
                "width": 414,
                "height": 232,
                "stop_time": 0,
                "disable_full_screen": 0
            }, {
                'playStatus': function(status) {
                    if (status == 'playing') { //播放中
                    }
                    if (status == "playEnd") { //播放结束
                    }
                    if (status == "stop") { //试看结束
                    }
                },
            });
    };

    //点播、直播预览自增
    // function increaseViewn(id, type) {
    //     var param = {};
    //     param["id"] = id;
    //     param["num"] = 1;
    //     param['type'] = type;
    //     var url = labUser.api_path + '/live/incre';
    //     ajaxRequest(param, url, function(data) {
    //         if (data.status) {}
    //     });
    // }

  

        

function getSubscribe(id,subscribe) {
    if (isAndroid) {
        javascript:myObject.getSubscribe(id,subscribe);
    } else if (isiOS) {
        var data = {
            'id':id,
            'subscribe':subscribe
        };
        window.webkit.messageHandlers.getSubscribe.postMessage(data);
    }
}

    function tips(e) {
        $('.tips').text(e).removeClass('none');
        setTimeout(function() {
            $('.tips').addClass('none ');
        }, 1500);

    };
