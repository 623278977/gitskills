//zhangxm
Zepto(function() {
	new FastClick(document.body);
	var args = getQueryStringArgs(),
		id = args['customer_id'] || '0', //被查看的投资人id   
		uid = args['agent_id'] || '0', //登录的id            
		urlPath = window.location.href,
		origin_mark = args['share_mark'] || 0, //分销参数，分享页用
		code = args['code'] || 0;
	var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
	// 获取详情
	function getdetail(id, uid) {
		var param = {};
		param['customer_id'] = id;
		param['agent_id'] = uid;
		if(shareFlag) {
			param['guess'] = 1;
		};
		var url = labUser.agent_path + '/customer/detail-infos/_v010000';
		ajaxRequest(param, url, function(data) {
			if(data.status) {
				var conHtml = '';
				var k = data.message;
				if(data.message) {
					var created_at = unix_to_yeardate(data.message.created_at); //注册时间

					conHtml += '<div class="datum bcg-f medium"><img src="' + k.avatar + '" class="photo"/>';
					conHtml += '<div class="datum-l "><span class="f18 b text_black bold">' + k.realname + '</span><br />';
					conHtml +='<div>';
					if(k.gender == 0) {
						conHtml += '<img src="/images/agent/boy.png" class="gender mt05 mr05"/>';
					} else if(k.gender == 1) {
						conHtml += '<img src="/images/agent/boy.png" class="gender mt05 mr05"/>';
					}
					conHtml += '<span class="city dark_gray f14 mt05">' + k.city + '</span>';
					conHtml += '</div>';
					conHtml += '<span class="dark_gray f14 mt05">最后一次登录：' + unix_to_mdhm( k.relation_tel) + '</span></div></div>';
					conHtml += '<div class="relation bcg-f medium"><span class="f15 color3 b">关系</span><p><span class="color999 f12 ">' + k.relation + '</p></div>';
					conHtml += '<div class="content bcg-f"><p class="fline"><span class="color3 b f15">地区</span><span class="color999 f12">' + k.city + '</span></p>';
					conHtml += '<p class="fline"><span class="color3 b f15">学历</span><span class="color999 f12">' + k.diploma + '</span></p>';
					conHtml += '<p class="fline"><span class="color3 b f15">职位</span><span class="color999 f12">' + k.positions + '</span></p>';
					conHtml += '<p class="fline"><span class="color3 b f15">收入</span><span class="color999 f12">' + k.earning + '</span></p>';
					conHtml += '<p class="fline"><span class="color3 b f15">感兴趣行业</span><span class="color999 f12">' + k.interest_industries + '</span></p>';
					conHtml += '<p class="fline"><span class="color3 b f15">投资意向</span><span class="color999 f12">' + k.invest_intention + '</span></p>';
					conHtml += '<p class="fline"><span class="color3 b f15">投资额度</span><span class="color999 f12">' + k.invest_quota + '</span></p></div>';
					conHtml += '<div class="content bcg-f"><p class="fline"><span class="color3 b f15">邀请人</span><span class="color999 f12">' + k.invite_agent + '</span></p>';
					conHtml += '<p class="fline"><span class="color3 b f15">注册时间</span><span class="color999 f12">' + created_at + '</span></p></div>';
					conHtml += '<div class="btn"><button class="btn-l"><img src="/images/agent/sms_03.png"/></button><button class="btn-r"><a href="tel:'+k.relation_tel+'"><img src="/images/agent/tel.png"/></a></button></div>';
				}
				$('#container').html(conHtml);
			};

		});
	};
	getdetail(id, uid);
	
//	document.body.addEventListener('touchmove', function (event) {
//  	event.preventDefault();
//	}, false);
	//跳转聊天
	function chat(id, uid, name) {
		if(isAndroid) {
			javascript: myObject.chat(id, uid,name);
		}
		else if(isiOS) {
			var data = {
				"id": id,
				"uid":uid,
				"name":name
			}
			window.webkit.messageHandlers.chat.postMessage(data);
		}
	};
	//电话
	function call(id, uid, name) {
		if(isAndroid) {
			javascript: myObject.call(id, uid,name);
		}
		else if(isiOS) {
			var data = {
				"id": id,
				"uid":uid,
				"name":name
			}
			window.webkit.messageHandlers.call.postMessage(data);
		}
	};
	$(document).on('click', '.btn-l', function() {
		var name = $('.text_black').html();
		chat(id, uid, name);
	});
//	$(document).on('click', '.btn-r', function() {
//		var name = $('.text_black').html();
//		call(id, uid,name);
//	});

	//时间戳转换
	function yeardate(unix) {
		var newDate = new Date();
		newDate.setTime(unix * 1000);
		var Y = newDate.getFullYear();
		var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
		var D = newDate.getDate() < 10 ? ('0' + newDate.getDate()) : newDate.getDate();
		return Y + '年' + M + '月' + D + '日';
	};
	/*时间戳转换成月日时分*/
	function unix_to_mdhm(unix) {
		var newDate = new Date();
		newDate.setTime(unix * 1000);
		var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
		var D = newDate.getDate() < 10 ? ('0' + newDate.getDate()) : newDate.getDate();
		var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
		var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
		return M + '月' + D + '日 ' + h + ':' + m;
	};

});