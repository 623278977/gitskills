@extends('layouts.default')
<!--zhangx-->
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/personal.css" rel="stylesheet" type="text/css"/>
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
        <!--<div class="app_install fixed none" id="installapp">
            <i class="l">打开无界商圈APP，体验更多精彩内容 >></i>
            <span class="r" id="openapp"><img src="{{URL::asset('/')}}/images/020502/install_btn.png" alt=""></span>
            <div class="clearfix"></div>
        </div>
        <div class="share pl1-33 pr1-33 share_0502 none" id="share" style='display: none'>
            <p class="f12 l">分享资讯，立即获得100积分</p>
            <button class="ff5 l f12 understand"><img src="{{URL::asset('/')}}/images/020502/notice.png" alt="">了解分享规则介绍</button>
            <span class="fff f16 r close_share"><img src="{{URL::asset('/')}}/images/share_close.png" alt=""></span>
        </div>-->
    <!--浏览器打开提示-->
        <!--<div class="safari none">
            <img src="{{URL::asset('/')}}/images/safari.png">
        </div>
        <button class="loadapp f16 none" id="loadapp">
            <img src="{{URL::asset('/')}}/images/dock-logo2.png" alt="">下载APP-->
            <!-- <iframe style="position:absolute; visibility:inherit; top:0px; left:0px; width:100%; height:100%; z-index:9999; filter='Alpha(style=0,opacity=0)';"></iframe> -->
        <!--</button>
         <div class="fixed_btn none">
            <button class="weizan_07 w50 headzan" id="zannum"></button>
             <button class="nothks_07 w33">感谢作者</button> 
            <button class="comment_07 w50" id="comment_num"></button>
        </div>-->
    <!-- 蒙层 -->
        <!--<div class="fixed-bg none"></div>
        <div class="alert none">
            <p></p>
        </div>-->
    </section>
	<section id='container'>
		<!--<div class="share-head f36 fline mb2-5 none">
			无界商圈经纪人
		</div>-->
		<!--<div class="personal">-->
			<!--<img src="" alt="" class="company mr1-33 fl xm-inb"/>
			<div class="xm-amounts xm-inb">
				<span class="b f30 color3">张长</span><br />
				<span class="color699 f24 xm-common">普通经纪人</span><br />
				<img src="../../../../public/images/head_0205.png" class="gender xm-inb"/>
				<span class="f24">浙江</span>&nbsp;
				<span class="f24">杭州</span>
				<span class="xm-edit f24">编辑</span>
			</div>
			<p class="keyword fline">
				<span class="keywords m05 f22">特色餐饮</span>
				<span class="keywords m05 f22">特色餐饮</span>
				<span class="keywords m05 f22">特色餐饮</span>
				<span class="keywords m05 f22">特色餐饮</span>
				<span class="keywords m05 f22">特色餐饮</span>		
			</p>
			<div class="f24 color6 xm-sign ui-nowrap-multi">我的签名我的签名我的签名我的签名</div>-->
		<!--</div>-->
		<!--<div class="personals Medium">-->
				<!--<p class="f30 fline ptb1-4 b keyword">
					代理品牌
					<span class="">(100)</span>
				</p>
				<div class="xm-acting xm-inb">
					<img src="" class="xm-acting-img"/>
					<div class="f1 xm-inb mr3-5">
						<span class="f30 b xm-b">果冻</span><br />
						<span class="f22 dark_gray xm-b">行业分类： </span>
                        <span class="f22 dark_gray xm-b">大闸蟹</span><br />
                        <span class="f22 dark_gray xm-b">行业分类： </span>
                        <span class="f22 dark_gray xm-b">大闸蟹</span>
					</div>
				</div>-->
				
		<!--</div>-->
		
		
	</section>
	<section>
		<div class="xm-btn none">
			<span href="" class="xm-btn-invest f15 bold">成为投资人</span>
			<span href="" class="xm-btn-agent f15 bold">成为经纪人</span>
		</div>
	</section>
    <section class="enjoy" style='padding-bottom:10rem;background-color: #f2f2f2;'>
    </section>
    <section style="border-top:1px solid transparent"></section>
@stop
@section('endjs')
    <script src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
    <script src="{{URL::asset('/')}}/js/agent/personal.js"></script>
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