@extends('layouts.default')
@section('css')
     <link href="{{URL::asset('/')}}/css/agent/_v010100/list.css" rel="stylesheet" type="text/css"/> 
@stop
@section('main')
  <section class="containerBox none">
    <div class="ui-pictcon">
        <img class="ui-pict" src=""/>
    </div>
    <div class="ui-bg" style="width:100%;height:1rem;background: #fff"></div>
    <nav class="flex-around flex lh45 f13 ">
        <!-- <div class="choosen">推荐部分</div>
        <div>正能量占</div>
        <div>金句很想</div>
        <div>分类图说</div>
        <div>推荐部分</div>
        <div>正能量占</div> -->
    </nav> 
    <div style="width:100%;height:1rem;clear:both"></div>
    <footer>
            <!--  <ul class="con-list">
                <li><img class="ui-picture" src="/images/default/avator-m.png"/></li>
                <li>
                    <p class="f13 b c2873ff">金老师</p>
                    <p class="f13 b color333">哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈</p>
                    <ul class="soncontain">
                         <li><img class="ui-picturson" src="/images/default/avator-m.png"/></li>
                         <li><img class="ui-picturson" src="/images/default/avator-m.png"/></li>
                         <li><img class="ui-picturson" src="/images/default/avator-m.png"/></li>
                         <li><img class="ui-picturson" src="/images/default/avator-m.png"/></li>
                         <li><img class="ui-picturson" src="/images/default/avator-m.png"/></li>
                         <li><img class="ui-picturson" src="/images/default/avator-m.png"/></li>
                         <li><img class="ui-picturson" src="/images/default/avator-m.png"/></li>
                         <li><img class="ui-picturson" src="/images/default/avator-m.png"/></li>
                    </ul>
                    <p class="clear f11 color666">
                       已转发11次<button class="r  f12">立即转发</button>
                    </p>
                </li>
             </ul>
             <div class="clear fline style" ></div> -->
           <!--   <ul class="con-list">
                <li><img class="ui-picture" src="/images/default/avator-m.png"/></li>
                <li>
                    <p class="f13 b c2873ff">金老师</p>
                    <p class="f13 b color333">哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈</p>
                    <ul class="newcontain">
                         <li><img  src="/images/default/avator-m.png"/></li>
                    </ul>
                    <div class="clear" style="width:100%;height:0.5rem;"></div>
                    <p  class=" f11 color666">
                       已转发11次<button class="r  f12">立即转发</button>
                    </p>
                </li>
             </ul>
             <div class="clear fline style"></div> -->
    </footer>  
     <div style="width:100%;height:5rem;"></div>
     <input type="hidden" id="share">
  </section>
@stop
@section('endjs')
<script type="text/javascript" src="{{URL::asset('/')}}/js/agent/_v010100/wechat.js"></script>
@stop