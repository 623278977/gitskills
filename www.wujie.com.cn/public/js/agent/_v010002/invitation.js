
//zhangxm
Zepto(function() {
	new FastClick(document.body);
	var args = getQueryStringArgs(),
		id = args['customer_id'] || '0', //被查看的投资人ID
		uid = args['agent_id'] || '0', //查看人的id
		urlPath = window.location.href,
		origin_mark = args['share_mark'] || 0, //分销参数，分享页用
		code = args['code'] || 0;
	var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
	//获取信息
	function getdetail(uid) {
		var param = {};
		param['agent_id'] = uid;
		param['customer_id'] = id;
		if(shareFlag) {
			param['source'] = 'other';
		};
		var url = labUser.agent_path + '/customer/detail-infos/_v010002';
		ajaxRequest(param, url, function(data) {
			if(data.status) {
				if(data.message) {
					
					if(data.message.last_login!=0){
						var last_login = unix_to_mdhm(data.message.last_login);	//最后一次登录时间
					}
					var myHtml = '';
					myHtml += '<div class="personal">';
					myHtml +='<img src="'+data.message.avatar+'" alt="" class="company mr1-33 fl"/>';
					myHtml += '<div class="xm-amounts xm-detail">';
					myHtml += '<p class="mb05 xm-inbs">';
					if(data.message.nickname==''){
						myHtml += '<span class="b f15 color3 ">' + data.message.realname + '</span>';
						$('title').text('投资人'+data.message.realname);
					}else {
						myHtml += '<span class="b f15 color3 ">' + data.message.nickname + '</span>';
						$('title').text('投资人'+data.message.nickname);
					};
					myHtml += '</p>';
					myHtml += '<p class="mb08 xm-inbs">';
					if(data.message.gender == 1) {
						myHtml += '<img src="/images/agent/boy.png" class="gender xm-inb fl"/>';
					} else if(data.message.gender == 0) {
						myHtml += '<img src="/images/agent/girl.png" class="gender xm-inb fl"/>';
					}
					myHtml += '<span class="f12 color999">' + data.message.city +'</span></p>';
					myHtml += '<p class="">';
					if(data.message.last_login==0){
						myHtml += '<span class="dark_gray f14 mt05">未登录</span>';
					}else {
						myHtml += '<span class="dark_gray f14 mt05">最后一次登录：'+last_login+'</span>';
					};
					
					myHtml += '</p></div>';
					myHtml += '</div>';
					myHtml += '<p class="keyword fline label_tags">';
					if(data.message.tags.customer_time!=''){
						myHtml += '<span class="keywords m05 f11 scale-1">' + data.message.tags.customer_time + '</span>';
					}
					if(data.message.tags.constellation!=''){
						myHtml += '<span class="keywords m05 f11 scale-1">' + data.message.tags.constellation + '</span>';
					}
					if (data.message.tags.customer_zone!=''){
						myHtml += '<span class="keywords m05 f11 scale-1">' + data.message.tags.customer_zone + '</span>';
					}
					if (data.message.tags.intention!=''){
						myHtml += '<span class="keywords m05 f11 scale-1">' + data.message.tags.intention + '</span>';
					}
					if (data.message.tags.customer_money!=''){
						myHtml += '<span class="keywords m05 f11 scale-1">' + data.message.tags.customer_money + '万</span>';
					}
					if(data.message.tags.fond_cate){
						if (data.message.tags.fond_cate.length>0){
							$.each(data.message.tags.fond_cate, function(i, v) {
								if(v!=''){
									myHtml += '<span class="keywords m05 f11 scale-1">' + v + '</span>';
								}
							});
							
						};
					};
					myHtml += '</p>';
					
					//代理品牌
						if(data.message.recommend_brand.length > 0) {
							myHtml += '<div class="personals pb1-5">';
							myHtml += '<p class="f15 ptb1-4 b keyword mb2">意向品牌';
							myHtml += '<span class="keyword-num">&nbsp;('+data.message.brand_count+')</span></p>';
							myHtml += '<div class="brand_wrap">';
							$.each(data.message.recommend_brand, function(j, k) {
								myHtml += '<div class="xm-acting xm-inb mb1" brand_id="'+k.brand_id+'"><img src="' + k.brand_logo + '" class="xm-acting-img"/>';
								myHtml += '<div class="f1 xm-inb"><span class="f15 xm-b brand_name color333 medium">' + k.brand_name + '</span>';
								myHtml += '<span class="f11 dark_gray xm-b color999">行业分类:</span>';
								myHtml += '<span class="f11 dark_gray xm-b color999">' + k.brand_cate + '</span><br />';
								myHtml += '<span class="f11 dark_gray xm-b color999">启动资金:</span>';
								myHtml += '<span class="f11 dark_gray xm-b color999">' + k.start_money + '</span></div></div>';
							});
							myHtml += '</div>';
							myHtml += '</div>';
							
							//底部按钮
							var conHtml = '';
							if(data.message.status=='-1'){
								conHtml+='<div class="order_fail_no">';
								conHtml+='<span class="yet_order_no">拒绝</span>';
								conHtml+='</div>';
							};
							if(data.message.status==0){
								conHtml+='<div class="btn ordering">';
								conHtml+='<button class="btn-l f15">不感兴趣</button>';
								conHtml+='<button class="btn-r f15">接单</button>';
								conHtml+='</div>';
								
								conHtml+='<div class="order_fail_no none">';
								conHtml+='<span class="yet_order_no">拒绝</span>';
								conHtml+='</div>';
								
								
								if(data.message.nickname!=''){
									conHtml+='<div class="order_success none" nickname="'+data.message.nickname+'">';
								}else {
									conHtml+='<div class="order_success none" nickname="'+data.message.realname+'">';
								}
								
								conHtml+='<span><img src="/images/agent/sms_03.png" alt=""></span>';
								conHtml+='</div>';
							};
							if(data.message.status==1){
								if(data.message.nickname!=''){
									conHtml+='<div class="order_success" nickname="'+data.message.nickname+'">';
								}else {
									conHtml+='<div class="order_success" nickname="'+data.message.realname+'">';
								}
								
								conHtml+='<span><img src="/images/agent/sms_03.png" alt=""></span>';
								conHtml+='</div>';
							};
							if(data.message.status==2){
								conHtml+='<div class="order_fail">';
								conHtml+='<span>已被他人接单</span>';
								conHtml+='</div>';
							};
						}else {
							myHtml += '<div class="personal_none"><p class="f15 ptb1-4 b keyword mb2 pl1-5">意向品牌<span class="keyword-num">&nbsp;('+data.message.brand_count+')</span></p>';
							myHtml += '<div id="defind"><img src="/images/agent/intention.png" style="height: 16rem;width: 16rem;margin:0 auto;display: inline-block;"/></div>';
							myHtml += '</div>';
							//底部按钮
							var conHtml = '';
							if(data.message.status=='-1'){
								conHtml+='<div class="order_fail_no">';
								conHtml+='<span class="yet_order_no">拒绝</span>';
								conHtml+='</div>';
							};
							if(data.message.status==0){
								conHtml+='<div class="btn ordering">';
								conHtml+='<button class="btn-l f15">不感兴趣</button>';
								conHtml+='<button class="btn-r f15">接单</button>';
								conHtml+='</div>';
								
								conHtml+='<div class="order_fail_no none">';
								conHtml+='<span class="yet_order_no">拒绝</span>';
								conHtml+='</div>';
								if(data.message.nickname!=''){
									conHtml+='<div class="order_success none" nickname="'+data.message.nickname+'">';
								}else {
									conHtml+='<div class="order_success none" nickname="'+data.message.realname+'">';
								}
								
								conHtml+='<span><img src="/images/agent/sms_03.png" alt=""></span>';
								conHtml+='</div>';
							};
							if(data.message.status==1){
								if(data.message.nickname!=''){
									conHtml+='<div class="order_success" nickname="'+data.message.nickname+'">';
								}else {
									conHtml+='<div class="order_success" nickname="'+data.message.realname+'">';
								}
								
								conHtml+='<span><img src="/images/agent/sms_03.png" alt=""></span>';
								conHtml+='</div>';
							};
							if(data.message.status==2){
								conHtml+='<div class="order_fail">';
								conHtml+='<span>已被他人接单</span>';
								conHtml+='</div>';
							};
						};
					$('.foot_btn').html(conHtml);
					$('#container').html(myHtml);
					if(isAndroid){
						$('.keywords').css({
							lineHeight:'2rem'
						});
					};
				};
			}

			//判断是否是分享页
			if(shareFlag) {
				$('.xm-btn').removeClass('none');
				$('.xm-edit').addClass('none');
				if(isiOS) {
					/**下载app**/
					//成为投资人
					$(document).on('click','.xm-btn-invest', function() {
						window.location.href = labUser.path + '/webapp/agent/register/detail?agent_id=' + uid;
					});
					//成为经纪人
					$(document).on('click','.xm-btn-agent', function() {
						window.location.href = labUser.path + '/webapp/agent/letter/send-letter?agent_id=' + uid;
					});
					oppenIos();
				} else if(isAndroid) {
					//成为投资人
					$(document).on('click','.xm-btn-invest', function() {
						
						window.location.href = labUser.path + '/webapp/agent/register/detail?agent_id=' + uid;
					});
					//成为经纪人
					$(document).on('click','.xm-btn-agent', function() {
						window.location.href = labUser.path + '/webapp/agent/letter/send-letter?agent_id=' + uid;
					});
					openAndroid();
				}
			} else {};
		});
	};

	
	getdetail(uid);
//跳转聊天 id:被查看的人的id    name：被查看人的名字
function goChat(uType,id, name) {
	if(isAndroid) {
		javascript: myObject.goChat(uType,id,name);
	}
	else if(isiOS) {
		var data = {
			"uType":uType,
			"id": id,
			"name":name
		}
		window.webkit.messageHandlers.goChat.postMessage(data);
	}
};

$(document).on('click','.order_success',function(){
	var nickname = $(this).attr('nickname');
	goChat('c',id,nickname);
});
//点击接单显示聊天
$(document).on('click','.btn-r',function(){
//	var user_id = $(this).attr('user_id');
//	var re_receives = $(this).parents('.ordering');
	var parames = {};
	parames['agent_id'] = uid;
	parames['customer_id'] = id;
	parames['status'] = 1;
	var url = labUser.agent_path + '/message/change-customer-button-status/_v010002';
	ajaxRequest(parames,url,function(data){
		if(data.status){
			tips('操作成功');
			$('.ordering').addClass('none');
			$('.order_success').removeClass('none');
		}else {
			tips('操作失败');
		};
	});
});
//点击拒绝接单
$(document).on('click','.btn-l',function(){
// 		var re_noreceives = $(this).parents('.order_receive');
//		var user_id = $(this).attr('user_id');
//		change_customer_status(uid,user_id,'-1');
		var parames = {};
 		parames['agent_id'] = uid;
 		parames['customer_id'] = id;
 		parames['status'] = '-1';
 		var url = labUser.agent_path + '/message/change-customer-button-status/_v010002';
 		ajaxRequest(parames,url,function(data){
 			if(data.status){
 				tips('操作成功');
 				$('.ordering').addClass('none');
 				$('.order_fail_no').removeClass('none');
 			}else {
 				tips('操作失败');
 			};
 		});
 	});
//打开本地--Android
function openAndroid() {
	var strPath = window.location.pathname;
	var strParam = window.location.search.replace(/is_share=1/g, '');
	var appurl = strPath + strParam;
	window.location.href = 'openwjsq://welcome' + appurl;
}
//IOS
function oppenIos() {
	var strPath = window.location.pathname.substring(1);
	var strParam = window.location.search;
	var appurl = strPath + strParam;
	var share = '&is_share';
	var appurl2 = appurl.substring(0, appurl.indexOf(share));
	window.location.href = 'openwjsq://' + appurl2;
};

//跳转相应品牌页
$(document).on('click','.xm-acting',function(){
	var brand_id = $(this).attr('brand_id');
	//分享页跳转
	if(shareFlag){
		window.location.href = labUser.path + '/webapp/brand/detail/_v020800?uid=0&id='+brand_id+'&is_share=1';
	}else {
		window.location.href = labUser.path + 'webapp/agent/brand/detail/_v010002?agent_id='+uid+'&id='+brand_id;
	};
});
//接单或拒绝
//function change_customer_status(agent_id,customer_id,status){
//	var parames = {};
//	parames['agent_id'] = agent_id;
//	parames['customer_id'] = customer_id;
//	parames['status'] = status;
//	var url = labUser.agent_path + '/message/change-customer-button-status/_v010002';
//	ajaxRequest(parames,url,function(data){
//		if(data.status){
//			
//		};
//	});
//}
/*时间戳转换成月日时分*/
function unix_to_mdhm(unix) {
    var newDate = new Date();
    newDate.setTime(unix * 1000);
    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
    var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
    var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
    return M + '月' + D + '日 ' + h + ':' + m;
};
//二次分享
	function weixinShare(obj, is_share) {
		if(is_share && is_weixin()) {
			/**微信内置浏览器**/
			$(document).on('tap', '#loadapp,#openapp', function() {
				var _height = $(document).height();
				$('.safari').css('height', _height);
				$('.safari').removeClass('none');
			});
			//点击隐藏蒙层
			$(document).on('tap', '.safari', function() {
				$(this).addClass('none');
			});
			var wxurl = labUser.api_path + '/weixin/js-config';
			//详情描述
			var desptStr = removeHTMLTag(obj.detail);
			var nowhitespace = desptStr.replace(/&nbsp;/g, '');
			var despt = cutString(desptStr, 60);
			var nowhitespaceStr = cutString(nowhitespace, 60);
			// var num=window.location.href.indexOf('from=singlemessage');
			// var w_url=window.location.href.substring(0,num-1);
			// var w_url=encodeURIComponent(window.location.href);

			ajaxRequest({
				url: location.href
			}, wxurl, function(data) {
				if(data.status) {
					wx.config({
						debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
						appId: data.message.appId, // 必填，公众号的唯一标识
						timestamp: data.message.timestamp, // 必填，生成签名的时间戳
						nonceStr: data.message.nonceStr, // 必填，生成签名的随机串
						signature: data.message.signature, // 必填，签名，见附录1
						jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
					});
					wx.ready(function() {
						wx.onMenuShareTimeline({
							title: obj.title, // 分享标题
							link: location.href, // 分享链接
							imgUrl: obj.logo, // 分享图标
							success: function() {
								// 用户确认分享后执行的回调函数
								if($('#share').data('reward') == 1) {
									sencondShare('relay')
								}
							},
							cancel: function() {
								// 用户取消分享后执行的回调函数
							}
						});
						wx.onMenuShareAppMessage({
							title: '无界商圈经纪人',
							desc: nowhitespaceStr,
							link: location.href,
							imgUrl: obj.logo,
							trigger: function(res) {
								// 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
								console.log('用户点击发送给朋友');
							},
							success: function(res) {
								console.log('已分享');
								if($('#share').data('reward') == 1) {
									sencondShare('relay')
								}
							},
							cancel: function(res) {
								console.log('已取消');
							},
							fail: function(res) {
								console.log(JSON.stringify(res));
							}
						});
					});
				}
			});
		} else {
			if(isiOS) {
				/**下载app**/
				//成为投资人
				$(document).on('click', '.xm-btn-invest', function() {
					window.location.href = labUser.path + '/webapp/agent/register/detail?agent_id=' + uid;
				});
				//成为经纪人
					$(document).on('click', '.xm-btn-agent', function() {
						window.location.href = labUser.path + '/webapp/agent/letter/send-letter?agent_id=' + uid;
					});
//				oppenIos();
			} else if(isAndroid) {
				//成为投资人
				$(document).on('click ', '.xm-btn-invest', function() {
					window.location.href = labUser.path + '/webapp/agent/register/detail?agent_id=' + uid;
				});
				//成为经纪人
				$(document).on('click', '.xm-btn-agent', function() {
					window.location.href = labUser.path + '/webapp/agent/letter/send-letter?agent_id=' + uid;
				});
				
//				openAndroid();
			}
		}
	};
});

