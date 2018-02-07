//By Hongky
Zepto(function(){
	 new FastClick(document.body);
    var args=getQueryStringArgs();
        id=args['id'];
   var object={
             init:function(id){
                    var params={};
                        params['id']=id;
                    var url=labUser.api_path + '/user/package-detail/_v020902';
                    ajaxRequest(params, url, function(data) {
                        if (data.status){
                                    if(data.message.type==4){//type=4为奖励红包
                                     object.data(data.message);
                                    }
                                    $('.containerBox').removeClass('none');
                                  }
                          })
              },
              data:function(obj){
                  $('.ui-red-bao li').eq(0).find('p').eq(0).text('￥'+obj.amount);
                  $('.ui-red-bao li').eq(1).find('p').eq(1).text(obj.name);
                  $('.ui-red-bao li').eq(1).find('p').eq(2).text(obj.expire_at);
                  $('.slogn').text('联系您的经纪人，商量'+obj.brand_name+'品牌加盟吧！');
                  $('.gochatagent').data('id',obj.agent_id).data('name',obj.agent_name);
                  if(obj.use_scenes==1){
                     $('.ui-red-bao li').eq(1).find('p').eq(3).text('可用于品牌考察抵扣');
                  }else if(obj.use_scenes==2){
                     $('.ui-red-bao li').eq(1).find('p').eq(3).text('可用于品牌合同支付抵扣');
                  }else if(obj.use_scenes==3){
                     $('.ui-red-bao li').eq(1).find('p').eq(3).text('可用于品牌考察和合同支付抵扣二选一');
                  }
                  if(obj.status==0){
                    $('.ui-how-use').removeClass('none');
                   }else if(obj.status==1){
                    $('.ui-has-used').removeClass('none');
                    $('.hasnoagent,.hasagent,.ui-bar').addClass('none');
                    $('.ui-middle p').eq(0).find('span').text(obj.used_info.contract_name);
                    $('.ui-middle p').eq(1).find('span').text(obj.used_info.contract_no);
                    $('.ui-middle p').eq(2).find('span').text(obj.brand_name);
                    $('.ui-middle p').eq(3).find('span').text('￥'+obj.used_info.amount);
                    $('.ui-footer p').eq(0).find('span').text('-￥'+obj.used_info.invention_deduction);
                    $('.ui-footer p').eq(1).find('span').text('-￥'+obj.used_info.common_red_packet);
                    $('.ui-footer p').eq(2).find('span').text('-￥'+obj.used_info.brand_red_packet);
                    $('.ui-footer p').eq(3).find('span').text('-￥'+obj.used_info.reward_red_packet);
                    $('.ui-footer-p p').eq(0).find('span').text('￥'+obj.used_info.total_deduction);
                    $('.ui-footer-p p').eq(1).find('span').text('￥'+obj.used_info.real_pay);
                   }else if(obj.status==-1){
                    $('.ui-how-use').removeClass('none');
                    $('.hasnoagent,.hasagent,.ui-bar').addClass('none');
                    $('.ui-red-bao li').find('p').removeClass('f04').addClass('color999');
                   }
              }
          
        }
     object.init(id);
    $(document).on('click','.gochatagent',function(){
      var id=$(this).data('id'),
          name=(this).data('name');
        goChat('A', id, name);
    })
    function goChat(uType, uid, nickname) {
    if (isAndroid) {
      javascript: myObject.goChat(uType, uid, nickname);
    }
    else if (isiOS) {
         var message = {
                method:'goChat',
                params:{
                    'uType': uType,
                    'id': uid,
                    'name':nickname
                }
            }; 
      window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
    }
  };
});