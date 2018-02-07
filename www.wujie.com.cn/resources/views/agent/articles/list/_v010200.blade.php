<!-- Created by wangcx -->

@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/dist/swiper.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/agent/_v010004/list.css" rel="stylesheet" type="text/css"/>
    <!-- <link href="{{URL::asset('/')}}/css/agent/_v010004/articles.css" rel="stylesheet" type="text/css"/> -->
    <style type="text/css">
        .flex{
          display: flex;
        }
        .flex-around{
          justify-content: space-around;
        }
        .lh45{
          height: 4.5rem;
          line-height:4.5rem;
        }
        .c2873ff{
          color:#2873ff;
        }
        .choosen{
          color:#2873ff;
          border-bottom: 2px solid #2873ff;
        }
        .banner{
          padding-bottom:1.2rem;
          background: #f2f2f2;
        }
        .banner .slide_img{
          width: 100%;
          height: 12rem;
        }
        .tf.fline::after{
            top:-1px;
            bottom: 0;
        }

    </style>
@stop
@section('main')
  <section class="containerBox bgcolor " style="min-height: 100%;" id="containerBox">
    <div class="bgwhite">
      <nav class="flex-around flex lh45 f13">
       <!--  <div class="choosen">推荐</div>
        <div>正能量</div>
        <div>金句</div>
        <div>分类</div>
        <div>自定义</div> -->
      </nav>
      <div class="banner swiper-container" id="swiper-container">
        <div class="swiper-wrapper">
          <!-- <div class="swiper-slide">
            <img src="/images/agent/ins-success.png" class="slide_img">
          </div> -->
          <!--  <div class="swiper-slide">
            <img src="/images/agent/ins-success.png">
          </div> -->
         <!-- <div class="swiper-slide">
            <img src="/images/agent/ins-success.png">
          </div> -->

        </div>
        <div class="swiper-pagination"></div>
      </div>
      <div class="commend">
          <!--  <div class="ui_con color999">
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
           </div> -->
           
      </div>
     <div class="tc lh45 color999 f12 tf fline getmore none"><img style="width:1.2rem;height:1.2rem" src="/images/agent/h.gif"/>正在加载</div>
     <div class="tc lh45 color999 f12 tf fline none nomore" style="background: #f2f2f2;">已加载全部</div>
    </div>  
  </section>
@stop
@section('endjs')
    <script type="text/javascript" src="{{URL::asset('/')}}/js/dist/swiper-3.4.2.min.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/agent/getlist.js"></script>
    <script>
      var $body = $('body');
      document.title = "商圈热文";
      // hack在微信等webview中无法修改document.title的情况
      var $iframe = $('<iframe ></iframe>').on('load', function() {
      setTimeout(function() {
      $iframe.off('load').remove()
      }, 0)
      }).appendTo($body);
  </script> 
 
@stop