@extends('layouts.default')
<!--zhangx-->
@section('css')
    <link href="{{URL::asset('/')}}/css/_v020800/invest.css" rel="stylesheet" type="text/css"/>
    <style>
        .spread{ 
            margin: 1.5rem auto;
        }
    </style>
@stop
<!--zhangxm-->
@section('main')
    <!--内容-->
	<section id='container'>
		<div class="personal bgwhite" class="mudium">
			<div class="messa head pl-r">
    			<div class="messa-l">
        			<img src="" alt="" class="avatar mr05 " />
        			<div class="">
        				<span class="mb077 bold f14 b color333 nickname"></span><br />
        				<p class="gender-city">
        					<img src="" class="gender mr1 none"/>
        					<span class="f12 color999 medium city"></span>
        				</p>
        				<span class="f12 color999 medium ">最近一次登录</span>
        				<span class="f12 color999 medium last_login"></span>
        			</div>
    			</div>
        	</div>
			<p class="keyword fline pl-r">
				<!--<span class="keywords m05 f11 color-years hobby">特色餐饮</span>
				<span class="keywords m05 f11 color-years">特色餐饮</span>
				<span class="keywords m05 f11 color-years">特色餐饮</span>
				<span class="keywords m05 f11 color-years">特色餐饮</span>
				<span class="keywords m05 f11 color-years">特色餐饮</span>		-->
			</p>
			<div class="f12 color6 xm-sign ui-nowrap-multi mudium pl-r sign"></div>
		</div>
		<div class="cont mt1-33 bgwhite">
			<p class="dis-bt cont-p"><span class="f12 color666 medium">地区</span><span class="f12 c8a869e medium zone"></span></p>
			<p class="dis-bt cont-p"><span class="f12 color666 medium">学位</span><span class="f12 c8a869e medium diploma"></span></p>
			<p class="dis-bt cont-p"><span class="f12 color666 medium">职位</span><span class="f12 c8a869e medium position"></span></p>
			<p class="dis-bt cont-p"><span class="f12 color666 medium">收入</span><span class="f12 c8a869e medium earning"></span></p>
			<p class="dis-bt cont-p"><span class="f12 color666 medium">感兴趣行业</span><span class="f12 c8a869e medium interested_industry"></span></p>
			<p class="dis-bt cont-p"><span class="f12 color666 medium">投资意向</span><span class="f12 c8a869e medium invest_intention"></span></p>
			<p class="dis-bt cont-p"><span class="f12 color666 medium">投资额度</span><span class="f12 c8a869e medium invest_quota"></span></p>
		</div>
		<div class="foot mt1-33 bgwhite">
			<p class="dis-bt pr1-33 pt1-5 pb1-5 "><span class="f12 color666 medium">邀请人</span><span class="f12 c8a869e medium invitor"></span></p>
			<p class="fline"></p>
			<p class="dis-bt pr1-33 pt1-5 pb1-5 "><span class="f12 color666 medium">注册时间</span><span class="f12 c8a869e medium created_at"></span></p>
		</div>
	</section>
    <section class="enjoy" style='margin-bottom:5rem'></section>
    <section style="border-top:1px solid transparent"></section>
@stop
@section('endjs')
    <script src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
    <script src="{{URL::asset('/')}}/js/_v020800/invest.js"></script>
	
@stop