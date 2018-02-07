@extends('layouts.default')
@section('css')
    <link href="/css/agent/_v010300/fudai.css" rel="stylesheet" type="text/css"/>
    <link href="/css/animate.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section class="containerBox none" id="containerBox">
             <header class="animated zoomInLeft">
                 <img class="ui-size1" src="/images/default/avator-m.png">
             </header>
             <p class="ui-a color333 f18 b mt33 animated zoomInLeft">经纪人：Xaiver的红包</p>
             <p class="ui-b color999 f13 b animated zoomInLeft">为你加盟创业加油助力！</p>
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
             <div class="ui-tips f11 color999 none animated zoomInLeft">红包暂未被领取，5小时内未领取将自动退回至您的福袋。</div>
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
                         <a class="ui-call f14 b fff fr gochat">联系他</a>
                     </li>
                 </ul>
                 <div class="clear" style="width:100%;height:0.1rem"></div>
             </div>
             <footer class="f14 f28 animated zoomInLeft">联系我们</footer>
    </section>
@stop
@section('endjs')
   <script src="/js/agent/_v010300/fudai.js"></script>
   <script>
   	    $(document).ready(function(){
   	    	$('title').text('无界商圈红包')
   	    })
   </script>
@stop