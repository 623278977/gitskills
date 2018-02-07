@extends('layouts.default')
@section('css')
    <link href="/css/agent/_v010300/index.css" rel="stylesheet" type="text/css"/>
    <link href="/css/animate.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{URL::asset('/')}}/css/agent/_v010300/swiper.min.css"> 
@stop
<style>
  .swiper-container{
    width:100%;
    height:2rem;
    text-align:center;
  }
  .swiper-slide{
     width:100%;
    height:2rem;
    text-align:center;
  }
</style>
@section('main')
    <section id="container" class="container" >
              <div class="tips none"></div>
              <div class="mt20"></div>
               <div class="swiper-container animated bounceInRight">
                <div class="swiper-wrapper">
                </div>
                <!-- Add Pagination -->
                <div class="swiper-pagination none"></div>
            </div>
              <div class="dati f18 b center startdati animated zoomIn">开始答题</div>
              <article class="color333 f11 animated zoomInLeft">
                  <p class="color000 f18 b center">规则说明</p>
                  <p>
                        <span class="circle fl f13 b center">1</span>
                        <span class="pl1">点击 “ 开始答题” 按钮，将随机跳出问答题，包括平台知识</span>
                  </p>
                  <p class="indent mb05">题目、品牌问答。</p>
                  <p class="mt1">
                        <span class="circle fl f13 b center">2</span>
                        <span class="pl1">回答正确，可获得通用红包或品牌红包（通用红包，加盟任</span>
                  </p>
                  <p class="indent mb05">一品牌都可抵扣；品牌红包，加盟对应品牌时方可抵扣）；</p>
                  <p class="mt1">
                        <span class="circle fl f13 b center">3</span>
                        <span class="pl1">红包均可转赠,为了更好的吸引投资人加盟品牌,可将获得的</span>
                  </p>
                  <p class="indent mb05">红包赠送给投资人,加盟时用于抵扣,不可叠加使用；</p>
                  <p class="mt1">
                        <span class="circle fl f13 b center">4</span>
                        <span class="pl1">活动最终解释权归杭州天涯若比邻网络信息服务有限公司所</span>
                  </p>
                  <p class="indent mb05">有，有任何疑问或者帮助可以联系客服：400-011-0061。</p>
              </article>
    </section>
@stop
@section('endjs')
<script src="{{URL::asset('/')}}/js/swiper.min.js"></script>
<script src="/js/agent/_v010300/index.js"></script>
<script>
 
    $(document).ready(function(){
        $('title').text('赚钱攻略');
    })
   
</script>
@stop