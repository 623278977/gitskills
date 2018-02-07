@extends('layouts.default')
<!--zhangx-->
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/_v010004/headline.css" rel="stylesheet" type="text/css"/>
    <style>
        .spread{ 
            margin: 1.5rem auto;
        }
    </style>
@stop
<!--zhangxm-->
@section('main')
 	<section >
    <!--安装app-->
        <div class="app_install fixed none" id="installapp">
            <i class="l">打开无界商圈APP，体验更多精彩内容 >></i>
            <span class="r" id="openapp"><img src="{{URL::asset('/')}}/images/020502/install_btn.png" alt=""></span>
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
            <img src="{{URL::asset('/')}}/images/dock-logo2.png" alt="">下载APP
            <!-- <iframe style="position:absolute; visibility:inherit; top:0px; left:0px; width:100%; height:100%; z-index:9999; filter='Alpha(style=0,opacity=0)';"></iframe> -->
        </button>
         <div class="fixed_btn none">
            <button class="weizan_07 w50 headzan" id="zannum"></button>
            <!-- <button class="nothks_07 w33">感谢作者</button> -->
            <button class="comment_07 w50" id="comment_num"></button>
        </div>
    <!-- 蒙层 -->
        <div class="fixed-bg none"></div>
        <div class="alert none">
            <p></p>
        </div>
    </section>
    
	<section id='container' class="bgwhite">
		 <div class="head fline bgwhite">
		 	<span class="f20 color section_title"></span>&nbsp;
		 	<span class="f20 color headline_title"></span>
		 </div>
		 <div class="detail">
		 	
		 </div>
		 <div class="tipsfor none">完成阅读学习，点击“完成学习”参与小测试</div>
         <div class="triangle none"></div>
         <div style="width:100%;height:7rem"></div>
         <button class="ui-fixed-button f15 b none">完成学习，参与测试</button>
         <button class="again_xuexi f15 none">已完成学习</button>
	</section>
    <section class="enjoy" style="padding-bottom:5rem;background-color: #FFFFFF;">
    </section>
    <section style="border-top:1px solid transparent"></section>
@stop
@section('endjs')
    <!--<script src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>-->
    <script src="{{URL::asset('/')}}/js/agent/_v010004/headline.js"></script>
	<script>
		$(document).ready(function(){
			$('title').text('资讯学习');
		});	
        //分享
        function showShare() {
            var type='news';
            var title = $('.title').text();
            var img =  $('#container').attr('logo');
            var header = '资讯';
            var summary = cutString($('#content').attr('summary'), 18); ;
            var content = cutString(removeHTMLTag($('#content').text()), 18);
            if(content==''){
            	content = summary;
            }
            var id=id;
            var url = window.location.href;
            if(summary==''){
            	shareOut(title, url, img, header, content,'','',id,type,'','','','','');
            }else {
            	shareOut(title, url, img, header, summary,'','',id,type,'','','','','');
            };
        };
//      var f=window.localStorage.getItem('e');
//		    if(f==null){
//		        delay(); 
//		    };
//		function delay(){
//	          $('.tipsfor,.triangle').removeClass('none');
//	           setTimeout(function(){
//	              $('.tipsfor,.triangle').addClass('none');
//	            },5000)
// 		};
// 		setTimeout(function(){
//        $('.tipsfor,.triangle').addClass('none');
//      },5000);
// 		if(!window.localStorage){
//          alert("浏览器不支持支持localstorage");
//        }else{
//          var storage=window.localStorage;
//              storage["e"]=1;
//        }   
    </script>
@stop