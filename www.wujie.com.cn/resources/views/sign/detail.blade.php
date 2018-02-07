@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/sign.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
	<section style='background-color: #1E8CD4;width:100%;min-height:100%' class="sign none" id='sign'>
		<div class="head">
			<p class="">请确认你的签到信息</p>
			<p class="sec">请放心填写，我们仅对活动报名进行人员统计，</p>
			<p class="sec">不涉及资料外泄</p>
		</div>
		<div class="activity none">
			<img src="" alt="" id='act_img'>
			<div class="act_intro">
				<p class='act_name'>活动名称</p>
				<p class="c9" id="act_time">活动开始：</p>
				<p class='c9' id='live_time'>直播开始：</p>
			</div>
		</div>
		<div class="user none">
			<p class='header'>
				<img src="" alt="" ><span>Ason</span>
			</p>
			<p class="tel_num">
				<span class="tel_img"><img src="{{URL::asset('/')}}/images/shouji.png" alt=""></span>
				<span id='tel_num'>18600000000</span></p>
			<p class='ovo'>
				<img src="{{URL::asset('/')}}/images/address_icon.png" alt="">
				<span class="ovo_name">杭州OVO中心</span>
				<span class="ovo_add">杭州市下城区11号城西大厦</span>
			</p>
			<p class="enroll_time">
				<span class="enroll_img"><img src="{{URL::asset('/')}}/images/time_icon.png" alt=""></span>
				<span id='enroll_time'>2016-10-10 21:00</span>
			</p>
		</div>
		<div class="check_num none">
			<p class="phone_num">
				<span class='tel_img'><img src="{{URL::asset('/')}}/images/shouji.png" alt=""></span>
				<input type="text" placeholder="重新输入" id='phonenumber'>
			</p>
			<p class="check_mes">	
				<span><img src="{{URL::asset('/')}}/images/mes-code.png" alt=""></span>
				<input type="text" placeholder="请输入短信验证码" id='mes_code'>
				<button class="send_code">获取验证码</button>
			</p>
		</div>
		<div class="real_name none">
			<p>	
				<span><img src="{{URL::asset('/')}}/images/zhanghu.png" alt=""></span>
				<input type="text" placeholder="请输入真实姓名" id='realname'>
			</p>
		</div>
		<div class="ensure ">
			<button class="sure_sign">确定并完成签到</button>
		</div>

	</section>
	<div class="module none">
			<div class="tankuang">
				<p class="success">签到成功</p>
				<p>已成功签到无界商圈活动。</p>
				<p>您参加的是<span class="cb6" id='mo_act_name'>活动名称</span>活动即将在<span class="cb6" id='mo_act_time'>18:00</span>开始，</p>
				<p>请按照会场提示就坐。</p>
				<p>如有疑问，请联系会场工作人员。</p>
				<p>我们会为你竭诚服务。</p>	
				<a class="know">知道了</a>
			</div>
	</div>
	<div class="alert none">
        <p></p>
    </div>
@stop

@section('endjs')
 <script type="text/javascript">
	 Zepto(function(){
	 //短信验证码计时器
	    var tt;
	    var wait = 60;
	    function time(o) {
	        if (wait == 0) {
	            o.removeAttr("disabled");
	            o.html("重新发送");
	            o.css({
	              "background":"#8EC5E9",
	              'font-size':'1.6rem'
	            });
	            wait = 60;
	        } else {
	            o.attr("disabled", true);
	            o.css({
	              "background":"#C2C2C2",
	              'font-size':'1.2rem'
	            });
	            o.html('重新发送(' + wait + 's)');
	            wait--;
	            tt = setTimeout(function () {
	                    time(o)
	                },
	                1000)
	        }
	    };

	 	var arg=getQueryStringArgs(),
			activity_id=arg['id'],
			maker_id=arg['maker_id']||'0',
			uid=arg['uid']||'0';
			console.log(uid);
	// 获取活动信息和OVO中心信息
		// function getActivity(id,maker_id){
		// 	var param={};
		// 		param['id']=id;
		// 		param['maker_id']=maker_id;
		// 	var url=labUser.api_path+'/activity/detail';
		// 	ajaxRequest(param,url,function(data){
		// 		if(data.status){
		// 			var arr_id=data.message.self.maker_ids.split('@'),
		// 				arr_ovo=data.message.self.name.split('@'),
		// 				arr_address=data.message.self.address.split('@');
		// 			for(var i=0;i<arr_id.length;i++){
		// 				if(arr_id[i]==maker_id){
		// 					var ovo_name=arr_ovo[i],
		// 						ovo_address=arr_address[i];
		// 						break;
		// 				}else{
		// 					ovo_name='未知的ovo名称';
		// 					ovo_address='未知的ovo地址';
		// 				}
		// 			};
		// 			$('#act_img').attr('src',data.message.self.list_img);
		// 			$('.act_name').text(cutString(data.message.self.subject,20));
		// 			$('#act_time').text('活动开始：'+unix_to_yeardatetime(data.message.self.begin_time));
		// 			$('#live_time').text('直播开始：'+unix_to_yeardatetime(data.message.self.begin_time));
		// 			$('.ovo_name').text(ovo_name);
		// 			$('.ovo_add').text(ovo_address);

		// 		}
		// 	})
		// };
	// 获取用户信息
		// function getUser(uid,user_outh){
		// 	var param={};
		// 		param['uid']=uid;
		// 		param['user_outh']=user_outh;
		// 	var url=labUser.api_path+'/user/getuserdetail';
		// 	ajaxRequest(param,url,function(data){
		// 		if(data.status){
		// 			$('.header img').attr('src',data.message[0].avatar);
		// 			$('.header span').text(data.message[0].nickname);
		// 			$('#tel_num').text(data.message[0].username);
		// 		}
		// 	})
		// };
	// 临时签到
		function tempSign(uid,activity_id,maker_id,sign_type,name,tel){
			var param={};
				param['uid']=uid;
				param['activity_id']=activity_id;
				param['maker_id']=maker_id;
				param['name']=name;
				param['tel']=tel;
				param['sign_type']=sign_type;
				param['name']=name;
				param['tel']=tel;
			var url=labUser.api_path+'/activity/tempsign/_v020400';
			ajaxRequest(param,url,function(data){
				if(data.status){
					$('.module').removeClass('none');
				}
			})
		}
	// 签到
		function sign(uid,activity_id,maker_id){
			var param={};
				param['uid']=uid;
				param['activity_id']=activity_id;
				param['maker_id']=maker_id;
			var url=labUser.api_path+'/activity/sign/_v020400';
			ajaxRequest(param,url,function(data){
				if(data.status){
					if(data.message=='half_open'){
						$('.sure_sign').text('下一步');	
						$('.check_num').removeClass('none');
						$('.real_name').removeClass('none');
						$('.user').addClass('none');
						$('.activity').addClass('none');
						$('#sign').removeClass('none');

					}else{
						$('#act_img').attr('src',data.message.list_img);
						$('.act_name').text(cutString(data.message.subject,20));
						$('#act_time').text('活动开始：'+data.message.activity_begin_time);
						$('#live_time').text('直播开始：'+data.message.live_begin_time);
						$('.ovo_name').text(data.message.maker);
						$('.ovo_add').text(data.message.maker_address);
						$('.header img').attr('src',data.message.user_avatar);
						$('.header span').text(data.message.user);
						$('#tel_num').text(data.message.user_tel);
						$('#enroll_time').text(data.message.sign_time);

						$('#mo_act_name').text(data.message.subject);
						$('#mo_act_time').text(data.message.activity_begin_time.split(' ')[1]);

						$('.check_num').addClass('none');
						$('.real_name').addClass('none');
						$('.user').removeClass('none');
						$('.activity').removeClass('none');
						$('#sign').removeClass('none');
						// getActivity(activity_id,maker_id);
						// getUser(uid,uid);
						$('.sure_sign').text('确定并完成签到');
					};
				}else{
					$('.alert p').html(data.message);
					errorshow();
				};
			});
		}
		var name=$('#realname').val();
			tel=$('#phonenumber').val();
			console.log(name);
	// 发送验证码
		function sendCode(username,type){
			var param={};
				param['username']=username;
				param['type']=type;
			var url=labUser.api_path+'/identify/sendcode';
			ajaxRequest(param,url,function(data){
				if(data.status){
					time($('.send_code'));
				}else{
					$('.alert p').html(data.message);
					errorshow();
				}
			})
		}
	// 验证验证码
		function check_identify (code,username,type){
			  		var param={};
			  			param['username']=username;
			  			param['code']=code;
			  			param['type']=type;
			  		var url=labUser.api_path+'/identify/checkidentify';
			  		ajaxRequest(param,url,function(data){
			  			if(data.status){
			  				tempSign(uid,activity_id,maker_id,'half_open',name,tel)
			  			}else{
			  				$('.alert p').html(data.message);
							errorshow();
			  			};
			  		});
			  };

		sign(uid,activity_id,maker_id);


	 // 点击确定签到
	 	$(document).on('click','.sure_sign',function(){
	 		var text=$('.sure_sign').text();
	 		var name=$('#realname').val(),
	 			tel=$('#phonenumber').val();
	 			code=$('#mes_code').val();
	 		if(text=='确定并完成签到'){
	 			tempSign(uid,activity_id,maker_id,'standard');
	 		}else if(text=='下一步'){
	 			if(name==''){
	 				$('.alert p').html('请填写真实姓名');
					errorshow();
	 			}else{
	 				check_identify(code,tel,'standard')
	 			}
	 		}
	 	});

	 	$(document).on('click','.know',function(){
	 		$('.module').addClass('none');
	 	});
	 	$(document).on('click','.send_code',function(){
	 		var tel=$('#phonenumber').val();
	 		console.log(tel);
	 		sendCode(tel,'standard');
	 	});

	//错误提示
 		function errorshow(){
			 $(".alert").css("display","block");
			 setTimeout(function(){$(".alert").css("display","none")},2000);
		}; 

	 })
 </script>
@stop