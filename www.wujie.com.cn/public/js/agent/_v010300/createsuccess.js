//zhangxm

	new FastClick(document.body);
		var args=getQueryStringArgs(),
            agent_id = args['agent_id'] || '0',   //经纪人id
            brand_id = args['brand_id'] || '0',   //品牌id
            contract_id = args['contract_id'] || '0',   //合同模版id
            uid = args['uid'] || '0',   //客户id
            urlPath = window.location.href,
		    shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
    	function getdetail(agent_id,brand_id,contract_id,uid){
    		var param = {};
    			param['agent_id'] = agent_id;
    			param['brand_id'] = brand_id;
    			param['brand_contract_id'] = contract_id;
    			param['uid'] = uid;
    		var url = labUser.agent_path + '/customer/contract-step4/_v010300';
    		ajaxRequest(param,url,function(data){
    			var conHtml = '';
	    		if(data.status){
	    			if(data.message!=''){
	    				//头部
	    				conHtml+='<div class="create_success bgwhite mb1-2 pb2">';
	    				conHtml+='<img src="/images/agent/createSuccess.png" class="mt2 mb05"/>';
	    				conHtml+='<p class="f15 ">成功发送品牌加盟至投资人</p>';
	    				conHtml+='<p class="f13 color999 mt05">等待对方确认并支付首付款项</p>';
	    				conHtml+='</div>';
	    				//目标品牌
	    				conHtml+='<div class="choose mt1-2 bgwhite">';
	    				conHtml+='<p class="pt1-5 pb1-5 fline"><span class="f15 color333">目标品牌</span>';
	    				conHtml+='<div class="brand_list">';
	    				conHtml+='<div class="chooseBrand fline">';
	    				conHtml+='<div class="brand">';
	    				conHtml+='<p class="brand_logo mr1"><img src="'+data.message.brand_info.logo+'"/></p>';
	    				conHtml+='<div class="">';
			  			conHtml+='<p class="f14 color333 brand_name">'+data.message.brand_info.name+'</p>';
			  			conHtml+='<p class="f11 color999 mb1-2 brand_text">'+data.message.brand_info.slogan+'</p>';
			  			conHtml+='<p style="width: 12rem;"><span class="f12 color666 l_h12">行业分类：</span><span class="f12 color333 l_h12">'+data.message.brand_info.category+'</span></p> ';
	    				conHtml+='</div>';
	    				conHtml+='</div>';
	    				conHtml+='<p class="textEnd">';
			  			conHtml+='<span class="f11 color999">支持：</span><span class="support f11 color999">'+data.message.brand_info.agency_way+'</span><br />';
			  			conHtml+='<span class="f11 color999">该品牌有 <em class="brand_num f11 c2873ff">'+data.message.brand_info.contract_count+'</em> 个加盟方案</span>';
			  			conHtml+='</p>';
			  			conHtml+='</div>';
			  			conHtml+='</div>';
			  			conHtml+='</div>';
			  			//目标投资人
	    				conHtml+='<div class="chooseclient mt1-2 bgwhite">';
	    				conHtml+='<p class="pt1-5 pb1-5 fline"><span class="f15 color333">目标投资人</span></p>';
	    				conHtml+='<div class="investor">';
	    				conHtml+='<div class="">';
	    				conHtml+='<p class="mr1"><img src="'+data.message.customer_info.avatar+'" class="avatar"/></p>';
	    				conHtml+='<div class="investorMes">';
	    				conHtml+='<p class=""><span class="f15 color333 mr05">'+data.message.customer_info.nickname+'</span>';
	    				if(data.message.customer_info.gender==0){  //女
	    					conHtml+='<img src="/images/agent/girl.png" class="grade" />';
	    				}else if(data.message.customer_info.gender==1){ //男
	    					conHtml+='<img src="/images/agent/boy.png" class="grade" />';
	    				};
	    				conHtml+='</p>';
	    				conHtml+='<p class=""><span class="f12 color666">'+data.message.customer_info.zone+'</span></p>';
	    				conHtml+='</div>';
	    				conHtml+='</div>';
	    				conHtml+='<span class="chat scale-1 f14 c2873ff" uid="'+data.message.customer_info.uid+'" brand_name="'+data.message.brand_info.name+'" real_name="'+data.message.customer_info.nickname+'">与他聊聊</span>';
	    				conHtml+='</div>';
	    				conHtml+='</div>';
	    				//加盟方案
	    				conHtml+='<div class="choosePlan mt1-2 bgwhite" contractName="'+data.message.contract_info.name+'" league_type="'+data.message.contract_info.league_type+'" total_cost="'+data.message.contract_info.total_cost+'" contract_ids="'+data.message.contract_info.id+'">';
	    				conHtml+='<p class="pt1-5 pb1-5 fline"><span class="f15 color333">选择加盟方案</span>';
	    				conHtml+='<div class="plan">';
	    				conHtml+='<div class="packageType mb1-5">';
	    				conHtml+='<p class="lh2-3"><span class="f12 color666">加盟方案</span><span class="f12 color666">'+data.message.contract_info.name	+'</span></p>';
	    				conHtml+='<p class="lh2-3"><span class="f12 color666">加盟类型</span><span class="f12 color666">'+data.message.contract_info.league_type+'</span></p>';
	    				conHtml+='<p class="lh2-3"><span class="f12 color666">总费用</span><span class="f12 cfd4d4d">¥ '+data.message.contract_info.total_cost	+'</span></p>';
	    				conHtml+='</div>';
	    				conHtml+='<div class="planDetail  bgf2f2 ">';
	    				conHtml+='<div class="costDetail">';
	    				conHtml+='<p class="f11 color666">费用明细</p>';
	    				
	    				conHtml+='<p class="">';
	    				$.each(data.message.contract_info.cost_details, function(m,n) {
			  				conHtml+='<span class="f11 color999">'+n.cost_type+'：¥ '+n.cost+'</span>';	
			  			});
	    				conHtml+='</p>';
	    				conHtml+='</div>';
	    				conHtml+='<p class="dis_bet mt1-5 mb2 ml">';
	    				conHtml+='<span class="f11 color666">最高提成</span><span class="f11 cffa300">可提成佣金部分 '+data.message.contract_info.max_commission+'</span>';
	    				conHtml+='</p>';
	    				conHtml+='<div class="dis_bet mb2">';
			  			conHtml+='<span class="f11 color666">合同/文件</span>';
			  			conHtml+='<p class="textEnd">';
			  			conHtml+='<span class="f11 c2873ff pct-2" address="'+data.message.contract_info.address+'">《品牌加盟付款协议》</span>';
			  			conHtml+='</p>';
			  			conHtml+='</div>';
	    				conHtml+='<p class="f10 color999 lh1-5">* 如款项存在修改幅度，请联系商务对其进行修改。</p>';
						conHtml+='<p class="f10 color999 lh1-5">* 加盟合同将安排线下签约，实际成交按款项交齐为准。</p>';
						conHtml+='<p class="f10 color999 lh1-5">* 佣金结算以可提成佣金部分乘以提成比例进行计算。</p>';
						conHtml+='<p class="f10 color999 lh1-5">*  对加盟方案存在疑问，请联系商圈客服人员。</p>';
						conHtml+='<p class="f10 color999 lh1-5">*  无界商圈保持最终解释权。</p>';
			  			conHtml+='</div>';
			  			conHtml+='</div>';
	    				conHtml+='</div>';
	    				//底部-生成加盟函按钮
	    				conHtml+='<div class="setup pt05 pb05 fixed-bottom-iphoneX f15 color-f" uid="'+data.message.customer_info.uid+'" brand_name="'+data.message.brand_info.name+'" real_name="'+data.message.customer_info.nickname+'">与投资人聊聊</div>';
	    			}
    				$('.containerBox').html(conHtml);
		    		var contractName = $('.choosePlan').attr('contractName');
					var league_type = $('.choosePlan').attr('league_type');
					var total_cost = $('.choosePlan').attr('total_cost');
					var contract_ids = $('.choosePlan').attr('contract_ids');
					var nickname = $('.setup').attr('real_name');
		    		console.log(contractName,league_type,total_cost,nickname);
		    		console.log(window.screen.height);
		    		clientChoose(uid,0,contract_ids,contractName,league_type,total_cost,nickname);
		    		iphonexBotton('.setup');
//		    		if(isiOS){
//		    			if (window.screen.height === 812) {
//						    $('.setup').css('bottom', '17px');
//						  }
//		    		}
	    		}else {
	    				tips(data.message);
	    			};
	    		
    		});
    		
    	};
		getdetail(agent_id,brand_id,contract_id,uid);
//跳转聊天 id:被查看的人的id  name：被查看人的名字  uType:c代表客户-投资人
function goChat(uType,id, name) {
	if(isAndroid) {
		javascript: myObject.goChat(uType,id,name);
	}
	else if(isiOS) {
		
		var message = {
			method:'goChat',
			params:{
				"uType":uType,
				"id": id,
				"name":name
			}
			
		}
		window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
	}
};
//跳转合同文本
$(document).on('click','.pct-2',function(){
	var address = $(this).attr('address');
	window.location.href = labUser.path +'js/agent/generic/web/viewer.html?file='+address;
});

$(document).on('click','.setup,.chat',function(){
	var nickname =  $(this).attr('real_name');
	console.log(nickname);
	goChat('c',uid,nickname);
});
//投资人id  类型		合同id	   加盟方案	加盟类型		总费用	投资人名字
function clientChoose(id,type,contract_id,contractName,league_type,total_cost,nickname) {
	if(isAndroid) {
 		javascript: myObject.clientChoose(id,type,contract_id,contractName,league_type,total_cost,nickname);
	}
	else if(isiOS) {
		var message = {
			method : 'clientChoose',
			params : {
				"id": id,
				"type":type,
				"contract_id":contract_id,
				"contractName":contractName,
				"league_type":league_type,
				"total_cost":total_cost,
				"nickname":nickname
			}	
		}
	window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
	}
};
