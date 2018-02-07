//By Hongky
Zepto(function(){
	 new FastClick(document.body);
   var args=getQueryStringArgs(),
       uid=args['uid'],
       agent_id=args['agent_id'],
       id= args['id'];
   var Fudai={
             init:function(agent_id,id,uid){
                  var param={};
                      param['type']='customer';
                      param['uid'] = uid;
                      param['agent_id'] = agent_id;
                      param['agent_get_red_id'] = id;
             var url = labUser.agent_path + '/lucky-bag/look-red-details/_v010300';
             ajaxRequest(param, url, function(data) {
                if (data.status){
                             Fudai.data(data.message);
                             Fudai.investor(data.message.use_person);
                             $('#containerBox').removeClass('none');
                           }
                  })
           },
           data:function(obj){
                    $('.ui-a').text('经纪人：'+obj.use_agent.agent_nickname+'的红包');
                    $('header img').attr('src',obj.use_agent.agent_avatar);
                    if(obj.brand_logo){
                      $('.ui-size2').attr('src',obj.brand_logo); 
                    }else{
                      $('.ui-red-detail li').eq(0).css({'width':'0.0001rem'}).find('img').addClass('none');
                      $('.ui-red-detail li').eq(1).css({'width':'50%'});
                      $('.ui-red-detail li').eq(2).css({'width':'50%'});
                    }    
                    $('.name').text(obj.red_name);
                    $('.type').text((obj.red_support_type.length<14?obj.red_support_type:obj.red_support_type.substr(0,14)+'…'));
                    $('.time').text('有效期至'+obj.red_expire_at);
                    $('.meony').text('￥'+obj.red_limit);
                    $('.meony2').text('满'+obj. min_consume+'减'+obj.red_limit);
                    $('footer').html('<a  href="tel:4000110061" style="color:#2873ff;display:block;width:100%;height:100%">联系我们</a>');
           },
           stamp:function(unix){
             var newDate = new Date();
                  newDate.setTime(unix * 1000);
                  var Y = newDate.getFullYear(),
                      M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1,
                      D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();  
                  return Y+'.'+ M + '.' + D ;
           },
           investor:function(obj){
                   if(obj){
                    $('.title-top').text(obj.open_time);
                    $('.ui-size3').attr('src',obj.user_logo);
                    $('.red-pocket-detail li').eq(1).find('p').eq(0).html('<span class="color333 f15">'+(obj.user_name.length<6?obj.user_name:obj.user_name.substr(0,6)+'…')+'</span>('+obj.user_tel+')');
                    $('.red-pocket-detail li').eq(1).find('p').eq(1).text(unix_to_datetime(obj.time));
                    $('.gochat').data('id',obj.uid).data('nickname',obj.user_name);
                    $('.ui-da-kai').removeClass('none')
                   }else{
                    $('.ui-tips').removeClass('none')
                   }
           }
          
        }
     Fudai.init(agent_id,id,uid);
     function unix_to_datetime(unix) {
        var newDate = new Date();
            newDate.setTime(unix * 1000);
            var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
            var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
            var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
            var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
        return M + '-' + D + '  ' + h + ':' + m;
    }
});