Zepto(function(){
		new FastClick(document.body);
		$('body').css('background-color','#f2f2f2');
		var args=getQueryStringArgs(),
            id = args['contract_id'] || '0',  //合同id
//          uid = args['agent_id'] || '0',
			urlPath = window.location.href,
            origin_mark = args['share_mark'] || 0,//分销参数，分享页用
            code = args['code'] || 0;
		var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
		var is_out = urlPath.indexOf('is_out') > 0 ? true : false;
		function getdetail(id){
			var param = {};
			param['contract_id'] = id;
			if(shareFlag){
                param['guess']=1;
            }
			var url = labUser.agent_path + '/contract/detail/_v010000';
			ajaxRequest(param,url,function(data){
				var conHtml = '';
				if(data.status){
					if(data.message){
						$.each(data.message.conreact, function(i,v) {
							if(v.status==-1){
								conHtml+='<div class="head cfc5d5d down-pay ">';
								conHtml+='<div class="head_dis"><img src="/images/agent/ico_delete.png" class="mr1"/>';
								if(v.realname==''){
									conHtml+='<span class="f15 ">投资人：'+v.nickname+'已拒绝'+v.brand+'付款协议</span></div>';
								}else {
									conHtml+='<span class="f15 ">投资人：'+v.realname+'已拒绝'+v.brand+'付款协议</span></div>';
								};
								conHtml+='<img class="head-imgr " src="/images/agent/ico_pact2.png">';
								conHtml+= '</div>';
							}else if(v.status==1 || v.status==2){
								conHtml+= '<div class="head c57c88d down-pay ">';
								conHtml+='<div class="head_dis"><img src="/images/agent/icon-true2.png" class="mr1"/>';
								if(v.realname==''){
									conHtml+='<span class="f15 ">投资人：'+v.nickname+'已签订'+v.brand+'付款协议</span></div>';
								}else {
									conHtml+='<span class="f15 ">投资人：'+v.realname+'已签订'+v.brand+'付款协议</span></div>';
								};
								conHtml+='<img class="head-imgr " src="/images/agent/ico_pact2.png">';
								conHtml+= '</div>';
							};
							conHtml+='<div class="pub-state bgwhite mt4-5 ml1-5 mr1-5"><p class="bold f15 b color333 mb1-5"><span class="">To 无界商圈投资人&nbsp;</span>';
							if(v.realname==''){
								conHtml+='<span class="cffa300">'+v.nickname+'</span>';
							}else {
								conHtml+='<span class="cffa300">'+v.realname+'</span>';
							}

							if(v.gender==0){
								conHtml+='<span class="">&nbsp;女士:</span>';
							}else if(v.gender==1){
								conHtml+='<span class="">&nbsp;先生:</span>';
							}else {
								conHtml+='<span class="">:</span>';
							};
							conHtml+='</p>';
							conHtml+='<p class="pub-1  f13 color666"><span class="">通过经纪人</span><span class="cffa300 f12 "> ('+v.agent_name+') </span>';
							conHtml+='<span class="">的对接，您是否对品牌已经有了加盟的想法？</span><br />';
							conHtml+='<span class="f13">在这里，向您发出品牌加盟的橄榄枝，我们提供全网最优质的服务和最低的加盟费用，并为您提供相应的加盟扶持。</span></p>';
							conHtml+='<span class="logo-img"><img src="/images/agent/logopact_10.png"/></span></div>';
							conHtml+='<div class="pact bgwhite mt1-2"><div class=" ml08 pl2 pr1-5 pt05">';
							//公共信息
							conHtml+='<div class="act-2 "><div class="inst-2l "><p class="inst-2lp mb05"><span class="f12 color666">付款协议</span><span class="f12 color666">'+v.contract_title+'</span></p>';
							if(v.status==1 || v.status==2){
								conHtml+='<p class="inst-2lp mb05"><span class="f12 color666">编号</span><span class="f12 color666">'+v.contract_no+'</span></p>';
							};

							conHtml+='<p class="inst-2lp mb05"><span class="f12 color666">加盟品牌</span><span class="f12 color666">'+v.brand+'</span></p>';
//							conHtml+='<p class="inst-2lp mb05 text-end"><span class="f12 color666">撰写</span><span class="f12 color666">无界商圈法务代表<br />'+v.brand+'法务代表</span></p>';
							conHtml += '</div></div>';
							//拒绝
							if(v.status==-1){
								conHtml+='<div><p class="inst-2lp mb05"><span class="f12 color333">加盟总费用</span><span class="f12 color666">¥&nbsp;'+v.amount+'</span></p>';
								conHtml+='<p class="inst-2lp mb05"><span class="f12 color333">协议文本</span></p>';
								conHtml+='<div class="pct-2 mb1" pact_id='+v.id+' address="'+v.address+'"><div class="act-2l pact-text">';
								conHtml+='<img src="/images/agent/my_contract.png" class="pact-img mr1"/>';
								conHtml+='<p class="pact-2lp over-text"><span class="over-text f14 b bold act-2lspan color333">'+v.brand+'加盟合同</span><br />';
//								conHtml+='<span class="over-text f11 act-2lspan color333">合同编号：'+v.contract_no+'</span><br />';
								conHtml+='</p></div>';
								conHtml+='<img src="/images/jump.png" class="pct-jump"/>';
								conHtml+='</div>';
      							conHtml+='<div class="fline"></div>';
      							conHtml+='</div></div></div>';

								conHtml+='<div class="pay-off text-end pr1-5 pt1 f13 mb3">';
								conHtml+='<p  class="mb05"><span class="color333 b bold">邀请人</span></p>';
								conHtml+='<p  class="mb05"><span class="color333 b bold">跟单经纪人：</span><span class="color333 b bold">'+v.agent_name+'</span></p>';
								conHtml+='<p  class="mb05"><span class="color999">邀请时间：</span><span class="color999">'+unix_to_fulltime_s(v.created_at)+'</span></p>';
								conHtml+='<p  class="mb05"><span class="color999">拒绝时间：</span><span class="color999">'+unix_to_fulltime_s(v.confirm_time)+'</span></p>';
								conHtml+='<p  class="mb05"><span class="color999">拒绝理由：</span><span class="color999">'+v.remark+'</span></p></div>';

							};
							//接受
							if(v.status==1 || v.status==2){
									//首付情况
//									conHtml+='<div class=""><div class="inst-2l "><p class="down-pay mb05"><span class="fline wid"></span><span class="f12 color666">首付情况</span><span class="fline wid"></span></p>';
//									conHtml+='<p class="inst-2lp mb05"><span class="f12 color666">首次支付</span><span class="f12 color666">¥&nbsp;'+v.pre_pay+'</span></p>';
//									conHtml+='<p class="inst-2lp mb05"><span class="f12 color666">定金抵扣</span><span class="f12 color666">-&nbsp;¥&nbsp;'+v.invitation+'</span></p>';
//									conHtml+='<p class="inst-2lp mb05"><span class="f12 color666">创业基金抵扣</span><span class="f12 color666">-&nbsp;¥&nbsp;'+v.fund+'</span></p>';
//									conHtml+='<p class="inst-2lp mb05"><span class="f12 color666">实际支付</span><span class="f12 color666">¥&nbsp;'+v.first_amount+'</span></p>';
//									conHtml+='<p class="inst-2lp mb05"><span class="f12 color666">支付状态</span><span class="f12 color666">'+v.first_pay_status+'</span></p>';
//									conHtml+='<p class="inst-2lp mb05 text-end"><span class="f12 color666">支付方式</span><span class="f12 color666 ">'+v.pay_way+'<br />'+v.buyer_id+'</span></p>';
//									conHtml+='<p class="inst-2lp mb05"><span class="f12 color666">支付时间</span><span class="f12 color666">'+unix_to_fulltime_s(v.pay_at)+'</span></div></div>';
									//尾款情况
									conHtml+='<div class="act-2"><div class="inst-2l ">';
//									conHtml+='<p class="down-pay mb05"><span class="fline wid"></span><span class="f12 color666">尾款情况</span><span class="fline wid"></span></p>';
//									conHtml+='<p class="inst-2lp mb05"><span class="f12 color666">尾款补齐</span><span class="f12 color666">¥&nbsp;'+v.tail_pay+'</span></p>';
//									if(v.status==1){
//										conHtml+='<div class="inst-2lp mb05 text-end"><span class="f12 color666">支付状态</span><p class="f12 ">';
//										conHtml+='<span class="cfd4d4d mb05">未支付</span><br />';
//										conHtml+='<span class=" mb05 color666">* 请提醒投资人尽快支付尾款费用</span><br /><span class=" mb05 color666">支付方式为线下对公账号转账</span><br /><span class="c2873ff mb05 wk_payment">了解尾款补齐操作办法</span><br /></p></div>';
//									}else if(v.status==2){
//										conHtml+='<div class=" mb05 text-end"><p class="inst-2lp mb05"><span class="f12 color666">支付状态</span><span class="f12 c59c78a">'+v.tail_pay_status+'</span></p>';
//										conHtml+='<div class="inst-2lp mb05 text-end"><span class="f12 color666">支付方式</span><p><span class="f12 color666 mb05">银行卡转账</span><br/>';
//										conHtml+='<span class="f12 color666">'+v.bank_no+'&nbsp;('+v.bank_name+')</span></p></div>';
//										conHtml+='<p class="inst-2lp mb05"><span class="f12 color666">到账时间</span><span class="f12 color666">'+unix_to_fulltime_s(v.tail_pay_at)+'</span></p>';
//										conHtml+='<p class="inst-2lp mb05"><span class="f12 color666">财务确认人</span><span class="f12 color666">'+v.auditor+'</span></p>';
//										conHtml+='</div>';
//									};
									conHtml+='<p class="inst-2lp mb05"><span class="f12 color666">协议文本</span></p>';
									conHtml+='<div class="pct-2 mb1" pact_id='+v.id+' address="'+v.address+'">';
									conHtml+='<div class="act-2l pact-text"><img src="/images/agent/my_contract.png" class="pact-img mr1"/><p class="pact-2lp over-text">';
									conHtml+='<span class="over-text f14 b bold act-2lspan color333">'+v.brand+'加盟电子合同'+'</span><br />';

//										conHtml+='<span class="over-text f11 act-2lspan color333">合同编号：'+v.contract_no+'</span><br />';

									conHtml+='</p></div>';
									conHtml+='<img src="/images/jump.png" class="pct-jump"/></div>';
									conHtml+='<div class="fline"></div>';
									conHtml+='<p class="inst-2lp pt1 pb1"><span class="f12 color666">确定时间</span><span class="f12 color666">'+unix_to_fulltime_s(v.confirm_time)+'</span></p>';
									conHtml+='</div></div></div></div>';

									conHtml+='<div class="accept text-end pr1-5 pt1 f13 mb3">';
									conHtml+='<p class="mb05"><span class="color333 b bold">邀请人</span></p>';
									conHtml+='<p class="mb05"><span class="color333 b bold">跟单经纪人：</span><span class="color333 b bold">'+v.agent_name+'</span></p>';
									conHtml+='<p  class="mb05"><span class="color999">邀请时间：</span><span class="color999">'+unix_to_fulltime_s(v.created_at)+'</span></p>';
									conHtml+='<p  class="mb05"><span class="color999">确定时间：</span><span class="color999">'+unix_to_fulltime_s(v.confirm_time)+'</span></p></div>';
									if(!is_out){
										conHtml+='<div class="pd-btn bgwhite f15">';
										conHtml+='<span class="money push_money" agent_id="'+v.agent_id+'" contract_id="'+v.id+'" status="'+v.status+'">查看我的提成</span></div>';
									};

							};

							//待确认
							if(v.status==0){
								conHtml+='<div><p class="inst-2lp mb05"><span class="f12 color333">加盟总费用</span><span class="f12 color666">¥&nbsp;'+v.amount+'</span></p>';
								conHtml+='<p class="inst-2lp mb05"><span class="f12 color333">线上首付</span><span class="f12 color666">¥&nbsp;'+v.pre_pay+'</span></p>';
								conHtml+='<p class="inst-2lp mb05"><span class="f12 color333">线下尾款</span><span class="f12 color666">¥&nbsp;'+v.tail_pay+'</span></p>';
								conHtml+='<div class="inst-2lp mb05 text-end"><span class="f12 color333">支付状态</span> ';
								conHtml+='<p class="f12 ">';
	  							conHtml+='<span class=" mb05 color666">* 请提醒投资人尽快支付尾款费用</span><br />';
	  							conHtml+='<span class=" mb05 color666">支付方式为线下对公账号转账</span><br />';
	  							conHtml+='<span class="c2873ff mb05 wk_payment">了解尾款补齐操作办法</span><br />';
	  							conHtml+='</p></div>';
	  							conHtml+='<p class="inst-2lp mb05"><span class="f12 color333">协议文本</span></p>';
	  							conHtml+='<div class="pct-2 mb1" pact_id='+v.id+' address="'+v.address+'">';
	  							conHtml+='<div class="act-2l pact-text">';
      							conHtml+='<img src="/images/agent/my_contract.png" class="pact-img mr1"/>';
      							conHtml+='<p class="pact-2lp over-text">';
      							conHtml+='<span class="over-text f14 b bold act-2lspan color333">'+v.brand+'加盟合同</span><br />';
//    							conHtml+='<span class="over-text f11 act-2lspan color333">合同编号：'+v.contract_no+'</span><br />';
      							conHtml+='</p></div>';
      							conHtml+='<img src="/images/jump.png" class="pct-jump"/>';
      							conHtml+='</div>';
      							conHtml+='<div class="fline"></div>';
      							conHtml+='</div></div></div>';

      							conHtml+='<div class="accept text-end pr1-5 pt1 f13">';
								conHtml+='<p class="mb05"><span class="color333 b bold">邀请人</span></p>';
								conHtml+='<p class="mb05"><span class="color333 b bold">跟单经纪人：</span><span class="color333 b bold">'+v.agent_name+'</span></p>';
								conHtml+='<p  class="mb05"><span class="color999">邀请时间：</span><span class="color999">'+unix_to_fulltime_s(v.created_at)+'</span></p></div>';
								conHtml+='<div class="pd-btn bgwhite f15 fixed-bottom-iphoneX"><span class="cffa300">状态：待确认</span>';
								conHtml+='<span class="money send-again" contract_id="'+v.id+'" realname="'+v.realname+'" brand="'+v.brand+'" uid="'+v.uid+'" contract_title="'+v.contract_title+'" contract_joinType="'+v.league_type+'" amount="'+v.amount+'">再次发送</span></div>';

							};
							
							if(v.status==0){
								$('.pub-state').css('margin-top','1.5rem');
							}
						});

					};
					$('.containerBox').html(conHtml);
				}else{
					if(data.message.type=='contract_close'){
						$('.define').removeClass('none');
					};
					if(data.message.type=='brand_down'){
						$('.brand_down').removeClass('none');
					}
				}
				
			})
		};
		getdetail(id);
		
		//跳转合同文本
		$(document).on('click','.pct-2',function(){
			var address = $(this).attr('address');
			window.location.href = labUser.path +'js/agent/generic/web/viewer.html?file='+address;
		});
		//尾款补齐操作办法 
		$(document).on('click','.wk_payment',function(){
			window.location.href = labUser.path +'webapp/agent/way/detail';
		});
		
		//已确认-查看付款情况
		$(document).on('click','.to-pay',function(){
			var contract_id = $(this).attr('contract_id');
			var agent_id = $(this).attr('agent_id');
			var customer_id = $(this).attr('customer_id');
			window.location.href = labUser.path + 'webapp/agent/brand/payment?id='+contract_id+'&agent_id='+agent_id+'&customer_id='+customer_id;
		});
		//已确认-查看提成
		$(document).on('click','.push_money',function(){
			var agent_id = $(this).attr('agent_id');
			var contract_id = $(this).attr('contract_id');
			var status = $(this).attr('status');
			if(status==2){
				window.location.href = labUser.path + 'webapp/agent/brand/commission?id='+contract_id+'&agent_id='+agent_id;
			}else {
				tips('客户尾款未交齐，无法查看！');
			}

		});
		//提示语
		function tips(e){
            $('.common_pops').text(e).removeClass('none');
            setTimeout(function() {
                $('.common_pops').addClass('none');
            }, 1500);
        };
		//待确认-再次发送
		function sendRichMsg(type,uType,uid,id,title,imgUrl,date,store,contract_joinType,amount){
			if(isAndroid) {
			javascript: myObject.sendRichMsg(type,uType,uid,id,title,imgUrl,date,store,contract_joinType,amount);
			}
			else if(isiOS) {
				var data = {
					"type": type,
					"uType":uType,
					"uid":uid,
					"id":id,
					"title":title,
					"imgUrl":imgUrl,
					"date":date,
					"store":store,
					"contract_joinType":contract_joinType,
					"amount":amount
				};
				window.webkit.messageHandlers.sendRichMsg.postMessage(data);
			}
		};
		$(document).on('click','.send-again',function(){
			var uid = $(this).attr('uid');
			var id = $(this).attr('contract_id');
			var title = $(this).attr('contract_title');
			var contract_joinType = $(this).attr('contract_joinType');
			var amount = $(this).attr('amount');
			sendRichMsg('3','C',uid,id,title,'','','',contract_joinType,amount);
		});
		function unix_to_fulltime_s(unix) {
		      var newDate = new Date();
		      newDate.setTime(unix * 1000);
		      var Y = newDate.getFullYear();
		      var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
		      var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
		      var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
		      var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
		      var s = newDate.getSeconds() < 10 ? '0' + newDate.getSeconds() : newDate.getSeconds();
		      return Y + '/' + M + '/' + D + '日' + ' ' + h + ':' + m + ':' +s;
		};
});