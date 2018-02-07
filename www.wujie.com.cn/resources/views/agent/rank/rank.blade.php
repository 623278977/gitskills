@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/_v020700/actdetail.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/v010000/rank.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section id="act_container" >
        <!--安装app-->
        <div class="app_install fixed none" id="installapp">
            <i class="l">打开无界商圈APP，体验更多精彩内容 >></i>
            <span class="r" id="openapp"><img src="{{URL::asset('/')}}/images/yaoqing.png" alt=""></span>
            <div class="clearfix"></div>
        </div>
        <!-- 上部分展示等级图框 -->
        <header class="ui_rank_content">
           <div class="ui_rank_container silver">
               <!-- 头像 -->
               <div class="ui_image">
                  <center><img src="{{URL::asset('/')}}/images/default/avator-m.png" alt=""/></center>
               </div>
               <div style="width:100%;height:0.8rem"></div>
              <!-- 等级文字 -->
               <div class="ui_text1 f12">当前等级</div>
               <div style="width:100%;height:0.3rem"></div>
               <div id="ui_text_rank" class="ui_text_rank f18"></div>
               <div style="width:100%;height:1.3rem"></div>
              <!--进度条 -->
               <div class="ui_progress_container">
                   <ul class="ui_progress_bar">
                      <li><span></span><span></span><span></span><span></span></li>
                      <li><span></span><span></span><span></span><span></span></li>   
                   </ul>
                <!-- 小黑点 -->
                   <ul class="ui_dot">
                      <li style="text-align:left;"><span class="ui_dot_pict fl"></span></li>
                      <li style="text-align:left;padding-left: 15%"><span class="ui_dot_"></span></li>
                      <li style="text-align:right"><span class="ui_dot_ fr"></span></li>   
                   </ul> 
                   <div style="width:100%;height:1rem"></div>
                  <!--  文字 -->
                   <ul class="ui_rank_text">
                       <li>
                           <div class="ui_bold_text">铜牌经纪人</div>
                           <div class="ui_thin_text">个人接单量0+</div>
                       </li>
                       <li>
                           <div class="ui_bold_text">银牌经纪人</div>
                           <div class="ui_thin_text">个人接单量20+</div>
                       </li>
                       <li>
                           <div class="ui_bold_text">金牌经纪人</div>
                           <div class="ui_thin_text">个人接单量50+</div>
                       </li>
                   </ul> 
                   <div style="width:100%;height:2rem;clear:both"></div>
                   <!-- 下面数字 -->
                   <div style="color:#999;font-size:1.1rem">个人累计完成单量数据，每天早上8点完成更新</div>
               </div>
           </div>
        </header>
        <!-- 中部展示等级权益 -->
        <div class="ui_rank_rights">
          <ul class="ui_border_flex">
              <li style="width:40%"><div class="ui-border-b ui_row"></div></li>
              <li style="width:20%"><b id="agent_title">银牌经纪人</b></li>
              <li style="width:40%"><div class="ui-border-b ui_row"></div></li>
          </ul>
          <div style="width:100%;height:1rem;clear:both"></div>
          <!-- 等级权益文本内容 -->
          <article class="ui_rights_text">
              <!-- <p>派单优先级优于普通经纪人</p> -->
             <!--  <p></p> -->
              <!-- <p><span class="ui_text_row">—</span>派单优先级优于普通经纪人</p> -->
          </article>   
        </div>
         <!-- 展示统计资料 -->
        <section class="ui_rank_datashow">
              <div class="ui_rank_datashow_text ui-border-b"><span id="current">距离下一个等级：</span><span id="agent_rank">金牌经纪人</span></div>
              <!-- 展示统计图 -->
              <div class="ui_chart">
                 <div style="width:100%;height:1rem;clear:both;"></div>
                 <span class="ui_need_data">还需完成单</span>
                 <!-- <center> <div id="chart"></div></center> -->
                 <div style="height:14rem; width:14rem;margin:0 auto;transform: translateX(-13px);"><canvas id="mychart"></canvas></div>
                 <div style="width:100%;height:1rem;clear:both;"></div>
                 <p class="ui_rank_datadetail">个人累计接单量：<b><span id="data_detail">27单</span></b></p> 
              </div>     
        </section> 
        <!-- 跳转箭头 -->
        <div style="width:100%;height:1rem;clear:both;"></div>
        <div class="ui_href">
            了解等级权益
            <span class="sj_icon ui_pict"></span>
        </div>
    </section>
@stop
@section('endjs')
   <!--  <script type="text/javascript" src="{{URL::asset('/')}}/js/agent/jquery-3.0.0.min.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/agent/chart.js"></script> -->
    <script type="text/javascript" src="{{URL::asset('/')}}/js/agent/dist/Chart.bundle.min.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/agent/progress.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/agent/rank3.js"></script>
@stop