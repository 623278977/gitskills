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
			id=arg['id'],
			uid=arg['uid']||'0',
			maker_id=arg['uid']||'0',
			sign=(window.location.href).indexOf('is_sign') > 0 ? true : false;
		function getAddress(id){
			var param={};
				param['id']=id;
			var url=labUser.api_path+'/activity/detail/_v020400';
				ajaxRequest(param,url,function(data){
					if(data.status){
						var html='',
							timestamp = Math.round(new Date().getTime() / 1000),
							end_time=data.message.end_time,
							title='活动地点('+data.message.activity_location_arr.length+')';
						setPageTitle(title)	;
						$.each(data.message.activity_location_arr,function(index,item){
							html+='<div class="address fline" data_id='+item.id+'><img class="gomap" src="{{URL::asset('/')}}/images/jump.png"><p class="ovo_name">'+item.subject+'</p>';
							html+='<p class="c9 detail_address">'+item.address+'</p>';
							html+='<p class="c9">'+item.tel+'</p>';
							html+='<p class="none des">'+removeHTMLTag(item.description)+'</p></div>';
						});
							html+='<div class="footer"><span class="f63">*挑选合适的会场，赶紧报名活动吧</span><button type="button" class="signup" style="background-color:#ff5a00">立即报名</button></div>';
						$('#address').html(html);
						if(sign){
							$('.footer').addClass('none');
						};
						if (timestamp > end_time) {
				       	  $('.signup').css('background-color', '#ccc').text('报名结束').attr('disabled','true');
				        }else{
				        	$(document).on('tap', '.signup', function () {
				                var ovoid=maker_id;
				                var act_id = id;
				                var act_name = data.message.subject;
				                ActivityApply(act_id, ovoid, act_name, 'activity');
				       		});
				        }				        
				        $('#address').removeClass('none');
				        // 跳转地图
						$(document).on('tap','.address',function(){
							var address=$(this).find('.detail_address').text(),
								ovo_name=$(this).find('.ovo_name').text(),
								des=removeHTMLTag($(this).find('.des').text());
								des = cutString(des, 40);
							locationAddress(address, ovo_name, des);
						});						
					}else{
						alert(data.message);
					}
				})
		};
		
		getAddress(id);
	})

 </script>
 <script>
 	 // title
        function setPageTitle(title) {
            if (isAndroid) {
                javascript:myObject.setPageTitle(title);
            } 
            else if (isiOS) {
                var data = {
                   "title":title
                }
                window.webkit.messageHandlers.setPageTitle.postMessage(data);
            }
        }
     
 </script>
@stop