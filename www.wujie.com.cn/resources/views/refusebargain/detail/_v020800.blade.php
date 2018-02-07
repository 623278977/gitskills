@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/_v020800/refusebargain.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section id="act_container">
       <article class="ui_top none">已成功拒绝本次加盟合同邀请</article>
       <article class="ui_con   location">
        <!--  <div class="ui_infor1 f14 color333">
           <p>加盟合同<span class="fr a869e">合同名称</span></p>
           <p>合同号<span class="fr a869e">合同名称</span></p>
           <p>加盟品牌<span class="fr a869e">合同名称</span></p>
           <p>合同撰写<span class="fr a869e">合同名称</span></p>
           <p><span class="fr a869e">合同名称</span></p>
         </div>
         <div style="width:100%;height:1.9rem"></div>
         <ul class="ui_circle">
           <li><div class="ui_left_circle"></div></li>
           <li><div class="ui_dotted"></div></li>
           <li><div class="ui_right_circle fr"></div></li>
         </ul>
         <div class="ui_infor1 f14 color333 padding">
           <ul class="ui_refus">
             <li>拒绝理由</li>
             <li><p></p></li>
           </ul>
           <div style="width:100%;height:1rem;clear:both"></div>
           <p style="padding-left: 0.5rem">确认时间<span class="fr a869e">2017/07/12</span></p>
         </div> -->
       </article>
    </section>
@stop
@section('endjs')
<script src="{{URL::asset('/')}}/js/_v020800/require.js" defer async="true" data-main="{{URL::asset('/')}}/js/_v020800/main"></script>
<script>
  $(document).ready(function(){
    $('title').text('拒绝加盟品牌')
  })
</script>
@stop