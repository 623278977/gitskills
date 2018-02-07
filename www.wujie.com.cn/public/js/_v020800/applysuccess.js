Zepto(function() {
	if(!is_weixin() && !isiOS && !isAndroid) {
		//              $('.succ-sec').css({'width':'740px','margin':'auto'});
		$('#installapp').remove();
	};
	new FastClick(document.body);
	var args = getQueryStringArgs(),
		order_no = args['order_no'] || '0',
		activity_id = args['activity_id'],
		agent_id = args['agent_id'] || '0',
		types = args['types'],  //是否是受邀报名    0：通过邀请函报名成功  1:未通过邀请函报名成功
		urlPath = window.location.href,
		shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
	function getdetail(order_no) {
//		if(!shareFlag) {
//			setPageTitle('报名成功');
//		}
		var param = {};
		param['order_no'] = order_no;
		param['agent_id'] = agent_id;
		var url = labUser.api_path + '/activity/check-and-apply/_v020800';
		ajaxRequest(param, url, function(data) {
			if(data.status){
				if(data.message) {
					$('.act_id').attr('actId',data.message.id)
					$('.des_name').text(data.message.subject);
					$('.begin_time').text(data.message.begin_time);
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
					if (data.message.type!='') {
						$('.type').text(data.message.type);
					} else{
						$('.type').text('/');
					};
					$('.mem_num').text(data.message.images_count);
					
					//是否通过邀请函报名
					if(data.message.agent_id>0){
						$('.agent_name').text(data.message.agent_name);
					}else {
						$('.invite_way').addClass('none');
						$('.invite_way').removeClass('dis_bet');
					}
					
					//判断报名人数
					if(data.message.images_count>15){
						$('.member').removeClass('none');
					}
					var conHtml = '';
					if(data.message.images.length > 0) {
						$.each(data.message.images, function(i, v) {
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
							window.location.href = 'openwjsq://' + 'webapp/activity/detail/_v020700?pagetag=02-2&uid=0&makerid=0&id=' + activity_id;
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
							window.location.href = 'openwjsq://welcome' + '/webapp/activity/detail/_v020700?pagetag=02-2&uid=0&makerid=0&id=' + activity_id;
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
    act(activity_id);
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
    var type = 'Activity',
     title = $('.des_name').text();
     url = labUser.path+'webapp/activity/detail/_v020700?pagetag=02-2&id='+$('.act_id').attr('actId')+'&is_share=1';
     img =  $('#succ_shareimg').attr('src');
     header = '活动',
     content = '我在无界商圈发现了一个不错的活动，想邀请你一起参加！';
     begintime = $('.begin_time').html(),
     citys = $('#citys').html(),
     act_id=$('.act_id').attr('actId');
     console.log(citys,begintime)
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
}
	
});