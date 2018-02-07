
@extends('layouts.default')
@section('css')
     <link href="{{URL::asset('/')}}/css/agent/_v010300/investorlist.css" rel="stylesheet" type="text/css"/> 
     <link href="/css/animate.css" rel="stylesheet" type="text/css"/>
    
@stop

@section('main')
    <section class="containerBox none">
    	<!---->
    <div class="tips none"></div>
    <article class="animated zoomInLeft">
       <p class="f11 color999 mt1-2 ml1-5">“<span class="ui-brandname">一点点奶茶</span>”<span class="">品牌意向客户</span></p>
       <div class="chooseclient mt1-2 bgwhite A">
	       	<!-- <div class="fline choose_kehu" data="1" num="ad">
	       		<div class="investor ">
	       			<p class="mr1"><img src="/images/act_banner.png" class="avatar"/></p>
	       			<div class="investorMes">
	       				<p class=""><span class="f15 color333 mr05" >姚凯</span><img src="/images/agent/girl.png" class="grade" /></p>
	       				<p class=""><span class="f12 color666">上海&nbsp;浦东</span><span class=""></span></p>
	       			</div>
	       		</div>
	       		<img src="/images/agent/rightyellow.png" class="choosen none"/>
	       	</div> -->
       		
       		<!-- <div class="fline choose_kehu" data="2" num="ad">
	       		<div class="investor ">
	       			<p class="mr1"><img src="/images/act_banner.png" class="avatar"/></p>
	       			<div class="investorMes">
	       				<p class=""><span class="f15 color333 mr05" >姚凯</span><img src="/images/agent/girl.png" class="grade" /></p>
	       				<p class=""><span class="f12 color666">上海&nbsp;浦东</span><span class=""></span></p>
	       			</div>
	       		</div>
	       		<img src="/images/agent/rightyellow.png" class="choosen none"/>
	       	</div> -->
       </div>
       <!--其他客户-->
       <p class="f11 color999 mt1-2 ml1-5"><span class="">其他客户</span></p>   
       <div class="chooseclient mt1-2 bgwhite B">
	       <!-- 	<div class="fline choose_kehu" data="3" num="ad">
	       		<div class="investor ">
	       			<p class="mr1"><img src="/images/act_banner.png" class="avatar"/></p>
	       			<div class="investorMes">
	       				<p class=""><span class="f15 color333 mr05" >姚凯</span><img src="/images/agent/girl.png" class="grade" /></p>
	       				<p class=""><span class="f12 color666">上海&nbsp;浦东</span><span class=""></span></p>
	       			</div>
	       		</div>
	       		<img src="/images/agent/rightyellow.png" class="choosen none"/>
	       	</div> -->
       		
       		<!-- <div class="fline choose_kehu"  data="4" num="ad">
	       		<div class="investor ">
	       			<p class="mr1"><img src="/images/act_banner.png" class="avatar"/></p>
	       			<div class="investorMes">
	       				<p class=""><span class="f15 color333 mr05">姚凯</span><img src="/images/agent/girl.png" class="grade" /></p>
	       				<p class=""><span class="f12 color666">上海&nbsp;浦东</span><span class=""></span></p>
	       			</div>
	       		</div>
	       		<img src="/images/agent/rightyellow.png" class="choosen none"/>
	       	</div> -->
       </div>
     </article>
       <div class="tc none nocomment" id="nocommenttip3">
                <img src="{{URL::asset('/')}}/images/nomessage_icon1.png" style="height: 16rem;width: 16rem;margin:0 auto;display: inline-block;">
       </div>  
    </section>
    <section style="height: 10rem;"></section>
@stop

@section('endjs')
    <script src="{{URL::asset('/')}}/js/agent/_v010300/investorlist.js"></script>
    <script type="text/javascript">
    	$(document).ready(function(){$('title').text('选择发送客户')});
    </script>
@stop