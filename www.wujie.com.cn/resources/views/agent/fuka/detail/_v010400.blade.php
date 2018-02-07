@extends('layouts.default')
@section('css')
    <link href="/css/agent/_v010300/fuka.css" rel="stylesheet" type="text/css"/>
    <link href="/css/animate.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section class="containerBox none" id="containerBox">
             <header class="animated zoomInLeft">
                 <img class="ui-size1 border-radius" src="/images/agent-share-logo.png">
             </header>
             <div class="animated zoomInLeft mt33 hello" style="margin-top: 5.5rem"> 
              <center><img class="ui-size7" src="/images/fu.png"></center>
            </div>
             <p class="ui-b color999 f13  animated zoomInLeft mt2  hello2">获得一张福卡，祝您新年心想事成</p>
             <div class="ui-share f3 f20 b animated zoomIn " style="margin-top: 3.5rem">分享有才</div>
             <p class="ui-b color999 f13  animated zoomInLeft mt1">你还有<span class="hello3" style="color:#ff0000">0</span>次机会获得赏金</p>
             <article class="animated zoomInLeft mt33">
                <div class="ui-pict-con"><img class="ui-use" src="/images/use.png"/></div>
                <div class="ui-text-detail color333 f12">
                    <p>1.点击主页面“ 立即开福袋 ” 按钮，抽取新年礼包（红包或福卡），最高可获得100元现金红包</p>
                    <p>2.每人每天均有1次抽取新年礼包的机会;</p>
                    <p>3.分享无界商圈新春活动页面至好友，并邀请他们注册成为无界商圈投资人，您将额外获得1次抽取新年礼包的机会！</p>
                    <p>4.集福卡奖励<br/>一等奖：集齐 “无”“界”“商”“圈”“福”5张福卡，获得88元现金红包<br/> 二等奖：集齐“无”“界”“商”“福”4张福卡，获得18元现金红包；<br/>三等奖：集齐“无”“界”“商”“圈”4张福卡中任意3张连贯卡，即可获得8.88现金红包
                    </p>
                    <p>5.活动结束后，于2月24日统一公布获奖名单；</p>
                    <p>6.现金红包会自动划入您的钱包，可在账目结清后进行提现<br/>操作。实打实的现金，还不心动来抽取？</p>
                    <p>7.活动最终解释权归杭州天涯若比邻网络信息服务有限公司<br/>所有，有任何疑问或者帮助可以联系客服：400-011-0061。</p>
                </div>
             </article>
             <div style="width:100%;height:16rem"></div>
             <footer class="f14 f28 animated zoomInLeft fixed-bottom-iphoneX">联系我们</footer>
    </section>
    <div class="tc none nocomment" id="nocommenttip3">
                <img src="{{URL::asset('/')}}/images/nomessage_icon1.png" style="height: 16rem;width: 16rem;margin:5rem auto;display: inline-block;">
    </div>    
@stop
@section('endjs')
   <script src="/js/agent/_v010300/fuka.js"></script>
   <script>
   	    $(document).ready(function(){
   	    	$('title').text('无界商圈红包')
   	    })
        var wechat=[
         {
            title:'收到一份神秘大礼！',
            content:'你的好友为你定制了一份新年礼物，拆开看看吧。',
            img:'/images/redbg.png'
          },
          {
            title:'集福气，赢大奖',
            content:'试试手气？压岁钱都没这个大！',
            img:'/images/redbg.png'
          },
           {
            title:'这个新年“惠”不同！',
            content:'压轴盛宴，礼见新年，10万豪礼，让你拿到手软！',
            img:'/images/redbg.png'
          },
        ]
        var weibo=[
         {
            title:'集福气，赢大奖！超高概率分百万！',
            img:'/images/redbg.png'
          },
          {
            title:'有钱就是任性！抢到了的都说压岁钱都没这个大！',
            img:'/images/redbg.png'
          },
           {
            title:'乐享新年，豪礼钜献，超级红包，引爆春晚！',
            img:'/images/redbg.png'
           },
           {
            title:'压轴盛宴，礼见新年，10万豪礼，让你拿到手软！',
            img:'/images/redbg.png'
           }
        ]
        function forshare(){
               var a= Math.round(Math.random()*2);
               var b= Math.round(Math.random()*3);
               return $('#containerBox').data('title',wechat[a].title).data('content',wechat[a].content).data('img',wechat[a].img),
                      $('header').data('title',weibo[b].title).data('img',weibo[b].img);
        }
        forshare();
        function showShare() {
            var type='getFu',
                title = $('#containerBox').data('title'),
                img = labUser.path+'/images/redbg.png',
                header = '',
                content = $('#containerBox').data('content'),
                id='',
                url = window.location.href,
                wechat=$('#containerBox').data('content'),
                weibo=$('header').data('content'); 
            agentShare(title, url, img, header, content,type,id,weibo,wechat);   
        };
        $('.ui-share').on('click',function(){
               showShare() 
        })
   </script>
@stop