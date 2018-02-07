@extends('layouts.default')
<!--zhangx-->
@section('css')
    <style>
    	#container {
    		background-image: url(/images/agent/zhuanqian.png);
    		padding-top: 18.4rem;
    		background-size: 100% 100%;
    		padding-bottom: 13.8rem;
    	}
    	.plateOne {
    		background-image: url(/images/agent/zhuanqian1.png);
    		background-size: 100% 100%;
    		width: 34.5rem;
    		height: 54.8rem;
    		
    	}
    	.plateTwo {
    		background-image: url(/images/agent/zhuanqian2.png);
    		background-size: 100% 100%;
    		width: 34.5rem;
			height: 50.45rem;
    	}
    	.btn {
    		width: 34.5rem;
			height: 4.8rem;
			background-color: #f4d25d;
			border-radius: 0.2rem;
			margin-top: 2.8rem;
			color: #cf3a40;
			font-size: 2.25rem;
			text-align: center;
			line-height: 4.8rem;
    	}
    	.plateOne,.plateTwo,.btn {
    		margin-left: auto;
    		margin-right: auto;
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
    <!--浏览器打开提示-->
        <div class="safari none">
            <img src="{{URL::asset('/')}}/images/safari.png">
        </div>
        <button class="loadapp f16 none" id="loadapp">
            <img src="{{URL::asset('/')}}/images/agent/dock-logo.png" alt="">下载APP
        </button>
    <!-- 蒙层 -->
        <div class="fixed-bg none"></div>
        <div class="alert none">
            <p></p>
        </div>
    </section>
	<section id='container' class="">
		 <div class="plateOne"></div>
		 <div class="plateTwo mt3"></div>
		 <p class="btn">立即邀请</p>
	</section>
    <section class="enjoy" style="background-color: #f2f2f2;">
    	<div class="common_pops none"></div>
    </section>
    <section style="border-top:1px solid transparent"></section>
@stop
@section('endjs')
	<script type="text/javascript" src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
    <!--<script src="{{URL::asset('/')}}/js/agent/_v010400/newyear.js"></script>-->
	<script>
		new FastClick(document.body);
		$(document).ready(function(){
			$('title').text('赚钱攻略');
		});	
        //分享
        function showShare() {
        	var args=getQueryStringArgs(),
        		agent_id = args['agent_id'];  
            var type='';
            var title = $('title').text();
            var img =  $('#container').attr('logo');
            if(img==''){
            	img=labUser.path+'images/agent/dock-logo.png';
            }
            var header = '压轴盛宴，礼见新年';
            var summary = cutString($('#content').attr('summary'), 18);
            var content = '在一起，过福年，集齐五福共分钱！';
            if(content==''){
            	content = summary;
            }
            var id=agent_id;
            var url = window.location.href;
            if(summary==''){
            	shareOut(title, url, img, header, content,'','',id,type,'','','','','');
            }else {
            	shareOut(title, url, img, header, summary,'','',id,type,'','','','','');
            };
        };
       	$(document).on('click','.btn',function(){
       		showShare();
       	});
    </script>
@stop