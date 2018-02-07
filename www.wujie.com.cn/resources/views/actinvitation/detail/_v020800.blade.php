@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/v010000/newsactask.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/_v020800/add.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section id="act_container" class="none">
    <!-- 公用蒙层 -->
        <div class="tips none"></div>
        <div class="ui_onmessage none" style="margin:10rem auto">
         <center><img src="{{URL::asset('/')}}/images/agent/turnoff_act.png" style="height: 16rem;width: 16rem;display: inline-block;"></center>
        </div>
        <div class="ui_container">
        <section class="ui_top_fixed f15 fff">
          投资人：哈哈哈哈已经接受了您的OVO活动邀请
        </section>
        <div class="ui_top relative">
          <p class="f15 b ">To无界商圈投资人<span id="customer_name" class="name f15 ffaf32">哈哈哈哈</span><span id="sex" class="sex none">男士</span>:</p>
          <p class="f13 color666">您的跟单经纪人<span id="agent_name" class="name f13 ffaf32">(哈哈)</span>邀请您参加本期的无界商圈</p>
          <p class="f13 color666">OVO活动发布会。</p>
          <p class="f13 color666">在这里我们诚邀您抽空前来参加OVO发布会。</p>
          <img class="ui_limit absolute ui_limit1" src="{{URL::asset('/')}}/images/020700/n1.png" alt="">
        </div>
        <ul class="ui_act_con">
          <li><img class="ui_limit2" src="{{URL::asset('/')}}/images/020700/m7.png" ></li>
          <li>
          <!-- 如果标题为一行那么 --><!-- margin11 -->
            <p class="toLeft f14 b color333 margin04 toTOP">新营销时代下的营我的就是这个东西的活动</p>
            <p class="toLeft bababa f11  margin04  ui-actinvitation-color">开始时间</p>
            <p class="toLeft bababa f11 margin04  ui-actinvitation-color">活动地点：杭州、上海、南京、杭州</p>
          </li>
          <li>
            <img class="ui_limit3" src="{{URL::asset('/')}}/images/rightjt.png" >
          </li>
        </ul>
        <div class="ui_bottom">
          <p class="f11 color999">*请点击活动并按照后续提示完成后续报名。</p>
          <p class="f11 color999">*确定活动参与场地，并妥善安排好活动时间。</p>
          <p class="f11 color999">*感谢你的配合和信赖，如有疑问，可联系您的经纪人<span id="name_agent" class="name f11">(哈哈哈哈)</span></p>
          <div style="width:100%;height:4.3rem"></div>
          <p class="f13 color333"><span id="name" class="name f13 color333 fr">邀请人</span></p>
          <div style="width:100%;height:1rem" class="clear"></div>
          <p class="f13 color333"><span id="name" class="name_agent name f13 color333 fr">跟单经纪人：<span class="b">阿叔</span></span></p>
          <div style="width:100%;height:1rem" class="clear"></div>
          <p class="f13 color999"><span id="name" class=" in_time name f13 color999 fr">邀请时间：2017/21/28 15:00:00</span></p>
          <div style="width:100%;height:1rem" class="clear"></div>
          <p class="f13 color999 none"><span id="name" class=" comform_time name f13 color999 fr">确定时间：2017/21/28 15:00:00</span></p>
          <div style="width:100%;height:1rem" class="clear"></div>
          <p class="f13 color999 "><span id="name" class="refusebg name f13 color999  none fr">拒绝理由：时间不够</span></p>
         <!--  <div style="width:100%;height:5rem" class="clear bg"></div> -->
        </div>
        <ul class="ui_fixed f16">
          <li class="fe556b ">拒绝</li>
          <li class="be74">接受活动邀请函</li>
        </ul>
        <ul class="ui-fixed f16 ui-border-t none">
          <li class="ff5a00">查看我的门票</li>
        </ul>
      </div>
    </section>
    <section style="position: fixed;bottom: 0;background: #FFFFFF;height:17px" class="iphone_btn none"></section>
@stop
@section('endjs')
<script type="text/javascript" src="{{URL::asset('/')}}/js/_v020800/ask.js"></script>
<script>
  $(document).ready(function(){
    $('title').text('活动邀请函详情')
  })
  
</script>
@stop