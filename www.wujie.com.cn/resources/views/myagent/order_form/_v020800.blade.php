@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/myagent/order_form.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section id="container" class="">
    	<!--<div class="bgwhite ">
    		<div class="head wrap">
	        	<div class="logo mb1 messa-l">
	        		<img src="/images/agent/logopact_10.png" class="mr1-33"/>
		        	<div class="">
		        		<span class="bold f14 mb1">喜茶</span><br />
		        		<span class="f12">行业分类：</span>
		        		<span class="cffac00 f12 mr2">鲜果饮品</span>
		        		<span class="f12">启动资金：</span>
		        		<span class="cff4d64 f12">3~5万元</span>
		        	</div>
	        	</div>
	        	<div class="black_to">
	        		<img src="/images/agent/black_to.png"/>
	        	</div>
        	</div>
        	<div class="fline"></div>
        	<div class="agent mt1 mb1-33 wrap">
        		<span class="mr1-33 f14 color333">成单经纪人</span>
        		<div class="messa head">
        			<div class="messa-l">
	        			<img src="/images/default.jpg" alt="" class="avatar mr05" />
	        			<div class="">
	        				<span class="mb08 bold f14">阿树</span><br />
	        				<img src="/images/agent/boy.png" class="gender"/>
	        				<span class="">浙江 杭州</span>
	        			</div>
        			</div>
        			<div class="relation"><img src="/images/agent/red-tel3.png" alt="" class="tel"/><img src="/images/agent/red-mes3.png" alt="" class="mes"/></div>
        		</div>
        	</div>
        	<p class="time wrap">
        		<span class="mr1-33 f14 color333">成单时间</span>
        		<span class="f14 color333">2222年3月3日</span>
        	</p>
        	<div class="btn head medium wrap">
        		<span class="btn-pac pt-b f14">查看电子合同</span>
        		<span class="btn-pay pt-b f14">查看付款详情</span>
        		<span class="btn-com pt-b f14">查看我的评价</span>
        		<span class="btn-com pt-c f14">查看我的评价</span>
        	</div>
    	</div>-->
        <!--<div class="">
        	<img src="/images/agent/brand_list.png" class="brand_list"/>
        </div>-->
    </section>
@stop
@section('endjs')
		<script src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
    	<script src="{{URL::asset('/')}}/js/_v020800/order_form.js"></script>
@stop