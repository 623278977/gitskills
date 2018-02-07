@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/act_related.css?v=20162123" rel="stylesheet" type="text/css"/>
@stop
@section('main')
	<section class="enrollment none">
		<div id="list">
			<!-- <p class="enroll"><img src="" alt=""><span class="name">张二仇</span><span class="ticket r">现场票（29）</span></p> -->
		</div>		
		<div class="more none">点击加载更多</div>
		<div class="no_data none">
			<img src="{{URL::asset('/')}}/images/no_data.png" alt="">
			<p class='nomessage f14'>暂时没有人报名该活动！</p>
		</div>
	</section>
	
@stop
@section('endjs')
	<script >
		 Zepto(function (){
			var urlPath=window.location.href,
				arg=getQueryStringArgs(),
				activity_id=arg['id'];
			var is_share = urlPath.indexOf('is_share') > 0 ? true : false;
			function getUsers(activity_id,offset,size){
				var param={};
					param['activity_id']=activity_id;

				var url=labUser.api_path+'/activity/signuserlist/_v020400';
				ajaxRequest(param,url,function(data){
					if(data.status){
						var title='报名人数('+data.message.length+')';						
						if(!is_share){
							setPageTitle(title);
						};
						var list = data.message;
						if(list==''){
							$('#list').addClass('none');
							$('.no_data').removeClass('none');
						}else{
							$('#list').removeClass('none');
						};
			            var sum = data.message.length;			    
			            var Html = '';
			            if(sum - offset < size ){
			                size = sum - offset;			              
			              }           
			            for(var j,i=offset; i<(size+offset); i++){
			            	if(list[i].price==0){
			            		j='免费';
			            	}else{
			            		j=list[i].price+'元';
			            	}
			                Html+='<p class="enroll fline"><img src='+list[i].avatar+'><span class="name">'+list[i].name+'</span><span class="ticket r">'+list[i].type_name+' ('+j+')</span></p>';	
            			};
        				$('#list').append(Html);
    
            			if ( (offset + size) >= sum){
			                $(".more").hide();
			            }else{
			                $(".more").show();
			            };
						$('.enrollment').removeClass('none');
					}else{
						alert(data.message);
					}
				});	
			};
		// 加载更多
		    var counter = 0; /*计数器*/
		    var pageStart = 0; /*offset*/
		    var pageSize = 15; /*size*/
		    
		    /*首次加载*/
		    getUsers(activity_id,pageStart, pageSize);
		    
		    /*监听加载更多*/
		    $(document).on('tap', '.more', function(){
		        counter ++;
		        pageStart = counter * pageSize;		        
		        getUsers(activity_id,pageStart, pageSize);
		    });
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