//By Hongky
Zepto(function(){
	 new FastClick(document.body);
    var args=getQueryStringArgs();
        uid=args['uid'],
        id=args['id'];
   var object={
             init:function(id){
                    var params={};
                        params['id']=id;
                    var url=labUser.api_path + '/user/package-detail/_v020902';
                    ajaxRequest(params, url, function(data) {
                        if (data.status){
                                    if(data.message.type==1){//type=1为通用红包
                                        object.data(data.message);
                                        $('.containerBox').removeClass('none');
                                    }
                                  }
                          })
              },
              data:function(obj){
                  $('.ui-red-bao li').eq(0).find('p').eq(0).text('￥'+obj.amount);
                  $('.ui-red-bao li').eq(1).find('p').eq(1).text(obj.name);
                  $('.ui-red-bao li').eq(1).find('p').eq(2).text(obj.expire_at);
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
                    if(obj.used_info.used_way==='invention'){
                       $('.ui-has-ordered').removeClass('none');
                       $('.ui-middle-p p').eq(1).find('span').text(obj.brand_name);
                    }else{
                      $('#ui-has-used').removeClass('none');
                    }
                      $('.hasagent,.ui-bar').addClass('none');
                      $('.ui-red-bao li').find('p').removeClass('f04').addClass('color999');
                      $('.ui-middle p').eq(0).find('span').text(obj.used_info.contract_name);
                      $('.ui-middle p').eq(1).find('span').text(obj.used_info.contract_no);
                      $('.ui-middle p').eq(2).find('span').text(obj.brand_name);
                      $('.ui-middle p').eq(3).find('span').text('￥'+obj.used_info.amount);
                      $('.ui-footer p').eq(0).find('span').text('-￥'+obj.used_info.invention_deduction);
                      $('.ui-footer p').eq(1).find('span').text('-￥'+obj.used_info.common_red_packet);
                      $('.ui-footer p').eq(2).find('span').text('-￥'+obj.used_info.brand_red_packet);
                      $('.ui-footer p').eq(3).find('span').text('-￥'+obj.used_info.reward_red_packet);
                      $('.ui-footer p').eq(4).find('span').text('￥'+obj.used_info.total_deduction);
                      $('.ui-footer p').eq(5).find('span').text('￥'+obj.used_info.real_pay);
                   }else if(obj.status==-1){
                      $('.ui-how-use').removeClass('none');
                      $('.hasnoagent,.hasagent,.ui-bar').addClass('none');
                      $('.ui-red-bao li').find('p').removeClass('f04').addClass('color999');
                   }
                }
          
        }
     object.init(id);
});