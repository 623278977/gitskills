@extends('layouts.default')
<!--zhangx-->
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/agent_detail.css" rel="stylesheet" type="text/css"/>
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
    <!--内容-->
	<section id='container'>
		<!--<div class="share-head f36 fline mb2-5 none">
			无界商圈经纪人
		</div>
		<div class="personal" class="mudium">
			<img src="" alt="" class="company mr1-33 fl xm-inb"/>
			<div class="xm-amounts xm-inb xm-detail">
				<span class="b f15 color3 bold">张长</span>
				<span class="rank f11"><img src="/images/agent/relation.png"/>我的下级</span><br />
				<span class="badge"><img src="/images/agent/badge_07.png"/></span>
				<span class="color699 f12 xm-common">普通经纪人</span><br />
				<img src="/images/head_0205.png" class="gender xm-inb fl"/>
				<span class="f12">浙江</span>
			</div>
			<p class="keyword fline">
				<span class="keywords m05 f11 color-years">特色餐饮</span>
				<span class="keywords m05 f11 color-years">特色餐饮</span>
				<span class="keywords m05 f11 color-years">特色餐饮</span>
				<span class="keywords m05 f11 color-years">特色餐饮</span>
				<span class="keywords m05 f11 color-years">特色餐饮</span>		
			</p>
			<div class="f12 color6 xm-sign ui-nowrap-multi mudium">“我的签名我的签名我的签名我的签名”</div>
		</div>
		<div class="pl1-5 bgwhite">
			<p class="fline mrl1-5"><span class="bold f15 b color333">促单业绩</span></p>
			<p class="medium f12 color333 grade pr1-5 mrl1-5"><span class="">本季度已成单</span><span class="">1单</span></p>
			<p class="medium f12 color333 grade pr1-5 mrl1-5"><span class="">当前跟单客户</span><span class="">12人</span></p>
			<p class="medium f12 color333 grade pr1-5 mrl1-5"><span class="">累计成单</span><span class="">3单</span></p>
		</div>
		<div class="personals Medium">
				<p class="f15 fline ptb1-4 b keyword bold">
					代理品牌
					<span class="keyword-num">&nbsp;(100)</span>
				</p>
				<div class="xm-acting xm-inb mt1-5">
					<img src="" class="xm-acting-img"/>
					<div class="f1 xm-inb">
						<span class="f15 b xm-b">果冻</span><br />
						<span class="f11 dark_gray xm-b">行业分类： </span>
                        <span class="f11 dark_gray xm-b">大闸蟹</span><br />
                        <span class="f11 dark_gray xm-b">启动资金： </span>
                        <span class="f11 dark_gray xm-b">2-3万</span>
					</div>
				</div>
		</div>
		<div class="xm-btn">
			<a href="tel:1234567890" class="tel f15 bold">电话联系</a>
		</div>-->
		
		<!--<div class="defind"><img src="/images/agent/defind_brand.png"/></div>-->
		
	</section>
    <section class="enjoy" style='margin-bottom:5rem;background-color: #f2f2f2;'>
    </section>
    <section style="border-top:1px solid transparent"></section>
@stop
@section('endjs')
    <script src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
    <script src="{{URL::asset('/')}}/js/agent/agent_detail.js"></script>
	 <script>	
        
   		//分享
        function showShare() {
            // shareOut('title', window.location.href, '', 'header', 'content');
            var type='news';
            var title = $('.share-head').text();
            var url = window.location.href;
            var img =  $('#container').data('logo');
            var header = '个人详情';
            var content = cutString(removeHTMLTag($('#content').text()), 18);
            var id={{$id}};
            var share_mark=$('#container').data('sharemark');
            var url = window.location.href+'&share_mark='+share_mark;
            var p_url=labUser.api_path+'/user/card';
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