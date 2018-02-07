@extends('layouts.default')  
<!--zhangxm-->        
@section('css')
<link href="{{URL::asset('/')}}/css/agent/pactdetails.css" rel="stylesheet" type="text/css"/>

@stop
@section('main')
  <div class="ui_onmessage none" style="margin:10rem auto">
          <center><img src="{{URL::asset('/')}}/images/agent/turnoff_con.png" style="height: 16rem;width: 16rem;display: inline-block;"></center>
  </div>
<section class="containerBox">
	<!-- 待确认  无横幅-->
	<!--已签订   已结清  绿色背景-->     
	<!--<div class="head c57c88d down-pay ">
		<div class="">
			<img src="/images/agent/icon-true@2×.png" class="mr1"/>
			<span class="f15 ">投资人：氵水已签订酷道喜茶加盟加盟合同</span>
		</div>
		<img class="head-imgr " src="/images/agent/ico_pact.png">
	</div>-->
	<!--拒绝 红色背景-->
	<!--<div class="head cfc5d5d down-pay ">
		<div class="">
			<img src="/images/agent/ico_delete@2x.png" class="mr1"/>
			<span class="f15">投资人：氵水已拒绝酷道喜茶加盟加盟合同</span>
		</div>
		<img class="head-imgr " src="/images/agent/ico_pact.png">
	</div>-->
	<!--正文 公共说明-->
	<!--<div class="pub-state bgwhite mt3">
		<p class="bold f15 b color333 mb1-5">
			<span class="">To 无界商圈投资人&nbsp;</span><span class="cffa300"> 氵水 </span><span class="">&nbsp;女士</span>
		</p>
		
		<p class="pub-1 medium f13 color666">
			<span class="">通过经纪人</span><span class="cffa300"> (阿树) </span><span class="">的对接，您是否对品牌已经有了加盟的想法？</span><br />
		<span class="f13">在这里，向您发出品牌加盟的橄榄枝，我们提供全网最优质的服务和最低的加盟费用，并为您提供相应的加盟扶持。</span>
		</p>
		<span class="logo-img"><img src="/images/agent/logopact_10.png"/></span>
	</div>-->
	
	 <!--<div class="pact bgwhite mt1-2">
      	<div class=" ml08 pl2 pr1-5 pt05">-->
      		<!--公共合同信息-->
      			<!--<div class="act-2 ">
      				<div class="inst-2l ">
      					<p class="inst-2lp mb05"><span class="f12 color333">加盟合同</span><span class="f12 color666">合同名称</span></p>
      					<p class="inst-2lp mb05"><span class="f12 color333">合同号</span><span class="f12 color666">12343241</span></p>
      					<p class="inst-2lp mb05"><span class="f12 color333">加盟品牌</span><span class="f12 color666">喜茶</span></p>
      					<p class="inst-2lp mb05 text-end"><span class="f12 color333">合同撰写</span><span class="f12 color666">无界商圈法务代表<br />喜茶法务代表</span></p>
      				</div>
      			</div>-->
      			<!--待确认-->
      			<!--<div>
      				<p class="inst-2lp mb05"><span class="f12 color333">加盟总费用</span><span class="f12 color666">¥ 50.000</span></p>
      				<p class="inst-2lp mb05"><span class="f12 color333">线上首付</span><span class="f12 color666">¥ 50.000</span></p>
      				<p class="inst-2lp mb05"><span class="f12 color333">线下尾款</span><span class="f12 color666">¥ 50.000</span></p>
      				<div class="inst-2lp mb05 text-end">
  						<span class="f12 color333">支付状态</span> 
  						<p class="f12 medium">
  							<span class=" mb05 color666">* 请提醒投资人尽快支付尾款费用</span><br />
  							<span class=" mb05 color666">支付方式为线下对公账号转账</span><br />
  							<span class="c2873ff mb05">了解尾款补齐操作办法</span><br />
  						</p>
      				</div>
      				<p class="inst-2lp mb05"><span class="f12 color333">合同文本</span></p>
      					<div class="pct-2 mb1">
      						<div class="act-2l pact-text">
      							<img src="/images/agent/my_contract.png" class="pact-img mr1"/>
      							<p class="pact-2lp over-text">
      								<span class="over-text f14 b bold act-2lspan color333">喜茶加盟合同</span><br />	
      								<span class="over-text f11 act-2lspan color333">合同编号：3月3日 23:23</span><br />
      							</p>
      						</div>
      						<img src="/images/jump.png" class="pct-jump"/>
      					</div>
      			</div>-->
      			<!--拒绝-->
      			<!--<div>
      				<p class="inst-2lp mb05"><span class="f12 color333">加盟总费用</span><span class="f12 color666">¥ 50.000</span></p>
      				<p class="inst-2lp mb05"><span class="f12 color333">合同文本</span></p>
      					<div class="pct-2 mb1">
      						<div class="act-2l pact-text">
      							<img src="/images/agent/my_contract.png" class="pact-img mr1"/>
      							<p class="pact-2lp over-text">
      								<span class="over-text f14 b bold act-2lspan color333">喜茶加盟合同</span><br />	
      								<span class="over-text f11 act-2lspan color333">合同编号：3月3日 23:23</span><br />
      							</p>
      						</div>
      						<img src="/images/jump.png" class="pct-jump"/>
      					</div>
      			</div>-->
      			
      			
      			<!--已确认  已付清   首付-->
      			<!--<div class="">
      				<div class="inst-2l ">
      					<p class="down-pay mb05"><span class="fline wid"></span><span class="f12 color666">首付情况</span><span class="fline wid"></span></p>
      					<p class="inst-2lp mb05"><span class="f12 color333">首次支付</span><span class="f12 color666">¥ 23,333</span>
      					<p class="inst-2lp mb05"><span class="f12 color333">定金抵扣</span><span class="f12 color666">-¥ 23,333</span></p>
      					<p class="inst-2lp mb05"><span class="f12 color333">创业基金抵扣</span><span class="f12 color666">-¥ 0</span></p>
      					<p class="inst-2lp mb05"><span class="f12 color333">实际支付</span><span class="f12 color666">¥ 14,222</span></p>
      					<p class="inst-2lp mb05"><span class="f12 color333">支付状态</span><span class="f12 color666">已支付</span>
      					<p class="inst-2lp mb05 text-end"><span class="f12 color333">支付方式</span><span class="f12 color666 ">支付宝<br />251175150@qq.com</span></p>
      					<p class="inst-2lp mb05"><span class="f12 color333">支付时间</span><span class="f12 color666">1111/22/22 22:22:22</span>
      				</div>
      			</div>-->
      			<!--尾款-->
      			<!--<div class="act-2">
      				<div class="inst-2l ">
      					<p class="down-pay mb05"><span class="fline wid"></span><span class="f12 color666">尾款情况</span><span class="fline wid"></span></p>
      					<p class="inst-2lp mb05"><span class="f12 color333">尾款补齐</span><span class="f12 color666">¥ 23,333</span></p>-->
      					<!--未支付情况-->
      					<!--<div class="inst-2lp mb05 text-end">
      						<span class="f12 color333">支付状态</span> 
      						<p class="f12 medium">
      							<span class="cfd4d4d mb05">未支付</span><br />
      							<span class=" mb05 color666">* 请提醒投资人尽快支付尾款费用</span><br />
      							<span class=" mb05 color666">支付方式为线下对公账号转账</span><br />
      							<span class="c2873ff mb05">了解尾款补齐操作办法</span><br />
      						</p>
      					</div>-->
      					<!--已结清-->
      					<!--<div class=" mb05 text-end">
      						<p class="inst-2lp mb05"><span class="f12 color333">支付状态</span><span class="f12 c59c78a">已结清</span></p> 
      						<p class="inst-2lp mb05 text-end"><span class="f12 color333">支付方式</span><span class="f12 color666">支付宝<br />1234567890(工商银行)</span></p>
      						<p class="inst-2lp mb05"><span class="f12 color333">到账时间</span><span class="f12 color666">3222/22/22 22:22:22</span></p>
      						<p class="inst-2lp mb05"><span class="f12 color333">财务确认人</span><span class="f12 color666">皮皮凯</span></p>
      					</div>
      					<p class="inst-2lp mb05"><span class="f12 color333">合同文本</span></p>
      					<div class="pct-2 mb1">
      						<div class="act-2l pact-text">
      							<img src="/images/agent/my_contract.png" class="pact-img mr1"/>
      							<p class="pact-2lp over-text">
      								<span class="over-text f14 b bold act-2lspan color333">喜茶加盟合同</span><br />	
      								<span class="over-text f11 act-2lspan color333">合同编号：3月3日 23:23</span><br />
      							</p>
      						</div>
      						<img src="/images/jump.png" class="pct-jump"/>
      					</div>
      					<div class="fline"></div>
      					<p class="inst-2lp pt1 pb1"><span class="f12 color333">确定时间</span><span class="f12 color666">1111/22/22 22:22:22</span></p>
      				</div>
      			</div>
      	</div>
      </div>-->
      <!--落款处-->
      <!--已接受  已结清-->
      <!--<div class="accept text-end pr1-5 pt1 f13 mb3">
      	<p class="mb05"><span class="color333 b bold">邀请人</span></p>
      	<p class="mb05"><span class="color333 b bold">跟单经纪人：</span><span class="color333 b bold">皮皮凯</span></p>
      	<p class="mb05"><span class="color999">邀请时间：</span><span class="color999">1111/22/22 22:22:22</span></p>
      	<p class="mb05"><span class="color999">确定时间：</span><span class="color999">1111/22/22 22:22:22</span></p>
      </div>-->
      <!--已拒绝-->
      <!--<div class="pay-off text-end pr1-5 pt1 f13 mb3">
      	<p  class="mb05"><span class="color333 b bold">邀请人</span></p>
      	<p  class="mb05"><span class="color333 b bold">跟单经纪人：</span><span class="color333 b bold">皮皮凯</span></p>
      	<p  class="mb05"><span class="color999">邀请时间：</span><span class="color999">1111/22/22 22:22:22</span></p>
      	<p  class="mb05"><span class="color999">拒绝时间：</span><span class="color999">1111/22/22 22:22:22</span></p>
      	<p  class="mb05"><span class="color999">拒绝理由：</span><span class="color999">皮皮凯太菜，带不动</span></p>
      </div>-->
      <!--待确认-->
      <!--<div class="accept text-end pr1-5 pt1 f13 mb3">
      	<p class="mb05"><span class="color333 b bold">邀请人</span></p>
      	<p class="mb05"><span class="color333b bold">跟单经纪人：</span><span class="color333 b bold">皮皮凯</span></p>
      	<p class="mb05"><span class="">邀请时间：</span><span>1111/22/22 22:22:22</span></p>
      </div>
      
      <div class="pd-btn bgwhite f15"><span class="to-pay">查看付款情况</span><span class="money">查看我的提成</span></div>
      <div class="pd-btn bgwhite f15"><span class="cffa300">状态：待确认</span><span class="money">查看我的提成</span></div>-->
</section>
<section style="height:8.85rem;width:100%;"></section>
<section style="position: fixed;bottom: 0;background: #FFFFFF;" class="iphone_btn none"></section>
<section>
	<div class="common_pops none"></div>
</section>
@stop
@section('endjs')
<script src="{{URL::asset('/')}}/js/agent/_v010002/pactdetails.js"></script>
<script type="text/javascript">
    $('body').css('background','#f2f2f2');
	$(document).ready(function(){
    	$('title').text('付款协议详情'); 
    }); 
</script>
@stop