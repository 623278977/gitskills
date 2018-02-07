//zhangxm

Zepto(function(){
	new FastClick(document.body);
	$('body').css('background','#f2f2f2');
			var args=getQueryStringArgs(),
            id = args['customer_id'] || '0',   //被查看的经纪人id 
            uid = args['agent_id'] || '0',    //登录的经纪人id             
			urlPath = window.location.href,
            origin_mark = args['share_mark'] || 0,//分销参数，分享页用
            code = args['code'] || 0;
		var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
        // 获取详情
		function getdetail(id,uid){
			var param={};
			param['agent_id']=id;
            param['login_agent_id']=uid;
            
            if(shareFlag){
                param['guess']=1;
            };
        var	url=labUser.agent_path + '/index/account/_v010000';
		ajaxRequest(param,url,function(data){
//			console.log(data);
			if(data.status){
				if(data.message){
					var contHtml = '',
					level_id = data.message.agent_data.level_id;
					contHtml += '<div class="share-head f36 fline mb2-5 none">无界商圈经纪人</div>';
					contHtml += '<div class="personal">';
					contHtml += '<div class="dis_star">';
					contHtml += '<div class="dis_con">';
					contHtml += '<div class="head_ava">';
					contHtml += '<img src="' + data.message.agent_data.avatar + '" alt="" class="company mr1-33"/>';
					if(data.message.agent_data.is_attestation==1){
						contHtml += '<img src="/images/agent/attestation.png" class="attestation"/>';
					};
					
					contHtml += '</div>';
//					contHtml += '<img src="'+data.message.agent_data.avatar+'" alt="" class="company mr1-33 fl "/>';
					contHtml += '<div class="xm-amounts ">';
					if(data.message.agent_data.realname==''){
						contHtml += '<p class="dis_con mb08"><span class="b f15 color3 text_black "> '+data.message.agent_data.nickname+'</span>';
					}else {
						contHtml += '<p class="dis_con mb08"><span class="b f15 color3 text_black "> '+data.message.agent_data.realname+'</span>';
					}
					
//					if(data.message.agent_data.is_attestation==1){
//						contHtml += '<span class="badge"><img src="/images/agent/badge_07.png" class="badge_07"/></span>';
//					}
					contHtml += '</p>';
					contHtml += '<p class="dis_con mb08">';
//					if(level_id==1){
//						contHtml += '<img src="/images/agent/tp.png" class="level"/>';
//					}else if(level_id==2){
//						contHtml += '<img src="/images/agent/yp.png" class="level"/>';
//					}else if(level_id==3){
//						contHtml += '<img src="/images/agent/jp.png" class="level"/>';
//					};
					
					contHtml += '<span class="color333 f12 share_lv mr05"><img src="/images/agent/level0'+data.message.agent_data.level_id+'.png"/></span>';
					if(data.message.agent_data.level_id==1){
						contHtml += '<span class="color333 f12">初级经纪人</span></p>';
					}
					if(data.message.agent_data.level_id==2){
						contHtml += '<span class="color333 f12">中级经纪人</span></p>';
					}
					if(data.message.agent_data.level_id==3){
						contHtml += '<span class="color333 f12">主任</span></p>';
					}
					if(data.message.agent_data.level_id==4){
						contHtml += '<span class="color333 f12">经理</span></p>';
					}
//					contHtml += '<p class="dis_con">';
//					if(data.message.agent_data.gender=='女'){
//						contHtml +=	'<img src="/images/agent/girl.png" class="gender xm-inb fl"/>';
//					}else if(data.message.agent_data.gender=='男'){
//						contHtml +=	'<img src="/images/agent/boy.png" class="gender xm-inb fl"/>';
//					};
//					
//					contHtml += '<span class="f12 color999">'+data.message.agent_data.zone+'</span></p>';
					contHtml += '</div></div>';
					if(data.message.agent_data.relation==1){
						contHtml +=	'<span class="rank f11"><img src="/images/agent/relation.png"/>我的邀请人</span>';
					}else if(data.message.agent_data.relation == 2){
						contHtml +=	'<span class="rank f11"><img src="/images/agent/relation.png"/>我的团队成员</span>';
					}else {
						contHtml += '<span class="rank f11"></span>';
					};
					contHtml += '</div>';
					contHtml += '<p class="keyword fline">';
					if(data.message.agent_data.tags.length>0){
						$.each(data.message.agent_data.tags, function(i,v) {
							if(v!=''){
								contHtml += '<span class="keywords m05 f11 color-years scale-1">'+v+'</span>';
							}
						});
					};
					contHtml += '</p>';
					contHtml += '<div class="f15 color6 xm-sign ui-nowrap-multi mudium">“&nbsp;'+data.message.agent_data.sign+'&nbsp;”</div></div></div>';
					//判断两者关系
					if(  data.message.relation_data){
						contHtml += '<div class="pl1-5 bgwhite mb1-2"><p class="fline mrl1-5"><span class=" f15 b color333">促单业绩</span></p>';
						contHtml += '<p class=" grade pr1-5 mrl1-5"><span class=" f12 color333">本季度已促单</span><span class=" f12 color666">'+data.message.relation_data.quarter_follow_orders+'单</span></p>';
						contHtml += '<p class=" grade pr1-5 mrl1-5"><span class=" f12 color333">当前跟单客户</span><span class=" f12 color666">'+data.message.relation_data.follow_customers+'人</span></p>';
						contHtml += '<p class=" grade pr1-5 mrl1-5"><span class=" f12 color333">累计促单</span><span class=" f12 color666">'+data.message.relation_data.total_follow_orders+'单</span></p></div>';
					};
					if(data.message.agent_data.brands.length>0){
						contHtml += '<div class="personals Medium"><p class="f15 fline ptb1-4 b keyword ">代理品牌<span class="keyword-num">&nbsp;('+data.message.agent_data.brands_count+')</span></p>';
						contHtml += '<div class="brand_wrap">';
						$.each(data.message.agent_data.brands, function(j,k) {
							contHtml += '<div class="xm-acting xm-inb mt1-5" brand_id="'+k.id+'"><img src="'+k.logo+'" class="xm-acting-img"/>';
							contHtml += '<div class="f1 xm-inb"><span class="f15 b xm-b brands_name">'+k.name+'</span>';
							contHtml += '<span class="f11 dark_gray xm-b">行业分类： </span>';
							contHtml += '<span class="f11 dark_gray xm-b">'+k.category_name+'</span><br />';
							contHtml += '<span class="f11 dark_gray xm-b">启动资金： </span>';
							contHtml += '<span class="f11 dark_gray xm-b">'+k.investment_min+'~'+k.investment_max+'万</span></div></div>';
						});
						contHtml += '</div>';
					}else {
						contHtml += '<div class="personal_none Medium"><p class="f15 fline ptb1-4 b keyword  ml1 mr1">代理品牌<span class="keyword-num">&nbsp;('+data.message.agent_data.brands_count+')</span></p>';
						contHtml += '<div id="defind"><img src="/images/agent/defind_brand.png" style="height: 16rem;width: 16rem;margin:0 auto;display: inline-block;"/></div>';
					}
					contHtml += '</div>';
					contHtml += '<div class="xm-btn bline">';
					if(data.message.agent_data.relation!=0){
						contHtml += '<button class="click lline chat"><img src="/images/agent/sms_03.png" alt="message"/></button>';
						contHtml += '<a href="tel:'+data.message.agent_data.username+'" class="click f15 "><img src="/images/agent/tel.png" alt="tel" /></a></div>';
					}else {
						contHtml += '<a href="tel:'+data.message.agent_data.username+'" class="clicks f15 "><img src="/images/agent/tel.png" alt="tel" /></a></div>';
					}
					
	 			}
			};
			$('#container').html(contHtml);
			if(isAndroid){
				$('.keywords').css({
					lineHeight:'2rem'
				});
			};
		});
//		console.log(param)
      };
		getdetail(id,uid);
		//点击相关品牌跳转
		$(document).on('click','.xm-acting',function(){
			var brand_id = $(this).attr('brand_id');
			window.location.href = labUser.path + 'webapp/agent/brand/detail?agent_id='+uid+'&id='+brand_id; 
		});
		
		//聊天
		function goChat(uType, uid, name) {
			if(isAndroid) {
				javascript: myObject.goChat(uType, uid,name);
			}
			else if(isiOS) {
				var data = {
					"uType": uType,
					"agent_id":uid,
					"name":name
				}
				window.webkit.messageHandlers.goChat.postMessage(data);
			}
		};
		$(document).on('click', '.chat', function() {
			var name = $('.text_black').html();
			goChat('A', id, name);
		});
});