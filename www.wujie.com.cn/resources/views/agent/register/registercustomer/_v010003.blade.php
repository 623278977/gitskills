@extends('layouts.default')
@section('css')
<link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/agent/_v010003/registercustomer.css"/>
<link href="{{URL::asset('/')}}/css/intlTelInput.css" rel="stylesheet" type="text/css"/>
@stop
<!--zhangxm-->
@section('main')
<section id='container' class="none">
	<div class="logo">
		<img src="/images/dock-logo2.png"/>
	</div>
	<div class="fline dis">
		<input type="" name="" id="zone" value="+86" readonly="readonly" class="color_ccc f15"/>
		<input type="" name="" id="" placeholder="请输入手机号" class="mt3 inp f15 color_ccc tel"/>
		
	</div>
	<div class="fline picture">
		<input type="" name="" id="picture_code" value="" class="mt3 inp f15 color_ccc" placeholder="请输入图形验证码"/>
		
		<img src="{{URL::asset('/')}}/identify/piccaptcha" class="mt2 yanzhengma" onclick="this.src='/identify/piccaptcha/'+Math.random()"/>
	</div>
	<div class="fline picture">
		<input type="" name="" id="code" value="" class="mt3 inp f15 color_ccc yanzheng" placeholder="请输入验证码"/>
		<button class="f12 fr getcode inp mt3 f15 color999">获取验证码</button>
	</div>
	<div class="mt1-5">
		<span class="f11 color999 ">您的邀请人：</span><span class="f11 ff5a00 realname"></span><span class="f11 color999">（无界商圈经纪人）</span>
	</div>
	<button class="foot_btn">成为无界商圈投资人</button>
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
<script src="{{URL::asset('/')}}/js/agent/_v010003/registercustomer.js"></script>
@stop