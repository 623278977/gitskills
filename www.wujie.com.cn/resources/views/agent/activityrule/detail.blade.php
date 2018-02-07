@extends('layouts.default')
@section('css')
    <link rel="stylesheet" href="{{URL::asset('/')}}/css/animate.css">
    <link rel="stylesheet" href="{{URL::asset('/')}}/css/agent/_v010200/rule.css">
@stop
@section('main')
    <section id="act_container" class="box8">
                <article >
                    <div class="ui-img animated rotateIn">
                        <img src="/images/agent/rule.png"/>
                    </div> 
                    <p class="f12 color666 mT mT25 animated zoomInLeft"><span class="circle f11 b">1</span><span class="padding">活动期间(12月18日—1月8日)邀请投资人参与OVO品<span></p>
                    <p class="f12 color666 text-indent mT animated zoomInLeft">牌招商会，必须满足以下2个条件即可获得80元现金</p>
                    <p class="f12 color666 text-indent1 animated zoomInLeft">奖励(每邀请1人即可获得80元，无上限）；</p>
                    <p class="f12 color666 text-indent1 animated zoomInLeft">（1）必须报名参加活动；</p>
                    <p class="f12 color666 text-indent1 animated zoomInLeft">（2）活动当日参加活动并于活动现场签到；</p>
                    <p class="f12 color666 mT25 animated zoomInLeft"><span class="circle f11 b">2</span><span class="padding">邀请投资人进行实体考察,即可获得200元现金奖励；<span></p>
                    <p class="f12 color666 mT25 animated zoomInLeft"><span class="circle f11 b">3</span><span class="padding">活动邀请的投资人必须和经纪人是邀请关系；<span></p>
                    <p class="f12 color666 mT25  mT animated zoomInLeft"><span class="circle f11 b">4</span><span class="padding">活动期间集赞满30“上传截图”，客服审核通过即可获<span></p>
                    <p class="f12 color666 text-indent animated zoomInLeft">得红包；</p>
                    <p class="f12 color666 mT25 mT animated zoomInLeft"><span class="circle f11 b">5</span><span class="padding">活动最终解释权归杭州杭州天涯若比邻网络信息服务<span></p>
                    <p class="f12 color666 text-indent mT animated zoomInLeft">有限公司所有；</p>
                    <p class="f12 color666 text-indent mT0 animated zoomInLeft">有任何疑问或者帮助可以联系客服：400-001-0061。</p>
                    <div class="picture animated rotateIn">
                         <img src="/images/agent/1.png"/>
                         <img src="/images/agent/2.png"/>
                         <img src="/images/agent/3.png"/>
                    </div>
                </article>
    </section>
@stop
@section('endjs')
  <script type="text/javascript">
      $(document).ready(function(){
        $('title').text('活动规则')
      })
  </script>
@stop