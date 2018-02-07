@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/v010000/tracklist.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/v010000/add.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/_v020800/bargainwait.css" rel="stylesheet" type="text/css"/>
@stop
<style>
	.none{
		display: none!important
	}
	.ui-pay-contract{
		padding: 1rem  3.5rem!important;
		color:#fff;
		background: #ff5a00;
		border-radius: 4px ;
		border: 0;
	}
</style>
@section('main')
    <section id="act_container" class="">
    <img id="storeage" class="none" src="{{URL::asset('/')}}/images/020700/contract.png">
    <!-- 公用蒙层 -->
    <div class="tips none"></div>
    <section class="ui_con" style="padding-bottom: 5rem">
    	<!--  <div class="ui_top_time">
                <div style="width:100%;height:2rem"></div>
                <center><div class="ui_show_time width10" >2017年1月</div></center>
         </div>
		 <div class="ui_common_contrack  bgcolor add_ui1">
		    	<div class="ui_contrack_middle fline ui_pR color999 padding00">
		    		<p style="text-align:left" class="margin07 f12 ">加盟合同<span class="fr color333">合同名称</span></p>
		    		<p style="text-align:left" class="margin07 f12 ">合同号<span class="fr color333">123456789</span></p>
		    		<p style="text-align:left" class="margin07 f12 ">加盟品牌<span class="fr color333">喜茶</span></p>
		    		<p style="text-align:left" class="margin07 f12 ">经纪人<span class="fr color333">无界商圈法务人员</span></p>
		    		<div style="width:100%;height:0.3rem"></div>
		    	</div>
		    	<div class="ui_contrack_middle  ui_pR color999">
		    		<p style="text-align:left" class="margin07 f12">加盟总费用<span class="fr color333">￥120000</span></p>
		    		<p style="text-align:left" class="margin07 f12">线上首付<span class="fr color333">￥120000</span></p>
		    		<p style="text-align:left" class="margin07 f12">线下尾款<span class="fr color333">￥120000</span></p>
		    		<p style="text-align:left" class="margin07 f12">缴纳方式<span class="fr color333">线上首付一次性结清</span></p>
		    		<p style="text-align:left" class="margin07  f12"><span class="fr color333">线下尾款银行转账</span></p>
		    		<div style="width:100%;height:1rem" class="clear"></div>
		    		<p style="text-align:left" class="margin07  f12 clear"><span class="fr ff">了解尾款补齐操作方法</span></p>
		    		<div style="width:100%;height:0.3rem" class="clear"></div>
		    	</div>
		    	<div class="ui_contrack_bottom fline ui_pR color999">
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
				    		<div style="width:100%;height:1rem;clear:both"></div>
				</div>
				<div style="width:100%;height:2.3rem;"></div>
		    	<div class="ui_pR color666">
			    	<ul class="accept_refuse">
	                  <li><a class="ui_border ui_refuse f13 color666">拒绝</a></li>
	                  <li><a class="f13 ui_accept clear"><center>签约加盟合同</center></a></li>
	                </ul>
	                <center><button class="ui-pay-contract f13 none">支付费用</button></center>
		    	</div>
		    </div> -->
    </section>
    <div class="tc none nocomment" id="nocommenttip3">
        <img src="{{URL::asset('/')}}/images/nomessage_icon1.png" style="height: 16rem;width: 16rem;margin:0 auto;display: inline-block;">
    </div>         
    </section>
@stop
@section('endjs')
    <script type="text/javascript" src="{{URL::asset('/')}}/js/_v020800/bargainwait.js"></script>
    <!-- 掉移动端弹框方法 -->
    <script>
         function checkMyorder(orderNo){
		    if (isAndroid) {
		      javascript: myObject.checkMyorder(orderNo);
		    }
		    else if (isiOS) {
		         var message = {
		                method:'checkMyorder',
		                params:{
		                	'order_no':orderNo
		                }
		            }; 
		      window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
		    }
		  };
		  $(document).on('click','.ui-pay-contract',function(){
		  	           var order_no=$(this).data('order_no');
		  			   checkMyorder(order_no);
		  })
  </script>
@stop