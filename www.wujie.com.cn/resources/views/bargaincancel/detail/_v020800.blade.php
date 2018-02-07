@extends('layouts.default')
@section('css')
   <!--  <link href="{{URL::asset('/')}}/css/v010000/bargain.css" rel="stylesheet" type="text/css"/> -->
    <link href="{{URL::asset('/')}}/css/v010000/tracklist.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/v010000/add.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/_v020800/bargaincancel.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section id="act_container">
	  <section class="ui_con" style="padding-bottom: 5rem">
		   <!--  <div class="ui_top_time">
	                <div style="width:100%;height:2rem"></div>
	                <center><div class="ui_show_time width20" >2017年1月</div></center>
	        </div>
		    <div class="ui_common_contrack  bgcolor add_ui1 ">
		    	<div class="ui_contrack_middle fline ui_pR color999 padding00">
		    		<p style="text-align:left" class="margin07 f12">加盟合同<span class="fr color333">合同名称</span></p>
		    		<p style="text-align:left" class="margin07 f12">合同号<span class="fr color333">123456789</span></p>
		    		<p style="text-align:left" class="margin07 f12">加盟品牌<span class="fr color333">喜茶</span></p>
		    		<p style="text-align:left" class="margin07 f12">经纪人<span class="fr color333">姚凯</span></p>
		    		<p style="text-align:left" class="margin07 f12">合同撰写<span class="fr color333">无界商圈法务人员</span></p>
		    		<p style="text-align:left" class=" f12"><span class="fr color333">喜茶法务人员</span></p>
		    		<div style="width:100%;height:2.5rem"></div>
		    	</div>
		    	<div class="ui_contrack_bottom fline ui_pR color999">
		    		<p style="text-align:left" class="margin07 f12">加盟费用<span class="fr color333">￥120000</span></p>
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
		    				<img class="ui_img7"  src="{{URL::asset('/')}}/images/020700/y.png">
		    			</li>
		    		</ul>
		    		<div style="width:100%;height:1.5rem"></div>
		    	</div>
		    	<div class="ui_pR color999">
		    		<div style="width:100%;height:1.5rem"></div>
		    		 <ul class="ui_refus f12">
			             <li>拒绝理由</li>
			             <li>
			             	<p style="margin-right: 0" class="color333">
			                </p>
			             </li>
			         </ul>
		    		<p style="text-align:left;" class="margin0 f12">确定时间<span class="fr color333">123456789</span></p>
		    	</div>
		    </div> -->
	 </section>
	    <div class="tc none nocomment" id="nocommenttip3">
	        <img src="{{URL::asset('/')}}/images/nomessage_icon1.png" style="height: 16rem;width: 16rem;margin:0 auto;display: inline-block;">
	    </div>         
    </section>
@stop
@section('endjs')
    <script type="text/javascript" src="{{URL::asset('/')}}/js/_v020800/bargaincancel.js"></script>
@stop