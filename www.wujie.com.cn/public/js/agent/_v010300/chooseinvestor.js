//Created By hongky
	new FastClick(document.body);
		var args=getQueryStringArgs(),
            brand_id = args['brand_id'],
            contract_id=args['contract_id'],  
            agent_id= args['agent_id'];
     var Choose={
           init(brand_id,agent_id,contract_id,uid){
                  var param={};
                      param['brand_id'] = brand_id;
                      param['agent_id'] = agent_id;
                      param['uid']=uid;
                      param['contract_id']=contract_id;
            var url = labUser.agent_path + '/customer/contract-step3/_v010300';
            ajaxRequest(param, url, function(data) {
                if (data.status){
                              Choose.brand(data.message.brand_info);
                              Choose.contract(data.message.contract_info);
                              Choose.investor(data.message);
                              $('.containerBox').removeClass('none')
                           }
                  })
           },
           brand(obj){
                $('.brand').find('.brand_logo').attr('src',obj.logo);
                $('.brand_name').text((obj.name.length<8?obj.name:obj.name.substr(0,8)+'…'));
                $('.brand_text').text(obj.slogan);
                $('.hang_rank').text(obj.category);
                $('.support').text(obj.agency_way);
                $('.ui-num1').text(obj.contract_count);   
           },
           contract(obj){
                $('.ui-name').text(obj.name);
                $('.ui-zone').text(obj.league_type);
                $('.ui-pay').text('￥'+obj.total_cost);
                var str='';
                for(var i=0;i<obj.cost_details.length;i++){
                    str+='<span class="f11 color999">'+obj.cost_details[i].cost_type+'：¥ '+obj.cost_details[i].cost+'</span>';
                }
                $('.ui-name-cost').append(str);
                $('.ui-get-maxmoeny').text('可提成佣金部分'+' '+obj.max_commission); 
                $('.textEnd').data('address',obj.address);     
           },
           investor(obj){
                $('.ui-num2').text(obj.customers);
                if(obj.user_info){
                    $('.avatar').attr('src',obj.user_info.avatar);
                    $('.investorMes p').eq(0).find('span').text(obj.user_info.nickname);
                    if(obj.user_info.gender==0){
                        $('.grade').attr('src','/images/agent/girl.png');
                    }else if(obj.user_info.gender==1){
                        $('.grade').attr('src','/images/agent/boy.png');
                    }else if(obj.user_info.gender==-1){
                        $('.grade').addClass('none');
                    }
                    $('.ui-place').text(obj.user_info.zone);
                    $('.chooseAgain').removeClass('none');
                    $('.ui-get-investor').addClass('none');
                    $('.fixed-bottom-iphoneX').removeClass('none');
                    $('.investor').removeClass('none');
                }
           }
     }
     Choose.init(brand_id,agent_id,contract_id);
    //展开查看详情
    $(document).on('click','.unfold',function(){
    	$(this).addClass('none');
    	$(this).siblings('.planDetail').removeClass('none');
    });
    //选择加盟方案
    $(document).on('click','.packageType',function(){
    	$(this).children('.chooseNo').addClass('choose_img');
    	$(this).parent('.plan').siblings().find('.chooseNo').removeClass('choose_img');
    });
	 //跳转投资人列表
    $(document).on('click','.ui-get-investor',function(){
        window.location.href=labUser.path+'webapp/agent/investorlist/detail?agent_id='+agent_id+'&contract_id='+contract_id+'&brand_id='+brand_id;
    })
    
  
 // 判断有无客户的方法：
     function ownInvestoruid(uid){ 
     	    $('.ui-name-cost').empty();
          Choose.init(brand_id,agent_id,contract_id,uid);
          $('.containerBox').data('uid',uid);
  }  
   //跳转s4    
    $(document).on('click','.fixed-bottom-iphoneX',function(){
        var uid=$('.containerBox').data('uid');
           console.log(uid);
        window.location.href=labUser.path+'/webapp/agent/createsuccess/detail?agent_id='+agent_id+'&brand_id='+brand_id+'&contract_id='+contract_id+'&uid='+uid;
    })
    // ownInvestoruid(2);
    //重新选择投资人
    $(document).on('click','.chooseAgain',function(){
        var uid=$('.containerBox').data('uid');
        window.location.href=labUser.path+'webapp/agent/investorlist/detail?agent_id='+agent_id+'&contract_id='+contract_id+'&brand_id='+brand_id+'&uid='+uid;
    })
    function getInvestoragain(uid) {
    if (isiOS) {
           var message = {
                method:'getInvestoragain',
                params:uid
            }; 
        window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
    } else if (isAndroid) {
        javascript:myObject.getInvestoragain(uid);
    }
}
$(document).on('click','.textEnd',function(){
         var url=$(this).data('address');
         window.location.href = labUser.path +'js/agent/generic/web/viewer.html?file='+url;
});
iphonexBotton('.setup');
//if(isiOS){
//	if (window.screen.height === 812) {
//	    $('.setup').css('bottom', '17px');
//	  }
//	$('.iphone_btn').removeClass('none');
//}
   