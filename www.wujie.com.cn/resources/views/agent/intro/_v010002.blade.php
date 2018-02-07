@extends('layouts.default')
@section('css')
    <!--<link href="{{URL::asset('/')}}/css/agent/wjload.css" rel="stylesheet" type="text/css"/>-->
    <style type="text/css">
    	#container {
    		text-align: center;
    	}
    	body {
    		background: #FFFFFF;
    	}
    	p {
    		margin-bottom: 0;
    	}
    	.text,.text_img {
    		font-size: 1.3rem;
    		line-height: 2rem;
    		background: #fff;
    	}
    	.text {
    		text-align: left;
    		padding-left: 2rem;
    		padding-right: 2rem;
    	}
    	.imgs {
    		width: 100%;
    	} 
    	.imgs img {
    		width: 80%;
    	}
    </style>
@stop
@section('main')
    <section id="container" class="container">
    		<h3>商圈简介</h3>
        <p class="text pt2 pb2">无界商圈OVO品牌招商推广平台，隶属于天涯若比邻网络信息服务有限公司，成立于2012年，总部位于浙江杭州。下设四大区域运营中心（杭州、广州、成都、北京）和100+城市网点。是一家互联网+综合商业服务与跨域资源共享平台。</p>
        <p class="text pb2">公司运用国际领先的天涯云网真视频会议系统和互联网直播技术，独创OVO（online-video-offline）场景化招商服务模式，解决跨域（时间和空间）信息传递，提供了一套综合化的解决方案，让信息得以高效快速地实现连接、共享、传播，实现优质资源匹配。目前无界商圈服务有：品牌招商服务、培训服务、政府智慧服务、投融资对接服务、海外项目服务、第三方服务 等6大行业服务方向。</p>
        <p class="imgs "><img src="/images/agent/u2368.png"/></p>
        <p class="text_img pt2">无界商圈生态图</p>
        <p class="text pt2 pb2">无界商圈既发轫招商业务以来，基于互联网开放性特点，使用前沿的信息技术，线上线下多维布局：线上移动端、网页端、微信端三位一体，构建完整的线上平台；线下以杭州总部为核心的四大区域运营中心领衔，衔接关联数百个城市商机速配中心，点线面全维度虹吸城市供需商机。同时以招商经纪人、OVO跨域场景化招商会、分享分销等作为核心行为角色，贯穿线上线下，构建完整的无界商圈招商生态圈。</p>
        <p class="imgs "><img src="/images/agent/u2366.jpg"/></p>
        <p class="text_img pt2">无界商圈经纪人运作图</p>
        <p class="text pt2 pb2">凭借成熟的商业生态、领先的商业模式、完备的运作流程、专业的服务团队，无界商圈短期内先后成功服务韩尚宫、谢蟹浓、壹Q鲜等多个知名连锁品牌，取得了令人惊喜的招商效果，并通过入驻无界商圈生态，招商效果进行长期发酵，后期的成果更为可观。</p>
        <p class="text pb2">无界商圈秉持客户利益第一的原则，敢于承诺以效果说话、以成果收费，只做有实效的招商平台，真诚欢迎广大品牌洽谈合作，共创辉煌明天。</p>
    </section>
@stop

@section('endjs')
	<script>
		$(document).ready(function(){
			$('title').text('商圈简介');
		});
	</script>
@stop