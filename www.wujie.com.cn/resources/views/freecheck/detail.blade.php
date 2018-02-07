@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/freecheck.css" rel="stylesheet" type="text/css"/>
@stop

@section('main')
    <section id="container" class="">
    	<div class="intro">
			<img  alt="活动海报" class="l img" id="img">
			<p class="eventName l" id="eventName">活动名称</p>		
		</div>
		<div class="clearfix"></div>
		<div class="event time">
			<p id="begin_time">4/17 13:00</p>
			<p>活动开始时间</p>
		</div>
		<div class="event ticket">
			<p>普通票 <span class="free">免费</span></p>
			<p>现场参与</p>
		</div>
		<div class="bg">
		</div>
		<form action="">
		<div class="detail">
			<p class="head">完善与会人信息</p>
			<p >
				<label for=""  class="w3">姓<span></span>名：</label><input type="text"  name=nickname placeholder="请输入姓名">
			</p>
			<p >
				<label for="">手机号：</label><input type="text" name="phonenum" placeholder="请输入手机号">
			</p>
			<p>
				<label for="" class="w3">验证码：</label><input type="text" name="identify" placeholder="请输入验证码">
				<button type='button' id='btn' >获取验证码</button>
			</p>
			<p>
				<label for="" class="w3">公<span></span>司：</label><input type="text" name="company" placeholder="请输入所在公司">
			</p>
			<p>
				<label for="" class="w3">职<span></span>位：</label><input type="text" name="job" placeholder="请输入所在公司职位">
			</p>
			
		</div>

		<!-- <p class="f12 color999" style="background-color: #f2f2f2;padding:1.2rem">*个人信息仅用于活动报名，不会对外泄露</p> -->
		<div class="bg">
		</div>
		<div class="address" id="address">
			<p class="head">选择参会场地</p>
			<div>
				<span class="l checked" ></span>
				<p class="center" id="center1">杭州OVO路演中心</p>
				<p>浙江杭州下城区体育场路浙江国际大酒店11F</p>
				<p>0571-1234567</p>
			</div>
		</div>
		<div class="big-bg">
		</div>
		<!-- <p class="f12" style="margin-bottom: 9rem;background-color: #f2f2f2;color: #999;padding:1.2rem">
			* 如没有合适的参会地点，您可以前往无界商圈应用端，订阅该场活动直
		播不到现场，也可以通过无界商圈直播服务参与活动，体验跨地精彩互动
		！提交订单，确认报名  <a href="javascript:;" id="loadapp" style="color: #6BC24B">点击下载无界商圈应用</a>
		</p> -->
		<button class="fix" type="button" id="enroll">已确认订单，立即报名</button>
		</form>
		<div class="alert hide">
        	<p></p>
    	</div>
    </section>
@stop

@section('endjs')
 <script>
    var $body = $('body');
    document.title = "确认订单";
    // hack在微信等webview中无法修改document.title的情况
    var $iframe = $('<iframe ></iframe>').on('load', function() {
    setTimeout(function() {
    $iframe.off('load').remove()
    }, 0)
    }).appendTo($body)
</script> 
<script> 
  Zepto(function () {
  	   //是否分享页面，以及点击下载app
  	    //是否在分享页面
	    var urlPath = window.location.href;
	    var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;   
	    function share(is_flag) {
	    	if (is_flag) {
                    // $('#loadAppBtn').removeClass('none');
                    // $('#installapp').removeClass('none');
                    //浏览器判断
                    if (is_weixin()) {
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
                        //活动详情描述
                        var desptStr = removeHTMLTag(selfObj.description);
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
                                        title: selfObj.subject, // 分享标题
                                        link: location.href, // 分享链接
                                        imgUrl: selfObj.detail_img, // 分享图标
                                        success: function () {
                                            // 用户确认分享后执行的回调函数
                                        },
                                        cancel: function () {
                                            // 用户取消分享后执行的回调函数
                                        }
                                    });
                                    wx.onMenuShareAppMessage({
                                        title: selfObj.subject,
                                        desc: despt,
                                        link: location.href,
                                        imgUrl: selfObj.detail_img,
                                        trigger: function (res) {
                                            // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
                                            console.log('用户点击发送给朋友');
                                        },
                                        success: function (res) {
                                            console.log('已分享');
                                        },
                                        cancel: function (res) {
                                            console.log('已取消');
                                        },
                                        fail: function (res) {
                                            console.log(JSON.stringify(res));
                                        }
                                    });
                                });
                            }
                        });
                    }
                    else {
                        if (isiOS) {
                            //打开本地app
                            $(document).on('tap', '#openapp', function () {
                                //var strPath = window.location.pathname.substring(1);
                                //var strParam = window.location.search;
                                //var appurl = strPath + strParam;
                                //var share = '&is_share';
                                //var appurl2 = appurl.substring(0, appurl.indexOf(share));
                               window.location.href = 'openwjsq://' + 'webapp/activity/detail?pagetag=02-2&uid=0&makerid=0&id='+act_id;
                            });
                            /**下载app**/
                            $(document).on('tap', '#loadapp', function () {
                                window.location.href = 'https://itunes.apple.com/app/id981501194';
                            });
                        }
                        else if (isAndroid) {
                            $(document).on('tap', '#loadapp', function () {
                                window.location.href = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.tyrbl.wujiesq';
                            });
                            $(document).on('tap', '#openapp', function () {
                                window.location.href = 'openwjsq://welcome' + '/webapp/activity/detail?pagetag=02-2&uid=0&makerid=0&id='+act_id;
                            });
                        }
                    }
            }
	    }
	    share(shareFlag);
  	
	   // 活动内容加载
		var activity_id={{$id}},
			ticket_id={{$ticket_id}};
			 function freeActivity(activity_id) {
                    var param = {};
                    param["id"] = activity_id;
                    var url = labUser.api_path + '/activity/detail';
                    ajaxRequest(param, url, function (data) {
                        if (data.status) {
                          activityHtml(data.message.self);
                        }
                    });
                };

              function getAddress(activity_id){
              	var param={};
              		param['activity_id']=activity_id;
              		var url=labUser.api_path+'/activity/makers';
              	//	var url='/api/activity/makers';
              		ajaxRequest(param,url,function(data){
              			if(data.status){
              				addressHtml(data.message);
              				console.log(data.message.length);
              			}
              		});
              };
        //获取活动介绍
              function activityHtml(obj){
              		$("#img").attr('src',obj.detail_img);  //图片预览
              		$("#eventName").html(obj.subject);		//活动标题
              		var begin_time=unix_to_datetime(obj.begin_time);
              		$("#begin_time").html(begin_time);  //活动开始时间
              };
       //获取举办地城市
              function addressHtml(obj){
              		var Html='<p class="head">选择参会场地</p>';
              		$.each(obj,function(index,item){
              			Html+='<div><img class="gomap" src="{{URL::asset('/')}}/images/go_map_03.png"><span class="l" data-id="'
              				+obj[index].maker_id+'"></span>';
              			Html+='<i class="choose_add"><p class="center" >'+obj[index].subject+'</p>';
              			Html+='<p>'+obj[index].address+'</p>';
              			Html+='<p>'+obj[index].tel+'</p></i></div>';
              		});
              // 模拟地址单选按钮
              		$("#address").html(Html);
              		$(".address div span").each(function(){
						$(this).addClass("uncheckimg");
						$('.address div span').eq(0).removeClass("uncheckimg").addClass("checkedimg");	
					});
              		$(".address div span").on("click",function(){
						$(".address div span").addClass("uncheckimg").removeClass('checkedimg');
						$(this).removeClass("uncheckimg").addClass("checkedimg");
						console.log($(this).attr('data-id'));
					});
					$(".choose_add").on('tap',function(){
						$(".address div span").addClass("uncheckimg").removeClass('checkedimg');
						$(this).siblings('span').removeClass('uncheckimg').addClass('checkedimg');
					})
              };

              freeActivity(activity_id);
              getAddress(activity_id);

         //短信验证码计时器
			    var tt;
			    var wait = 60;
			    function time(o) {
			        if (wait == 0) {
			            o.removeAttr("disabled");
			            o.html("重新发送");
			            o.css({
			              "background":"#1E8CD4"
			            });
			            wait = 60;
			        } else {
			            o.attr("disabled", true);
			            o.css({
			              "background":"#C2C2C2"
			            });
			            o.html('重新发送(' + wait + 's)');
			            wait--;
			            tt = setTimeout(function () {
			                    time(o)
			                },
			                1000)
			        }
			    };

		//获取验证码
			  function get_identify(username,type){
			  		var param={'username':username,'type':type};
			  		var url=labUser.api_path+'/identify/sendcode';
			  		ajaxRequest(param,url,function(data){
			  			 if(!data.status){	
			  			 	$('.alert p').html(data.message);
							errorshow();
			  			 }else{
			  			 	var identify_code=$('#btn');
			  			 	time(identify_code);
			  			 }
			  		});
			  }; 
		//验证验证码
			  function check_identify (code,username,type){
			  		var param={};
			  			param['username']=username;
			  			param['code']=code;
			  			param['type']=type;
			  		var url=labUser.api_path+'/identify/checkidentify';
			  		ajaxRequest(param,url,function(data){
			  			if(!data.status){
			  				$('.alert p').html(data.message);
							errorshow();
			  			}else{
			  				var nickname=$('input[name="nickname"]').val(),
								phonenum=$('input[name="phonenum"]').val(),
								identify=$('input[name="identify"]').val(),
								company=$('input[name="company"]').val(),
								job=$('input[name="job"]').val();
				
							var	maker_id=$('.checkedimg').attr('data-id'),
								product=$('#eventName').text(),
								body=product;
								if(!maker_id){
									$('.alert p').html("请选择参会地点");
									errorshow();
								}
								console.log(nickname);console.log(phonenum);console.log(identify);
								console.log(company);console.log(job);console.log(maker_id);
								console.log(product);console.log(body);
			  				enroll(0,activity_id,company,job,ticket_id,maker_id,0,product,body,'none',nickname,phonenum,'html5',identify);
			  			}
			  		});
			  }; 
			  $('#btn').click(function(e){
			  	
			  	 var phonenum=$('input[name="phonenum"]').val();
			  	 	 get_identify(phonenum,'authorize');
			  	 	 e.stopPropagation();

			  });

			  function enroll(uid,activity_id,company,job,ticket_id,maker_id,cost,product,body,pay_way,name,tel,path,code){
			  		var param={};
			  		param['uid']=uid;
			  		param['activity_id']=activity_id;
			  		param['company']=company;
			  		param['job']=job;
			  		param['ticket_id']=ticket_id;
			  		param['maker_id']=maker_id;
			  		param['cost']=cost;
			  		param['product']=product;
			  		param['body']=body;
			  		param['pay_way']=pay_way;
			  		param['name']=name;
			  		param['tel']=tel;
			  		param['path']=path;
			  		param['code']=code;
			  		var url=labUser.api_path+'/activity/apply-no-pay';
			  		ajaxRequest(param,url,function(data){
			  			if(data.status){
			  				var activity_id={{$id}},
			  					maker_id=$('.checkedimg').attr('data-id');
			  					register=data.message.is_register;
			 				window.location.href=labUser.path +'/webapp/ticket/applysuccess?id='+activity_id +'&maker_id='+maker_id+'&register='+register+'&is_share=1';
			  				console.log(data.message);
			  			}else{
			  				$('.alert p').html(data.message);
							errorshow();							
			  			}
			  			
			  		})
			  }
		//跳转地图
			$(document).on('tap','.gomap',function(){
				var maker_id=$(this).siblings('span').attr('data-id');
				window.location.href=labUser.path+'/webapp/activity/bmap?id='+activity_id+'&maker_id='+maker_id;
			})
		//个人信息完善
			//var name_reg=/^[\u4e00-\u9fff\w]{2,16}$/;//2-16位汉子字母数字下划线组合
			var phone_reg=/^1[3578]\d{9}$/; 
			$(document).on('tap','#enroll',function(){

				var nickname=$('input[name="nickname"]').val(),
					phonenum=$('input[name="phonenum"]').val(),
					identify=$('input[name="identify"]').val(),
					company=$('input[name="company"]').val(),
					job=$('input[name="job"]').val();
				var	
					maker_id=$('.checkedimg').attr('data-id'),
					product=$('#eventName').text(),
					body=product;
					console.log(maker_id);

				if(nickname==''){
					$('.alert p').html('姓名不能为空');
					errorshow();
					return false;
				};
				if(phonenum==''){
					$('.alert p').html('手机号码不能为空！');
					errorshow();
					return false;
				};
				if(!phone_reg.test(phonenum)){
					$('.alert p').html('手机号格式不对！');
					errorshow();
					return false;
				};
				if(identify==''){
					$('.alert p').html('验证码不能为空！');
					errorshow();
					return false;
				};
				// if(company==''){
				// 	$('.alert p').html('请填写所在公司！');
				// 	errorshow();
				// 	return false;
				// };
				// if(job==''){
				// 	$('.alert p').html('请填写所在职位！');
				// 	errorshow();
				// 	return false;
				// };
				check_identify(identify,phonenum,'authorize');
				

			});

		//获取焦点时，改变按钮状态
				$('.detail p input').focus(function(){
					$('#enroll').css('position','static');
					$('.big-bg').css('height','2rem');
					console.log('获取焦点')
				});
				$('.detail p input').blur(function(){
					$('#enroll').css('position','fixed');
					$('.big-bg').css('height','6.8rem');				
					
				});
		// 增加热区
		$('.head').siblings('p').click(function(){
			$(this).children('input').focus();
		})
		//错误提示
   	 		function errorshow(){
       			 $(".alert").css("display","block");
       			 setTimeout(function(){$(".alert").css("display","none")},2000);
   			}; 
		});
</script>
@stop