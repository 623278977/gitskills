<!--zhangxm-->
@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/_v010002/recommend.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section class="containerBox pl1-5 mt1" id="containerBox" >
      <!--<div class="act ">
      	<div class="top ">
      		<img src="/images/agent/time_03.png" class="mr1 "/><span class="time c2873ff f13">09/09</span>
      	</div>
      	<div class="bord-l ml08">
      		<div class="act-cont bgwhite mb1-2">
      			<div class="act-1 f13">
      				<div class="re_l down-pay">
      					<img src="/images/awardpic.png" class="avatar mr1"/>
	      				<div class="">
	      					<span class="color333 f13">阿树</span>
	      					<p class="mt1-5">
	      						<img src="/images/agent/boy.png" class="gender"/>
	      						<span class="color999 f12">浙江 杭州</span>
	      					</p>
	      					
	      				</div>
      				</div>
      				<div class="re_r order_receive">
      					<button class="re_btn re_receive f12 mb1">接单</button><br />
      					<button class="re_btn re_noreceive scale-1">不感兴趣</button>
      				</div>
      				<div class="yet_order none">
      					<span class="f12 color999">已被他人接单</span>
      				</div>
      				<div class="yet_order re_r chit_chat none">
      					<span class="f12 color999 order_already">已接单</span><br />
      					<button class="re_btn re_receive f12 mb1 chat">聊天</button>
      				</div>
      			</div>
      			<div class="act-2">
      				<p class="">
      					<span class="f12 color999">意向品牌:</span>
      					<span class="f12 color999">秦国、一点点</span>
      				</p>
      				<p class="">
      					<span class="f12 color999">活动参与:</span>
      					<span class="f12 color999">参加过无界商圈OVO活动</span>
      				</p>
      				<p class="">
      					<span class="f12 color999">平台活跃度:</span>
      					<span class="f12 color999">最近7天有活跃时间</span>
      				</p>
      			</div>
      		</div>
      	</div>
      </div>-->
      <!--<div class="act ">
      	<div class="top ">
      		<img src="/images/agent/time_03.png" class="mr1 "/><span class="time c2873ff f13">09/09</span>
      	</div>
      	<div class="bord-l ml08">
      		<div class="act-cont bgwhite mb1-2">
      			<div class="define_order none"></div>
      			<div class="act-1 f13">
      				<div class="re_l down-pay">
      					<img src="/images/awardpic.png" class="avatar mr1"/>
	      				<div class="">
	      					<span class="color333 f13">阿树</span>
	      					<p class="mt1-5">
	      						<img src="/images/agent/boy.png" class="gender"/>
	      						<span class="color999 f12">浙江 杭州</span>
	      					</p>
	      					
	      				</div>
      				</div>
      				<div class="yet_order none">
      					<span class="f12 color999">已被他人接单</span>
      				</div>
      				<div class="yet_order re_r chit_chat">
      					<span class="f12 color999 order_already">已接单</span>
      					<button class="re_btn re_receive f12 mb1 chat">聊天</button>
      				</div>
      			</div>
      			<div class="act-2">
      				<p class="">
      					<span class="f12 color999">意向品牌:</span>
      					<span class="f12 color999">秦国、一点点</span>
      				</p>
      				<p class="">
      					<span class="f12 color999">活动参与:</span>
      					<span class="f12 color999">参加过无界商圈OVO活动</span>
      				</p>
      				<p class="">
      					<span class="f12 color999">平台活跃度:</span>
      					<span class="f12 color999">最近7天有活跃时间</span>
      				</p>
      			</div>
      		</div>
      	</div>
      </div>-->
      
    </section>
    <section>
    	<div class="common_pops none"></div>
    	<div class="default none">
      		<img src="/images/agent/no_kehu.png"/>
        </div>
    </section>
@stop
@section('endjs')
    <script src="{{URL::asset('/')}}/js/agent/_v010002/recommend.js" ></script>
    <script type="text/javascript">
    	$(document).ready(function(){
    		$('title').text('推荐投资人');
    	})
    </script>	
@stop