<!--zhangxm-->
@extends('layouts.default')
@section('css')
<link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/agent/_v010100/download.css"/>
<link href="{{URL::asset('/')}}/css/intlTelInput.css" rel="stylesheet" type="text/css"/>
@stop
<!--zhangxm-->
@section('main')
<section id='container' class="">
	<div class="wrap none">
		<div class="logo mb3-5">
			<img src="/images/dock-logo2.png"/>
		</div>
		<div class="text_style">
			<p class=" mb1"><span class="f14 color333">恭喜用户&nbsp;</span><span class="telNum f14 color333"></span>&nbsp;<span class="f14 color333">加入无界商圈</span></p>
			<p class="f14 color999 mb2-5">下载商圈APP，更多惊喜等着你</p>
		</div>
		<!---->
		<div class="bcg none">
			<!--未领取-->
			<p class="white f18 ">恭喜您获得千元红包大礼</p>
			<div class=" mb05">
				<div class="ticket noget mt1-5 ">
					<p class="ml1-5 pr1-5 pt1 pb1 money mr1-5"><span class="f12 color333">¥</span><span class="f22 color333 moneyNum">1000</span><br /><span class="f12 color666">全场无条件红包</span></p>
					<p class=""><span class="f15 color333">不限品牌</span><br /><span class="f12 color999">加盟抵扣券</span></p>
				</div>
				
			</div>
			<p class="f13 color-f mb1-5 ">立即访问无界商圈投资人端，加盟意向品牌。立即抵扣1000元加盟费用，更多品牌红包等着你领取！独家优惠，尽在无界商圈！</p>
			<p class="f13 cffec18 ">确认领取，之后还有更大惊喜</p>
		</div>
		<!--已领取-->
		<div class="old_yiling none">
			<p class="white f18  mb2">您已领取千元红包大奖</p>
			<div class="yiling  mb05">
				<div class="ticket get ">
					<p class="ml1-5 pr1-5 pt1 pb1 money mr1-5 opcity"><span class="f12 color333">¥</span><span class="f22 color333 amount"></span><br /><span class="f12 color666">全场无条件红包</span></p>
					<p class="opcity"><span class="f15 color333">不限品牌</span><br /><span class="f12 color999">加盟抵扣券</span></p>
				</div>
			</div>
			<p class="f13 color-f mb1-5 yiling ">立即访问无界商圈投资人端，加盟意向品牌。立即抵扣1000元加盟费用，更多品牌红包等着你领取！独家优惠，尽在无界商圈！</p>
		</div>
		
		<p class="downbutton"><img src="/images/agent/downbutton.png"/></p>
		<div class="result_text pb3-5">
			<p class="f15 color333 mb1">点击“立即使用”，下载无界商圈投资人版！</p>
			<p class="f12 color999">更多抵扣优惠信息，请咨询无界商圈客服人员或浏览品牌详情获取更多信息。</p>
		</div>
	</div>
</section>
<section>
	<!-- 弹窗 -->
	<div class="common_pops none"></div>
</section>
<!--<section class="enjoy" style='padding-bottom:7rem'></section>-->
@stop
@section('endjs')
<script src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
<script src="{{URL::asset('/')}}/js/intlTelInput.js"></script>
<script src="{{URL::asset('/')}}/js/agent/_v010100/download.js"></script>
<script type="text/javascript">
	$(document).ready(function(){$('title').text('赢取无界商圈创业大红包')});
</script>
@stop