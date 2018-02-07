//zhangxm
Zepto(function(){
	new FastClick(document.body);
	$('body').css('background','#f2f2f2');
	var args = getQueryStringArgs(),
		id = args['id'] || '0', //音频的id
		agent_id = args['agent_id'] || '0',
		audio_len = args['audio_len'],
		urlPath = window.location.href;
	var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
	//获取详情
	function getdetail(id){
		var param = {};
		param['id'] = id;
		param['agent_id'] = agent_id;
		var url = labUser.agent_path + '/talking_skill/detail/_v010004';
		ajaxRequest(param,url,function(data){
			var conHtml = '';
			if(data.status){
				$('#container').attr('logo',data.message.logo);
				$('#content').attr('summary',data.message.share_summary);
				if(data.message){
					$('.walkplay_header img').attr('src',data.message.image);
					$('.walkplay_title').text(data.message.subject);
					
					conHtml += '<p class="cont_text">'+data.message.description+'</p>';
					$('#container').attr('sharecontent',data.message.share_summary);
				};
				$('.weixinAudio').weixinAudio({
					autoplay:true,
					src:data.message.audio_url,
				}); 
				forShareBy(data.message);
			};
			$('.detail').html(conHtml);
			console.log(audio_len)
			$('#none_audio_len').text(audio_len);
		});
		
	}
	getdetail(id);
	
	//--------------------------
//分享专用
function forShareBy(selfObj) {
	if(shareFlag) {
		$('#installapp').removeClass('none');
		$('#loadapp').removeClass('none');
		if(is_weixin()) {
			$(document).on('tap', '#loadapp,#openapp', function() {
				var _height = $(document).height();
				$('.safari').css('height', _height);
				$('.safari').removeClass('none');
			});
			$(document).on('tap', '.safari', function() {
				$(this).addClass('none');
			});
			var wxurl = labUser.api_path + '/weixin/js-config';
			var desptStr = removeHTMLTag(selfObj.detail.contents);
			var nowhitespace = desptStr.replace(/&nbsp;/g, '');
			var despt = cutString(desptStr, 60);
			var nowhitespaceStr = cutString(nowhitespace, 60);
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
						//分享到朋友圈
						wx.onMenuShareTimeline({
							title: selfObj.detail.title, // 分享标题
							link: location.href, // 分享链接
							imgUrl: selfObj.detail.share_image, // 分享图标
							success: function() {
								if($('#share').data('reward') == 1) {
									sencondShare('relay')
								}

							},
							cancel: function() {
								// 用户取消分享后执行的回调函数
							}
						});
						//分享给朋友
						wx.onMenuShareAppMessage({
							title: selfObj.detail.title,
							desc: nowhitespaceStr,
							link: location.href,
							imgUrl: selfObj.detail.share_image,
							trigger: function(res) {
								// 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
								console.log('用户点击发送给朋友');
							},
							success: function(res) {
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
		} else { //2
			if(isiOS) {
				$(document).on('tap', '#openapp', function() {
					oppenIos();
				});
				/**下载app**/
				$(document).on('tap', '#loadapp', function() {
					window.location.href = 'https://itunes.apple.com/cn/app/id1282277895';
				});
			} else if(isAndroid) {
				$(document).on('tap ', '#loadapp', function() {
					window.location.href = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.tyrbl.agent';
				});
				$(document).on('tap', '#openapp', function() {
					openAndroid();
				});
			}
		} //2
	}
};
//打开本地--Android
function openAndroid() {
	var strPath = window.location.pathname;
	var strParam = window.location.search.replace(/is_share=1/g, '');
	var appurl = strPath + strParam;
	window.location.href = 'openagent://welcome' + appurl;
}

function oppenIos() {
	var strPath = window.location.pathname,
		strParam = window.location.search.replace(/&is_share=1/g, ''),
		appurl = labUser.path + strPath + strParam;
	window.location.href = 'openagent://' + appurl;
};

})
