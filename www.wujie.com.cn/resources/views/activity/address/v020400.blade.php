@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/act_related.css?v=20162123" rel="stylesheet" type="text/css"/>
@stop
@section('main')
	<section style='background-color: #fff;padding-left:1.33rem;' class="none" id="address" >
		<!-- <div class="address" >		
				<p class="ovo_name">杭州OVO路演中心</p>
				<p class="c9">浙江杭州下城区体育场路浙江国际大酒店11F</p>
				<p class="c9">0571-1234567</p>			
		</div>
		<div class="footer">
			<span class="f63">*挑选合适的会场，赶集报名活动吧</span>
			<button type='button' class="signup">立即报名</button>
		</div> -->
	</section>
@stop

@section('endjs')
 <script type="text/javascript">
	Zepto(function () {
		var arg=getQueryStringArgs(),
			activity_id=arg['id'],
			maker_id=arg['maker_id']||'0';
		function getAddress(activity_id){
			var param={};
				param['activity_id']=activity_id;
			var url=labUser.api_path+'/activity/makers';
				ajaxRequest(param,url,function(data){
					if(data.status){
						var html='';
						$.each(data.message,function(index,item){
							html+='<div class="address" data_id='+item.maker_id+'><img class="gomap" src="{{URL::asset('/')}}/images/go_map_03.png"><p class="ovo_name">'+item.subject+'</p>';
							html+='<p class="c9">'+item.address+'</p>';
							html+='<p class="c9">'+item.tel+'</p></div>'
						});
							html+='<div class="footer"><span class="f63">*挑选合适的会场，赶集报名活动吧</span><button type="button" class="signup">立即报名</button></div>';
						$('#address').html(html);
					}else{
						alert(data.message);
					}
				})
		};
		function getTime(id){
			var param={};
				param['id']=id;
			var url=labUser.api_path+'/activity/detail';
			ajaxRequest(param,url,function(data){
				if(data.status){
					// $('#address').attr({'data_time':data.message.self.end_time,'data_sub':data.message.self.subject});
					var timestamp = Math.round(new Date().getTime() / 1000);
					var end_time=data.message.self.end_time;
					console.log(end_time);
					console.log(timestamp);
					//报名结束
			        if (timestamp > end_time) {
			       	  $('.signup').css('background-color', '#ccc').text('报名结束').attr('disabled','true');
			        }else{
			        	$(document).on('click', '.signup', function () {
			                var ovoid = maker_id;
			                var act_id = activity_id;
			                var act_name = data.message.self.subject;
			                ActivityApply(act_id, ovoid, act_name, 'activity');
			       		});
			        }
					// signup();
					$('#address').removeClass('none');
				}
			})
		};
		getAddress(activity_id);
		getTime(activity_id);
	// 跳转地图
		$(document).on('click','.address',function(){
			var data_id=$(this).attr('data_id');
			window.location.href=labUser.path+'/webapp/activity/bmap?id='+activity_id+'&maker_id='+data_id;
		});
	})
 </script>
@stop