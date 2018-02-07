//zhangxm
Zepto(function(){
	

	new FastClick(document.body);
	var args=getQueryStringArgs(),
	    agent_id = args['agent_id'] || '0',   //经纪人id
	    card_id = args['card_id'],   //福卡id
	    urlPath = window.location.href,
	    shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
	function getDetail(agent_id,card_id){
		var params = {};
			params['agent_id'] = agent_id;
			params['card_id'] = card_id;
		var url = labUser.agent_path + '/agent-redpacket/f-card-log/_v010400';
		ajaxRequest(params,url,function(data){
			if(data.status){
				var listHtml = '';
				$('#container').removeClass('none');
				if(data.message){
					$('.sendFriend').attr('agent_get_red_id',data.message.agent_get_red_id);
					$('.sendFriend').attr('cardName',data.message.card_name);
					$('.blag').attr('cardName',data.message.card_name);
					if(data.message.can_use_num==0){   // 没有该福卡
						$('.fukaliang').addClass('none');
						$('.fukagrey').removeClass('none');
						//对应福卡展示
						if(data.message.card_name == '无'){
							$('.wu').removeClass('none').siblings().addClass('none');
						}else if (data.message.card_name == '界') {
							$('.jie').removeClass('none').siblings().addClass('none');
						}else if (data.message.card_name == '商') {
							$('.shang').removeClass('none').siblings().addClass('none');
						}else if (data.message.card_name == '圈') {
							$('.quan').removeClass('none').siblings().addClass('none');
						}else if (data.message.card_name == '福') {
							$('.fu').removeClass('none').siblings().addClass('none');
						}
						
					}else {								//有该福卡
						$('.fukagrey').addClass('none');
						$('.fukaliang').removeClass('none');
						//对应福卡展示
						if(data.message.card_name == '无'){
							$('.wu').removeClass('none').siblings().addClass('none');
						}else if (data.message.card_name == '界') {
							$('.jie').removeClass('none').siblings().addClass('none');
						}else if (data.message.card_name == '商') {
							$('.shang').removeClass('none').siblings().addClass('none');
						}else if (data.message.card_name == '圈') {
							$('.quan').removeClass('none').siblings().addClass('none');
						}else if (data.message.card_name == '福') {
							$('.fu').removeClass('none').siblings().addClass('none');
						}
						
						listHtml+='<ul>';
						$.each(data.message.log, function(i,v) {
							var time ='';
								time = unix_YMD(v.time);
							if(v.type==1){
								listHtml+='<li class="cfff"><span class="f14">“'+v.person_name+'”抽奖获得福卡一张（'+v.card_name+'）</span><span class="f12">'+time+' </span></li>';
							}else if (v.type==3) {
								listHtml+='<li class="cfff"><span class="f14">赠送给经纪人'+v.card_name+'一张福卡（'+v.card_name+'）</span><span class="f12">'+time+'</span></li>';
							}else if(v.type==2){
								listHtml+='<li class="cfff"><span class="f14">'+v.person_name+'经纪人赠送一张福卡</span><span class="f12">'+time+'</span></li>';
							}
							
						});
						listHtml+='</ul>';
					};
					
					
				}
			};
			$('.scroll-box').html(listHtml);
			awards();
		});
	};
	getDetail(agent_id,card_id);
//获奖情况
function awards() {
	//获得当前<ul>
	var $uList = $(".scroll-box ul");
	var timer = null;
	
	//滚动动画
	function scrollList(obj) {
		//获得当前<li>的高度
		var scrollHeight = $(".scroll-box ul li:first").height();
		//滚动出一个<li>的高度
		$uList.stop().animate({
			marginTop: -scrollHeight
		}, 1000, function() {
			//动画结束后，将当前<ul>marginTop置为初始值0状态，再将第一个<li>拼接到末尾。
			$uList.css({
				marginTop: 0
			}).find("li:first").appendTo($uList);
		});
	};
	//计时
	timer = setInterval(function() {
			scrollList($uList);
		}, 1200);

};	

//分享
$(document).on('click','.shareFuka',function(){
	showShare();
});


//送福卡给小伙伴       	agent_get_red_id:经纪人领取红包表对应的ID   card_id:福卡id
//function sendFriend(card_id,agent_get_red_id){
//  if (isAndroid) {
//        javascript:myObject.sendFriend(card_id,agent_get_red_id);
//    } else if (isiOS) {
//        var message = {
//        method : 'sendFriend',
//        params : {
//          'card_id':card_id,
//          'agent_get_red_id':agent_get_red_id
//        }
//    }; 
//        window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
//    }
//};


//赠送福卡
$(document).on('click','.sendFriend',function(){
//	var agent_get_red_id = $(this).attr('agent_get_red_id');
//	sendFriend(card_id,agent_get_red_id);
	var cardName = $(this).attr('cardName');
	window.location.href = labUser.path + 'webapp/agent/addresslook/detail/_v010300?agent_id='+agent_id+'&card_id='+card_id+'&sendflag=1&cardName='+cardName;

});
//索要福卡
$(document).on('click','.blag',function(){
//	var agent_get_red_id = $(this).attr('agent_get_red_id');
	var cardName = $(this).attr('cardName');
	window.location.href = labUser.path + 'webapp/agent/addresslook/detail/_v010300?agent_id='+agent_id+'&card_id='+card_id+'&getflag=1&cardName='+cardName;;

});


});

