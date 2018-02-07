 //zhangxm
Zepto(function() {
	new FastClick(document.body);
	var args = getQueryStringArgs(),
		id = args['customer_id'] || '0',
		uid = args['agent_id'] || '0',
		urlPath = window.location.href,
		origin_mark = args['share_mark'] || 0, //分销参数，分享页用
		code = args['code'] || 0;
	var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
	function getdetail(id,uid) {
		var param = {};
		param['brand_id'] = id;
		param['agent_id'] = uid;
		var	url=labUser.agent_path + '/message/contracts/_v010000';
		ajaxRequest(param,url,function(data){
			if(data.status){
				if(data.message){
//					console.log(data.message)
					//合同列表
					var conHtml = '';
					if(data.message.length>0){
						$.each(data.message, function(i,v) {
						$('.enjoy').removeClass('none');
//						if(v.amount>1000){
//							var	amountk =  parseFloat(v.amount);
//						}else {
//							var	amountk =  v.amount;
//						}
						
//						console.log(parseFloat(1,000))
						 conHtml += '<div class="amount"><div class="top">';
						 conHtml += '<p><span class="brand f11 medium color999">品牌：</span><span class="brand-num f11 medium color999">'+v.brand_name+'</span></p>';
						 conHtml +='<p><span class="jion f11 medium color999">加盟金额&nbsp;&nbsp;</span><span class="money corfd4d4d f11">¥&nbsp;'+v.amount+'</span></p></div>';
						 conHtml += '<div class="pact"><div class="pact-survey pact-sur">';
						 conHtml += '<span class="pact-choose mr1-5" pact-id=\''+JSON.stringify(v)+'\'></span>';
						 conHtml += '<img src="/images/agent/my_contract2.png" class="pact-img"/>&nbsp;';
						 conHtml += '<span class="pact-name b bold f12">'+v.contract_name+'</span></div>';
						 conHtml += '<div class="pact-details none color666">';
						 conHtml += '<p class=" flex"><span class="f12 color666">付款协议</span><span class="f12 color666">'+v.contract_name+'</span></p>';
						 conHtml += '<p class="fline mb1-5 flex"><span class=" f12 color666">加盟品牌</span><span class="f12 color666 mb1-5">'+v.brand_name+'</span></p>';
						 conHtml += '<p class=" flex"><span class="f12 color666">加盟总费用</span><span class="f12 color666">¥&nbsp;'+v.amount+'</span></p>';
						 conHtml += '<p class=" flex"><span class="f12 color666">线上首付</span><span class="f12 color666">¥&nbsp;'+v.first_money+'</span></p>';
						 conHtml += '<p class=" flex"><span class="f12 color666">线下尾款</span><span class="f12 color666">¥&nbsp;'+v.last_money+'</span></p>';
						 conHtml += '<div class="mb1-5 flex f12 text-end">';
						 conHtml += '<span class="f12">缴纳方式</span>';
						 conHtml += '<p class="f12 medium">';
						 conHtml += '<span class=" mb05 color666 f12">线上首付一次结清</span><br /><span class=" mb05 color666 f12">线下尾款银行转账</span><br /><span class="c2873ff mb05 f12 wk_payment">了解尾款补齐操作办法</span><br /></p></div></div>';
						 conHtml += '<p class="pact-flexible remarks"><img src="/images/agent/zhankai.png" class="flexible-img"/></p></div></div>';
						});
					}else {
						conHtml += '<div class="default"><img src="/images/agent/no_contract.png"/></div>';
					}
					conHtml += '<button class="btn f15">确定</button>';
					$('#container').html(conHtml);
				};
				
			}
			
		});
		//选择合同
			var pactArr = [];
			$(document).on('click','.pact-sur',function(){
				$(this).children('.pact-choose').toggleClass('pact-choose-img');
				var chooseY = $(this).parents('.amount').siblings('.amount').find('.pact-choose');
				chooseY.removeClass('pact-choose-img');
				if($(this).children('.pact-choose').hasClass('pact-choose-img')){
				   var pactId = $(this).children('.pact-choose').attr('pact-id');
				   pactArr.push(pactId);
				   if(pactArr.length>1){
				   	pactArr.shift();
				   }
				};
//				console.log($(this).parents('.amount').siblings('.amount').find('.pact-choose'))
//				$(this).toggleClass('pact-choose-img');
				
//				if($(this).children('.pact-choose').is('.pact-choose-img')){
//					pactArr.push(pactId);
//				}else {
//					pactArr.pop(pactId);
//				}
//				console.log(pactId);
//				console.log(pactArr);
//				if(pactArr.length>1){
//					alert('只能选择一份合同！')
//				}else if(pactArr.length==0){
//					alert("请选择一份合同！")
//				}else {
//					pactChoose(pactArr);
//					console.log(pactArr);
//				}
			});
			
		//点击确定
		$(document).on('click','.btn',function(){
//			console.log(pactArr);
			pactChoose(pactArr);
		});
//		$('.btn').click(function(){
//			console.log(pactArr)
//			pactChoose(pactArr);
//		})
//	//选择合同id传给移动端
			function pactChoose(id) {
//				console.log(pactArr)
				if(isAndroid) {
					javascript: myObject.pactChoose(id);
				}
				else if(isiOS) {
					var data = {
						"id": id
					}
					window.webkit.messageHandlers.pactChoose.postMessage(data);
				}
			};
			//尾款补齐操作办法 
			$(document).on('click','.wk_payment',function(){
				window.location.href = labUser.path +'webapp/agent/way/detail';
			});	
			//展开收起合同详情
		$(document).on('click','.pact-flexible',function(){
			$('.enjoy').removeClass('none');
			var flexible = $(this).siblings('.pact-details');
			flexible.toggleClass('none');
			$(this).siblings('.pact-sur').toggleClass('fline');
			$(this).siblings('.pact-sur').toggleClass('pact-survey');
			$(this).siblings('.pact-sur').toggleClass('pact-surveys');
//			$(this).children('.flexible-img').attr('src','/images/agent/shou.png');
			
			if($(this).children('.flexible-img').attr('src')=='/images/agent/zhankai.png'){
				$(this).children('.flexible-img').attr('src','/images/agent/shou.png');
			}else{
				$(this).children('.flexible-img').attr('src','/images/agent/zhankai.png');
			}
			
			
		});
		
	};
	getdetail(id,uid);
	
			
			
	

	
	
	

	
	
	
})