<!--zhangxm-->
@extends('layouts.default')
@section('css')
<!--<link href="{{URL::asset('/')}}/css/agent/_v010004/demo.css"  rel="stylesheet"/>
<link href="{{URL::asset('/')}}/css/agent/_v010004/audioplayer.css" rel="stylesheet" type="text/css"/>-->
<link href="{{URL::asset('/')}}/css/agent/_v010004/walkman_detail.css" rel="stylesheet" type="text/css"/>

@stop
@section('main')

<section id="container" class="container">
	<!-- 头部 -->
	<div class='walkplay_header'>
		<img src="" alt="" class="">
	</div>
	<div class="walkplay_wrap">
		<ul>
			<li>
				<p class="walkplay_title">

				</p>
			</li>
			<li>
				<p class="weixinAudio">
					<audio src="" id="media" width="1" height="1" preload ></audio>
					<span id="audio_area" class="db audio_area">
						<span class="audio_wrp db">
							<span class="audio_play_area">
								<i class="icon_audiodefault"><img src="/images/agent/shengyin.png" alt=""></i>
								<i class="icon_audioplaying"><img src="/images/agent/shengyin.gif" alt=""></i>
		            		</span>
		            		
							<span id="audio_length" class="audio_length tips_global none"></span>
							<span id="none_audio_len"></span>
							<span id="curent_time">00:00</span>
							<span class="db audio_info_area">
		                		<strong class="db audio_title"></strong>
							</span>
							<span id="int_c"></span>
							<span class="progress_bar_box" style="width: 24.2rem;">
								<span id="audio_progress" class="progress_bar" style="width: 0%;"></span>
							</span>
						</span>
					</span>
		
				</p>
	
				<!--<div class="walkplay_player">
				<audio src="" controls>
				<source src=""></source>
				你的游览器不支持
				</audio>
				</div>-->
			</li>
			<li class="detail">
				<!--<p>
					主题就是让“中产阶级”通过人群孤立和人群恐惧的精神洗脑，对人群进行隔离，从而让这一部分被隔离的人群都可以根据数据标准在物质上获得慰藉。
				</p>
				<p>
					大家想一想，是不是我们越来越需要一种东西，叫做个人空间。而且我们正在越来越依赖这个空间的数据标准给自己制定价值？
				</p>
				<p>
					房屋地段，车子型号，飞机位置，餐厅包间，这是物理的私人空间。各种不用说话，不用再面对面就可以满足自己需求的硬件以及软件，是心理上的私人间。
				</p>
				<p>
					我们越来越无所谓三次元的人了。也许我们隔壁邻居生病，也不如一只网红猫的照片分享的自我精力多。
				</p>-->

			</li>
	</div>
</section>
<section>
	 <div class="install none" id="installapp">
          <p class="l">打开无界商圈AgentAPP，体验更多精彩内容 >> </p>
          <!--蓝色图标-->
          <span class="r" id="openapp" style="width:8.66rem"><img class="r" src="{{URL::asset('/')}}/images/opennow.png" alt="">
          </span>
          <div class="clearfix"></div>
     </div>
     <div class="tips none"></div>
	<!--  分享用 -->
      <input type="hidden" id="share">
         <!--浏览器打开提示-->
      <div class="safari none">
          <img src="{{URL::asset('/')}}/images/safari.png">
      </div>
      <div class="isFavorite"></div>
      <button class="loadapp f16 none" id="loadapp">
            <img src="{{URL::asset('/')}}/images/agent/dock-logo.png" alt="">下载APP
      </button>
</section>
@stop

@section('endjs')
<script src="{{URL::asset('/')}}/js/agent/jquery-3.0.0.min.js"></script>
<script src="{{URL::asset('/')}}/js/agent/_v010004/weixinaudio.js"></script>
<script src="{{URL::asset('/')}}/js/agent/_v010004/workmandetail.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('title').text('话术随身听');
	});
	//分享
        function showShare() {
        	var id=id;
            var url = window.location.href;
        	var type='walkmen',
                title = $('.walkplay_title').text(),
                img = labUser.path+'images/agent/dock-logo.png',
                header = '话术随身听',
                content = '最全经纪人话术宝典！一分钟搞定客户，教你快速成单秘诀。',
                sharecontent=$('#container').attr('sharecontent'),
                contain=sharecontent.length<20?sharecontent:sharecontent.substr(0,20)+'…',
                weibo='最全经纪人话术宝典！一分钟搞定客户，教你快速成单秘诀，点击收听！';
                wechat='最全经纪人话术宝典！一分钟搞定客户，教你快速成单秘诀，点击收听！';
            if(sharecontent){
            	agentShare(title, url, img, header, contain,type,id,weibo,wechat);
            }else{
            	agentShare(title, url, img, header, content,type,id,weibo,wechat);
            };
            audioPause();
        };
        function audioPause(){
			$('#media')[0].pause();
			$(".icon_audiodefault").css({"display":"block"});
			$(".icon_audioplaying").css({"display":"none"});
		};
</script>
@stop