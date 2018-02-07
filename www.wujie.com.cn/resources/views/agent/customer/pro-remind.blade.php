@extends('layouts.default')
@section('css')
	<link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/agent/customer-data.css"/>
@stop
<!--zhangxm-->
@section('main')
	<section id='container' class="medium">
		<!--<div class="top f12 color666">
			<span class="mb1 medium">按照保护期规则，您邀请的客户，将给与30天的保护期。</span><br />
			<span class="mb1 medium">在保护期内，您可以安排对该客户的跟进。</span>
		</div>
		<div class="remind-client fline">
			<p class="">
				<img src="" alt="" class="mr1 via" />
				<span class="f15 bold b color333 mt05">心灵魔咒</span>
				<img src="" alt="" class="grade " /><br />
				<span class="mt05 f12 medium">上海&nbsp;美国</span>
			</p>
			<p class="f11 color999">
				<span class="medium">还剩</span><span class="color999 f11 medium">2</span><span>天</span>
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
    <script src="{{URL::asset('/')}}/js/agent/pro-remind.js" ></script>
@stop