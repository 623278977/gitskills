//By Hongky
   new FastClick(document.body);
   var args=getQueryStringArgs(),
       id=args['card_id'],
       agent_id=args['agent_id'],
       cardName=args['cardName'];
   var getFlag=window.location.href.indexOf('getflag=1')>0?true:false;
   var sendFlag=window.location.href.indexOf('sendflag=1')>0?true:false;
   var List={
             init:function(agent_id){
                  var param={};
                      param['agent_id'] = agent_id;
                      param['type']='1,2,3';
             var url = labUser.agent_path + '/user/agent-relation/_v010300';
             ajaxRequest(param, url, function(data) {
                if (data.status){
                             List.myteam(data.message.down);
                             List.otheragent(data.message.friend);
                             List.myleader(data.message.up);
                             $('.containerBox').removeClass('none');
                           }
                  })
           },
           myleader:function(obj){
                  if(obj!=''){
                    $.each(obj,function(k,v){
                      var h='';
                          h+='<div class="fline choose_kehu" data-id="'+v.id+'" num="ad" data-nickname="'+v.nickname+'" data-realname="'+v.realname+'">\
                              <div class="investor ">\
                                <p class="mr1"><img src="'+v.avatar+'" class="avatar"/></p>\
                                <div class="investorMes">\
                                <p class=""><span class="f15 color333 mr05" >'+(v.realname?v.realname:v.nickname)+'</span>';
                          if(v.gender==0){
                            h+='<img src="/images/agent/girl.png" class="grade" /></p>';
                          }else if(v.gender==1){
                            h+='<img src="/images/agent/boy.png" class="grade" /></p>';
                          }else if(v.gender==-1){
                            h+='<img src="/images/agent/boy.png" class="grade none" /></p>';
                          }
                            h+='<p class=""><span class="f12 color666">'+v.zone+'</span><span class=""></span></p>\
                                </div>\
                              </div>\
                              <img src="/images/agent/rightyellow.png" class="choosen none"/>\
                            </div>';
                      $('.myleader').append(h);
                    })
                  }else{
                    $('.myleader').addClass('none').prev().addClass('none');
                  } 
           },
           myteam:function(obj){
                   if(obj!=''){
                    $.each(obj,function(k,v){
                      var h='';
                          h+='<div class="fline choose_kehu" data-id="'+v.id+'" num="ad" data-nickname="'+v.nickname+'" data-realname="'+v.realname+'">\
                              <div class="investor ">\
                                <p class="mr1"><img src="'+v.avatar+'" class="avatar"/></p>\
                                <div class="investorMes">\
                                  <p class=""><span class="f15 color333 mr05" >'+(v.realname?v.realname:v.nickname)+'</span>';
                          if(v.gender==0){
                            h+='<img src="/images/agent/girl.png" class="grade" /></p>';
                          }else if(v.gender==1){
                            h+='<img src="/images/agent/boy.png" class="grade" /></p>';
                          }else if(v.gender==-1){
                            h+='<img src="/images/agent/boy.png" class="grade none" /></p>';
                          }
                            h+='<p class=""><span class="f12 color666">'+v.zone+'</span><span class=""></span></p>\
                                </div>\
                              </div>\
                              <img src="/images/agent/rightyellow.png" class="choosen none"/>\
                            </div>';
                      $('.myteam').append(h);
                    })
                  }else{
                    $('.myteam').addClass('none').prev().addClass('none');
                  } 
           },
           otheragent:function(obj){
                 if(obj!=''){
                    $.each(obj,function(k,v){
                      var h='';
                          h+='<div class="fline choose_kehu" data-id="'+v.id+'" num="ad" data-nickname="'+v.nickname+'" data-realname="'+v.realname+'">\
                              <div class="investor ">\
                                <p class="mr1"><img src="'+v.avatar+'" class="avatar"/></p>\
                                <div class="investorMes">\
                                  <p class=""><span class="f15 color333 mr05" >'+(v.realname?v.realname:v.nickname)+'</span>';
                          if(v.gender==0){
                            h+='<img src="/images/agent/girl.png" class="grade" /></p>';
                          }else if(v.gender==1){
                            h+='<img src="/images/agent/boy.png" class="grade" /></p>';
                          }else if(v.gender==-1){
                            h+='<img src="/images/agent/boy.png" class="grade none" /></p>';
                          }
                            h+='<p class=""><span class="f12 color666">'+v.zone+'</span><span class=""></span></p>\
                                </div>\
                              </div>\
                              <img src="/images/agent/rightyellow.png" class="choosen none"/>\
                            </div>';
                      $('.otheragent').append(h);
                    })
                  }else{
                    $('.otheragent').addClass('none').prev().addClass('none');
                  }   
          },
          tips:function(e){
                $('.tips').text(e).removeClass('none');
                setTimeout(function(){
                    $('.tips').addClass('none');
                },1500)
          }
        };
     List.init(agent_id);
    $(document).on('click','.choose_kehu',function(){
              $(this).find('.choosen').removeClass('none');
              $(this).siblings().find('.choosen').addClass('none');
              $(this).parent('.chooseclient').siblings().find('.choosen').addClass('none'); 
                var uid=$(this).data('id'),
                    nickname=$(this).data('nickname'),
                    realname=$(this).data('realname');
                $('.gochat').data('id',uid).data('nickname',nickname).data('realname',realname);
                $('.containerBox').data('id',uid);
          
        }) 

    // 确定发送红包或者索要红包
    function postRedpocket(agent_id,id,uid){
                  var param={};
                      param['give_agent_id'] = agent_id;
                      param['card_id']=id;
                      param['get_agent_id']=uid;
             var url = labUser.agent_path + '/agent-redpacket/give-f-redpacket/_v010300';
             ajaxRequest(param, url, function(data) {
                if (data.status){
                         var uid=$('.containerBox').data('id');
                             if(getFlag){
                              $('.bg-model').removeClass('none');
                              $('.get,.geta').removeClass('none');
                               pushMessage(id, uid,cardName,'get');
                             } else if(sendFlag){
                              $('.bg-model').removeClass('none');
                              $('.send,.senda').removeClass('none');
                               pushMessage(id, uid,cardName,'send');
                             }    
                           }else{
                              List.tips(data.message)
                           }
                  })
    }
    // 移动端调用
    function confirmSend(){
      var uid=$('.containerBox').data('id');
          if(uid){
              postRedpocket(agent_id,id,uid);
          }else{
              List.tips('您未选择发送人')
          }
    }
  // 知道了
   $(document).on('click','.gochat',function(){
         haveKnown()
      
    });

    function haveKnown() {
    if (isAndroid) {
      javascript: myObject.haveKnown();
    }
    else if (isiOS) {
         var message = {
                method:'haveKnown'
            }; 
      window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
    }
  };
  //发送成功推送消息
  function pushMessage(id, uid,name,type) {
    if (isAndroid) {
      javascript: myObject.pushMessage(id, uid,name,type);
    }
    else if (isiOS) {
         var message = {
                method:'pushMessage',
                params:{
                    'id': id,
                    'uid':uid,
                    'name':name,
                    'type':type
                }
            }; 
      window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
    }
  };
  