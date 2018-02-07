@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/_v020800/successbargain.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section id="act_container">
       <article class="ui_top none">成功加盟品牌，合约签署完成</article>
       <article class="ui_con   location none">
        <!--  <div class="ui_infor1 f14 color333">
           <p>加盟合同<span class="fr a869e">合同名称</span></p>
           <p>合同号<span class="fr a869e">合同名称</span></p>
           <p>加盟品牌<span class="fr a869e">合同名称</span></p>
           <p>合同撰写<span class="fr a869e">合同名称</span></p>
           <p><span class="fr a869e">合同名称</span></p>
         </div>
         <div style="width:100%;height:1rem"></div>
         <ul class="ui_circle">
           <li><div class="ui_left_circle"></div></li>
           <li>
              <ul class="ui_border_flex ui_pR a869e f12">
                      <li style="width:40%"><div class="ui-border-b ui_row"></div></li>
                      <li style="width:20%"><span>首付情况</span></li>
                      <li style="width:40%"><div class="ui-border-b ui_row"></div></li>
              </ul>
           </li>
           <li><div class="ui_right_circle fr"></div></li>
         </ul>
         <div class="ui_infor2 f14 color333 paddingb01">
           <p>首次支付<span class="fr a869e">￥12000</span></p>
           <p>定金抵扣<span class="fr a869e">2017/07/12</span></p>
           <p>创业基金抵扣<span class="fr a869e">没兴趣谢谢</span></p>
           <p>实际支付<span class="fr a869e">2017/07/12</span></p>
           <p>支付状态<span class="fr a869e">没兴趣谢谢</span></p>
           <p>支付方式<span class="fr a869e">2017/07/12</span></p>
           <p><span class="fr a869e">2017/07/12</span></p>
           <div style="width:100%;height:1rem;clear:both"></div>
           <p>支付时间<span class="fr a869e">2017/07/12</span></p>
         </div>
         <ul class="ui_border_flex ui_pR color666 f12 padding01">
                      <li style="width:40%"><div class="ui-border-b ui_row"></div></li>
                      <li style="width:20%"><span>尾款情况</span></li>
                      <li style="width:40%"><div class="ui-border-b ui_row"></div></li>
         </ul>
         <div class="ui_infor2 f14 color333 paddingb01">
           <p>尾款补齐<span class="fr a869e">￥12000</span></p>
           <p>支付状态<span class="fr ff4d64">未支付</span></p>
         </div>
         <div class="ui_infor3 f12 a869e ">
           <p><span class="fr a869e">*请于8/30日前支付相关款项</span></p>
           <div style="height:0.5rem;width:100%;clear:both"></div>
           <p><span class="fr">如有延误等情况，请尽早联系经纪人</span></p>
           <div style="height:0.5rem;width:100%;clear:both"></div>
           <p><span class="fr">如有延误等情况，请尽早联系经纪人</span></p>
           <div style="height:0.5rem;width:100%;clear:both"></div>
           <p><span class="fr f14 ff2873">了解尾款补齐操作办法</span></p>
         </div>
         <div class="ui_infor2 f14 color333 paddingb01">
           <p>对公账号<span class="fr a869e">123456789000000</span></p>
           <p>所属银行<span class="fr a869e">中国工商银行</span></p>
           <p>单位名称<span class="fr a869e">中国工商银行</span></p>
           <p><span class="fr f12 a869e">*对公账号转账前，请先联系经纪人确认线下对公账户</span></p>
           <div style="height:0.5rem;width:100%;clear:both"></div>
           <p><span class="fr f12 a869e">*转账后，3~4天确认账户到账，届时会有专人通知您</span></p>
         </div>
         <div class="ui_contrack_bottom ui_pR color333 padding00">
                <p style="text-align:left" class="margin07 f12">合同文本</p>
                <ul class="ui_contrack_detail ui_add_bg ">
                  <li>
                    <img class="ui_img6"  src="{{URL::asset('/')}}/images/020700/bargain2.png">
                  </li>
                  <li>
                    <p class="f14 b textleft color333 margin05">喜茶加盟电子合同</p>
                    <p class="f11 textleft color333">合同编号：</p>
                  </li>
                  <li>
                    <img class="ui_img7"  src="{{URL::asset('/')}}/images/020700/y.png">
                  </li>
                </ul>
          </div>             -->
       </article>
       <div id="ui_bottom" style="height:87rem;width:100%" class="none"></div>
    </section>
@stop
@section('endjs')
<script type="text/javascript" src="{{URL::asset('/')}}/js/_v020800/require.js" defer async="true" data-main="{{URL::asset('/')}}/js/_v020800/success"></script>
@stop