@extends('layouts.default')
<!--zhangxm-->
@section('css')
    
    <style>
        p {
        	margin-bottom: 0;
        }
        .button {
        	width: 100%;
        	height: 32.1rem;
        	background-image: url(/images/agent/1q04.png);
        	background-size: 100% 100%;
        	display: flex;
        	justify-content: center;
        }
        .btn_share {
        	border-radius: 1rem;
        	background-image: url(/images/agent/1qbutton.png);
        	background-size: 100% 100%;
        	color: #178d42;
        	width: 16rem;
			height: 4.8rem;
			font-size: 1.7rem;
			line-height: 4.8rem;
			text-align: center;
			display: inline-block;
			margin-top: 20rem;
        }
    </style>
@stop
<!--zhangxm-->
@section('main')
 	<section >
    <!--安装app-->
        <div class="app_install fixed none" id="installapp">
            <i class="l">打开无界商圈AgentAPP，体验更多精彩内容 >></i>
            <span class="r" id="openapp" style="width:8.66rem"><img class="r" src="{{URL::asset('/')}}/images/opennow.png" alt=""></span>
            <div class="clearfix"></div>
        </div>
        <div class="share pl1-33 pr1-33 share_0502 none" id="share" style='display: none'>
            <p class="f12 l">分享资讯，立即获得100积分</p>
            <button class="ff5 l f12 understand"><img src="{{URL::asset('/')}}/images/020502/notice.png" alt="">了解分享规则介绍</button>
            <span class="fff f16 r close_share"><img src="{{URL::asset('/')}}/images/share_close.png" alt=""></span>
        </div>
    <!--浏览器打开提示-->
        <div class="safari none">
            <img src="{{URL::asset('/')}}/images/safari.png">
        </div>
        <button class="loadapp f16 none" id="loadapp">
            <img src="{{URL::asset('/')}}/images/agent/dock-logo.png" alt="">下载APP
            <!-- <iframe style="position:absolute; visibility:inherit; top:0px; left:0px; width:100%; height:100%; z-index:9999; filter='Alpha(style=0,opacity=0)';"></iframe> -->
        </button>
    <!-- 蒙层 -->
        <div class="fixed-bg none"></div>
        <div class="alert none">
            <p></p>
        </div>
    </section>
	<section id='container' class="medium">
		<div class="special">
			<p class=""><img src="/images/agent/1q01.png" alt="" class="" /></p>
			<p class="brand_detail"><img src="/images/agent/1q02.png" alt="" class="" /></p>
			<p class=""><img src="/images/agent/1q03.png" alt="" class="" /></p>
			<p class="button"><span class="btn_share">邀请投资人</span></p>
		</div>
	</section>
	
@stop
@section('endjs')
    <!--<script src="{{URL::asset('/')}}/js/agent/_v010200/headline.js"></script>-->
	<script>
		
			new FastClick(document.body);
			$(document).ready(function(){
				$('title').text('壹Q鲜品牌盛典');
			});	
			$(document).on('click','.brand_detail',function(){
				window.location.href = labUser.path+'/webapp/agent/brand/detail/_v010200?id=120'
			});
			$(document).on('click','.btn_share',function(){
				showShare();
			});
	        //分享
	        function showShare() {
	        	var args=getQueryStringArgs(); 
	            var type='yiq';
	            var title = '商机来了 “钱”力 无限';
	            var img = labUser.path+'images/agent/dock-logo.png';
	            var header = '';
	            var content = '壹Q鲜品牌盛典，你开店我出钱，名额仅有10个，先到先得！';
	            var id=id;
	            var url = labUser.path+'webapp/activity/yiqxian/_v020901?id=120&is_share=1';
	            var weibo =  wechat = '壹Q鲜品牌盛典，你开店我出钱，疯狂让利，劲爆低价，名额仅有10个，点击速抢！';
	            agentShare(title, url, img, header, content,type,id,weibo,wechat);
	            console.log(222)
	        };
	        function reload(){
	            location.reload();
	        }
	        function Refresh(){
	            reload();
	            $('body').scrollTop($('body')[0].scrollHeight);
	       }
		
		
    </script>
@stop