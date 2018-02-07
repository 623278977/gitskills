// created byhongky
Zepto(function (){
    new FastClick(document.body);
    var urlPath = window.location.href,
        args = getQueryStringArgs(),
        uid = args['uid'];
    var num=0,
        mum=0;   
    var obj = {
        detail: function (uid,type) {
            var param = {};
            param["uid"] = uid;
            param["status"]=type;
            var url=labUser.api_path+'/user/inspect-invites/_v020800';
            ajaxRequest(param, url, function (data) {
            if (data.status) {               
                              wait(data.message);
                              obj.code();
                              }//data.status
                    })// ajaxRequest
            },//detail方法
        table:function(){
          $('.fixedtop li').on('click',function(){
            $(this).addClass('ff5a00  b').siblings().removeClass('ff5a00  b');
            var index=$(this).index();
            $('.firefox').eq(index).removeClass('none').siblings('.firefox').addClass('none');
          })
          $('.fixedtop li').eq(1).on('click',function(){
            num++;
            if(num==1){
              accept(uid,1);
            }
          })
          $('.fixedtop li').eq(2).on('click',function(){
             mum++;
             if(mum==1){
              refuse(uid,-1)
             }
          })
        },
        code:function(){
          if(isAndroid){
            $('.ui_show_time').css('padding-top','0.2rem');
          }
        }

        }//activityDetail对象    
     obj.detail(uid,0);
     obj.table(); 
});//zepto外层
  function wait(obj){
    if(obj.undetermined_result){
      $.each(obj.undetermined_result,function(k,v){
        var html='';
            html+='<div class="ui_top_time">\
                        <div style="width:100%;height:2.2rem"></div>\
                        <center><div class="ui_show_time" >'+year_to_month_(v.created_at)+'</div></center>\
                   </div>';
            html+='<section class="ui_invite_container" style="height:31.5rem" data-id="'+v.invite_id+'">\
                    <section class="comom_" data-id="'+v.invite_id+'">\
                     <p class="clear color999 f12">考察品牌<span class="fr b color333">'+v.brand_title+'</span></p>\
                      <ul class="ui_address">\
                        <li class="f12 color999">考察场地</li>\
                        <li>\
                          <div class="ui_con">\
                             <p class="f12 b color333 toleft">'+v.store_name+'</p>\
                             <p class="f12  color333 margin0 toleft">\
                               <span class="ui_calendar_con">\
                                 <img class="ui_calendar fl"  src="/images/020700/gg.png" alt="">\
                               </span>';
            if(v.inspect_address.length>12){
            html+='<span style="padding-left: 0.5rem">地址：'+v.inspect_address.substring(0,12)+'…'+'</span>';
            }else{
            html+='<span style="padding-left: 0.5rem">地址：'+v.inspect_address+'</span>';
            }
            html+='</p>\
                          </div>\
                        </li>\
                      </ul>\
                      <div style="width:100%;height:1.3rem;clear:both"></div>\
                      <p class="clear color999 f12">考察时间<span class="fr color333">'+year_to_month(v.inspect_time)+'</span></p>\
                      <p class="clear color999 f12">定金金额<span class="fr color333">￥'+v.currency+'</span></p>\
                      <p class="clear color999 f12">邀请人<span class="fr color333">'+v.agent+'(经纪人)</span></p>\
                      <ul class="ui_status">\
                        <li class="f12 color999">状态</li>\
                        <li>\
                          <p class="f12 ffa300">待确认</p>\
                          <p class="f12 color333">'+v.status_summary+'</p>\
                        </li>\
                      </ul>\
                      <div style="width:100%;height:1.3rem;clear:both"></div>\
                      </section>\
                      <ul class="accept_refuse clear">\
                        <li><a class="ui_border ui_refuse f13 color666" data-id="'+v.invite_id+'">拒绝</a></li>\
                        <li><a class="f13 ui_accept clear " data-id="'+v.invite_id+'">接收考察邀请函</a></li>\
                      </ul>\
                 </section>';
                 $('.ui_container_wait').append(html);
             })
    }else{
      $('#nocommenttip1').removeClass('none')
    }
  }
  function accept(uid,type){
        var param = {};
            param["uid"] = uid;
            param["status"]=type;
            var url=labUser.api_path+'/user/inspect-invites/_v020800';
            ajaxRequest(param, url, function (data) {
            if (data.status) {               
                              accept_(data.message)
                              }//data.status
                    })
  }
  function accept_(obj){
    if(obj.confirm_time){
        $.each(obj.confirm_time,function(k,v){
          var html='';
              html+='<div class="ui_top_time">\
                        <div style="width:100%;height:2.2rem"></div>\
                        <center><div class="ui_show_time" >'+year_to_month_(v.created_at)+'</div></center>\
                     </div>';
              html+='<section class="ui_invite_container comom_" style="height:28.8rem" data-id="'+v.invite_id+'">\
               <p class="clear color999 f12">考察品牌<span class="fr b color333">'+v.brand_title+'</span></p>\
                <ul class="ui_address">\
                  <li class="f12 color999">考察场地</li>\
                  <li>\
                    <div class="ui_con">\
                       <p class="f12 b color333 toleft">'+v.store_name+'</p>\
                       <p class="f12  color333 margin0 toleft">\
                         <span class="ui_calendar_con">\
                           <img class="ui_calendar fl"  src="/images/020700/gg.png" alt="">\
                            </span>';
                    if(v.inspect_address.length>12){
                    html+='<span style="padding-left: 0.5rem">地址：'+v.inspect_address.substring(0,12)+'…'+'</span>';
                    }else{
                    html+='<span style="padding-left: 0.5rem">地址：'+v.inspect_address+'</span>';
                    }
                    html+='</p>\
                    </div>\
                  </li>\
                </ul>\
                <div style="width:100%;height:1.3rem;clear:both"></div>\
                <p class="clear color999 f12">考察时间<span class="fr color333">'+year_to_month(v.inspect_time)+'</span></p>\
                <p class="clear color999 f12">订金金额<span class="fr color333">￥'+v.currency+'</span></p>\
                <p class="clear color999 f12">支付方式<span class="fr color333">'+v.pay_way+'</span></p>\
                <p class="clear color999 f12">支付时间<span class="fr color333">'+stampchange(v.pay_at)+'</span></p>\
                <p class="clear color999 f12">邀请人<span class="fr color333">'+v.agent+'(经纪人)</span></p>\
                <p class="clear color999 f12 margin0">确认时间<span class="fr color333">'+stampchange(v.confirm_time)+'</span></p>\
             </section>';
             $('.ui_container_accept').append(html);
        })
    }else{
       $('#nocommenttip2').removeClass('none')
    }
  }
  function refuse(uid,type){
            var param = {};
                param["uid"] = uid;
                param["status"]=type;
                var url=labUser.api_path+'/user/inspect-invites/_v020800';
                ajaxRequest(param, url, function (data) {
                if (data.status) {               
                                  refuse_(data.message)
                                  }//data.status
                        })
  }
  function refuse_(obj){
    if(obj.reject_result){
          $.each(obj.reject_result,function(k,v){
            var html='';
                html+='<div class="ui_top_time">\
                          <div style="width:100%;height:2.2rem"></div>\
                          <center><div class="ui_show_time">'+year_to_month_(v.created_at)+'</div></center>\
                       </div>';
                html+='<section class="ui_invite_container comom_" style="height:29rem" data-id="'+v.invite_id+'">\
                        <p class="clear color999 f12">考察品牌<span class="fr b color333">'+v.brand_title+'</span></p>\
                          <ul class="ui_address">\
                            <li class="f12 color999">考察场地</li>\
                            <li>\
                              <div class="ui_con">\
                                 <p class="f12 b color333 toleft">'+v.store_name+'</p>\
                                 <p class="f12  color333 margin0 toleft">\
                                   <span class="ui_calendar_con">\
                                     <img class="ui_calendar fl"  src="/images/020700/gg.png" alt="">\
                                      </span>';
                if(v.inspect_address.length>12){
                html+='<span style="padding-left: 0.5rem">地址：'+v.inspect_address.substring(0,12)+'…'+'</span>';
                }else{
                html+='<span style="padding-left: 0.5rem">地址：'+v.inspect_address+'</span>';
                }
                html+='</p>\
                              </div>\
                            </li>\
                          </ul>\
                          <div style="width:100%;height:1.3rem;clear:both"></div>\
                          <p class="clear color999 f12">考察时间<span class="fr color333">'+year_to_month(v.inspect_time)+'</span></p>\
                          <p class="clear color999 f12">订金金额<span class="fr color333">￥'+v.currency+'</span></p> \
                          <p class="clear color999 f12">邀请人<span class="fr color333">'+v.agent+'(经纪人)</span></p>\
                          <p class="clear color999 f12">状态<span class="fr ff4d4d">已拒绝</span></p>';
                  if(v.remark.length>15){
                   html+='<p class="clear color999 f12">拒绝理由<span class="fr color333">'+v.remark.substring(0,15)+'…'+'</span></p>';
                  }else{
                    html+='<p class="clear color999 f12">拒绝理由<span class="fr color333">'+v.remark+'</span></p>'; 
                  }
                  html+='<p class="clear color999 f12 margin0">确认时间<span class="fr color333">'+stampchange(v.confirm_time)+'</span></p>\
                        </section>';
                  $('.ui_container_refuse').append(html);
          })
    }else{
      $('#nocommenttip3').removeClass('none')
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
                                    return Y + '年' + M + '月'+ D+ '日';
     }
     function year_to_month_(unix) {
                                    var newDate = new Date();
                                    newDate.setTime(unix * 1000);
                                    var Y = newDate.getFullYear();
                                    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
                                    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
                                    return Y + '年' + M + '月';
     }
    function acceptInvestigate(invite_id){
       if (isAndroid) {
        javascript:myObject.acceptInvestigate(invite_id);
        }else if(isiOS){
            var data={
                'inspect_id':invite_id
                    };
            window.webkit.messageHandlers.acceptInvestigate.postMessage(data);
        }
    
     }
      function rejectInvestigate(invite_id){
       if (isAndroid) {
            javascript:myObject.rejectInvestigate(invite_id);
        }else if(isiOS){
            var data={
                'inspect_id':invite_id
                    };
            window.webkit.messageHandlers.rejectInvestigate.postMessage(data);
        } 
     } 
     //跳转到详情页
     $(document).on('click','.comom_',function(){
        var inspect_id=$(this).data('id');
       window.location.href = labUser.path + "webapp/investinvitation/detail/_v020800?inspect_id="+inspect_id;
     })
     $(document).on('click','.ui_refuse',function(){
           var invite_id=$(this).data('id');
           rejectInvestigate(invite_id); 
     })
     $(document).on('click','.ui_accept',function(){
          var  invite_id=$(this).data('id');
          acceptInvestigate(invite_id);  
     })          
     