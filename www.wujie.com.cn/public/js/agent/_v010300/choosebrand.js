//zhangxm

Zepto(function(){
	new FastClick(document.body);
		var args=getQueryStringArgs(),
            agent_id = args['agent_id'] || '0',   //被查看的经纪人id   
            code = args['code'] || 0,
            urlPath = window.location.href,
		    shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
		var brandId = '';    
		    function getdetail(agent_id){
		    	var param = {};
		    	param['agent_id'] = agent_id;
		    	var url = labUser.agent_path + '/customer/contract-step1/_v010300';
		    	ajaxRequest(param,url,function(data){
		    		var conHtml = '';
		    		if(data.status){
		    			if(data.message!=''){
		    				//头部
		    				conHtml+='<div class="top dis_bet bgwhite">';
		    				conHtml+='<div class="mu">';
		    				conHtml+='<img src="/images/agent/mub_blue.png" class="mb05"/>';
		    				conHtml+='<span class="f13 c2873ff">选择目标品牌</span>';
		    				conHtml+='</div>';
		    				conHtml+='<span class="fline w4 mt2-5"></span>';
		    				conHtml+='<div class="fangan">';
		    				conHtml+='<img src="/images/agent/fangan_grey.png" class="mb05"/>';
		    				conHtml+='<span class="f13 color_ccc">选择加盟方案</span>';
		    				conHtml+='</div>';
		    				conHtml+='<span class="fline w4 mt2-5"></span>';
		    				conHtml+='<div class="">';
		    				conHtml+='<img src="/images/agent/send_grey.png" class="mb05"/>';
		    				conHtml+='<span class="f13 color_ccc">发送至投资人</span>';
		    				conHtml+='</div>';
		    				conHtml+='</div>';
		    				//选择品牌
		    				conHtml+='<div class="choose mt1-2 bgwhite">';
		    				conHtml+='<p class="pt1-5 pb1-5 fline"><span class="f15 color333">选择品牌</span><span class="f12 color999">目前您代理 <em class="brand_num f12 color999">'+data.message.count+'</em> 个品牌</span></p>';
		    				conHtml+='<div class="brand_list">';
		    				if(data.message.data.length>0){
		    					$.each(data.message.data, function(i,v) {
		    						conHtml+='<div class="chooseBrand fline" brand_id="'+v.brand_id+'">';
				    				conHtml+='<div class="">';
				    				conHtml+='<span class="chooseNo"></span>';
				    				conHtml+='<div class="brand pl1">';
				    				conHtml+='<p class="brand_logo mr1"><img src="'+v.logo+'"/></p>';
				    				conHtml+='<div class="">';
				    				conHtml+='<p class="f14 color333 brand_name">'+v.name+'</p>';
				    				conHtml+='<p class="f11 color999 mb1-2 brand_text">'+v.slogan+'</p>';
				    				conHtml+='<p style="width: 12rem;"><span class="f12 color666">行业分类：</span><span class="f12 color333">'+v.category+'</span></p>';
				    				conHtml+='</div>';
				    				conHtml+='</div>';
				    				conHtml+='</div>';
				    				conHtml+='<p class="f11 color999"><span class="">支持：</span><span class="support">'+v.agency_way+'</span></p>';
				    				conHtml+='</div>';
		    					});
		    				};
		    				conHtml+='</div>';
		    				conHtml+='</div>';
		    				conHtml+='<div class="mt1-5 foot">';
		    				conHtml+='<p class="f11 color999">没有代理投资人想要加盟的品牌？</p>';
		    				conHtml+='<p class="f11 color999 mb1">赶紧申请代理，好机会不要错过！</p>';
		    				conHtml+='<p class="f11 c2873ff brand_lists">点击前往品牌列表 ></p>';
		    				conHtml+='</div>';
		    			}
		    		}else {
		    			tips(data.message);
		    		};
		    		$('.containerBox').html(conHtml);
		    	});
		    };
		    getdetail(agent_id);
    //选择品牌 
    
	$(document).on('click','.chooseBrand',function(){
		$(this).find('.chooseNo').addClass('choose_img');
		$(this).siblings().find('.chooseNo').removeClass('choose_img');
		var brandId = $(this).attr('brand_id');
		$('.choose_img').attr('brand_id',brandId);
	});
	//跳转品牌列表
	function brand_list(agent_id) {
		if(isAndroid) {
			javascript: myObject.brand_list();
		} 
		else if(isiOS) {
			var data = {
				'agent_id':agent_id
			}
			window.webkit.messageHandlers.brand_list.postMessage(data);
		}
	};
	$(document).on('click','.brand_lists',function(){
		brand_list(agent_id); 
	});

		
});