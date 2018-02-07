@extends('layouts.default')
@section('css')
    <link href="/css/_v020902/fudai.css" rel="stylesheet" type="text/css"/>
    <link href="/css/animate.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section class="containerBox none" id="containerBox">
             <header class="animated zoomInLeft">
                 <img class="ui-size1" src="/images/default/avator-m.png">
             </header>
             <p class="ui-a color333 f18 b mt33 animated wobble">经纪人：Xaiver的红包</p>
             <p class="ui-b color999 f13 b animated wobble">为你加盟创业加油助力！</p>
             <article class="animated zoomInLeft">
                 <ul class="ui-red-detail">
                     <li>
                         <img class="ui-size2" src="/images/default/avator-m.png">
                     </li>
                     <li>
                         <p class="f15 color333 b margin10 name">和别磨蹭</p>
                         <p class="f11 color666 margin11 type">和别磨蹭</p>
                         <p class="f11 color666 margin11 time">和别磨蹭</p>
                     </li>
                     <li>
                         <p class="f31 f04 margin11 meony">￥200</p>
                         <p class="f11 color666 margin11 meony2">满1000减11200</p>
                     </li>
                 </ul>
                 <div class="clear" style="width:100%;height:1rem"></div>
             </article>
             <div class="ui-tips f11 color999 animated zoomInLeft">红包自动存入您的红包库，请在有效期内尽快使用</div>
             <div class="ui-da-kai none animated zoomInLeft">
                 <div class="fline f15 color666 title-top">
                     5分钟将红包拆开
                 </div>
                 <ul class="red-pocket-detail">
                     <li><img class="ui-size3" src="/images/default/avator-m.png"></li>
                     <li>
                         <p class="color999 f12"><span class="color333 f15">我的的</span>(132****5676)</p>
                         <p class="color666 f12">12-32 21:00</p>
                     </li>
                     <li>
                         <a class="ui-call f14 color666  fr">获得红包</a>
                     </li>
                 </ul>
                 <div class="clear" style="width:100%;height:0.1rem"></div>
             </div>
             <div class="ui-how-use animated zoomInLeft">
                 <div class="ui-title-for-text">
                     <img class="ui-size4" src="/images/reward.png">
                 </div>
                 <ul class="redbao-text">
                    <li><img class="ui-size5" src="/images/redbg.png"></li>
                    <li class="f12 color666">
                        <p>线上进行品牌加盟，支付首付款，除了常规的通用红包、品牌红包进行首付款金额抵扣，还有另一种奖励红包。</p>
                        <p>基于有效的门店考察，部分品牌会抽出奖励金额用户反馈用户，给予用户考察的车旅费用抵扣。</p>
                        <p>这部分费用将用于加盟首付款的支付抵扣,与品牌红包或通用红包进行叠加使用。</p>
                    </li>
                 </ul>
                 <div class="clear" style="height:1rem"></div>
             </div>
             <div class="ui-how-use animated zoomInLeft">
                 <div class="ui-title-for-text">
                     <img class="ui-size4" src="/images/reward1.png">
                 </div>
                 <ul class="redbao-text">
                    <li><img class="ui-size5" src="/images/redbg.png"></li>
                    <li class="f12 color666">
                        <p>奖励红包的获得，是通过接受经纪人发送的考察邀请函，并对品牌进行有效的门店考察。</p>
                        <p>* 以实际品牌为准，部分品牌未设置奖励红包。用户进行考察，并不会获得相应奖励。</p>
                        <p>如有异议，请联系无界商圈客服。</p>
                    </li>
                 </ul>
                 <div class="clear" style="height:2rem"></div>
             </div>
             <center class="mt2"><img class="ui-size6" src="/images/wujie.png"></center>
             <div style="width:100%;height:10rem"></div>
             <footer class="f14 f28">联系我们</footer>
    </section>
@stop
@section('endjs')
   <script src="/js/_v020902/fudai.js"></script>
   <script>
   	    $(document).ready(function(){
   	    	 $('title').text('无界商圈红包');
   	    })
   </script>
@stop