@extends('layouts.default')
<!--zhangxm-->
@section('css')
	<link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/agent/customer-data.css"/>
@stop
<!--zhangxm-->
@section('main')
	<section id='container' class="medium">
		<!--<div class="top f12 color666">
			<span class="mb1">考察品牌：</span><span class="mb1">万达广场</span><br />
			<span class="mb1">考察门店：</span><span class="mb1">LOL线下冠军赛</span><br />
			<span class="">考察时间：</span><span class="">8月3日（今天）</span>
		</div>
		<div class="remind-client fline">
			<p class="">
				<img src="" alt="" class="mr1 via" />
				<span class="f15 bold b color333 mt05">心灵魔咒</span>
				<img src="/images/agent/boy.png" alt="" class="grade " /><br />
				<span class="mt05 f12">上海&nbsp;美国</span>
			</p>
			<p class="">
				<span class=""><a href="tel:1234567890">电话提醒&nbsp;</a></span>
				<span class=""><a href="sms:1234567890">短信提醒</a></span>
			</p>
		</div>-->
	</section>
    <section class="enjoy " style='padding-bottom:4rem;background-color: #f2f2f2;'>
    	<div class="common_pops none"></div>
    	<div class="define none" style="width: 100%;margin: auto;height: 100%;text-align: center;">
			<img src="/images/agent/no_proremind.png" style="width: 50%;text-align: center;margin-top: 30%;"/>
		</div>
    </section>
    <section style="border-top:1px solid transparent"></section>
@stop
@section('endjs')
    <script src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
    <script src="{{URL::asset('/')}}/js/agent/ins-remind.js" ></script>
@stop