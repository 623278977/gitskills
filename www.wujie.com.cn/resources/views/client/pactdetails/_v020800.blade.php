@extends('layouts.default')  
<!--zhangxm-->        
@section('css')
<link href="{{URL::asset('/')}}/css/_v020800/pactdetails.css" rel="stylesheet" type="text/css"/>
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
	<!--已签订   已结清  绿色背景-->     
	<!--<div class="head c57c88d ">
		<img src="/images/agent/icon-true@2×.png" class="mr1"/>
		<span class="f16 color-white">已签订酷道喜茶加盟加盟合同</span>
	</div>-->
	<!--拒绝 红色背景-->
	<!--<div class="head cfc5d5d none">
		<img src="/images/agent/ico_delete@2x.png" class="mr1"/>
		<span class="f16 color-white">已拒绝酷道喜茶加盟加盟合同</span>
	</div>-->
	<!--正文 公共说明-->
	<!--<div class="pub-state bgwhite mt3">
		<p class=" mb1-5">
			<span class="bold f14 b color333 bold">To 无界商圈投资人&nbsp;</span><span class="bold f14 b cffa300"> 氵水 </span><span class="f14 b color333 bold">&nbsp;女士</span>
		</p>
		<p class="pub-1">
			<span class="f12 color666 medium">通过经纪人</span><span class="cffa300 medium">(阿树)</span><span class="f12 color666 medium">的对接，您是否对品牌已经有了加盟的想法？</span><br />
		<span class="f12 color666 medium">在这里，向您发出品牌加盟的橄榄枝，我们提供全网最优质的服务和最低的加盟费用，并为您提供相应的加盟扶持。</span>
		</p>
		<span class="logo-img"><img src="/images/agent/logopact_10.png"/></span>
	</div>
	 <div class="pact bgwhite mt1-2">
      	<div class="pl1-33 pr1-33 pt05">-->
      		<!--公共合同信息-->
      			<!--<div class="act-2 ">
      				<div class="inst-2l ">
      					<p class="inst-2lp mb05"><span class="f14 color999 medium">加盟合同</span><span class="f14 color333 medium">合同名称</span>
      					<p class="inst-2lp mb05"><span class="f14 color999 medium">合同号</span><span class="f14 color333 medium">12343241</span></p>
      					<p class="inst-2lp mb05"><span class="f14 color999 medium">加盟品牌</span><span class="f14 color333 medium">喜茶</span></p>
      					<p class="inst-2lp mb05"><span class="f14 color999 medium">经纪人</span><span class="f14 color333 medium">姚凯</span></p>
      				</div>
      			</div>-->
      			<!--拒绝-->
      			<!--<div>
      				<p class="inst-2lp  text-end fline"><span class="f14 color999 medium">合同撰写</span><span class="f14 color333 pb1 medium">无界商圈法务代表<br />喜茶法务代表</span></p>
      				<p class="inst-2lp mb05 pt1"><span class="f12 color999 medium">加盟总费用</span><span class="f12 color333 medium">¥ 50.000</span></p>
      				<p class="inst-2lp mb05"><span class="f12 color999 medium">合同文本</span></p>
      					<div class="pct-2 mb1 fline">
      						<div class="act-2l pact-text">
      							<img src="/images/agent/my_contract.png" class="pact-img mr1"/>
      							<p class="pact-2lp over-text">
      								<span class="over-text f14 b bold act-2lspan color333">喜茶加盟合同</span><br />	
      								<span class="over-text f10 act-2lspan color333 medium">合同编号：3月3日 23:23</span><br />
      							</p>
      						</div>
      						<img src="/images/jump.png" class="pct-jump"/>
      					</div>
      					<p class="inst-2lp mb05 pt1"><span class="f14 color999 medium">拒绝理由</span><span class="f14 color333 medium">理由</span></p>
      						<p class="inst-2lp mb05 pt1"><span class="f14 color999 medium">确定时间</span><span class="f14 color333 medium">1111/11/11 11:11:11<span></p>
      			</div>-->
      			<!--待确认-->
      			<!--<div>
      				<p class="inst-2lp mb05 pt1"><span class="f14 color999 medium">加盟总费用</span><span class="f14 color333 medium">¥ 50.000</span></p>
      				<p class="inst-2lp mb05 pt1"><span class="f14 color999 medium">线上首付</span><span class="f14 color333 medium">¥ 50.000</span></p>
      				<p class="inst-2lp mb05 pt1"><span class="f14 color999 medium">线下尾款</span><span class="f14 color333 medium">¥ 500.000</span></p>
      				<div class="inst-2lp mb05 pt1 text-end">
  						<span class="f14 color999 medium">缴纳方式</span> 
  						<p class="f12 medium">
  							<span class="f14 mb05 color333 medium">线上首付一次结清</span><br />
  							<span class="f14 mb05 color333 medium">线下尾款银行转账</span><br />
  							<span class="f10 c2873ff mb05 medium">了解尾款补齐操作办法</span><br />
  						</p>
      				</div>
      				<p class="inst-2lp mb05 pt1"><span class="f14 color999 medium">合同文本</span></p>
  					<div class="pct-2 mb1 fline">
  						<div class="act-2l pact-text">
  							<img src="/images/agent/my_contract.png" class="pact-img mr1"/>
  							<p class="pact-2lp over-text">
  								<span class="over-text f14 b bold act-2lspan color333">喜茶加盟合同</span><br />	
  								<span class="over-text f10 act-2lspan color333 medium">合同编号：3月3日 23:23</span><br />
  							</p>
  						</div>
  						<img src="/images/jump.png" class="pct-jump"/>
  					</div>
      			</div>-->
      			
      			<!--已确认  已付清   首付-->
      			<!--<div class="">
      				<div class="inst-2l ">
      					<p class="inst-2lp mb05"><span class="f14 color999 medium">加盟费用</span><span class="f12 color666">¥ 50.000</span></p>
      					<p class="inst-2lp mb05"><span class="f14 color999 medium">创建时间</span><span class="f14 color333 medium">1111/11/11 11:11:11</span></p>
      					<p class="inst-2lp mb05"><span class="f14 color999 medium">确定时间</span><span class="f14 color333 medium">1111/11/11 11:11:11</span></p>
      					<p class="down-pay mb05"><span class="fline wid"></span><span class="f12 color666 medium">首付情况</span><span class="fline wid"></span></p>
      					<p class="inst-2lp mb05"><span class="f14 color999 medium">首次支付</span><span class="f14 color333 medium">¥ 23,333</span></p>
      					<p class="inst-2lp mb05"><span class="f14 color999 medium">订金抵扣</span><span class="f14 color333 medium">-¥ 23,333</span></p>
      					<p class="inst-2lp mb05"><span class="f14 color999 medium">创业基金抵扣</span><span class="f14 color333 medium">-¥ 0</span></p>
      					<p class="inst-2lp mb05"><span class="f14 color999 medium">实际支付</span><span class="f14 color333 medium">¥ 14,222</span></p>
      					<p class="inst-2lp mb05"><span class="f14 color999 medium">支付状态</span><span class="f14 color333 medium">已支付</span></p>
      					<p class="inst-2lp mb05 text-end"><span class="f14 color999 medium">支付方式</span><span class="f14 color333 medium ">支付宝</span></p>
      				</div>
      			</div>-->
      			<!--尾款-->
      			<!--<div class="act-2">
      				<div class="inst-2l ">
      					<p class="down-pay mb05"><span class="fline wid"></span><span class="f12 color666  medium">尾款情况</span><span class="fline wid"></span></p>
      					<p class="inst-2lp mb05"><span class="f14 color999 medium">尾款补齐</span><span class="f14 color333 medium">¥ 23,333</span></p>-->
      					<!--未支付情况-->
      					<!--<div class="inst-2lp mb05 text-end">
      						<span class="f14 color999 medium inst-2lp-span">支付状态</span> 
      						<p class="f12 medium">
      							<span class="cfd4d4d mb05 medium">未支付</span><br />
      							<span class="f10 mb05 color999 medium">* 请于8月13日前支付响应款项，如有延误等情况，请尽早联系经纪人</span><br />
      							<span class="f10 mb05 color999 medium">支付方式为线下对公账号转账</span><br />
      							<span class="f10 c2873ff mb05 medium">了解尾款补齐操作办法</span><br />
      						</p>
      					</div>-->
      					<!--已结清-->
      					<!--<div class="inst-2lp mb05 text-end">
      						<span class="f14 color999 medium inst-2lp-span">支付状态</span> 
      						<p class="f12 medium">
      							<span class="cfd4d4d mb05 medium">已结清</span><br />
      							<span class="f10 mb05 color999 medium">* 请于8月13日前支付响应款项，如有延误等情况，请尽早联系经纪人</span><br />
      							<span class="f10 mb05 color999 medium">支付方式为线下对公账号转账</span><br />
      							<span class="f10 c2873ff mb05 medium">了解尾款补齐操作办法</span><br />
      						</p>
      					</div>
      					<p class="inst-2lp mb05"><span class="f14 color999 medium">合同文本</span></p>
      					<div class="pct-2 mb1 fline">
      						<div class="act-2l pact-text">
      							<img src="/images/agent/my_contract.png" class="pact-img mr1"/>
      							<p class="pact-2lp over-text">
      								<span class="over-text f14 b bold act-2lspan color333">喜茶加盟合同</span><br />	
      								<span class="over-text f10 act-2lspan color333 medium">合同编号：3月3日 23:23</span><br />
      							</p>
      						</div>
      						<img src="/images/jump.png" class="pct-jump"/>
      					</div>
      				</div>
      			</div>
      			
      	</div>
      </div>-->
      <!--落款处-->
      <!--已接受  已结清-->
      <!--<div class="accept text-end pr1-5 pt1 f13 mb3">
      	<p class="mb05"><span class="color333 b medium">邀请人</span></p>
      	<p class="mb05"><span class="color333 b medium">跟单经纪人：</span><span class="color333 b bold">皮皮凯</span></p>
      	<p class="mb05"><span class="color999 medium">邀请时间：</span><span class="color999 medium">1111/22/22 22:22:22</span></p>
      	<p class="mb05"><span class="color999 medium">确定时间：</span><span class="color999 medium">1111/22/22 22:22:22</span></p>
      </div>-->
      <!--已拒绝-->
      <!--<div class="pay-off text-end pr1-5 pt1 f12 mb3">
      	<p  class="mb05"><span class="color333 b medium">邀请人</span></p>
      	<p  class="mb05"><span class="color333 b medium">跟单经纪人：</span><span class="color333 b bold">皮皮凯</span></p>
      	<p  class="mb05"><span class="color999 medium">邀请时间：</span><span class="color999 medium">1111/22/22 22:22:22</span></p>
      	<p  class="mb05"><span class="color999 medium">拒绝时间：</span><span class="color999 medium">1111/22/22 22:22:22</span></p>
      	<p  class="mb05"><span class="color999 medium">拒绝理由：</span><span class="color999 medium">皮皮凯太菜，带不动</span></p>
      </div>-->
      <!--待确认-->
      <!--<div class="accept text-end pr1-5 pt1 f13 mb3">
      	<p class="mb05"><span class="color333 b medium">邀请人</span></p>
      	<p class="mb05"><span class="color333 b medium">跟单经纪人：</span><span class="color333 b bold">皮皮凯</span></p>
      	<p class="mb05"><span class="f12 color999 medium">邀请时间：</span><span class="f12 color999 medium">1111/22/22 22:22:22</span></p>
      </div>-->
      <!--已支付按钮-->
      <!--<div class="pdyet-btn bgwhite f15"><span class="appraise look cff5a00">评价促单经纪人</span><span class="look look-order">查看我的订单</span><span class="look-pay look">查看付款情况</span></div>-->
      <!--待确认按钮-->
      <!--<div class="pd-btn bgwhite f15"><span class="to-pay f16 cfe556b">拒绝</span><span class="sign color-white">签署合同</span></div>-->
      
</section>
<section class="enjoy" style='padding-bottom:5.6rem'></section>
<section style="position: fixed;bottom: 0;background: #FFFFFF;" class="iphone_btn none"></section>
<section>
	<div class="common_pops none"></div>
	<div class="define none" style="width: 100%;margin: auto;height: 100%;text-align: center;">
		<img src="/images/agent/close_contact.png" style="width: 50%;text-align: center;margin-top: 30%;"/>
	</div>
	<div class="brand_down none" style="width: 100%;margin: auto;height: 100%;text-align: center;">
		<img src="/images/agent/brand_down.png" style="width: 50%;text-align: center;margin-top: 30%;"/>
	</div>
</section>
<!--<section class="enjoy" style='padding-bottom:5.5rem'></section>-->
@stop
@section('endjs')
<script type="text/javascript" src="{{URL::asset('/')}}/js/dist/agentfontsize.min.js"></script>
<script src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
<script src="{{URL::asset('/')}}/js/_v020800/pact_details.js"></script>
@stop