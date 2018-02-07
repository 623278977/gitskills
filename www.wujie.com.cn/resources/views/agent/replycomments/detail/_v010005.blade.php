@extends('layouts.default')
<!--zhangx-->
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/_v010005/replycomments.css" rel="stylesheet" type="text/css"/>
    <style>
        .spread{ 
            margin: 1.5rem auto;
        }
    </style>
@stop
<!--zhangxm-->
@section('main')
	<section id="container" class="bgwhite">
		<!--<div class="wrap pt2 pl1 ">
			<div class="message mb1">
				<div class="message_l">
					<p class="naver mr1"><img src="/images/act_banner.png"/></p>
					<p class="name">
						<span class="f10 c2873ff">张老板</span>&nbsp;
						<span class="f10 color666 praise">赞了你的评论</span>
					</p>
				</div>
				<p class="date f10 color999 mr2">10月11日</p>
			</div>
			<div class="fline ml4"><span class="f15 color333 b pb2 comment_text">还是不错的，值得分享！</span></div>
		</div>
		
		<div class="wrap pt2 pl1 ">
			<div class="message mb1">
				<div class="message_l">
					<p class="naver mr1"><img src="/images/act_banner.png"/></p>
					<p class="name">
						<span class="f10 c2873ff">张老板</span>&nbsp;
						<span class="f10 color666 praise">回复了你的评论</span>
					</p>
				</div>
				<p class="date f10 color999 mr2">10月11日</p>
			</div>
			<div class="fline ml4">
				<span class="f15 color333 b pb2 comment_text">还是不错的，值得分享！</span>
				<p class="reply mr2 mb2">
					<span class="reply_name f10 c2873ff mb1">阿树 ：</span>
					<span class="reply_text ui-nowrap-multi">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendumLorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum</span>
				</p>
			</div>
		</div>
		
		<div class="wrap pt2 pl1 ">
			<div class="message mb1">
				<div class="message_l">
					<p class="naver mr1"><img src="/images/act_banner.png"/></p>
					<p class="name">
						<span class="f10 c2873ff">张老板</span>&nbsp;
						<span class="f10 color666 praise">回复了你的评论</span>
					</p>
				</div>
				<p class="date f10 color999 mr2">10月11日</p>
			</div>
			<div class="fline ml4">
				<span class="f15 color333 b pb2 comment_text">还是不错的，值得分享！</span>
				<p class="reply mr2 mb2">
					<span class="reply_name f10 c2873ff mb1">阿树 ：</span>
					<span class="reply_text ui-nowrap-multi">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendumLorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean euismod bibendum</span>
				</p>
			</div>
		</div>-->
		
		
		
	</section>
	<section>
		<button class="getmore none"><img class="h_gif" style="width:1.2rem;height:1.2rem" src="/images/agent/h.gif"/ >正在加载...</button>
		<div class="define none" style="width: 100%;margin: auto;height: 100%;text-align: center;">
			<img src="/images/agent/no_comment.png" style="width: 50%;text-align: center;margin-top: 55%;"/>
		</div>
	</section>
@stop
@section('endjs')
    <script src="{{URL::asset('/')}}/js/agent/_v010005/replycomments.js"></script>
    <script type="text/javascript">
		$(document).ready(function(){
			$('title').text('评论回复');
		});
	</script>
@stop