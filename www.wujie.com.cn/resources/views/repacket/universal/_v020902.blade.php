@extends('layouts.default')
@section('css')
    <link href="/css/_v020902/universal.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section class="containerBox none" id="containerBox">
             <header></header>
             <article class="mt20">
             	 <ul class="ui-red-bao">
             	 	<li class="mt2">
             	 		<p class="b f32 f04 margin4">￥1000</p>
             	 		<p class="f12 f04 margin2">通用红包</p>
             	 		<p class="opcity">hah</p>
             	 	</li>
             	 	<li>
             	 		<p></p>
             	 		<p class="b f16 color333 margin5">新用户邀请红包</p>
             	 		<p class="f12 color999  margin3">不限使用期限；全场通用红包</p>
             	 		<p class="f12 color999 margin3">可用于考察订金抵扣、品牌加盟费用抵扣</p>
             	 	</li>
             	 </ul>
             	 <div class="clear">
             	 <div class="clear ui-bar"></div>
                 <p class="color666 f12 ui-bety hasagent">快去发现你中意的品牌吧！<button class="ui-use fr f14 b margin6">立即使用</button></p>	
             	 </div> 
             </article>
             <span class="ui-span"></span>
             <section class="ui-how-use color666 f12 none">
             	      <div class="ui-bg"></div>
             	      <p class="opcity" style="margin:0 0 0">hah</p>
             	      <ul class="ui-flex-text">
             	      	       <li><img class="ui-pict-size" src="/images/redbg.png"/></li>
             	      	       <li>
             	      	       	   <p class="transform100">在线上进进行考察订金、加盟首付款支付的时候，可以使用品牌红包进行抵扣。</p>
             	      	       	   <p>通用红包不限品牌。</p>
             	      	       	   <p>部分通用红包支持考察订金的抵扣。</p>
             	      	       	   <p>仅支持线上抵扣,不支持线下签约付款使用。</p>
             	      	       </li>
             	      </ul>
             	      <p class="clear" style="margin-top: 0.6rem">请在红包有效期内进行使用，超过期限，则无法正常使用。</p>
             	      <p>请确认红包是否支持叠加使用。</p>
             	      <p style="margin:0 0 0">红包使用如有问题，请联系无界商圈客服人员或您的经纪人。</p>
             </section>
             <div class="ui-has-used none " id="ui-has-used">
             	  <div class="fline ui-title f15 b color333">使用记录</div>
             	  <div class="fline ui-middle f14 color999">
             	  	   <p>加盟合同<span class="fr color333">呵呵呵</span></p>
             	  	   <p>合同号<span class="fr color333">呵呵呵</span></p>
             	  	   <p>加盟品牌<span class="fr color333">呵呵呵</span></p>
             	  	   <p>加盟总费用<span class="fr color333">￥10000,000</span></p>
             	  </div>
             	   <div class="fline ui-footer f14 color999">
             	  	   <p>考察订金抵扣<span class="fr fd4d4d">呵呵呵</span></p>
             	  	   <p>通用红包<span class="fr fd4d4d">-￥10000,000</span></p>
             	  	   <p>品牌红包<span class="fr fd4d4d">-￥10000,000</span></p>
             	  	   <p>奖励红包<span class="fr fd4d4d">￥10000,000</span></p>
             	  	   <p>返现金额<span class="fr color333">￥10000,000</span></p>
                       <p>实际支付金额<span class="fr color333">￥10000,000</span></p>
             	  </div>
             </div>
             <div class="ui-has-ordered none">
                  <div class="fline ui-title f15 b color333">使用记录</div>
                  <div class="fline ui-middle-p f14 color999">
                       <p>订单类型<span class="fr color333">考察定金</span></p>
                       <p>品牌名称<span class="fr color333">喜茶</span></p>
                       <p>订单状态<span class="fr color333">已支付</span></p>
                       <p>红包抵扣<span class="fr fd4d4d">-￥1000,000</span></p>
                       <p>实际支付<span class="fr color333">￥0</span></p>
                      <!--  <p>支付方式<span class="fr color333">￥10000,000</span></p> -->
                       <p>支付时间<span class="fr color333">2016年10月11日 23:17:00</span></p>
                  </div>
             </div>
             <footer class="f14 f28"><a href="tel:4000110061" class="f14 f28" style="display: block;width: 100%;height:100%">联系我们</a></footer>
    </section>
@stop
@section('endjs')
   <script src="/js/_v020902/universal.js"></script>
   <script>
   	    $(document).ready(function(){
   	    	$('title').text('通用红包')
   	    })
   </script>
@stop