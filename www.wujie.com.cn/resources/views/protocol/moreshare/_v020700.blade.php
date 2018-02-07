@extends('layouts.default')
@section('css')
	<style>
		.share_btn {
			background-color:#ffac00;
			color:#fff;
			font-size: 1.6rem;
			border: none;
			height:3rem;
			width:18rem;
	 		border-radius: 1.5rem;
		}

	</style>
@stop
@section('main')
	<section class="pl1-33 pr1-33 f14">
		<h2 class="f16 b tc pt2 pb2">无界商圈分享规则</h2>
		<div class="mb2 bgwhite">
			<p>
				<img src="{{URL::asset('/')}}/images/fxpic260.jpg" alt="" width="100%">
			</p>
			<div class="tc pt2 pb2">
				<p class="color666 f12">立即分享活动、直播等商机条目赚取丰厚积分、佣金</p>
				<button class="share_btn">分享赚佣</button>
			</div>
		</div>
		<div class="pb2">
			<p>奖励须知：</p>
			<p>1.每次转发奖励50积分，最多得50积分；</p>
			<p>2.转发后每产生一次阅读，即送10积分，最多可得1000积分；</p>
			<p>3.转发后每产生一次直播或录播观看（观看时间超过10分钟）可得50积分，最多可得2500积分；</p>
			<p>4.每产生一个有效的活动报名用户，可以得到10元奖励，最高可得1000元；</p>
			<p>5.报名用户来到活动现场签到确认后还可得到40元/人次的奖励；</p>
			<p>6.所得现金奖励会在数据确认后发放到您的账户余额，随时可以发起提现；</p>
			<p>7.以上规则对应每一个分享任务有效；</p>
			<p>8.严禁通过刷单软件、外挂等工具手段套取积分或奖励，一旦查实，视为无效。</p>
		</div>
	</section>
@stop
@section('endjs')
	<script type='text/javascript'>
		$(document).on('click','.share_btn',function(){
			shareEarned();
		})
		function shareEarned() {
		    if (isAndroid) {
		        javascript:myObject.shareEarned();
		    }else if (isiOS) {
		        window.webkit.messageHandlers.shareEarned.postMessage('noparam');
		    }
		}
	</script>
@stop