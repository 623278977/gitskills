//zhangxm
Zepto(function() {
	new FastClick(document.body);
	var args = getQueryStringArgs(),
		id = args['customer_id'] || '0',
		uid = args['agent_id'] || '0',
		urlPath = window.location.href,
		origin_mark = args['share_mark'] || 0, //分销参数，分享页用
		code = args['code'] || 0;
	var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
	//电话号
var phone_num='';
function decrypt(en_tel){
	var param = {};
		param['en_tel'] = en_tel;
		param['platform'] = 'agent';
	var url = labUser.path + '/data-center/decrypt';
	ajaxRequest(param,url,function(data){
		if (data.status) {
			phone_num = data.message;
			
		}
	})
};
	function getdetail(uid) {
		var param = {};
		param['agent_id'] = uid;
		var	url=labUser.agent_path + '/customer/activity-remind/_v010000';
		ajaxRequest(param,url,function(data){
			if(data.status){
				if(data.message.length>0){
					var conHtml = '';
					$.each(data.message, function(i,v) {
						conHtml += '<div class="top f12 color666"><span class="mb1">活动名称：</span><span class="mb1">'+v.activity_title+'</span><br />';
						conHtml += '<span class="">活动时间：</span><span class="">'+v.activity_begin_time+'</span>';
						if(v.today){
							conHtml += '<span class="">'+v.today+'</span></div>';
						}else {
							conHtml += '</div>';
						};
						if(v.list.length>0){
							$.each(v.list,function(j,k){
								conHtml += '<div class="bgwhite pl1-5"><div class="remind-client fline"><div class="width80"><div class="skip" customer_id="'+k.uid+'"><img src="'+k.avatar+'" alt="" class="mr1 via" />';
								conHtml +='<div>';
								conHtml += '<div class="skips"><span class="f15  b color333 nickname">'+k.nickname+'</span>';
								if(k.gender==0){
										conHtml += '<img src="/images/agent/girl.png" alt="" class="grades " /><br />';	
								}else if(k.gender==1){
										conHtml += '<img src="/images/agent/boy.png" alt="" class="grades " /><br />';
								}else if(k.gender==-1){
										conHtml += '<br />';
								}else {
									conHtml += '<br />'
								};
								conHtml += '</div>';
								conHtml += '<span class=" f12 color666">'+k.city+'</span></div></div>';
								conHtml += '</div>';
								decrypt(k.phone);
								conHtml += '<p class=""><span class="c2873ff sms_span" is_pub_phone="'+k.is_pub_phone+'">短信提醒</span></p></div></div>';
							})
						}
					});
					$('#container').html(conHtml);
				}else{
					$('.define').removeClass('none');
				};
				
			}else{
					$('.define').removeClass('none');
				};
			
		});
		
	};
	getdetail(uid);
	$(document).on('click','.sms_span',function(){
		var is_pub_phone = $(this).attr('is_pub_phone');
		if(is_pub_phone == 0){
			tips('手机号未公开！');
		}else {
			note(phone_num);
		}
	});
	//点击跳转对象详情页
	$(document).on('click','.skip',function(){
		var customer_ids = $(this).attr('customer_id');
		window.location.href = labUser.path + '/webapp/agent/customer/detail?agent_id='+uid+'&customer_id='+customer_ids;
	});
	function yeardatetime(unix) {
    var newDate = new Date();
    newDate.setTime(unix * 1000);
    var Y = newDate.getFullYear();
    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
    return M + '月' + D;
};
//发送短信
function note(phone_num){
	if (isAndroid) {
        javascript:myObject.note(phone_num);
    } else if (isiOS) {
        var message = {
        method : 'note',
        params : {
          'phone_num':phone_num
        }
    }; 
        window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
    }
};
});