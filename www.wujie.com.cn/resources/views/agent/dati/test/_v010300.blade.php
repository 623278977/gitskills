@extends('layouts.default')
@section('css')
    <link href="/css/agent/_v010300/test.css" rel="stylesheet" type="text/css"/>
    <link href="/css/animate.css" rel="stylesheet" type="text/css"/>
@stop

@section('main')
    <section id="container" class="container">
           <div class="game_time"></div>
           <div class="tips none"></div>
           <article class="animated zoomInLeft none">
               <p class="color000 b f18"></p>
               <p class="f15 color000 mb4"></p>
              <div class="ui-test">
                  <div class="xuanti b f18 color666 mb2">
                      上茶          <img class="ui-size1 one" src="/images/020902/j.png">
                  </div>
                  <div class="xuanti b f18 color666 mb2 error">
                      上茶        <img class="ui-size1 fr transform" src="/images/020902/j.png">
                  </div>
                  <div class="xuanti b f18 color666 mb2 right">
                      上茶        <img class="ui-size1 fr transform" src="/images/020902/k.png">
                  </div>
                  <div class="xuanti b f18 color666 mb2">
                      上茶
                  </div>
              </div>
           </article>
           <div class ="bg-model none animated zoomInLeft">
        　　   <div class ='ui_content error none'>
                  <p class="off"> <img class="ui-size1  transform" src="/images/020902/j.png"></p>
                  <p class="clear f18 b center colorfff transform01">很遗憾，离正确就差一点点！</p>
                  <div class="dati f18 color333 center datitorromow">明天再战</div>
               </div>
                <div class ='ui_content right none'>
                  <p class="off"> <img class="ui-size1  transform" src="/images/020902/j.png"></p>
                  <p class="clear f18 b center colorfff transform01">恭喜你，回答正确~</p>
                  <section class="red-bga">
                      <ul class="ui-red-detail">
                             <li>
                                 <p class="f15 color333 b margin10 name">红包名称</p>
                                 <p class="f11 color666 margin11 type">支持加盟费用抵扣</p>
                                 <p class="f11 color666 margin11 time">有效期至2018.18.90</p>
                             </li>
                             <li>
                                 <p class="f31 f04 margin11 meony">￥200</p>
                                 <p class="f11 color666 margin11 meony2">满1000减11200</p>
                             </li>
                       </ul>
                   </section>
                  <p class="ui-a f11 clear transform01">红包自动存入你的福袋中.获得的红包可以发送至你的投资人，
                     让他们加盟更轻松！此外，请在红包有效期内尽快发送至投资
                     人，避免过期无效。
                  </p>
                  <div class=" clear dati f18 color333 center ffe433 mb2 lookfudai">查看福袋</div>
               </div>
           </div>
           <div style="width:100%;height:3rem"></div>
    </section>
@stop
@section('endjs')
<script src="/js/agent/_v010300/test.js"></script>
<script>
    $(document).ready(function(){
        $('title').text('测验答题');
    })
        var tt;
        var wait = 180;
        // var reg=/1[34578]\d{9}/;
        function time(o) {
        if (wait == 0) {
            o.removeAttr("disabled");
            o.html("00");
            o.css({
               "font-size":"2rem",
               "color":"#fff",
               "line-height":"6rem"
            });
               wait = 180;
               } else {
            o.attr("disabled", true);
            o.css({
              "font-size":"2rem",
              "color":"#fff",
              "line-height":"6rem"
            });
            o.html(wait);
            o.data('time',wait);
            wait--;
            tt = setTimeout(function () {
                    time(o)
                },
                1000)
               }
             };
        var that=$('.game_time');
        time(that);
            
</script>
@stop