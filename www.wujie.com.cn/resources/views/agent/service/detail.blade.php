@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/service.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section id="act_container" class="">
     	<div class="service">
     		<img src="/images/agent/service.png" class="service_img"/><br />
     		<span class="f15 color333 bold">客服电话</span>&nbsp;
     		<a href="tel:400-011-0061"><span class="f15 c2873ff">400-011-0061</span></a><br />
     		<span class="f13 color999 medium pt1-5">周一至周五 &nbsp;09:00 - 20:00</span><br />
     		<span class="f13 color999 medium pt1">周末及法定节假日&nbsp; 09:00 - 17:00</span>
     	</div>
    </section>
@stop
@section('endjs')
    <!--<script src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
    <script type="text/javascript">
    	Zepto(function(){
    		function tel(telphone){
    			if(isiOS) {
					var data = {
						"tel":telphone
					}
					window.webkit.messageHandlers.tel.postMessage(telphone);
				}
    		}
    		tel('400-011-0061');
    		
    		
    		
    	})
    </script>-->
@stop