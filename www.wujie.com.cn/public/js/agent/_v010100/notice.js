//zhangxm
Zepto(function(){
	new FastClick(document.body);
	$('body').css('background:#f2f2f2;');
		var args=getQueryStringArgs(),
            uid = args['agent_id'] || '0',
			urlPath = window.location.href,
            origin_mark = args['share_mark'] || 0,//分销参数，分享页用
            code = args['code'] || 0;
		var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
		
		function getdetail(uid){
			var param={};
            param['agent_id']=uid;
            if(shareFlag){
                param['guess']=1;
            }       
			var	url=labUser.agent_path + '/inspector/message-back/_v010100'; 
			ajaxRequest(param,url,function(data){
				if(data.status){
					var conHtml = '';
					if(data.message.length>0){
						$.each(data.message, function(i,v) {
							conHtml += '<div class="act ">';
							conHtml += '<p class="top"><img src="/images/agent/time_03.png" class="mr1 "/><span class="time c2873ff b f13">'+v.confirm_day+'</span></p>';
							$.each(v.result, function(m,n) {
								if(n.type == 'first_register'){  //首次成为经纪人的通知
									conHtml+='<div class="bord-l ml08">';
									conHtml+='<div class="act-cont bgwhite mb1-2">';
									conHtml+='<div class="act-1 fline f13">';
									conHtml+='<span class="over-text bold f13 b color333">从今天起，你就是无界商圈专业的经纪人！</span>';
									conHtml+='</div>';
									conHtml+='<div class="not-2 ">';
									conHtml+='<div class="act-2l">';
									conHtml+='<div>';
									conHtml+='<p style="display: flex;" class="mb05">';
									conHtml+='<span class="not-area f12 color999">Hi，</span><span class=" f12 color999 nickname">'+n.cont.realname+'</span>';
									conHtml+='</p>';
									conHtml+='<p class="not-area mb05">';
									conHtml+='<span class=" f12 color999">欢迎加入无界商圈，并选择我们作为成长的平台。</span>';
									conHtml+='</p>';
									conHtml+='<p class="not-area mb05">';
									conHtml+='<span class=" f12 color999">无界商圈OVO品牌招商推广平台，隶属于天涯若比邻网络信息服务有限公司，成立于2012年，总部位于杭州。下设四大区域运营中心（杭州、广州、成都、北京）和100多个城市运营中心。是一家互联网+综合商业服务与跨域资源共享平台。 公司运用国际领先的天涯云网真视频会议系统和互联网直播技术，独创OVO（online-video-offline）场景化招商服务模式，解决跨域（时间和空间）信息传递，提供了一套综合化的解决方案，让信息得以高效快速地实现连接、共享、传播，实现优质资源匹配。目前无界商圈服务有：品牌招商服务、培训服务、政府智慧服务、投融资对接服务、海外项目服务、第三方服务 等6大行业服务方向。</span>';
									conHtml+='</p>';
									conHtml+='<p class="not-area mb05">';
									conHtml+='<span class=" f12 color999">成为一名专业的经纪人，成为优质品牌的代理，向无界商圈投资客进行品牌宣传、包装，邀请投资客参加无界商圈OVO活动、品牌实地考察，最终邀请成单。</span>';
									conHtml+='</p>';
									conHtml+='<p class="not-area mb05">';
									conHtml+='<span class=" f12 color999">经纪人将作为中间力量，衔接品牌和投资人，在无界商圈的平台上碰撞出火花。经纪人通过投资人邀请、成单，获得邀请、促单奖励。无界商圈为经纪人提供佣金保障，海量佣金赚不停。</span>';
									conHtml+='</p>';
									conHtml+='<p class="not-area mb05">';
									conHtml+='<span class=" f12 color999 headline text_line" headline_id="'+n.cont.new_id+'">了解更多无界商圈经纪人玩法</span>';
									conHtml+='</p>';
									conHtml+='<p class="not-area mb05">';
									conHtml+='<span class=" f12 color999 brand_list text_line ">尝试代理第一个品牌</span>';
									conHtml+='</p>';
									conHtml+='<p class="not-area mb05">';
									conHtml+='<span class=" f12 color999 letter text_line" agent_id="">邀请好友注册无界商圈投资人</span>';
									conHtml+='</p>';
									conHtml+='</div></div></div></div></div>';
								};
								//代理品牌成功
								if(n.type == 'brand_success'){  
									conHtml+='<div class="bord-l ml08">';
									conHtml+='<div class="act-cont bgwhite mb1-2">';
									conHtml+='<div class="act-1 fline f13">';
									conHtml+='<span class=" bold f13 b color333">成功解锁新技能，成功代理品牌</span>';
									conHtml+='<span class=" bold f13 b color333 brand_name">【'+n.cont.brand_name+'</span>';
									conHtml+='<span class=" bold f13 b color333 brand_slogan">-&nbsp;'+n.cont.brand_slogan+'】</span>';
									conHtml+='</div>';
									conHtml+='<div class="not-2">';
									conHtml+='<div class="act-2l">';
									conHtml+='<div>';
									conHtml+='<p class="mb05 not-area">';
									conHtml+='<span class="not-area f12 color999">您成功的成为了品牌</span>';
									conHtml+='<span class="f12 color999 brand_name">【'+n.cont.brand_name+'</span>';
									conHtml+='<span class="f12 color999 brand_slogan">-&nbsp;'+n.cont.brand_slogan;
									conHtml+='<span class="f12 color999">】的代理经纪人。</span>';
									conHtml+='</p>';
									conHtml+='<p class="not-area mb05">';
									conHtml+='<span class=" f12 color999">成为代理经纪人后，品牌意向投资人将会通过无界商圈系统派单至您。您可以通过投资人的个人描述以及接单意向，最终选择是否对其进行跟单操作。</span>';
									conHtml+='</p>';
									conHtml+='<p class="not-area mb05">';
									conHtml+='<span class=" f12 color999">确认跟单关系后，您将成为该投资人的品牌跟进人，品牌的活动、考察、资讯、报价等均由您进行跟进和管理。</span>';
									conHtml+='</p>';
									conHtml+='<p class="not-area mb05">';
									conHtml+='<span class=" f12 color999">我们希望最终您能邀请投资人在无界商圈平台上加盟品牌，最终为您创造丰厚的佣金提成。</span>';
									conHtml+='</p>';
									conHtml+='<p class="not-area mb05">';
									conHtml+='<span class=" f12 color999">跟单途中如有疑问，请致电无界商圈客服人员进行相关询问。</span>';
									conHtml+='</p>';
									conHtml+='<p class="not-area mb05">';
									conHtml+='<span class=" f12 color999 headline text_line" headline_id="'+n.cont.new_id+'">了解更多无界商圈经纪人玩法</span>';
									conHtml+='</p>';
									conHtml+='</div></div></div></div></div>';
								};
								if(n.type=='my_message'){
									conHtml += '<div class="bord-l ml08"><div class="act-cont bgwhite mb1-2"><div class="act-1 fline f13"><span class="over-text bold f13 b color333">'+n.cont.title+'</span></div>';
									if(n.cont.url!=''){
										conHtml += '<a href="'+n.cont.url+'"><div class="not-2 "><div class="act-2l">';
										if(n.cont.image!=''){
											conHtml += '<img src="'+n.cont.image+'" class="not-2limg mr1"/>';
										};
										conHtml += '<span class="not-area f11 color999">'+n.cont.content+'</span></div>';
										if(n.cont.url != ''){
											conHtml += '<img src="/images/jump.png"/>';
										};
										conHtml += '</div></a></div></div>';
									}else {
										conHtml += '<div class="not-2 "><div class="act-2l">';
										if(n.cont.image!=''){
											conHtml += '<img src="'+n.cont.image+'" class="not-2limg mr1"/>';
										};
										conHtml += '<span class="not-area f11 color999">'+n.cont.content+'</span></div>';
										if(n.cont.url != ''){
											conHtml += '<img src="/images/jump.png"/>';
										};
										conHtml += '</div></div></div>';
									}
									
								};
								//抢单成功
								if(n.type=='customer_info'){
									var take_time = unix_to_yeardate(n.cont.take_time)
									conHtml+='<div class="bord-l ml08">';
									conHtml+='<div class="act-cont bgwhite mb1-2">';
									conHtml+='<div class="act-1 fline f13">';
									conHtml+='<span class="f13 b color333">'+n.cont.title+'</span>';
									conHtml+='</div>';
									conHtml+='<p class="mt1-5 not-area"><span class="f11 color999">意向品牌：</span>';
									conHtml+='<span class="f11 color999 monad_brand">'+n.cont.brand_name+'</span></p>';
									conHtml+='<p class="not-area"><span class="f11 color999">投资人：</span>';
									conHtml+='<span class="f11 color999 monad_name">'+n.cont.user_name+'</span></p>';
									conHtml+='<p class="mb1-5 not-area"><span class="f11 color999">接单时间：</span>';
									conHtml+='<span class="f11 color999 monad_time">'+take_time+'</span></p>';
									conHtml+='<p class="f11 color999 not-area">'+n.cont.describe+'</p>';
					
									conHtml+='<p class="c2873ff f11 mt1-5 investor_detail not-area" customer_id="'+n.cont.user_id+'">投资人详情页>></p>';
									conHtml+='</div></div>';
								};
								//活动报名成功
								if(n.type=='activity_info'){
									var activity_time = unix_to_fulltime_s(n.cont.activity_time)
									conHtml+='<div class="bord-l ml08">';
									conHtml+='<div class="act-cont bgwhite mb1-2">';
									conHtml+='<div class="act-1 fline f13"><span class="f13 b color333">'+n.cont.title+'</span></div>';
									conHtml+='<p class="mt1-5 not-area"><span class="f11 color999">活动时间：</span><span class="f11 color999 activity_time">'+activity_time+'</span></p>';
									conHtml+='<p class="not-area"><span class="f11 color999">参与场地：</span><span class="f11 color999 activity_place">'+n.cont.activity_make+'</span></p>';
									conHtml+='<p class="f11 color999 mt1-5 not-area">'+n.cont.activity_describe+'</p>';
									conHtml+='<p class="c2873ff f11 mt1-5 activity_tickets not-area">点击查看我的门票>></p>';
									conHtml+='</div></div>'; 
								};
								//季度佣金结清
								if(n.type=='currency_info'){
									conHtml+='<div class="bord-l ml08">';
									conHtml+='<div class="act-cont bgwhite mb1-2">';
									conHtml+='<div class="act-1 fline f13"><span class="f13 b color333">'+n.cont.title+'</span></div>';
									conHtml+='<p class="mt1-5"><span class="f11 color999">结算周期：</span><span class="f11 color999 monad_brand">'+n.cont.settle_time+'</span></p>';
									conHtml+='<p class=""><span class="f11 color999">结算范围：</span><span class="f11 color999 monad_name">'+n.cont.settle_scope+'</span></p>';
									conHtml+='<p class="mb1-5"><span class="f11 color999">本季度佣金总额：</span><span class="f11 color999 monad_time">'+n.cont.commission+'元整</span></p>';
									conHtml+='<p class="f11 color999">提现须知，请绑定有效银行卡，200元以上不收取手续费用；200元以下收取10元手续费。</p>';
									conHtml+='<p class="f11 color999">如提现或佣金结算存疑，请联系无界商圈客服人员。</p>';
									conHtml+='<p class="c2873ff f11 mt1-5 my_wallet">点击查看金额详情说明>></p>';
									conHtml+='</div>';
									conHtml+='</div>';
								}
								
							});
							conHtml += '</div>';
						});
					};
					$('#containerBox').html(conHtml);
				};
				
			});
		};
		getdetail(uid);
	//跳转邀请   投资人
	$(document).on('click','.letter',function(){
//		window.location.href = labUser.path +'/webapp/agent/register/detail?agent_id='+uid;
		noti_invite();
	});
	//跳转邀请   投资人
	function noti_invite(){
		if (isAndroid) {
        	javascript:myObject.noti_invite();
	    } 
	    else if (isiOS) {
	        var data = {}
	        window.webkit.messageHandlers.noti_invite.postMessage(data);
	    }
	}
	//跳转品牌列表
	$(document).on('click','.brand_list',function(){
		brand_list();
	});
	//跳转品牌列表
	function brand_list(){
		if (isAndroid) {
        javascript:myObject.brand_list();
	    } 
	    else if (isiOS) {
	        var data = {}
	        window.webkit.messageHandlers.brand_list.postMessage(data);
	    }
	};
	//跳转投资人详情
	$(document).on('click','.investor_detail',function(){
		var customer_id = $(this).attr('customer_id');
		window.location.href = labUser.path + '/webapp/agent/investor/detail/_v010003?customer_id='+customer_id+'&agent_id='+uid;
	});
	//跳转我的钱包
	$(document).on('click','.my_wallet',function(){
		window.location.href = labUser.path+'/webapp/agent/mycharge/detail?agent_id='+uid;
	});
	//跳转我的门票
	function myTickets(){
		if (isAndroid) {
	        javascript:myObject.myTickets();
	    } else if (isiOS) {
	        var message = {
			    method : 'myTickets'
			}; 
	        window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
	    }
	};
	$(document).on('click','.activity_tickets',function(){
		myTickets();
	});
	
	//跳转咨询详情
	$(document).on('click','.headline',function(){
		var headline_id = $(this).attr('headline_id');
		console.log(headline_id);
		window.location.href = labUser.path +'/webapp/agent/headline/detail?agent_id='+uid+'&id='+headline_id;
	});
	
	//时间戳转换
	function proDay(unix) {
	    var newDate = new Date();
	    newDate.setTime(unix * 1000);
	    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
	    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
	    var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
	    var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
	    return M +'/'+ D;
	};
function unix_to_yeardate(unix) {
    var newDate = new Date();
    newDate.setTime(unix * 1000);
    var Y = newDate.getFullYear();
    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
    return Y + '-' + M + '-' + D;
};
function unix_to_fulltime_s(unix) {
      var newDate = new Date();
      newDate.setTime(unix * 1000);
      var Y = newDate.getFullYear();
      var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
      var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
      var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
      var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
      var s = newDate.getSeconds() < 10 ? '0' + newDate.getSeconds() : newDate.getSeconds();
      return Y + '-' + M + '-' + D  + ' ' + h + ':' + m + ':' +s;
};
})
