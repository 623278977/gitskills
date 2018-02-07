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
					<p class="ph-title green" id="title-x">企业员工职业化训练整体解决方案</p>
					<img src="{{URL::asset('/')}}/images/wechat.png" alt="" class="code-img" >
					<p class="ticket-num">票号：102287633982</p>
				</div>
				<div class="white-bg sf-line"><img src="{{URL::asset('/')}}/images/lsat-line.png" alt="" class="sf-line" /></div>
			</section>
			<section class="section-box pl36 ns-detail">
				<a href="" id="zb-detail">
					<em>直播详情 <i class="next-show">下一场直播将于 <span class="green"></span> 开启</i></em>
					<span class="icon w16 icon-right"></span>
				</a>
			</section>
			<section class="section-box pl36 ticket-new">
				<div class="person color3">
					<span class="icon w33"><img src="{{URL::asset('/')}}/images/wechat.png" alt="" /></span>Anson
				</div>
				<p class="line1"></p>
				<div>
					<span class="icon w16 icon-clock"></span>04/17  13:00
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
			Ticket.zbTicketDetial({{ $id }});
			Ticket.zbTicketDetial_n({{ $id }});
		})
	</script>
@stop