// created byhongky
Zepto(function (){  
    new FastClick(document.body);
    var urlPath = window.location.href,
        args = getQueryStringArgs(),
        uid = args['uid'],
        num=0,
        mum=0; 
    var Detail = {
        detail: function (uid,type) {
            var param = {};
            param["uid"] = uid;
            param["type"]=type;
            var url=labUser.api_path+'/user/activity-invites/_v020800';
            ajaxRequest(param, url, function (data) {
            if (data.status) {
                              wait(data.message);
                              $('#act_container').removeClass('none')
                              }//data.status
                    })// ajaxRequest
            },//detail方法
        table:function(){
          $('.fixedtop li').on('click',function(){
            $(this).addClass('ff5a00  b').siblings().removeClass('ff5a00  b');
          })
          $('.fixedtop li').eq(0).on('click',function(){
              $('.ui_container_wait').removeClass('none');
              $('.ui_container_accept').addClass('none');
              $('.ui_container_refuse').addClass('none');
          })
          $('.fixedtop li').eq(1).on('click',function(){
            num++;
            if(num==1){
              accpet(uid,1);
            }
            $('.ui_container_wait').addClass('none');
            $('.ui_container_accept').removeClass('none');
            $('.ui_container_refuse').addClass('none');
          })
          $('.fixedtop li').eq(2).on('click',function(){
             mum++;
            if(mum==1){
              refuse(uid,-1);
            }
            $('.ui_container_wait').addClass('none');
            $('.ui_container_accept').addClass('none');
            $('.ui_container_refuse').removeClass('none');
          })
        }

        }//activityDetail对象    
     Detail.detail(uid,0);
     Detail.table(); 
});//zepto外层
  //活动邀请函等待
  function wait(obj){
       if(obj!=''){
        $.each(obj,function(k,v){
          var html='';
              html+='<div class="ui_top_time">\
                        <div style="width:100%;height:2.2rem"></div>\
                        <center><div class="ui_show_time" >'+year_to_month(v.create_time)+'</div></center>\
                     </div>';
              html+='<section class="ui_invite_container" style="height:23.5rem">\
                      <section class="common_" data-id="'+v.invite_id+'">\
                      <ul class="ui_address">\
                        <li class="f12 color999">活动</li>\
                        <li>\
                          <div class="ui_con">\
                            <ul class="ui_con1">\
                              <li><img class="ui_img" src="'+v.img+'" alt=""></li>\
                              <li>';
              if(v.title.length>15){
                 html+='<p class="toleft f12 margin3 toTop1 b color333 act-title">'+v.title.substring(0,15)+'…'+'</p>';
              }else{
                 html+='<p class="toleft f12 margin3 toTop1 b color333 act-title">'+v.title+'</p>';  
              }
             
              html+='<p class="toleft f10 margin3 color333 act-time">\
                                  <span class="ui_calendar_con">\
                                      <img class="ui_calendar fl"  src="/images/020700/ico.png" alt="">\
                                  </span>\
                                  <span class="ui_calendar_detail ">'+change_unix(v.begin_time)+'</span>\
                                </p>\
                                <p class="toleft f10 margin3 color333 act-city">\
                                  <span class="ui_calendar_con">\
                                      <img class="ui_calendar fl"  src="/images/020700/gg.png" alt="">\
                                  </span>\
                                  <span class="ui_calendar_detail">'+v.host_cities+'</span>\
                                </p>\
                              </li>\
                            </ul>\
                          </div>\
                        </li>\
                      </ul>\
                      <div style="width:100%;height:1.3rem"></div>\
                      <p class="clear color999 f12">受邀人<span class="fr color333 b">'+v.invitor+'</span></p>\
                      <ul class="ui_status">\
                        <li class="f12 color999">状态</li>\
                        <li>\
                          <p class="f12 ffa300">待确认</p>\
                          <p class="f12 color333">'+v.status_info.remark+'</p>\
                        </li>\
                      </ul>\
                      <div style="width:100%;height:1.3rem;clear:both"></div>\
                      </section>\
                      <ul class="accept_refuse clear">\
                        <li><a class="ui_border ui_refuse f13 color666 " data-agent_id="'+v.agent_id+'" data-activity_id="'+v.activity_id+'"  data-id="'+v.invite_id+'">拒绝</a></li>\
                        <li><a class="f13 ui_accept clear " data-agent_id="'+v.agent_id+'" data-activity_id="'+v.activity_id+'"  data-id="'+v.invite_id+'">接受活动邀请函</a></li>\
                      </ul>\
                     </section>';
                $('.ui_container_wait').append(html);
        })
       }else{
            $('#nocommenttip1').removeClass('none');
       }
  }
  //活动邀请已接受
  function accpet(uid,type){
        var param = {};
            param["uid"] = uid;
            param["type"]=type;
            var url=labUser.api_path+'/user/activity-invites/_v020800';
            ajaxRequest(param, url, function (data) {
            if (data.status) {
                               accept_(data.message)
                              }//data.status
                    })// ajaxRequ

  }
  function  accept_(obj){
    if(obj!=''){
       $.each(obj,function(k,v){
         var html='';
             html+='<div class="ui_top_time">\
                        <div style="width:100%;height:2.2rem"></div>\
                        <center><div class="ui_show_time" >'+year_to_month(v.create_time)+'</div></center>\
                    </div>';
             html+='<section class="ui_invite_container  common_" style="height:18.5rem" data-id="'+v.invite_id+'">\
                        <ul class="ui_address">\
                          <li class="f12 color999">活动</li>\
                          <li>\
                            <div class="ui_con">\
                              <ul class="ui_con1">\
                                <li><img class="ui_img" src="'+v.img+'" alt=""></li>\
                                <li>';
             if(v.title.length>15){
             html+='<p class="toleft f12 margin3 toTop1 b color333 act-title">'+v.title.substring(0,15)+'…'+'</p>';
            }else{
             html+='<p class="toleft f12 margin3 toTop1 b color333 act-title">'+v.title+'</p>';  
            }
              html+='<p class="toleft f10 margin3 color333 act-time">\
                                    <span class="ui_calendar_con">\
                                        <img class="ui_calendar fl"  src="/images/020700/ico.png" alt="">\
                                    </span>\
                                    <span class="ui_calendar_detail ">'+change_unix(v.begin_time)+'</span>\
                                  </p>\
                                  <p class="toleft f10 margin3 color333 act-city">\
                                    <span class="ui_calendar_con">\
                                        <img class="ui_calendar fl"  src="/images/020700/gg.png" alt="">\
                                    </span>\
                                    <span class="ui_calendar_detail">'+v.host_cities+'</span>\
                                  </p>\
                                </li>\
                              </ul>\
                            </div>\
                          </li>\
                        </ul>\
                        <div style="width:100%;height:1.3rem"></div>\
                        <p class="clear color999 f12">受邀人<span class="fr b color333">'+v.invitor+'</span></p>\
                        <p class="clear color999 f12">状态<span class="fr ce97">已确认</span></p>\
                        <p class="clear color999 f12 margin0">确认时间<span class="fr color333">'+stampchange(v.confirm_time)+'</span></p>\
                       </section>';
                       $('.ui_container_accept').append(html);
       })
    }else{
         $('#nocommenttip2').removeClass('none');
    }
  }
  //活动邀请已拒绝
  function refuse(uid,type){
        var param = {};
            param["uid"] = uid;
            param["type"]=type;
            var url=labUser.api_path+'/user/activity-invites/_v020800';
            ajaxRequest(param, url, function (data) {
            if (data.status) {
                               refuse_(data.message)
                              }//data.status
                    })// ajaxRequ

  }
  function refuse_(obj){
        if(obj!=''){
          $.each(obj,function(k,v){
            var html='';
                html+='<div class="ui_top_time">\
                        <div style="width:100%;height:2.2rem"></div>\
                        <center><div class="ui_show_time" >'+year_to_month(v.create_time)+'</div></center>\
                    </div>';
                html+='<section class="ui_invite_container common_" style="height:21rem" data-id="'+v.invite_id+'">\
                        <ul class="ui_address">\
                          <li class="f12 color999">活动</li>\
                          <li>\
                            <div class="ui_con">\
                              <ul class="ui_con1">\
                                <li><img class="ui_img" src="'+v.img+'" alt=""></li>\
                                <li>';
              if(v.title.length>15){
                 html+='<p class="toleft f12 margin3 toTop1 b color333 act-title">'+v.title.substring(0,15)+'…'+'</p>';
              }else{
                 html+='<p class="toleft f12 margin3 toTop1 b color333 act-title">'+v.title+'</p>';  
              }
                 html+='<p class="toleft f10 margin3 color333 act-time">\
                                    <span class="ui_calendar_con">\
                                        <img class="ui_calendar fl"  src="/images/020700/ico.png" alt="">\
                                    </span>\
                                    <span class="ui_calendar_detail ">'+change_unix(v.begin_time)+'</span>\
                                  </p>\
                                  <p class="toleft f10 margin3 color333 act-city">\
                                    <span class="ui_calendar_con">\
                                        <img class="ui_calendar fl"  src="/images/020700/gg.png" alt="">\
                                    </span>\
                                    <span class="ui_calendar_detail">'+v.host_cities+'</span>\
                                  </p>\
                                </li>\
                              </ul>\
                            </div>\
                          </li>\
                        </ul>\
                        <div style="width:100%;height:1.3rem"></div>\
                        <p class="clear color999 f12">受邀人<span class="fr color333 b">'+v.invitor+'</span></p>\
                        <p class="clear color999 f12">状态<span class="fr fd4d4d">已拒绝</span></p>';
                   if(v.status_info.remark.length>15){
                   html+='<p class="clear color999 f12">拒绝理由<span class="fr color333">'+v.status_info.remark.substring(0,15)+'…'+'</span></p>'; 
                   }else{
                   html+='<p class="clear color999 f12">拒绝理由<span class="fr color333">'+v.status_info.remark+'</span></p>'; 
                   }
                   
                   html+='<p class="clear color999 f12 margin0">确认时间<span class="fr color333">'+stampchange(v.confirm_time)+'</span></p>\
                       </section>';
                   $('.ui_container_refuse').append(html);   
          })
        }else{
          $('#nocommenttip3').removeClass('none');
        }
  }
    function stampchange(unix){
                                var newDate = new Date();
                                newDate.setTime(unix * 1000);
                                var Y = newDate.getFullYear();
                                var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
                                var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
                                var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
                                var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
                                var s = newDate.getSeconds() < 10 ? '0' + newDate.getSeconds() : newDate.getSeconds();
                                return Y + '年' + M + '月' + D +'日'+ ' ' + h + ':' + m+ ':'+s;
    }
     function change_unix(unix){
                                var newDate = new Date();
                                newDate.setTime(unix * 1000);
                                var Y = newDate.getFullYear();
                                var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
                                var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
                                var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
                                var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
                                var s = newDate.getSeconds() < 10 ? '0' + newDate.getSeconds() : newDate.getSeconds();
                                return Y + '-' + M + '-' + D + ' ' + h + ':' + m;
    }
    function year_to_month(unix) {
                                    var newDate = new Date();
                                    newDate.setTime(unix * 1000);
                                    var Y = newDate.getFullYear();
                                    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
                                    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
                                    return Y + '年' + M + '月';
     }
     function acceptActivityInvitation(id,agent_id,activity_id){
       if (isAndroid) {
        javascript:myObject.acceptActivityInvitation(id,agent_id,activity_id);
        }else if(isiOS){
            var data={
                'id':id,
                'agent_id':agent_id,
                'activity_id':activity_id
                    };
            window.webkit.messageHandlers.acceptActivityInvitation.postMessage(data);
        }
    
     }
      function rejectActivityInvitation(invite_id,agent_id,activity_id){
       if (isAndroid) {
            javascript:myObject.rejectActivityInvitation(invite_id,agent_id,activity_id);
        }else if(isiOS){
            var data={
                'invite_id':invite_id,
                'agent_id':agent_id,
                'activity_id':activity_id
                    };
            window.webkit.messageHandlers.rejectActivityInvitation.postMessage(data);
        } 
     } 
     $(document).on('click','.ui_refuse',function(){
           var agent_id=$(this).data('agent_id'),
               invite_id=$(this).data('id'),
               activity_id=$(this).data('activity_id');
           rejectActivityInvitation(invite_id,agent_id,activity_id);   
     })
     $(document).on('click','.ui_accept',function(){
          var agent_id=$(this).data('agent_id'),
              invite_id=$(this).data('id'),
              activity_id=$(this).data('activity_id');
          acceptActivityInvitation(invite_id,agent_id,activity_id);   
     })
     //跳转到详情页
     $(document).on('click','.common_',function(){
      var id=$(this).data('id');
          window.location.href=labUser.path + "webapp/actinvitation/detail/_v020800?invite_id="+id;
     })         