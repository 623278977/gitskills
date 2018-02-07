@extends('layouts.default')
@section('css')
   <link href="{{URL::asset('/')}}/css/agent/_v010002/investask.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section id="act_container" class="none">
        <div class="tips none"></div>
        <!--安装app-->
        <div class="app_install fixed none" id="installapp">
            <i class="l">打开无界商圈APP，体验更多精彩内容 >></i>
            <span class="r" id="openapp"><img src="{{URL::asset('/')}}/images/yaoqing.png" alt=""></span>
            <div class="clearfix"></div>
        </div>
        <!-- 顶部菜单栏切换 -->
        <ul class="ui_tab fixedtop">
            <li class="ui-border-r b blue">等待对方确认</li>
            <li class="ui-border-r">对方接受</li>
            <li>对方拒绝</li>
        </ul>
        <div style="width:100%;height:4.4rem"></div>
       <!-- 下部邀请函正文 -->
       <div class="ui_container_wait">
            <!--  <div class="ui_top_time">
                <div style="width:100%;height:2rem"></div>
                <center><div class="ui_show_time" >2017年1月7日</div></center>
             </div>
             <section class="ui_invite_container add_bg" style="min-height:27rem">
               <div class="ui_infor">
                 <ul class="ui_infor_tab">
                   <li class="f12 color333">邀请人</li>
                   <li>
                        <p class=" f12 b margin05">hahh</p>
                        <p class="f12 margin05">hahah</p>   
                   </li>
                   <li><img class="fr avator" src="{{URL::asset('/')}}/images/020700/gold.png" alt=""></li>
                 </ul>
               </div>
               <div style="width:100%;height:1rem"></div>
              <ul class="ui_address">
                <li class="f12 color333">考察场地</li>
                <li>
                  <div class="ui_address_text">
                    <p class="ui_address_detail">总部：杭州喜茶我</p>
                    <p class="ui_address_pict">
                      <img class="fl martop"  src="{{URL::asset('/')}}/images/020700/gg.png" alt="">
                      <span id="ui_detail">杭州市人民检察院</span>
                    </p>
                  </div>
                </li>
              </ul>
              <div style="width:100%;height:1.5rem"></div>
              <p class="ui_go_time"><span class="ui_go_time_text color333 f12">考察时间</span><span class="ui_go_time_ fr color666 f12">2017-1-15</span></p>
              <p class="ui_order_money"><span class="ui_go_time_text color333 f12">定金金额</span><span class="ui_go_time_ fr color666 f12">￥3000</span></p>
              <ul class="ui_circle">
                 <li><div class="ui_left_circle"></div></li>
                 <li><div class="ui_dotted"></div></li>
                 <li><div class="ui_right_circle fr"></div></li>
              </ul>
              <ul class="ui_status">
                <li class="f12 color333">状态</li>
                <li style="height:1.5rem;line-height:1.5rem;margin-top:0.6rem">
                  <p class="fr float a6a6 f12">待确认</p>
                  <p class="ui_sheng_time a6a6 f12" style="margin:0 0 0 0">还剩4天17时25分</p>
                </li>
              </ul>
              <div style="width:100%;height:2rem;clear:both"></div>
              <ul class="accept_refuse clear">
                  <li class="ui_upordown"><a class="ui_border ui_refuse f13 color666 ">关闭</a></li>
                  <li class="ui_send_again"><a class="f13 ui_accept clear ">再次发送</a></li>
              </ul>
             </section> -->
             <!-- <div class="ui_send_again">再次发送</div> -->
             <div class="tc none nocomment" id="nocommenttip1">
                <img src="{{URL::asset('/')}}/images/nomessage_icon1.png" style="height: 16rem;width: 16rem;margin:0 auto;display: inline-block;">
            </div>     
       </div>
       <!-- 对方已经接受 -->
       <div class="ui_container_accept none">
          <!-- <div class="ui_top_time">
                <div style="width:100%;height:2rem"></div>
                <center><div class="ui_show_time" >2017年1月7日</div></center>
          </div>
          <section class="ui_invite_container" style="height:29.55rem">
               <div class="ui_infor">
                 <ul class="ui_infor_tab">
                  <li class="f12 color333">邀请人</li>
                   <li>
                        <p class=" f12 b margin05">hahh</p>
                        <p class="f12 margin05">hahah</p>
                        
                   </li>
                   <li><img class="fr avator" src="{{URL::asset('/')}}/images/020700/gold.png" alt=""></li>
                 </ul>
               </div>
               <div style="width:100%;height:1rem"></div>
              <ul class="ui_address">
                <li class="f12 color333">考察场地</li>
                <li>
                  <div class="ui_address_text">
                    <p class="ui_address_detail">总部：杭州喜茶@black</p>
                    <p class="ui_address_pict">
                      <img class="fl martop"  src="{{URL::asset('/')}}/images/020700/gg.png" alt="">
                      <span id="ui_detail">杭州市人民检察院</span>
                    </p>
                  </div>
                </li>
              </ul>
              <div style="width:100%;height:1.2rem"></div>
              <p class="ui_go_time"><span class="ui_go_time_text color333 f12">考察时间</span><span class="ui_go_time_ fr color666 f12">2017-1-15</span></p>
              <p class="ui_order_money">
                  <span class="ui_go_time_text color333 f12">定金金额</span><span class="ui_go_time_ fr color666 f12">￥3000</span>
              </p>
              <p class="ui_order_money" style="margin:0 0 0 0">
                  <span class="ui_go_time_text color333 f12">支付方式</span><span class="ui_go_time_ fr color666 f12">支付宝()</span>
              </p>
              <div style="width:100%;height:1.9rem"></div>
              <ul class="ui_status_">
                <li class="f12 color333" style="margin: 1rem 0 1rem 0;">状态<span class="fr  ffaf20">已接受邀请</span></li>
                <li style="clear:both" class="f12 color333 ">确认时间<span class="fr a6a6">2017-15-12 18:00:00</span></li>
              </ul>
             </section> -->
             <div class="tc none nocomment" id="nocommenttip2">
                <img src="{{URL::asset('/')}}/images/nomessage_icon1.png" style="height: 16rem;width: 16rem;margin:0 auto;display: inline-block;">
            </div>     
       </div>
       <!-- 对方已经拒绝 -->
       <div class="ui_container_refuse none">
            <!--  <div class="ui_top_time">
                <div style="width:100%;height:2rem"></div>
                <center><div class="ui_show_time" >2017年1月7日</div></center>
             </div>
             <section class="ui_invite_container" style="height:30.1rem">
               <div class="ui_infor">
                 <ul class="ui_infor_tab">
                   <li class="f12 color333">邀请人</li>
                   <li>
                        <p class=" f12 b margin05">hahh</p>
                        <p class="f12 margin05">hahah</p>
                        
                   </li>
                   <li><img class="fr avator" src="{{URL::asset('/')}}/images/020700/gold.png" alt=""></li>
                 </ul>
               </div>
               <div style="width:100%;height:1rem"></div>
              <ul class="ui_address">
                <li class="f12 color333">考场场地</li>
                <li>
                  <div class="ui_address_text">
                    <p class="ui_address_detail">总部：杭州喜茶@black</p>
                    <p class="ui_address_pict">
                      <img class="fl martop"  src="{{URL::asset('/')}}/images/020700/gg.png" alt="">
                      <span id="ui_detail">杭州市人民检察院</span>
                    </p>
                  </div>
                </li>
              </ul>
              <div style="width:100%;height:1.2rem"></div>
              <p class="ui_go_time"><span class="ui_go_time_text color333 f12">考察时间</span><span class="ui_go_time_ fr color666 f12">2017-1-15</span></p>
              <p class="ui_order_money"><span class="ui_go_time_text color333 f12">定金金额</span><span class="ui_go_time_ fr color666 f12">￥3000</span></p>
              <div style="width:100%;height:0.8rem"></div>
              <ul class="ui_status_">
                <li class="f12 color333">状态 <span class="fr  fd4d4d">已拒绝</span></li>
                <li class="f12 color333">拒绝理由 <span class="fr color333">因时间冲突无法在当天参加会议因时间</span></li>
                <li style="clear:both" class="f12 color333 ">确认时间<span class="fr a6a6">2017-15-12 18:00:00</span></li>
              </ul>
             </section> -->
             <div class="tc none nocomment" id="nocommenttip3">
                <img src="{{URL::asset('/')}}/images/nomessage_icon1.png" style="height: 16rem;width: 16rem;margin:0 auto;display: inline-block;">
            </div>     
       </div>
    </section>
@stop
@section('endjs')
    <script type="text/javascript" src="{{URL::asset('/')}}/js/agent/_v010002/investask.js"></script>
@stop