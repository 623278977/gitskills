@extends('layouts.default')
<!--zhangx-->
@section('css')
    <link href="{{URL::asset('/')}}/css/_v020800/agent_detail.css" rel="stylesheet" type="text/css"/>
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
		<!--<div class="personal bgwhite" class="medium">
			<div class="messa head pl-r">
    			<div class="messa-l">
        			<img src="" alt="" class="avatar mr05" />
        			<div class="">
        				<span class="mb08 bold f14 nickname"></span><br />
        				<div class="gen-zone">
        					<img src="" class="gender"/>
        					<span class="zone f12 color999 medium"></span>
        				</div>
        			</div>
    			</div>
    			<div class=""><img src="/images/agent/jp.png" alt="" class="medal"/>&nbsp;<span class="f12 color333 medium level">金牌经纪人</span></div>
        	</div>
			<p class="keyword fline pl-r medium tags">
				<span class="keywords m05 f12 color-years">特色餐饮</span>
				<span class="keywords m05 f12 color-years">特色餐饮</span>
				<span class="keywords m05 f12 color-years">特色餐饮</span>
				<span class="keywords m05 f12 color-years">特色餐饮</span>
				<span class="keywords m05 f12 color-years">特色餐饮</span>		
			</p>
			<div class="f16 color6 xm-sign ui-nowrap-multi medium pl-r">“”</div>
		</div>
		<p class="medium f12 color333 grade bgwhite mt1-33 pl-r"><span class="b bold f16 color333">关系</span><span class="relation f14 color666"></span></p>
		<div class="personals medium bgwhite pl1 pb1-33 send_orders">
			<p class="f16 fline ptb1-4 b keyword bold ">
				派单品牌
			</p>
			<div class="xm-acting xm-inb mt1-5">
				<img src="" class="xm-acting-img mr05"/>
				<div class="f1 xm-inb medium">
					<span class="f16 b xm-b color333 mb1 brands_name">果冻</span><br />
					<span class="f12 dark_gray xm-b mb1 color999">行业分类： </span>
                    <span class="f12 dark_gray xm-b mb1 color999">大闸蟹</span><br />
                    <span class="f12 dark_gray xm-b color999">启动资金： </span>
                    <span class="f12 dark_gray xm-b color999">2-3万</span>
				</div>
			</div>
		</div>-->
		<!--评分-->
		<!--<div class=" bgwhite ">
			<div class="medium grade mt1-33 fline pl-r">
				<span class="bold f16 b color333">评价评分</span>
				<p class="">
					<span class="color666 f14">已有人参与评价</span>
				</p>
			</div>
			<ul class="star pl-r">
				<li><span class="f14 color666 medium mr2">综合评分</span></li>
				<li class="composite score"><img src="/images/agent/ico_star_yellow@3x.png" class="mr1"/>
					<img src="/images/agent/ico_star_gray@3x.png" class="mr1"/>
					<img src="/images/agent/ico_star_gray@3x.png" class="mr1"/>
					<img src="/images/agent/ico_star_gray@3x.png" class="mr1"/>
				    <img src="/images/agent/ico_star_gray@3x.png" class="mr1"/>
					<span class="f16 medium cffa300 composite_grade">5.0</span></li>
			</ul>
			<ul class="star pl-r">
				<li><span class="f14 color666 medium mr2">服务态度</span></li>
				<li class="service score"><img src="/images/agent/ico_star_yellow@3x.png" class="mr1"/>
					<img src="/images/agent/ico_star_gray@3x.png" class="mr1"/>
					<img src="/images/agent/ico_star_gray@3x.png" class="mr1"/>
					<img src="/images/agent/ico_star_gray@3x.png" class="mr1"/>
				    <img src="/images/agent/ico_star_gray@3x.png" class="mr1"/>
					<span class="f16 medium cffa300 service_grade">5.0</span></li>
			</ul>
			<ul class="pl-r star">
				<li><span class="f14 color666 medium mr2">专业能力</span></li>
				<li class="power score"><img src="/images/agent/ico_star_yellow@3x.png" class="mr1"/>
					<img src="/images/agent/ico_star_gray@3x.png" class="mr1"/>
					<img src="/images/agent/ico_star_gray@3x.png" class="mr1"/>
					<img src="/images/agent/ico_star_gray@3x.png" class="mr1"/>
				    <img src="/images/agent/ico_star_gray@3x.png" class="mr1"/>
					<span class="f16 medium cffa300 power_grade">5.0</span></li>
			</ul>
			<ul class="pl-r star">
				<li><span class="f14 color666 medium mr2">响应及时</span></li>
				<li class="respond score"><img src="/images/agent/ico_star_yellow@3x.png" class="mr1"/>
					<img src="/images/agent/ico_star_gray@3x.png" class="mr1"/>
					<img src="/images/agent/ico_star_gray@3x.png" class="mr1"/>
					<img src="/images/agent/ico_star_gray@3x.png" class="mr1"/>
				    <img src="/images/agent/ico_star_gray@3x.png" class="mr1"/>
					<span class="f16 medium cffa300 respond_grade">5.0</span></li>
			</ul>
		</div>
		<div class="personals medium bgwhite pl1 pb1-33 agency ">
			<p class="f15 fline ptb1-4 b keyword bold pl-r">
				代理品牌
			</p>
			<div class="xm-acting xm-inb mt1-5">
				<img src="" class="xm-acting-img mr05"/>
				<div class="f1 medium">
					<span class="f15 b xm-b color333 mb1">果冻</span><br />
					<span class="f11 dark_gray xm-b mb1">行业分类： </span>
                    <span class="f11 dark_gray xm-b mb1">大闸蟹</span><br />
                    <span class="f11 dark_gray xm-b">启动资金： </span>
                    <span class="f11 dark_gray xm-b">2-3万</span>
				</div>
			</div>
		</div>
		<div class="xm-btn">
			<span class="mes"><img src="/images/agent/mes@3x.png"/></span>
			<span class="tel"><a href="tel:18626867060"><img src="/images/agent/tel@3x.png"/></a></span>
		</div>-->
	</section>
    <section class="enjoy" style='margin-bottom:5rem'>
    </section>
    <section style="border-top:1px solid transparent"></section>
@stop
@section('endjs')
    <script src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
    <script src="{{URL::asset('/')}}/js/_v020801/agent_detail.js"></script>
@stop