@extends('layouts.default')
<!--zhangx-->
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/headline.css" rel="stylesheet" type="text/css"/>
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
	<section id='container' class="none medium">
		 <!--<div class="banner"></div>
            <!--<div class="intro">
                <h1 class="title fline">四川小伙800万买退役波音737收藏 卖家还不包邮</h1>
                <p class="some06">作者：<span>无界商圈</span><i>2016-11-08 14:30</i></p>
                <p class="space"></p>
                <div class="detail ">  
                    <p class=" f16 fline ptb1-4 b">详情介绍</p>
                    <div class="content">
                       
                    </div>
                   <div> 展开全文 还剩<span class="percent">()</span>  </div> 
              </div>-->
            <!--</div>-->
            <!--<div class="white-bg mt1-33 pl1-33 mb10">
                <p class="f16 fline ptb1-4 b">相关品牌</p>
                <div class=" brand-company pl1-33 " data-brand=''>
                    <img src="" alt="" class="company mr1-33 fl">
                    <div class="fl width70 ">
                        <em class="service f12 mr1">
                            服装品牌
                        </em>
                        <span class="f14 b">品牌一号</span> 
                        <div class="brand-desc f12 color999 mb05 ui-nowrap-multi">
                            品牌一号品牌一号品牌一号品牌一号品牌一号品牌一号
                        </div>
                        <p class="f12 mb05"><span class="c8a">投资额：</span><span class="color-red">15-22</span>
                        	
                        </p>
                        <a class="tags-key border-8a-radius">组五个字</a>
                    </div>
                     <div class="clearfix"></div>
                </div>
            </div>  -->
            <!--<span class="c8a">投资额：</span><span>15-22</span>-->
	</section>
    <section class="enjoy" style="padding-bottom:5rem;background-color: #f2f2f2;">
    	<div class="common_pops none"></div>
    </section>
    <section style="border-top:1px solid transparent"></section>
@stop
@section('endjs')
    <!--<script src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>-->
    <script src="{{URL::asset('/')}}/js/agent/headline.js"></script>
	<script>
		$(document).ready(function(){
			$('title').text('资讯详情');
		});	
        //分享
        function showShare() {
        	var args=getQueryStringArgs(); 
            var type='news';
            var title = $('.title').text();
//          if($('#container').attr('logo')==''){
//          	$('#container').attr('logo','labUser.path'+'/images/agent/dock-logo.png');
//          };
            var img =  $('#container').attr('logo');
//          var imga=labUser.path+'images/agent/dock-logo.png';
            if(img==''){
            	img=labUser.path+'images/agent/dock-logo.png'
            }
            var header = '资讯';
            var summary = cutString($('#content').attr('summary'), 18);
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
    </script>
@stop