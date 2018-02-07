//zhangxm
Zepto(function() {
	new FastClick(document.body);
	$('body').css('background','#f2f2f2');
	var args = getQueryStringArgs(),
		uid = args['agent_id'] || '0',
		urlPath = window.location.href; 
	var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
	function getdetail(uid) {
		var param = {};
		param['agent_id'] = uid;
		var	url=labUser.agent_path + '/message/message-record/_v010000';
		ajaxRequest(param,url,function(data){
			if(data.status){
				if(data.message){
					if(data.message.length<1){
						$('#containerBox').removeClass();
						$('#containerBox').css('padding-top',0);
						$('.default').removeClass('none');
					}else {
						var conHtml = '';
						$.each(data.message,function(m,n){
							var create_time = n.confirm_day;
							conHtml += '<div class="inst act pact"><p class="top"><img src="/images/agent/time_03.png" class="mr1 "/><span class="time c2873ff b  f13">'+create_time+'</span></p>';
							$.each(n.result, function(i,v) {
								
								//考察邀请情况
							if(v.type==1){
								var confirm_time = ymd_hms(v.confirm_time);
								var create_time = md(v.confirm_time);
								conHtml += '<div class="bord-l ml08 "><div class="act-cont bgwhite "><div class="act-1 fline f13">';
								conHtml += '<p><img src="'+v.avatar+'" class="avatar "/></p>';
								conHtml +='<div class="pl1">'
								conHtml += '<span class="b ">'+v.nickname+'</span>&nbsp;';
								if(v.status_summary=="已拒绝"){
									conHtml += '<span class="cfd4d4d">&nbsp;拒绝了&nbsp;</span><span class="">你的&nbsp;</span>';
								}
								else if(v.status_summary=="已接受邀请"){
									conHtml += '<span class="">&nbsp;接受了&nbsp;</span><span class="">你的&nbsp;</span>';
								};
								conHtml += '<span class="c2873ff">'+v.brand_name+'考察邀请</span></div>';
//								if(v.brand_name.length>6){
//									conHtml += '<span class="c2873ff">考察邀请函</span></div>';
//								}else {
//									conHtml += '<span class="c2873ff">'+v.brand_name+'考察邀请</span></div>';
//								};
								conHtml += '</div>';
								conHtml += '<div class="act-2 fline inst-jump" inspect_id="'+v.inspect_id+'"><div class="inst-2l pr1"><div class="inst-2lp text-end mb05">';
								conHtml += '<p style="width:6rem;text-align:left;">';
								conHtml += '<span class="f12 color333">考察场地</span>';
								conHtml += '</p>';
								conHtml += '<p class="ins_add">'
								conHtml += '<span class="f12 color666 mb05 inline_block">'+v.head_address+'</span></br>';
								conHtml += '<span class="f12 color999 inline_block">地址：'+v.inspect_address+'</span>';
								conHtml += '</p></div>';
								conHtml += '<p class="inst-2lp mb1"><span class="f12 color333">考察时间</span><span class="f12 color666">'+ymd(v.inspect_time)+'</span></p>';
								conHtml += '<p class="inst-2lp mb1"><span class="f12 color333">订金金额</span><span class="f12 color666">¥&nbsp;'+v.currency+'</span></p>';
								if(v.status_summary=="已接受邀请"){
									conHtml +='<p class="inst-2lp mb1"><span class="f12 color333">支付方式</span><span class="f12 color666">'+v.pay_way+'</span></p>';
									conHtml += '<p class="inst-2lp mb1"><span class="f12 color333">支付时间</span><span class="f12 color666">'+confirm_time+'</span></p>';
								};
								conHtml += '</div><img src="/images/jump.png"  ins_status="'+v.status+'"/></div>';
								if(v.status_summary=="已拒绝"){
									conHtml += '<span class="cfd4d4d f12 pt1 inline_block">拒绝理由：'+v.reson+'</span>';
								};
								conHtml += '</div></div>';
							}else if(v.type==2){   //活动
								var begin_time = md_hm(v.begin_time);
								conHtml += '<div class="bord-l ml08"><div class="act-cont bgwhite "><div class="act-1 fline f13">';
								conHtml += '<p><img src="'+v.avatar+'" class="avatar "/></p>';
								conHtml += '<div class="pl1">';
								conHtml += '<span class="b ">'+v.nickname+'</span>';
								if(v.status_info.status == -1){
									conHtml += '<span class="cfd4d4d "> &nbsp;拒绝了&nbsp; </span><span class="color666">你的&nbsp;</span>';
								}else if(v.status_info.status == 1){
									conHtml += '<span class="">&nbsp; 接受了&nbsp; </span><span class="color666">你的&nbsp;</span>';
								};
								conHtml += '<span class="c2873ff ">'+v.activity_title+'活动邀请</span></div>';
//								if(v.activity_title.length>6){
//									conHtml += '<span class="c2873ff ">活动邀请函</span></div>';
//								}else {
//									conHtml += '<span class="c2873ff ">'+v.activity_title+'活动邀请</span></div>';
//								};
								conHtml += '</div>';
								conHtml += '<div class="act-2 fline act-jump" act_id="'+v.id+'"><div class="act-2l"><img src="'+v.activity_list_img+'" class="act-2limg mr1"/>';
								conHtml += '<p class="act-2lp over-text"><span class="mb05 over-text f13 b  act-2lspan color333">'+v.activity_title+'</span><br />';
								conHtml += '<span class="over-text f11 act-2lspan color999">开始时间：'+begin_time+'</span><br />';
								conHtml += '<span class="over-text f11 color999">活动地点：'+v.cities+'</span></p></div>';
								conHtml += '<img src="/images/jump.png" act_status="'+v.status_info.status+'"/></div>';
								if(v.status_info.status == -1){
									conHtml += '<span class="cfd4d4d f12 pt1 inline_block">拒绝理由：'+v.status_info.remark+'</span>';
								}
								conHtml += '</div></div>';
							}else if(v.type==3){   //合同
								var confirm_time = ymd_hms(v.confirm_time);
								var pay_time = ymd_hms(v.pay_time);
								var tail_pay_at = ymd_hms(v.tail_pay_at);
								conHtml += '<div class="bord-l ml08 ">';
								conHtml+='<div class="act-cont bgwhite ">';
								conHtml+='<div class="act-1 fline f13">';
								conHtml += '<p><img src="'+v.avatar+'" class="avatar "/></p>';
								conHtml += '<div class="pl1">';  
								if(v.realname ==''){
									conHtml += '<span class="b ">'+v.nickname+'</span>&nbsp;';
								}else {
									conHtml += '<span class="b ">'+v.realname+'</span>&nbsp;';
								}
								
								if(v.status==-1){
									conHtml += '<span class="cfd4d4d">&nbsp;拒绝了&nbsp;</span><span class="">你的&nbsp;</span>';
								}else if(v.status==1 || v.status==2 || v.status==3 || v.status==4 || v.status==5 || v.status==6) {
									conHtml += '<span class="f13 color666 medium">&nbsp;签订&nbsp;</span>';
								};
								conHtml += '<span class="c2873ff">'+v.contract_title+'</span>';
//								if(v.contract_title.length>6){
//									conHtml += '<span class="c2873ff">加盟合同</span>';
//								}else {
//									conHtml += '<span class="c2873ff">'+v.contract_title+'</span>';
//								};
								conHtml += '</div>';
								conHtml += '</div>';
								conHtml += '<div class="act-2 contract_detail" contract_id="'+v.id+'">';
								conHtml += '<div class="inst-2l ">';
								conHtml +='<p class="inst-2lp mb05"><span class="f12 color333">付款协议</span><span class="f12 color666">'+v.contract_title+'</span></p>';
								if(v.status==1 || v.status==2){
									conHtml += '<p class="inst-2lp mb05"><span class="f12 color333">流水号</span><span class="f12 color666">'+v.contract_no+'</span></p>';
								};
								
								conHtml += '<p class="inst-2lp mb05"><span class="f12 color333">加盟品牌</span><span class="f12 color666">'+v.brand+'</span></p>';
								conHtml+='</div>';
								conHtml+='</div>';
								conHtml += '<p class="inst-2lp mb05 mt1"><span class="f12 color333">加盟总费用</span><span class="f12 color666">¥'+v.amount+'</span></p>';
								conHtml += '<p class="inst-2lp mb05"><span class="f12 color333">协议文本</span></p>';
								conHtml += '<div class="pct-2 mb1" address="'+v.address+'">';
								conHtml += '<div class="act-2l pact-text pct-jump"><img src="/images/agent/my_contract.png" class="pact-img mr1"/><p class="pact-2lp ">';
								conHtml += '<span class="over-text f14 b  act-2lspan color333">'+v.contract_title+'</span><br />';
								conHtml+='</p></div>';
								conHtml += '<img src="/images/jump.png" /></div>';
								conHtml += '<div class="fline"></div>'; 
								if(v.status==-1){
									conHtml += '<p class="inst-2lp pt1 pb1"><span class="f12 color333">拒绝理由</span><span class="f12 color666">'+v.remark+'</span></p></div>';  
								}else if(v.status==1 || v.status==2){
									conHtml += '<p class="inst-2lp pt1 pb1"><span class="f12 color333">确定时间</span><span class="f12 color666">'+confirm_time+'</span></p></div></div></div>';
								};
								
								conHtml +='</div>';
							}
								
							});
							conHtml += '</div>';
						});
						$('#containerBox').html(conHtml);
					};
				}
		}
			
		});
		//尾款补齐操作办法 
		$(document).on('click','.wk_payment',function(){
			window.location.href = labUser.path +'webapp/agent/way/detail';
		});
		//点击跳转到相关ovo活动邀请函
		$(document).on('click','.act-jump',function(){
			var actId = $(this).attr('act_id');
			var act_status = $(this).attr('act_status');
			window.location.href = labUser.path + '/webapp/agent/newsactask/detail?invite_id='+actId;
		});
		//点击跳转到相关活动考察邀请函
		$(document).on('click','.inst-jump',function(){
			var inspectId = $(this).attr('inspect_id');
			var ins_status = $(this).attr('ins_status');
			window.location.href = labUser.path + '/webapp/agent/newsinvestask/detail?inspect_id='+inspectId;
		});
		//点击跳转到相关合同邀请函
		$(document).on('click','.contract_detail',function(){
			var contract_id = $(this).attr('contract_id');
			window.location.href = labUser.path +'/webapp/agent/contract/pactdetails?contract_id='+contract_id;
		});
		//点击跳转合同文本
		$(document).on('click','.pct-2',function(){
			var address = $(this).attr('address');
			window.location.href = labUser.path +'js/agent/generic/web/viewer.html?file='+address;
		});
		
	};
		getdetail(uid);
		//时间戳转换
		function md_hm(unix) {
		    var newDate = new Date();
		    newDate.setTime(unix * 1000);
		    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
		    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
		    var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
		    var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
		    return M+'月'+D+'日'+' '+h+':'+m;
		};
		function ymd_hms(unix) {
		    var newDate = new Date();
		    newDate.setTime(unix * 1000);
		    var Y = newDate.getFullYear();
		    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
		    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
		    var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
		    var m = newDate.getMinutes() < 10 ? ('0' + newDate.getMinutes()) : newDate.getMinutes();
		    var s = newDate.getSeconds()<10 ? ('0'+ newDate.getSeconds()) : newDate.getSeconds();
		    return Y + '/' + M + '/' + D + ' ' + h + ':' + m + ':' + s;
		};
		
		function ymd(unix){
			var newDate = new Date();
		    newDate.setTime(unix * 1000);
		    var Y = newDate.getFullYear();
		    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
		    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
		    var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
		    var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
		    return Y + '-' + M + '-' + D;
		};
		//时间戳转换
		function md(unix) {
		    var newDate = new Date();
		    newDate.setTime(unix * 1000);
		    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
		    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
		    var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
		    var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
		    return M +'/'+ D;
		};
});