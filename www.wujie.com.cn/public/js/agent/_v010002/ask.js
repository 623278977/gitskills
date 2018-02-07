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
                                Extend.go();
                                $('#act_container').removeClass('none');

                              }else{
                                Extend.tips(data.message);
                              }
                    })
            },
        data:function(obj){
                    $('.ui_act_con').data('id',obj.activity_id).data('agent_id',obj.agent_id);
                  if(obj.custom_realname){
                    $('#customer_name').html(' '+obj.custom_realname+' ');  
                    }else{
                    $('#customer_name').html(' '+obj.custom_nickname+' ');  
                    }
                    $('#agent_name').html(' '+'('+(obj.agent_realname?obj.agent_realname:obj.agent_nickname)+')'+' ');
                    $('.ui_act_con li').eq(0).find('img').attr('src',obj.img);
                  if(obj.title.length>15){
                    $('.ui_act_con li').eq(1).find('p').eq(0).html(obj.title).addClass('ui-nowrap-multi'); 
                  }else{
                    $('.ui_act_con li').eq(1).find('p').eq(0).html(obj.title).addClass('margin11');
                    $('.ui_act_con li').eq(1).find('p').eq(1).addClass('margin11'); 
                    $('.ui_act_con li').eq(1).find('p').eq(2).addClass('margin11'); 
                  };
                    $('.ui_act_con li').eq(1).find('p').eq(1).html('开始时间：'+stampchange(obj.begin_time));
                    $('.ui_act_con li').eq(1).find('p').eq(2).html('活动地点：'+obj.citys);
                    $('#name_agent').html(' '+'('+(obj.agent_realname?obj.agent_realname:obj.agent_nickname)+')').removeClass(' f15  ffaf32');
                    $('.name_agent').html('跟单经纪人：'+(obj.agent_realname?obj.agent_realname:obj.agent_nickname));
                    $('.in_time').html('邀请时间：'+change_unix(obj.invite_time));
                    if(obj.custom_gender==-1){
                        $('#sex').addClass('none')
                     }else if(obj.custom_gender==1){
                        $('#sex').html('先生')
                     }else if(obj.custom_gender==0){
                        $('#sex').html('女士') 
                     }
            $('.ui_fixed li').eq(1).data('id',obj.invite_id).data('uid',obj.uid).data('imgUrl',obj.img).data('title',obj.title).data('realname',obj.custom_realname).data('nickname',obj.custom_nickname);
        },
        status:function(obj){
            if(obj.status==0){
                     $('.ui_top_fixed').addClass('none');
               }else if(obj.status==1){
                     $('.ui_fixed').addClass('none');
                     $('.ui_top').css('margin','4.5rem  auto');
                    if(obj.custom_realname){
                     $('.ui_top_fixed').html('投资人：'+obj.custom_realname+'已经接受了您的OVO活动邀请'); 
                    }else{
                     $('.ui_top_fixed').html('投资人：'+obj.custom_nickname.substring(0,2)+'…'+'已经接受了您的OVO活动邀请'); 
                    }  
                     $('.comform_time').html('确定时间：'+change_unix(obj.confirm_time)).parent().removeClass('none').next().removeClass('none');
               }else if(obj.status==-1){
                     $('.ui_fixed').addClass('none');
                     $('.ui_top').css('margin','4.5rem  auto');
                    if(obj.custom_realname){
                     $('.ui_top_fixed').html('投资人：'+obj.custom_realname+'已经拒绝了您的OVO活动邀请').addClass('refuse'); 
                    }else{
                     $('.ui_top_fixed').html('投资人：'+obj.custom_nickname.substring(0,2)+'…'+'已经拒绝了您的OVO活动邀请').addClass('refuse');
                     }  
                     $('.comform_time').html('拒绝时间：'+change_time(obj.confirm_time)).parent().removeClass('none').next().removeClass('none');
                     $('.refusebg').html('拒绝原因：'+obj.reason).removeClass('none');
               }else if(obj.status==-2){
                     $('.ui_fixed').addClass('none');
                     $('.ui_top').css('margin','4.5rem  auto');
                    if(obj.custom_realname){
                     $('.ui_top_fixed').html('投资人：'+obj.custom_realname+'已经拒绝了您的OVO活动邀请').addClass('refuse'); 
                   }else{
                     $('.ui_top_fixed').html('投资人：'+obj.custom_nickname.substring(0,2)+'…'+'已经拒绝了您的OVO活动邀请').addClass('refuse');
                   }  
                     $('.comform_time').html('拒绝时间：'+change_time(obj.confirm_time)).parent().removeClass('none').next().removeClass('none');
                     $('.refusebg').html('拒绝原因：'+'已过期').removeClass('none');
               }else if(obj.status==-3){
                     $('.ui_container').addClass('none');
                     $('.ui_onmessage').removeClass('none');
               }
        },
        go:function(){
                    $(document).on('click','.ui_act_con',function(){
                      var activity_id=$(this).data('id'),
                          agent_id=$(this).data('agent_id');
                     window.location.href = labUser.path + "webapp/agent/activity/detail?id=" + activity_id + "&agent_id=" +agent_id;
                    })
        },
        tips:function(e){
            $('.tips').text(e).removeClass('none');
          setTimeout(function() {
            $('.tips').addClass('none');
          }, 1500);
        }
    }//activityDetail对象    
     Extend.detail(invite_id);  
      // 再次发送
 function sendRichMsg(type,uType,uid,id,title,imgUrl,date,store,nickname,amount,league_type){
        if (isAndroid) {
            javascript:myObject.sendRichMsg(type,uType,uid,id,title,imgUrl,date,store,amount,league_type);
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
                'nickname':nickname,
                 'amount':amount,
                 'league_type':league_type
                    };
            window.webkit.messageHandlers.sendRichMsg.postMessage(data);
        }
    }
 $('.ui_fixed li').eq(1).on('click',function(){
     var uid=$(this).data('uid'),
           id=$(this).data('id'),//邀请函id
           title=$(this).data('title'),//活动标题
           imgUrl=$(this).data('imgUrl'),
           realname=$(this).data('realname'),
           nickname=$(this).data('nickname')
           if(realname){
            sendRichMsg(1,'C',uid,id,title,imgUrl,'','',realname,'','');
           }else{
            sendRichMsg(1,'C',uid,id,title,imgUrl,'','',nickname,'','');
           }
    
 })
  $('.ui_fixed li').eq(2).on('click',function(){ 
       shutUp(invite_id);
 })
 //关闭邀请函方法
  function shutUp(invite_id){
            var param={};
                param['invite_id']=invite_id;
             var url=labUser.agent_path+'/user/back-out/_v010002';
             ajaxRequest(param, url, function (data) {
                   if(data.status){
                     $('.ui_container').addClass('none');
                     $('.ui_onmessage').removeClass('none');                        
                   }else{
                          Extend.tips(data.message);
                   }
            })  
           }
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
                                return  M + '月' + D +'日'+ ' ' + h + ':' + m;
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