Zepto(function(){
		new FastClick(document.body);
		var args=getQueryStringArgs(),
		id = args['id'] || '0',  //用户id         
		urlPath = window.location.href,
		origin_mark = args['share_mark'] || 0,//分销参数，分享页用
		code = args['code'] || 0;
		function getdetail(data){
			var param = {};
			param['uid'] = id;
			var url = labUser.api_path + '/user/success-brands/_v020800';
			ajaxRequest(param,url,function(data){
				if(data.status){ 
					var conHtml = '';
					if(data.message.length>0){
						$.each(data.message, function(i,v) {
							conHtml += '<div class="bgwhite "><div class="head wrap brand_jump" brand_id="'+v.brand.id+'"><div class="logo mb1 messa-l"><img src="'+v.brand.logo+'" class="mr1-33"/>';
							conHtml += '<div class=""><span class="bold f14 mb1">'+v.brand.name+'</span><br />';
							conHtml += '<span class="f12">行业分类：</span><span class="cffac00 f12 mr2">'+v.brand.category_name+'</span></br>';
							conHtml += '<span class="f12">启动资金：</span><span class="cff4d64 f12">'+v.brand.investment_min+'~'+v.brand.investment_max+'万元</span></div></div>';
							conHtml += '<div class="black_to"><img src="/images/agent/black_to.png"/></div></div>';
							conHtml += '<div class="fline"></div>';
							conHtml += '<div class="agent mt1 mb1-33 wrap"><span class="mr1-33 f14 color333">成单经纪人</span>';
							conHtml += '<div class="messa head">';
							conHtml += '<div class="messa-l" agent_id="'+v.agent.id+'"><img src="'+v.agent.avatar+'" alt="" class="avatar mr05" />';
							conHtml += '<div class=""><span class="mb08 bold f14">'+v.agent.nickname+'</span><br />';
							conHtml += '<div class="order_gender">'
							if(v.agent.gender == 0){
								conHtml += '<img src="/images/agent/girl.png" class="gender"/>';
							}else if (v.agent.gender == 1){
								conHtml += '<img src="/images/agent/boy.png" class="gender"/>';
							}
							conHtml += '<span class="f12 color999 medium">&nbsp; '+v.agent.city+'</span>';
							conHtml += '</div></div></div>';
//							conHtml += '<div class="relation"><a href="tel:'+v.agent.tell+'"><img src="/images/agent/red-tel3.png" alt="" class="tel" agent_id="'+v.agent.id+'"/></a>';
							conHtml += '<div class="relation"><img src="/images/agent/red-tel3.png" alt="" class="tel" agent_id="'+v.agent.id+'" tel="'+v.agent.username+'"/>';
							conHtml += '<img src="/images/agent/red-mes3.png" alt="" class="mes" agent_id="'+v.agent.id+'" nickname="'+v.agent.nickname+ '"/></div>';
							conHtml += '</div></div>';
							conHtml += '<p class="time wrap"><span class="mr1-33 f14 color333">成单时间</span>';
							conHtml += '<span class="f14 color333">'+unix_to_yeardate(v.created_at)+'</span></p>';
							conHtml += '<div class="btn head medium wrap">';
							if(v.hasEvaluate==0){
								conHtml += '<span class="btn-com pt-c f14" agent_id="'+v.agent.id+ '" brand_id="'+v.brand.id+'" contract_id="'+v.contract_id+'">立即评价</span>'
							}else if(v.hasEvaluate==1){
								conHtml += '<span class="btn-com-look pt-b f14" agent_id="'+v.agent.id+ '" brand_id="'+v.brand.id+'" >查看我的评价</span>';
							};
							conHtml += '<span class="btn-pac pt-b f14" contract_id="'+v.contract_id+'">查看电子合同</span>';
							conHtml += '<span class="btn-pay pt-b f14" contract_id="'+v.contract_id+'">查看付款详情</span>';
							
							conHtml += '</div></div>';
						});
					}else {
						conHtml += '<div class="define"><img src="/images/agent/brand_list.png" class="brand_list"/></div>';
					};
				}else {
					conHtml += '<div class="define"><img src="/images/agent/brand_list.png" class="brand_list"/></div>';
				};
				$('#container').html(conHtml);
			})
		};
		getdetail(id);
		
		//跳转相关品牌
		$(document).on('click','.brand_jump',function(){
			var brand_id = $(this).attr('brand_id');
			window.location.href = labUser.path + '/webapp/brand/detail/_v020800?uid='+id+'&id='+brand_id;
		})
		//跳转相关经纪人
		$(document).on('click','.messa-l',function(){
			var agent_id = $(this).attr('agent_id');
			window.location.href = labUser.path + '/webapp/myagent/agent_detail/_v020800?agent_id='+agent_id+'&uid='+id+'&customer_id='+id;
		});
		
		//聊天
	function goChat(uType,uid, nickname) {
		if(isAndroid) {
			javascript: myObject.goChat(uType,uid, nickname);
		}
		else if(isiOS) {
			var data = {
				"uType": uType,
				"nickname": nickname,
				"uid":uid
			}
			window.webkit.messageHandlers.goChat.postMessage(data);
		}
	};
	//电话
	function callNum(tel) {
		if(isAndroid) {
			javascript: myObject.callNum(tel);
		}
		else if(isiOS) {
			var data = {
				"tel":tel
			}
			window.webkit.messageHandlers.callNum.postMessage(data);
		}
	};
	//评价促单经纪人 agent_id:经纪人id brand_id:品牌id contract_id:合同id
	function comments(agent_id,brand_id,contract_id) {
		if(isAndroid) {
			javascript: myObject.comments(agent_id,brand_id,contract_id);
		}
		else if(isiOS) {
			var data = {
				"brand_id": brand_id,
				"agent_id":agent_id,
				"contract_id":contract_id
			}
			window.webkit.messageHandlers.comments.postMessage(data);
		}
	};
	//查看评价  brand_ids:品牌id  uid：经纪人id
	function look_comment(agent_id,brand_id) {
		if(isAndroid) {
			javascript: myObject.look_comment(agent_id,brand_id);
		}
		else if(isiOS) {
			var data = {
				"brand_id": brand_id,
				"agent_id":agent_id
			}
			window.webkit.messageHandlers.look_comment.postMessage(data);
		}
	};
	//打电话
	$(document).on('click', '.tel', function() {
		var tel = $(this).attr('tel');
		callNum(tel);
	});
	//聊天
	$(document).on('click', '.mes', function() {
		var uid = $(this).attr('agent_id');
		var nickname = $(this).attr('nickname');
		goChat('c',uid, nickname);
	});
	//跳转电子合同页
	$(document).on('click','.btn-pac',function(){
		var contract_id = $(this).attr('contract_id');
		window.location.href = labUser.path + '/webapp/client/pactdetails/_v020800?contract_id='+contract_id;
	});
	//跳转付款情况页
	$(document).on('click','.btn-pay',function(){
		var contract_id = $(this).attr('contract_id');
		window.location.href = labUser.path + 'webapp/client/payment/_v020800?id='+contract_id;
	});
	
	//前往评价
	$(document).on('click','.btn-com',function(){
		var agent_id = $(this).attr('agent_id');
		var brand_id = $(this).attr('brand_id');
		var contract_id = $(this).attr('contract_id');
		comments(agent_id,brand_id,contract_id);
//		console.log($(this).parent('.btn').siblings('.agent').children('.messa').children('.messa-l').attr('agent_id'));
	});
	
	//查看我的评价
	$(document).on('click','.btn-com-look',function(){
		var agent_id = $(this).attr('agent_id');
		var brand_id = $(this).attr('brand_id');
		look_comment(agent_id,brand_id);
	});
	
function unix_to_yeardate(unix) {
    var newDate = new Date();
    newDate.setTime(unix * 1000);
    var Y = newDate.getFullYear();
    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
    return Y + '年' + M + '月' + D + '日';
}
});
