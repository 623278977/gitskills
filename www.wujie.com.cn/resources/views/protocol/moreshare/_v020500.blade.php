@extends('layouts.default')
@section('css')
@stop
@section('main')
	<section class="pl1-33 pr1-33 f14">
		<h2 class="f16 b tc pt2 pb2">无界商圈分享规则</h2>
		<!--
		<div class=" pb2">
			<p class="b">一、分销流程</p>
			<p>分销的最终目的是促成项目的加盟和推广，分销流程以下：</p>
				<p>1.	分享（微信好友、微博、朋友圈、QQ空间、QQ好友）</p>
				<p>2.	产生报名</p>
				<p>3.	报名用户到现场签到 OR 产生直播或录播观看</p>
				<p>4.	产生加盟意向</p>
				<p>5.	加盟成交</p>
				<p>6.	获得无界币</p>
		</div>
		<div class="pb2">
			<p class="b">二、奖励原则</p>
			<p>整体奖励机制包括两大原则</p>
			<p>1.	转发分享且产生报名，现场每产生一个签到用户，即奖励本人若干（1-50）个无界币；</p>
			<p>2.	严禁通过刷单软件、利用外挂工具等手段进行无界币的获取与提现，一旦查实，即没收违规所得无界币。</p>
		</div>
		<div>
			<p class="b">三、奖励机制</p>
			<p>积分奖励：</p>
			<p>1.	每次转发奖励100积分，最多可得1000积分；</p>
			<p>2.	转发后每产生一个阅读，即送20积分，最多可得2000积分；</p>
			<p>3.	转发后每产生1个直播或录播观看（观看时间超过10分钟）可得500积分，最多可得5000积分；</p>
			<p class="pt2">无界币奖励：</p>
			<p>4.	每邀请产生一个有效线下报名用户，本人可获奖10个无界币，无上限（但实际到场签到率需超过70%，否则视为邀请无效）；</p>
			<p>5.	每邀请产生一个到场签到人员，本人可获奖40个无界币；</p>
			<p class="pb2">6.	每产生一个有效“品牌加盟意向”，本人可获奖30个无界币；</p>
		</div>
		-->
		<div class="pb2">
			<img src="{{URL::asset('/')}}/images/fxpic260.jpg" alt="" width="100%">
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
@stop