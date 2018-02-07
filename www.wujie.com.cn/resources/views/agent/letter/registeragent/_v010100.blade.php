@extends('layouts.default')
@section('css')
<link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/agent/_v010100/registeragent.css"/>
<link href="{{URL::asset('/')}}/css/intlTelInput.css" rel="stylesheet" type="text/css"/>
@stop
<!--zhangxm-->
@section('main')
<section id='container' class="none">
	<div class="wrap">
		<div class="logo">
			<img src="/images/agent/dock-logo.png"/>
		</div>
		<div class="fline">
			<input type="" name="" id="name" value="" placeholder="请输入昵称" class="mt3 inp f15 color_ccc"/>
		</div>
		<div class="fline dis">
			<input type="" name="" id="zone" value="+86" readonly="readonly" class="color_ccc f15 ml1"/>
			<input type="" name="" id="" placeholder="请输入手机号" class="mt3 inp f15 color_ccc tel ml1-5"/>
			
		</div>
		<div class="fline picture">
			<input type="" name="" id="picture_code" value="" class="mt3 inp f15 color_ccc" placeholder="请输入图形验证码"/>
			<img src="{{URL::asset('/')}}/identify/piccaptcha" class="mt2 yanzhengma" onclick="this.src='/identify/piccaptcha/'+Math.random()"/>
		</div>
		<div class="fline picture">
			<input type="" name="" id="code" value="" class="mt3 inp f15 color_ccc yanzheng" placeholder="请输入验证码"/>
			<button class="f12 fr getcode inp mt3 f15 color999">获取验证码</button>
		</div>
		<div class="fline">
			<input type="password" name="" maxlength="16" id="password" value="" class="mt3 inp f15 color_ccc" placeholder="设置登录密码" />
		</div>
		<div class="fline">
			<input type="password" name="" id="password_again" value="" class="mt3 inp f15 color_ccc" placeholder="再输入一次登录密码"/>
		</div>
		<div class="mt1-5">
			<span class="f11 color999 ">您的邀请人：</span><span class="f11 color2873 realname"></span>
			<span class="f11 color999">（无界商圈经纪人）</span>
		</div>
		<button class="foot_btn">成为无界商圈经纪人</button>
		<p class="realize pb1-5"><span class="f12 c2873ff mr05">了解更多经纪人</span><img src="/images/agent/show.png" class="show_up"/></p>
	</div>
	<!--_v106-->
	<div class="bgf2f2 section_two">
		<p class="show_up mt1-5"><img src="/images/agent/show_up.png"/><br /><img src="/images/agent/show_up.png" class="showup_img"/></p>
		<p class="explain">
			<span class="f11 color333">无界商圈经纪人，开启新的招商代理模式。</span><br />
			<span class="f11 color333">为你的客户提供优质跟单服务：品牌筛选，品牌咨询，活动邀约、考察邀请、意向培养优质客户，直至最终成功签约加盟合同。使经纪人渗透到投资人开店创业的各个环节，让开店，更轻松；也让经纪人，更获利！</span>
		</p>
		<!--丰厚佣金拿不停-->
		<p class="mt3 mb3 fhyj">
			<img src="/images/agent/dian_zuo.png" alt="" class="" />
			<span class="f15 text_black b">丰厚佣金拿不停</span>
			<img src="/images/agent/dian_you.png" alt="" class="" />
		</p>
		<div class="fh_icon">
			<p class=""><img src="/images/agent/chengdan.png" alt="" class="" /><span class="f14 color999">成单提成</span></p>
			<p class=""><img src="/images/agent/tuandui.png" alt="" class="" /><span class="f14 color999">团队分成</span></p>
			<p class=""><img src="/images/agent/yaoqing.png" alt="" class="" /><span class="f14 color999">邀请提成</span></p>
		</div>
		<p class="mt1-5 lh1_9"><span class="c2873ff f12 b">成单提成:</span><span class="f12 color333">无界商圈经纪人成功邀请投资人加盟品牌，将获得成单提成。你及下线经纪人的成单数量越多，提成额度也相应越高。</span></p>
		<p class="mt1-5 mb1-33 lh1_9"><span class="c2873ff f12 b">团队分成:</span><span class="f12 color333">你的下线分支越多，获得的团队分成也就会相应越高。</span></p>
		<p class="lh1_9"><span class="c2873ff f12 b">邀请提成:</span><span class="f12 color333">你邀请的投资人入驻无界商圈（投资人版），并成功加盟品牌，你将获得 <span class="cff0000 f12">1000</span> 元邀请提成，上不封顶。</span></p>
		<!--成为无界商圈经纪人-->
		<p class="mt3 mb3 fhyj">
			<img src="/images/agent/dian_zuo.png" alt="" class="" />
			<span class="f15 text_black b">成为无界商圈经纪人</span>
			<img src="/images/agent/dian_you.png" alt="" class="" />
		</p>
		<p class="mb3">
			<span class="f15 b color333" style="line-height: 3rem;">01</span>&nbsp;<span class="f14 color333">“完成快速注册”</span><br />
			<span class="f12" style="line-height: 1.9rem;">通过引导，完成手机号、登录密码的设置。</span><br />
			<span class="f12" style="line-height: 1.9rem;">需要确认您的邀请人是否正确。</span>
		</p>
		<span class="f15 b color333">02</span>&nbsp;<span class="f14 color333">“下载无界商圈AGENT版”</span>
		<div class="download mt2-5">
			<div class="">
				<img src="/images/agent/agenterwei.png" alt="" class="erweima mb1-33" />
				<div class="">
					<div class="download_text">
						<span class="short"></span>
						<p class="">
							<span class="f10 color999">长按下载Ios版本</span><br />
							<span class="f10 color999">无界商圈经纪人APP</span>
						</p>
						<span class="short"></span>
					</div>
					<img src="/images/agent/fingerprint.png" alt="" class="fingerprint mt1" />
				</div>
			</div>
			<div class="">
				<img src="/images/agent/agenterwei.png" alt="" class="erweima mb1-33" />
				<div class="">
					<div class="download_text">
						<span class="short"></span>
						<p class="">
							<span class="f10 color999">长按下载Android版本</span><br />
							<span class="f10 color999">无界商圈经纪人APP</span>
						</p>
						<span class="short"></span>
					</div>
					<img src="/images/agent/fingerprint.png" alt="" class="fingerprint mt1" />
				</div>
			</div>
			<div class="">
				<p class="yybao mb2"><img src="/images/agent/yingyongbao.png" alt="" class="" /></p>
				<span class="c2873ff f10 myApp">点击直接跳转应用宝</span>
			</div>
			
		</div>
		<p class="f10 color999 mt2-25 mb3">* 完成应用安装，完善注册最后几个步骤</p>
		<div class="foot_img">
			<img src="/images/agent/telcontent.png"/>
			<p class="mt3 mb3 fhyj">
				<img src="/images/agent/dian_zuo.png" alt="" class="" />
				<span class="f15 text_black b">开启无界商圈经纪人</span>
				<img src="/images/agent/dian_you.png" alt="" class="" />
			</p>
			<img src="/images/agent/wjlogo.png"/>
		</div>
	</div>
	
	
</section>
<section class="" style="height: 7rem;">
	
</section>
<section>
	<!--弹窗-->
<!--<div class ="bg-model none">
		<div class ='ui_content'>
			<div class="ui_task ui-border-b relative">
				<span class="f15">请输入手机验证码</span>
			</div>
			<div class="ui_task_detail f12 color666 padding">
				<div class="ui_iphone border999">
					<input id="code" type="text" class="input f12 fl" name="wrirecode" maxlength="5" placeholder="请输入验证码">
					<button class="f12 fr getcode">
					获取验证码
					</button>
				</div>
				<div style="width:100%;height:3.3rem"></div>

			</div>
		</div>
	</div>-->
	<!-- 注册成功弹窗 -->
	<div class="common_pops none"></div>
	<div class="fixbg none">
		<div class="suc_tips tc pt2 pl1-5 pr1-5">
			<p class="mb1 pop_img"><img src="/images/agent/succeed.png"/></p>
			<p class="f15 b color333" style="padding-bottom: 1rem;">
				经纪人注册成功
			</p>
			<p class="f12 pt1 color999">
				欢迎加入无界商圈，成为商圈经纪人
			</p>
			<p class="f12 pt1 color999">
				海量项目、优质活动、精彩直播等你探索发掘
			</p>
			<button class="be_sure mb2 mt3 f12">
			下载 无界商圈经纪人版
			</button>
		</div>
		
	</div>
</section>
<!--<section class="enjoy" style='padding-bottom:7rem'></section>-->
@stop
@section('endjs')
<script src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
<script src="{{URL::asset('/')}}/js/intlTelInput.js"></script>
<script src="{{URL::asset('/')}}/js/agent/_v010100/registeragent.js"></script>
<!--<script type="text/javascript">
	//分享
        function showShare() {
        	var args=getQueryStringArgs(); 
            var type='';
            var title = '【无界商圈】邀请加入无界商圈，成为品牌加盟领航者！';
            var img =  $('#container').attr('logo');
            var header = '快速注册经纪人';
            var content = '无界商圈，提供品牌加盟新模式，开创经纪人服务模式，让您更好的把握每一份商机！';
            var id=id;
            var url = window.location.href;
            shareOut(title, url, img, header, content,'','',id,type,'','','','','');
        };
</script>-->
@stop