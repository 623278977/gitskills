//zhangxm
	new FastClick(document.body);
		var args=getQueryStringArgs(),
            agent_id = args['agent_id'] || '0',   //被查看的经纪人id   
            brand_id = args['brand_id'] || 0,
            uid = args['uid'] || 0,
            urlPath = window.location.href,
		    shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
		function getdetail(agent_id,brand_id){
			var param = {};
				param['agent_id']=agent_id;
				param['brand_id']=brand_id;
			var url = labUser.agent_path + '/customer/contract-step2/_v010300';
			ajaxRequest(param,url,function(data){
				var conHtml='';
				  if(data.status){
				  		if(data.message!=''){
				  			conHtml+='<div class="top dis_bet bgwhite">';
				  			conHtml+='<div class="mu">';
				  			conHtml+='<img src="/images/agent/mub_blue.png" class="mb05"/>';
				  			conHtml+='<span class="f13 c2873ff">选择目标品牌</span>';
				  			conHtml+='</div>';
				  			conHtml+='<span class="ifline w4 mt2-5 c2873ff"></span>';
				  			conHtml+='<div class="fangan">';
				  			conHtml+='<img src="/images/agent/fangan_blue.png" class="mb05"/>';
				  			conHtml+='<span class="f13 c2873ff">选择加盟方案</span>';
				  			conHtml+='</div>';
				  			conHtml+='<span class="fline w4 mt2-5"></span>';
				  			conHtml+='<div class="">';
				  			conHtml+='<img src="/images/agent/send_grey.png" class="mb05"/>';
				  			conHtml+='<span class="f13 color_ccc">发送至投资人</span>';
				  			conHtml+='</div>';
				  			conHtml+='</div>';
				  			//目标品牌
				  			conHtml+='<div class="choose mt1-2 bgwhite">';
				  			conHtml+='<p class="pt1-5 pb1-5 fline"><span class="f15 color333">目标品牌</span></p>';
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
				  			conHtml+='<div class="choosePlan mt1-2 bgwhite">';
				  			conHtml+='<p class="pt1-5 pb1-5 fline"><span class="f15 color333">选择加盟方案</span></p>';
				  			if(data.message.contract_info.length>0){
				  				$.each(data.message.contract_info, function(i,v) {
				  					conHtml+='<div class="plan fline">';
						  			conHtml+='<div class="packageType" contract_id="'+v.id+'" brand_id="'+data.message.brand_info.id+'" contract_mes=\''+JSON.stringify(v)+'\'>';
						  			conHtml+='<p class="chooseNo mr1"></p>';
						  			conHtml+='<div class="planText mb1-5">';
						  			conHtml+='<p class="lh2-3"><span class="f12 color333">加盟方案</span><span class="f12 color333">'+v.name+'</span></p>';
						  			conHtml+='<p class="lh2-3"><span class="f12 color333">加盟类型</span><span class="f12 color333">'+v.league_type+'</span></p>';
						  			conHtml+='<p class="lh2-3"><span class="f12 color333">总费用</span><span class="f12 cfd4d4d">¥'+v.total_cost+'</span></p>';
						  			conHtml+='</div>';
						  			conHtml+='</div>';
						  			conHtml+='<div class="unfold">';
						  			conHtml+='<span class="c2873ff f12">展开查看详细</span>';
						  			conHtml+='</div>';
						  			conHtml+='<div class="planDetail  bgf2f2 ml2-5 none">';
						  			conHtml+='<div class="costDetail">';
						  			conHtml+='<p class="f11 color666">费用明细</p>';
						  			conHtml+='<p class="">';
						  			$.each(v.cost_details, function(m,n) {
						  				conHtml+='<span class="f11 color999">'+n.cost_type+'：¥ '+n.cost+'</span>';	
						  			});
						  			conHtml+='</p>';
						  			conHtml+='</div>';
						  			conHtml+='<p class="dis_bet mt1-5 mb2 ml">';
						  			conHtml+='<span class="f11 color666">最高提成</span><span class="f11 cffa300">可提成佣金部分 '+v.max_commission+'</span>';
						  			conHtml+='</p>';
						  			conHtml+='<div class="dis_bet mb2">';
						  			conHtml+='<span class="f11 color666">合同/文件</span>';
						  			conHtml+='<p class="textEnd">';
									conHtml+='<span class="f11 c2873ff pct-2" address="'+v.address+'">《品牌加盟付款协议》</span>';
						  			conHtml+='</p>';
						  			conHtml+='</div>';
						  			conHtml+='<p class="f10 color999 lh1-5">* 如款项存在修改幅度，请联系商务对其进行修改。</p>';
									conHtml+='<p class="f10 color999 lh1-5">* 加盟合同将安排线下签约，实际成交按款项交齐为准。</p>';
									conHtml+='<p class="f10 color999 lh1-5">* 佣金结算以可提成佣金部分乘以提成比例进行计算。</p>';
									conHtml+='<p class="f10 color999 lh1-5">*  对加盟方案存在疑问，请联系商圈客服人员。</p>';
									conHtml+='<p class="f10 color999 lh1-5">*  无界商圈保持最终解释权。</p>';
						  			conHtml+='</div>';
						  			conHtml+='</div>';
						  			
				  				});
				  			};
				  			conHtml+='</div>';
				  			conHtml+='<div class="mt1-5 foot">';
				  			conHtml+='<p class="f11 color999">没有合适的加盟方案？</p>';
				  			conHtml+='<p class="f11 color999 mb1">不要着急，联系商务客服代表，为你快速解决！</p>';
				  			conHtml+='<p class=""><a href="tel:'+data.message.tel+'" class="f11 c2873ff">电话商务代表 ></a></p>';
				  			conHtml+='</div>';
				  		};
				  }else {
              conHtml+='<div class="mt1-5 foot">';
              conHtml+='<p class="f11 color999">没有合适的加盟方案？</p>';
              conHtml+='<p class="f11 color999 mb1">不要着急，联系商务客服代表，为你快速解决！</p>';
              conHtml+='<p class=" "><a href="tel:'+data.message.tel+'" class="c2873ff f11">电话商务代表 ></a></p>';
              conHtml+='</div>';
          };
				  $('.containerBox').html(conHtml);
			})
		};
		getdetail(agent_id,brand_id);
    //展开查看详情
    $(document).on('click','.unfold',function(){
    	$(this).addClass('none');
    	$(this).siblings('.planDetail').removeClass('none');
    });
    //选择加盟方案
    $(document).on('click','.packageType',function(){
    	$(this).children('.chooseNo').addClass('choose_img');
    	$(this).parent('.plan').siblings().find('.chooseNo').removeClass('choose_img');
    	var contractId = $(this).attr('contract_id');
    	var brandId = $(this).attr('brand_id');
    	var contract_mes = $(this).attr('contract_mes');
		$('.choose_img').attr('contract_id',contractId);
		$('.choose_img').attr('brand_id',brandId);
		$('.choose_img').attr('contract_mes',contract_mes);
    });
//跳转合同文本
$(document).on('click','.pct-2',function(){
	var address = $(this).attr('address');
	window.location.href = labUser.path +'js/agent/generic/web/viewer.html?file='+address;
});	
	
//	function configContract(contract_mes){
//  if (isAndroid) {
//        javascript:myObject.configContract(contract_mes);
//    } else if (isiOS) {
//        var message = {
//        method : 'configContract',
//        params : {
//          'contract_mes':contract_mes
//        }
//    }; 
//        window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
//    }
//};

