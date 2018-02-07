@extends('layouts.default')
<!--zhangx-->
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/_v010400/newyear.css" rel="stylesheet" type="text/css"/>
    <style>
		.scroll-box {
			width: 100%;
			height: 3rem;
			overflow: hidden;
		}
		.scroll-box ul {
			list-style: none;
			width: 100%;
			height: 100%;
		}
		.scroll-box ul li {
			width: 100%;
			font-size: 1.2rem;
			box-sizing: border-box;
			line-height: 3rem;
			text-align: center;
			
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
	<section id='container' class="medium">
		 <div class="head pt8">
		 	<div class="head_hb">
		 		<p class="f15 cfff">邀请好友注册无界商圈投资人</p>
		 		<p class="f15 cfff">你就赚到一次赏金抽奖机会~</p>
		 		<p class="f14 cf9da49 mb4">赚钱攻略在此 ></p>
		 	</div>
		 </div>
		 <div class="content pt5 pl1-5 pr1-5">
		 	<div class="scroll-box">
				<!--<ul>
					<li class="cfff">*艳已获得1,699元红包</li>
					<li class="cfff">*艳已获得1张福卡</li>
					<li class="cfff">皮皮鳝抽中路虎揽胜</li>
					<li class="cfff">皮皮伟抽中劳斯莱斯幻影</li>
					<li class="cfff">皮皮骏抽中玛莎拉蒂</li>
					<li class="cfff">皮皮瑞抽中梅赛德斯奔驰</li>
					<li class="cfff">皮皮斌抽中宾利</li>
				</ul>-->
			</div>
			<button class="ccf3a40 lottery f22">恭喜发财</button>
			<!--有抽奖次数-->
			<p class="f12 cjYes none" style="line-height: 3rem;">
				<span class="cfff">您有</span>
				<span class="cf4c85d draw_nums">1</span>
				<span class="cfff">次抽奖机会</span>
			</p>
			<!--没有抽奖次数-->
			<p class="f12 cjNo none" style="line-height: 3rem;">
				<span class="cfff">您抽奖次数已用完</span><br />
				<span class="cfff share_out">邀请好友获得更多抽奖机会 ></span>
			</p>
			<!--<p class="f12" style="line-height: 3rem;">
				<span class="cfff">抽取可获得现金红包、新年红包</span>
			</p>-->
			<p class="cf4c85d f18 ml1-5 mt3">新年集5福，瓜分10万豪礼！</p>
			<div class="blessing mt2 mb1">
				<p class="wu fuka"><img src="/images/agent/wugrey.png" alt="" class="" /><span class="f13 b none">x0</span></p>
				<p class="jie fuka"><img src="/images/agent/jiegrey.png" alt="" class="" /><span class="f13 b none">x0</span></p>
				<p class="shang fuka"><img src="/images/agent/shanggrey.png" alt="" class="" /><span class="f13 b none">x0</span></p>
				<p class="quan fuka"><img src="/images/agent/quangrey.png" alt="" class="" /><span class="f13 b none">x0</span></p>
				<p class="fu fuka"><img src="/images/agent/fugrey.png" alt="" class="" /><span class="f13 b none">x0</span></p>
			</div>
			<p class="state"><span class="cd96448 f12">福运到，集5福。喜迎2018！</span><span class="cf4c85d f12">集福锦囊 ></span></p>
			<p class="" style="text-align: left;"><span class="cd96448 f12">每一张福卡都是一份福气，福气送给你，集齐5福，喜迎2018！</span></p>
		 </div>
		 <div class="foot pl1-5 pr1-5 pb11">
		 	<div class="footCut">
		 		<button class="answer"><img src="/images/agent/anschoose.png"/></button>
		 		<button class="foot_share"><img src="/images/agent/sharebtnfu.png"/></button>
		 	</div>
		 	<div class="answerBtn ">
		 		<p class="pb1-5 "><img src="/images/agent/anstext.png"/ class="mt3-5 mb3"><br /><button class="f18 cf4c85d dati">立即答题</button></p>
		 	</div>
		 	<div class="shareBtn none">
		 		<p class="pb1-5"><img src="/images/agent/sharetext.png"/ class="mt3-5 mb3"><br /><button class="f18 cf4c85d share_out">分享赢五福</button></p>
		 	</div>
		 </div>
	</section>
    <section class="enjoy" style="background-color: #f2f2f2;">
    	<div class="common_pops none"></div>
    </section>
    <section style="border-top:1px solid transparent"></section>
@stop
@section('endjs')
	<script type="text/javascript" src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
    <script src="{{URL::asset('/')}}/js/agent/_v010400/newyear.js"></script>
	<script>
		$(document).ready(function(){
			$('title').text('压轴盛宴，礼见新年');
		});	
        //分享
        function showShare() {
        	var args=getQueryStringArgs(),
        		agent_id = args['agent_id'];  
            var type='news';
            var title = $('title').text();
            var img =  $('#container').attr('logo');
            if(img==''){
            	img=labUser.path+'images/agent/dock-logo.png'
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
        function reload(){
            location.reload();
        }
        function Refresh(){
            reload();
            $('body').scrollTop($('body')[0].scrollHeight);
       }
       
    </script>
@stop