@extends('layouts.default')
@section('css')
<!--<link href="{{URL::asset('/')}}/css/agent/_v010004/walkman.css" rel="stylesheet" type="text/css"/>-->
<style>
	.list_item>img{
		width: 2rem;
		height: 2rem;
	}
	.talkeveryday_list>img {
		width: 1rem;
		height: 1rem;
	}
	.talkeveryday_list,.list_item {
		display: flex;
		align-items: center;
	}
	.talkeveryday_list {
		justify-content: space-between;
		height: 6rem;
	}
	.list_item {
		font-size: 2rem;
		justify-content: flex-start;
		text-align: left;
	}
	.title {
		overflow: hidden;
		text-overflow: ellipsis;
		white-space: nowrap;
		width: 28rem;
	}
	.getmore {
		width: 100%;
		height: 5rem;
		font-size: 1.5rem;
		text-align: center;
		line-height: 5rem;
		background: #fff;
		border: none;
		color: #999;
	}
</style>
@stop

@section('main')

<section id="container" class="container">
	<ul class="talkeveryday mt1 pl2 bgwhite">
		<!--<li class="talkeveryday_list fline ">
			<div class="list_item">
				<img src="/images/agent/talkeveryday.png"/>
				<span class="ml1 color333 title f15"></span>
			</div>
			<img src="/images/agent/black_to.png" class="mr2"/>
		</li>-->
		
	</ul>
</section>
<section>
	<div class="getmore none"><img class="h_gif" style="width:1.2rem;height:1.2rem" src="/images/agent/h.gif"/ >正在加载...</div>
</section>
@stop

@section('endjs')
<!--<script src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>-->
<script src="{{URL::asset('/')}}/js/agent/_v010005/talkeverydaylist.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('title').text('话术天天练');
	});
</script>
@stop