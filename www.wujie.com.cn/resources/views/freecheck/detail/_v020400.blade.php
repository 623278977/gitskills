@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/freecheck.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/intlTelInput.css" rel="stylesheet" type="text/css"/>
@stop

@section('main')
    <section id="container" class="none">
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
		
		<form action="">
		<div class="detail">
			<p class="head">完善与会人信息</p>
			<p >
				<label for=""  class="w3">姓<span></span>名：</label><input type="text"  name=nickname placeholder="请输入姓名">
			</p>
			<!-- <p class="countrys">
				<label for="">区<span></span>号：</label>
				<input name="zonenum"  id="zone" type="text" >
			</p> -->
			<p>
				<label for="">手机号：</label><input type="text" name="phonenum" placeholder="请输入手机号码" id="zone" value='+86 '>
			</p>
			<!-- <p>
				<label for="" class="w3">验证码：</label><input type="text" name="identify" placeholder="请输入验证码">
				<button type='button' id='btn' >获取验证码</button>
			</p> -->
			<!-- <p>
				<label for="" class="w3">公<span></span>司：</label><input type="text" name="company" placeholder="请输入所在公司">
			</p>
			<p>
				<label for="" class="w3">职<span></span>位：</label><input type="text" name="job" placeholder="请输入所在公司职位">
			</p> -->
			
		</div>

		<p class="f12 color999" style="background-color: #f2f2f2;padding:1.2rem">*个人信息仅用于活动报名，不会对外泄露</p>
		
		<div class="address" id="address">
			<p class="head">选择参会场地</p>
			<div>
				<span class="l checked" ></span>
				<p class="center" id="center1">杭州OVO路演中心</p>
				<p>浙江杭州下城区体育场路浙江国际大酒店11F</p>
				<p>0571-1234567</p>
			</div>
		</div>
		<div class="clearfix"></div>
		<!-- <div class="big-bg">
		</div> -->
		<p class="f12" style="margin-bottom: 9rem;background-color: #f2f2f2;color: #999;padding:1.2rem">
			* 如没有合适的参会地点，您可以前往无界商圈应用端，订阅该场活动直播不到现场，也可以通过无界商圈直播服务参与活动，体验跨地精彩互动
		！提交订单，确认报名  <a href="javascript:;" id="loadapp" style="color: #6BC24B">点击下载无界商圈应用</a>
		</p>
		<button class="fix" type="button" id="enroll">已确认订单，立即报名</button>
		</form>
		<div class="alert hide">
        	<p></p>
    	</div>
    </section>
@stop

@section('endjs')
<script src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
<script src="{{URL::asset('/')}}/js/intlTelInput.js"></script>
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
  	    new FastClick(document.body);
        var args= getQueryStringArgs();
  	    var activity_id=args['id'];
  	    var ticket_id = args['ticket_id'];
  	    var sharemark = args['share_mark'];
  	    var code = args['code'];
	    var urlPath = window.location.href;
	    var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false; 
	  	$('#zone').intlTelInput();
	  	// $(document).on('tap','.country',function(){
	  	// 	$('#zone').val($(this).data('dial-code'));
	  	// })

	  // 判断客户端
	    if(!is_weixin()&&!isiOS&&!isAndroid){
	    	$('#container').css({'width':'740px','margin':'auto'});
	    	$('.fix').css('width','740px');
	    	$('#loadapp').remove();
	    	$(document).on('click','#enroll',function(){
				var nickname=$('input[name="nickname"]').val(),
					phonenum=$('input[name="phonenum"]').val().split(' ')[1],
					identify=$('input[name="identify"]').val(),
					sharemark=$('#container').data('sharemark');
					nation_code = $('input[name="phonenum"]').val().split(' ')[0];
				
				var	
					maker_id=$('.checkedimg').attr('data-id'),
					product=$('#eventName').text(),
					body=product;

				if(nickname==''){
					$('.alert p').html('姓名不能为空');
					errorshow();
					return false;
				};
				if(nation_code == ''){
					$('.alert p').html('区号不能为空！');
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
				enroll(0,activity_id,ticket_id,maker_id,0,product,body,'none',nickname,phonenum,'html5',sharemark);
			});
	    };
	    $('#container').removeClass('none');
	    $('#container').data('sharemark',sharemark);  
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
                       
                    }
                    else {
                        if (isiOS) {
                            //打开本地app
                            $(document).on('tap', '#openapp', function () {
                                
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
              		var Html='<p class="head">选择参会城市</p>';
              		$.each(obj,function(index,item){
              			Html+='<div><span class="l" data-id="'
              				+obj[index].maker_id+'" style="margin-top:0;"></span>';
              			Html+='<i class="choose_add"><p class="center" >'+obj[index].city+'</p></i></div>';
              			// Html+='<p>'+obj[index].address+'</p>';
              			// Html+='<p>'+obj[index].tel+'</p></i></div>';
              			// <img class="gomap" src="{{URL::asset('/')}}/images/go_map_03.png">
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

			  //获取订单编号
			  function getOrder(result) {
			  	  $('#enroll').data('order',result.order_no);
			  }

			  function enroll(uid,activity_id,ticket_id,maker_id,cost,product,body,pay_way,name,tel,path,share_mark){
			  		var param={};
			  		param['uid']=uid;
			  		param['activity_id']=activity_id;
			  		param['ticket_id']=ticket_id;
			  		param['maker_id']=maker_id;
			  		param['cost']=cost;
			  		param['product']=product;
			  		param['body']=body;
			  		param['pay_way']=pay_way;
			  		param['name']=name;
			  		param['tel']=tel;
			  		param['path']=path;
			  		param['share_mark']=share_mark;
			  		var url=labUser.api_path+'/activity/apply-no-pay/_v020500';
			  		ajaxRequest(param,url,function(data){
			  			if(data.status){
			  				// getOrder(data.message);
			  				var activity_id={{$id}},
			  					maker_id=$('.checkedimg').attr('data-id'),
			  					register=data.message.is_register;
			  					order = data.message.order_no;
			  				// var order = $('#enroll').data('order')
			 				window.location.href=labUser.path +'/webapp/ticket/applysuccess/_v020700?activity_id='+activity_id +'&pagetag=024-4'+'&order_no='+order+'&is_share=1'+'&share_mark='+sharemark+'&code='+code;
			  				
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
			var phone_reg=/^\d{10,11}$/; 
			$(document).on('tap','#enroll',function(){
				var nickname=$('input[name="nickname"]').val(),
					phonenum=$('input[name="phonenum"]').val().split(' ')[1],
					nation_code = $('input[name="phonenum"]').val().split(' ')[0],
					identify=$('input[name="identify"]').val(),
					sharemark=$('#container').data('sharemark');
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
				if( nation_code == ''){
					$('.alert p').html('区号不能为空！');
					errorshow();
					return false;
				};
				if(phonenum=='' || nation_code == ''){
					$('.alert p').html('手机号码不能为空！');
					errorshow();
					return false;
				};
				if(!phone_reg.test(phonenum)){
					$('.alert p').html('手机号格式不对！');
					errorshow();
					return false;
				};
				enroll(0,activity_id,ticket_id,maker_id,0,product,body,'none',nickname,phonenum,'html5',sharemark);
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