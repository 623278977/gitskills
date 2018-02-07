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
            "page":page,
            "page_size": pageSize
            };
    var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
    var messageDetail = {
        init: function () {
            var param = {};
                param["id"]=id;
                param["agent_id"]=agent_id;
                param['page']=page;
                param['page_size']= pageSize;
            var url=labUser.agent_path+'/new-agent-details/new-agent-details/_v010005';
            ajaxRequest(param, url, function (data) {
            if (data.status) {
               messageDetail.data(data.message);
               messageDetail.brandlist(data.message.brand_data);
               messageDetail.videolist(data.message.video_data);
               $('#act_container').removeClass('none');
                forShareBy(data.message);
             }
            });
        },
        data:function(obj){
             $('.ui_professor_pict').css('background','url('+obj.banner_img+') no-repeat center');
             $('.ui_professor_text').find('p').eq(0).text((obj.title.lenght<20?obj.title:obj.title.substring(0,20)+'…'));
             $('.ui_professor_text').find('p').eq(1).find('span').eq(0).text(obj.summary).next().text(obj.browse_num+'人浏览');
             $('.ui-text-detail').html((obj.content).replace(/http:/g,'https:'));
             $('.ui-text-detail').find('p').css({'width':'100%!important','word-break':'break-all'});
             getpict('.ui-text-detail');
             // //分享用的数据
             $('#share').data('title',obj.title).data('img',obj.shar_img).data('name',obj.agent_name);
             $('.ui-zan-zhaun li').eq(0).find('p').eq(1).html(obj.zan_num);
             if(obj.is_zan==0){
                 $('.ui-zan-zhaun li').eq(0).find('img').attr('src','/images/agent/weizan.png');   
             }else if(obj.is_zan==1){
                 $('.ui-zan-zhaun li').eq(0).find('img').attr('src','/images/agent/ui_pict.png'); 
                 $('.ui-zan-zhaun li').eq(0).find('button').attr('disabled',true).css('border','1px solid#2873ff');   
             }
            
        },
        brandlist:function(obj){
            if(obj!=''){
                $.each(obj,function(k,v){
                      var html='';
                          html+='<ul class="ui-brand-zone " data-id="'+v.brand_id+'">\
                                   <li><img class="ui-brand-pict" src="'+v.brand_logo+'"/></li>\
                                   <li>\
                                       <p class="b f14 color333 ui-nowrap-multi1 margin-ui">'+v.brand_name+'</p>';
                      if(v.brand_slogan){
                          html+=' <p class="margin-ui f11 color999 none">'+v.brand_slogan+'</p>'; 
                      }else{
                          html+=' <p class="margin-ui f11 color999 none">'+v.brand_slogan+'</p>'; 
                         }
                          html+=' <p class="margin-ui f12 color666">行业分类<span class="color333" style="padding-left: 0.5rem">'+v.brand_cate+'</span></p>\
                                       <p class="margin-ui f12 color666">启动资金<span  style="padding-left: 0.5rem;color:#ff4d64">'+v.money_limit+' '+'(万)</span></p>';
                          html+='<p>';
                          var str='';
                          if(v.brand_keyword.length>0){
                            for(var i=0;i<v.brand_keyword.length;i++){
                                    str+='<span class="border-8a-radius ui-border-radius-8a circle">'+v.brand_keyword[i]+'</span>';
                            }
                          }
                          html+=str;
                          html+='</p>\
                                   </li>\
                                   <li>\
                                       <p style="margin: 3rem 0 0"></p>\
                                       <p class="f18 b" style="margin:0 0 0"><span  style="color:#ff4d64">￥'+parseInt(v.brand_commission)+'</span></p>\
                                       <p class="f11 color999">成单提成最高金额</p>\
                                   </li>\
                                 </ul>';
                          html+='<div class="clear fline" style="width:100%;height:0.0001rem"></div>';
                         $('#brandcon').append(html);
                })
            }else{
                  $('#brandcon').addClass('none');
            }
        },
        videolist:function(obj){
                          if(obj!=''){
                            $.each(obj,function(k,v){
                                var html='';
                                    html+='<ul class="ui-video-zone" data-id="'+v.video_id+'">\
                                                 <li><img class="ui-video-pict" src="'+v.video_image+'"/></li>\
                                                 <li>\
                                                     <p class="b f14 color333 ui-nowrap-multi1">'+v.video_name+'</p>\
                                                     <p></p>\
                                                     <p class="margin-ui2 f11 color999 ">录制时间：<span>'+messageDetail.unix(v.created)+'</span></p>\
                                                 </li>\
                                          </ul>\
                                          <div class="fline" style="background: #fff"></div>';
                                    $('#videocon').append(html);
                            })
                          }else{
                                     $('#videocon').addClass('none');
                          }
        },
        parise:function(id,agent_id){
                                    var param={};
                                        param['id']=id;
                                        param['agent_id']=agent_id;
                                    var url=labUser.agent_path+'/new-agent-details/new-agent-zans/v010005';
                                      ajaxRequest(param, url, function (data) {
                                        if (data.status){
                                           
                                          }
                                        });
        },
        recordmumber:function(id){
                                    var param={};
                                        param['id']=id;
                                    var url=labUser.agent_path+'/new-agent-details/new-agent-shards/v010005';
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
                      return  M + '月' + D + '日'+' '+h+':'+m ;
       },
        getcommentList:function(id,uid,page,page_size){
                        var param={};
                            param['id']=id;
                            param['agent_id']=uid;
                            param['page']=page;
                            param['page_size']=page_size;
                        var url=labUser.agent_path+'/new-agent-details/new-agent-comments/_v010005';
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
                                                <p class="color666 f13 comment_name">'+v.nickname+'</p>\
                                                <p class="f15 relative inline-block comment_content">\
                                                <span class="comment_text">'+v.content+'</span>';
                                      if(v.c_uid == uid){
                                          html += '<span class="comment_tip none" style="width:6.5rem;"><em class="copy" style="width:100%;">复制</em></span></p>'
                                      }else{
                                          html += '<span class="comment_tip none"><em class="reply" data-id="'+v.id+'" data-type="newmes">回复</em><em class="copy">复制</em></span></p>';
                                      }
                                      if(v.pId){
                                          html += '<div class="bgcolor f13 p1 mb1"><p><span class="c2873ff">'+v.p_nickname+'</span>：</p>\
                                                  <p class="color666 break-word ui-nowrap-multi">'+v.pContent+'</p></div>';
                                      }          
                                         html+='<p class="color999 f11 ui-zan-zone" data-id="'+v.id+'">'+messageDetail.unix(v.created_at_time)+'<span class="fr"><img class="ui-pict20" src="'+(v.is_zhan==1?'/images/agent/zan.png':'/images/agent/weizan.png')+'" />\
                                                  <span class="zan_num" style="padding: 0.5rem">'+v.likes+'</span></span>\
                                                </p>\
                                              </li>\
                                        </ul>';
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
    messageDetail.init();
    messageDetail.getcommentList(Params.id,Params.uid,Params.page,Params.page_size);
    console.log(Params);
    //文章点赞
     $('.ui-forzan').on('click',function(){
                           var dian_zan=$(this).find('img').attr('src')==('/images/agent/weizan.png')?true:false;
                           var cont= $(this).find('p').eq(1).text();
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
        if(shareFlag){
              tips('请至APP发表评论')
            }else{
               uploadpic(id,'new_agent_detail', true);  
            }    
      })

      // 评论回复与复制
      commonReply(id);
      //转发详情页；
      $('.ui-zan-zhaun li').eq(1).find('button').on('click',function(){
            if(shareFlag){
              tips('请至APP转发')
            }else{
                showShare(); 
                messageDetail.recordmumber(id);
            }        
     }) 
     //点击加载更多
     $('.ui-get-more').on('click',function(){
       Params.page++;  
       messageDetail.getcommentList(Params.id,Params.uid,Params.page,Params.page_size); 
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
     //跳转至品牌详情页
     $(document).on('click','.ui-brand-zone',function(){
            var id=$(this).data('id');
            window.location.href=labUser.path + "webapp/agent/brand/detail/_v010004?id="+id+"&agent_id="+agent_id; 
      }) 
     //跳转至视频详情页
     $(document).on('click','.ui-video-zone',function(){
            var id=$(this).data('id');
            window.location.href=labUser.path + "webapp/agent/vod/detail?agent_id="+agent_id+"&id="+id; 
      }) 
     //分享专用
    function forShareBy(selfObj){
        if (shareFlag) {
            $('#installapp').removeClass('none');
            if (is_weixin()) {
                $(document).on('tap', '#loadapp,#openapp', function () {
                    var _height = $(document).height();
                    $('.safari').css('height', _height);
                    $('.safari').removeClass('none');
                });
                $(document).on('tap', '.safari', function () {
                    $(this).addClass('none');
                });
                var wxurl = labUser.api_path + '/weixin/js-config';
                var desptStr = removeHTMLTag(selfObj.contents);
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
                                title: selfObj.title, // 分享标题
                                link: location.href, // 分享链接
                                imgUrl: selfObj.shar_img, // 分享图标
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
                                title: selfObj.title,
                                desc: nowhitespaceStr,
                                link: location.href,
                                imgUrl: selfObj.shar_img,
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
                         $(document).on('tap', '#openapp', function () {
                                oppenIos();
                            });
                            /**下载app**/
                            $(document).on('tap', '#loadapp', function () {
                                window.location.href = 'https://itunes.apple.com/cn/app/id1282277895';
                            });
                     }else if (isAndroid) {
                         $(document).on('tap ', '#loadapp', function () {
                            window.location.href = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.tyrbl.agent';
                        });
                        $(document).on('tap', '#openapp', function () {
                           openAndroid();
                       });
                    }
                }//2
           }
      }//forShareBy;
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