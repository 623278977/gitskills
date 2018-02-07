Zepto(function() {
	if(!is_weixin() && !isiOS && !isAndroid) {
		$('#installapp').remove();
	};
	new FastClick(document.body);
	var args = getQueryStringArgs(),
		order_no = args['order_no'] || '0',
		agent_id = args['agent_id'] || '0',
		types = args['types'],  //是否是受邀报名    0：通过邀请函报名成功  1:未通过邀请函报名成功
		urlPath = window.location.href,
		shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
	function getdetail(order_no) {
//		if(!shareFlag) {
//			setPageTitle('报名成功');
//		}
		var param = {};
		param['id'] = order_no;
		param['agent_id'] = agent_id;
		var url = labUser.agent_path + '/activity/apply-success/_v010005';
		ajaxRequest(param, url, function(data) {
			if(data.status){
				if(data.message) {
					$('.act_id').attr('actId',data.message.activity_id);
					$('.des_name').text(data.message.subject);
					$('.begin_time').text(gw_now(data.message.begin_time));
					$('#citys').text(data.message.zone_name);
					if(data.message.name!=''){
						$('.names').text(data.message.name);
					}else {
						$('.names').text('/');
					};
					if(data.message.tel!=''){
						$('.tel').text(data.message.tel);
					}else {
						$('.tel').text('/');
					};
					if(data.message.company!=''){
						$('.company').text(data.message.company);
					}else {
						$('.company').text('/');
					};
					if (data.message.job!='') {
						$('.job').text(data.message.job);
					} else{
						$('.job').text('/');
					};
					if (data.message.ticket!='') {
						$('.ticket').text(data.message.ticket);
					} else{
						$('.ticket').text('/');
					};
					$('.mem_num').text(data.message.sign_count);
					
					
					//判断报名人数
					if(data.message.sign_count>15){
						$('.member').removeClass('none');
					}
					var conHtml = '';
					if(data.message.sign_users.length > 0) {
						$.each(data.message.sign_users, function(i, v) {
							conHtml += '<img src="' + v.image + '" class="mr1 avaters mb1"/>';
						});
					};
				} else {
					alert(data.message);
				};
				$('.mem_ava').html(conHtml);
			}
		});
		//是否在分享页面
		function share(is_flag) {
			if(is_flag) {
				$('#loadAppBtn').removeClass('none');
				$('#installapp').removeClass('none');
				$('.succ-share').addClass('none');
				//浏览器判断
				if(is_weixin()) {
					/**微信内置浏览器**/
					$(document).on('tap', '#loadapp,#openapp', function() {
						var _height = $(document).height();
						$('.safari').css('height', _height);
						$('.safari').removeClass('none');
					});
					//点击隐藏蒙层
					$(document).on('tap', '.safari', function() {
						$(this).addClass('none');
					});
				} else {
					if(isiOS) {
						//打开本地app
						$(document).on('tap', '#openapp', function() {
							// var strPath = window.location.pathname.substring(1);
							// var strParam = window.location.search;
							// var appurl = strPath + strParam;
							// var share = '&is_share';
							// var appurl2 = appurl.substring(0, appurl.indexOf(share));
							// window.location.href = 'openwjsq://' + appurl2;
							window.location.href = 'openwjsq://' + '/webapp/agent/applysuccess/_v010005?order_no=' + order_no +'&agent_id='+agent_id;
						});
						/**下载app**/
						$(document).on('tap', '#loadapp', function() {
							window.location.href = 'https://itunes.apple.com/app/id981501194';
						});
					} else if(isAndroid) {
						$(document).on('tap', '#loadapp', function() {
							window.location.href = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.tyrbl.wujiesq';
						});
						$(document).on('tap', '#openapp', function() {
							// window.location.href = 'openwjsq://welcome'+ '/webapp/activity/detail?pagetag=02-2&uid=0&makerid=0&id='+act_id;
							window.location.href = 'openwjsq://welcome' + '/webapp/agent/applysuccess/_v010005?order_no=' + order_no +'&agent_id='+agent_id;
						});
					}
				}
			}
		};
		
		share(shareFlag);
	};
	getdetail(order_no);
	
	//获取活动的缩略图
    function actDetail(result) {
        $('#succ_shareimg').attr('src',result.share_image);
        $('#succ_des').text(result.description);
        $('#citys').text(result.zone_name);
    }
	//获取活动的缩略图和简介
    function act(id) {
       var param = {
            "id":id,
        };
        var url = labUser.api_path + '/activity/detail/_v020500';
        ajaxRequest(param, url, function (data) {
            if (data.status) {
               actDetail(data.message);
               // if (data.message.share_reward_unit != 'none') {
               //       getReward(sharemark,'enroll',0,code);
               // }
            } 
        }); 
    };
    act(order_no);
	var avaHeight = Math.floor($('.mem_ava').height() / 30);
	if(avaHeight > 10) {
		$('.mem_ava').addClass('ava');
		//          	$('.ava').css('overflow','hidden');
		//          	console.log(avaHeight)
		$('.mem_more').removeClass('none');
	};
	$(document).on('click', '.mem_more', function() {
		$('.mem_ava').removeClass('ava');
		$('.mem_more').addClass('none');
	});
	//页面点击分享
	$(document).on('click', '.btn', function() {
		showShare();
	});
 //分享
function showShare() {
    var type = 'activity',
     	title = $('.des_name').text(),
     	act_id=$('.act_id').attr('actId'),
     	url = labUser.path+'webapp/agent/activity/detail/_v010005?id='+act_id,
     	img =  $('#succ_shareimg').attr('src'),
     	header = '活动',
     	content = '我在无界商圈发现了一个不错的活动，想邀请你一起参加！',
     	begintime = $('.begin_time').html(),
     	citys = $('#citys').html();
    shareOut(title, url, img, header, content, begintime, citys, act_id, type);
}  
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
};
function gw_now(unix){
	var newDate = new Date();
    newDate.setTime(unix * 1000);
	var Y = newDate.getFullYear();
	var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
	var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
	var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
	var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
	switch (newDate.getDay()) {
		case 0:week="周日";break
		case 1:week="周一";break
		case 2:week="周二";break
		case 3:week="周三";break
		case 4:week="周四";break
		case 5:week="周五";break
		case 6:week="周六";break
	}
// obj.innerHTML=year + "-" + month + "-" + day + " " + hour + ":" + minute + ":" + second + " " + week; 
// setTimeout("gw_now('" + id + "')",1000);
 return Y + "-" + M + "-" + D + "  " + week + "  " + h + ":" + m;
}

	
});