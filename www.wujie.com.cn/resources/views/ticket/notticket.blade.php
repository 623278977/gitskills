@extends('layouts.default')
@section('css')
	<link href="{{URL::asset('/')}}/css/my_detial.css" rel="stylesheet" type="text/css" />
@stop
@section('main')
	<div class="container my_detial">
		<div class="nav-top-bar"></div>
		<div class="code-box">
			<section class="section-box tc relative">
				<div class="st-box">
					<div class="bg-top"></div>
					<p class="ph-title" id="noticket-title">Gdevops全球敏捷运维峰会 —— 杭州站</p>
					<p class="ph-title orange none">抱歉！支付超时，该门票已过期</p>
					<p class="need-money">需付款 <i>¥99</i> 剩余支付时间</p>
					<ul class="time-dowm" id="time-low">
						<li>3</li>
						<li>0</li>
						<li class="fenhao">:</li>
						<li>0</li>
						<li>0</li>
					</ul>
					<div class="clearfix"></div>
					<div class="subThis"><button class="btn blue-bg">立即支付</button></div>
					
				</div>
				<div class="white-bg sf-line"><img src="{{URL::asset('/')}}/images/lsat-line.png" alt="" class="sf-line" /></div>
			</section>
			<section class="section-box pl36 ticket-new">
				<div class="person color3">
					<span class="icon w33"><img src="{{URL::asset('/')}}/images/wechat.png" alt="" /></span>Anson
				</div>
				<p class="line1"></p>
				<div>
					<span class="icon w16 icon-clock"></span><i id="times">04/17  13:00</i>
				</div>
				<p class="line1"></p>
				<div class="two-sk">
					<span class="icon w16 icon-address"></span>
					<em id="maker-ks">
						杭州OVO路演中心<br>
						<i>浙江杭州下城区体育场路浙江国际大酒店11F</i>
					</em>
				</div>
				<p class="line1"></p>
				<div>
					<span class="icon w16 icon-ticket"></span><i id="ticket-type">免费票</i>
				</div>
			</section>
			<section class="section-box pl36 ns-detail">
				<a href="" id="act-detail"><em>活动详情</em> <span class="icon w16 icon-right"></span></a>
			</section>
		</div>
	</div>
@stop
@section('endjs')
	<script type="text/javascript" src="{{URL::asset('/')}}/js/ticket.js"></script>
	<script type="text/javascript">
		Zepto(function(){
			//支付
			Ticket.notTicket({{ $id }});
			//未完成
			//Ticket.sceneTicketDetial({{ $id }});
			//删除门票
			$(document).on("click",".btn.green-bg",function(){
				Ticket.deleted(labUser.uid,{{ $id }});
			});
		});
	</script>
@stop