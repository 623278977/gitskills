//zhangxm
new FastClick(document.body);
var urlPath = window.location.href,
	args = getQueryStringArgs(),
	id = args['id'],
	agent_id = args['agent_id'] || 0,
	page = 1,
	pageSize = 3;
var Params = {
	"id": id,
	"uid": agent_id,  
	"page": page,
	"page_size": pageSize
}; 
var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
var messageDetail = {
	init: function(id) {
		var param = {};
		param["id"] = id;
		param["uid"] = agent_id;
		var url = labUser.agent_path + '/talking_exercise/detail/_v010100';
		ajaxRequest(param, url, function(data) {
			if(data.status) {
				forShareBy(data.message);
				messageDetail.data(data.message);
				$('#act_container').removeClass('none');
				$('#content').attr('summary',data.message.share_summary);
			}
		});
	},
	data: function(obj) {
		$('.ui-titletext').html(obj.title);
		$('.ui-public-time').html(obj.date);
    	$('.ui-text-detail').html(obj.detail.replace(/http:/g, 'https:'));
    	getpict('.ui-text-detail');
		//分享用的数据
		$('#share').data('title', obj.title).data('content', obj.detail).data('img',obj.share_image);
		if(obj.recommend) {
			$.each(obj.recommend, function(k, v) {
				var html = '';
				html += '<p data-id="' + v.id + '"><a class="ui-nowrap-multi1">' + v.title + '</a></p>';
				$('.ui-link-title').append(html);
			})
		}
	},
	unix: function(unix) {
		var newDate = new Date();
		newDate.setTime(unix * 1000);
		var Y = newDate.getFullYear(),
			M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1,
			D = newDate.getDate() < 10 ? ('0' + newDate.getDate()) : newDate.getDate(),
			h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours(),
			m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes(),
			s = newDate.getSeconds() < 10 ? '0' + newDate.getSeconds() : newDate.getSeconds();
		return M + '月' + D + '日';
	}

};
messageDetail.init(id, agent_id);
//转发详情页；
$('.ui-zan-zhaun li').eq(1).find('button').on('click', function() {
	if(shareFlag) {
		tips('请至APP转发');
	} else {
		showShare();
	}
});

//评论点赞或取消；
//   $(document).on('click','.ui-zan-zone',function(){
//              var id=$(this).data('id');
//              var zan_num=$(this).find('.zan_num').text();
//              if(!shareFlag){
//                  if($(this).find('img').attr('src')=='/images/agent/weizan.png'){
//                      $(this).find('img').attr('src','/images/agent/zan.png');
//                      $(this).find('.zan_num').text(zan_num-1+2);
//                      zanOrzan(id,agent_id,1);
//                  }else{
//                      $(this).find('img').attr('src','/images/agent/weizan.png');
//                      $(this).find('.zan_num').text(zan_num-1);
//                      zanOrzan(id,agent_id,0);
//                  }
//              }else{
//                  tips('请至APP点赞')
//              }
//
//   })
function zanOrzan(id, agent_id, type) {
	var param = {};
	param['id'] = id;
	param['uid'] = agent_id;
	param['type'] = type;
	var url = labUser.agent_path + '/comment/assign-user-comment-add-zan/_v010005';
	ajaxRequest(param, url, function(data) {

	})
}
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
} //forShareBy;
function tips(e) {
	$('.tips').text(e).removeClass('none');
	setTimeout(function() {
		$('.tips').addClass('none');
	}, 3000);
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
