//zhangxm
Zepto(function(){
	new FastClick(document.body);
	var args=getQueryStringArgs(),
        id = args['contract_id'] || '0',  //合同id
//      uid = args['uid'] || '0',
		urlPath = window.location.href,
        origin_mark = args['share_mark'] || 0,//分销参数，分享页用
        code = args['code'] || 0;
	var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
	var is_out = urlPath.indexOf('is_out') > 0 ? true : false;
	var brandName='';
	function getdetail(id){
		var param = {}; 
		param['contract_id'] = id;
		if(is_out){
            param['guess']=1;
        };
       var url = labUser.api_path + '/contract/detail/_v020902';
       ajaxRequest(param,url,function(data){
       		var conHtml = '';
       		if (data.status) {
       			if(data.message!=''){
       				
       					var v = data.message;
       					brandName = v.brand_name;
       					
       					if(v.status==0){
       						
//     						<!--待确认  橙色背景-->
       						conHtml+='<div class="head c57c88d down-pay ">';
       						conHtml+='<div class="head_dis">';
       						conHtml+='<img src="/images/agent/time.png" class="mr1"/>';
       						conHtml+='<span class="f15 ">待确认'+v.contract_title+'加盟合同</span>';
       						conHtml+='</div>';
       						conHtml+='<img class="head-imgr " src="/images/agent/he.png">';
       						conHtml+='</div>';
       					}else {
//     						<!--拒绝 红色背景-->
       						conHtml+='<div class="head cfc5d5d down-pay ">';
       						conHtml+='<div class="head_dis">';
       						conHtml+='<img src="/images/agent/ico_delete3.png" class="mr1"/>';
       						if(v.realname!=''){
       							conHtml+='<span class="f15 ">投资人：'+v.realname+'已拒绝'+v.contract_title+'加盟合同</span>';
       						}else {
       							conHtml+='<span class="f15 ">投资人：'+v.nickname+'已拒绝'+v.contract_title+'加盟合同</span>';
       						};
       						conHtml+='</div>';
       						conHtml+='<img class="head-imgr " src="/images/agent/he.png">';
       						conHtml+='</div>';
       					};
//     					正文 公共说明
						conHtml+='<div class="pub-state bgwhite mt5"><p class=" mb1-5">';
						if(v.realname!=''){
							conHtml+='<span class=" f14 color333 b">To 无界商圈投资人&nbsp;</span><span class="b f14 cffa300">'+v.realname+'</span>';
						}else {
							conHtml+='<span class=" f14 color333 b">To 无界商圈投资人&nbsp;</span><span class="b f14 cffa300">'+v.nickname+'</span>';
						};
						if(v.gender==0){
							conHtml+='<span class="f14 color333 b">&nbsp;女士</span>';
						}else if(v.gender==1){
							conHtml+='<span class="f14 b color333">&nbsp;先生:</span>';
						}else {
							conHtml+='<span class="f14 color333 b ">:</span>';
						};
						conHtml+='</p>';
						conHtml+='<p class="pub-1">';
						conHtml+='<span class="f12 color666 ">通过经纪人</span><span class="cffa300 f12"> '+v.agent_name+'</span><span class="f12 color666 ">的对接，您是否对品牌已经有了加盟的想法？</span><br />';
						conHtml+='<span class="f12 color666 ">在这里，向您发出品牌加盟的橄榄枝，我们提供全网最优质的服务和最低的加盟费用，并为您提供相应的加盟扶持。</span>';
						conHtml+='</p>';
						conHtml+='<span class="logo-img"><img src="/images/agent/logopact_10.png"/></span>';
						conHtml+='</div>';
						//目标品牌
						conHtml+='<div class="choose mt1-2 bgwhite fline">';
						conHtml+='<p class="pt1-5 pl1-5 pr1-5"><span class="f15 color333">目标品牌</span>';
						conHtml+='<div class="brand_list">';
						conHtml+='<div class="chooseBrand">';
						conHtml+='<div class="brand">';
						conHtml+='<p class="brand_logo mr1"><img src="/images/act_banner.png"/></p>';
						conHtml+='<div class="brand_logo_p">';
						conHtml+='<span class="f14 color333 brand_name">'+v.brand_name+'</span>';
						conHtml+='<span class="f12 color999 brand_text">'+v.slogan+'</span>';
						conHtml+='<p><span class="f13 color666 l_h12">行业分类：</span><span class="f13 cffac00 l_h12">'+v.brand_cate+'</span></p>';
						conHtml+='</div>';
						conHtml+='</div>';
//						拒绝情况下展示图标
						if(v.status!=0){
							conHtml+='<img src="/images/reject.png" class="reject"/>';
						}
						conHtml+='</div>';
						conHtml+='</div>';
						conHtml+='</div>';
//						加盟方案
						conHtml+='<div class="choosePlan bgwhite">';
						conHtml+='<p class="pt1-5 pr1-5 pl1-5"><span class="f15 color333">加盟方案</span>';
						conHtml+='<div class="plan">';
						conHtml+='<div class="packageType mb1-5">';
						conHtml+='<p class="lh2-3"><span class="f12 color666">加盟方案</span><span class="f12 color666">'+v.contract_title+'</span></p>';
						conHtml+='<p class="lh2-3"><span class="f12 color666">加盟类型</span><span class="f12 color666">'+v.league_type+'</span></p>';
						conHtml+='<p class="lh2-3"><span class="f12 color666">总费用</span><span class="f12 cfd4d4d">¥'+v.amount+'</span></p>';
						conHtml+='</div>';
						conHtml+='<div class="planDetail  bgf2f2">';
						conHtml+='<div class="costDetail">';
						conHtml+='<p class="f11 color666">费用明细</p>';
						conHtml+='<p class="">';
						if(v.cost){
							$.each(v.cost, function(m,n) {
								conHtml+='<span class="f11 color999">'+n.cost_type+'：¥ '+n.cost_limit+'</span>';
							});
						}
						
						
						conHtml+='</p>';
						conHtml+='</div>';
						conHtml+='<div class="dis_bet mb2">';
						conHtml+='<span class="f11 color666">合同/文件</span>';
						conHtml+='<p class="textEnd">';
						conHtml+='<span class="f11 c2873ff pct-2" address="'+v.address+'">《品牌加盟付款协议》</span>';
						conHtml+='</p>';
						conHtml+='</div>';
						conHtml+='<p class="f10 color999 lh1-5">* 如款项存在修改幅度，请联系商务对其进行修改。</p>';
						conHtml+='<p class="f10 color999 lh1-5">* 加盟合同将安排线下签约，实际成交按款项交齐为准。</p>';
						conHtml+='<p class="f10 color999 lh1-5">* 佣金结算以可提成佣金部分乘以提成比例进行计算。</p>';
						conHtml+='<p class="f10 color999 lh1-5">*  对加盟方案存在疑问，请联系商圈客服人员。</p>';
						conHtml+='<p class="f10 color999 lh1-5">*  无界商圈保持最终解释权。</p>';
						conHtml+='</div>';
						conHtml+='</div>';
						conHtml+='</div>';
//						合同状态
//						待确认
						if(v.status==0){
							conHtml+='<div class="state_stay bgwhite pl1-33">';
							conHtml+='<p class="pt1-5 "><span class="f13 color666">电子合同状态</span></p>';
							conHtml+='<p class=" pl3"><span class="f15 color333">等待对方确认</span></p>';
							conHtml+='<p class=" pl3 pb1-33"><span class="f10 color999">有效期</span><span class="f10 cfd4d4d">'+v.leftover+'</span></p>';
							conHtml+='</div>';
						}else {
							//拒绝
							conHtml+='<div class="state_reject bgwhite pl1-33">';
							conHtml+='<p class="pt1-5 "><span class="f13 color666">电子合同状态</span></p>';
							conHtml+='<p class=" pl3"><span class="f15 color333">已拒绝</span></p>';
							conHtml+='<p class=" pl3 pb1-33"><span class="f12 cfd4d4d">理由：</span><span class="f12 cfd4d4d">'+v.remark+'</span></p>';
							conHtml+='</div>';
							
						}
						conHtml+='<div class="accept text-end pr1-5 pt1 mb3">';
						conHtml+='<p class="mb05"><span class="color333 f12">邀请人</span></p>';
						conHtml+='<p class="mb05"><span class="color333 f12">跟单经纪人：</span><span class="color333 f12">'+v.agent_name+'</span></p>';
						conHtml+='<p class="mb05"><span class="color999 f12">邀请时间：</span><span class="color999 f12">'+unix_to_fulltime_s(v.created_at)+'</span></p></div>';
						//待确认情况下展示底部按钮
						if(v.status==0){
							conHtml+='<div class="pd-btn bgwhite f15 pt05 pb05">';
							conHtml+='<span class="to-pay f16 color333">残忍拒绝</span>';
							conHtml+='<span class="sign color-white" returnMoney="'+v.total_packet+'" amount="'+v.amount+'">确定加盟</span></div>';
						}
       				
       			}
       		}else{
       			if(data.message.type=='contract_close'){
					$('.define').removeClass('none');
				}else if(data.message.type=='brand_down'){
					$('.brand_down').removeClass('none');
				}
       		}; 
       		$('.containerBox').html(conHtml);
			if(isiOS){
				if (window.screen.height === 812) {
//				    $('.head').css('top', '40px');
				    $('.pd-btn').css('bottom','17px');
				    $('.iphone_btn').removeClass('none');
				    $('.iphone_btn').css('background','#ffffff');
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
	
//接受合同    returnMoney:返现金额     amount:总费用             
function acceptContract(id,brandName,returnMoney,amount) {
		if(isAndroid) {
			javascript: myObject.acceptContract(id,brandName,returnMoney,amount);
		}
		else if(isiOS) {
			var data = {
				"id": id,
				"brandName":brandName,
				"returnMoney":returnMoney,
				"amount":amount
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
//确定加盟
$(document).on('click','.sign',function(){
	console.log(brandName);
	var returnMoney = $(this).attr('returnMoney');
	var amount = $(this).attr('amount');
	acceptContract(id,brandName,returnMoney,amount);
});
//残忍拒绝
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
// $(document).on('click','.look_eval',function(){
// 	var agent_id = $(this).attr('agent_id');
// 	var brand_id = $(this).attr('brand_id');
// 	if (is_out) {
// 		tips('请在APP中打开！');
// 	} else{
// 		look_comment(agent_id,brand_id);
// 	}
// });

// //查看我的订单
// $(document).on('click','.look-order',function(){
// 	var order_nos = $(this).attr('order_no');
// 	if(is_out){
// 		tips('请在APP中打开！');
// 	}else {
// 		checkMyorder(order_nos);
// 	}
// //	console.log(order_nos);
	
	
// });
// //查看付款情况
// $(document).on('click','.look-pay',function(){
// 	window.location.href = labUser.path + 'webapp/client/payment/_v020800?id='+id;
// });
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
      return Y + '/' + M + '/' + D + ' ' + h + ':' + m + ':' +s;
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
};
function unix_to_mdhm(unix) {
    var newDate = new Date();
    newDate.setTime(unix * 1000);
    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
    var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
    var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
    return D + '天 ' + h + '小时' + m + '分';
}
})