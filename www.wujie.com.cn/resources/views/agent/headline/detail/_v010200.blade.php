@extends('layouts.default')
<!--zhangx-->
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/headline.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/agent/vod.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/agent/_v010004/hotmessage.css" rel="stylesheet" type="text/css"/>
    <style>
        .spread{ 
            margin: 1.5rem auto;
        }
        
		.nocomment{
		    width: 13rem;
		    margin: 0 auto;
		    padding-top: 10rem;
		}
		/*1.0.5新增*/
		.comment_tip{
			background: #2a2a2a;
		    /*display: block;*/
		    width: 13rem;
		    height: 3.6rem;
		    border-radius: 1rem;
		    color: #fff;
		    text-align: center;
		    line-height: 3.6rem;
		    position: absolute; 
		    top: -7rem;
		    right: 0rem;
		}
		/*小三角*/
		.comment_tip::after{
			content: '';
			width:0;height: 0;
			border-top:0.9rem solid #2a2a2a;
			border-right:0.9rem solid transparent;
			border-bottom:0.9rem solid transparent;
			border-left:0.9rem solid transparent;
			position: absolute;
			bottom:-1.6rem;
			right:3rem;
		}
		.change_position::after{
			left:1rem;
		}
		.comment_tip em{
			display: inline-block;
			width: 50%;
			font-size: 1.3rem;
		}
		.reply{
			border-right:1px solid #fff;
		}

		.copy_suc{
			width:9rem;
			/*height: 9rem;*/
			border-radius:1rem;
			background: #2a2a2a;
			position: fixed;
			padding-top: 2em;
			top:50%;margin-top: -4.5rem;
			left:50%;margin-left: -4.5rem;
			z-index: 10;
			transition: all 0.5s;
			-webkit-transition:all 0.5;	
			-moz-transition:all 0.5;	
			-o-transition:all 0.5;	
		}
		.copy_suc img{
			width: 2.2rem;
			height: 2.2rem;
			margin-bottom: 1rem;
		}
		.comment_text{
			word-break: break-all;
			display: block;	
		}
		.break-word{
			word-wrap: break-word;
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
         <!--<div class="fixed_btn none">
            <button class="weizan_07 w50 headzan" id="zannum"></button>
            <button class="comment_07 w50" id="comment_num"></button>
        </div>-->
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
	<section style="background: #FFFFFF;" class="zan_zhuan none">
		<!-- 点赞和转发 -->
                    <ul class="ui-zan-zhaun">
                      <li>
                          <center>
                                  <button class="ui-forzan headzan">
                                    <p></p>
                                    <img class="ui-pict6 dian-zan" src="/images/agent/ui_pict.png"/>
                                    <p class="color2873ff f13 ui-margin5" id="zannum"></p>
                                  </button>
                          </center>
                        <p class="f13 color999 ui-margain2 " >点赞鼓励</p>
                      </li>
                      <li>
                        <center><button><center><img class="ui-pict7" src="/images/agent/zhuan.png"/></center></button></center>
                        <p class="f13 color999 ui-margain2 zhuan">分享好友</p>
                      </li>
                    </ul>
	</section>
	<section>
		<div class="conmments mt2-5 none">
				<img src="{{URL::asset('/')}}/images/novideo.png" alt="" class="nocomment none">
                <div class="comment none fline">
                    <ul id="comment" class="pr1-33 commentJump" >
                    	<!-- <li>
                    		<img src="http://test.wujie.com.cn/attached/image/20170424/20170424094758_41819.jpg" alt="header" class="l">
                    		<div class="publisher r">
                    			<p class="f16 color666 b lh3-3 m0">愿是阳光
                    				<span class="r laub lh3-3">	
                    					<img src="/images/littlewz.png" data-zan="0" data-id="2473">
                    					<em data-zannum="0">0</em>
                    				</span></p>
                    			<p class="c8a f12">OK</p>
                    			<p class="time">04月24日 09:46</p>
                    		</div>
                    		<div class="clearfix"></div>
                    	</li> -->
                    	
                    </ul>
                </div>
                <button class="getMore f12 c8a" style="margin-bottom: 1rem;"><img class="h_gif" style="width:1.2rem;height:1.2rem" src="/images/agent/h.gif"/ >正在加载</button>
        </div>
		<!--<div id="comment_btn" class="comment_btn">
			<span type="button" class="tl" style="width: 26rem;">我来说两句...</span>
			<span class="uploadpic1"></span><i class="uploadpictext f12">发表图片</i>
		</div>-->
		<div id="comment_btn" class="comment_btn ui-fixed-botton fixed-bottom-iphoneX">
			<!--<span type="" class="tl" style="width: 26rem;">我来说两句...</span>-->
			<input placeholder="写评论…" readonly="readonly">
			<button class="uploadpictext f12 fr">评论</i>
		</div>
		<!--<div class="ui-fixed-botton comment_btn" >
            <input placeholder="写评论…" readonly="readonly"><button class="fr">评论</button>
        </div>-->
		 <div class="copy_suc tc none">
	      	<img src="/images/agent/success.png" style="">
	      	<p class="white f12 ">已复制</p>
	    </div>
	</section>
    <section class="enjoy" style="padding-bottom:5rem;background-color: #f2f2f2;">
    	<div class="common_pops none"></div>
    </section>
    <section style="border-top:1px solid transparent"></section>
@stop
@section('endjs')
    <!--<script src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>-->
    <script src="{{URL::asset('/')}}/js/agent/_v010200/headline.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/agent/_v010200/reply.js"></script>
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
        function reload(){
            location.reload();
        }
        function Refresh(){
            reload();
            $('body').scrollTop($('body')[0].scrollHeight);
       }
    </script>
@stop