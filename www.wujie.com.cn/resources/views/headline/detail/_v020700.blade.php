@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/headline_detail.css" rel="stylesheet" type="text/css"/>
    <style>
        .spread{ 
            margin: 1.5rem auto;
        }
    </style>
@stop

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
	<section id='container' class="none">
		<!-- <div class="banner"></div>
            <div class="intro">
                <h1 class="title fline">四川小伙800万买退役波音737收藏 卖家还不包邮</h1>
                <p class="some06">作者：<span>无界商圈</span><i>2016-11-08 14:30</i></p>
                <p class="space"></p>
                <div class="detail ">
                    <p class=" f16 fline ptb1-4 b">详情介绍</p>
                    <div class="content">
                       
                    </div>
                   <div> 展开全文 还剩<span class="percent">()</span>  </div> -->
            <!--  </div>
            </div>
            <div class="white-bg mt1-33 pl1-33 mb10">
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
            </div> --> 
            
	</section>
    <section class="enjoy none " style='padding-bottom:10rem'>
    </section>
    <section style="border-top:1px solid transparent"></section>
@stop
@section('endjs')
    <!--<script src="http://html5media.googlecode.com/svn/trunk/src/html5media.min.js"></script>-->
    <script src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
    <script src="{{URL::asset('/')}}/js/_v020700/headline.js"></script>
	 <script>	
        
   		//分享
        function showShare() {
            // shareOut('title', window.location.href, '', 'header', 'content');
            var type='news';
            var title = $('.title').text();
            var url = window.location.href;
            var img =  $('#container').data('logo');
            var header = '资讯';
            var content = cutString(removeHTMLTag($('#content').text()), 18);
            var id={{$id}};
            var share_mark=$('#container').data('sharemark');
            var url = window.location.href+'&share_mark='+share_mark;
            var p_url=labUser.api_path+'/index/code/_v020500';
                ajaxRequest({},p_url,function(data){
                    if(data.status){
                        var code=data.message;
                        url+= '&code=' +code;
                        if($('#share').data('reward')==1){   
                            shareOut(title, url, img, header, content,'','','',type,share_mark,code,'share','news',id);
                        }else{
                            shareOut(title, url, img, header, content,'','','','','','','','','');
                        }                    
                    }
                    
                })
        };
   </script>
@stop