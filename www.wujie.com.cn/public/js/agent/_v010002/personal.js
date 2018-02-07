//zhangxm
Zepto(function() {
	new FastClick(document.body);
	var args = getQueryStringArgs(),
		id = args['customer_id'] || '0', //查看人的id
		uid = args['agent_id'] || '0', //被查看的经纪人ID
		urlPath = window.location.href,
		origin_mark = args['share_mark'] || 0, //分销参数，分享页用
		code = args['code'] || 0;
	var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
	//获取信息
	function getdetail(uid) {
		var param = {};
		param['agent_id'] = uid;
		param['source'] = 'agent';
		if(shareFlag) {
			param['source'] = 'other';
		};
		var url = labUser.agent_path + '/user/card/_v010002';
		ajaxRequest(param, url, function(data) {
			if(data.status) {
				if(data.message) {
					//个人信息
					var myHtml = '';
					myHtml += '<div class="share-head f36 fline mb2-5 none">无界商圈经纪人</div>';
					myHtml +='<div class="personal head_bcg">';
					myHtml += '<div class="tops"><div class="xm-amounts xm-inb xm-inbs">';
					myHtml += '<div class="head_ava">';
					myHtml += '<img src="' + data.message.avatar + '" alt="" class="company mr1-33"/>';
					if(data.message.is_real_auth==1){
						myHtml += '<img src="/images/agent/attestation.png" class="attestation"/>';
					};
					
					myHtml += '</div>';
					myHtml += '<div class="messages">';
					if(data.message.is_public_realname==1){
						myHtml += '<p class="dis_con mb1"><span class="b f15 white ">' + data.message.realname + '</span></p>';
					}else {
						myHtml += '<p class="dis_con mb1"><span class="b f15 white ">' + data.message.nickname + '</span></p>';
					};
					myHtml += '<p class="dis_con ">';
					myHtml += '<span class="share_lv white mr05"><img src="/images/agent/level0'+data.message.level_num+'.png"/></span>';
					if(data.message.level_num==1){
						myHtml += '<span class="color999 f11">初级经纪人</span></p>';
					}
					if(data.message.level_num==2){
						myHtml += '<span class="color999 f11">中级经纪人</span></p>';
					}
					if(data.message.level_num==3){
						myHtml += '<span class="color999 f11">主任</span></p>';
					}
					if(data.message.level_num==4){
						myHtml += '<span class="color999 f11">经理</span></p>';
					}

					myHtml += '</div></div>';
					myHtml += '<span class="xm-edit f12">编辑</span></div>';
					if(data.message.keywords.years!=''){
						myHtml += '<p class="keyword fline"><span class="keywords m05 f11 scale-1 none">' + data.message.keywords.years + '</span>';
					} 
					if(data.message.keywords.constellation!=''){
						myHtml += '<span class="keywords m05 f11 scale-1 none">' + data.message.keywords.constellation + '</span>';
					}
					if (data.message.keywords.native!=''){
						myHtml += '<span class="keywords m05 f11 scale-1 none">' + data.message.keywords.native + '</span>';
					}
					
					if(data.message.keywords.industrys.length > 0) {
						$.each(data.message.keywords.industrys, function(i, v) {
							if(v!=''){
								myHtml += '<span class="keywords m05 f11 scale-1 none">' + v.name + '</span>';
							}
						});
					};
					if(data.message.keywords.evaluate.length > 0) {
						$.each(data.message.keywords.evaluate, function(i, j) {
							if(j!=''){
								myHtml += '<span class="keywords m05 f11 scale-1 none">' + j + '</span>';
							};
						})
					};
					
					myHtml += '</div>';
					myHtml += '<div class="pl1-5 bgwhite">'
					myHtml += '<p class="fline idea f15 color333 bgwhite">服务理念</p>';
					myHtml += '<div class="f15 color6 xm-sign ui-nowrap-multi bgwhite">“' + data.message.signature + '”</div>';
					myHtml += '</div>';
					//代理品牌
					var agentHtml = '';
					if(data.message.brand.length > 0) {
						agentHtml += '<div class="personals mt1-2">';
						agentHtml += '<p class="f15 fline b mb1-5 pt1-5 pb1-5">';
						agentHtml += '<span class="">代理品牌</span>';
						agentHtml += '<span class="keyword-num">(' + data.message.my_agents + ')</span>';
						agentHtml += '</p>';
						agentHtml += '<div class="brand_wrap">';
						$.each(data.message.brand, function(i, k) {
							agentHtml += '<div class="xm-acting xm-inb mb1" brand_id="'+k.id+'"><img src="' + k.logo + '" class="xm-acting-img"/>';
							agentHtml += '<div class="f1 xm-inb"><span class="f15 xm-b brand_name color333 ">' + k.name + '</span>';
							agentHtml += '<span class="f11 dark_gray xm-b color999">行业分类:</span>';
							agentHtml += '<span class="f11 dark_gray xm-b color999">' + k.cateName + '</span><br />';
							agentHtml += '<span class="f11 dark_gray xm-b color999">启动资金:</span>';
							agentHtml += '<span class="f11 dark_gray xm-b color999">' + k.investment_min + '~' + k.investment_max + '万</span></div></div>';
							
						});
						agentHtml += '</div>';
						agentHtml += '</div>';
					}else {
						agentHtml += '<div class="personal_none "><p class="f15 fline ptb1-4 b keyword  ml1 mr1">代理品牌<span class="keyword-num">&nbsp;('+data.message.my_agents+')</span></p>';
						agentHtml += '<div id="defind"><img src="/images/agent/defind_brand.png" style="height: 16rem;width: 16rem;margin:0 auto;display: inline-block;"/></div>';
						agentHtml += '</div>';
					}
					$('#container').html(myHtml+agentHtml);
					if(isAndroid){
						$('.keywords').css({
							lineHeight:'2rem'
						});
					};
				};
			}

			//点击编辑跳转
			function editSelfInfo(id) {
				if(isAndroid) {
					javascript: myObject.editSelfInfo(id);
				}
				else if(isiOS) {
					var data = {
						"id": id
					}
					window.webkit.messageHandlers.editSelfInfo.postMessage(data);
				}
			};
			$(document).on('click', '.xm-edit', function() {
				editSelfInfo(uid);
			});

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
	getdetail(uid);

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
			window.location.href = labUser.path + 'webapp/agent/brand/detail?agent_id='+uid+'&id='+brand_id;
		};
 	});
 	
});