//zhangxm
Zepto(function(){
		new FastClick(document.body);
		var args=getQueryStringArgs(),
            agent_id = args['agent_id'] || '0',
            brand_id = args['is_brand'],
            isShare = args['is_share'],
			urlPath = window.location.href;
		var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
		if(shareFlag){
			$(document).ready(function(){
				$('title').text('压轴盛宴，礼见新年');
			});
		};
        // 获取详情
		function getdetail(agent_id){
			var param={};
            param['agent_id']=agent_id;
            if(shareFlag){
                param['guess']=1;
            }       
			var	url=labUser.agent_path + '/agent-redpacket/active-detail/_v010300';
			ajaxRequest(param,url,function(data){
				if (data.status) {
					var listHtml = '',fu = data.message.good_card_list;
					//抽奖次数获取
					$('.lottery').attr('draw_num',data.message.draw_num);
					$('.draw_nums').text(data.message.draw_num);
					if(data.message.draw_num>0){
						$('.cjNo').addClass('none');
						$('.cjYes').removeClass('none');
					}else {
						$('.lottery').attr("disabled",'disabled');
						$('.lottery').css("background",'grey');
						$('.lottery').removeClass('ccf3a40');
						$('.cjYes').addClass('none');
						$('.cjNo').removeClass('none');
					}
					if(data.message.draw_log.length>0){
						
						listHtml += '<ul>';
						$.each(data.message.draw_log, function(i,v) {
							if(v.type==7){
								listHtml += '<li class="cfff">'+v.name+'已获得'+v.cont+'元红包';
							}else {
								listHtml += '<li class="cfff">'+v.name+'已获得'+v.cont+'字福卡';
							};
						});
						listHtml += '</ul>';
						//福字储存情况
						if(fu.wu){
							
							if(fu.wu.count==0){
								$('.wu img').attr('src','/images/agent/wugrey.png');
								$('.wu span').addClass('none');
							}else {
								$('.wu img').attr('src','/images/agent/wured.png');
								$('.wu span').text('x'+fu.wu.count);
							};
							$('.wu img').attr({
								'card_id':fu.wu.id,
								'num':fu.wu.count
							});
						}
						if(fu.jie){
							
							if (fu.jie.count==0) {
								$('.jie img').attr('src','/images/agent/jiegrey.png');
								$('.jie span').addClass('none');
							} else{
								$('.jie img').attr('src','/images/agent/jiered.png');
								$('.jie span').text('x'+fu.jie.count);
							};
							$('.jie img').attr({
								'card_id':fu.jie.id,
								'num':fu.jie.count
							});
						}
						if(fu.shang){
							
							if (fu.shang.count==0) {
								$('.shang img').attr('src','/images/agent/shanggrey.png');
								$('.shang img').addClass('none');
							} else{
								$('.shang img').attr('src','/images/agent/shangred.png');
								$('.shang span').text('x'+fu.shang.count);
							};
							$('.shang img').attr({
								'card_id':fu.shang.id,
								'num':fu.shang.count
							});
						}
						if (fu.quan) {
							
							if (fu.quan.count==0) {
								$('.quan img').attr('src','/images/agent/quangrey.png');
								$('.quan span').addClass('none');
							} else{
								$('.quan img').attr('src','/images/agent/quanred.png');
								$('.quan span').text('x'+fu.quan.count);
							};
							$('.quan img').attr({
								'card_id':fu.quan.id,
								'num':fu.quan.count
							});
						}
						if (fu.fu) {
							
							if (fu.fu.count==0) {
								$('.fu img').attr('src','/images/agent/fugrey.png');
								$('.fu span').addClass('none');
							} else{
								$('.fu img').attr('src','/images/agent/fured.png');
								$('.fu span').text('x'+fu.fu.count);
							};
							$('.fu img').attr({
								'card_id':fu.fu.id,
								'num':fu.fu.count
							});
						}
						
					}
				} else{
					
				}
				$('.scroll-box').html(listHtml);
				awards();
			}); 
		};
		getdetail(agent_id);
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

//点击福卡跳转详情
$(document).on('click','.fuka',function(){
	var card_id = $(this).attr('card_id');
	var num = $(this).attr('num');
	window.location.href = labUser.path +'/webapp/agent/acquirefu/detail?agent_id='+agent_id+'&card_id='+card_id+'&num='+num;
});
//答题
$(document).on('click','.dati',function(){
	window.location.href = labUser.path+'/webapp/agent/dati/index/_v010300?agent_id='+agent_id;
})

//分享
$(document).on('click','.share_out',function(){
	showShare();
})
//底部答题和分享切换
//点击显示答题
$(document).on('click','.answer',function(){
	$('.answer img').attr('src','/images/agent/anschoose.png');
	$('.foot_share img').attr('src','/images/agent/sharebtnfu.png');
	$('.answerBtn').removeClass('none');
	$('.shareBtn').addClass('none');
});
//点击显示分享
$(document).on('click','.foot_share',function(){
	$('.answer img').attr('src','/images/agent/ans.png');
	$('.foot_share img').attr('src','/images/agent/sharechoose.png');
	$('.answerBtn').addClass('none');
	$('.shareBtn').removeClass('none');
});
//点击抽奖
$(document).on('click','.lottery',function(){
	var draw_num = $(this).attr('draw_num');
	if(draw_num!=0){
		$('.lottery').removeAttr('disabled');
		$('.lottery').css("background",'#f4c85d');
		$(this).addClass('ccf3a40');
		//跳转获奖页
		window.location.href = labUser.path + '/webapp/agent/fuka/detail?agent_id='+agent_id;
	}else {
		tips('您的抽奖次数已用完');
	};
	
});

//转发详情页；
$('.ui-zan-zhaun li').eq(1).find('button').on('click',function(){
	if(shareFlag){
	  tips('请至APP转发')
    }else{
        showShare(); 
    }        
});
if(shareFlag){
	if(isShare==1){
		$('#installapp').removeClass('none');
        $('#share').addClass('none');
        $('#container').css('padding-top','3.2rem');
        $('.loadapp').removeClass('none');
        weixinShare(data.message,shareFlag);
	}
    
};
		

		


//点击隐藏蒙层
$(document).on('tap', '.safari', function () {
    $(this).addClass('none');
});
//关闭分享机制提醒
 $(document).on('tap','.close_share',function(){
    $('.share').addClass('none');
 });
        // 提示框
function alertShow(content){
    $(".alert>p").text(content);
    $(".alert").css("display","block");
    setTimeout(function(){$(".alert").css("display","none")},2000);
}; 

//禁用长按弹出菜单事件
$('img').bind('contextmenu', function(e) {
  e.preventDefault();
});         
});

//二次分享
		function weixinShare(obj,is_share){
			if(is_share&&is_weixin()){
                /**微信内置浏览器**/
                $(document).on('tap', '#loadapp,#openapp', function () {
                    var _height = $(document).height();
                    $('.safari').css('height', _height);
                    $('.safari').removeClass('none');
                });
                //点击隐藏蒙层
                $(document).on('tap', '.safari', function () {
                    $(this).addClass('none');
                });
				var wxurl = labUser.api_path + '/weixin/js-config';
                        //详情描述
                        var desptStr = removeHTMLTag(obj.detail);
                        var nowhitespace = desptStr.replace(/&nbsp;/g,'');
                        var despt = cutString(desptStr, 60);
                        var nowhitespaceStr =cutString(nowhitespace, 60);
                        // var num=window.location.href.indexOf('from=singlemessage');
                        // var w_url=window.location.href.substring(0,num-1);
                        // var w_url=encodeURIComponent(window.location.href);
                        ajaxRequest({url: location.href}, wxurl, function (data) {
                            if (data.status) {
                                wx.config({
                                    debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                                    appId: data.message.appId, // 必填，公众号的唯一标识
                                    timestamp: data.message.timestamp, // 必填，生成签名的时间戳
                                    nonceStr: data.message.nonceStr, // 必填，生成签名的随机串
                                    signature: data.message.signature, // 必填，签名，见附录1
                                    jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
                                });
                                wx.ready(function () {
                                	//分享到朋友圈
                                    wx.onMenuShareTimeline({
                                        title: obj.title, // 分享标题
                                        link:location.href, // 分享链接
                                        imgUrl: obj.logo, // 分享图标
                                        success: function () {
                                            // 用户确认分享后执行的回调函数
                                            
                                        },
                                        cancel: function () {
                                            // 用户取消分享后执行的回调函数
                                        }
                                    });
                                    //分享给朋友
                                    wx.onMenuShareAppMessage({
                                        title: obj.title,
                                        desc: nowhitespaceStr,
                                        link: location.href,
                                        imgUrl: obj.logo,
                                        trigger: function (res) {
                                            // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
//                                          console.log('用户点击发送给朋友');
                                        },
                                        success: function (res) {
                                            console.log('已分享');
                                            
                                        },
                                        cancel: function (res) {
//                                          console.log('已取消');
                                        },
                                        fail: function (res) {
//                                          console.log(JSON.stringify(res));
                                        }
                                    });
                                });
                            }
                        });
			}else{
                if (isiOS) {
                            //打开本地app
                            $(document).on('tap', '#openapp', function () {
                                oppenIos();
                            });
                            /**下载app**/
                            $(document).on('tap', '#loadapp', function () {
                                window.location.href = 'https://itunes.apple.com/cn/app/id1282277895';
                            });
//                          oppenIos();
                    }
                    else if (isAndroid) {
                        $(document).on('tap ', '#loadapp', function () {
                            window.location.href = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.tyrbl.agent';
                        });
                        $(document).on('tap', '#openapp', function () {
                           openAndroid();
                       });
//                      openAndroid();
                    }
            }

		};
//打开本地--Android
function openAndroid(){
    var strPath = window.location.pathname;
    var strParam = window.location.search.replace(/is_share=1/g, '');
    var appurl = strPath + strParam;
    window.location.href = 'openagent://welcome' + appurl;
}
function oppenIos(){
    var strPath = window.location.pathname.substring(1);
    var strParam = window.location.search;
    var appurl = strPath + strParam;
    var share = '&is_share';
    var appurl2 = appurl.substring(0, appurl.indexOf(share));
    window.location.href = 'openagent://' + appurl2;
};
            