@extends('layouts.default')
<!--zhangx-->
@section('css')
    <style type="text/css">
	.points_li{
		margin-top: 2.3rem;
		padding: 0 1.1rem;
		text-align: left;
	}
	.points_title {
		font-size: 1.5rem;
		color: #000000;
		margin-bottom: 1.25rem;
	}
	.points_cont {
		font-size: 1.3rem;
		color: #666666;
		line-height: 1.9rem;
		margin-left: 1.65rem;
	}
    </style>
@stop
<!--zhangxm-->
@section('main')
	<section id='container'>
		<div class="points">
			<ul class="points_ul">
				<li class="points_li">
					<p class="points_title ">1. 每日打卡</p>
					<p class="points_cont">首日获得1分，次日为2分，依次累加，至最高分7分后，再次由1分累加统计。如有打断，则重新统计，以一个标准周7天为统计进行统计。</p>
				</li>
				<li class="points_li">
					<p class="points_title ">2. 分享资讯、视频等</p>
					<p class="points_cont">分享无界商圈资讯、项目、录播视频、海报、个人名片、话术随声听、OVO活动、活动直播至朋友圈，均能获得相应的分享奖励。每次分享获得1分，最高每日5分。</p>
				</li>
				<li class="points_li">
					<p class="points_title ">3. 点赞、评分</p>
					<p class="points_cont">对无界商圈资讯等进行点赞或评论留言，均能获得1分奖励，最高每日5分。</p>
				</li>
				<li class="points_li">
					<p class="points_title ">4. 学习奖励</p>
					<p class="points_cont">阅读新手学院内的商圈热文（知识树）、视频课堂，话术随声听及精品专栏内的资讯、视频，均能获得学习奖励，均给予1分奖励。该奖励为首次奖励，再次阅读同一篇资讯等均不给于重复奖励。此外，最高每日5分。</p>
				</li>
				<li class="points_li">
					<p class="points_title ">5. 报名OVO活动</p>
					<p class="points_cont">报名OVO活动，获得相应1分经验值奖励。</p>
				</li>
				<li class="points_li">
					<p class="points_title ">6. 参加OVO活动（完成签到）</p>
					<p class="points_cont">参加无界商圈OVO活动，并完成相应的活动签到，获得活动奖励20分。</p>
				</li>
				<li class="points_li">
					<p class="points_title ">7. 发送活动邀请函</p>
					<p class="points_cont">发送活动邀请函至投资人，获得经验值1分奖励。</p>
				</li>
				<li class="points_li">
					<p class="points_title ">8. 发放考察邀请函</p>
					<p class="points_cont">发送考察邀请函至投资人，获得经验值2分奖励。</p>
				</li>
				<li class="points_li">
					<p class="points_title ">9. 发放加盟合同</p>
					<p class="points_cont">发送加盟合同至投资人，获得经验值2分奖励。</p>
				</li>
				<li class="points_li">
					<p class="points_title ">10. 获得投资人手机号。</p>
					<p class="points_cont">获得派单投资人的手机号，奖励经验值5分。</p>
				</li>
				<li class="points_li">
					<p class="points_title ">11. 成功邀请投资人参加活动。</p>
					<p class="points_cont">投资人成功接受了活动邀请函。获得经验值奖励5分。</p>
				</li>
				<li class="points_li">
					<p class="points_title ">12. 成功邀请投资人考察品牌。</p>
					<p class="points_cont">投资人成功接收了考察邀请函，并缴纳保证金。获得经验值奖励20分。</p>
				</li>
				<li class="points_li">
					<p class="points_title ">13. 成功邀请投资人加盟品牌</p>
					<p class="points_cont">投资人成功接收合同邀请函，并加盟品牌。获得经验值奖励50分。</p>
				</li>
				<li class="points_li">
					<p class="points_title ">14. 接受派单咨询任务</p>
					<p class="points_cont">获得新的派单投资人，获得经验值奖励2分。</p>
				</li>
				<li class="points_li">
					<p class="points_title ">15. 邀请投资人</p>
					<p class="points_cont">成功邀请好友注册商圈投资人端，成为商圈投资人，获得经验值2分/人奖励。</p>
				</li>
				<li class="points_li">
					<p class="points_title ">16. 发展团队</p>
					<p class="points_cont">成功发展团队，邀请自己的好友成为经纪人，并成为自己的下线成员。获得经验值3分/人奖励。</p>
				</li>
				<li class="points_li">
					<p class="points_title ">17. 完成品牌学习</p>
					<p class="points_cont">完成品牌学习内容，获得经验值奖励1分。</p>
				</li>
				<li class="points_li">
					<p class="points_title ">18. 获得品牌代理</p>
					<p class="points_cont">通过电话考核，获得品牌的代理，获得5分经验值奖励。</p>
				</li>
				<li class="points_li">
					<p class="points_title ">19. 进行bug反馈</p>
					<p class="points_cont">进行bug反馈或意见提交，每次获得1分。每日最高2分。</p>
				</li>
				<li class="points_li">
					<p class="points_title ">20. 完善个人资料</p>
					<p class="points_cont">完成个人资料的全部内容填写，给予2分奖励。</p>
				</li>
				<li class="points_li">
					<p class="points_title ">21. 实名认证</p>
					<p class="points_cont">完成实名认证，获得5分奖励。</p>
				</li>
			</ul>
		</div>
	</section>
	<section style="height: 10rem;"></section>
@stop
@section('endjs')
    <script type="text/javascript">
    	$(document).ready(function(){
    		$('title').text('积分规则');
    	});
    </script>
@stop