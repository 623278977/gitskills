@extends('layouts.default')
@section('css')
	<link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/agent/customer-data.css"/>
@stop
<!--zhangxm-->
@section('main')
	<section id='container' class="medium">
		<!--<div class="datum bcg-f medium">
			<img src="" class="photo"/>
			<div class="datum-l ">
				<span class="f18 b text_black bold">刘鹏</span><br />
				<span class="dark_gray f14 mt05">昵称：sunshine</span><br />
				<span class="dark_gray f14 mt05">备注：刘鹏</span><span class="ml2 dark_gray f14 c2873ff mt05 amend">修改</span><br />
				<span class="dark_gray f14 mt05">联系电话：1234567890</span>
			</div>
		</div>
		<div class="relation bcg-f medium">
			<span class="f15 color3 b">关系</span>
			<p>
				<span class="color999 f12 ">邀请客户&nbsp;跟单客户</span>
			</p>
		</div>
		<div class="content bcg-f">
			<p class="fline"><span class="color3 b f15">地区</span><span class="color999 f12">浙江 加拿大</span></p>
			<p class="fline"><span class="color3 b f15">学历</span><span class="color999 f12">小学僧</span></p>
			<p class="fline"><span class="color3 b f15">职位</span><span class="color999 f12">村长</span></p>
			<p class="fline"><span class="color3 b f15">收入</span><span class="color999 f12">1000-2222</span></p>
			<p class="fline"><span class="color3 b f15">感兴趣行业</span><span class="color999 f12">家政服务&nbsp;家政服务&nbsp;家政服务</span></p>
			<p class="fline"><span class="color3 b f15">投资意向</span><span class="color999 f12">投资意向很大</span></p>
			<p class="fline"><span class="color3 b f15">投资额度</span><span class="color999 f12">11-11万元</span></p>
		</div>
		<div class="content bcg-f">
			<p class="fline"><span class="color3 b f15">邀请人</span><span class="color999 f12">skjhd(经纪人)</span></p>
			<p class="fline"><span class="color3 b f15">注册时间</span><span class="color999 f12">1111年11月11日</span></p>
		</div>-->
	</section>
    <section class="enjoy " style='padding-bottom:4rem;background-color: #f2f2f2;'>
    </section>
    <section style="border-top:1px solid transparent"></section>
@stop
@section('endjs')
    <script src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
    <script src="{{URL::asset('/')}}/js/agent/customer-data.js"></script>
@stop