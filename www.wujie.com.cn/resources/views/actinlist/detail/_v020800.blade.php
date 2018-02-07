@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/_v020800/actInvlist.css" rel="stylesheet" type="text/css"/>
   <!--  <link href="{{URL::asset('/')}}/css/v010000/actask.css" rel="stylesheet" type="text/css"/> -->
@stop
@section('main')
    <section id="act_container" class="none">
    <!-- 公用蒙层 -->
    <div class="tips none"></div>
        <!-- 顶部菜单栏切换 -->
        <ul class="ui_tab fixedtop">
            <li class="ui-border-r b ff5a00">待确认</li>
            <li class="ui-border-r">已接受</li>
            <li>已拒绝</li>
        </ul>
        <div style="width:100%;height:4.4rem" id="empty_box"></div>
       <!-- 下部邀请函正文 -->
       <div class="ui_container_wait firefox">
             <!-- <div class="ui_top_time">
                <div style="width:100%;height:2.2rem"></div>
                <center><div class="ui_show_time" >2017年1月</div></center>
             </div>
             <section class="ui_invite_container" style="height:23.5rem">
              <ul class="ui_address">
                <li class="f12 color999">活动</li>
                <li>
                  <div class="ui_con">
                    <ul class="ui_con1">
                      <li><img class="ui_img" src="{{URL::asset('/')}}/images/default/avator-m.png" alt=""></li>
                      <li>
                        <p class="toleft f12 margin3 toTop1 b color333 act-title">我是活动名称我是活动名称我是活动…</p>
                        <p class="toleft f10 margin3 color333 act-time">
                          <span class="ui_calendar_con">
                              <img class="ui_calendar fl"  src="{{URL::asset('/')}}/images/020700/ico.png" alt="">
                          </span>
                          <span class="ui_calendar_detail ">2017-01-14 17:00</span>
                        </p>
                        <p class="toleft f10 margin3 color333 act-city">
                          <span class="ui_calendar_con">
                              <img class="ui_calendar fl"  src="{{URL::asset('/')}}/images/020700/gg.png" alt="">
                          </span>
                          <span class="ui_calendar_detail">上海、杭州、南京</span>
                        </p>
                      </li>
                    </ul>
                  </div>
                </li>
              </ul>
              <div style="width:100%;height:1.3rem"></div>
              <p class="clear color999 f12">受邀人<span class="fr b color333">哈哈哈</span></p>
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
              </ul> -->
             <!-- </section> -->
            <div class="tc none nocomment" id="nocommenttip1">
                <img src="{{URL::asset('/')}}/images/nomessage_icon1.png" style="height: 16rem;width: 16rem;margin:0 auto;display: inline-block;">
            </div>     
       </div>
       <!-- 对方接受 -->
       <div class="ui_container_accept none firefox">
           <!-- <div class="ui_top_time">
                <div style="width:100%;height:2.2rem"></div>
                <center><div class="ui_show_time" >2017年1月</div></center>
            </div>
            <section class="ui_invite_container ui_add_border" style="height:18.5rem">
              <ul class="ui_address">
                <li class="f12 color999">活动</li>
                <li>
                  <div class="ui_con">
                    <ul class="ui_con1">
                      <li><img class="ui_img" src="{{URL::asset('/')}}/images/default/avator-m.png" alt=""></li>
                      <li>
                        <p class="toleft f12 margin3 toTop1 b color333 act-title">我是活动名称我是活动名称我是活动…</p>
                        <p class="toleft f10 margin3 color333 act-time">
                          <span class="ui_calendar_con">
                              <img class="ui_calendar fl"  src="{{URL::asset('/')}}/images/020700/ico.png" alt="">
                          </span>
                          <span class="ui_calendar_detail ">2017-01-14 17:00</span>
                        </p>
                        <p class="toleft f10 margin3 color333 act-city">
                          <span class="ui_calendar_con">
                              <img class="ui_calendar fl"  src="{{URL::asset('/')}}/images/020700/gg.png" alt="">
                          </span>
                          <span class="ui_calendar_detail">上海、杭州、南京</span>
                        </p>
                      </li>
                    </ul>
                  </div>
                </li>
              </ul>
              <div style="width:100%;height:1.3rem"></div>
              <p class="clear color999 f12">受邀人<span class="fr color333 b">哈哈哈</span></p>
              <p class="clear color999 f12">状态<span class="fr ce97">已确认</span></p>
              <p class="clear color999 f12 margin0">确认时间<span class="fr color333">2017年1月12日 18:00:00</span></p>
             </section> -->
             <div class="tc none nocomment" id="nocommenttip2">
                <img src="{{URL::asset('/')}}/images/nomessage_icon1.png" style="height: 16rem;width: 16rem;margin:0 auto;display: inline-block;">
             </div>        
       </div>
       <!-- 对方拒绝 -->
       <div class="ui_container_refuse none firefox">
           <!-- <div class="ui_top_time">
                <div style="width:100%;height:2.2rem"></div>
                <center><div class="ui_show_time" >2017年1月</div></center>
            </div>
            <section class="ui_invite_container ui_add_border" style="height:21rem">
              <ul class="ui_address">
                <li class="f12 color999">活动</li>
                <li>
                  <div class="ui_con">
                    <ul class="ui_con1">
                      <li><img class="ui_img" src="{{URL::asset('/')}}/images/default/avator-m.png" alt=""></li>
                      <li>
                        <p class="toleft f12 margin3 toTop1 b color333 act-title">我是活动名称我是活动名称我是活动…</p>
                        <p class="toleft f10 margin3 color333 act-time">
                          <span class="ui_calendar_con">
                              <img class="ui_calendar fl"  src="{{URL::asset('/')}}/images/020700/ico.png" alt="">
                          </span>
                          <span class="ui_calendar_detail ">2017-01-14 17:00</span>
                        </p>
                        <p class="toleft f10 margin3 color333 act-city">
                          <span class="ui_calendar_con">
                              <img class="ui_calendar fl"  src="{{URL::asset('/')}}/images/020700/gg.png" alt="">
                          </span>
                          <span class="ui_calendar_detail">上海、杭州、南京</span>
                        </p>
                      </li>
                    </ul>
                  </div>
                </li>
              </ul>
              <div style="width:100%;height:1.3rem"></div>
              <p class="clear color999 f12">受邀人<span class="fr b color333">哈哈哈</span></p>
              <p class="clear color999 f12">状态<span class="fr fd4d4d">已拒绝</span></p>
              <p class="clear color999 f12">拒绝理由<span class="fr color666">因为时间冲突，无法在当天来参加</span></p>
              <p class="clear color999 f12 margin0">确认时间<span class="fr color333">2017年1月12日 18:00:00</span></p>
             </section> -->
             <div class="tc none nocomment" id="nocommenttip3">
                <img src="{{URL::asset('/')}}/images/nomessage_icon1.png" style="height: 16rem;width: 16rem;margin:0 auto;display: inline-block;">
             </div>         
       </div>
    </section>
@stop
@section('endjs')
    <script type="text/javascript" src="{{URL::asset('/')}}/js/_v020800/actasklist.js"></script>
    <script>
      if(isiOS){
          if (window.screen.height === 812) {
              $('#empty_box').css('height', '88px');
              $('.fixedtop').css('top','13.5rem');
            }
        }
    </script>
@stop