//zhangxm
Zepto(function() {
	new FastClick(document.body);
	var args = getQueryStringArgs(),
		uid = args['agent_id'] || '0', 
		urlPath = window.location.href,
		origin_mark = args['share_mark'] || 0, //分销参数，分享页用
		code = args['code'] || 0;
	var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
	//获取信息
	function getdetail(uid) {
		var param = {};
		param['agent_id'] = uid;
		var url = labUser.agent_path + '/message/recommend-customer/_v010002';
		ajaxRequest(param, url, function(data) {
			if(data.status) {
				if(data.message.length>0) {
					  var myHtml = '';
					  $.each(data.message, function(i,v) {
					  	  myHtml += '<div class="act ">';
				          myHtml +='<div class="top ">';
				          myHtml += '<img src="/images/agent/time_03.png" class="mr1 "/><span class="time c2873ff f13 b">'+v.format+'</span>';
				          myHtml += '</div>';
				          if(v.data_list.length>0){
				          	$.each(v.data_list, function(j,k) {
				          		  myHtml += '<div class="bord-l ml08">';
						          myHtml += '<div class="act-cont bgwhite mb1-2">';
						          
						          myHtml += '<div class="act-1 f13">';
						          myHtml += '<div class="re_l down-pay" user_id="'+k.user_id+'">';
						          
						          myHtml += '<img src="'+k.user_img+'" class="avatar mr1"/>';
						          
						          myHtml += '<div class="">';
						          myHtml += '<span class="color333 f13">'+k.user_name+'</span>';
						          myHtml += '<p class="mt1-5">';
								  if(k.gender==0){
								  	myHtml += '<img src="/images/agent/girl.png" class="gender"/>';
								  }
								  if(k.gender==1){
								  	myHtml += '<img src="/images/agent/boy.png" class="gender"/>';
								  }
						          myHtml += '<span class="color999 f12">'+k.user_zone+'</span>';
						          myHtml += '</p>';
						          myHtml += '</div>';
						          myHtml += '</div>';
						          if(k.status=='-1'){
						          	  myHtml += '<div class="re_r order_receive none">';
							          myHtml += '<button class="re_btn re_receive f12 mb1" user_id="'+k.user_id+'">接单</button><br />';
							          myHtml += '<button class="re_btn re_noreceive scale-1 f12" user_id="'+k.user_id+'">不感兴趣</button>';
							          myHtml += '</div>';
							          
							          myHtml += '<div class="re_r chit_chat none">';
							          myHtml += '<span class="f12 color999 order_already have_order">已接单</span><br />';
							          myHtml += '<button class="re_btn f12 chat mt1" user_id="'+k.user_id+'" user_name="'+k.user_name+'">聊天</button>';
							          myHtml += '</div>';
							          
							          myHtml += '<div class="re_r yet_order_no ">';
							          myHtml += '<span class="f12 color999">拒绝</span>';
							          myHtml += '</div>';
						          }
						          if(k.status==0){
						          	  myHtml += '<div class="re_r order_receive">';
							          myHtml += '<button class="re_btn re_receive f12 mb1" user_id="'+k.user_id+'">接单</button><br />';
							          myHtml += '<button class="re_btn re_noreceive scale-1 f12" user_id="'+k.user_id+'">不感兴趣</button>';
							          myHtml += '</div>';
							          
							          myHtml += '<div class="re_r chit_chat none">';
							          myHtml += '<span class="f12 color999 order_already have_order">已接单</span><br />';
							          myHtml += '<button class="re_btn f12 mb1 chat mt1" user_id="'+k.user_id+'" user_name="'+k.user_name+'">聊天</button>';
							          myHtml += '</div>';
							          
							          myHtml += '<div class="re_r yet_order_no none">';
							          myHtml += '<span class="f12 color999">拒绝</span>';
							          myHtml += '</div>';
						          }
						         
						          //已接单
						          if(k.status==1){
						          	  myHtml += '<div class="re_r order_receive none">';
							          myHtml += '<button class="re_btn re_receive f12 mb1" user_id="'+k.user_id+'">接单</button><br />';
							          myHtml += '<button class="re_btn re_noreceive scale-1 f12" user_id="'+k.user_id+'">不感兴趣</button>';
							          myHtml += '</div>';
							          
							          myHtml += '<div class="re_r chit_chat">';
							          myHtml += '<span class="f12 color999 order_already have_order">已接单</span><br />';
							          myHtml += '<button class="re_btn f12 chat mt1" user_id="'+k.user_id+'" user_name="'+k.user_name+'">聊天</button>';
							          myHtml += '</div>';
							          
							          myHtml += '<div class="re_r yet_order none">';
							          myHtml += '<span class="f12 color999">已被他人接单</span>';
							          myHtml += '</div>';
						          }
						           //已被他人接单
						          if(k.status=='2'){
						          	  myHtml += '<div class="re_r order_receive none">';
							          myHtml += '<button class="re_btn re_receive f12 mb1" user_id="'+k.user_id+'">接单</button><br />';
							          myHtml += '<button class="re_btn re_noreceive scale-1 f12" user_id="'+k.user_id+'">不感兴趣</button>';
							          myHtml += '</div>';
							          myHtml += '<div class="re_r chit_chat none">';
							          myHtml += '<span class="f12 color999 order_already have_order">已接单</span><br />';
							          myHtml += '<button class="re_btn f12 mb1 chat mt1" user_id="'+k.user_id+'" user_name="'+k.user_name+'">聊天</button>';
							          myHtml += '</div>';
							          
							          myHtml += '<div class="re_r yet_order">';
							          myHtml += '<span class="f12 color999">已被他人接单</span>';
							          myHtml += '</div>';
						          }
						          
						          myHtml += '</div>';
						          myHtml += '<div class="act-2">';
						          myHtml += '<p class="">';
						          myHtml += '<span class="f12 color999">意向品牌:&nbsp;</span>';
						          myHtml += '<span class="f12 color999">'+k.fond_brand+'</span>';
						          myHtml += '</p>';
						          myHtml += '<p class="">';
						          myHtml += '<span class="f12 color999">活动参与:&nbsp;</span>';
						          if(k.activity=='是'){
						          	myHtml += '<span class="f12 color999">参加过无界商圈OVO活动</span>';
						          }else {
						          	myHtml += '<span class="f12 color999">没有参加过无界商圈OVO活动</span>';
						          }
						          
						          myHtml += '</p>';
						          myHtml += '<p class="">';
						          
						          myHtml += '<span class="f12 color999">平台活跃度:&nbsp;</span>';
						          myHtml += '<span class="f12 color999">'+k.active+'</span>';
						          myHtml += '</p>';
						          myHtml += '</div>';
						          myHtml += '</div>';
						          myHtml += '</div>';
				          	});
				          }
				          
				          myHtml += '</div>';
					  });
			          
				}else {
					$('.default').removeClass('none');
				}
					$('#containerBox').html(myHtml);
				};
			});
		
	};
	getdetail(uid);
	//点击接单显示聊天
	$(document).on('click','.re_receive',function(){
		var user_id = $(this).attr('user_id');
//		change_customer_status(uid,user_id,1);
		var re_receives = $(this).parents('.order_receive');
		var parames = {};
 		parames['agent_id'] = uid;
 		parames['customer_id'] = user_id;
 		parames['status'] = 1;
 		var url = labUser.agent_path + '/message/change-customer-button-status/_v010002';
 		ajaxRequest(parames,url,function(data){
 			if(data.status){
 				tips('操作成功');
 				re_receives.addClass('none');
				re_receives.siblings('.chit_chat').removeClass('none');
 			}else {
 				tips('操作失败');
 			};
 		});
		
	});
 	//点击拒绝接单
 	$(document).on('click','.re_noreceive',function(){
 		var re_noreceives = $(this).parents('.order_receive');
		var user_id = $(this).attr('user_id');
//		change_customer_status(uid,user_id,'-1');
		var parames = {};
 		parames['agent_id'] = uid;
 		parames['customer_id'] = user_id;
 		parames['status'] = '-1';
 		var url = labUser.agent_path + '/message/change-customer-button-status/_v010002';
 		ajaxRequest(parames,url,function(data){
 			if(data.status){
 				tips('操作成功');
 				re_noreceives.addClass('none');
 				re_noreceives.siblings('.yet_order_no').removeClass('none');
 			}else {
 				tips('操作失败');
 			};
 		});
 	});
 	//点击跳转102投资人详情
 	$(document).on('click','.down-pay',function(){
 		var user_id = $(this).attr('user_id');
 		window.location.href = labUser.path + '/webapp/agent/investor/invition/_v010002?customer_id='+user_id+'&agent_id='+uid;
 	})
 	//点击跳转聊天
 	function goChat(uType,uid,name){
 		if(isAndroid) {
			javascript: myObject.goChat(uType,uid,name);
		}
		else if(isiOS) {
			var data = {
				"uType":uType,
				"id":uid,
				"name":name
			}
			window.webkit.messageHandlers.goChat.postMessage(data);
		}
 	};
 	$(document).on('click','.chat',function(){
 		var user_id = $(this).attr('user_id');
 		var user_name = $(this).attr('user_name');
 		goChat('c',user_id,user_name);
 	});
 	
 	function change_customer_status(agent_id,customer_id,status){
 		var parames = {};
 		parames['agent_id'] = agent_id;
 		parames['customer_id'] = customer_id;
 		parames['status'] = status;
 		var url = labUser.agent_path + '/message/change-customer-button-status/_v010002';
 		ajaxRequest(parames,url,function(data){
 			if(data.status){
 				tips('操作成功');
 			}else {
 				tips('操作失败');
 			};
 		});
 	}
});