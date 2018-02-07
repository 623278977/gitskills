//ByHongky
    new FastClick(document.body);
    var urlPath = window.location.href,
        args = getQueryStringArgs(),
        id = args['id'],
        agent_id=args['agent_id']||0,
        page=1,
        pageSize=3;
    var Params ={
            "id": id,
            "uid":agent_id,
            "type":'WeChat',
            "page":page,
            "page_size": pageSize
            };
    var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
    var messageDetail = {
        init: function (id) {
            var param = {};
                param["id"]=id;
                param["agent_id"]=agent_id;
            var url=labUser.agent_path+'/workspace/we-chat-detail/_v010100';
            ajaxRequest(param, url, function (data) {
            if (data.status) {
              forShareBy(data.message);
               messageDetail.data(data.message);
               $('#act_container').removeClass('none');
             }
            });
        },
        data:function(obj){
             $('.ui-titletext').html(obj.detail.title);
             $('.ui-public-time').html(obj.detail.date);
             $('.ui-author li').eq(0).find('img').attr('src',(obj.detail.teacher_avatar?obj.detail.teacher_avatar:'/images/default/avator-m.png'));
             $('.ui-author li').eq(1).find('p').eq(0).html(obj.detail.appellation);
             $('.ui-author li').eq(1).find('p').eq(1).html('作者'+' '+obj.detail.teacher);
             $('.ui-text-detail').html(obj.detail.contents.replace(/http:/g, 'https:'));
             $('.ui-fixed-botton').data('id',obj.detail.id);
             $('.ui-forzan').data('id',obj.detail.id);
             if(!obj.detail.teacher){
                $('.ui-author').addClass('none')
             }
             getpict('.ui-text-detail');
             commonReply(obj.detail.id);
             //分享用的数据
             $('#share').data('title',obj.detail.title).data('img',obj.detail.share_image).data('content',obj.detail.contents).data('sharecontent',obj.detail.share_summary);
             if(obj.recommend!=''){
                $.each(obj.recommend,function(k,v){
                var html='';
                    html+='<p data-id="'+v.id+'"><a class="ui-nowrap-multi1">'+v.title+'</a></p>';
                    $('.ui-link-title').append(html);
                })  
             }
            $('.ui-zan-zhaun li').eq(0).find('p').eq(1).html(obj.detail.count_zan);
             if(obj.detail.is_zan==0){
                 $('.ui-zan-zhaun li').eq(0).find('img').attr('src','/images/agent/weizan.png');   
             }else if(obj.detail.is_zan==1){
                 $('.ui-zan-zhaun li').eq(0).find('img').attr('src','/images/agent/ui_pict.png'); 
                 $('.ui-zan-zhaun li').eq(0).find('button').attr('disabled',true).css('border','1px solid#2873ff');   
             }
            
        },
        parise:function(id,agent_id){
                                    var param={};
                                        param['id']=id;
                                        param['agent_id']=agent_id;
                                        param['type']='we_chat';
                                    var url=labUser.agent_path+'/comment/add-zan/_v010100';
                                      ajaxRequest(param, url, function (data) {
                                        if (data.status){
                                           
                                          }
                                        });
        },
        stattic:function(id){
                                     var param={};
                                         param['id']=id;
                                     var url=labUser.agent_path+'/workspace/we-chat-share/_v010100';
                                      ajaxRequest(param, url, function (data) {
                                        if (data.status){
                                           
                                          }
                                        });
        },
        unix:function(unix){
                      var newDate = new Date();
                          newDate.setTime(unix * 1000);
                      var Y = newDate.getFullYear(),
                          M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1,
                          D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate(),
                          h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours(),
                          m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes(),
                          s = newDate.getSeconds() < 10 ? '0' + newDate.getSeconds() : newDate.getSeconds();
                      return  M + '月' + D + '日' ;
       },
        getcommentList:function(id,uid,type,page,page_size){
                        var param={};
                            param['id']=id;
                            param['uid']=uid;
                            param['type']='WeChat';
                            param['page']=page;
                            param['page_size']=page_size;
                        var url=labUser.agent_path+'/comment/comment-list/_v010100';
                        ajaxRequest(param, url, function (data) {
                        if (data.status){
                          if(data.message.all_count==0){
                            $('#nocommenttip2').removeClass('none');
                            $('.ui-get-more').hide();
                          }
                          if(data.message.data!=''){
                            $.each(data.message.data,function(k,v){
                                 var html='';
                                     html+='<ul class="ui-commnet-list clear">\
                                              <li><img class="ui-pict10" src="'+(v.avatar?v.avatar:'/images/default/avator-m.png')+'"/></li>\
                                              <li class="fline">\
                                                <p></p>\
                                                <p class="color666 f13 comment_name">'+v.c_nickname+'</p>\
                                                <p class="f15 relative inline-block comment_content">\
                                                <span class="comment_text">'+v.content+'</span>';
                                if(v.c_uid == uid){
                                    html += '<span class="comment_tip none" style="width:6.5rem;"><em class="copy" style="width:100%;">复制</em></span></p>'
                                }else{
                                    html += '<span class="comment_tip none"><em class="reply" data-id="'+v.id+'" data-type="wechat">回复</em><em class="copy">复制</em></span></p>';
                                }
                                if(v.pId){
                                    html += '<div class="bgcolor f13 p1 mb1"><p><span class="c2873ff">'+v.p_nickname+'</span>：</p>\
                                            <p class="color666 break-word ui-nowrap-multi" >'+v.pContent+'</p></div>';
                                }
                                    html +='<p class="color999 f11 ui-zan-zone" data-id="'+v.id+'">'+messageDetail.unix(v.created_at_time)+'<span class="fr"><img class="ui-pict20" src="'+(v.is_zhan==0?'/images/agent/weizan.png':'/images/agent/zan.png')+'" />\
                                                  <span class="zan_num" style="padding: 0.5rem">'+v.likes+'</span></span>\
                                                </p>\
                                              </li>\
                                        </ul>'
                                                ;
                                    if(param.page==1){
                                      $('.ui_list').append(html); 
                                     if(data.message.data.length==0){
                                        $('.ui-get-more').addClass('none');
                                        $('#nocommenttip2').removeClass('none');
                                      }
                                       if(data.message.data.length<3){
                                        $('.ui-get-more').text('已加载全部评论').attr('disabled',true);
                                       }else{
                                        $('.ui-get-more').text('点击加载更多').removeAttr('disabled');
                                        }
                                    }else{
                                       $('.ui_list').append(html);
                                      if(data.message.data.length<3){
                                       $('.ui-get-more').text('已经加载全部评论').attr('disabled',true);
                                     }   
                                    }
                               })
                        }else{
                                $('.ui-get-more').text('已加载全部评论').attr('disabled',true);
                            
                        }
                        }
                    });
                       
        }

    };
    messageDetail.init(id,agent_id);
    messageDetail.getcommentList(Params.id,Params.uid,Params.type,Params.page,Params.page_size);
    console.log(Params);
    //文章点赞
     $('.ui-forzan').on('click',function(){
                           var dian_zan=$(this).find('img').attr('src')==('/images/agent/weizan.png')?true:false;
                           var cont= $(this).find('p').eq(1).text();
                           var id=$(this).data('id');
                           if(dian_zan){
                             if(shareFlag){
                               tips('请至APP点赞')
                             }else{
                                messageDetail.parise(id,agent_id); 
                               $(this).find('img').attr('src','/images/agent/ui_pict.png');
                               $(this).find('p').eq(1).text(cont-1+2);
                               $(this).css('border','1px solid#2873ff');
                             }     
                            }
     })
     //底部评论
      $('.ui-fixed-botton').on('click',function(){
        var id=$(this).data('id');
        if(shareFlag){
              tips('请至APP发表评论')
            }else{
               uploadpic(id, 'WeChat', true,'','');  
            }    
      })


      //转发详情页；
      $('.ui-zan-zhaun li').eq(1).find('button').on('click',function(){
            if(shareFlag){
              tips('请至APP转发')
            }else{
                showShare(); 
                messageDetail.stattic(id);
            }        
     }) 
     //相关文章跳转
     $(document).on('click','.ui-link-title p',function(){
        var id=$(this).data('id');
        if(shareFlag){
         window.location.href=labUser.path+'webapp/agent/wechatdetail/detail/_v010100?id='+id+'&agent_id='+agent_id+'&is_share=1';
        }else{
          window.location.href=labUser.path+'webapp/agent/wechatdetail/detail/_v010100?id='+id+'&agent_id='+agent_id;   
        }
     })
     //点击加载更多
     $('.ui-get-more').on('click',function(){
       Params.page++;  
       messageDetail.getcommentList(Params.id,Params.uid,Params.type,Params.page,Params.page_size); 
     })
     //评论点赞或取消；
     $(document).on('click','.ui-zan-zone',function(){
                var id=$(this).data('id');
                var zan_num=$(this).find('.zan_num').text();
                if(!shareFlag){
                    if($(this).find('img').attr('src')=='/images/agent/weizan.png'){
                        $(this).find('img').attr('src','/images/agent/zan.png');
                        $(this).find('.zan_num').text(zan_num-1+2);
                        zanOrzan(id,agent_id,1);
                    }else{
                        $(this).find('img').attr('src','/images/agent/weizan.png');
                        $(this).find('.zan_num').text(zan_num-1);
                        zanOrzan(id,agent_id,0);
                    }
                }else{
                    tips('请至APP点赞')
                }

     })
     function zanOrzan(id,agent_id,type){
                var param={};
                    param['id']=id;
                    param['uid']=agent_id;
                    param['type']=type;
                var url=labUser.agent_path+'/comment/assign-user-comment-add-zan/_v010005';
                ajaxRequest(param, url, function (data) {

                })
     }
     //分享专用
    function forShareBy(selfObj){
        if (shareFlag) {
            $('#installapp').removeClass('none');
            $('.ui-fixed-botton').addClass('none');
            $('#loadapp').removeClass('none');
            if (is_weixin()) {
                $(document).on('tap', '#openapp,#loadapp', function () {
                    var _height = $(document).height();
                    $('.safari').css('height', _height);
                    $('.safari').removeClass('none');
                });
                $(document).on('tap', '.safari', function () {
                    $(this).addClass('none');
                });
                var wxurl = labUser.api_path + '/weixin/js-config';
                var desptStr = removeHTMLTag(selfObj.detail.contents);
                var nowhitespace = desptStr.replace(/&nbsp;/g, '');
                var despt = cutString(desptStr, 60);
                var nowhitespaceStr = cutString(nowhitespace, 60);
                ajaxRequest({url: location.href}, wxurl, function (data){
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
                                title: selfObj.detail.title, // 分享标题
                                link: location.href, // 分享链接
                                imgUrl: selfObj.detail.share_image, // 分享图标
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
                                title: selfObj.detail.title,
                                desc: nowhitespaceStr,
                                link: location.href,
                                imgUrl: selfObj.detail.share_image,
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
            }else{//2
                    if(isiOS) {
                         $(document).on('click', '#openapp', function () {  
                            oppenIos();
                            });
                      }else if (isAndroid) {
                         $(document).on('click', '#openapp', function () {
                            openAndroid();
                        });
                    }
                }//2
           }
      }//forShareBy;
    $(document).on('click','#loadapp',function(){
        if(isAndroid){
            window.location.href = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.tyrbl.agent'; 
        }else if(isiOS){
             window.location.href = 'https://itunes.apple.com/cn/app/id1282277895';
        }
    })
    function tips(e){
            $('.tips').text(e).removeClass('none');
            setTimeout(function() {
                $('.tips').addClass('none');
            }, 3000);
        };

    //打开本地--Android
         function openAndroid(){
            var strPath = window.location.pathname;
            var strParam = window.location.search.replace(/is_share=1/g, '');
            var appurl = strPath + strParam;
            window.location.href = 'openagent://welcome' + appurl;
        }
        function oppenIos(){
             var strPath = window.location.pathname,
                 strParam = window.location.search.replace(/&is_share=1/g, ''),
                 appurl=labUser.path+strPath + strParam;
            window.location.href = 'openagent://'+ appurl;
        };
        function  uploadpic(id, type, istext,upid,name){
          if (isAndroid) {
                javascript:myObject.uploadpic(id, type, istext,upid,name);
              }else if (isiOS) {
                        var message = {
                        method : 'uploadpic',
                        params : {
                          "id":id,
                          "type":type,
                          "istext":istext,
                          "upid":upid,
                          "name":name
                        }
                    }; 
                 window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
            }
        }