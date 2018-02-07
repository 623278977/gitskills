@extends('layouts.default')
@section('css')
	<link href="{{URL::asset('/')}}/css/my_detial.css" rel="stylesheet" type="text/css" />
	<link href="{{URL::asset('/')}}/css/w-pages.css" rel="stylesheet" type="text/css" />
@stop
@section('main')
	<section id="signSection">
		<section class="section-box pl36 ticket-new ">
			<div>
				<input type="text" placeholder="请输入联系人姓名：">
			</div>
			<p class="line1"></p>
			<div>
				<input type="text" placeholder="昵称：">
			</div>
			<p class="line1"></p>
			<div>
				<input type="text" placeholder="请输入联系手机号：">
			</div>
			<p class="line1"></p>
			<div>
				<input type="text" placeholder="常用联系邮箱：">
			</div>
		</section>
		<section class="section-box pl36 ticket-new">
			<div class="ticket-cf">
				<span class="icon w20 icon-name"></span>
				<label for="username">姓名</label>
				<input type="text"  value="王大锤" id="username">
			</div>
			<p class="line1"></p>
			<div class="ticket-cf">
				<span class="icon w20 icon-phone"></span>
				<label for="tel">联系电话</label>
				<input type="text"  value="18812354425" id="tel">
			</div>
		</section>
		<button class="btn-ticket tc f14">提交</button>
	</section>
@stop

@section('endjs')
	<script>var ticket_id ={{$ticket_id}};var activity_id={{$activity_id}};</script>
	<script typr="text/javascript" src="{{URL::asset('/')}}/js/actsign.js"></script>
@stop