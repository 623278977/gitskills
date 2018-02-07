@extends('layouts.default')
@section('css')
<link href="{{URL::asset('/')}}/css/agent/_v010004/walkman.css" rel="stylesheet" type="text/css"/>
<style></style>
@stop

@section('main')

<section id="container" class="container">
	<!-- 头部 -->
	<!--<div class='walk_header mb1-2'>
		<img src="" alt="" class="">
	</div>
	<ul class="walk_list mt1-2 pl1-5">
		<li class="walkman_list">
			<img src="/images/agent/walkman.png" alt="">
			<div class="list_item fline">
				<p>
					033期：【异议】等我想买了再联系你
				</p>
				<span>688人听过</span>
				<span>时长00:53</span>
				<span>2.28MB</span>
			</div>
			<div class="fline"></div>
		</li>
		<li class="walkman_list">
			<img src="/images/agent/walkman.png" alt="">
			<div class="list_item fline">
				<p>
					033期：【异议】等我想买了再联系你
				</p>
				<span>688人听过</span>
				<span>时长00:53</span>
				<span>2.28MB</span>
			</div>
			<div class="fline"></div>
		</li>
		<li class="walkman_list">
			<img src="/images/agent/walkman.png" alt="">
			<div class="list_item fline">
				<p>
					033期：【异议】等我想买了再联系你
				</p>
				<span>688人听过</span>
				<span>时长00:53</span>
				<span>2.28MB</span>
			</div>
			<div class="fline"></div>
		</li>
		<li class="walkman_list">
			<img src="/images/agent/walkman.png" alt="">
			<div class="list_item fline">
				<p>
					033期：【异议】等我想买了再联系你
				</p>
				<span>688人听过</span>
				<span>时长00:53</span>
				<span>2.28MB</span>
			</div>
			<div class="fline"></div>
		</li>
		<li class="walkman_list">
			<img src="/images/agent/walkman.png" alt="">
			<div class="list_item fline">
				<p>
					033期：【异议】等我想买了再联系你
				</p>
				<span>688人听过</span>
				<span>时长00:53</span>
				<span>2.28MB</span>
			</div>
			<div class="fline"></div>
		</li>
		<li class="walkman_list">
			<img src="/images/agent/walkman.png" alt="">
			<div class="list_item fline">
				<p>
					033期：【异议】等我想买了再联系你
				</p>
				<span>688人听过</span>
				<span>时长00:53</span>
				<span>2.28MB</span>
			</div>
			<div class="fline"></div>
		</li>
		<li class="walkman_list">
			<img src="/images/agent/walkman.png" alt="">
			<div class="list_item fline">
				<p>
					033期：【异议】等我想买了再联系你
				</p>
				<span>688人听过</span>
				<span>时长00:53</span>
				<span>2.28MB</span>
			</div>
			<div class="fline"></div>
		</li>
		<li class="walkman_list">
			<img src="/images/agent/walkman.png" alt="">
			<div class="list_item fline">
				<p>
					033期：【异议】等我想买了再联系你
				</p>
				<span>688人听过</span>
				<span>时长00:53</span>
				<span>2.28MB</span>
			</div>
			<div class="fline"></div>
		</li>
	</ul>-->
</section>
<section>
	<div class="getmore none"><img class="h_gif" style="width:1.2rem;height:1.2rem" src="/images/agent/h.gif"/>正在加载</div>
</section>
@stop

@section('endjs')
<!--<script src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>-->
<script src="{{URL::asset('/')}}/js/agent/_v010004/walkmanlist.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('title').text('话术随身听');
	});
</script>
@stop