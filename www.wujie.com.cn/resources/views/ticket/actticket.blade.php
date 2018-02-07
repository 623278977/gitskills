@extends('layouts.default')
@section('css')
	<link href="{{URL::asset('/')}}/css/my_detial.css" rel="stylesheet" type="text/css" />
	<link href="{{URL::asset('/')}}/css/w-pages.css" rel="stylesheet" type="text/css" />
@stop
@section('main')
	<section id="ticketSection">
		<div class="container" id="ticketList">

		</div>
	</section>
@stop

@section('endjs')
	<script>var act_id ={{$id}}</script>
	<script typr="text/javascript" src="{{URL::asset('/')}}/js/act_ticket.js"></script>
@stop