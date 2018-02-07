@extends('layouts.default')
<!--zhangxm-->
@section('css')
	<!--<link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/_v020800/follow.css"/>
	<link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/_v020903/mui.min.css"/>
    <link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/_v020903/component.css" >-->
    <link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/_v020903/award.css" >
    <link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/_v020903/animate.min.css"/>
    <style type="text/css">
    	#container{
    		background-image: url(/images/020903/choujiangbg.png);
    		background-size: 100% 100%;
    	}
    	.text_align {
    		text-align: center;
    	}
    	.tops {
    		font-size: 3.2rem;
    		color: #fffefe;
    		text-align: center;
    	}
    	.times {
    		color: #fffefe;
    		text-align: center;
    	}
    	.begin {
    		width: 10rem;
			height: 4rem;
			background-color: rgba(255, 255, 255, 0.1);
			border: solid 1px rgba(255, 255, 255, 0.1);
			text-align: center;
			line-height: 4rem;
			border-radius: 5rem;
			display: inline-block;
    	}
    	.choujiang_num {
    		color: #ffef68;
    	}
    	.xiala img{
    		width: 4.6rem;
			height: 4.367rem;
    	}
    	.rulebg {
    		background-image: url(/images/020903/guizebg.png);
    		background-size: 100% 100%;
    		padding: 1.3rem 3.5rem;
    		width: 90%;
    		margin: 0 auto;
    	}
    	.rulebg p {
    		width: 100%;
    		line-height: 2.5rem;
    	}
    	.huang {
    		width: 1rem;
			height: 1rem;
    	}
    	.xxcy_text {
    		background-image: url(/images/020903/nozjbg.png);
    		background-size: 100% 100%;
    		text-align: center;
    		padding-top: 3.7rem;
    		padding-bottom: 3.5rem;
    		width: 80%;
			margin: 0 auto;
    	}
    	/*.zj-main,.xxcy-main {
    		display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
    	}*/
    	.zj_text {
    		color: #fab231;
    	}
    	.txzl {
    		padding-top: 18rem;
    	}
    	.masking {
    		width: 100%;
    		height: 100%;
    		display: flex;
    		flex-direction: column;
			align-items: center;
			justify-content: center;
    	}
    	#name,#tel {
    		height: 3.9rem;
    	}
    	#site {
    		height: 6.9rem;
    	}
    	#name,#tel,#site {
			background-color: #e5e5e5;
			border-radius: 0.2rem;
			width: 88%;
			border: 0;
    	}
    </style>
@stop
<!--zhangxm-->
@section('main')
	<section id='container' class="none">
		<!--<input type="hidden" name="gamed" id="gamed" value="{{gamed}}" />
		<input type="hidden" name="gameState" id="gameState" value="{{gameState}}" />
		<input type="hidden" name="cardCode" id="cardCode" value="{{cardCode}}" />
		<input type="hidden" name="mId" id="mId" value="{{mId}}" />-->
	
	
<!-------------抽奖页面-------------->
	<img src="/images/020903/aiqiyi.png" id="aiqiyi" style="display:none;" />
    <img src="/images/020903/aixin.png" id="aixin" style="display:none;" />
    <img src="/images/020903/gouwuka.png" id="gouwuka" style="display:none;" />
    <img src="/images/020903/hongbao.png" id="hongbao" style="display:none;" />
    <img src="/images/020903/jifen.png" id="jifen" style="display:none;" />
    <img src="/images/020903/shouji.png" id="shouji" style="display:none;" />
    <img src="/images/020903/xianjin.png" id="xianjin" style="display:none;" />
	<div class="ml-main" id="ml-main">
		<p class="tops">新年新气象，无界送心意</p>
		<p class="times mt3 mb4 f16">时间：2月13日-2月23日</p>
		<!--<img class="animated zoomIn img_2_1" src="img/img_1.png">
		<img class="animated bounceIn img_2_2" src="img/img_2.png">-->
        <div class="kePublic">
            <!--转盘效果开始-->
            <div style="margin:0 auto">
                <div class="banner">
                    <div class="turnplate" style="background-image:url(/images/020903/turnplate-bg_2.png);background-size:100% 100%;font-size:24px !important;">
                        <canvas class="item" id="wheelcanvas" width="516" height="516"></canvas>
                        <img id="tupBtn" class="pointer" src="/images/020903/turnplate-pointer_2.png"/>
                    </div>
                </div>
            </div>
            <!--转盘效果结束-->
            <div class="clear"></div>
        </div>
        <p class="mt4 text_align"><span class="pointer begin f16 white">开始</span></p>
        <p class="f16 white text_align mt1 mb4">当前还有  <span class="choujiang_num f20 b"></span> 次抽奖机会</p>
        <p class="xiala text_align mb3-5"><img src="/images/020903/xiala.png"/></p>
        <div class="rulebg ">
        	<p class="white f12 mt1-2 mb3 text_align"><span class="color999">— </span><span class="">活动规则</span><span class="color999"> —</span></p>
        	<p class="white f14"><img src="/images/020903/huang.png" alt="" class="huang"/>门槛<span style="color: #ffef68;">100</span>积分/次，每天可抽<span style="color: #ffef68;">3</span>次；</p>
        	<p class="white f14"><img src="/images/020903/huang.png" alt="" class="huang"/>邀请<span style="color: #ffef68;">3</span>位好友注册无界商圈APP，可获得<span style="color: #ffef68;">1</span>次抽奖机会；</p>
        	<p class="white f14">
        		<img src="/images/020903/huang.png" alt="" class="huang"/>奖品发放：实物奖品（包含爱奇艺VIP：月卡）统一在活动结束后发放，为了方便寄送实物奖品，大家请认真填写联系人姓名、电话和住址；虚拟奖品我们会在<span style="color: #ffef68;">24小时</span>内将奖品发放到相应中奖者的账户中， 请及时查收；
        	</p>
        	<p class="white f14"><img src="/images/020903/huang.png" alt="" class="huang"/>通用红包不可提现，不可叠加使用，仅限于品牌加盟时抵扣加盟费所用；</p>
        	<p class="white f14"><img src="/images/020903/huang.png" alt="" class="huang"/>活动最终解释权归杭州天涯若比邻网络信息服务有限公司所有，有任何疑问或者帮助可以联系客服：<span style="color: #ffef68;">400-011-0061</span>。</p>
        	<p class="white f12 mt5 mb2 text_align"><span class="color999">— </span><span class="">奖品及说明</span><span class="color999"> —</span></p>
        	<p class="white f14"><img src="/images/020903/huang.png" alt="" class="huang"/>iPhoneX，官网全新，颜色可选；</p>
        	<p class="white f14"><img src="/images/020903/huang.png" alt="" class="huang"/>京东购物卡100元；</p>
        	<p class="white f14"><img src="/images/020903/huang.png" alt="" class="huang"/>爱奇艺VIP：1月；</p>
        	<p class="white f14"><img src="/images/020903/huang.png" alt="" class="huang"/>通用红包1888元：不可提现、不可叠加，用于无界商圈平台所有品牌加盟费用的抵扣；</p>
        	<p class="white f14"><img src="/images/020903/huang.png" alt="" class="huang"/>现金红包68元；</p>
        	<p class="white f14"><img src="/images/020903/huang.png" alt="" class="huang"/>100积分：积分可抵扣活动门票，收费视频、直播、资讯的所有费用，也可在积分商城兑换物品；</p>
        	<p class="f14 mt4" style="color: #ffef68;"><img src="/images/020903/huang.png" alt="" class="huang"/>PS：实物奖品需提供联系人姓名、电话和住址，用于邮寄奖品</p>
        </div>
        <!--------------滚动中奖纪录---------------->
        <!--<div class="record_line" id="Marquee">
        	<div id="">
        		恭喜  159****3540  的用户抽中  <span id="gift_coupon">200元现金代金券</span>
        	</div>
        </div>-->
        <!-------------底部声明-------------->
        <!--<img class="rule_title" src="img/rule_title.png"/>-->
        <!--<div class="rule_text">
        	点击转盘进行抽奖，每人每天可以抽奖一次。<br>
        	分享还可以再获得一次抽奖机会，抽中的奖品券可以进行核销兑换。
        </div>-->
	</div>
    
    <!-------------中奖弹窗页面-------------->
    
    <div class="zj-main" id="zj-main">
    	<div class="masking">
    		<div class="txzl pb4">
	        	<div class="zj_text mb2-5">
	            	<span class="f24">恭喜您抽中</span><br /><span id="jiangpin" class="f24 b"></span>
	        	</div>
	        	<div class="">
	        		<div class="close_zj none f16">知道了</div>
	        		<div class="my_message none f16">填写信息</div>
	        	</div>
	        </div>
    	</div>
        
	</div>
    <!-------------填写信息弹框-------------->
    <div class="mes-main" id="mes-main">
    	<div class="masking">
    		<div class="xxcy_text f14 color999 pt13-5 pb2">
    			<p class="f20 mb1-5" style="color: #470649;">请填写信息</p>
    			<input type="text" class="pl1 mb05" id="name" value="" placeholder="请输入姓名"/><br />
    			<input type="text" class="pl1 mb05" id="tel" value="" placeholder="请输入手机号码"/><br />
    			<textarea class="pt1 pl1 mb2" id="site" rows="" cols="" placeholder="请输入地址"></textarea>
    			<p class="submit-mes f16 ">提交信息</p>
    		</div>
    	</div>
    </div>
    
    
    
    <!-------------谢谢参与弹窗-------------->
    <div class="xxcy-main" id="xxcy-main">
    	<div class="masking">
        	<div class="xxcy_text">
        		<p class="f30 mb2-5" style="color: #470649;">很遗憾</p>
        		<p class="f24 mb2" style="color: #470649;">这次没有抽中哟~</p>
        		<div class="close_xxcy f16">再抽一次</div>	
        	</div>   
        	
        </div>
    </div>
	
    <section class="enjoy" style='padding-bottom:4rem'></section>
    <section style="border-top:1px solid transparent"></section>
    <section ><div class="common_pops none"></div></section>
@stop
@section('endjs')
    <script src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
	<!--<script type="text/javascript" src="{{URL::asset('/')}}/js/_v020903/mui.min.js"></script>-->
	<script type="text/javascript" src="{{URL::asset('/')}}/js/_v020903/awardRotate.js"></script>
<!--<script type="text/javascript" src="js/main.js"></script>-->
	<script type="text/javascript" src="{{URL::asset('/')}}/js/_v020903/award.js"></script>
	<script type="text/javascript">
	$(function(){
		$("img").on("click",function(){
			return false;
		});
		document.addEventListener("WeixinJSBridgeReady",function(){
			WeixinJSBridge.call('hideOptionMenu'); 
		});
	});
</script>
@stop