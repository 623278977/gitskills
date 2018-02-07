// created byhongky
Zepto(function (){
    new FastClick(document.body);
    var urlPath = window.location.href,
        args = getQueryStringArgs(),
        inspect_id = args['inspect_id'];
    var Extend = {
        detail: function (inspect_id) {
            var param = {};
                param["inspect_id"] = inspect_id;
            var url=labUser.agent_path+'/message/show-inspect-invitation/_v010000';
            ajaxRequest(param, url, function (data) {
            if (data.status) {
                                Extend.data(data.message);
                                Extend.style(data.message);
                                $('#act_container').removeClass('none');
                              }
                    })
            },
        data:function(obj){
            if(obj.customer_realname){
              $('#customer_name').html('&nbsp'+obj.customer_realname+'&nbsp');  
          }else{
              $('#customer_name').html('&nbsp'+obj.customer_nickname+'&nbsp');  
          }  
            if(obj.customer_gender=='未知'){
               $('#sex').addClass('none')  
             }else if(obj.customer_gender='女'){
               $('#sex').html('女士'); 
             }else if(obj.customer_gender='男'){
               $('#sex').html('先生'); 
             }    
             $('.ui_onetime').html('&nbsp'+unix_to_gagag(obj.inspect_time)+'&nbsp');
             $('.ui_brand_infor li').eq(1).find('img').attr('src',obj.img);
             $('.ui_brand_infor li').eq(2).html(obj.title);
             $('.storename').html('考察门店：'+obj.store_name);
             $('.belongname').html('所在地区：'+obj.head_address);
             $('.detailname').html('地址:'+obj.totals_address);
             $('.time_name').html(unix_to_parttime(obj.inspect_time));
             $('.curreny_name').html('￥'+obj.default_money);
             $('#agent_name').html('&nbsp'+'('+(obj.agent_realname?obj.agent_realname:obj.agent_nickname)+')'+'&nbsp');
             if(obj.title.length>8){
               $('#brand').html('&nbsp'+'【'+obj.title+'】'+'&nbsp'+'进行实地考察。').parent().addClass('ui_left'); 
               $('.showit').addClass('none'); 
               $('#ui_need').addClass('none');
           }else{
               $('#brand').html('&nbsp'+'【'+obj.title+'】'+'&nbsp');
           }
            
             $('.hahaname').html('&nbsp'+'('+(obj.agent_realname?obj.agent_realname:obj.agent_nickname)+')'+'&nbsp');
             $('.in_time').html('邀请时间：'+change_unix(obj.invite_time));
             $('.ggname').html('跟单经纪人:'+' '+(obj.agent_realname?obj.agent_realname:obj.agent_nickname));
             $('.ui_fixed li').eq(1).data('uid',obj.uid).data('id',obj.invite_id).data('title',obj.title).data('imgUrl',obj.img).data('date',obj.inspect_time).data('store',obj.store_name).data('realname',obj.customer_realname).data('nickname',obj.customer_nickname);
        },
        style:function(obj){
          if(obj.status==0){
            $('.ui_top_fixed').addClass('none');
          }else if(obj.status==1){
            $('.ui_fixed').addClass('none');
            $('.ui_top').css('margin','4.5rem  auto');
            $('.ui_top_fixed').html('投资人:'+obj.customer_realname+'接受了您的品牌考察邀请');
            $('.comform_time').html('确认时间:'+change_unix(obj.confirm_time)).parent().removeClass('none');
            $('.ui_fixeded').html('<a class="fff" href="tel:'+obj.agent_tel+'">联系品牌商务代表</a>').removeClass('none').find('a').css({'display':'block','width':'100%','height':'5.4rem'});
          }else if(obj.status==-1){
            $('.ui_fixed').addClass('none');
            $('.ui_top').css('margin','4.5rem  auto');
            $('.ui_top_fixed').html('投资人：'+obj.customer_realname+'拒绝了您的品牌考察邀请').addClass('refuse');
            $('.comform_time').html('拒绝时间：'+change_unix(obj.confirm_time)).parent().removeClass('none');
            $('.refusebg').html('拒绝理由：'+obj.reason).removeClass('none');
          }else if(obj.status==-2){
            $('.ui_fixed').addClass('none');
            $('.ui_top').css('margin','4.5rem  auto');
            $('.ui_top_fixed').html('投资人：'+obj.customer_realname+'拒绝了您的品牌考察邀请').addClass('refuse');
            $('.comform_time').html('拒绝时间：'+change_unix(obj.confirm_time)).parent().removeClass('none');
            $('.refusebg').html('拒绝理由：'+'已过期').removeClass('none');
          }else if(obj.status==2||obj.status==3){
            $('.ui_fixed').addClass('none');
            $('.ui_top').css('margin','4.5rem  auto');
            $('.ui_top_fixed').html('投资人:'+obj.customer_realname+'接受了您的品牌考察邀请');
            $('.comform_time').html('确认时间:'+change_unix(obj.confirm_time)).parent().removeClass('none');
            $('.ui_fixeded').html('<a class="fff" href="tel:'+obj.agent_tel+'">联系品牌商务代表</a>').removeClass('none').find('a').css({'display':'block','width':'100%','height':'5.4rem'});
          }
        }
      
        }//activityDetail对象    
     Extend.detail(inspect_id);  
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
 $('.ui_fixed li').eq(1).on('click',function(){
     var uid=$(this).data('uid'),
          id=$(this).data('id'),
          title=$(this).data('title'),
          imgUrl=$(this).data('imgUrl'),
          date=$(this).data('date').toString(),
          store=$(this).data('store'),
          realname=$(this).data('realname'),
          nickname=$(this).data('nickname');
          if(realname){
            sendRichMsg(2,'C',uid,id,title,imgUrl,date,store, realname); 
          }else{
            sendRichMsg(2,'C',uid,id,title,imgUrl,date,store,nickname); 
          }
        
 })
 //点击进入详情
 $(document).on('click','.zone',function(){
    window.location.href =labUser.path+'webapp/agent/profit/detail'; 
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
                                return Y + '/' + M + '/' + D + ' ' + h + ':' + m+ ':'+s;
    }
    function unix_to_parttime(unix) {
                                    var newDate = new Date();
                                    newDate.setTime(unix * 1000);
                                    var Y = newDate.getFullYear();
                                    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
                                    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
                                    return Y + '/' + M + '/' + D ;
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
    function unix_to_gagag(unix) {
                                    var newDate = new Date();
                                    newDate.setTime(unix * 1000);
                                    var Y = newDate.getFullYear();
                                    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
                                    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
                                    return  M + '/' + D ;
     }

