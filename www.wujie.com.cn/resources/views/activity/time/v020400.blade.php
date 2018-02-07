@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/act_related.css?v=20162123" rel="stylesheet" type="text/css"/>
@stop
@section('main')
	<section style='background-color: #fff;' class="none time">
		<div class="c0">
			<p>活动开始时间：<span id='activity_time'></span></p>
			<p>活动时长预计：<span id='expected_time'></span></p>
		</div>
		<div class="c6">
			<p>开始签到时间：<span id='sign_time'>提前30分钟开始签到</span></p>
			<p >线上直播开始时间：<span id='online_time'>2016年11月29日 8:10</span></p>
			<p class="f63">* 时间具体的调整，以当天实际通知为准</p>
		</div>
	</section>
@stop

@section('endjs')
 <script type="text/javascript">
	Zepto(function () {
		var arg=getQueryStringArgs(),
			id=arg['id'];
		function getTime(id){
			var param={};
				param['id']=id;
			var url=labUser.api_path+'/activity/detail';
				ajaxRequest(param,url,function(data){
					if(data.status){
						var begin_time = unix_to_yeardatetime(data.message.self.begin_time),
							end_time = unix_to_yeardatetime(data.message.self.end_time),
							
							expected_time=Math.round((data.message.self.end_time-data.message.self.begin_time)/3600);
							console.log(end_time);
							console.log(expected_time);
						$('#activity_time').html(begin_time);
						$('#expected_time').html(expected_time+'小时');
						$('.time').removeClass('none');
					}else{
						alert(data.message);
					}
				})

		}	

		getTime(id);
	})
 </script>
@stop