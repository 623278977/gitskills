@extends('layouts.default')
@section('css')
  <link href="{{URL::asset('/')}}/css/v010000/tracklist.css" rel="stylesheet" type="text/css"/>
  <link href="{{URL::asset('/')}}/css/v010000/add.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section id="act_container">
		<section class="ui_con" style="padding-bottom: 5rem">
			<!-- <div class="ui_top_time">
		                <div style="width:100%;height:2rem"></div>
		                <center><div class="ui_show_time" >2017年1月7日</div></center>
		    </div>
			<div class="ui_common_contrack  bgcolor add_ui1">
				    	<div class="ui_contrack_top  ui_pR fline">
				    		<span class="f13">X大拒绝加盟合同[喜茶]已经拒绝</span>
				    		<span class="be74 fr f13">支付成功</span>
				    	</div>
				    	<div class="ui_contrack_middle  ui_pR color666">
				    		<p style="text-align:left" class="margin07 f12">加盟合同<span class="fr">合同名称</span></p>
				    		<p style="text-align:left" class="margin07 f12">合同号<span class="fr">123456789</span></p>
				    		<p style="text-align:left" class="margin07 f12">加盟品牌<span class="fr">喜茶</span></p>
				    		<p style="text-align:left" class="margin07 f12">合同撰写<span class="fr">无界商圈法务人员</span></p>
				    		<p style="text-align:left" class=" f12"><span class="fr">喜茶法务人员</span></p>
				    		<div style="width:100%;height:1.5rem"></div>
				    	</div>
				    	<ul class="ui_border_flex ui_pR color666 f12">
			                <li style="width:40%"><div class="ui-border-b ui_row"></div></li>
			                <li style="width:20%"><span>首付情况</span></li>
			                <li style="width:40%"><div class="ui-border-b ui_row"></div></li>
		                </ul>
				    	<div style="width:100%;height:0.7rem;clear:both"></div>
				    	<div class=" clear ui_bg ui_pR color666">
				    		<p style="text-align:left" class="margin07 f12">首次支付<span class="fr">￥120000</span></p>
				    		<p style="text-align:left" class="margin07 f12">定金抵扣<span class="fr">-￥120000</span></p>
				    		<p style="text-align:left" class="margin07 f12">创业基金抵扣<span class="fr">-￥120000</span></p>
				    		<p style="text-align:left" class="margin07 f12">实际支付<span class="fr">-￥120000</span></p>
				    		<p style="text-align:left" class="margin07 f12">支付状态<span class="fr">已支付</span></p>
				    		<p style="text-align:left" class="margin07 f12">支付方式<span class="fr">支付宝</span></p>
				    		<p style="text-align:left" class="margin07 f12"><span class="fr">123456789@qq.com</span></p>
				    		<div style="width:100%;height:0.7rem;clear:both"></div>
				    		<p style="text-align:left" class="margin07 f12">支付时间<span class="fr">2015/12/12 18:000000</span></p>
				    	</div>
				    	<div style="width:100%;height:0.7rem;clear:both"></div>
				    	<ul class="ui_border_flex ui_pR color666 f12">
			                <li style="width:40%"><div class="ui-border-b ui_row"></div></li>
			                <li style="width:20%"><span>尾款情况</span></li>
			                <li style="width:40%"><div class="ui-border-b ui_row"></div></li>
		                </ul>
				    	<div class="ui_bg ui_pR color666">
				    		<p style="text-align:left" class="margin07 f12">尾款补齐<span class="fr">￥120000</span></p>
				    		<p style="text-align:left" class="margin07 f12">尾款状态<span class="fr fc6262">未支付</span></p>
				    		<p style="text-align:left" class="margin7 f12 clear"><span class="fr">*请投资人尽快支付尾款费用</span></p>
				    		<div style="width:100%;height:0.5rem" class="clear"></div>
				    		<p style="text-align:left" class="margin7 f12 clear"><span class="fr">支付方式为线下对公账号转账</span></p>
				    		<div style="width:100%;height:0.5rem" class="clear"></div>
				    		<p style="text-align:left" class="margin7 f12 clear"><span class="fr ff">了解尾款补齐操作方法</span></p>
				    	</div>
				    	<div class="ui_contrack_bottom fline ui_pR color666">
				    		<p style="text-align:left" class="margin07 f12">合同文本</p>
				    		<ul class="ui_contrack_detail ui_add_bg">
				    			<li>
				    				<img class="ui_img6"  src="{{URL::asset('/')}}/images/020700/bargain2.png">
				    			</li>
				    			<li>
				    				<p class="f14 b textleft color333 margin05">喜茶加盟电子合同</p>
				    				<p class="f11 textleft color333">合同编号：</p>
				    			</li>
				    			<li>
				    				<img class="ui_img7"  src="{{URL::asset('/')}}/images/rightjt.png">
				    			</li>
				    		</ul>
				    		<div style="width:100%;height:1.5rem"></div>
				    	</div>
				    	<div class="ui_pR color666">
				    		<div style="width:100%;height:1.5rem"></div>
				    		<p style="text-align:left" class="margin0 f12">确定时间<span class="fr">123456789</span></p>
				    	</div>
			</div> -->
		</section> 
      <div class="tc none nocomment" id="nocommenttip3">
	        <img src="{{URL::asset('/')}}/images/020700/nobargain.png" style="height: 16rem;width: 16rem;margin:15rem auto;display: inline-block;">
	  </div>            
    </section>
@stop
@section('endjs')
    <script type="text/javascript" src="{{URL::asset('/')}}/js/agent/bargaincomplete.js"></script>
@stop