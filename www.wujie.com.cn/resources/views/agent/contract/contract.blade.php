@extends('layouts.default')
<!--zhangxm-->
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/contract.css" rel="stylesheet" type="text/css"/>
@stop
<!--zhangxm-->
@section('main')
	<section id='container'>
		<!--<div class="amount">
			<div class="top">
				<p><span class="brand f11">品牌：</span>
				<span class="brand-num f11">播音123</span></p>
				<p>
					<span class="jion f11">加盟金额&nbsp;</span>
					<span class="money corfd4d4d f11">¥ 123K</span>
					</p>
			</div>
			<div class="pact">
				<div class="pact-survey">
					<span class="pact-choose mr2-5" pact-id=""></span>
					<img src="{{URL::asset('/')}}/images/agent/my_contract@2x.png" class="pact-img"/>&nbsp;&nbsp;
					<span class="pact-name b bold f12">一份8亿的合同</span>
				</div>
			
				<div class="pact-details ">
					<p class=" flex"><span class="f12">加盟套餐</span><span class="f12">¥ 60K</span></p>
					<p class=" flex"><span class="f12">缴纳方式</span><span class="f12">一次性结清</span></p>
					<p class=" flex"><span class="f12">创建时间</span><span class="f12">2017-11-11&nbsp;17:11:11</span></p>
					<p class="mb1-5 flex fline f12"><span class="f12">合同撰写</span><span
						class="mb1-5 f12">无界商圈法务人员<br /><br />喜茶品牌法务人员</span>
					</p>
					
					<p class=" flex"><span class="f12">加盟总费用</span><span class="f12">¥ 60.221</span></p>
					<p class=" flex"><span class="f12">线上首付</span><span class="f12">¥ 120.000</span></p>
					<p class=" flex"><span class="f12">线下尾款</span><span class="f12">¥ 60.001</span></p>
					<div class="mb1-5 flex f12 text-end">
						<span class="f12">合同撰写</span>
						<p class="f12 medium">
      						<span class=" mb05 color666">线上首付一次结清</span><br />
      						<span class=" mb05 color666">线下尾款银行转账</span><br />
      						<span class="c2873ff mb05">了解尾款补齐操作办法</span><br />
						</p>
					</div>
				</div>
				<p class="pact-flexible remarks"><img src="{{URL::asset('/')}}/images/agent/zhankai.png" class="flexible-img"/>
				</p>
		</div>-->
		<!--<div class="top f22">
			<p><span class="brand">品牌：</span>
			<span class="brand-num">播音123</span></p>
			<p><span class="jion">加盟金额&nbsp;</span>
			<span class="money corfd4d4d f22">¥ 123&nbsp;K</span></p>
		</div>
		<div class="pact f24">
			<div class="pact-survey">
				<span class="pact-choose mr2-5"></span>
				<img src="{{URL::asset('/')}}/images/agent/my_contract@2x.png" class="pact-img"/>&nbsp;&nbsp;
				<span class="pact-name b bold">一份8亿的合同</span>
			</div>
			<div class="pact-details none">
				<p class=" flex"><span>加盟套餐</span><span>¥ 60 K</span></p>
				<p class=" flex"><span>缴纳方式</span><span>一次性结清</span></p>
				<p class=" flex"><span>创建时间</span><span>2017-11-11&nbsp;17:11:11</span></p>
				<p class="mb3 flex fline"><span>合同撰写</span><span
					class="mb3">无界商圈法务人员<br /><br />喜茶品牌法务人员</span>
				</p>
				<p class="flex-center remarks f24 periods">
					<span class="mb3">第一期<br /><br />¥ 60 K</span>
					<span>第一期<br /><br />¥ 60 K</span>
					<span>第一期<br /><br />¥ 60 K</span>
					<span>第一期<br /><br />¥ 60 K</span>
					<span>第一期<br /><br />¥ 60 K</span>
					<span>第一期<br /><br />¥ 60 K</span>
					<span>第一期<br /><br />¥ 60 K</span>
				</p>
				<p class="remarks f22 color999"><span>每月10日前付款，请及时与投资人协商</span><br /><span>300000元为分期利息</span></p>
			</div>
			<p class="pact-flexible remarks">
				<img src="{{URL::asset('/')}}/images/agent/zhankai.png" class="flexible-img"/>
			</p>
		</div>-->
		
		
		<!--<button class="btn f30">确定</button>-->
	</section>
    <section class="enjoy none" style='padding-bottom:16rem;background-color: #f2f2f2;'>
    </section>
    <section style="border-top:1px solid transparent"></section>
@stop
@section('endjs')
    <script src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
    <script src="{{URL::asset('/')}}/js/agent/contract.js"></script>
	
@stop