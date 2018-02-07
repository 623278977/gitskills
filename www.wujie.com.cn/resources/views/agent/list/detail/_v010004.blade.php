@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/_v010004/list.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section id="act_container" class="none">
             <div style="width:100%;height:1rem"></div>
             <div class="ui-list"></div>
            <!--  <div class="ui_con color999">
                  <div class="padding">
                        <ul class="ui_text_pict">
                             <li>
                                 <p class="color333 f14 b ui-nowrap-multi">骤然的爱情，穿不过永不睡哈哈哈哈</p>
                                 <p class="f12 ui-nowrap-multi">
                                    狮子（Lion）被假设等同于行走动物（Walk），老鹰（Eagle）被假设等同于飞行动物（Fly）。
                                    这看起来很成功，因为子类能严格向上转型，但他有隐患。
                                    当有一种天马（Pegasus）介入到里面的时候，我们才发现狮子其实只是“会行走的动物”， 
                                 </p>
                             </li>
                             <li>
                              <div class="ui_protect_pict fr"> <img class="ui_pict1" src="/images/agent/ui1.png"/></div>
                             </li>
                        </ul>
                        <p class="clear ui-border-b ui_row"></p>
                        <ul class="ui_text_down clear f11">
                              <li>
                                <ul class="ui_flex">
                                    <li>
                                      <img class="ui_zan fl" src="/images/agent/grey.png"/><span class="ui_padding fl">6000</span>
                                    </li>
                                    <li>
                                      <img class="ui_zan ui_mess" src="/images/agent/ui.png"/><span class="ui_padding">6000</span>
                                    </li>
                                    <li>
                                      <img class="ui_seen ui_mess" src="/images/agent/seen.png"/><span class="ui_padding">6000</span>
                                    </li>
                                </ul>
                              </li>
                              <li>作者：无界商圈</li>
                        </ul>
                        <p class="clear margin"></p>
                    </div>
                  <div class="fline style"></div>
            </div>
            <div class="ui_con color999">
                  <div class="padding">
                        <ul class="ui_text_pict">
                             <li>
                                 <p class="color333 f14 b ui-nowrap-multi">骤然的爱情，穿不过永不睡哈哈哈哈哈</p>
                                 <p class="f12 ui-nowrap-multi">
                                    狮子（Lion）被假设等同于行走动物（Walk），老鹰（Eagle）被假设等同于飞行动物（Fly）。
                                    这看起来很成功，因为子类能严格向上转型，但他有隐患。
                                    当有一种天马（Pegasus）介入到里面的时候，我们才发现狮子其实只是“会行走的动物”， 
                                 </p>
                             </li>
                             <li>
                              <div class="ui_protect_pict fr"><img class="ui_pict1" src="/images/agent/ui2.png"/></div>
                             </li>
                        </ul>
                        <p class="clear ui-border-b ui_row"></p>
                        <ul class="ui_text_down clear f11">
                              <li>
                                <ul class="ui_flex">
                                    <li>
                                      <img class="ui_zan fl" src="/images/agent/grey.png"/><span class="ui_padding fl">6000</span>
                                    </li>
                                    <li>
                                      <img class="ui_zan ui_mess" src="/images/agent/ui.png"/><span class="ui_padding">6000</span>
                                    </li>
                                    <li>
                                      <img class="ui_seen ui_mess" src="/images/agent/seen.png"/><span class="ui_padding">6000</span>
                                    </li>
                                </ul>
                              </li>
                              <li>作者：无界商圈</li>
                        </ul>
                        <p class="clear margin"></p>
                    </div>
                  <div class="fline style"></div>
             </div>
             <div class="ui_con color999">
                  <div class="padding">
                        <ul class="ui_text_pict">
                             <li style="width:100%">
                                 <p class="color333 f14 b ui-nowrap-multi">骤然的爱情，穿不过永不睡哈哈哈哈哈</p>
                                 <p class="f12 ui-nowrap-multi">
                                    狮子（Lion）被假设等同于行走动物（Walk），老鹰（Eagle）被假设等同于飞行动物（Fly）。
                                    这看起来很成功，因为子类能严格向上转型，但他有隐患。
                                    当有一种天马（Pegasus）介入到里面的时候，我们才发现狮子其实只是“会行走的动物”， 
                                 </p>
                             </li>
                        </ul>
                        <p class="clear ui-border-b ui_row"></p>
                        <ul class="ui_text_down clear f11">
                              <li>
                                <ul class="ui_flex">
                                    <li>
                                      <img class="ui_zan fl" src="/images/agent/grey.png"/><span class="ui_padding fl">6000</span>
                                    </li>
                                    <li>
                                      <img class="ui_zan ui_mess" src="/images/agent/ui.png"/><span class="ui_padding">6000</span>
                                    </li>
                                    <li>
                                      <img class="ui_seen ui_mess" src="/images/agent/seen.png"/><span class="ui_padding">6000</span>
                                    </li>
                                </ul>
                              </li>
                              <li>作者：无界商圈</li>
                        </ul>
                        <p class="clear margin"></p>
                    </div>
                  <div class="fline style"></div>
             </div> -->
             <button class="getmore">加载更多数据</button>
             <div class="tc none nocomment" id="nocommenttip2">
                  <img src="{{URL::asset('/')}}/images/nomessage_icon1.png" style="height: 16rem;width: 16rem;margin:0 auto;display: inline-block;">
              </div>   
    </section>
@stop
@section('endjs')
<script type="text/javascript" src="{{URL::asset('/')}}/js/agent/_v010004/list.js"></script>
@stop