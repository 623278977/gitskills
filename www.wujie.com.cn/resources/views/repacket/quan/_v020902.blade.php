@extends('layouts.default')
@section('css')
    <link href="/css/_v020902/quan.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section class="containerBox" id="containerBox">
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
                 <p class="color666 f12 ui-bety">快去发现你中意的品牌吧！<button class="ui-use fr f14 b margin6">进入聊天窗</button></p>	
             	 </div> 
             </article>
             <span class="ui-span"></span>
             <section class="ui-how-use color666 f12 none">
             	      <div class="ui-bg"></div>
                      <p class="opcity" style="margin:0 0 0">hah</p>
             	      <p class="clear">
                        <img class="tranform001" src="/images/020902/u.png"/>
                        <span style="padding-left: 0.2rem">所邀请的投资人在平台成功加盟品牌这时候,您获得的千元邀请奖券</span>
                        <span style="padding-left: 1rem">会直接兑现，变现成现金！</span></p>
             	      <p>
                         <img class="tranform001" src="/images/020902/u.png"/>
                         <span  style="padding-left: 0.2rem">您可以关注投资人在平台上的动态，也可以推荐优质品牌给投资人</span>
                         <span  style="padding-left: 1rem">促使他们加盟品牌。</span>
                     </p>
             	      <p style="margin:0 0 0">
                        <img class="tranform001" src="/images/020902/u.png"/>
                        <span>商机掌握在您手中，不要让他溜走！快和您的邀请投资人联系吧！</span>
                      </p>
                      <p class="f10" style="text-align:center;margin:4rem 0 0">有疑问联系我们的客服人员，最终解释归无界商圈所有。</p>
             </section>
             <div class="ui-has-used">
             	  <div class="fline ui-title f15 b color333">详情说明</div>
             	  <div class="fline ui-footer f14 color999">
             	  	   <p>投资人<span class="fr color333">呵呵呵</span></p>
             	  	   <p>加盟品牌<span class="fr color333">-￥10000,000</span></p>
             	  	   <p>加盟金额<span class="fr color333">-￥10000,000</span></p>
             	  	   <p>促单经纪人<span class="fr color333">￥10000,000</span></p>
             	  	   <p>邀请经纪人<span class="fr color333">￥10000,000</span></p>
             	  	   <p>成交时间<span class="fr color333">2017.10.11 18:00:00</span></p>
             	  </div>
             </div>
             <footer class="f14 f28"><a href="tel:4000110061" class="f14 f28" style="display: block;width: 100%;height:100%">联系我们</a></footer>
    </section>
@stop
@section('endjs')
   <script>
   	    $(document).ready(function(){
   	    	$('title').text('奖券')
   	    })
   </script>
@stop