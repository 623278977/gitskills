//zhangxm
Zepto(function(){
	new FastClick(document.body);
	var args = getQueryStringArgs(),
		id = args['id'] || '0',  //合同id
		agent_id = args['agent_id'], //经纪人id
		urlPath = window.location.href,
		origin_mark = args['share_mark'] || 0, //分销参数，分享页用
		code = args['code'] || 0;
		function getdetail(id,agent_id) {
		var param = {};
		param['contract_id'] = id;
		param['agent_id'] = agent_id;
		if(shareFlag) {
			param['guess'] = 1;
		};
		var	url=labUser.agent_path + '/brand/brand-customer/_v010000';
		ajaxRequest(param,url,function(data){
				if(data.status){
					if(data.message){
						var conHtml = '';
						conHtml += '<p class="title bgfont f11 color666"><span class="">根据</span><span class=""> '+data.message.brand_name+' </span><span class="">跟进的投资人，按照跟单时间倒叙排列</span></p>';
						if(data.message.following_customers.length>0){
							conHtml += '<div class="pl1-5 bgwhite">';
							$.each(data.message.following_customers, function(i,v) {
								var begin_time = proDay(v.begin_time);
								
								conHtml += '<div class="bgwhite list_choose"><div class="list bgfont fline"><div class="list-l"><span class="pact-choose mr1" pact-id="'+v.uid+'"'+'pact-name="'+v.nickname+'"></span>';
								conHtml += '<img src="'+ v.avatar +'" class="avatar m1"/>';
								conHtml += '<div class="listl-right" uid="'+v.uid+ '">';
								conHtml += '<p class="mb05 dis_cen">';
								conHtml += '<span class="listl-name  f15 b">'+v.nickname+'</span>';
								if(v.gender=='女'){
										conHtml += '<img src="/images/agent/girl.png" alt="" class="gender" /><br />';	
								}else if(v.gender=='男'){
										conHtml += '<img src="/images/agent/boy.png" alt="" class="gender " /><br />';
								}else {
									conHtml += '<br />'
								};
								conHtml += '</p>';
								conHtml += '<span class="listl-pro f11">'+v.city+'</span></div></div>';
								conHtml += '<div class="list-r"><span class="listr-time f12 color666">'+begin_time+'</span>';
								conHtml += '<span class="listr-begin f12 color666">开始跟单</span><br />';
								conHtml += '<span class="listr-yet f11 color999">已跟单'+v.followed_days+'天</span></div></div></div>';			
							});
							conHtml += '</div>';
						}else {
							conHtml += '<div class="default"><img src="/images/agent/no_client.png"/></div>';
							
						}
						$('.containerBox').html(conHtml);
					}
				}
			})
		};
		getdetail(id,agent_id);
			//选择客户id传给后台
			var pactArr = [];
			$(document).on('click','.list',function(){
				uid = $(this).find('.pact-choose').attr('pact-id');
				
				$(this).find('.pact-choose').toggleClass('pact-choose-img');
				var chooseY = $(this).parent('.list_choose').siblings('.list_choose').find('.pact-choose');
				chooseY.removeClass('pact-choose-img');
				if($(this).find('.pact-choose').hasClass('pact-choose-img')){
					var pactId = $(this).find('.pact-choose').attr('pact-id');
					var pactName = $(this).find('.pact-choose').attr('pact-name');
					var pactArray = {
						'id':pactId,
						'name':pactName
					};
					pactArr.push(pactArray);
					if(pactArr.length>1){
						pactArr.shift();
					};
				}
				
			});
			//跳转投资人详情页
//			$(document).on('click','.listl-right',function(){
//				var uid = $(this).attr('uid');
//				window.location.href = labUser.path + 'webapp/agent/investor/invitation?customer_id='+uid+'&agent_id='+agent_id;
//			})
			function clientChoose(id,type,contract_id) {
				if(isAndroid) {
					javascript: myObject.clientChoose(id[0].id,type,contract_id);
				}
				else if(isiOS) {
					var data = {
						"id": id[0],
						"type":type,
						"contract_id":contract_id
					}
				window.webkit.messageHandlers.clientChoose.postMessage(data);
				}
			};
			//确定按钮
			$(document).on('click','.btn',function(){
//				clientChoose(pactArr);
				var params = {};
//				console.log(pactArr[0].id)
				if(pactArr.length>0){
					params['agent_id'] = agent_id;
					params['brand_contract_id'] = id;
					params['uid'] = pactArr[0].id;
	//				console.log(params)
					var url = labUser.agent_path + '/contract/send/_v010000';
					ajaxRequest(params,url,function(data){
						if(data.status){
							$('.masking').removeClass('none');
							$('.back').attr('contract_id',data.message);
						}else {
	//						alert(data.message);
							tips('合同已存在！');
						}
					})
				}else {
					tips('请选择客户！');
				}
				
			});
			
			
			
			//前往对应聊天窗
			$(document).on('click','.back',function(){
				$('.masking').addClass('none');
				var contract_id = $('.back').attr('contract_id');
//				skip(pactArr,0);
				console.log(contract_id);
				clientChoose(pactArr,1,contract_id);
			})
			
			//停留在电子合同
			$(document).on('click','.stay',function(){
				$('.masking').addClass('none');
				var contract_id = $('.back').attr('contract_id');
//				skip(pactArr,1);
				clientChoose(pactArr,0,contract_id);
			});
			
			//点击跳转相应页面  type=0，返回合同；type=1，前往聊天
//			function skip(id,type){
//				if(type==0){
//					
//				}else if(type==1){
//					
//				}
//			}
	function tips(e){
        $('.common_pops').text(e).removeClass('none');
        setTimeout(function() {
            $('.common_pops').addClass('none');
        }, 1500);
    };		
			
			//时间戳转换
	function proDay(unix) {
	    var newDate = new Date();
	    newDate.setTime(unix * 1000);
	    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
	    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
	    var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
	    var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
	    return M +' '+'月'+' '+ D+' '+ '日';
	};
});
