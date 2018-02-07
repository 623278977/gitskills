@extends('layouts.default')
<!--zhangx-->
@section('css')
    
@stop
<!--zhangxm-->
@section('main')
	<section id="container" class="bgwhite">
		<div class="define" style="width: 100%;margin: auto;height: 100%;text-align: center;">
			<img src="/images/agent/404.png" style="width: 50%;text-align: center;margin-top: 55%;"/>
		</div>
	</section>
@stop
@section('endjs')
    <script type="text/javascript">
    	$('body').css('background','#ffffff');
		$(document).ready(function(){
			$('title').text('404');
		});
	</script>
@stop