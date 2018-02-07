@extends('layouts.default')
<!--zhangxm-->
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/_v010400/acquirefu.css" rel="stylesheet" type="text/css"/>
    <style>
		.scroll-box {
			width: 100%;
			height: 9rem;
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
			display: flex;
			justify-content: space-between;
			align-items: center;
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
	<section id='container' class="pt9-5 pb10-5 none">
		<div class="fukaliang ">
			<div class="acquirefu">
				<p class="">
					<img src="/images/agent/roundwuliang.png" class="fuzi_img wu "/>
					<img src="/images/agent/roundjieliang.png" class="fuzi_img jie "/>
					<img src="/images/agent/roundshangliang.pngg" class="fuzi_img shang "/>
					<img src="/images/agent/roundquanliang.png" class="fuzi_img quan "/>
					<img src="/images/agent/roundfuliang.png" class="fuzi_img fu "/>
				</p>
				<div class="fu_wenzi">
					<img src="/images/agent/lingxing.png" class="lingxing"/>
					<p class="">
						<span class="wu fuzi_text  ml05 mr05">迎春接福，喜气洋洋</span>
						<span class="jie fuzi_text  ml05 mr05">财源滚滚，阖家幸福</span>
						<span class="shang fuzi_text  ml05 mr05">意气风发，好事连连</span>
						<span class="quan fuzi_text  ml05 mr05">万事如意，恭喜发财</span>
						<span class="fu fuzi_text  ml05 mr05">吉祥富贵，连年有余</span>
					</p>
					<img src="/images/agent/lingxing.png" class="lingxing"/>
				</div>
			 	<p class="acquirefu_btn">
			 		<button class="shareFuka f15 c4f3b0b b">分享应福卡</button>
			 		<button class="sendFriend f15 cff422f b">送给小伙伴</button>
			 	</p>
			 </div>
			 
		</div>
		 <div class="fukagrey none">
		 	<div class="noacquirefu">
		 		<p class="">
					<img src="/images/agent/roundwuhui.png" class="fuzi_img wu "/>
					<img src="/images/agent/roundjiehui.png" class="fuzi_img jie "/>
					<img src="/images/agent/roundshanghui.png" class="fuzi_img shang "/>
					<img src="/images/agent/roundquanhui.png" class="fuzi_img quan "/>
					<img src="/images/agent/roundfuhui.png" class="fuzi_img fu "/>
				</p>
				<p class="noacquirefu_text mt1"><span class=" f14 color666">哭，未抽中该福卡</span></p>
				<p class="noacquirefu_text mt1 mb2"><span class=" f14 color666">立即邀请投资人，获得更多福卡机会</span></p>
			 	<p class="noacquirefu_btn">
			 		<button class="shareFuka f15 c4f3b0b b">分享应福卡</button>
			 		<button class="blag f15 cff422f b">索要福卡</button>
			 	</p>
			 </div>
		 </div>
		<p class="huojiang mt4 mb3"><img src="/images/agent/huojiang.png"/></p>
		<div class="awards_record ">
			<div class="scroll-box pl1-5 pr1-5">
				<!--<ul>
					<li class="cfff"><span class="f14">“立即领取福袋”抽奖获得福卡一张（卡的名称）</span><span class="f12">2018年2月2日 </span></li>
					<li class="cfff"><span class="f14">赠送给经纪人***一张福卡（卡的名字）</span><span class="f12">2018年2月2日 </span></li>
					<li class="cfff"><span class="f14">成功邀请投资人Lil（186****2260）</span><span class="f12">2018年2月2日 </span></li>
					<li class="cfff"><span class="f14">“恭喜发财”抽奖获得福卡一枚</span><span class="f12">2018年2月2日 </span></li>
					<li class="cfff"><span class="f14">成功邀请投资人Lil（186****2260）</span><span class="f12">2018年2月2日 </span></li>
					<li class="cfff"><span class="f14">“恭喜发财”抽奖获得福卡一枚</span><span class="f12">2018年2月2日 </span></li>
					<li class="cfff"><span class="f14">***经纪人赠送一张福卡</span><span class="f12">2018年2月2日 </span></li>
				</ul>-->
			</div>
		</div> 
	</section>
    <section class="enjoy" style="background-color: #f2f2f2;">
    	<div class="common_pops none"></div>
    </section>
@stop
@section('endjs')
	<script type="text/javascript" src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
    <script src="{{URL::asset('/')}}/js/agent/_v010400/acquirefu.js"></script>
	<script>
		$(document).ready(function(){
			$('title').text('五福临门');
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
            var header = '五福临门';
            var summary = cutString($('#content').attr('summary'), 18);
            var content = '在一起，过福年，好福气，要分享~';
            if(content==''){
            	content = summary;
            }
            var id = agent_id;
            var url = window.location.href;
            if(summary==''){
            	shareOut(title, url, img, header, content,'','',id,type,'','','','','');
            }else {
            	shareOut(title, url, img, header, summary,'','',id,type,'','','','','');
            };
        };
//获奖记录
function awardsRecord() {
	//获得当前<ul>
	var $uList = $(".scroll-box ul");
	var timer = null;
	
	//滚动动画
	function scrollList(obj) {
		//获得当前<li>的高度
		var scrollHeight = $(".scroll-box ul li:first").height();
		//滚动出一个<li>的高度
		$uList.stop().animate({
			marginTop: -scrollHeight
		}, 1000, function() {
			//动画结束后，将当前<ul>marginTop置为初始值0状态，再将第一个<li>拼接到末尾。
			$uList.css({
				marginTop: 0
			}).find("li:first").appendTo($uList);
		});
	};
	//计时
	timer = setInterval(function() {
			scrollList($uList);
		}, 1200);

}
awardsRecord()
    </script>
@stop