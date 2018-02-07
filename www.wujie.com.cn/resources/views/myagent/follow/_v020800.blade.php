@extends('layouts.default')
<!--zhangxm-->
@section('css')
	<link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/_v020800/follow.css"/>
@stop
<!--zhangxm-->
@section('main')
	<section id='container' class="medium">
		<!--<div class="fellow bgwhite">
			<div class="fellow_brand ">
				<div class="fellow_mes">
					<img src="/images/default.jpg" class="fellow_logo mr1"/>
					<p class="fellow_mark">
						<span class="bold color333 f13 mb08">喜茶</span><br />
						<span class="f11 medium c8a869e">行业分类：</span>
						<span class="f11 medium cffac00 mr2">鲜果饮品</span>
						<span class="f11 medium c8a869e">启动资金：</span>
						<span class="f11 medium cff4d64">5 ~ 19万</span>
					</p>
				</div>
				<img src="/images/agent/black_to.png" class="fellow_jump"/>
			</div>
			<div class="fline"></div>
			<div class="cont ">
				<div class="cont_l">
					<img src="/images/agent/downs.png" class="down"/>
					<span class="f12 color666 medium">跟进经纪人：5人</span>
				</div>
				<span class="f12 color666 medium">最早跟进时间：1111/11/11</span>	
			</div>
			<div class="fline"></div>
			
			
			<div class="pl1">
				<div class="fellow_agent fline">
					<img src="/images/default.jpg" class="avater mr1"/>
					<div class="">
						<p class="name_gen mb05"><span class="f16 bold b color333 mr05">蔡江海</span><img src="/images/agent/boy.png" class="gender"/></p>
					<span class="f14 color666 medium">上海 松江</span>
					</div>
				</div>
			</div>
			
		</div>-->
	</section>
	<!--<section>
		<div class="define">
			<img src="/images/agent/no_brand.png" class="no_brand"/>
		</div>
	</section>-->
    <section class="enjoy " style='padding-bottom:4rem'>
    </section>
    <section style="border-top:1px solid transparent"></section>
@stop
@section('endjs')
    <script src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
    <script src="{{URL::asset('/')}}/js/_v020800/follow.js"></script>
@stop