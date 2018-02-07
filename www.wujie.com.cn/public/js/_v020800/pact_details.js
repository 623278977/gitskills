Zepto(function(){
	new FastClick(document.body);
	var args=getQueryStringArgs(),
        id = args['contract_id'] || '0',  //合同id
//      uid = args['agent_id'] || '0',
		urlPath = window.location.href,
        origin_mark = args['share_mark'] || 0,//分销参数，分享页用
        code = args['code'] || 0;
	var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
	var is_out = urlPath.indexOf('is_out') > 0 ? true : false;
	function getdetail(id){
		var param = {};
		param['contract_id'] = id;
		if(is_out){
            param['guess']=1;
       };
       var url = labUser.api_path + '/contract/detail/_v020800';
       ajaxRequest(param,url,function(data){
       		var conHtml = '';
       		var textHtml = '';
       		if(data.status){
       			if(data.message){
       				$.each(data.message, function(i,v) {
       					if(v.status==-1){
       						conHtml+='<div class="head cfc5d5d head_dis">';
       						conHtml+='<img src="/images/agent/ico_delete3.png" class="mr1"/>';
       						conHtml+='<span class="f16 color-white">已拒绝'+v.brand+'付款协议</span>';
       						conHtml+='</div>';
       					}else if(v.status==1 || v.status==2){
       						conHtml+='<div class="head c57c88d head_dis">';
       						conHtml+='<img src="/images/agent/icon-true3.png" class="mr1"/>';
       						conHtml+='<span class="f16 color-white">已签订'+v.brand+'付款协议</span>';
       						conHtml+='</div>'; 
       					};
       					conHtml+='<div class="pub-state bgwhite mt4-5"><p class=" mb1-5">';
       					if(v.realname!=''){
       						conHtml+='<span class="bold f14 b color333 bold">To 无界商圈投资人&nbsp;</span><span class="bold f14 b cffa300">&nbsp;'+v.realname+'&nbsp;</span>';
       					}else {
       						conHtml+='<span class="bold f14 b color333 bold">To 无界商圈投资人&nbsp;</span><span class="bold f14 b cffa300">&nbsp;'+v.nickname+'&nbsp;</span>';
       					}
       					
       					if(v.gender==0){
							conHtml+='<span class="f14 b color333 bold">&nbsp;女士:</span>';
						}else if(v.gender==1){
							conHtml+='<span class="f14 b color333 bold">&nbsp;先生:</span>';
						}else {
							conHtml+='<span class="f14 color333 b bold">:</span>';
						};
						conHtml+='</p>';
						conHtml+='<p class="pub-1"><span class="f12 color666 ">通过经纪人</span><span class="cffa300 f12 ">&nbsp;('+v.agent_name+')&nbsp;</span>';
						conHtml+='<span class="f12 color666 ">的对接，您是否对品牌已经有了加盟的想法？</span><br />';
						conHtml+='<span class="f12 color666 ">在这里，向您发出品牌加盟的橄榄枝，我们提供全网最优质的服务和最低的加盟费用，并为您提供相应的加盟扶持。</span></p>';
						conHtml+='<span class="logo-img"><img src="/images/agent/logopact_10.png"/></span></div>';
						conHtml+='<div class="pact bgwhite mt1-2"><div class="pl1-33 pr1-33 pt05">';
						
						conHtml+='<div class="act-2 "><div class="inst-2l ">';
						conHtml+='<p class="inst-2lp mb05"><span class="f14 color999 ">付款协议</span><span class="f14 color333 ">'+v.contract_title+'</span>';
						if(v.status==1 || v.status==2){
							conHtml+='<p class="inst-2lp mb05"><span class="f14 color999">流水号</span><span class="f14 color333">'+v.contract_no+'</span></p>';
						};
						conHtml+='<p class="inst-2lp mb05"><span class="f14 color999 ">加盟品牌</span><span class="f14 color333 ">'+v.brand+'</span></p>';
						conHtml+='<p class="inst-2lp mb05"><span class="f14 color999 ">经纪人</span><span class="f14 color333 ">'+v.agent_name+'</span></p></div></div>';
						//拒绝 
						if(v.status==-1){
							conHtml+= '<div>';
//							conHtml+='<p class="inst-2lp  text-end fline mb05"><span class="f14 color999 ">撰写</span><span class="f14 color333 pb1 ">无界商圈法务代表<br />'+v.brand+'法务代表</span></p>';
							conHtml+='<p class="inst-2lp mb05 "><span class="f12 color999 ">加盟总费用</span><span class="f12 color333 ">¥&nbsp;'+v.amount+'</span></p>';
							conHtml+='</div>';
							conHtml+='<p class="inst-2lp mb05"><span class="f12 color999 ">协议文本</span></p>';
							conHtml+='<div class="pct-2 mb1 fline" address="'+v.address+'"><div class="act-2l pact-text">';
							conHtml+='<img src="/images/agent/my_contract.png" class="pact-img mr1" />';
							conHtml+='<p class="pact-2lp over-text"><span class="over-text f14 b bold act-2lspan color333">'+v.brand+'加盟合同</span><br />';
							conHtml+='</p></div>';
							conHtml+='<img src="/images/jump.png" class="pct-jump" brand_id="'+v.brand_id+'"/>';
							conHtml+='</div></div>';
							
							conHtml+='<div class="pay-off text-end pr1-5 f12 mb3"><p class="mb05"><span class="color333 b ">邀请人</span></p>';
							conHtml+='<p class="mb05"><span class="color333 b ">跟单经纪人：</span><span class="color333 b bold">'+v.agent_name+'</span></p>';
							conHtml+='<p class="mb05"><span class="color999 ">邀请时间：</span><span class="color999 ">'+unix_to_fulltime_s(v.created_at)+'</span></p>';
							conHtml+='<p class="mb05"><span class="color999 ">拒绝时间：</span><span class="color999 ">'+unix_to_fulltime_s(v.confirm_time)+'</span></p></div>';
						};
						//待确定
						if(v.status==0){
							conHtml+='<div><div class="fline"></div><p class="inst-2lp mb05"><span class="f14 color999 ">加盟总费用</span><span class="f14 color333 ">¥ '+v.amount+'</span></p>';
							conHtml+='<p class="inst-2lp mb05 "><span class="f14 color999 ">线上首付</span><span class="f14 color333 ">¥ '+v.pre_pay+'</span></p>';
							conHtml+='<p class="inst-2lp mb05"><span class="f14 color999 ">线下尾款</span><span class="f14 color333 ">¥ '+v.tail_pay+'</span></p>';
							conHtml+='<div class="inst-2lp mb05  text-end"><span class="f14 color999 ">缴纳方式</span>';
							conHtml+='<p class="f12 ">';
							conHtml+='<span class="f14 mb05 color333 ">线上首付一次结清</span><br />';
							conHtml+='<span class="f14 mb05 color333 ">线下尾款银行转账</span><br />';
							conHtml+='<span class="f10 c2873ff mb05  wk_payment">了解尾款补齐操作办法</span><br />';
							conHtml+='</p></div>';
							conHtml+='</div>';
							conHtml+='<p class="inst-2lp mb05"><span class="f14 color999 ">协议文本</span></p>';
							conHtml+='<div class="pct-2 mb1 fline" address="'+v.address+'"><div class="act-2l pact-text">';
							conHtml+='<img src="/images/agent/my_contract.png" class="pact-img mr1" />';
							conHtml+='<p class="pact-2lp over-text"><span class="over-text f14 b bold act-2lspan color333">'+v.brand+'加盟合同</span><br />';
							conHtml+='</p></div>';
							conHtml+='<img src="/images/jump.png" class="pct-jump" brand_id="'+v.brand_id+'"/>';
							conHtml+='</div></div>';
							conHtml+='<div class="pay-off text-end pr1-5  f12 mb3"><p class="mb05"><span class="color333 b ">邀请人</span></p>';
							conHtml+='<p class="mb05"><span class="color333 b ">跟单经纪人：</span><span class="color333 b bold">'+v.agent_name+'</span></p>';
							conHtml+='<p class="mb05"><span class="color999 ">邀请时间：</span><span class="color999 ">'+unix_to_fulltime_s(v.created_at)+'</span></p></div>';
							//待支付按钮
							conHtml+='<div class="pd-btn bgwhite f15 fixed-bottom-iphoneX"><span class="to-pay f16 cfe556b">拒绝</span><span class="sign color-white">签署合同</span></div>';
						};
						//已确定
						if(v.status==1 || v.status==2){
							conHtml+='<div class=""><div class="inst-2l ">';
							conHtml+='<p class="inst-2lp mb05"><span class="f14 color999 ">加盟费用</span><span class="f14 color333">¥ '+v.amount+'</span></p>';
							conHtml+='<p class="inst-2lp mb05"><span class="f14 color999 ">创建时间</span><span class="f14 color333 ">'+unix_to_fulltime_s(v.created_at)+'</span></p>';
							conHtml+='<p class="inst-2lp mb05"><span class="f14 color999 ">确定时间</span><span class="f14 color333 ">'+unix_to_fulltime_s(v.confirm_time)+'</span></p>';
							conHtml+='</div></div>';
							//尾款
							conHtml+='<div class="act-2"><div class="inst-2l ">';
							conHtml+='<p class="inst-2lp mb05"><span class="f14 color999 ">协议文本</span></p>';
							conHtml+='<div class="pct-2 mb1 fline" address="'+v.address+'"><div class="act-2l pact-text">';
							conHtml+='<img src="/images/agent/my_contract.png" class="pact-img mr1" />';
							conHtml+='<p class="pact-2lp over-text"><span class="over-text f14 b bold act-2lspan color333">'+v.brand+'加盟合同</span><br />';
//							conHtml+='<span class="over-text f10 act-2lspan color333 ">合同编号：'+v.contract_no+'</span><br />';
							conHtml+='</p></div>';
							conHtml+='<img src="/images/jump.png" class="pct-jump" brand_id="'+v.brand_id+'"/>';
							conHtml+='</div>';
							conHtml+='</div></div>';
							conHtml+='</div></div>';
							conHtml+='<div class="accept text-end pr1-5 pt1 f13 mb3"><p class="mb05"><span class="f13 color333  ">邀请人</span></p>';
							conHtml+='<p class="mb05"><span class="color333  f13">跟单经纪人：</span><span class="color333 b bold f13">'+v.agent_name+'</span></p>';
							conHtml+='<p class="mb05"><span class="color999  f13">邀请时间：</span><span class="color999  f13">'+unix_to_fulltime_s(v.created_at)+'</span></p>';
							conHtml+='<p class="mb05"><span class="color999  f13">确定时间：</span><span class="color999  f13">'+unix_to_fulltime_s(v.confirm_time)+'</span></p></div>';
							//已确定按钮
							conHtml += '<div class="pdyet-btn bgwhite f15">'
//							if(!is_out){
								if(v.status==2){
									if(v.is_score==0){
										conHtml+='<span class="appraise look cff5a00" brand_id="'+v.brand_id+'"'+'agent_id="'+v.agent_id+'">评价促单经纪人</span>';
									}else if(v.is_score==1){
										conHtml+='<span class="look_eval look cff5a00" brand_id="'+v.brand_id+'"'+'agent_id="'+v.agent_id+'">查看我的评价</span>';
									}
								};
								conHtml+='<span class="look look-order" order_no="'+v.order_no+'">查看我的订单</span></div>';
//							}
							
						};
						if(is_out){
								conHtml += '<div class="install_app install-app2" id="installapp"><a href="javascript:;" class="install_open" id="openapp">跳转到 APP</a></div>';
							}
						
	       				if(v.status==0){
							$('.pub-state').css('margin-top','1.5rem');
						};
       				});
       			}
       		}else{
				if(data.message.type=='contract_close'){
					$('.define').removeClass('none');
				};
				if(data.message.type=='brand_down'){
					$('.brand_down').removeClass('none');
				}
			};
				$('.containerBox').html(conHtml);
				if(isiOS){
					if (window.screen.height === 812) {
	//				    $('.head').css('top', '40px');
					    $('.pdyet-btn').css('bottom','17px');
					    $('.iphone_btn').removeClass('none');
					  }
					
				}
       })
	}
	getdetail(id);
	
	if(is_out){
		$('#installapp').removeClass('none');
		$(document).on('tap','#openapp,#loadapp,#App',function(){					
                var _height = $(document).height();
                $('.safari').css('height', _height);
                $('.safari').removeClass('none');                      
		});
		$(document).on('tap','.safari',function(){
				$('.safari').addClass('none');
		});
		if(is_weixin()){
			var wxurl = labUser.api_path + '/weixin/js-config';
			//微信二次分享
            var desptStr = removeHTMLTag(sel.description);
            var despt = cutString(desptStr, 60);
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
                        wx.onMenuShareTimeline({
                            title: sel.subject, // 分享标题
                            link: location.href, // 分享链接
                            imgUrl: sel.image	, // 分享图标
                            success: function () {
                                // 用户确认分享后执行的回调函数
                                if($('#share').data('reward')==1){
									sencondShare('relay')
                                }
                                
                            },
                            cancel: function () {
                                // 用户取消分享后执行的回调函数
                            }
                        });
                        wx.onMenuShareAppMessage({
                            title: sel.subject,
                            desc: despt,
                            link: location.href,
                            imgUrl: sel.image,
                            trigger: function (res) {
                                // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
                                console.log('用户点击发送给朋友');
                            },
                            success: function (res) {
                            	if($('#share').data('reward')==1){
									sencondShare('relay')
                                }
                            },
                            cancel: function (res) {
                                console.log('已取消');
                            },
                            fail: function (res) {
                                console.log(JSON.stringify(res));
                            }
                        });
                    });
                    // regsiterWX(selfObj.v_subject,selfObj.detail_img,location.href,selfObj.v_description,'','');
                }
            });
		}else {
            if (isiOS) {
                //打开本地app
                $(document).on('click', '#openapp', function () {
                    oppenIos();
                });
                /**下载app**/
                $(document).on('click', '#loadapp', function () {
                    window.location.href = 'https://itunes.apple.com/app/id981501194';
                });
                oppenIos();
            }
            else if (isAndroid) {
                $(document).on('click', '#loadapp', function () {
                    window.location.href = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.tyrbl.wujiesq';
                });
                $(document).on('click', '#openapp,#App', function () {
                   openAndroid();
               });
                openAndroid();
            }
            
        };
	};
	
//尾款补齐操作办法 
$(document).on('click','.wk_payment',function(){
	window.location.href = labUser.path +'webapp/agent/way/detail';
});	
	
//接受合同
function acceptContract(id) {
		if(isAndroid) {
			javascript: myObject.acceptContract(id);
		}
		else if(isiOS) {
			var data = {
				"id": id
			}
			window.webkit.messageHandlers.acceptContract.postMessage(data);
		}
};
//拒绝合同
function rejectContract(id) {
		if(isAndroid) {
			javascript: myObject.rejectContract(id);
		}
		else if(isiOS) {
			var data = {
				"id": id
			}
			window.webkit.messageHandlers.rejectContract.postMessage(data);
		}
};
//跳转合同文本
$(document).on('click','.pct-2',function(){
	var address = $(this).attr('address');
	window.location.href = labUser.path +'js/agent/generic/web/viewer.html?file='+address;
});
//点击接受
$(document).on('click','.sign',function(){
	acceptContract(id);
});
//点击拒绝
$(document).on('click','.to-pay',function(){
	rejectContract(id);
});
//评价促单经纪人 agent_id:经纪人id brand_id:品牌id contract_id:合同id
function comments(agent_id,brand_id,contract_id){
	if(isAndroid) {
			javascript: myObject.comments(agent_id,brand_id,contract_id);
		}
		else if(isiOS) {
			var data = {
				"agent_id": agent_id,
				"brand_id": brand_id,
				"contract_id":contract_id
			}
			window.webkit.messageHandlers.comments.postMessage(data);
		}
}
//点击评价促单经纪人
$(document).on('click','.appraise',function(){
	var agent_id = $(this).attr('agent_id');
	var brand_id = $(this).attr('brand_id');
	if (is_out) {
		tips('请在APP中打开！');
	} else{
		comments(agent_id,brand_id,id);
	}
	
});
//查看我的评价
function look_comment(agent_id,brand_id){
	if(isAndroid) {
			javascript: myObject.look_comment(agent_id,brand_id);
		}
		else if(isiOS) {
			var data = {
				"agent_id": agent_id,
				"brand_id": brand_id
			}
			window.webkit.messageHandlers.look_comment.postMessage(data);
		}
};
//查看我的订单
function checkMyorder(order_no){
	  if (isAndroid) {
	      javascript:myObject.checkMyorder(order_no);
	  }else if(isiOS){
	      var data={
	          'order_no':order_no,
	          'type':2
	          };
	  window.webkit.messageHandlers.checkMyorder.postMessage(data);
	  }
	  
};
 //点击查看我的评价
$(document).on('click','.look_eval',function(){
	var agent_id = $(this).attr('agent_id');
	var brand_id = $(this).attr('brand_id');
	if (is_out) {
		tips('请在APP中打开！');
	} else{
		look_comment(agent_id,brand_id);
	}
});

//查看我的订单
$(document).on('click','.look-order',function(){
	var order_nos = $(this).attr('order_no');
	if(is_out){
		tips('请在APP中打开！');
	}else {
		checkMyorder(order_nos);
	}
//	console.log(order_nos);
	
	
});
//查看付款情况
$(document).on('click','.look-pay',function(){
	window.location.href = labUser.path + 'webapp/client/payment/_v020800?id='+id;
});
//跳转合同页
$(document).on('click','.pct-jump',function(){
	var brand_id =  $(this).attr('brand_id');
});
function tips(e){
    $('.common_pops').text(e).removeClass('none');
    setTimeout(function() {
        $('.common_pops').addClass('none');
    }, 1500);
};
function unix_to_fulltime_s(unix) {
      var newDate = new Date();
      newDate.setTime(unix * 1000);
      var Y = newDate.getFullYear();
      var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
      var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
      var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
      var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
      var s = newDate.getSeconds() < 10 ? '0' + newDate.getSeconds() : newDate.getSeconds();
      return Y + '/' + M + '/' + D + '日' + ' ' + h + ':' + m + ':' +s;
};
function unix_to_mdhm(unix) {
    var newDate = new Date();
    newDate.setTime(unix * 1000);
    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
    var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
    var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
    return M + '月' + D + '日 ';
};
//打开本地--Android
function openAndroid(){
    var strPath = window.location.pathname;
    var strParam = window.location.search.replace(/is_out=1/g, '');
    var appurl = strPath + strParam;
    window.location.href = 'openwjsq://welcome' + appurl;
}
function oppenIos(){
    var strPath = window.location.pathname.substring(1);
    var strParam = window.location.search;
    var appurl = strPath + strParam;
    var share = '&is_out';
    var appurl2 = appurl.substring(0, appurl.indexOf(share));
    window.location.href = 'openwjsq://' + appurl2;
}
})