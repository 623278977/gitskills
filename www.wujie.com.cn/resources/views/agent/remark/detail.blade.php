@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/v010000/remark.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section id="act_container" class="">
     <div class="common_pops none"></div>
      <div class="f12 ui_bg color666">跟单备注</div>
      <textarea placeholder="添加跟单备注"></textarea>
    <!--   <div class="f12 ui_bg color666">相关品牌</div>
      <div class=" ui_bg color999 f15 fff   brand">
          <span id="ui_brand">请选择相关品牌</span>
          <img class="ui_img5 fr"  src="{{URL::asset('/')}}/images/020700/r1.png">
      </div>
      <ul class="ui_brand_con f15 none a-fadeinB">
        <li class="ui-border-b">喜茶</li>
        <li class="ui-border-b">冰红茶</li>
        <li class="ui-border-b">哈啊哈</li>
        <li class="ui-border-b">哇哈哈</li>
      </ul> -->
    <!-- 相关品牌 -->
    <div class="brand_sel none">
      <div class="f12 ui_bg color666">相关品牌</div>
      <div class="ui_bg color999 f15 fff  brand_level">
          <span id="brand_level">请选择相关品牌</span>
          <img class="ui_img5 fr"  src="{{URL::asset('/')}}/images/020700/r1.png">
      </div>
      <ul class="brand_ul  f15 none a-fadeinB"></ul>
    </div>
      
    <!-- 客户等级 -->
      <div class="f12 ui_bg color666">客户等级</div>
      <div class=" ui_bg color999 f15 fff  level">
          <span id="ui_level">请选择客户等级</span>
          <img class="ui_img5 fr"  src="{{URL::asset('/')}}/images/020700/r1.png">
      </div>
       <ul class="ui_level_con f15 none a-fadeinB"></ul>
    </section>
@stop
@section('endjs')
    <script type="text/javascript" src="{{URL::asset('/')}}/js/agent/remark.js"></script>
@stop