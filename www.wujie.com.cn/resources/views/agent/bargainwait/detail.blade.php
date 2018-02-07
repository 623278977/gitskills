@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/v010000/tracklist.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/v010000/add.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section id="act_container" class="">
    <img id="storeage" class="none" src="{{URL::asset('/')}}/images/020700/contract.png">
    <!-- 公用蒙层 -->
    <div class="tips none"></div>
    <section class="ui_con" style="padding-bottom: 5rem">
    	<!--  <div class="ui_top_time">
                <div style="width:100%;height:2rem"></div>
                <center><div class="ui_show_time" >2017年1月7日</div></center>
         </div>
		    <div class="ui_common_contrack  bgcolor add_ui1">
		    	<div class="f13 ui_contrack_top fline ui_pR">
		    		X大拒绝加盟合同[喜茶]已经拒绝
		    		<span class="ffa300 fr">等待中</span>
		    	</div>
		    	<div class="ui_contrack_middle fline ui_pR color666">
		    		<p style="text-align:left" class="margin07 f12">加盟合同<span class="fr">合同名称</span></p>
		    		<p style="text-align:left" class="margin07 f12">合同号<span class="fr">123456789</span></p>
		    		<p style="text-align:left" class="margin07 f12">加盟品牌<span class="fr">喜茶</span></p>
		    		<p style="text-align:left" class="margin07 f12">合同撰写<span class="fr">无界商圈法务人员</span></p>
		    		<p style="text-align:left" class=" f12"><span class="fr">喜茶法务人员</span></p>
		    		<div style="width:100%;height:1.5rem"></div>
		    	</div>
		    	<div class="ui_contrack_middle fline ui_pR color666">
		    		<p style="text-align:left" class="margin07 f12">加盟总费用<span class="fr">￥120000</span></p>
		    		<p style="text-align:left" class="margin07 f12">线上首付<span class="fr">￥120000</span></p>
		    		<p style="text-align:left" class="margin07 f12">线下尾款<span class="fr">￥120000</span></p>
		    		<p style="text-align:left" class="margin07 f12">缴纳方式<span class="fr">线上首付一次性结清</span></p>
		    		<p style="text-align:left" class="margin07  f12"><span class="fr">线下尾款银行转账</span></p>
		    		<div style="width:100%;height:1rem" class="clear"></div>
		    		<p style="text-align:left" class="margin07  f12 clear"><span class="fr ff">了解尾款补齐操作方法</span></p>
		    		<div style="width:100%;height:0.3rem" class="clear"></div>
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
		    		<div style="width:100%;height:1.8rem"></div>
		    		<p style="text-align:left" class="margin07 f12">邀请状态：
		    			<span><span class="ffa300">待确认</span><span class="b color333 padding">还剩3天5小时6分</span></span> 
		    			<span class="fr ui_send">再次发送</span>
		    		</p>
		    	</div>
		    </div> -->
    </section>
    <div class="tc none nocomment" id="nocommenttip3">
	        <img src="{{URL::asset('/')}}/images/020700/nobargain.png" style="height: 16rem;width: 16rem;margin:15rem auto;display: inline-block;">
	</div>           
    </section>
@stop
@section('endjs')
    <script type="text/javascript" src="{{URL::asset('/')}}/js/agent/bargainwait.js"></script>
@stop