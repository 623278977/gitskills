//created byhongky
Zepto(function (){
    new FastClick(document.body);
    var page=1;
    var page_size=100;
    var accept=0;
    var refuse=0;
    var again=0;
    var urlPath = window.location.href,
        args = getQueryStringArgs(),
        activity_id = args['id'],
        agent_id = args['agent_id'] || '0';
    var askDetail = {
        detail: function (agent_id) {
            var param = {};
                param["agent_id"] = agent_id;
                param['type']=0;
                param['page']=page;
                param['page_size']=page_size;
            var url=labUser.agent_path+'/user/activity-invitation/_v010000';
            ajaxRequest(param, url, function (data) {
            if (data.status) {
              waitConfirmation(data.message);
              $('#act_container').removeClass('none');
            }//data.status
            })// ajaxRequest
            }//detail方法
        }//activityDetail对象 
     askDetail.detail(agent_id);
    function tabChange(){
      $('.ui_tab li').eq(0).on('click',function(){
        $(this).addClass('b blue').siblings().removeClass('b blue');
        $('.ui_container_wait').removeClass('none');
        $('.ui_container_accept').addClass('none');
        $('.ui_container_refuse').addClass('none');
      })
      $('.ui_tab li').eq(1).on('click',function(){
        $(this).addClass('b blue').siblings().removeClass('b blue');
        $('.ui_container_wait').addClass('none');
        $('.ui_container_accept').removeClass('none');
        $('.ui_container_refuse').addClass('none');
        accept++;
        if(accept==1){
         acceptConfirmation(); 
        }    
      })
      $('.ui_tab li').eq(2).on('click',function(){
        $(this).addClass('b blue').siblings().removeClass('b blue');
        $('.ui_container_wait').addClass('none');
        $('.ui_container_accept').addClass('none');
        $('.ui_container_refuse').removeClass('none');
        refuse++;
        if(refuse==1){
          refuseConfirmation()
        }      
      })
    }
    tabChange();
    //等待对方确认
    function waitConfirmation(obj){
    if(obj.length>0){
      $.each(obj,function(k,v){
        var html='';
            html+='<div class="ui_top_time">\
                    <div style="width:100%;height:2rem"></div>\
                    <center><div class="ui_show_time" >'+unix_to_pretime(v.updated_at)+'</div></center>\
                 </div>';
            html+='<section class="ui_invite_container" data-id="'+v.id+'"><div class="ui_infor"><ul class="ui_infor_tab">';
            html+='<li class="f12 color333">受邀人</li>';
            html+='<li>\
                    <p class=" f12 b margin05">'+v.nickname+'</p>';
            if(v.relation==1){
            html+='<p class="f12 margin05">派单关系</p>';  
            }else if(v.relation==2){
            html+='<p class="f12 margin05">邀请关系</p>';  
            }else if(v.relation==3){
            html+='<p class="f12 margin05">邀请、派单关系</p>';  
            }else if(v.relation==4){
            html+='<p class="f12 margin05">派单、邀请关系</p>';  
            }else if(v.relation==5){
            html+='<p class="f12 margin05">推荐关系</p>';  
            }else if(v.relation==0){
            html+='<p class="f12 margin05">没有关系</p>';  
            }               
            html+='</li><li>';
            if(v.avatar){
                      html+='<img class="fr avator" src="'+v.avatar+'" alt="">';
                    }else{
                      html+='<img class="fr avator" src="/images/default/avator-m.png" alt=""></li>';
                    }   
            html+='</li></ul></div>';
            html+='<div style="width:100%;height:1rem"></div>';
            html+='<ul class="ui_address">\
                    <li class="f12 color333">活动</li>\
                    <li>\
                      <div class="fl block"><img style="width:5rem;height:5rem;clear:both;border-radius:0.2rem" src='+v.activity_list_img+' alt=""></div>\
                      <div class="ui_address_text weui-cell_access" data-id="'+v.activity_id+'">';
            if(v.activity_title.length>14){
              html+='<p class="ui_address_detail b f14">'+v.activity_title.substring(0,14)+'…'+'</p>';
             }else{
              html+='<p class="ui_address_detail b f14">'+v.activity_title+'</p>';
             }
              html+='<p class="ui_address_pict margin05">\
                          <img class="fl martop"  src="/images/020700/ico.png" alt="">\
                          <span id="ui_detail">'+unix_to_yeardatetime(v.begin_time)+'</span>\
                        </p>\
                        <p class="ui_address_pict margin05">\
                          <img class="fl martop"  src="/images/020700/gg.png" alt="">';
           if(v.cities.length>14){
             html+= '<span id="ui_detail_">'+v.cities.substring(0,14)+'…'+'</span>'; 
           }else{
            html+= '<span id="ui_detail_">'+v.cities+'</span>'; 
           } 
            html+=' </p></div></li></ul><div style="width:100%;height:4rem"></div>';
            html+='<ul class="ui_status">\
                    <li class="f12 color333">状态</li>\
                    <li  style="height:1.5rem;line-height:1.5rem;margin-top:1.8rem">\
                      <p class="fr float a6a6 f12">待确认</p>\
                      <p class="ui_sheng_time a6a6 f12" style="margin:0 0 0 0">'+v.status_info.remark+'</p>\
                    </li>\
                   </ul>\
                 </section>\
                 <div class="ui_send_again" data-id="'+v.id+'"  data-title="'+v.activity_title+'" data-uid="'+v.uid+'" data-imgurl="'+v.activity_list_img+'"  data-nickname="'+v.nickname+'">再次发送</div>';
                 $('.ui_container_wait').append(html);

      })
     }else{
        $('#nocommenttip1').removeClass('none');
     }
    }
    function tips(e) {
         $('.tips').text(e).removeClass('none');
        setTimeout(function() {
            $('.tips').addClass('none');
        }, 1500);
    }; 
    //改造时间戳
    function unix_to_yearalldatetime(unix){
                                            var newDate = new Date();
                                            newDate.setTime(unix * 1000);
                                            var Y = newDate.getFullYear();
                                            var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
                                            var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
                                            var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
                                            var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
                                            var s = newDate.getSeconds() < 10 ? '0' + newDate.getSeconds() : newDate.getSeconds();
                                            return Y + '-' + M + '-' + D + ' ' + h + ':' + m+ ':'+s;
     }
    function unix_to_pretime(unix) {
                                          var newDate = new Date();
                                          newDate.setTime(unix * 1000);
                                          var Y = newDate.getFullYear();
                                          var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
                                          var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
                                          return Y + '年' + M + '月' + D + '日';
     }
    //对方接受
    function acceptConfirmation(){
            var param = {};
                param["agent_id"] = agent_id;
                param['type']=1;
                param['page']=page;
                param['page_size']=page_size;
            var url=labUser.agent_path+'/user/activity-invitation/_v010000';
            ajaxRequest(param, url, function (data) {
            if (data.status) {
              if(data.message.length>0){
               $.each(data.message,function(k,v){
                 var html='';
                     html+='<div class="ui_top_time">\
                              <div style="width:100%;height:2rem"></div>\
                              <center><div class="ui_show_time" >'+unix_to_pretime(v.updated_at)+'</div></center>\
                           </div>';
                     html+='<section class="ui_invite_container" style="height:21.8rem" data-id="'+v.id+'">\
                             <div class="ui_infor">\
                               <ul class="ui_infor_tab">\
                                <li class="f12 color333">受邀人</li>\
                                <li>\
                                    <p class=" f12 b margin05">'+v.nickname+'</p>';
                  if(v.relation==1){
                    html+='<p class="f12 margin05">派单关系</p>';  
                     }else if(v.relation==2){
                    html+='<p class="f12 margin05">邀请关系</p>';  
                    }else if(v.relation==3){
                    html+='<p class="f12 margin05">邀请、派单关系</p>';  
                    }else if(v.relation==4){
                    html+='<p class="f12 margin05">派单、邀请关系</p>';  
                    }else if(v.relation==5){
                    html+='<p class="f12 margin05">推荐关系</p>';  
                    }else if(v.relation==0){
                   html+='<p class="f12 margin05">没有关系</p>';  
                   }
                    html+='</li><li>';
                    if(v.avatar){
                      html+='<img class="fr avator" src="'+v.avatar+'" alt="">';
                    }else{
                      html+='<img class="fr avator" src="/images/default/avator-m.png" alt=""></li>';
                    }  
                      html+='</li></ul></div><div style="width:100%;height:1rem"></div>';
                      html+='<ul class="ui_address">\
                            <li class="f12 color333">活动</li>\
                            <li>\
                              <div class="fl block"><img style="width:5rem;height:5rem;clear:both;border-radius:0.2rem" src='+v.activity_list_img+' alt=""></div>\
                              <div class="ui_address_text weui-cell_access" data-id="'+v.activity_id+'">';
                    if(v.activity_title.length>14){
                      html+='<p class="ui_address_detail b f14">'+v.activity_title.substring(0,14)+'…'+'</p>';
                     }else{
                      html+='<p class="ui_address_detail b f14">'+v.activity_title+'</p>';
                     }
                      html+='<p class="ui_address_pict margin05">\
                                  <img class="fl martop"  src="/images/020700/ico.png" alt="">\
                                  <span id="ui_detail">'+unix_to_yeardatetime(v.begin_time)+'</span>\
                                </p>\
                                <p class="ui_address_pict margin05">\
                                  <img class="fl martop"  src="/images/020700/gg.png" alt="">';
                      if(v.cities.length>14){
                      html+= '<span id="ui_detail_">'+v.cities.substring(0,14)+'…'+'</span>'; 
                   }else{
                      html+= '<span id="ui_detail_">'+v.cities+'</span>'; 
                   } 
                      html+=' </p></div></li></ul><div style="width:100%;height:4rem"></div>';
                      html+='<ul class="ui_status_">\
                            <li class="f12 color333">状态 <span class="fr  ffaf20">已接受邀请</span></li>\
                            <li style="clear:both" class="f12 color333 ">确认时间<span class="fr a6a6">'+unix_to_yearalldatetime(v.affirm_time)+'</span></li>\
                          </ul>\
                         </section>';
                    $('.ui_container_accept').append(html) 
                  })
               //如果没有数据的情况下执行样式
                }else{
                 $('#nocommenttip2').removeClass('none');
                }
               }//data.status
            })// ajaxRequest
    } 
    //对方拒绝
    function refuseConfirmation(){
            var param = {};
                param["agent_id"] = agent_id;
                param['type']=-1;
                param['page']=page;
                param['page_size']=page_size;
            var url=labUser.agent_path+'/user/activity-invitation/_v010000';
            ajaxRequest(param, url, function (data) {
            if (data.status) {
               //下面的if判断数据存在的情况下
               if(data.message.length>0){
               $.each(data.message,function(k,v){
                var html='';
                    html+='<div class="ui_top_time">\
                              <div style="width:100%;height:2rem"></div>\
                              <center><div class="ui_show_time" >'+unix_to_pretime(v.updated_at)+'</div></center>\
                           </div>';
                    html+='<section class="ui_invite_container" style="height:25rem" data-id="'+v.id+'">\
                             <div class="ui_infor">\
                               <ul class="ui_infor_tab">\
                                 <li class="f12 color333">受邀人</li>\
                                  <li>\
                                    <p class=" f12 b margin05">'+v.nickname+'</p>';
                    if(v.relation==1){
                    html+='<p class="f12 margin05">派单关系</p>';  
                    }else if(v.relation==2){
                    html+='<p class="f12 margin05">邀请关系</p>';  
                    }else if(v.relation==3){
                    html+='<p class="f12 margin05">邀请、派单关系</p>';  
                    }else if(v.relation==4){
                    html+='<p class="f12 margin05">派单、邀请关系</p>';  
                    }else if(v.relation==5){
                    html+='<p class="f12 margin05">推荐关系</p>';  
                    }else if(v.relation==0){
                    html+='<p class="f12 margin05">没有关系</p>';  
                   }
                    html+='</li><li>';
                    if(v.avatar){
                      html+='<img class="fr avator" src="'+v.avatar+'" alt="">';
                    }else{
                      html+='<img class="fr avator" src="/images/default/avator-m.png" alt=""></li>';
                    } 
                      html+='</li></ul></div><div style="width:100%;height:1rem"></div>';
                    html+='<ul class="ui_address">\
                            <li class="f12 color333">活动</li>\
                            <li>\
                              <div class="fl block"><img style="width:5rem;height:5rem;clear:both;border-radius:0.2rem" src='+v.activity_list_img+' alt=""></div>\
                              <div class="ui_address_text weui-cell_access" data-id="'+v.activity_id+'">';
                    if(v.activity_title.length>14){
                      html+='<p class="ui_address_detail b f14">'+v.activity_title.substring(0,14)+'…'+'</p>';
                     }else{
                      html+='<p class="ui_address_detail b f14">'+v.activity_title+'</p>';
                     }
                      html+='<p class="ui_address_pict margin05">\
                                  <img class="fl martop"  src="/images/020700/ico.png" alt="">\
                                  <span id="ui_detail">'+unix_to_yeardatetime(v.begin_time)+'</span>\
                                </p>\
                                <p class="ui_address_pict margin05">\
                                  <img class="fl martop"  src="/images/020700/gg.png" alt="">';
                      if(v.cities.length>14){
                      html+= '<span id="ui_detail_">'+v.cities.substring(0,14)+'…'+'</span>'; 
                     }else{
                      html+= '<span id="ui_detail_">'+v.cities+'</span>'; 
                     } 
                      html+=' </p></div></li></ul><div style="width:100%;height:3.5rem"></div>';
                    html+='<ul class="ui_status_">\
                            <li class="f12 color333">状态 <span class="fr  fd4d4d">已拒绝</span></li>';
                    if(v.status_info.remark.length>18){
                     html+= '<li class="f12 color333">拒绝理由 <span class="fr color333">'+v.status_info.remark.substring(0,18)+'…'+'</span></li>'; 
                   }else{
                     html+= '<li class="f12 color333">拒绝理由 <span class="fr color333">'+v.status_info.remark+'</span></li>';
                   }  
                    html+= '<li style="clear:both" class="f12 color333 ">确认时间<span class="fr a6a6">'+unix_to_yearalldatetime(v.affirm_time)+'</span></li>\
                          </ul>\
                         </section>';
                    $('.ui_container_refuse').append(html);
              
               })
              }else{
                    $('#nocommenttip3').removeClass('none');
               }
            }//data.status
            })// ajaxRequest
    }
    //再次发送
    function sendRichMsg(type,uType,uid,id,title,imgUrl,date,store,nickname){
        if (isAndroid) {
            javascript:myObject.sendRichMsg(type,uType,uid,id,title,imgUrl,date,store);
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
                'nickname':nickname
                    };
            window.webkit.messageHandlers.sendRichMsg.postMessage(data);
        }
    }
    // 再次发送事件
    $(document).on('click','.ui_send_again',function(){ 
       var uid=$(this).data('uid'),
           id=$(this).data('id'),
           title=$(this).data('title'),
           imgUrl=$(this).data('imgurl'),
           nickname=$(this).data('nickname');
        sendRichMsg(1,'C',uid,id,title,imgUrl,'','',nickname);
           
    });
    $(document).on('click','.ui_invite_container',function(){
          var id=$(this).data('id');
         window.location.href = labUser.path + "webapp/agent/newsactask/detail?invite_id="+id;
      })
});//zepto外层
