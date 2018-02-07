@extends('layouts.default')
@section('css')
<link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/education.css"/>
@stop
@section('main')
<section class="container ">
	<div class="head">
		<div class="w15 l" style='opacity:0'>
			<img src="{{URL::asset('/')}}/images/education/shangquan-logo.png" alt="">
		</div>
		<div class="w70 l ">
			<p class="title ">
				<img src="" alt="" id="sub_image">
			</p>
			<!-- <p class="stage tc color3 f16"><span style="letter-spacing:-1px">—</span>&nbsp;亲果&谢蟹浓&壹Q鲜&nbsp;<span style="letter-spacing:-1px">—</span></p> -->
		</div>
		<div class="erweima w15 l">
			<img src="{{URL::asset('/')}}/images/education/erweima.png" alt="二维码">
		</div>
	</div>
	<div class="main none">
		<p class="f14 b mes_account">
			<span id="add_message" class="ff5"></span>
			<br />
			<span class='ff5 mr_10' id='mescount'></span><span class="show_usercount">条信息</span><span class="mr_10 ff5 ml50 num_count" id="usercount"></span><span class="num_count">在线</span>
		</p>
		<div style="border-radius: 54px;overflow: hidden;">
			<div class="overscroll port-4">
				<!-- 留言页面 -->
				<div class="messages " id='messages' >
					
				</div>
				<!-- 成交信息页 -->
				<div class="orders text-desc">
					<div class="tc orderlist_bg" >
						<ul class="brand_order">
	
						</ul>
					</div>
					<div >
						<ul class="orderlists" id='orderlists'>
	
						</ul>
					</div>
				</div>
			</div>
		</div>
		

	</div>

	<div class="loading">
		<img src="{{URL::asset('/')}}/images/education/load.gif" alt="">
	</div>
	<div class="tab tc none">
		<span class="mes_pic is_mes_pic"></span>
		<span class="order_pic not_order_pic"></span>
	</div>
	<!-- 详情蒙层 -->
	<div class="module none">
		<div class="out ">
			<div class="out_mes none">
				<div class="inner">

				</div>
			</div>
			<div class="out_order none">
				<div class="inner">

				</div>

			</div>
			<div>
				<p class="close f14 b">
					×
				</p>
			</div>
		</div>
	</div>
	<!-- 图片蒙层 -->
	<!-- <div class="imgmodule">

	</div> -->
	<div id="outerdiv" class="bigimg_box none">
		<div id="innerdiv" style="position:absolute;">
			<img id="bigimg" style="border:5px solid #fff;" src="" />
		</div>
	</div>
	<input type="hidden" id='dataStore'>
	<audio id="myaudio1" src="{{URL::asset('/')}}/images/maleo.mp3" hidden="true" controls="controls"></audio>

</section>
@stop
@section('endjs')
<script type="text/javascript" src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
<script type='text/javascript'>Zepto(function() {

	var args = getQueryStringArgs(),
		id = args['id'] || '0',
		uid = args['uid'] || '0',
		urlPath = window.location.href;
	var dm_run_once = true;
	var do_run_once = true;
	var mesObj = $('.messages'),
		orderObj = $('.orders');
	var param = {
		"id": id,
		"uid": uid,
		"section": 0,
		"commentType": 'Live',
		"type": 'Live',
		"use": 'big_screen',
		"urlPath": window.location.href,
		"page": 1,
		"page_size": 15,
		"update": "new",
		"fecthSize": 5,
		"real_order_max_id": 0, //真订单最大id
		"sham_order_max_id": 0, //假订单最大id
		"fromId": 0,
		"case": 'mix',
		'with_anonymous': 0,
		'log_id': 0,
		'fetch_size': 0
	};
	//隐藏显示在线人数
	$('.num_count').hide();
	$('.show_usercount').on('click', function() {
		$('.num_count').show();
	});
	$('#mescount').on('click', function() {
		$('.num_count').hide();
	})
	//获取直播标题
	function getSubject(id) {
		var url = labUser.api_path + '/live/wall-info/_v020400';
		ajaxRequest({
			'live_id': id
		}, url, function(data) {
			if(data.status) {
				$('#sub_image').attr('src', data.message.image);
			}
		})
	};
	getSubject(id);
	// 操作评论详情
	$(document).on('click', '.close', function() {
		$('.module').addClass('none');
		$('.out').removeClass('hover')
		$('.out_mes').addClass('none');
		$('.out_order').addClass('none');
		freshAnimate();
	})

function yeardatetime(unix) {
    var newDate = new Date();
    newDate.setTime(unix * 1000);
    var Y = newDate.getFullYear();
    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
    var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
    var m = newDate.getMinutes() < 10 ? ('0' + newDate.getMinutes()) : newDate.getMinutes();
    var s = newDate.getSeconds() < 10 ? ('0' + newDate.getSeconds()): newDate.getSeconds();
    return Y + '-' + M + '-' + D + ' ' + h + ':' + m + ':' + s;
}

	//留言列表
	function getFreshList(param) {
		var params = {};
		params['type'] = param.type;
		params['use'] = param.use;
		params['uid'] = param.uid;
		params['id'] = param.id;
		params['fromId'] = param.fromId;
		params['update'] = param.update;
		params['fecthSize'] = param.fecthSize;
		var url = labUser.api_path + '/comment/fresh-lists';
		ajaxRequest(params, url, function(data) {
			if(data.status) {
				var resObj = data.message;
				var mesHtml = '';

				if(resObj.data.length > 0) {
					$.each(resObj.data, function(index, item) {
						var newDate = yeardatetime(item.created_at_time);
						//动态添加留言
						mesHtml += '<div class="mes freshmes"><img class="mes_head l "  src="' + item.avatar + '" alt="head">';
						mesHtml += '<div class="mes_middle l"><p class="mes_name ff5 b f12">' + item.c_nickname + '&nbsp;&nbsp;&nbsp;&nbsp; ' + newDate + '</p>';
						mesHtml += '<p class="mes_detail f12 ">' + item.content + '</p>';
						if(item.images.length > 0) {
							var imgHtml = '';
							$.each(item.images, function(i, j) {
								imgHtml += '<img class="pimg" src="' + j + '" alt="">';
							})
							mesHtml += '<p class="img_intro">' + imgHtml + '</p>';
						}
						if(item.zone_name) {
							mesHtml += '</div><span class="color999 f12 l location">' + item.zone_name + '</span></div>';
						} else {
							mesHtml += '</div></div>';
						}
					});
					$('#messages').append(mesHtml);
//					if($('#messages .mes').length < 5) {
//						$('#messages').append(mesHtml);
//					} else {
////						$('#messages .mes').eq(4).after(mesHtml);
//					};

				};

				$('#dataStore').attr("data-fromid", resObj.max_id);
				$('#dataStore').attr('data-mescount', resObj.all_count);

				var mescount = parseInt($('#dataStore').attr('data-mescount') || 0);
				var ordercount = parseInt($('#dataStore').attr('data-ordercount') || 0);
				var total = mescount + ordercount;
				//留言数
				if(total > 9999) {
					$('#mescount').html(9999 + '<em>+</em>');
				} else {
					$('#mescount').html(total);
				}
				if(dm_run_once) {
					freshAnimate();
					dm_run_once = false;
				}
				$('.main').removeClass('none');
				$('.loading').addClass('none');
			}
		});
	};
	//动态刷新留言列表
	function freshAnimate() {
		var freLength = $('.overscroll .mes');
		var height = 20;
		if(freLength.length > 4) {
			
			looper_fresh = setInterval(function() {
				
				if($('.overscroll .mes').length>5){
					var firstTag = $('#messages');
//					var height = firstTag.height();
					height = height - 87;
//					firstTag.animate({
//						'top': height,
////						'opacity': 0,
////						'margin-bottom': 0,
////						'padding': 0
//					}, 500, function() {
////						$('#messages .mes').first().remove();
////						$('#messages').find('.mes:last').css({
////							'height': height + 12 + 'px',
////							'opacity': 1,
////							'margin-bottom': 25 + 'px',
////							'padding': 5 + 'px'
////						});
//					
//				});
					$(".overscroll").scrollTop($(".overscroll")[0].scrollHeight);
				}
			}, 3000);
			
			console.log($('.overscroll .mes').length)
//			if($(".overscroll").scrollTop() == $(".overscroll")[0].offsetHeight - $(".overscroll")[0].clientHeight ){
//				clearInterval(looper_fresh);
//			}
//			console.log($(".overscroll").scrollTop(),$(".overscroll")[0].offsetHeight - $(".overscroll")[0].clientHeight)
		}
	};
	if($('.overscroll .mes').length>5){
		clearInterval(looper_fresh);
	}
	//订单列表
	function getOrderlist(param) {
		var params = {};
		params['live_id'] = param.id;
		params['sham_order_max_id'] = param.sham_order_max_id;
		params['real_order_max_id'] = param.real_order_max_id;
		params['type'] = param.case;
		var url = labUser.api_path + '/live/order-list';
		ajaxRequest(params, url, function(data) {

			if(data.status) {
				var returnObj = data.message;
				$('#dataStore').attr("data-realid", returnObj.real_order_max_id);
				$('#dataStore').attr("data-shamid", returnObj.sham_order_max_id);
				$('#dataStore').attr('data-ordercount', returnObj.all_count);
				//显示在线人数
				if(data.message.online_count > 9999) {
					$('#usercount').html(9999 + '<em>+</em>');
				} else {
					$('#usercount').html(data.message.online_count);
				}
				//订单详细信息
				if(returnObj.orders_dynamic.length > 0) {
					var orderHtml = '',
						orderlistHtml = '';
					$.each(returnObj.orders_dynamic, function(index, item) {
						var newDate = yeardatetime(item.created_at);
						//留言页展示下单动态
						orderHtml += '<div class="mes ordermes"  data_src="' + item.brand_logo + '" data_count="' + item.goods_count + '" data_zone="' + item.zone_name + '" data_name="' + item.realname + '"  data_brand="' + item.title + '"  data_produ="' + item.brand_goods_title + '" data_newData="' + newDate + '"><img class="mes_head l" src="{{URL::asset('/')}}/images/education/wujie_logo.png" alt="head">';
						orderHtml += '<div class="mes_middle l"><p class="mes_name b f12">' + '无界商圈' + '</p>';
						orderHtml += '<p class="mes_detail f12 ">恭喜' + item.zone_name + '市会员' + item.realname + '成功下单' + item.brand_goods_title + '！&nbsp;&nbsp;&nbsp;&nbsp; ' + newDate + '</p>';
						orderHtml += '</div></div>';
						//订单页展示订单信息
						orderlistHtml += '<li data_src="' + item.brand_logo + '" data_count="' + item.goods_count + '" data_zone="' + item.zone_name + '" data_name="' + item.realname + '"  data_brand="' + item.title + '"  data_produ="' + item.brand_goods_title + '" data_newData="' + newDate + '"><img src="{{URL::asset('/')}}/images/education/dui.png" alt="" >';
						orderlistHtml += '<span class="address">' + item.zone_name + '</span> <span class="orderName b">' + item.realname + '</span>';
						orderlistHtml += '<span class="orderTel ">' + item.mobile + '</span>';
						orderlistHtml += '<span class="brandName">' + item.title + '</span><span class="orderIntro">' + item.brand_goods_title + ' &nbsp;&nbsp;' + newDate + '</span></li>'
					});
					$('#messages').append(orderHtml);
//					if($('#messages .mes').length < 5) {
//						$('#messages').append(orderHtml);
//					} else {
//						$('#messages .mes').eq(4).after(orderHtml);
//					};
					$('#orderlists').append(orderlistHtml);
				}
				if(do_run_once) {
					orderAnimate();
					do_run_once = false;
				}
			}
		});
	}
	//订单页品牌展示
	function showLogo() {
		var params = {};
		params['live_id'] = param.id;
		params['sham_order_max_id'] = param.sham_order_max_id;
		params['real_order_max_id'] = param.real_order_max_id;
		params['type'] = param.case;
		var url = labUser.api_path + '/live/goodsdetail/_v020500';
		ajaxRequest(params, url, function(data) {
			if(data.status) {
				var logoHtml = '';
				if(data.message.length > 0) {
					$.each(data.message, function(i, j) {
						logoHtml += '<li><img src="' + j.brand_logo + '"  class="logo_pic"><p class="f16 b mt1">' + j.brand_name + '</p>';
						logoHtml += '<p class="brandIntro">包含<span class="b">' + j.count + '</span>个加盟套餐</p></li>'
					});
					$('.tab').removeClass('none');
				} else {
					$('.tab').addClass('none');
					$('.text-desc').addClass('none');
				}
				$('.brand_order').append(logoHtml);
			}
		});
	}

	//页签切换
	$(document).on('click', '.mes_pic', function() {
		$(this).addClass('is_mes_pic').removeClass('not_mes_pic');
		$('.order_pic').removeClass('is_order_pic').addClass('not_order_pic');
		mesObj.removeClass('hover');
		orderObj.removeClass('hover');
	});
	$(document).on('click', '.order_pic', function() {
		$(this).addClass('is_order_pic').removeClass('not_order_pic');
		$('.mes_pic').removeClass('is_mes_pic').addClass('not_mes_pic');
		mesObj.addClass('hover');
		orderObj.addClass('hover');
	});

	//订单滚动效果
	function orderAnimate() {
		var orderLength = $('#orderlists >li').length;
		if(orderLength > 2) {
			var looper_order = setInterval(function() {
				if(orderLength > 2){
					var firstTag = $('#orderlists').find('li:first');
					var height = firstTag.height();
					firstTag.animate({
						'height': 0,
						'opacity': 0
					}, 300, function() {
						$('#orderlists li').first().remove();
						$('#orderlists').find('li:last').css({
							'height': height,
							'opacity': 1
						});
					});
				}
				
			}, 3000);
		}

	};

	//初始加载
	getOrderlist(param);
	getFreshList(param);
	showLogo(param);

	//鼠标进入停止更新留言
	$('.overscroll').mouseenter(function() {
		var freLength = $('#messages .mes').length;
		if(freLength > 4) {
			clearInterval(looper_fresh);
		}
	});
	$('.overscroll').mouseleave(function() {
		var freLength = $('#messages .mes').length;
		if(freLength > 4) {
			clearInterval(looper_fresh);
		}
		if($('.module').hasClass('none') && $('#outerdiv').hasClass('none')) {
			freshAnimate();
		}
		//$('#messages').animate({'scrollTop':0},500);

	});
	//点击查看详情
	//留言详情
	$(document).on('click', '.freshmes', function() {
		var img = $(this).children('.mes_head').attr('src'),
			user_name = $(this).find('.mes_name').text(),
			loc = $(this).find('.location').text(),
			content = $(this).find('.mes_detail').text(),
			imgElem = $(this).find('.img_intro').html();
		var mesHtml = '';
		mesHtml += '<img class="mes_head l" src="' + img + '" alt="">';
		mesHtml += '<div class="l ml1"><p class="ff5 f12 mb5">' + user_name + '</p>';
		if(loc) {
			mesHtml += '<p class="mb5"><img class="loc" src="{{URL::asset('/')}}/images/education/location.png" alt=""><span class="color999 f14">' + loc + '</span></p>'
		}
		mesHtml += '<p class="f12 color666 mb5 f14_font">' + content + '</p>'
		if(imgElem) {
			mesHtml += '<p class="img_intro mb5">' + imgElem + '</p></div>'
		} else {
			mesHtml += '</div>'
		}
		$('.out_mes .inner').html(mesHtml);
		$('.module').removeClass('none');
		$('.out_mes').removeClass('none');

		var freLength = $('#messages .mes').length;
		if(freLength > 4) {
			clearInterval(looper_fresh);
		}
	})
	//留言页查看订单详情
	$(document).on('click', '.ordermes,#orderlists li', function() {

		var freLength = $('#messages .mes').length;
		if(freLength > 4) {
			clearInterval(looper_fresh);
		}
		playMaleo();
		var logo = $(this).attr('data_src'),
			zone_name = $(this).attr('data_zone'),
			name = $(this).attr('data_name'),
			brand = $(this).attr('data_brand'),
			count = $(this).attr('data_count'),
			produ = $(this).attr('data_produ');
		newData = $(this).attr('data_newData');
		var orderdetail = '';
		orderdetail += '<img class="mes_head l" src="{{URL::asset('/')}}/images/education/wujie_logo.png" alt="">';
		orderdetail += '<div class="l ml1"><p class="ff5 f12 mb5 f14_font">' + '无界商圈' + '</p>';
		orderdetail += '<p class="f14 color666 mb5 f14_font">恭喜' + zone_name + '市会员<span class="b color3">' + name + '</span>，成功下单' + produ + '！<br/>' + newData + '</p>';
		orderdetail += '<p class="brand_logo"><img src="' + logo + '"></p>'
		orderdetail += '<p class="f14 b tc ">' + brand + '</p><p class="tc color666 f14 brand_title">包含<span class="b">' + count + '</span>个加盟套餐</p>'
		orderdetail += '<img class="caidai" src="{{URL::asset('/')}}/images/education/caidai.png" >'
		$('.out_order .inner').html(orderdetail);
		$('.module').removeClass('none');
		$('.out_order').removeClass('none');

	})
	// 定时刷新
	setInterval(function() {
		param.fromId = $('#dataStore').attr("data-fromid");
		param.sham_order_max_id = $('#dataStore').attr("data-shamid");
		param.real_order_max_id = $('#dataStore').attr("data-realid");
		// param.log_id=$('#dataStore').attr("data-max_log_id");
		getOrderlist(param);
		getFreshList(param);
//		freshAnimate();
//		orderAnimate();
		// getUserlist(param);
	}, 5000)

	//点击查看大图
	function imgShow(outerdiv, innerdiv, bigimg, _this) {
		var src = _this.attr("src"); //获取当前点击的pimg元素中的src属性
		$(bigimg).attr("src", src); //设置#bigimg元素的src属性

		/*获取当前点击图片的真实大小，并显示弹出层及大图*/
		$("<img/>").attr("src", src).load(function() {
			var windowW = $(window).width(); //获取当前窗口宽度
			var windowH = $(window).height(); //获取当前窗口高度
			var realWidth = this.width; //获取图片真实宽度
			var realHeight = this.height; //获取图片真实高度
			var imgWidth, imgHeight;
			var scale = 0.8; //缩放尺寸，当图片真实宽度和高度大于窗口宽度和高度时进行缩放

			if(realHeight > windowH * scale) { //判断图片高度
				imgHeight = windowH * scale; //如大于窗口高度，图片高度进行缩放
				imgWidth = imgHeight / realHeight * realWidth; //等比例缩放宽度
				if(imgWidth > windowW * scale) { //如宽度扔大于窗口宽度
					imgWidth = windowW * scale; //再对宽度进行缩放
				}
			} else if(realWidth > windowW * scale) { //如图片高度合适，判断图片宽度
				imgWidth = windowW * scale; //如大于窗口宽度，图片宽度进行缩放
				imgHeight = imgWidth / realWidth * realHeight; //等比例缩放高度
			} else { //如果图片真实高度和宽度都符合要求，高宽不变
				imgWidth = realWidth;
				imgHeight = realHeight;
			}
			$(bigimg).css("width", imgWidth); //以最终的宽度对图片缩放

			var w = (windowW - imgWidth) / 2; //计算图片与窗口左边距
			var h = (windowH - imgHeight) / 2; //计算图片与窗口上边距
			$(innerdiv).css({
				"top": h,
				"left": w
			}); //设置#innerdiv的top和left属性
			$(outerdiv).fadeIn("fast"); //淡入显示#outerdiv及.pimg
			$(outerdiv).removeClass("none"); //淡入显示#outerdiv及.pimg
		});
		clearInterval(looper_fresh);

	}

	$(document).on('click', '.pimg', function(e) {
		e.stopPropagation();
		var _this = $(this); //将当前的pimg元素作为_this传入函数
		imgShow("#outerdiv", "#innerdiv", "#bigimg", _this);
	});
	$(document).on('click', '#outerdiv', function() { //再次点击淡出消失弹出层
		$(this).fadeOut("fast");
		$(this).addClass('none');
		if($('.module').hasClass('none')) {
			freshAnimate();
		}

	});
	//订单音频播放
	function playMaleo() {
		var myAuto_win = document.getElementById('myaudio1');
		myAuto_win.play();
	}

})</script>
@stop