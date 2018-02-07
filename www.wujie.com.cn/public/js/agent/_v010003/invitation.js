//zhangxm
Zepto(function() {
	new FastClick(document.body);
	$('body').css('background','#f2f2f2');
	var args = getQueryStringArgs(),
		id = args['customer_id'] || '0', //被查看的投资人id   
		uid = args['sender_agent_id'] || '0',  //登录的id 
		orter_uid = args['agent_id'] || '0', //查看投资人的经纪人id
		urlPath = window.location.href,
		origin_mark = args['share_mark'] || 0, //分销参数，分享页用
		code = args['code'] || 0;
	var cardFlag = urlPath.indexOf('is_card') > 0 ? true : false;
	var is_relation,levelId;
	// 获取详情
	function getdetail(id, uid) {
		var param = {};
		param['customer_id'] = id;
		param['agent_id'] = orter_uid; //查看投资人的经纪人id
		param['customer_agent_id'] = uid; //让别人看自己投资人的经纪人id
		
		var url = labUser.agent_path + '/customer/detail-infos/_v010003';
		ajaxRequest(param, url, function(data) {
			if(data.status) {
				var conHtml = '';
				if(data.message) {
					var k = data.message;
					is_relation = data.message.is_relation;
					levelId = data.message.user_level_id;
					$('.address_list').attr('level_id',levelId);
					$('.chat').attr('real_name',k.nickname);
					var created_at = unix_to_yeardate(data.message.created_at);//注册时间
					if(data.message.last_login!=0){
						var last_login = unix_to_mdhm(data.message.last_login);	//最后一次登录时间
					}
					conHtml += '<div class="datum bcg-f medium"><img src="'+k.avatar+'" class="photo"/>';
					conHtml += '<div class="datum-l "><span class="f18 b text_black bold">'+k.realname+'</span><br />';	
					conHtml += '<p class="">';
					if(k.gender=='男'){
						conHtml +=	'<img src="/images/agent/boy.png" class="gender mt05 mr05"/>';
					}else if(k.gender=='女'){
						conHtml +=	'<img src="/images/agent/girl.png" class="gender mt05 mr05"/>';
					};
					conHtml += '<span class="city dark_gray f14 mt05">'+k.city+'</span><br />';
					conHtml += '</p>';
					if(data.message.last_login==0){
						conHtml += '<span class="dark_gray f14 mt05">未登录</span></div>';
					}else {
						conHtml += '<span class="dark_gray f14 mt05">最后一次登录：'+last_login+'</span></div>';
					};
//					if(k.protect_time>0 && !cardFlag){
//						conHtml += '<div class="defend"><span class="f11 medium protect_s">保护期：</span><span class="f15 bold">'+k.protect_time+'</span><span class="f11 medium">天</span></div>';
//					}
					conHtml += '</div>';
					conHtml += '<p class="keyword fline">';
					if(k.tags!=''){
						if(k.tags.customer_time!=''){
							conHtml += '<span class="keywords m05 f11 color-years scale-1">'+k.tags.customer_time+'</span>';
						};
						if(k.tags.constellation!=''){
							conHtml += '<span class="keywords m05 f11 color-years scale-1">'+k.tags.constellation+'</span>';
						};
						if(k.tags.customer_zone!=''){
							conHtml += '<span class="keywords m05 f11 color-years scale-1">'+k.tags.customer_zone+'</span>';
						};
						if(k.tags.intention!='' && k.tags.intention!='未知'){
							conHtml += '<span class="keywords m05 f11 color-years scale-1">'+k.tags.intention+'</span>';
						}
						if(k.tags.customer_money!=''){
							conHtml += '<span class="keywords m05 f11 color-years scale-1">'+k.tags.customer_money+'</span>';
						};
						if(k.tags.customer_cate.length>0 || k.tags.customer_cate!=''){
							$.each(k.tags.customer_cate, function(i,v) {
									conHtml += '<span class="keywords m05 f11 color-years scale-1">'+v+'</span>';							
							});
						};
					};
					conHtml += '</p>';
					if(k.relation){
						if(k.relation!=''){
							conHtml += '<div class="relation bcg-f medium"><span class="f15 color3 b">关系</span><p><span class="color999 f12 ">'+k.relation+'</p></div>';
						}
					};
					
					conHtml += '<div class="content bcg-f"><p class="fline"><span class="color3 b f15">地区</span><span class="color999 f12">'+k.city+'</span></p>';
					conHtml +='<p class="fline"><span class="color3 b f15">学历</span><span class="color999 f12">'+k.diploma+'</span></p>';
					conHtml += '<p class="fline"><span class="color3 b f15">职位</span><span class="color999 f12">'+k.positions+'</span></p>';
					conHtml += '<p class="fline"><span class="color3 b f15">收入</span><span class="color999 f12">'+k.earning+'</span></p>';
					conHtml += '<p class="fline"><span class="color3 b f15">感兴趣行业</span><span class="color999 f12">'+k.interest_industries+'</span></p>';
					conHtml += '<p class="fline"><span class="color3 b f15">投资意向</span><span class="color999 f12">'+k.invest_intention+'</span></p>';
					conHtml += '<p class="fline"><span class="color3 b f15">投资额度</span><span class="color999 f12">'+k.invest_quota+'</span></p></div>';
					conHtml += '<div class="content bcg-f">';
					if(k.invite_agent){
						if(k.invite_agent!=''){
							conHtml += '<p class="fline"><span class="color3 b f15">邀请人</span><span class="color999 f12">'+k.invite_agent+'</span></p>';
						}
					}
					
					conHtml += '<p class="fline"><span class="color3 b f15">注册时间</span><span class="color999 f12">'+created_at+'</span></p></div>';
					conHtml += '<div class="btn">';
					if(k.is_relation==0){
						conHtml += '<div class="address_list f15" phone="'+k.relation_tel+'">';
						conHtml += '<span class="add_list">添加至通讯录</span>';
						conHtml += '</div>';
						
					}else {
						conHtml += '<button class="btn-l border_xm" real_name= "'+k.nickname+'"><img src="/images/agent/sms_03.png" class="tel_img"/></button>';
						if(k.has_tel == 0){
							conHtml += '<button class="btn-r border_xm tel_none"><img src="/images/agent/tel.png" class="tel_img"/></button>';
						}else {
							conHtml += '<button class="btn-r border_xm"><a href="tel:'+k.relation_tel+'"><img src="/images/agent/tel.png" class="tel_img"/></a></button>';
						}
//						conHtml += '<button class="btn-r border_xm"><a href="tel:'+k.relation_tel+'"><img src="/images/agent/tel.png" class="tel_img"/></a></button></div>';
					}
					conHtml += '</div>';
					
					//加载蒙版
//					conHtml += '<div class="masking none"><div class="masking-con medium"><p class="masking-p1 f15 medium color-f pl-r fline"><span class="bold f15 b c0">当前还剩'+k.protect_time+'天保护期</span></p>';
//					conHtml += '<p class="pl-r f12 color666 pw-lr p_flex"><span class="dot"></span><span class="inline f12 color666 medium">是否取消对该投资人的邀请保护？</span></p>';
//					conHtml += '<p class="pl-r f12 color666 pw-lr p_flex"><span class="dot"></span><span class="inline f12 color666 medium">保护期取消后，投资人将享受正常派单操作，提供经纪人跟单服务。</span></p>';
//					conHtml += '<p class="pl-r f12 color666 pw-lr p_flex"><span class="dot"></span><span class="inline f12 color666 medium">关闭保护期后，将无法开启</span></p>';
//					conHtml += '<p class="pl-r f12 color666 pw-lr masking-p-btn"><span class="cancel color999">取消</span><span class="close color-f">关闭保护期</span></p>';
//					conHtml += '</div></div>';
				}
				$('#container').html(conHtml);
				if(isAndroid){
					$('.keywords').css({
						lineHeight:'2rem'
					});
				};
				
				//当经济人分享投资人给另一个经纪人时
				if(cardFlag){
					$(document).ready(function(){
			    		$('title').text('投资人详情');  
			       });
					$('.btn').addClass('none');
					if(k.is_relation==0){
						$('.address_list').attr('phone',k.relation_tel);
						$('.address_list').removeClass('none');
					}else {
						$('.chat').removeClass('none');
					};
				};
			};
			
			//非公开时，弹出提示框
			$(document).on('click','.tel_none',function(){
				$('.masking_tels').removeClass('none');
			});
			//隐藏弹出框
			$(document).on('click','.cancel_tel',function(){
				$('.masking_tels').addClass('none');
			});
			
			//点击保护期弹出蒙版
			$(document).on('click','.defend',function(){
				$('.masking').removeClass('none');
			});
			//点击取消
			$(document).on('click','.cancel',function(){
				$('.masking').addClass('none');
			});
			//点击关闭保护人
			$(document).on('click','.close',function(){
				var params = {};
				params['customer_id'] = id;
				params['agent_id'] = uid;
				params['protect_result'] = 0;
				console.log(params);
				var url = labUser.agent_path + '/customer/if-protect';
				ajaxRequest(params,url,function(data){
					if(data.status){
						if($('.defend')){
							$('.defend').addClass('none');
						}
					};
					location.reload();
				});
				$('.masking').addClass('none');
			});
		});
		//		console.log(param)
	};
	getdetail(id, uid);
	//时间戳转换
	function yeardate(unix) {
    var newDate = new Date();
    newDate.setTime(unix * 1000);
    var Y = newDate.getFullYear();
    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
    return Y + '年' + M + '月' + D + '日';
};
/*时间戳转换成月日时分*/
function unix_to_mdhm(unix) {
    var newDate = new Date();
    newDate.setTime(unix * 1000);
    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
    var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
    var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
    return M + '月' + D + '日 ' + h + ':' + m;
};
//跳转聊天 id:被查看的人的id    name：被查看人的名字
function goChat(uType,id, name) {
	if(isAndroid) {
		javascript: myObject.goChat(uType,id,name);
	}
	else if(isiOS) {
		var data = {
			"uType":uType,
			"id": id,
			"name":name
		}
		window.webkit.messageHandlers.goChat.postMessage(data);
	}
};
$(document).on('click','.btn-l',function(){
	var nickname =  $(this).attr('real_name');
	goChat('c',id,nickname);
});
$(document).on('click','.chat',function(){
	var nickname =  $(this).attr('real_name');
	goChat('c',id,nickname);
});

//添加投资人到经纪人的通讯录中
$(document).on('click','.address_list',function(){
	var phone = $(this).attr('phone');
	
	addressBook(id,orter_uid,phone,'add_friends','user');
	refreshContacts();
});
function refreshContacts() {
	if(isAndroid) {
		javascript: myObject.refreshContacts();
	}
	else if(isiOS) {
		var data = {
			'id':0,
			'agend_id':0
		}
		window.webkit.messageHandlers.refreshContacts.postMessage(data);
	}
};
function addressBook(friends_id,uid,phone,type,friends_type){
	var params = {};
	params['agent_id'] = uid;
	params['friends_id'] = friends_id;
	params['phone'] = phone;
	params['type'] = type;
	params['friends_type']=friends_type;
	var url = labUser.agent_path + '/message/add-friends/_v010003';
	ajaxRequest(params,url,function(data){
		if(data.status){
			tips(data.message);
			setTimeout(function(){
				window.location.reload();
			},1000)
		}else{
			tips(data.message);
		}
	});
}

////点击确定进行实名认证
//$(document).on('click','.back',function(){
//	authentication()
//});
//function authentication() {
//	if(isAndroid) {
//		javascript: myObject.authentication();
//	}
//	else if(isiOS) {
//		var data = {
//			'id':0,
//			'agend_id':0
//		}
//		window.webkit.messageHandlers.authentication.postMessage(data);                                                                                                                                                                                                                                               
//	}
//};
////点击取消关闭弹框
//$(document).on('click','.stay',function(){
//	$('.masking').addClass('none');
//})


});