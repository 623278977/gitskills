@extends('layouts.default')
<!--zhangx-->
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/_v010005/posterlist.css" rel="stylesheet" type="text/css"/>
    <style>
        .spread{ 
            margin: 1.5rem auto;
        }
    </style>
@stop
<!--zhangxm-->
@section('main')
	<section id="container" class="bgwhite">
		<div class="bgwhite">
			<nav class="poster_nav dis_bet">
				<!--<span class="choosen">推荐</span>
				<span class="">正能量</span>
				<span class="">金句</span>
				<span class="">分类</span>
				<span class="">自定义</span>-->
			</nav>
			<div class="banner none">
				<img src=""/>
			</div>
			<div class="poster_list">
				<!--<p class=""><img src="/images/live.png" alt="" class="poster_img" /><span class="font">没有所谓的开心</span></p>
				<p class=""><img src="/images/live.png" alt="" class="" /><span class="font">没有所谓的开心</span></p>
				<p class=""><img src="/images/live.png" alt="" class="" /><span class="font">没有所谓的开心</span></p>
				<p class=""><img src="/images/live.png" alt="" class="" /><span class="font">没有所谓的开心</span></p>
				<p class=""><img src="/images/live.png" alt="" class="" /><span class="font">没有所谓的开心</span></p>
				<p class=""><img src="/images/live.png" alt="" class="" /><span class="font">没有所谓的开心</span></p>
				<p class=""><img src="/images/live.png" alt="" class="" /><span class="font">没有所谓的开心</span></p>
				<p class=""><img src="/images/live.png" alt="" class="" /><span class="font">没有所谓的开心</span></p>
				<p class=""><img src="/images/live.png" alt="" class="" /><span class="font">没有所谓的开心</span></p>
				<p class=""><img src="/images/live.png" alt="" class="" /><span class="font">没有所谓的开心</span></p>
				<p class=""><img src="/images/live.png" alt="" class="" /><span class="font">没有所谓的开心</span></p>-->
			</div>
		</div>
	</section>
	<section>
		<div class="getmore "><img class="h_gif" style="width:1.2rem;height:1.2rem" src="/images/agent/h.gif"/ >点击加载更多</div>
		<div class="define none" style="width: 100%;margin: auto;height: 100%;text-align: center;">
			<img src="/images/agent/no_proremind.png" style="width: 50%;text-align: center;margin-top: 30%;"/>
		</div>
	</section>
@stop
@section('endjs')
    <script src="{{URL::asset('/')}}/js/agent/_v010005/posterlist.js"></script>
    <script type="text/javascript">
		$(document).ready(function(){
			$('title').text('海报库');
		});
	</script>
@stop