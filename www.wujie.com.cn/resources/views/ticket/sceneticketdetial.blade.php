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
					<p class="ph-title green" id="title-x">Gdevops全球敏捷运维峰会 —— 杭州站</p>
					<img src="{{URL::asset('/')}}/images/wechat.png" alt="" class="code-img" >
					<p class="ticket-num">票号：<span>102287633982</span></p>
				</div>
				<div class="white-bg sf-line"><img src="{{URL::asset('/')}}/images/lsat-line.png" alt="" class="sf-line" /></div>
				<img src="{{URL::asset('/')}}/images/no-ticket.png" alt="" class="ticket-img" />
			</section>
			<section class="section-box pl36 ticket-new">
				<div class="person color3">

				</div>
				<p class="line1"></p>
				<div>
					<span class="icon w16 icon-clock"></span> <i id="times">04/17  13:00</i>
				</div>
				<p class="line1"></p>
				<div class="two-sk">
					<span class="icon w16 icon-address"></span>
					<em id="maker-ks">
						
					</em>
				</div>
				<p class="line1"></p>
				<div>
					<span class="icon w16 icon-ticket"></span><i id="ticket-type">免费票</i>
				</div>
			</section>
			<section class="section-box pl36 ns-detail">
				<a href="" id="act-detail"><em>活动详情</em> <span class="icon w16 icon-right"></span></a>
				<p class="line1"></p>
				<a href=""><em>添加到Passbook</em> <span class="icon w16 icon-right"></span></a>
			</section>
		</div>
	</div>
@stop
@section('endjs')
	<script type="text/javascript" src="{{URL::asset('/')}}/js/ticket.js"></script>
	<script type="text/javascript">
		Zepto(function(){
			Ticket.sceneTicketDetial({{ $id }});
		})
		
	</script>
@stop