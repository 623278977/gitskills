@extends('layouts.default')  
<!--zhangxm-->        
@section('css')
<link href="{{URL::asset('/')}}/css/agent/pactdetails.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
<section class="containerBox">
	<div>
            <canvas id="PDF"></canvas>     
      </div>
</section>
<!--<section class="enjoy" style='padding-bottom:5.5rem'></section>-->
@stop
@section('endjs')
      <script type="text/javascript" src="{{URL::asset('/')}}/js/agent/src/pdf.js"></script>
      <script type="text/javascript" src="{{URL::asset('/')}}/js/agent/src/pdf.worker.js"></script>
      <script type="text/javascript">
            
      </script>
@stop