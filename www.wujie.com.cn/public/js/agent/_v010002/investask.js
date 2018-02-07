// created byhongky
Zepto(function (){
    new FastClick(document.body);
    var page=1,
        page_size=100,
        accept=0,
        refuse=0,
        again=0;
    var urlPath = window.location.href,
        args = getQueryStringArgs(),
        activity_id = args['id'],
        agent_id = args['agent_id'] || '0';
    var askDetail = {
        detail: function (agent_id) {
            var param = {};
                param["agent_id"] = agent_id;
                param['status']=0;
                param['page']=page;
                param['page_size']=page_size;
            var url=labUser.agent_path+'/user/inspect-invitation/_v010000';
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
    // if(obj.length>0){
      // $.each(obj,function(k,v){
        if(obj){
        $.each(obj,function(i,t){
        var html='';
            html+='<div class="ui_top_time">\
                      <div style="width:100%;height:2rem"></div>\
                      <center><div class="ui_show_time" >'+unix_to_pretime(t.create_time)+'</div></center>\
                   </div>';
            html+='<section class="ui_invite_container add_bg"  style="min-height:27rem">';
            html+=' <div class="ui_infor zone" data-id="'+t.inspect_id+'">\
                     <ul class="ui_infor_tab">\
                       <li class="f12 color333">邀请人</li>';
            html+='<li><p class=" f12 b margin05">'+t.nickname+'</p><p class="f12 margin05">'+(t.brand_name.length>15?t.brand_name.substr(0,13)+'…':t.brand_name)+'·意向投资人</p></li>';
             if(t.avatar){
            html+='<li><img class="fr avator" src="'+t.avatar+'" alt=""></li>';
             }else{
            html+='<li><img class="fr avator" src="/images/default/avator-m.png" alt=""></li>';
             }
            html+='</ul></div>';
            html+='<div style="width:100%;height:1rem"></div>';
            html+='<ul class="ui_address zone" data-id="'+t.inspect_id+'">\
                      <li class="f12 color333">考察场地</li>\
                      <li>\
                      <div class="ui_address_text">';
            html+='<p class="ui_address_detail">'+t.store_name+'</p>';  
            html+='<p class="ui_address_pict">';
            html+='<img class="fl martop"  src="/images/020700/gg.png" alt="">';
              // if(t.inspect_address.length>16){
              // html+='<span id="ui_detail">'+t.inspect_address.substring(0,16)+'…'+'</span>';
              // }else{
            html+='<span id="ui_detail">'+(t.inspect_address.length>17?t.inspect_address.substr(0,17)+'…':t.inspect_address)+'</span>'; 
              // }
            html+='</p></div></li></ul>';    
            html+='<div style="width:100%;height:1.5rem"></div>'; 
            html+='<p class="ui_go_time zone" data-id="'+t.inspect_id+'"><span class="ui_go_time_text color333 f12">考察时间</span><span class="ui_go_time_ fr color666 f12">'+unix_to_2(t.inspect_time)+'</span></p>\
                     <p class="ui_order_money zone" data-id="'+t.inspect_id+'"><span class="ui_go_time_text color333 f12">定金金额</span><span class="ui_go_time_ fr color666 f12">￥'+t.currency+'</span></p>\
                     <div style="width:100%;height:2rem"></div>';
            html+='<ul class="ui_circle zone" data-id="'+t.inspect_id+'">\
                         <li><div class="ui_left_circle"></div></li>\
                         <li><div class="ui_dotted"></div></li>\
                         <li><div class="ui_right_circle fr"></div></li>\
                      </ul>';  
            html+='<ul class="ui_status zone" data-id="'+t.inspect_id+'">\
                      <li class="f12 color333">状态</li>\
                      <li style="height:1.5rem;line-height:1.5rem;margin-top:0.6rem">\
                        <p class="fr float a6a6 f12">待确认</p>\
                        <p class="ui_sheng_time a6a6 f12" style="margin:0 0 0 0">'+t.status_summary+'</p>\
                      </li>\
                    </ul>';
            html+='<div style="width:100%;height:2rem;clear:both"></div>\
                      <ul class="accept_refuse clear">\
                          <li class="ui_upordown" data-id="'+t.inspect_id+'"><a class="ui_border ui_refuse f13 color666 ">关闭</a></li>\
                          <li class="ui_send_again" data-uid="'+t.uid+'" data-id="'+t.inspect_id+'" data-title="'+t.brand_name+'" data-imgUrl="'+t.brand_logo+'" data-date="'+t.inspect_time+'" data-store="'+t.store_name+'"data-nickname="'+t.nickname+'"><a class="f13 ui_accept clear ">再次发送</a></li>\
                      </ul>';
            html+='</section>';
              // html+='<div class="ui_send_again" data-uid="'+t.uid+'" data-id="'+t.inspect_id+'" data-title="'+t.brand_name+'" data-imgUrl="'+t.brand_logo+'" data-date="'+t.inspect_time+'" data-store="'+t.store_name+'"data-nickname="'+t.nickname+'">再次发送</div>';
             $('.ui_container_wait').append(html);      
         })
          }else{
              $('#nocommenttip1').removeClass('none');
         }//第二次循环
      // })//第一次循环
    }
    //提示方法
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
    function unix_to_2(unix) {
                                        var newDate = new Date();
                                            newDate.setTime(unix * 1000);
                                            var Y = newDate.getFullYear();
                                            var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
                                            var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
                                        return Y + '-' + M + '-' + D ;
     }
    //对方接受
    function acceptConfirmation(){
                var param = {};
                    param["agent_id"] = agent_id;
                    param['status']=1;
                    param['page']=page;
                    param['page_size']=page_size;
            var url=labUser.agent_path+'/user/inspect-invitation/_v010000';
            ajaxRequest(param, url, function (data) {
            if (data.status) {
              var obj=data.message;
               // $.each(obj,function(k,v){
                  if(obj){
                    $.each(obj,function(i,t){
                         var html='';
                             html+='<div class="ui_top_time">\
                                        <div style="width:100%;height:2rem"></div>\
                                        <center><div class="ui_show_time" >'+unix_to_pretime(t.create_time)+'</div></center>\
                                    </div>';
                             html+='<section class="ui_invite_container zone"  data-id="'+t.inspect_id+'" style="height:29.55rem">';
                             html+=' <div class="ui_infor">\
                                       <ul class="ui_infor_tab">\
                                         <li class="f12 color333">邀请人</li>';
                             html+='<li><p class=" f12 b margin05">'+t.nickname+'</p><p class="f12 margin05">'+(t.brand_name.length>15?t.brand_name.substr(0,13)+'…':t.brand_name)+'·意向投资人</p></li>';
                          if(t.avatar){
                              html+='<li><img class="fr avator" src="'+t.avatar+'" alt=""></li>';
                            }else{
                              html+='<li><img class="fr avator" src="/images/default/avator-m.png" alt=""></li>';
                             }
                             html+='</ul></div>';
                             html+='<div style="width:100%;height:1rem"></div>';
                             html+='<ul class="ui_address">\
                                    <li class="f12 color333">考察场地</li>\
                                    <li>\
                                    <div class="ui_address_text">';
                             html+='<p class="ui_address_detail">'+t.store_name+'</p>';  
                             html+='<p class="ui_address_pict">';
                             html+='<img class="fl martop"  src="/images/020700/gg.png" alt="">';
                             html+='<span id="ui_detail">'+(t.inspect_address.length>17?t.inspect_address.substr(0,17)+'…':t.inspect_address)+'</span>'; 
                             html+='</p></div></li></ul>';    
                             html+='<div style="width:100%;height:1.2rem"></div>';
                             html+='<p class="ui_go_time"><span class="ui_go_time_text color333 f12">考察时间</span><span class="ui_go_time_ fr color666 f12">'+unix_to_2(t.inspect_time)+'</span></p>\
                                   <p class="ui_order_money">\
                                      <span class="ui_go_time_text color333 f12">定金金额</span><span class="ui_go_time_ fr color666 f12">￥'+t.currency+'</span>\
                                   </p>\
                                   <p class="ui_order_money" style="margin:0 0 0 0">\
                                      <span class="ui_go_time_text color333 f12">支付方式</span><span class="ui_go_time_ fr color666 f12">'+t.pay_way+'</span>\
                                   </p>';
                             html+='<div style="width:100%;height:1.9rem"></div>\
                                    <ul class="ui_status_">\
                                      <li class="f12 color333" style="margin: 1rem 0 1rem 0;">状态<span class="fr  ffaf20">已接受邀请</span></li>\
                                      <li style="clear:both" class="f12 color333 ">确认时间<span class="fr a6a6">'+unix_to_yearalldatetime(t.confirm_time)+'</span></li>\
                                    </ul>\
                                   </section>';
                     $('.ui_container_accept').append(html)
                   })//第二次循环多维数组
               }else{
                     $('#nocommenttip2').removeClass('none');
               }
               // })//第一次循环数据
              
               }//data.status
            })// ajaxRequest
    } 
    //对方拒绝
    function refuseConfirmation(){
                var param = {};
                    param["agent_id"] = agent_id;
                    param['status']=-1;
                    param['page']=page;
                    param['page_size']=page_size;
            var url=labUser.agent_path+'/user/inspect-invitation/_v010000';
            ajaxRequest(param, url, function (data) {
            if (data.status) {
               // $.each(data.message,function(k,v){
                if(data.message){
                 $.each(data.message,function(i,t){
                var html='';
                    html+='<div class="ui_top_time">\
                                    <div style="width:100%;height:2rem"></div>\
                                    <center><div class="ui_show_time" >'+unix_to_pretime(t.create_time)+'</div></center>\
                                </div>';
                    html+='<section class="ui_invite_container zone"  data-id="'+t.inspect_id+'" style="height:30.1rem">';
                    html+=' <div class="ui_infor">\
                                     <ul class="ui_infor_tab">\
                                       <li class="f12 color333">邀请人</li>';
                    html+='<li><p class=" f12 b margin05">'+t.nickname+'</p><p class="f12 margin05">'+(t.brand_name.length>15?t.brand_name.substr(0,13)+'…':t.brand_name)+'·意向投资人</p></li>';
                    if(t.avatar){
                    html+='<li><img class="fr avator" src="'+t.avatar+'" alt=""></li>';
                    }else{
                    html+='<li><img class="fr avator" src="/images/default/avator-m.png" alt=""></li>';
                    }
                    html+='</ul></div>';
                    html+='<div style="width:100%;height:1rem"></div>';
                    html+='<ul class="ui_address">\
                                    <li class="f12 color333">考察场地</li>\
                                    <li>\
                                    <div class="ui_address_text">';
                    html+='<p class="ui_address_detail">'+t.store_name+'</p>';  
                    html+='<p class="ui_address_pict">';
                    html+='<img class="fl martop"  src="/images/020700/gg.png" alt="">';
                    html+='<span id="ui_detail">'+(t.inspect_address.length>17?t.inspect_address.substr(0,17)+'…':t.inspect_address)+'</span>'; 
                    html+='</p></div></li></ul>';    
                    html+='<div style="width:100%;height:1.2rem"></div>';
                    html+='<p class="ui_go_time"><span class="ui_go_time_text color333 f12">考察时间</span><span class="ui_go_time_ fr color666 f12">'+unix_to_2(t.inspect_time)+'</span></p>\
                            <p class="ui_order_money"><span class="ui_go_time_text color333 f12">定金金额</span><span class="ui_go_time_ fr color666 f12">￥'+t.currency+'</span></p>\
                            <div style="width:100%;height:0.8rem"></div>\
                            <ul class="ui_status_">\
                              <li class="f12 color333">状态 <span class="fr  fd4d4d">已拒绝</span></li>';
                    if(t.reson.length>18){
                    html+= '<li class="f12 color333">拒绝理由 <span class="fr color333">'+t.reson.substring(0,18)+'…'+'</span></li>';
                    }else{
                    html+= '<li class="f12 color333">拒绝理由 <span class="fr color333">'+t.reson+'</span></li>';  
                    }
                    html+= '<li style="clear:both" class="f12 color333 ">确认时间<span class="fr a6a6">'+unix_to_yearalldatetime(t.confirm_time)+'</span></li>\
                            </ul>\
                           </section>';
                    $('.ui_container_refuse').append(html);
                 })
               }else{
                    $('#nocommenttip3').removeClass('none');
               }
               // })
            }//data.status
            })// ajaxRequest
    }
   // 再次发送考察邀请函
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
                            title=$(this).data('title');
                        var imgUrl=$(this).data('imgurl'),
                            date=$(this).data('date').toString(),
                            store=$(this).data('store'),
                            nickname=$(this).data('nickname');
                          sendRichMsg(2,'C',uid,id,title,imgUrl,date,store,nickname);
            
    })
    $(document).on('click','.zone',function(){
                        var ID=$(this).data('id');  
                        window.location.href = labUser.path + "webapp/agent/newsinvestask/detail/_v010002?inspect_id="+ID;
      
    })
    //当最后一个没有的时候调用，出现缺省图;
    function experience(){
                        var bigo=$('.ui_container_wait>.ui_top_time').length==0?true:false;
                        if(bigo){
                          $('.ui_container_wait>.ui_invite_container').addClass('none');
                          $('.ui_container_wait>.ui_top_time').addClass('none');
                          $('#nocommenttip1').removeClass('none');
                        }
    }
    $(document).on('click','.ui_upordown',function(){
                       $(this).parent().parent().addClass('none').prev().remove();
                       var id=$(this).data('id');
                       shutUp(id);
                       experience();
    })
  //关闭邀请函方法
    function shutUp(invite_id){
                      var param={};
                          param['invite_id']=invite_id;
                      var url=labUser.agent_path+'/user/back-out/_v010002';
                      ajaxRequest(param, url, function (data) {
                             if(data.status){
                                   if(data.message=='ok'){
                                      tips('关闭成功')
                                   }     
                             }else{
                                    tips(data.message);
                             }
                      })
  }
});//zepto外层
