@extends('layouts.default')  
<!--zhangxm-->        
@section('css')
<link href="{{URL::asset('/')}}/css/_v020902/pactdetails.css" rel="stylesheet" type="text/css"/>
@stop 
@section('main')
<section>
	
        <!--<div class="install none" id="installapp">
            <p class="l">打开无界商圈APP，观看完整高清视频 >> </p>
            <span class="r" id="openapp"><img src="{{URL::asset('/')}}/images/020502/install_btn.png" alt=""></span>
            <div class="clearfix"></div>
        </div>-->
        <!-- 公用-蒙层 -->
        <div class="fixed-bg none" ></div>
        <div class="tips none"></div>
        <!--浏览器打开提示-->
        <div class="safari none">
            <img src="{{URL::asset('/')}}/images/safari.png">
        </div>
        <!-- 分享出去 -->
        <!--<div class="brand-btns fixed width100  brand-p  brand-s none" >-->
            <!--<div class="btn fl width50 pt05 bc_fe" id="brand_suggest_share">
                <p class="tc color-white">发送加盟意向</p>
                <p class="tc color-yellow f12">*获取更多资料</p>
            </div>-->
            <!--<div class="btn fl width50 pt05 tc" id="loadapp" style="line-height: 4rem;">
                <img src="{{URL::asset('/')}}/images/dock-logo2.png" alt="" style="width:2rem;height:2rem;vertical-align: sub;">
                <span class="c8a f16">下载APP</span>
            </div>
        </div>-->
</section>
<section class="container_install">
	<!--安装app-->
    <!--<div class="install-app install-app2 none" id="installapp">
        <img src="{{URL::asset('/')}}/images/dock-logo2.png" alt="">
        <div class="fl pl1">
            <span>无界商圈</span><br>
            <span>用无界商圈找无限商机</span>
        </div>
        <a href="javascript:;" class="install-close f24">×</a>
        <a href="javascript:;" class="install-open" id="openapp">立即开启</a>
    </div>-->
</section>
<section class="containerBox">
	<!-- 待确认  无横幅-->
	<!--待确认  橙色背景-->     
	<!--<div class="head c57c88d down-pay ">
		<div class="head_dis">
			<img src="/images/agent/time.png" class="mr1"/>
			<span class="f15 ">投资人：氵水已签订酷道喜茶加盟加盟合同</span>
		</div>
		<img class="head-imgr " src="/images/agent/he.png">
	</div>-->
	<!--拒绝 红色背景-->
	<!--<div class="head cfc5d5d down-pay ">
		<div class="head_dis">
			<img src="/images/agent/ico_delete3.png" class="mr1"/>
			<span class="f15">投资人：氵水已拒绝酷道喜茶加盟加盟合同</span>
		</div>
		<img class="head-imgr " src="/images/agent/he.png">
	</div>-->
	<!--正文 公共说明-->
	<!--<div class="pub-state bgwhite mt3">
		<p class=" mb1-5">
			<span class=" f14 color333 b">To 无界商圈投资人&nbsp;</span><span class="b f14 cffa300"> 氵水 </span><span class="f14 color333 b">&nbsp;女士</span>
		</p>
		<p class="pub-1">
			<span class="f12 color666 ">通过经纪人</span><span class="cffa300 f12"> (阿树) </span><span class="f12 color666 ">的对接，您是否对品牌已经有了加盟的想法？</span><br />
		<span class="f12 color666 ">在这里，向您发出品牌加盟的橄榄枝，我们提供全网最优质的服务和最低的加盟费用，并为您提供相应的加盟扶持。</span>
		</p>
		<span class="logo-img"><img src="/images/agent/logopact_10.png"/></span>
	</div>-->
	<!--目标品牌-->
       <!--<div class="choose mt1-2 bgwhite">
       		<p class="pt1-5 pb1-5 fline"><span class="f15 color333">目标品牌</span>
       		<div class="brand_list">
       			<div class="chooseBrand fline">
       				<div class="brand">
       					<p class="brand_logo mr1"><img src="/images/act_banner.png"/></p>
       					<div class="">
       						<p class="f14 color333 brand_name">喜茶® HEEKCAA</p>
       						<p class="f11 color999 mb1-2 brand_text">INSPIRATION OF TEA</p>
       						<span class="f12 color666 l_h12">行业分类：</span><span class="f12 cffac00 l_h12">鲜果饮品</span>
       					</div>
       				</div>-->
	       			<!--拒绝情况下展示图标-->
	       			<!--<img src="/images/reject.png" class="reject"/>
	       		</div>
       		</div>
       </div> -->
	<!--加盟方案-->
       <!--<div class="choosePlan mt1-2 bgwhite fline">    
       		<p class="pt1-5 pb1-5 fline"><span class="f15 color333">加盟方案</span>
       		<div class="plan">
       			<div class="packageType mb1-5">
       					<p class="lh2-3"><span class="f12 color666">加盟方案A</span><span class="f12 color666">一点点区域代理方案</span></p>
       					<p class="lh2-3"><span class="f12 color666">加盟类型</span><span class="f12 color666">区域代理</span></p>
       					<p class="lh2-3"><span class="f12 color666">总费用</span><span class="f12 cfd4d4d">¥ 180,000</span></p>
       			</div>
       			<div class="planDetail  bgf2f2">
       					<div class="costDetail">
       						<p class="f11 color666">费用明细</p>
       						<p class="">
       							<span class="f11 color999">加盟费：¥ 20,000</span>
       							<span class="f11 color999">保证金：¥ 30,000</span>
       							<span class="f11 color999">设备费用：¥ 200,000</span>
       							<span class="f11 color999">首批货款：¥  30,000</span>
       							<span class="f11 color999">其他费用：¥ 0</span>
       						</p>
       					</div>
       					<p class="dis_bet mt1-5 mb2 ml">
       						<span class="f11 color666">最高提成</span><span class="f11 cffa300">可提成佣金部分 33%</span>
       					</p>
       					<div class="dis_bet mb2">
       						<span class="f11 color666">合同/文件</span>
       						<p class="textEnd">
       							<span class="f11 c2873ff">《 品牌加盟付款协议 》</span><br />
       							<span class="f11 c2873ff">《品牌加盟合同》</span>
       						</p>
       					</div>
       					<p class="f10 color999 lh1-5">* 如款项存在修改幅度，请联系商务对其进行修改。</p>
       					<p class="f10 color999 lh1-5">* 加盟合同将安排线下签约，实际成交按款项交齐为准。</p>
       					<p class="f10 color999 lh1-5">* 佣金结算以可提成佣金部分乘以提成比例进行计算。</p>
       					<p class="f10 color999 lh1-5">*  对加盟方案存在疑问，请联系商圈客服人员。</p>
       					<p class="f10 color999 lh1-5">*  无界商圈保持最终解释权。</p>
       				</div>
       		</div>
       </div> -->
		<!--合同状态--> 
		<!--待确认-->
		<!--<div class="state_stay bgwhite pl1-33">
			<p class="pt1-5 "><span class="f13 color666">电子合同状态</span></p>
			<p class=" pl3"><span class="f15 color333">等待对方确认</span></p>
			<p class=" pl3 pb1-33"><span class="f10 color999">有效期还剩</span><span class="f10 cfd4d4d">1天10小时22分</span></p>
		</div>-->
		<!--拒绝-->
		<!--<div class="state_reject bgwhite pl1-33">
			<p class="pt1-5 "><span class="f13 color666">电子合同状态</span></p>
			<p class=" pl3"><span class="f15 color333">已拒绝</span></p>
			<p class=" pl3 pb1-33"><span class="f12 cfd4d4d">理由：</span><span class="f12 cfd4d4d">本品牌的加盟存在价格过高因素，所以还是拒绝了。对不起。</span></p>
		</div>-->
      <!--待确认-->
      <!--<div class="accept text-end pr1-5 pt1 f12 mb3">
      	<p class="mb05"><span class="color333">邀请人</span></p>
      	<p class="mb05"><span class="color333">跟单经纪人：</span><span class="color333">皮皮凯</span></p>
      	<p class="mb05"><span class="f12 color999">邀请时间：</span><span class="f12 color999">1111/22/22 22:22:22</span></p>
      </div>-->
      <!--待确认按钮-->
      <!--<div class="pd-btn bgwhite f15 fixed-bottom-iphoneX"><span class="to-pay f16 color333">残忍拒绝</span><span class="sign color-white">确定加盟</span></div>-->
      
      
      <!--确定加盟弹窗-->
      <!--<div class="masking">
      		<div class="joinFlow bgwhite">
      			<p class=" f20 color333">提醒</p>
      			<p class="f15 color333"><span class="">确定同意加盟品牌 ——</span><span class="">HEEKCHA喜茶</span>?</p>
      			<p class="f10 color999">点击确定按钮，表示您已经同意无界商圈加盟的相关协议，并愿意、自觉地遵守协议相关条款。</p>
      			<p class="mt2 mb1 f15 color333">加盟品牌的具体流程如下：</p>
      			<img src="/images/agent/flow.png" class="mb1-2"/><br />
      			<p class="f10 color999">点击确定按钮，生成订单，之后将以POS机刷卡方式完成款项支付。</p>
      			<p class="f10 color999">此外，无界商圈提供丰厚的红包抵扣，具体操作请联系客服或您的经纪人。</p>
      			<p class="joinFlow_btn"><span class="scale-1 color999">再想想</span><span class="color-white">下一步，生成订单</span></p>
      		</div>
      </div>-->
</section> 
<section class="enjoy" style='padding-bottom:5.6rem'></section>
<section>
	<div class="common_pops none"></div>
	<div class="define none" style="width: 100%;margin: auto;height: 100%;text-align: center;">
		<img src="/images/agent/close_contact.png" style="width: 50%;text-align: center;margin-top: 30%;"/>
	</div>
	<div class="brand_down none" style="width: 100%;margin: auto;height: 100%;text-align: center;">
		<img src="/images/agent/brand_down.png" style="width: 50%;text-align: center;margin-top: 30%;"/>
	</div>
</section>
<section style="position: fixed;bottom: 0;background: #FFFFFF;" class="iphone_btn none"></section>
<!--<section class="enjoy" style='padding-bottom:5.5rem'></section>-->
@stop
@section('endjs')
<!--<script type="text/javascript" src="{{URL::asset('/')}}/js/dist/agentfontsize.min.js"></script>-->
<script src="{{URL::asset('/')}}/js/_v020902/pact_details.js"></script>
@stop