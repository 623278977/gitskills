@extends('layouts.default')
@section('css')
<link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/agent/_v010100/registercustomer.css"/>
<link href="{{URL::asset('/')}}/css/intlTelInput.css" rel="stylesheet" type="text/css"/>
@stop
<!--zhangxm-->
@section('main')
<section id='container' class="none">
	<div class="wrap">
		<div class="logo">
			<img src="/images/dock-logo2.png"/><br />
			<img src="/images/agent/text.png" class="mb1 mt05"/>
		</div>
		<div class="text_style">
			<p class="f14 color333 mb1">海量品牌 优选加盟 提供专业客服全程跟进服务</p>
			<p class="f14 color999">立即注册，赢取丰厚创业基金！</p>
			<p class="f14 color999 mb2-5">让你创业加盟更轻松，更得力！</p>
		</div>
		<div class="bcg">
			<div class=" dis mb1-2 pl3 pr1-5">
				<input type="" name="" id="zone" value="+86" readonly="readonly" class="color_ccc f15 ml1"/>
				<input type="" name="" id="" placeholder="请输入手机号" class="mt2 inp f15 color_ccc tel ml1-5"/>
			</div>
			<div class=" picture mb1-2 pl3 pr1-5">
				<input type="" name="" id="picture_code" value="" class="mt2 inp f15 color_ccc" placeholder="请输入图形验证码"/>
				<img src="{{URL::asset('/')}}/identify/piccaptcha" class=" yanzhengma" onclick="this.src='/identify/piccaptcha/'+Math.random()"/>
			</div>
			<div class=" picture pl3 pr1-5">
				<input type="" name="" id="code" value="" class="mt2 inp f15 color_ccc yanzheng" placeholder="请输入验证码"/>
				<button class=" fr getcode inp mt2 f15 cff5a00">获取验证码</button>
			</div>
			<button class="foot_btn cff5a00">获得千元创业红包</button>
			<div class="mt1-5" style="text-align:center;">
				<span class="f13 colorff">您的邀请人：</span><span class="f13 colorff realname"></span><span class="f13 colorff">(</span><span class="f13 colorff phone"></span><span class="f13 colorff">)</span>
			</div>
		</div>
		<p class="realize pb1-5 mt2-5 mb2"><span class="f12 cff5a00 mr05">了解更多无界商圈</span><img src="/images/agent/yellowshow.png" class="show_up"/></p>
	</div>
	<!--_v106-->
	<div class="bgf2f2 section_two">
		<div class="explain mt4-5">
			<p class="f11 color333">无界商圈，是一款以品牌发布、展示、投资、加盟为一体的资源整合平台。</p>
			<p class="f11 color333">在这里，你可以浏览海量“<span class="cff5a00">优选项目</span>”；</p>
			<p class="f11 color333">参与无界商圈独家“<span class="cff5a00">OVO活动</span>”、“<span class="cff5a00">OVO直播</span>”；</p>
			<p class="f11 color333">体验无界商圈专业的<span class="cff5a00">客服服务跟进</span>，感受一站式加盟体验服务，让品牌加盟更安全、更可靠、更透明。</p>
			<p class="f11 color333">无界商圈，这是一个提供梦想的地方。</p>
		</div>
		<!--赶紧注册下载无界商圈，抽取丰厚创业基金~--> 
		<p class="mt3 mb3">
			<span class="f15">赶紧注册下载无界商圈，抽取丰厚创业基金~</span>
		</p>
		<div class="download mt2-5 pb5-5">
			<div class="">
				<img src="/images/wjerweis.png" alt="" class="erweima mb1-33" />
				<div class="">
					<div class="download_text">
						<span class="short"></span>
						<p class="">
							<span class="f10 color999">长按下载Ios版本</span><br />
							<span class="f10 color999">无界商圈APP</span>
						</p>
						<span class="short"></span>
					</div>
					<img src="/images/agent/fingerprint.png" alt="" class="fingerprint mt1" />
				</div>
			</div>
			<div class="">
				<img src="/images/wjerweis.png" alt="" class="erweima mb1-33" />
				<div class="">
					<div class="download_text">
						<span class="short"></span>
						<p class="">
							<span class="f10 color999">长按下载Android版本</span><br />
							<span class="f10 color999">无界商圈APP</span>
						</p>
						<span class="short"></span>
					</div>
					<img src="/images/agent/fingerprint.png" alt="" class="fingerprint mt1" />
				</div>
			</div>
			<div class="myApp">
				<p class="yybao mb2"><img src="/images/agent/yingyongbao.png" alt="" class="" /></p>
				<span class="c2873ff f10 ">点击直接跳转应用宝</span>
			</div>
			
		</div>
	</div>
	
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
			<p class="mb1 pop_img"><img src="/images/agent/finish.png"/></p>
			<p class="f15 b color333" style="padding-bottom: 1rem;">
				投资人注册成功
			</p>
			<p class="f12 pt1 color999">
				欢迎加入无界商圈，成为商圈投资人
			</p>
			<p class="f12 pt1 color999">
				海量项目、优质活动、精彩直播等你探索发掘
			</p>
			<button class="be_sure mb2 mt3 f12">
			下载 无界商圈投资人版
			</button>
		</div>
	</div>
</section>
<!--<section class="enjoy" style='padding-bottom:7rem'></section>-->
@stop
@section('endjs')
<script src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
<script src="{{URL::asset('/')}}/js/intlTelInput.js"></script>
<script src="{{URL::asset('/')}}/js/agent/_v010100/registercustomer.js"></script>
<script type="text/javascript">
	$(document).ready(function () {
		$('title').text('无界商圈投资人');
	});
</script>
@stop