@extends('layouts.default')  
<!--zhangxm-->        
@section('css')
<link href="{{URL::asset('/')}}/css/agent/chooseclient.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
<section class="containerBox ">
	<!--<p class="title bgfont f11 color666">
		<span class="">根据</span><span class="">喜茶</span><span class="">跟进的投资人，按照跟单时间倒叙排列</span>
	</p>
	<div class="bgwhite">
		<div class="list bgfont fline">
			<div class="list-l">
				<span class="pact-choose mr1-5" pactId=""></span>
				<img src="" class="avatar m1"/>
				<div class="listl-right">
					<span class="listl-name bold mb1 f15 b">苹果</span>
					<img src="/images/agent/women.png" alt="" class="gender" />
					<br />
					<span class="listl-pro f11">浙江</span>
				</div>
			</div>
			<div class="list-r">
				<span class="listr-time mb1 f12 color666">7月11日</span>
				<span class="listr-begin mb1 f12 color666">开始跟单</span>
				<br />
				<span class="listr-yet f11 color999">已跟单100年</span>
			</div>
		</div>
	</div>-->

</section>
<section>
	<div class="common_pops none"></div>
	<!--邀请成功弹窗-->
	<div class="masking none">
		<div class="invite-succes">
			<!--<span class="send f24 bold">发送成功</span><br />-->
			<img src="/images/agent/succeed.png"/><br />
			<span class="mt1 mb1 f15 text_black b">发送成功</span>
			<p class="masking-p">
				<span class="stay f12  color999">停留在电子合同</span>
				<span class="back f12  ">前往对应聊天窗</span>
			</p>
		</div>
	</div>
	<button class="btn f16 ">确定</button>
</section>
<section class="enjoy" style='padding-bottom:5.5rem;background-color: #f2f2f2;'></section>
@stop
@section('endjs')
<!--<script src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>-->
<script src="{{URL::asset('/')}}/js/agent/chooseclient.js"></script>
@stop