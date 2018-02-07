//zhangxm
Zepto(function() {
	//zhangxm
	new FastClick(document.body);
	$('.body').css('background-color: #f2f2f2;');
	var args = getQueryStringArgs(),
		id = args['customer_id'] || '0',
		uid = args['agent_id'] || '0',
		urlPath = window.location.href,
		origin_mark = args['share_mark'] || 0, //分销参数，分享页用
		code = args['code'] || 0;
	var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
	function getdetail(uid) {
		var param = {};
		param['agent_id'] = uid;
		var	url=labUser.agent_path + '/customer/protected/_v010000';
		ajaxRequest(param,url,function(data){
			if(data.status){
				if(data.message){
//					console.log(data.message)
					var conHtml = '';
					if(data.message.length>0){
						conHtml += '<div class="top"><span class="mb1 color666 f12">按照保护期规则，您邀请的客户，将给与30天的保护期。</span><br /><span class="mb1 color666 f12">在保护期内，您可以安排对该客户的跟进。</span></div>';
						$.each(data.message,function(i,v){
							conHtml += '<div class="bgwhite pl1-5"><div class="remind-client fline"><div class="width80"><div class="skip" customer_id="'+v.customer_id+'"><img src="'+v.avatar+'" alt="" class="mr1 via" />';
							conHtml +='<div>';
							conHtml += '<div class="skips"><span class="f15 bold b color333 nickname">'+v.nickname+'</span>';
							if(v.gender == -1){
								conHtml += '<br />';
							}else if(v.gender == 0){
								conHtml += '<img src="/images/agent/girl.png" alt="" class="grades" /><br />';	
							}else if(v.gender == 1){
								conHtml += '<img src="/images/agent/boy.png" alt="" class="grades" /><br />';
							};
							conHtml += '</div>';
							conHtml += '<span class=" color666 f12">'+v.city+'</span></div></div>';
							conHtml += '</div>';
							conHtml += '<p class="f11 color999"><span class="f11 color999">还剩</span><span class="color999 f11 days">'+v.left_days+'</span><span>天</span></p></div></div>';
						});
					}
					$('#container').html(conHtml);
					if($('.days').html()<0){
						$('.remind-client').css('display','none');
					}
				}else{
					$('.define').removeClass('none');
				};	
			}else{
					$('.define').removeClass('none');
				};
		});
	};
	getdetail(uid);
	//点击跳转对象详情页
	$(document).on('click','.skip',function(){
		var customer_ids = $(this).attr('customer_id');
		window.location.href = labUser.path + 'webapp/agent/customer/detail?agent_id='+uid+'&customer_id='+customer_ids;
	});
	//时间戳转换
	function proDay(unix) {
    var newDate = new Date();
    newDate.setTime(unix * 1000);
    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
    var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
    var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
    return D;
}
});