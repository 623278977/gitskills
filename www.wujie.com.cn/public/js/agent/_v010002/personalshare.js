//zhangxm
Zepto(function() {
	new FastClick(document.body);
	var args = getQueryStringArgs(),
		id = args['customer_id'] || '0', //查看人的id
		uid = args['agent_id'] || '0', //被查看的经纪人ID
		urlPath = window.location.href,
		origin_mark = args['share_mark'] || 0, //分销参数，分享页用
		code = args['code'] || 0;
	var shareFlag = urlPath.indexOf('/share') > 0 ? true : false;
	//获取信息
	function getdetail(uid) {
		var param = {};
		param['agent_id'] = uid;
		param['is_share'] = 1;
		
		var url = labUser.agent_path + '/user/card/_v010002';
		ajaxRequest(param, url, function(data) {
			if(data.status) {
				if(data.message) {
					//个人信息
					var myHtml = '';
					myHtml += '<div class="share-head f36 fline mb2-5 none">无界商圈经纪人</div>';
					myHtml +='<div class="personal share_head">';
					myHtml += '<div class="tops fline tops_share"><div class="xm-amounts xm-inb xm-inbs">';
					myHtml += '<div class="head_ava">';
					myHtml += '<img src="' + data.message.avatar + '" alt="" class="company mr1-33"/>';
					if(data.message.is_real_auth==1){
						myHtml += '<img src="/images/agent/attestation.png" class="attestation"/>';
					};
					
					myHtml += '</div>';
					myHtml += '<div class="messages">';
					if(data.message.is_public_realname==1){
						myHtml += '<p class="dis_con mb08"><span class="b f15 mb1">' + data.message.realname + '</span></p>';
					}else {
						myHtml += '<p class="dis_con mb08"><span class="b f15 mb1">' + data.message.nickname + '</span></p>';
					};
					myHtml += '<p class="dis_con mb08">';
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
					myHtml += '<div class="tel_zan">';
					myHtml += '<a href="tel:'+data.message.username+'"><img src="/images/agent/010002tel.png" class="tel"/></a>';
					myHtml += '<img src="/images/agent/010002zan.png" class="praise"/>';
					myHtml += '</div>';
					myHtml += '</div>';
					myHtml += '<div class="keyword_share">';
					if(data.message.share_label){
						if(data.message.share_label.length>0){
							$.each(data.message.share_label,function(m,n){
								myHtml += '<p class="keywords m05 f11 scale-1">';
								myHtml += '<span class="">'+ n.keyword_name +'</span>&nbsp;';
								myHtml += '<span class="">' + n.likes + '</span>';
								myHtml += '</p>';
							});
						};
					}
					
					myHtml += '</div>';
					myHtml += '</div>';
					myHtml += '<div class="share_pl175 bgwhite mb1-2 pr1-5">';
					myHtml += '<span class="f15 color333">平台介绍</span>';
					myHtml += '<span class="f11 color999 brand_id">了解无界商圈 <img src="/images/agent/black_to.png" /></span>';
					myHtml += '</div>';
					myHtml += '<div class="share_sign bgwhite">';
					myHtml += '<p class="fline idea f15 color333"><span class="mb1-2">服务理念</span></p>';
					myHtml += '</div>';
					myHtml += '<div class="f15 color6 xm-sign ui-nowrap-multi bgwhite share_sign mb1-2">“' + data.message.signature + '”</div>';
					//代理品牌
					var agentHtml = '';
					if(data.message.brand.length > 0) {
						agentHtml += '<div class="personals">';
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
					$('.xm-btn').removeClass('none');
					if(isAndroid){
						$('.keywords').css({
							lineHeight:'2rem'
						});
					};
				};
			};
			//点赞
			$(document).on('click','.praise',function(){
				$('.masking').removeClass('none');
				$('.pop_div').removeClass('none');
				get_pop();
			});
			function get_pop(){
				var params = {};
				params['type'] = 'agent_share';
				var url = labUser.agent_path + '/user/keywords/_v010002';
				ajaxRequest(params,url,function(data){
					if(data.status){
						if(data.message){
							var myHtml = '';
							$.each(data.message, function(i,v) {
								myHtml+='<div class="swiper-slide">';
								$.each(v,function(j,k){
									myHtml+='<div class="tag">';
									myHtml+='<p class="keywords m05 f11 scale-1"keyword_id="'+k.keyword_id+'">';
									myHtml+='<span class="labels " >'+k.keyword_name+'</span>';
									myHtml+='</p>';
									myHtml+='</div>';
								})
								myHtml+='</div>';
							});
							
							$('.swiper-wrapper').append(myHtml);
							var mySwiper = new Swiper('.swiper-container',{
					//		 	direction: 'vertical',
						    	loop:false,
								pagination : '.swiper-pagination',
							});
						}
					}
					
				});
			};
			//跳转商圈简介
			$(document).on('click','.brand_id',function(){
				window.location.href = labUser.path + '/webapp/agent/intro/_v010002';
			});
			//选择标签
			var paramArr = [];
			$(document).on('click','.keywords',function(){
				$(this).toggleClass('scale-1');
				$(this).toggleClass('scale-blue');
				var keyword_id = $(this).attr('keyword_id');
//				console.log($(this).hasClass('scale-blue'));
				if($(this).hasClass('scale-blue')){
					paramArr.push(keyword_id);
				}else {
					$.each(paramArr, function(m,n) {
						if(n==keyword_id){
							paramArr.splice(m,1);
						}
					});
				}
				if(paramArr.length>5){
					tips('最多选5个！');
				}
				console.log(paramArr)
			});
			//关闭评价弹窗
			$(document).on('click','.close_pop',function(){
				paramArr = [];
				$('.swiper-wrapper').children('.swiper-slide').remove();
				$('.masking').addClass('none');
				$('.pop_div').addClass('none');
				window.location.reload();
			});
			//提交评价
			var paramJson = {};
			$(document).on('click','.pop_btn',function(){
				 paramJson = paramArr.join(',');
				 var parames = {};
				 parames['agent_id'] = uid;
				 parames['like_ids'] = paramJson;
				 var url = labUser.agent_path + '/user/share-like/_v010002';
				 if(paramArr.length==0){
				 	tips('请选择！');
				 }else if (paramArr.length>5){
				 	tips('最多选5个！');
				 }else {
				 	ajaxRequest(parames,url,function(data){
					 	if(data.status){
					 		tips('评价成功！');
					 		setTimeout(function(){
					 			$('.masking').addClass('none');
								$('.pop_div').addClass('none');
					 		},1000);
					 		setTimeout(function(){window.location.reload()},1000);
					 	}
					 });
				 }
				 
			});
			//分享页
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
				};
		});
	};

	//二次分享
	function weixinShare(obj, is_share) {
		if(is_weixin()) {
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
		window.location.href = labUser.path + '/webapp/brand/detail/_v020800?uid=0&id='+brand_id+'&is_share=1';
//		if(shareFlag){
//			window.location.href = labUser.path + '/webapp/brand/detail/_v020800?uid=0&id='+brand_id+'&is_share=1';
//		}else {
//			window.location.href = labUser.path + 'webapp/agent/brand/detail?agent_id='+uid+'&id='+brand_id;
//		};
 	});
 	
});