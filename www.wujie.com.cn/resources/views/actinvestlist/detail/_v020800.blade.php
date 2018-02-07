@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/_v020800/actinvestlist.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section id="act_container">
    <!-- 公用蒙层 -->
    <div class="tips none"></div>
        <!-- 顶部菜单栏切换 -->
        <ul class="ui_tab fixedtop">
            <li class="ui-border-r b ff5a00">待确认</li>
            <li class="ui-border-r">已接受</li>
            <li>已拒绝</li>
        </ul>
        <div style="width:100%;height:4.4rem"></div>
       <div class="ui_container_wait  firefox">
             <!-- <div class="ui_top_time">
                <div style="width:100%;height:2.2rem"></div>
                <center><div class="ui_show_time" >2017年1月</div></center>
             </div>
             <section class="ui_invite_container" style="height:31.5rem">
               <p class="clear color999 f12">考察品牌<span class="fr b color333">喜茶</span></p>
                <ul class="ui_address">
                  <li class="f12 color999">考察场地</li>
                  <li>
                    <div class="ui_con">
                       <p class="f12 b color333 toleft">总部：杭州喜茶@block</p>
                       <p class="f12  color333 margin0 toleft">
                         <span class="ui_calendar_con">
                           <img class="ui_calendar fl"  src="{{URL::asset('/')}}/images/020700/gg.png" alt="">
                         </span>
                         <span style="padding-left: 0.5rem">地址：杭州市上城区滨湖银泰路</span>
                       </p>
                    </div>
                  </li>
                </ul>
                <div style="width:100%;height:1.3rem;clear:both"></div>
                <p class="clear color999 f12">考察时间<span class="fr color333">2017年1月12日</span></p>
                <p class="clear color999 f12">定金金额<span class="fr color333">哈哈哈</span></p>
                <p class="clear color999 f12">受邀人<span class="fr color333">哈哈哈</span></p>
                <ul class="ui_status">
                  <li class="f12 color999">状态</li>
                  <li>
                    <p class="f12 ffa300">待确认</p>
                    <p class="f12 color333">还剩4天17时25分</p>
                  </li>
                </ul>
                <div style="width:100%;height:1.3rem;clear:both"></div>
                <ul class="accept_refuse clear">
                  <li><a class="ui_border ui_refuse f13 color666 ">拒绝</a></li>
                  <li><a class="f13 ui_accept clear ">接受活动邀请函</a></li>
                </ul>
             </section> -->
            <div class="tc none nocomment" id="nocommenttip1">
                <img src="{{URL::asset('/')}}/images/nomessage_icon1.png" style="height: 16rem;width: 16rem;margin:0 auto;display: inline-block;">
            </div>     
       </div>
       <!-- 对方接受 -->
       <div class="ui_container_accept  firefox none">
           <!-- <div class="ui_top_time">
                <div style="width:100%;height:2.2rem"></div>
                <center><div class="ui_show_time" >2017年1月</div></center>
            </div>
            <section class="ui_invite_container" style="height:28.8rem">
               <p class="clear color999 f12">考察品牌<span class="fr b color333">喜茶</span></p>
                <ul class="ui_address">
                  <li class="f12 color999">考察场地</li>
                  <li>
                    <div class="ui_con">
                       <p class="f12 b color333 toleft">总部：杭州喜茶@block</p>
                       <p class="f12  color333 margin0 toleft">
                         <span class="ui_calendar_con">
                           <img class="ui_calendar fl"  src="{{URL::asset('/')}}/images/020700/gg.png" alt="">
                         </span>
                         <span style="padding-left: 0.5rem">地址：杭州市上城区滨湖银泰路</span>
                       </p>
                    </div>
                  </li>
                </ul>
                <div style="width:100%;height:1.3rem;clear:both"></div>
                <p class="clear color999 f12">考察时间<span class="fr color333">2017年1月12日</span></p>
                <p class="clear color999 f12">订金金额<span class="fr color333">哈哈哈</span></p>
                <p class="clear color999 f12">支付方式<span class="fr color333">哈哈哈</span></p>
                <p class="clear color999 f12">支付时间<span class="fr color333">2017年1月12日 18:00:00</span></p>
                <p class="clear color999 f12">邀请人<span class="fr color333">张张（经纪人）</span></p>
                <p class="clear color999 f12 margin0">确认时间<span class="fr color333">2017年1月12日 18:00:00</span></p>
             </section> -->
             <div class="tc none nocomment" id="nocommenttip2">
                <img src="{{URL::asset('/')}}/images/nomessage_icon1.png" style="height: 16rem;width: 16rem;margin:0 auto;display: inline-block;">
             </div>        
       </div>
       <!-- 对方拒绝 -->
       <div class="ui_container_refuse firefox none">
          <!--  <div class="ui_top_time">
                <div style="width:100%;height:2.2rem"></div>
                <center><div class="ui_show_time" >2017年1月</div></center>
            </div>
            <section class="ui_invite_container " style="height:29rem">
              <p class="clear color999 f12">考察品牌<span class="fr b color333">喜茶</span></p>
                <ul class="ui_address">
                  <li class="f12 color999">考察场地</li>
                  <li>
                    <div class="ui_con">
                       <p class="f12 b color333 toleft">总部：杭州喜茶@block</p>
                       <p class="f12  color333 margin0 toleft">
                         <span class="ui_calendar_con">
                           <img class="ui_calendar fl"  src="{{URL::asset('/')}}/images/020700/gg.png" alt="">
                         </span>
                         <span style="padding-left: 0.5rem">地址：杭州市上城区滨湖银泰路</span>
                       </p>
                    </div>
                  </li>
                </ul>
                <div style="width:100%;height:1.3rem;clear:both"></div>
                <p class="clear color999 f12">考察时间<span class="fr color333">2017年1月12日</span></p>
                <p class="clear color999 f12">订金金额<span class="fr color333">哈哈哈</span></p> 
                <p class="clear color999 f12">邀请人<span class="fr color333">张张（经纪人）</span></p>
                <p class="clear color999 f12">状态<span class="fr ff4d4d">已拒绝</span></p>
                <p class="clear color999 f12">拒绝理由<span class="fr color333">哈哈哈哈哈哈哈哈</span></p>
                <p class="clear color999 f12 margin0">确认时间<span class="fr color333">2017年1月12日 18:00:00</span></p>
             </section> -->
             <div class="tc none nocomment" id="nocommenttip3">
                <img src="{{URL::asset('/')}}/images/nomessage_icon1.png" style="height: 16rem;width: 16rem;margin:0 auto;display: inline-block;">
             </div>         
       </div>
    </section>
@stop
@section('endjs')
   <script type="text/javascript" src="{{URL::asset('/')}}/js/_v020800/actinvestlist.js"></script>
@stop