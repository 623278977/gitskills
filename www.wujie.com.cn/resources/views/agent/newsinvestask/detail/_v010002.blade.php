@extends('layouts.default')
@section('css')
   <link href="{{URL::asset('/')}}/css/agent/_v010002/newsactask.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section id="act_container" class="none">
    <!-- 公用蒙层 -->
        <div class="common_pops none"></div>
        <div class="ui_onmessage none" style="margin:10rem auto">
          <center><img src="{{URL::asset('/')}}/images/agent/turnoff_invest.png" style="height: 16rem;width: 16rem;display: inline-block;"></center>
        </div>
        <div class="ui_container">
            <section class="ui_top_fixed f15 fff">
              投资人：哈哈哈哈接受了您的OVO品牌考察邀请
            </section>
            <div class="ui_top relative addheight addwidth" style="height:23.9rem">
              <p class="f15 b ">To&nbsp无界商圈投资人<span id="customer_name" class="name f15 ffaf32">哈哈哈哈</span><span id="sex" class="sex ">男士</span>:</p>
              <p class="f13 color666">您的跟单经纪人<span id="agent_name" class="name f13 ffaf32">(哈哈)</span>邀请您对<span id="brand">喜茶</span>
                <span id="ui_need">进行</span></p>
              <p class="f13 color666 showit">实地考察。</p>
              <p class="f13 color666">经过与您的事先沟通，本次考察将安排在<span class="ui_onetime">9/18</span>进行，</p>
              <p class="f13 color666">请妥善安排好您的时间，并提前与经纪人保持联系。</p>
              <img class="ui_limit absolute ui_limit1" src="{{URL::asset('/')}}/images/020700/n4.png" alt="">
            </div>
            <div class="ui_middle_brand">
               <ul class="ui_brand_infor">
                 <li class=" f12 color333">考察品牌</li>
                 <li><img class="ui_brand_size" src="{{URL::asset('/')}}/images/020700/m7.png" alt=""></li>
                 <li class="b f12 color666">酷品哈哈</li>
               </ul>
               <div style="width:100%;height:0.5rem;clear:both"></div>
               <p class="f12 color333 margin05">考察场地<span id="name" class="storename f12 color666 fr">考察门店：杭州喜茶文化创意园</span></p>
               <p class="f12 color333"><span id="name" class="belongname f12 color999 fr">所在地区：杭州市人民政府</span></p>
               <div style="width:100%;height:0.5rem;clear:both"></div>
               <p class="f12 color333"><span id="name" class="detailname f12 color999 fr">地址：杭州市人民政府</span></p>
               <div style="width:100%;height:0.5rem;clear:both"></div>
               <p class="f12 color333">考察时间<span id="name" class="time_name f12 color666 fr">2017/12/25</span></p>
               <p class="f12 color333">订金金额<span id="name" class="curreny_name f12 color666 fr">￥3000</span></p>
            </div>
            <div class="ui_bottom" style="padding-bottom: 5rem">
              <p class="f11 color999">*需要支付相应的订金，请确保及时付款。</p>
              <p class="f11 color999">*订金可退款，并可于最后的加盟支付中使用。</p>
              <p class="f11 zone" style="color:#2873ff">*点击接受默认同意并遵循《项目投资意向书》相关条款。</p>
              <p class="f11 color999">*感谢你的配合和信赖，如有疑问，可联系您的经纪人<span id="" class="hahaname f11 ">哈哈哈哈</span></p>
              <div style="width:100%;height:4.3rem"></div>
              <p class="f13 color333"><span  class="name f13 color333 fr">邀请人</span></p>
              <div style="width:100%;height:1rem" class="clear"></div>
              <p class="f13 color333"><span  class="name_agent ggname f13 color333 fr">跟单经纪人：阿叔</span></p>
              <div style="width:100%;height:1rem" class="clear"></div>
              <p class="f13 color999"><span  class="in_time name f13 color999 fr">邀请时间：2017/21/28 15:00:00</span></p>
              <div style="width:100%;height:1rem" class="clear"></div>
              <p class="f13 color999 none"><span  class=" comform_time name f13 color999 fr">确定时间：2017/21/28 15:00:00</span></p>
              <div style="width:100%;height:1rem" class="clear"></div>
              <p class="f13 color999 "><span id="name" class=" refusebg name f13 color999  none fr">拒绝理由：时间不够</span></p>
             <!--  <div style="width:100%;height:5rem" class="clear bg"></div> -->
            </div>
            <ul class="ui_fixed ">
              <li>状态：待确认</li>
              <li>再次发送</li>
              <li>关闭</li>
            </ul>
             <div class="ui_fixeded none">
              <a>联系品牌商务代表</a>
            </div>
        </div>
    </section>
    <section style="position: fixed;bottom: 0;background: #FFFFFF;height:17px" class="iphone_btn none"></section>
@stop
@section('endjs')
<script type="text/javascript" src="{{URL::asset('/')}}/js/agent/_v010002/invest.js"></script>
<script type="text/javascript">
  $(document).ready(function(){
    $('title').text('考察邀请函详情')  
        })  
  iphonexBotton('.ui_fixeded')
</script>    
@stop