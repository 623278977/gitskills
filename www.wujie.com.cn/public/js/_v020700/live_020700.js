
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
        uid = args['uid'] || '0',
        origin_mark = args['share_mark'] || 0, //分销参数，分享页用
        origin_code = args['code'] || 0,
        share_mark = null,
        code = null, //分销参数，APP内转发用
        videoExist = false, //直播结束页，是否有录播
        isbindActivity = false,
        activityImage = '',
        live_state = 'future',
        dataMessage = null;

    new FastClick(document.body);
    var shareFlag = (window.location.href).indexOf('is_share') > 0 ? true : false;
    function changemengceng(){
           $('.addbackgd').on('click',function(){
              $(this).find('.back').removeClass('none');
              $(this).siblings().find('.back').addClass('none');
              $('#bind_brand').css('padding-left','0')
            });
          };
  function directclick(){
            $('.addbackgd').on('click',function(){
               var brand_id =$(this).data('brand_id');
               if(shareFlag){
                     window.location.href = labUser.path + "webapp/brand/detail/_v020700?id=" + brand_id + "&uid=" + uid + "&pagetag=08-9&is_share=1";
                }else{
                     window.location.href = labUser.path + "webapp/brand/detail/_v020700?id=" + brand_id + "&uid=" + uid + "&pagetag=08-9"; 
                }
                });
          };
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
                var url = labUser.api_path + '/live/detail/_v020700';
                ajaxRequest(params, url, function(data) {
                    if (data.status) {
                        dataMessage=data.message;
                        var objVideos = data.message.videos || null,
                            liveNews = data.message.news || null,
                            objLive = data.message.live,
                            objActivity = data.message.activity || {};//,
                            live_state = objLive.situation;
                            var score_price=data.message.score_price;
                            var is_purchase=objLive.is_purchase;
                            if(is_purchase==0){
                                $('.fx_share1').show();//分享直播的栏目是否展示
                                if(shareFlag){
                                     $(document).on('click','.buy_btn',function(){
                                      tips('请到APP购买')   
                                  }) 
                                }else{
                                   if(score_price>0){
                                    $('.share_need').show();
                                    $('.nav_add').css('margin-top','3rem');
                                    } else {
                                        $('.share_need').hide();
                                        $('.nav_add').css('margin-top','1.2rem');
                                         } 
                                    }
                            }else{
                                 $('.fx_share1').hide();//分享直播的栏目是否展示
                                 $('#share_need').hide();
                            };
                            $('.fee').html(score_price);
                            console.log(score_price);
                            $('#containerBox').attr('score_price',score_price);
                            $('#containerBox').attr('ticket_id',data.message.live.ticket_id);
                            $('#containerBox').attr('activity_id',data.message.live.activity_id)

                        share_mark = data.message.share_mark;
                        objLive["id"] = id;
                        watchTime = (objLive.watch_reward_long > 0) ? (objLive.watch_reward_long * 60000) : 0;

                        //浏览
                        if (shareFlag) {
                            var lives='liveID'+id;
                            if (objLive.share_reward_unit != 'none'&&(!localStorage.getItem(lives))) {
                                is_fx = true;
                                disfx(origin_mark, 'view', '0', origin_code);
                                localStorage.setItem(lives,id);
                            }
                        }

                        $('#livesubject').data('share_mark', share_mark);
                        $('#livesubject').data('distribution_id', objLive.distribution_id);
                        fenxiao();
                        if (live_state == 'future') {
                            //预告
                            livePreview(objLive, objActivity, shareFlag, origin_code);
                             changemengceng();
                        } else if (live_state == 'is_living') {
                            //直播中
                            //请求互动列表
                            living(objLive, objActivity, shareFlag, origin_code);
                            //评论列表
                            Comment.getCommentList(param, 'reload', 'live');
                        } else if (live_state == 'past') {
                            //直播结束
                            directclick();
                            liveEnd(objLive, objActivity, shareFlag, objVideos, liveNews, origin_code);
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
                        increaseViewn(id, 'live');
                        getzannumber();
                       

                    }
                });
            },
            order: function(live_id, subscribe) {
                var param = {};
                param["uid"] = uid;
                param["live_id"] = live_id;
                param["type"] = subscribe;
                var url = labUser.api_path + "/live/subscribe";
                ajaxRequest(param, url, function(data) {
                    if (data.status) {
                       
                        if (param["type"] == "1") {
                            $("button.subscribe").html("取消订阅").data("subscribe", 1);
                             getSubscribe(live_id, subscribe);
                        } else {
                            $(".subscribe").html("+订阅").data("subscribe", 0);
                             getSubscribe(live_id, subscribe);
                        }
                    }
                });
            },
           smszan: function(id,uid,live_id, subscribe,relation){
                var param = {};
                param["id"] = id;
                param["uid"] = uid;
                param["live_id"] = live_id;
                param["type"] = subscribe;
                param["relation"] = relation;
                var url = labUser.api_path + "/userpraise/zan";
                ajaxRequest(param, url, function(data) {
                    if (data.status) {   
                        if (param["type"] == 1) {
                            $('#_zan_').addClass('dian_zan_').removeClass('wei_zan_');
                             getSubscribe(live_id, subscribe);
                        } else {
                          // $('#_zan_').addClass('wei_zan_').removeClass('dian_zan_');
                             getSubscribe(live_id, subscribe);
                        }
                    }
                });
           },
            commentzan: function(id,uid, subscribe){
                var param = {};
                var ID=$('#m_c_m').attr('data-id');
                param["id"] = id;
                param["uid"] = uid;
                param["type"] = subscribe;
                var url = labUser.api_path + "/comment/zhan";
                ajaxRequest(param, url, function(data) {
                    if (data.status) {   
                        if (param["type"] == 1) {
                            // $('#_zan_').addClass('dian_zan_').removeClass('wei_zan_');
                             getSubscribe(live_id, subscribe);
                        } else {
                          // $('#_zan_').addClass('wei_zan_').removeClass('dian_zan_');
                             getSubscribe(live_id, subscribe);
                        }
                    }
                });
           }

        }
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
        $("#nav_every_say").removeClass('none');
        $("#comment").addClass('none');
        addTips(objLive, 'preview', shareFlag);
        liveCommonHtml(objLive, objActivity, shareFlag);
        pinglunHtml(objLive, shareFlag);
        if (shareFlag) {
            adjustSharePage('preview', objActivity, objLive, code);
        } else {
            bottomBtn(objLive, 'preview');
            bottomBtnEvent(objLive, objActivity, uid, 'preview');
        }
        //有预告片
        //if (objLive.foreshow_url) {
        //直播预览的HTML
        // createPreviewHtml();
        //直播预览事件
        // previewEvent(objLive.foreshow_url, 'preview');
        //}
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
        if (objLive.with_activity == '1') {
            bindActivity(objActivity);
            isbindActivity = true;
            activityImage = objActivity.detail_img;
        }
        if (objLive.with_brand == '1') {
            bindBrand(objLive);
        }
        if (objLive.with_guest == '1') {
            bindGuests(objLive);
        }
        //分享用的图片
        $('#share_img').data('src', objLive.share_image);
        //直播介绍
        $('#live_info').html(objLive.description);
        $('#livesubject').html(objLive.subject);
       
    }

    //直播中---立即加盟和互动栏目
    function livingHtml(objLive, shareFlag) {
        //在线人数
        onlineuserpic(objLive, shareFlag);
        //创建navbar
        createNavBar(objLive, 'living', shareFlag);
        //创建互动栏评论按钮
        createCommentBtn(shareFlag);
        //分享页时创建评论框HTML
        createCommentDiv(shareFlag);
        //注册事件
        navBarEvent('living', shareFlag);
        commentBtnEvent(shareFlag);
        //加载下一页评论、最新评论
        commentsEvent();
        //购买商品
        buyGoodsEvent(shareFlag);
        //有预告片
        //if (objLive.live_url) {
        //直播预览的HTML
        //createPreviewHtml();
        //直播预览事件
        //previewEvent(objLive.live_url, 'living');
        //}
    }
   //预告评论框
   function pinglunHtml(objLive, shareFlag) {
        //在线人数
        //创建navbar
        yugaoCommentBtn(objLive,shareFlag);
        // $("#comment_btn").show();
        //分享页时创建评论框HTML
        // createCommentDiv(shareFlag);
        //注册事件
        // navBarEvent('living', shareFlag);
        commentBtnEvent(shareFlag);
        //加载下一页评论、最新评论
        //if (objLive.live_url) {
        //直播预览的HTML
        //createPreviewHtml();
        //直播预览事件
        //previewEvent(objLive.live_url, 'living');
        //}
    }
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
                videoHtml += '<p class="f14 color999">录制于<span>' + item.created_at + '</span></p>';
                if (item.keywords && item.keywords.length > 0) {
                    videoHtml += '<div class="f12 keyword">';
                    $.each(item.keywords, function(index, oneitem) {
                        keywordhtml += '<span class="ui-border-radius">' + oneitem + '</span>';
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
            show_type = objLive.show_type,
            navHtml = '';
        if (type == 'living') {
            $('.share_need').hide();
            $('#brand-mes').css('top','27.7875rem');
            if (objLive.with_brand == '1') {
                //绑定品牌，增加【立即加盟】一栏
                if (shareFlag) {
                    navHtml += '<div class="navbar color999 top30-7875">';
                } else {
                    navHtml += '<div class="navbar color999 top27-2875">';
                }
                navHtml += '   <span class="threecol live-orange" id="detail_block">直播概况</span>\
                               <i class="left33">|</i>\
                               <span class="threecol" id="addin_block">立即加盟</span>\
                               <i class="left66">|</i>\
                               <span class="threecol" id="comment_block">互动</span>\
                           </div>';
            } else {
                //【直播概况】栏和【互动】栏
                if (shareFlag) {
                    navHtml = '<div class="navbar color999 top30-7875">';
                } else {
                    navHtml = '<div class="navbar color999 top27-2875">';
                }
                navHtml += '<span class="twocol live-orange" id="detail_block">直播概况</span>\
                               <i class="left50">|</i>\
                               <span class="twocol" id="comment_block">互动</span>\
                               </div>';
            }
        } else if (type == 'liveend') {
            if (shareFlag) {
                navHtml = '<div class="navbar color999 top26-7875">';
            } else {
                navHtml = '<div class="navbar color999 top23-2875">';
            }
            navHtml += '<span class="twocol live-orange" id="detail_block">直播概况</span>\
                        <i class="left50">|</i>\
                        <span class="twocol" id="livevideo_block">直播回放</span>\
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
                directclick();
                $("#comment").hide();
                $('#distribution').hide();
                $('#bind_activity').css('border-top','1rem solid#f2f2f2');
                $("#detail_block").click(function() {   
                    $(this).addClass('live-orange').siblings().removeClass('live-orange');
                    $("#barnd_list").hide(); //立即加盟栏内容
                    $("#comment").hide(); //互动栏内容
                    $("#comment_btn").hide(); //底部评论按钮
                    $("#loadAppBtn").show(); //下载APP按钮
                    $("#live_introduce").show(); //直播概况
                });
                /**互动**/
                $('#distribution').hide();
                $("#comment_block").click(function() {
                    $(this).addClass('live-orange').siblings().removeClass('live-orange');
                    $("#loadAppBtn").hide();
                    $("#live_introduce").hide();
                    $("#barnd_list").hide();
                    $("#comment").show();
                    $("#comment_btn").show();
                    $("#allComment").show();
                });
                /**立即加盟**/
                $("#addin_block").click(function() {
                if($('#distribution').is(':visible')){
                     
                      $('#distribution').hide();
                   }
                    $(this).addClass('live-orange').siblings().removeClass('live-orange');
                    $("#loadAppBtn").hide();
                    $("#live_introduce").hide();
                    $("#comment_btn").hide();
                    $("#comment").hide();
                    $("#barnd_list").show();
                });
            } else {
                //app内部
                /**直播概况**/
                $("#comment").hide();
                 directclick();
                $("#detail_block").click(function() {  
                    $(this).addClass('live-orange').siblings().removeClass('live-orange');
                    $('#distribution').show();
                    var shows = $('#buyonline').data('isshow');
                    if (shows == '1') {
                        $("#buyonline").show();
                    }
                    $("#barnd_list").hide(); //立即加盟栏内容
                    $("#comment").hide(); //互动栏内容
                    $(".comment_btn").hide(); //底部评论按钮
                    $("#live_introduce").show();
                });
                /**互动**/
                $("#comment_block").click(function() {
                    $(this).addClass('live-orange').siblings().removeClass('live-orange');
                    var shows = $('#buyonline').data('isshow');
                    if (shows == '1') {
                        $("#buyonline").hide();
                    }
                    $("#live_introduce").hide();
                    $('#distribution').hide();
                    $("#barnd_list").hide();
                    $("#comment").show();
                    $(".comment_btn").show();
                });
                /**立即加盟**/
                $("#addin_block").click(function() {
                    $('#distribution').hide();
                    $(this).addClass('live-orange').siblings().removeClass('live-orange');
                    var shows = $('#buyonline').data('isshow');
                    if (shows == '1') {
                        $("#buyonline").hide();
                    }
                    $("#live_introduce").hide();
                    $(".comment_btn").hide();
                    $("#comment").hide();
                    $("#barnd_list").show();
                });
            }

        } else if (type == 'liveend') {
             directclick();
            $('.refreshpic1').addClass('none');
             fenxiao();
            /**直播概况**/
            $("#detail_block").click(function() {
                $(this).addClass('live-orange').siblings().removeClass('live-orange');
                $("#live_video").hide(); //直播回放
                $("#live_introduce").show();
                // $('#distribution').show();

            });
            //直播回放
            $("#livevideo_block").click(function() {
                $(this).addClass('live-orange').siblings().removeClass('live-orange');
                $("#live_video").show();
                $('#distribution').hide();
                $("#live_introduce").hide();
            });
        }
    }

    //直播中--创建互动评论按钮
    function createCommentBtn(shareFlag) {
        var commentBtnHtml = '';
        if (shareFlag) {
            commentBtnHtml = '<div class="comment_btn none" id="comment_btn">\
                                <button type="button" class="tl" style="width: 100%;">及时互动，分享智慧...</button>\
                            </div>';
        } else {
            commentBtnHtml = '<div id="comment_btn" class="comment_btn none">\
                                <button type="button" class="tl" style="width: 30rem;">及时互动，分享智慧...</button>\
                                <span class="uploadpic1"></span><i class="uploadpictext f12">发表图片</i>\
                            </div>';
        }
        $('#containerBox').append(commentBtnHtml);
    }
    //预告中的创建互动评论按钮
     function yugaoCommentBtn(objLive,shareFlag) {
        var objLive = objLive;
        // var commentBtnHtml = '';
        // if (shareFlag) {
        //     commentBtnHtml+='<div class="comm_btn none" class="live_yugao" id="comm_btn">';
        //     commentBtnHtml+='<span id="_zan_"></span> ';
        //     commentBtnHtml+='<span id="dian_zan_number" class="dian_zan_number"></span>';
        //     commentBtnHtml+='<button type="button" class="tl" style="width: 100%;">我来说两句...</button>';
        //     commentBtnHtml+='</div>';
        // } else {
        //     commentBtnHtml+= '<div id="comment_btn" class="comment_btn none">';
        //     commentBtnHtml+= '<span id="_zan_"></span> ';
        //     commentBtnHtml+= '<span id="dian_zan_number" class="dian_zan_number"></span>';
        //     commentBtnHtml+= '<button type="button" class="tl" style="width:30rem;">我来说两句...</button>';
        //     commentBtnHtml+= '<span class="uploadpic1" id="chuan_pict" style="float:right;margin-right:1rem;margin-top:0.5rem"></span>';
        //     commentBtnHtml+= '</div>';
        // }
        // $('#containerBox').append(commentBtnHtml);
        $('#_zan_').on('click', function () {
              if($(this).hasClass('wei_zan_')){
                $(this).addClass('dian_zan_').removeClass('wei_zan_');
                $('#dian_zan_number').html($('#dian_zan_number').text()-1+2);
                $('#_zan_').attr('disabled',true);
              }

            });
        $('#chuan_pict').on('click', function() {
                uploadpic(param.id, 'live', false);
            });
    }
   
    //请求点赞总数
   // var ID =$('.chargetip').data('distribution');
   // alert(ID);

    // function getzannumber(){
        
    //         if(dataMessage){
    //           var distribution_id=dataMessage.live.distribution_id;
    //           var count_zan=dataMessage.count_zan;
    //           var is_zan=dataMessage.is_zan;

    //           if(is_zan==1){
    //               $('#_zan_').addClass('dian_zan_');
    //           }else{
    //               $('#_zan_').addClass('wei_zan_');
    //           }
    //           var score_price=dataMessage.score_price;
    //          $('#dian_zan_number').text(count_zan);
    //          $('.fee').html(score_price);
    //         }
    // }

    //直播中--创建分享页的评论框
    function createCommentDiv(shareFlag) {
        if (shareFlag) {
            var comDivHtml = '';
            comDivHtml = '<div class="commentback none" id="commentback">\
                            <div id="tapdiv" class="covers"></div>\
                            <div class="textareacon">\
                                <textarea class="f12" name="comment" id="comtextarea" cols="30" rows="10" style="resize: none;" placeholder=""></textarea>\
                                <button class="fr subcomment f16" id="subcomments">发表</button>\
                            </div>\
                        </div>';
            $('#containerBox').append(comDivHtml);
        }
    }

    //评论按钮(发表图片文字)、打赏事件
    function commentBtnEvent(shareFlag) {
        if (shareFlag) {
            $('#subcomments').on('click', function() {
                param.content = utf16toEntities($("#comtextarea").val());
                if (param.content) {
                    Comment.addComment(param, 'no');
                }
                $("#comtextarea").val("");
                $("#commentback").addClass('none');
            });
            $('.awardpicture').on('click', function() {
                alert('请登录无界商圈APP');
            });
            var inputtext = document.getElementById('comtextarea');
            var submitbtn = document.getElementById('subcomments');
            //输入框
            $('#comtextarea').on('focus', function() {
                setTimeout(function() {
                    var c = window.document.body.scrollHeight;
                    window.scroll(0, c);
                }, 500);
                return false;
            });
            inputtext.oninput = function() {
                    var text = this.value;
                    if (text.length > 0) {
                        submitbtn.style.backgroundColor = '#1e8cd4';
                    } else {
                        submitbtn.style.backgroundColor = '#999';
                    }
                }
                /**评论按钮绑定input选中**/
            $(".comment_btn>button").bind("click", function() {
                $('#commentback').removeClass('none');
                $('.textareacon textarea').focus();
                $('#tapdiv').one('click', function() {
                    $('#subcomments').css('backgroundColor', '#999');
                    $('#comtextarea').val('');
                    $('#commentback').addClass('none');
                });
            });
        } else {
            //upload pictures发表图文
            $('.uploadpic,.uploadpictext').on('click', function() {
                uploadpic(param.id, 'live', false);
            });
            //仅文字
            $('.comment_btn>button').on('click', function() {
                uploadpic(param.id, 'live', true);
            });
            //打赏
            $('.awardpicture').on('click', function() {
                if (uid == '0') {
                    showLogin();
                } else {
                    reward(param.id, 'live');
                }
            });
        }
    };

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
    function fenxiao() {
        if (dataMessage) {
            var distribution_id=dataMessage.live.distribution_id;
            var is_distribution=dataMessage.is_distribution;
            var rule=dataMessage.distribution;
            var urlPath = window.location.href;
            var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
            // var live_situation = dataMessage.live.situation;

             if(distribution_id > 0 && is_distribution == 1){
                     $('#distribution').show();  
                } else {
                    if ($('#bind_activity>div').length==0){
                        $('#bind_activity').hide();
                    }
                    $('#distribution').hide();  
                }
              //是否显示分销赚佣
                if(distribution_id > 0 && (!shareFlag) && is_distribution == 1){
                    var ruleHtml='';
                    $.each(rule,function(i,j){
                        ruleHtml += j.describe +';' ;
                    });
                    // console.log(ruleHtml.match(/[0-9]{1,6}积分|[0-9]{1,6}元|\\s{0,}\/\\s{0,}(人|单)?/g));
                    ruleHtml = ruleHtml.replace(/[0-9]{1,6}积分|[0-9]{1,6}元|\\s{0,}\/\\s{0,}(人|单)?/g,'<em class="colorf4">$&</em>');
                    $('.dis_coin').html(ruleHtml);
                    $('#distribution').removeClass('none');

                     var dis_coin = $('.dis_coin'),more_icon =$('.more_icon img'),showMore = true;

                    $(document).on('click','.more_icon',function(){
                        if(showMore){
                            dis_coin.removeClass('ui-nowrap-multi');
                            more_icon.css({'transform':'rotate(180deg)',
                                        '-webkit-transform':'rotate(180deg)',
                                        '-o-transform':'rotate(180deg)'});
                            showMore = false ;
                        }else{
                            dis_coin.addClass('ui-nowrap-multi');
                            more_icon.css({'transform':'rotate(0deg)',
                                        '-webkit-transform':'rotate(0deg)',
                                        '-o-transform':'rotate(0deg)'});
                            showMore = true ;
                        }
                    });

                    var dis = $('.dis_coin');
                    var spanHeight= parseFloat(dis.css('height')),spanLH=parseFloat(dis.css('line-height'));
                    console.log(spanHeight);
                    console.log(spanLH);
                    if(spanHeight > spanLH*2){
                            dis.addClass('ui-nowrap-multi'); 
                    }else{
                        $('.more_icon').addClass('none');
                        dis.addClass('pb1-5');
                    }  
                }  
        }
    }

   //获取点赞数据
    function getzannumber(){
           // alert(dataMessage.count_zan);
        if(dataMessage){
                //dataMessage = data.message;
                var count_zan=dataMessage.count_zan;
                var is_zan=dataMessage.is_zan;
             
                if(is_zan==1){
                    $('#_zan_').addClass('dian_zan_');
                }else{
                    $('#_zan_').addClass('wei_zan_');
                }

                var score_price=dataMessage.score_price;
                $('#dian_zan_number').text(count_zan);
         
                $('.fee').html(score_price);
        }
       
    }
   // getzannumber(id,uid);
    //提示框文字[并非播放视频的容器]
    function addTips(objLive, type, shareFlag) {
        var objLive = objLive,
            ticket_price = objLive.ticket,
            distribution_id=objLive.distribution_id
        if (type == 'preview') {
            if (ticket_price > 0 && objLive.is_purchase == '0') {
                $('#share_video').append('<div class="chargetip" data-ticket_id='+objLive.ticket_id+' data-distribution='+objLive.distribution_id+'><div class="sjx"></div><div class="tiptext white">付费</div></div>');
                // $('#share_video').append('<p class="dark_white_text"><span style="font-size:1.2rem;color:#ffac00;">预告</span>'+objLive.subject+'</p>');
                $('#share_video').append('<p class="dark_white_color">本次直播将于</span> ' + unix_to_mdhm(objLive.begin_time) + ' 开始</p><p class="dao_ji_shi">倒计时</p>');
                $('#share_video').append('<p class="dark_white_" data-time="'+objLive.begin_time+'"><span id="time_dao"></span></p>');
            } else {
                // $('#share_video').append('<p class="dark_white_text"><span style="font-size:1.2rem;color:#ffac00;">预告</span>'+objLive.subject+'</p>');
                $('#share_video').append('<p class="dark_white_color">本次直播将于</span> ' + unix_to_mdhm(objLive.begin_time) + ' 开始</p><p class="dao_ji_shi">倒计时</p>');
                $('#share_video').append('<p class="dark_white_" data-time="'+objLive.begin_time+'"><span id="time_dao"></span></p>');

            }
            if (!shareFlag) {
                //APP内,如果有预告
                //if (objLive.foreshow_url) {
                //    $('#share_video').append('<div class="live_preview white"><span class="showvideo mr1 ui-border-radius">观看预告片</span><span class="preview_time">' + objLive.foreshow_duration + '</span></div>');
                //}
                if (objLive.share_reward_unit == 'score') { //积分
                    $('#share_video').append('<div class="pl1-33 pr1-33 fx_share1 none"><p class="f12 l">分享直播，立即获得' + objLive.share_reward_num + '积分</p><button class="c00a0ff1 l f12 understand"><img src="/images/notice1.png" alt="">了解分享规则介绍</button><span class="f16 r close_share">×</span></div>');
                } else if (objLive.share_reward_unit == 'currency') {
                    $('#share_video').append('<div class="pl1-33 pr1-33 fx_share1 none"><p class="f12 l">分享直播，立即获得' + objLive.share_reward_num + '无界币</p><button class="c00a0ff1 l f12 understand"><img src="/images/notice1.png" alt="">了解分享规则介绍</button><span class="f16 r close_share">×</span></div>');
                }
                //事件
                fx();
            }
        } else if (type == 'living') {
            if (ticket_price > 0) {
                $('#share_video').append('<div class="chargetip"><div class="sjx"></div><div class="tiptext white">付费</div></div>');
                if (shareFlag) {
                    $('#share_video').append('<div class="share_text"><p class="white">本场直播为有偿直播，观看前请先支付费用</p><p class="dark_yellow">请在APP中购买后观看</p></div>');
                } else {
                    $('#share_video').append('<div class="share_text"><p class="white">本场直播为有偿直播，观看前请先支付费用</p><p class="dark_yellow">请购买后观看</p></div>');
                }
            }
            //如果有预告
            //if (objLive.live_url) {
            //    $('#share_video').append('<div class="live_preview white"><span class="showvideo mr1 ui-border-radius">观看预告片</span><span class="preview_time">05:00</span></div>');
            //}
            if (!shareFlag) {
                if (objLive.share_reward_unit == 'score') { //积分
                    $('#share_video').append('<div class="pl1-33 pr1-33 fx_share1 none"><p class="f12 l">分享直播，立即获得' + objLive.share_reward_num + '积分</p><button class="c00a0ff1 l f12 understand"><img src="/images/notice1.png" alt="">了解分享规则介绍</button><span class="f16 r close_share">×</span></div>');
                } else if (objLive.share_reward_unit == 'currency') {
                    $('#share_video').append('<div class="pl1-33 pr1-33 fx_share1 none"><p class="f12 l">分享直播，立即获得' + objLive.share_reward_num + '无界币</p><button class="c00a0ff1 l f12 understand"><img src="/images/notice1.png" alt="">了解分享规则介绍</button><span class="f16 r close_share">×</span></div>');
                }
                //事件
                fx();
            }
        } else if (type == 'liveend') {
            $('#share_video').html('<div class="share_text"><p class="liveendpic"></p><p class="white">来晚了一步，本次直播已经结束啦！</p><p class="dark_yellow">观看本次直播回放，不错过精彩每一秒！</p></div>');
        }
    }

    //分销相关事件
    function fx() {
        //关闭分享机制提醒
        $(document).on('tap', '.close_share', function() {
            $('.fx_share1').addClass('none');
        });
        //了解更多分享机制
        $(document).on('tap', '.understand', function() {
            window.location.href = labUser.path + 'webapp/protocol/moreshare/_v020500?pagetag=025-4';
        })
    }

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
    function bindActivity(objActivity) {
        var objActivity = objActivity,
            actHtml = '',
            keywords = '';
        actHtml += '<div class="bgrightjt activity" data-activity_id="' + objActivity.id + '">';
        actHtml += '<p class="f16 mb05 b text-ellipsis">' + objActivity.subject + '</p>';
        actHtml += '<p class="color999 mb0">活动开始时间：' + unix_to_datetime(objActivity.begin_time) + '</p>';
        if (objActivity.keywords && objActivity.keywords.length > 0) {
            actHtml += '<p class="color999 mb1">活动场地：' + objActivity.city.split('@').join('、') + '</p>';
            actHtml += '<p class="keyword f12 mb05">';
            $.each(objActivity.keywords, function(index, item) {
                keywords += '<span class="border-8a-radius">' + item + '</span>';
            });
            actHtml += keywords;
            actHtml += '</p>';
        } else {
            actHtml += '<p class="color999 mb0">活动场地：' + objActivity.city.split('@').join('、') + '</p>';
        }
        actHtml += '</div>';
        $('#bind_activity').append(actHtml);
        $('#bind_activity').removeClass('none');
    }

    //绑定品牌,商品
    function bindBrand(objLive) {
        var brandHtml = '',
            objLive = objLive;
        $.each(objLive.brands, function(index, item) {
            var keywordhtml = '';
            brandHtml+='<div class="addbackgd" data-brand_id="' + item.id + '">';//我加的
            brandHtml += '<div class="brand-detail ui-border-t psrelative" data-brand_id="' + item.id + '">';
            brandHtml += '<img src="' + item.logo + '" alt="" class="fl brandlogo">';
            brandHtml += '<div class="fr width100">';
            brandHtml += '<div style="margin-left:10.73rem;">';
            brandHtml += '<div><em class="service f12 mr1">' + item.category_name + '</em><span class="f14 text_black b">' + item.name + '</span></div>';
            brandHtml += '<div class="f12 color999 mb05 mt05 ui-nowrap-multi">' + removeHTMLTag(item.detail) + '</div>';
            brandHtml += '<p class="f12 mb1"><span class="c8a">投资额：</span><span class="colorfe">' + item.investment_min + ' ~ ' + item.investment_max + '万</span></p>';
            brandHtml += '<div class="f12 keyword">';
            if (item.keywords.length > 0) {
                $.each(item.keywords, function(index, oneitem) {
                    keywordhtml += '<span class="border-8a-radius">' + oneitem + '</span>';
                });
                brandHtml += keywordhtml;
            }
            brandHtml += '</div>';
            brandHtml += '</div>';
            brandHtml += '</div>';
            brandHtml += '<div class="clearfix"></div>';
            brandHtml += '</div>';
            brandHtml +='<div id="back" class="back none">';
            if(item.is_collect==1){brandHtml +='<div class="shoucang_requ" data-brand_id="' + item.id + '"><div id="shoucang" data-brand_id="' + item.id + '" data-subscribe="do" class="shoucang shou"></div><span class="shoucangson">已收藏</span></div>';
            }else{
                 brandHtml +='<div class="shoucang_requ " data-brand_id="' + item.id + '"><div id="shoucang" data-brand_id="' + item.id + '"  data-subscribe="undo" class="shoucang cang"></div><span class="shoucangson">收藏</span></div>';
            }
            brandHtml +='<div class="tijiao_requ" data-brand_id="' + item.id + '"><div id="tijiao" class="tijiao"></div><span id="tijiaoson">提交意向</span></div>';
            brandHtml +='<div class="chakan_requ" data-brand_id="' + item.id + '"><div id="chakan" class="chakan"></div><span id="chakanson">查看详情</span></div>';
            brandHtml +='</div>';//我加的
            brandHtml +='</div>';
        });
        $('#bind_brand').append(brandHtml);
        $('#bind_brand').removeClass('none');
   
    function tips(e) {
        $('.tips').text(e).removeClass('none');
        setTimeout(function() {
            $('.tips').addClass('none ');
        }, 1500);

    };
        //点击出现提交意向框
    $('.tijiao_requ').on('click',function(){
            $('#brand-mes').removeClass('none').addClass('a-fadeinT');
             $('.fixed-bg').removeClass('none');
        });
    $('.fixed-bg').on('click',function(){
        $('#brand-mes').addClass('none');
        $(this).addClass('none');
      })
        //提交留言方法；
        function liuyantijiao(uid){
            var reg=/1[34578]\d{9}/;
            var mobile=$('input[name="phone"]').val();
            var consult=$('input[name="consult"]').val();
            var name=$('input[name="realname"]').val();
            var param={};
            var brandID=$('.brand-detail').attr('data-brand_id');
            param['id'] = brandID;
            param['uid']=uid;
            param['mobile'] = mobile;
            param['realname'] = name;
            param['consult'] = consult;
         var url = labUser.api_path + '/brand/message/_v020500';
          ajaxRequest(param, url, function(data) {
            if(data.status){
                $('#brand-mes').addClass('none');
                alert(data.message) 
            }
          })
        };
        $('.send-mes').on('click',function(){
             var reg=/1[34578]\d{9}/;
             var mobile=$('input[name="phone"]').val();
            if(reg.test(mobile)){
               liuyantijiao(uid);
            }else{
                alert('信息请填写正确')
            }
        });
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
                goodsHtml += '<p class="f12 mb1"><span class="c8a">投资额：</span><span class="colorfe">' + item.investment_min + ' ~ ' + item.investment_max + '万</span></p>';
                goodsHtml += '<div class="f12 keyword">';
                if (item.keywords.length > 0) {
                    $.each(item.keywords, function(index, oneitem) {
                        keyword += '<span class="border-8a-radius">' + oneitem + '</span>';
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
    function bindGuests(objLive) {
        var objLive = objLive,
            guestsHtml = '';
        if (objLive.guests && objLive.guests.length > 0) {
            $.each(objLive.guests, function(index, item) {
                guestsHtml += '<div class="pt1-5 pb1-5">';
                guestsHtml += '<img class="guest_img" src="' + item.image + '" alt="">';
                guestsHtml += '<p class="guest_name f14">' + item.name + '</p>';
                guestsHtml += '<p class="guest_intro f12 c8a">' + item.brief + '</p>';
                guestsHtml += '</div>';
            });
            $('#bind_guest').append(guestsHtml);
        }
        $('#bind_guest').removeClass('none');
    }
    //APP内，底部按钮
    function bottomBtn(objLive, type) {
        var bottomBtn = '';
        if (type == 'preview') { 
            if (objLive.ticket > 0) {
                if (objLive.is_purchase == '1') {
                    //已购买
                    if (objLive.subscribe == '1') {
                        //已订阅
                        dhtml='<div class="fix_btn_meet border-8a-radius border2">\
                         <button class="subscribe l border-8a-radius" data-subscribe="1">取消订阅</button>\
                         </div>';
                         $('#share_video').append(dhtml);
                    } else {
                          dhtml='<div class="fix_btn_meet border-8a-radius border2">\
                         <button class="subscribe l border-8a-radius" data-subscribe="0">+订阅</button>\
                         </div>';
                         $('#share_video').append(dhtml);
                    }
                } else {
                    //未购买
                    if (objLive.subscribe == '1') {
                        //已订阅
                         bottomBt = '<div class="fixed_btn_meet border1">\
                      <button class="buy_btn l ">购买</button>\
                        </div>';
                         dhtml='<div class="fix_btn_meet border-8a-radius border2">\
                         <button class="subscribe l border-8a-radius" data-subscribe="1">取消订阅</button>\
                         </div>';
                        $('.share_need').append(bottomBt);
                        $('#share_video').append(dhtml);
                    } else {
                        bottomBt = '<div class="fixed_btn_meet  border1">\
                      <button class="buy_btn l ">购买</button>\
                        </div>';
                        dhtml='<div class="fix_btn_meet border-8a-radius border2">\
                         <button class="subscribe l border-8a-radius" data-subscribe="0">+订阅</button>\
                         </div>';
                        $('.share_need').append(bottomBt);
                        $('#share_video').append(dhtml);
                    }

                }
            } else {
                if (objLive.subscribe == '1') {
                    //已订阅
                    bottomBt = '<div class="fixed_btn_meet border1">\
                      <button class="buy_btn l ">购买</button>\
                        </div>';
                    dhtml='<div class="fix_btn_meet border-8a-radius border2">\
                         <button class="subscribe l border-8a-radius" data-subscribe="1">取消订阅</button>\
                         </div>';
                    $('.share_need').append(bottomBt);
                    $('#share_video').append(dhtml);
                    $('#share_need').addClass('none');
                    $('#nav_every_say').css('margin-top','1.5rem');

                } else {
                     bottomBt = '<div class="fixed_btn_meet  border1">\
                      <button class="buy_btn l ">购买</button>\
                        </div>';
                    dhtml='<div class="fix_btn_meet border-8a-radius border2">\
                         <button class="subscribe l border-8a-radius" data-subscribe="0">+订阅</button>\
                         </div>';
                     $('.share_need').append(bottomBt);
                     $('#share_video').append(dhtml);
                     $('#share_need').addClass('none');
                }
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
       
    function buyTicket(activity_id,ticket_id,price) {
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
  }       $("#containerBox").append(bottomBtn);
        //购买直播
     $('.buy_btn').on('click',function(){
            var activity_id =$('#containerBox').attr('activity_id') ;
            console.log(activity_id);
            var ticket_id=$('#containerBox').attr('ticket_id');
             console.log(ticket_id);
            var price=$('#containerBox').attr('score_price');
             console.log(price);
            buyTicket(activity_id,ticket_id,price);
         })
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
               $(document).on("click", "#_zan_", function() {
                    var subscribe = $(this).data("subscribe");
                    if (subscribe == '0') {
                        subscribe = '1';
                    } else {
                        subscribe = '0';
                    }
                    Live.smszan(id,uid,param.id, subscribe,'live');
                });
                //评论点站
              $(document).on("click", ".zan_pict", function() {
                  $(this).css('backgroundColor','#fff');
                    var subscribe ;
                    var ID=$(this).attr('data-id'); 
                    var args = getQueryStringArgs();
                    var uid = args['uid'] || 0;      
                if($(this).hasClass('_pict_01')){
                    $(this).addClass('_pict_02').removeClass('_pict_01');
                    var mub=$(this).next().text();
                    $(this).next().text(mub-1+2);
                    subscribe=1;
                }else{
                    $(this).addClass('_pict_01').removeClass('_pict_02');
                     subscribe=0;
                       var mub=$(this).next().text();
                      $(this).next().text(mub-1);
                    };
                 Live.commentzan(ID,uid, subscribe);
                });  
            }
        } else if (type == 'living') {
           
        }
    }

    //APP内部调整元素元素位置
    function adjustInnerPage(type, objLive) {
        if (type == 'living') {
            $('#distribution').hide();
            var canPlay = playLiveVideo(objLive, false); //能否播放直播
            if (canPlay) {
                $('#share_video').empty();
                var v_height = $('#share_video').height();
                //切换到互动
                $("#comment_block").click();
                //调用移动端播放器
                showPlayer(objLive.live_url, v_height, objLive.subject, id, objLive.share_reward_unit, objLive.share_reward_num);
            }
            $('#live_detail').addClass('pt31-7875');
        } else if (type == 'liveend') {
            $('#live_detail').addClass('pt27-7875');
        }
    }

    //分享页，增加打开APP和下载APP功能、调整元素位置,微信二次分享功能
    //type='living'时含播放视频逻辑
    function adjustSharePage(type, objActivity, objLive, code) {
        <!--打开app-->
        var openApp = '<div class="install " id="openAppBtn">\
                            <p class="l">打开无界商圈APP，观看完整高清直播 &gt;&gt;</p>\
                            <span class="r" id="openapp"><img src="/images/install_btn1.png" alt=""></span>\
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
            $("#live_detail").addClass('pt26-7875');
        } else if (type == 'living') {
            creatLoadApp(true); //底部1个按钮['下载APP']
            var canPlay = playLiveVideo(objLive, true);
            if (canPlay) {
                $('#share_video').addClass('none');
                $('#video_box').removeClass('none');
                if (localStorage.getItem('isregister') == 'yes') {
                    getLive(objLive.live_url);
                    $('#video_box').removeClass('none');
                    fxLiveWatch(); //分享页观看分销直播送积分
                } else {
                    //快速注册后播放直播
                    fastRegister(); //html
                    fastRegisterEvent(objLive); //注册事件 
                }
                $('#wrapper').addClass('top35-2875'); //评论列表
                $("#live_detail").addClass('pt35-2875');
            } else {
                $('#share_video').addClass('top3-5');
                $("#live_detail").addClass('pt35-2875');
                $('#wrapper').addClass('top35-2875'); //评论列表
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
        // var rHtml = '<div class="remindpart" id="registerpart" style="z-index:197;">\
        //         <div class="content">\
        //             <div class="tiptitle f18 tc remindfontcolor">快速登录/注册,观看直播：</div>\
        //             <div class="userinput remindcolor">\
        //                 <div class="putdiv remindcolor f12 successtitle">登录无界商圈APP，更多高清视频等你来观看</div>\
        //                 <div class="putdiv remindcolor height06"><input type="text" name="phonenumber" value="+86 " placeholder="手机号" id="zcphone"/></div>\
        //                 <div class="putdiv remindcolor height06"><input type="text" name="mescode" placeholder="短信验证码" id="zcyzm"/><button class="ident_code" id="mescode">获取验证码</button></div>\
        //                 <div class="putdiv remindcolor tc"><button class="subbtn f16" id="registerbtn">提交</button></div>\
        //             </div>\
        //             <div class="closepic"></div>\
        //         </div>\
        //     </div>';
        // $('#containerBox').append(rHtml);
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
                    fxLiveWatch(); //看直播送积分
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
            $(document).on('click', '.chakan_requ', function() {
               var brand_id =$(this).data('brand_id');
                window.location.href = labUser.path + "webapp/brand/detail/_v020700?id=" + brand_id + "&uid=" + uid + "&pagetag=08-9&is_share=1";
            });
            //to share page of activity-detail
            $(document).on('click', '.activity', function() {
                var id = $(this).data('activity_id');
                window.location.href = labUser.path + "webapp/activity/detail/_v020700?id=" + id + "&uid=" + uid + "&makerid=0&position_id=0&is_share=1";
            });
        } else {
            if (isiOS) {
                //to brand-detail
                $(document).on('click', '.chakan_requ', function(){
                   var brand_id =$(this).data('brand_id');
                    pushToBrandDetail(brand_id);
                });
            } 
            else {
                //to brand-detail
                $(document).on('click', '.chakan_requ', function() {
                    var brand_id =$(this).data('brand_id');
                    window.location.href = labUser.path + "webapp/brand/detail/_v020700?id=" + brand_id + "&uid=" + uid + "&pagetag=08-9";
                });
            }

            //to activity detail
            $(document).on('click', '.activity', function() {
                var id = $(this).data('activity_id');
                window.location.href = labUser.path + "webapp/activity/detail/_v020700?id=" + id + "&uid=" + uid + "&pagetag=02-2&makerid=0&position_id=0";
            });
        }


    //分享页，分销观看直播送分
    function fxLiveWatch() {
        if (is_fx && watchTime) {
            var sid = setTimeout(function() {
                clearTimeout(sid);
                disfx(origin_mark, 'watch', 0, origin_code);
            }, watchTime);
        }
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

    //直播回放、咨询详情页
    function liveEndEvent(shareFlag) {
        if (shareFlag) {
            //直播回放
            $(document).on('click', '.livevideo', function() {
                var livevideo_id = $(this).data('livevideo_id');
                window.location.href = labUser.path + "webapp/vod/detail/_v020502?id=" + livevideo_id + "&uid=" + uid + "&pagetag=05-4&is_share=1";
            });
            //资讯
            $(document).on('click', '.livemessage', function() {
                var message_id = $(this).data('message_id');
                window.location.href = labUser.path + "webapp/headline/detail?id=" + message_id + "&pagetag=02-4&is_share=1";
            });
        } else {
            //直播回放
            $(document).on('click', '.livevideo', function() {
                var livevideo_id = $(this).data('livevideo_id');
                window.location.href = labUser.path + "webapp/vod/detail/_v020502?id=" + livevideo_id + "&uid=" + uid + "&pagetag=05-4";
            });
            //资讯
            $(document).on('click', '.livemessage', function() {
                var message_id = $(this).data('message_id');
                window.location.href = labUser.path + "webapp/headline/detail?id=" + message_id + "&pagetag=02-4";
            });
        }
    }

    //分享页，【直播中】时'onebtn=true'，只有下载按钮；直播预告时多一个【设置直播预约】按钮
    function creatLoadApp(onebtn) {
        var loadApp = '';
        if (onebtn) {
            loadApp = '<div class="fixed_btn" id="loadAppBtn">\
                          <button class="signup" id="loadapp" style="width:100%;float: right;background-color: #ff5a00;color:#fff;">下载APP</button>\
                       </div>';
        } else {
            loadApp = '<div class="fixed_btn" id="loadAppBtn">\
                          <button class="reserve"  id="reserve" style="width:50%;background-color: white;color:#6bc24b;">设置直播提醒</button>\
                          <button class="signup" id="loadapp" style="width:50%;float: right;background-color: #ff5a00;color:#fff;">下载APP</button>\
                       </div>';
        }
        $("#containerBox").append(loadApp); //下载APP
    }

    //分享页，预约直播\预约成功
    function remindLive(objLive) {
        $('#livename').html('直播名称：' + objLive.subject );
        $('#livetime').html('直播开始时间：' + unix_to_fulltime(objLive.begin_time) );
        // var remindHtml = '<div class="remindpart none" id="liveremind" style="z-index:196;">\
        //             <div class="content">\
        //                 <div class="tiptitle f18 tc remindfontcolor">设置直播提醒</div>\
        //                 <div class="userinput remindcolor">\
        //                     <div class="f12 putdiv">\
        //                         <div class="pdiv">\
        //                             <p id="livename">直播名称：' + objLive.subject + '</p>\
        //                             <p id="livetime">直播开始时间：' + unix_to_fulltime(objLive.begin_time) + '</p>\
        //                         </div>\
        //                     </div>\
        //                     <div class="putdiv remindcolor f12 tiptexts">可以订阅该场直播，我们将在 直播开始前30分钟 以短信发送直播提醒消息</div>\
        //                     <div class="putdiv remindcolor height06">\
        //                         <input type="text" name="phonenumber" placeholder="手机号" id="yyphone"/>\
        //                     </div>\
        //                     <div class="putdiv remindcolor height06">\
        //                         <input type="text" name="mescode" placeholder="短信验证码" id="yyyzm"/>\
        //                         <button class="ident_code" id="getcode">获取验证码</button>\
        //                     </div>\
        //                     <div class="putdiv remindcolor tc">\
        //                         <button class="subbtn f16" style="margin-top: 1rem;" id="yysubmit">提交</button>\
        //                     </div>\
        //                 </div>\
        //                 <div class="closepic"></div>\
        //             </div>\
        //           </div>';
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
        $('#yysubmit').on('click', function() {
            var params = {};
            params.tel = $('#yyphone').val().split(' ')[1];
            params.code = $('#yyyzm').val();
            param.nation_code = $('#yyphone').val().split(' ')[0];
            params.live_id = id;
            var url = labUser.api_path + '/live/sharesubscibe';
            ajaxRequest(params, url, function(data) {
                if (data.status) { //隐藏提醒
                    $('#liveremind').addClass('none');
                    $('#yyphone').val('');
                    $('#yyyzm').val('');
                    if (data.message == '1') {
                        $('#remindsuccess').removeClass('none');
                    } else if (data.message == '2') {
                        $('#membertips').html('欢迎成为无界商圈一员，账号、密码为预约所填手机号码。请及时登录无界商圈并对密码进行修改。');
                        $('#remindsuccess').removeClass('none');
                    }
                }
            });
        });
    }

    //加载下一页评论、最新评论
    function commentsEvent() {
        //加载更多评论
        $('#morecm').on('click', function() {
            var page = $('#pullUp').data('pagenow');
            page++;
            var param = {
                "id": id,
                "uid": uid,
                "commentType": 'Live',
                "page": page,
                "page_size": pageSize
            };
            Comment.getCommentList(param, null, 'live');
        });
        //加载最新评论v020500版本
        $(document).on('click','.refreshpic, .refreshpic1', function() {
            var refresh = $(this);
            refresh.css('transform', 'rotate(90deg)');
            var parameter = {
                "type": param.type,
                "uid": param.uid,
                "id": param.id,
                "fromId": $('#commentflag').data('maxid'),
                "update": "new",
                "fecthSize": 0
            };
            Comment.getFreshList(parameter);
            setTimeout(function() {
                refresh.css('transform', 'rotate(0deg)');
            }, 1000);

        });

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
        picHtml += '<span class="awardpicture fr"></span>';
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
    function increaseViewn(id, type) {
        var param = {};
        param["id"] = id;
        param["num"] = 1;
        param['type'] = type;
        var url = labUser.api_path + '/live/incre';
        ajaxRequest(param, url, function(data) {
            if (data.status) {}
        });
    }

    //分享页,分销接口1
    function disfx(share_mark, type, uid, code) {
        var url = labUser.api_path + "/share/collect-score/_v020500",
            share_mark = share_mark || $('#livesubject').data('share_mark');
        ajaxRequest({ share_mark: share_mark, type: type, uid: uid, relation_id: code }, url, function(data) {});
    }

    //分享页，分享记录入库
    function fxlogs(uid, content, content_id, source, code, share_mark) {
        var url = labUser.api_path + "/share/share/_v020500";
        ajaxRequest({
            uid: uid,
            content: content,
            content_id: content_id,
            source: source,
            code: code,
            share_mark: share_mark
        }, url, function(data) {});
    }

    //二次分享、观看直播调用
    function sencondShare(type) {
        if ($('#livesubject').data('distribution_id') > 0) {
            var getcodeurl = labUser.api_path + '/index/code/_v020500';
            ajaxRequest({}, getcodeurl, function(data) {
                var newcode = data.message; //code
                var logsurl = labUser.api_path + "/share/share/_v020500";
                ajaxRequest({
                    uid: '0',
                    content: 'live',
                    content_id: id,
                    source: 'weixin',
                    code: newcode,
                    share_mark: origin_mark
                }, logsurl, function(data) {
                    disfx(origin_mark, type, 0, newcode);
                });
            });
        }
    };
 
//加载评论列表
         
            function getComments(param,id,uid,type){
                var params={};
                    params['id']=id;
                    params['uid']=uid;
                    params['type']=type;
                    params['page']=param.page;
                    params['page_size']=param.page_size;
                    params['section']=param.section;
                var url=labUser.api_path+'/comment/list';
                ajaxRequest(params,url,function(data){
                    if(data.status){
                        var comHtml='',imgs='';
                        var obj=data.message.data;
                        console.log($('.com_num'));
                        $('.com_num').text(data.message.all_count); 
                        $.each(obj,function(index,item){
                            comHtml+='<li id="m_c_m" data-id="'+item.id+' "><img src="'+item.avatar+'" alt="header" class="l"><div class="publisher r">';
                            comHtml+='<p class="b lh3-3 m0"><span class="f16 color666" style="float:left;margin-top:1.8rem">'+item.c_nickname+'</span><span class="r laub lh3-3" style="float:right;margin-top:1.8rem">';
                            //评论人是否点赞
                            if(item.is_zhan==1){
                                comHtml +='<span  class="zan_pict _pict_02"    data-id="'+item.id+'"></span><span class="zan_net" style="color:#999;font-size:1rem;float:right;margin:0.3rem 0.3rem" >'+item.likes+'</span></span></p>';
                            }else{
                                comHtml +='<span  class="zan_pict _pict_01"   data-id="'+item.id+'"></span><span class="zan_net" style="color:#999;font-size:1rem;float:right;margin:0.3rem 0.3rem">'+item.likes+'</span></span></p>';
                            };
                            if(item.images.length > 0){
                                $.each(item.images,function(i,j){
                                    // imgs+='<img src="'+j.url +'">';
                                    imgs+='<img src="' + j + '" alt="" class="onimgs" data-picindex="' + i + '">';
                                })
                                comHtml+='<p class="c8a f12" style="width:100%;min-height:5rem;text-align:left;padding-top:4rem;padding-bottom:3rem;color:#8a869e;">'+item.content+'</p><p class="comment_pic">'+imgs+'</p>';
                            }else{
                                comHtml+='<div class="c8a f12" style="width:100%;min-height:5rem;text-align:left;padding-top:4rem;padding-bottom:3rem;color:#8a869e;">'+item.content+'</div>';
                            }
                            comHtml += '<p class="time" style="text-align:left;font-size:1rem;color:#ccc;">'+item.created_at+'</p></div><div class="clearfix"></div></li>';
                        });
                        if(params.page==1){
                            $("#comm").html(comHtml);
                        }else{
                            $("#comm").append(comHtml);
                            if(obj.length<5){
                                $('.getMore').text('没有更多了...').attr('disabled','true');
                            }
                        }
                         if(data.message.all_count==0){
                              $('.no_data').removeClass('none');
                              $("#comm").addClass('none'); 
                            }else{
                              $("#comm").removeClass('none');
                              $('.no_data').addClass('none'); 
                            }                                   
                         if(data.message.all_count<=5){
                            $('.getMore').addClass('none');
                            $("#comm").css('margin-bottom','11.1rem');        
                         }
                    }else{
                        if($('#comm>li').length==0){
                            $('#comm').html('<p style="padding:1rem 0 2rem" class="c8a">暂无评论</p>').css('margin-bottom','11.1rem');
                            // $('.com_num').addClass('none');
                            $('.getMore').addClass('none');
                        }else if($('#comm>li').length>5){
                            $('.getMore').text('没有更多了...').attr('disabled','true');

                        }
                    }
                })

            };
            getComments(param,id,uid,'Live');
           $(document).on('click','.getMore',function(){
                param.page++;
                getComments(param,id,uid,'Live');
              
            })
            // 评论点赞
            function zan(uid,id,type,ele,em){
                var param ={};
                    param['uid'] = uid ;
                    param['id'] = id;
                    param['type'] = type;
                var url = labUser.api_path + '/comment/zhan';
                ajaxRequest(param,url,function(data){
                    if(data.status){
                        if(type){  
                            ele.attr('src','/images/020502/zan.png');//点赞
                            ele.attr('data-zan',1);
                            em.text(zannum(parseInt(em.attr('data-zannum'))+1));//点赞数加一
                            em.attr("data-zannum",parseInt(em.attr('data-zannum'))+1) ; 
                        }else{
                            ele.attr('src','/images/littlewz.png');
                            ele.attr('data-zan',0)
                            em.text(zannum(parseInt(em.attr('data-zannum'))-1));//点赞数减一
                            em.attr("data-zannum",parseInt(em.attr('data-zannum'))-1) ;
                        }
                    }
                })
            }
        $(document).on('click','#publish',function(){
            if(shareFlag){
            $('#commentback').removeClass('none');
            $('#comtextarea').focus();
            if($('#comtextarea').val()==''){
            $('#subcomments').css('backgroundColor','#ea5520');
            getComments(param,id,uid,'Live');
            }
            }else{
                uploadpic(param.id,'Live',false);
                // $('#commentback').removeClass('none');
                }

            });
            $(document).on('click ','#subcomments',function(){
                param.content=$('#comtextarea').val();
                console.log(param.content);
                if(shareFlag){ getComments(param,id,uid,'Live');
                 $('body').scrollTop(0);          
                 };
                param.page=1;
                if(shareFlag){
                    param.uid=0;
                }
                addComment(param,shareFlag)
            });



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
