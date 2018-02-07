// created byhongky
Zepto(function (){
new FastClick(document.body);
var urlPath = window.location.href,
        args = getQueryStringArgs(),
        invite_id = args['invite_id'];
    var Extend = {
        detail: function (invite_id) {
            var param = {};
            param["invite_id"] = invite_id;
            var url=labUser.agent_path+'/message/show-active-invitation/_v010000';
            ajaxRequest(param, url, function (data) {
            if (data.status) {
                                Extend.data(data.message);
                                Extend.status(data.message);
                                $('#act_container').removeClass('none');

                              }//data.status
                    })// ajaxRequest
            },//detail方法 
        data:function(obj){
            if(obj.custom_realname){
              $('#customer_name').html(obj.custom_realname)  
          }else{
              $('#customer_name').html(obj.custom_nickname);
          }  
           if(obj.is_public_realname==1){
              $('#agent_name').html('('+(obj.agent_realname?obj.agent_realname:obj.agent_nickname)+')');
              $('#name_agent').html('('+(obj.agent_realname?obj.agent_realname:obj.agent_nickname)+')');
              $('.name_agent').html('跟单经纪人：<span class="b">'+(obj.agent_realname?obj.agent_realname:obj.agent_nickname)+'</span>');
           }else if(obj.is_public_realname==0){
              $('#agent_name').html('('+obj.agent_nickname+')');
              $('#name_agent').html('('+obj.agent_nickname+')');
              $('.name_agent').html('跟单经纪人：<span class="b">'+obj.agent_nickname+'</span>');
           }
            $('.ui_act_con').data('id',obj.activity_id).data('uid',obj.uid);
            $('.ui_act_con li').eq(0).find('img').attr('src',obj.img);
            if(obj.title.length>13){
              $('.ui_act_con li').eq(1).find('p').eq(0).html(obj.title).addClass('ui-nowrap-multi'); 
            }else{
              $('.ui_act_con li').eq(1).find('p').eq(0).html(obj.title).addClass('margin11');
              $('.ui_act_con li').eq(1).find('p').eq(1).addClass('margin11'); 
              $('.ui_act_con li').eq(1).find('p').eq(2).addClass('margin11'); 
            };
             $('.ui_act_con li').eq(1).find('p').eq(1).html('开始时间：'+stampchange(obj.begin_time));
             $('.ui_act_con li').eq(1).find('p').eq(2).html('活动地点：'+obj.citys); 
             $('.in_time').html('邀请时间：'+change_unix(obj.invite_time));
             if(obj.custom_gender==1){
              $('#sex').html('先生').removeClass('none')
             }else if(obj.custom_gender==0){
               $('#sex').html('女士').removeClass('none')
             }else if(obj.custom_gender==-1){
               $('#sex').addClass('none')
             }
            $('.ui_fixed li').eq(0).data('id',obj.activity_id).data('agent_id',obj.agent_id);
            $('.ui_fixed li').eq(1).data('id',obj.activity_id).data('agent_id',obj.agent_id);
        },
        status:function(obj){
               if(obj.status==0){
                $('.ui_top_fixed').addClass('none');
            //适配iphoneX
                iphonexBotton('.ui_fixed')
               }else if(obj.status==1){
                $('.ui_top').css('margin','4.5rem  auto');
                $('.ui_fixed').addClass('none');
                if(obj.is_public_realname==1){
                $('.ui_top_fixed').html('接受了来自'+(obj.agent_realname?obj.agent_realname:obj.agent_nickname)+'的OVO活动邀请');  
                }else if(obj.is_public_realname==0){
                $('.ui_top_fixed').html('接受了来自'+obj.agent_nickname+'的OVO活动邀请');   
                } 
                $('.comform_time').html('确定时间：'+change_unix(obj.confirm_time)).parent().next().removeClass('none');
               }else if(obj.status==-1){
                $('.ui_top').css('margin','4.5rem  auto');
                $('.ui_fixed').addClass('none');
                if(obj.is_public_realname==1){
                $('.ui_top_fixed').html('拒绝了来自'+(obj.agent_realname?obj.agent_realname:obj.agent_nickname)+'的OVO活动邀请').addClass('refuse'); 
               }else if(obj.is_public_realname==0){
                $('.ui_top_fixed').html('拒绝了来自'+obj.agent_nickname+'的OVO活动邀请').addClass('refuse'); 
               }  
                $('.comform_time').html('拒绝时间：'+change_time(obj.confirm_time)).parent().removeClass('none').next().removeClass('none');
                $('.refusebg').html('拒绝原因：'+obj.reason).removeClass('none');
               }else if(obj.status==-2){
                $('.ui_top').css('margin','4.5rem  auto');
                $('.ui_fixed').addClass('none');
                if(obj.is_public_realname==1){
                $('.ui_top_fixed').html('拒绝了来自'+(obj.agent_realname?obj.agent_realname:obj.agent_nickname)+'的OVO活动邀请').addClass('refuse'); 
               }else if(obj.is_public_realname==0){
                $('.ui_top_fixed').html('拒绝了来自'+obj.agent_nickname+'的OVO活动邀请').addClass('refuse'); 
               }  
                $('.comform_time').html('拒绝时间：'+change_time(obj.confirm_time)).parent().removeClass('none').next().removeClass('none');
                $('.refusebg').html('拒绝原因：'+'已过期').removeClass('none');
               }else if(obj.status==-3){
                $('.ui_container').addClass('none');
                $('.ui_onmessage').removeClass('none');
                iphonexBotton('.ui_fixed')
               }
        }
        }//activityDetail对象    
     Extend.detail(invite_id);  

 $('.ui_fixed li').eq(1).on('click',function(){
     var id= invite_id;
     var agent=$(this).data('agent_id');
     var activity_id=$(this).data('id');
     acceptActivityInvitation(id,agent,activity_id); 
 })
 $('.ui_fixed li').eq(0).on('click',function(){
     var id= invite_id;
     var agent=$(this).data('agent_id');
     var activity_id=$(this).data('id');
     rejectActivityInvitation(id,agent,activity_id); 
 })
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
 function rejectActivityInvitation(id,agent_id,activity_id){
    if (isAndroid) {
        javascript:myObject.rejectActivityInvitation(id,agent_id,activity_id);
    }else if(isiOS){
        var data={
            'invite_id':id,
            'agent_id':agent_id,
            'activity_id':activity_id
                };
        window.webkit.messageHandlers.rejectActivityInvitation.postMessage(data);
    }
 }  
 $(document).on('click','.ui_act_con',function(){
    var id=$(this).data('id'),
        uid=$(this).data('uid');
    window.location.href=labUser.path+"webapp/activity/detail/_v020800?id="+id+"&uid="+uid+'&is_tag=1';
 })  
});//zepto外层

     function stampchange(unix){
                                var newDate = new Date();
                                newDate.setTime(unix * 1000);
                                var Y = newDate.getFullYear();
                                var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
                                var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
                                var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
                                var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
                                var s = newDate.getSeconds() < 10 ? '0' + newDate.getSeconds() : newDate.getSeconds();
                                return  M + '/' + D +' '+ ' ' + h + ':' + m;
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
                                return Y + '/' + M + '/' + D +'/'+ ' ' + h + ':' + m+ ':'+s;
    }
    function unix_to_parttime(unix) {
                                    var newDate = new Date();
                                    newDate.setTime(unix * 1000);
                                    var Y = newDate.getFullYear();
                                    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
                                    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
                                    return Y + '年' + M + '月' + D + '日';
     }
     function change_time(unix){
                                var newDate = new Date();
                                newDate.setTime(unix * 1000);
                                var Y = newDate.getFullYear();
                                var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
                                var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
                                var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
                                var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
                                var s = newDate.getSeconds() < 10 ? '0' + newDate.getSeconds() : newDate.getSeconds();
                                return Y + '年'+  M + '月' + D +'日'+ ' ' + h + ':' + m;
    }
    function tips(e) {
         $('.tips').text(e).removeClass('none');
        setTimeout(function() {
            $('.tips').addClass('none');
        }, 1500);

    }; 
  