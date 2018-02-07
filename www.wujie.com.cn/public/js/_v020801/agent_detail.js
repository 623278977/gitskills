Zepto(function(){
	//zhangxm
	new FastClick(document.body);
	var args = getQueryStringArgs(),
		id = args['customer_id'] || '0', //投资人id
		uid = args['agent_id'] || '0',   //被查看的经纪人id
		urlPath = window.location.href;
		var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
	function getdetail(id){
		var param = {};
		param['customer_id'] = id;
		param['agent_id'] = uid;
		var url = labUser.api_path + '/user/details/_v020800';
		ajaxRequest(param,url,function(data){
			if(data.status){
				if(data.message){
					var conHtml = '';
					var d = data.message;
					conHtml+='<div class="personal bgwhite" class=""><div class="messa head pl-r"><div class="messa-l"><img src="'+d.agent.avatar+'" alt="" class="avatar mr05" />';
					conHtml+='<div class="">';
					if(d.agent.is_public_realname==0){
						conHtml += '<p class="dis_con mb08"><span class=" bold f14 nickname">'+d.agent.nickname+'</span>';
					}else {
						conHtml += '<p class="dis_con mb08"><span class=" bold f14 nickname">'+d.agent.realname+'</span>';
					}
					conHtml += '<span class="badge"><img src="/images/agent/badge_07.png" class="badge_img"/></span></p>';
					if(d.agent.gender == '女'){
						conHtml+='<div class="gen-zone"><img src="/images/agent/girl.png" class="gender"/>';
					}else if(d.agent.gender == '男'){
						conHtml+='<div class="gen-zone"><img src="/images/agent/boy.png" class="gender"/>';
					}else {
						conHtml+='<div class="gen-zone">';
					};
					conHtml+='<span class="zone f12 color999 ">'+d.agent.zone+'</span></div></div></div>';
					conHtml+='<div class="">';
					if(d.agent.level_num==1){
						conHtml+='<img src="/images/agent/tp.png" alt="" class="medal"/>&nbsp;';
						conHtml+='<span class="f12 color333  level">'+d.agent.level+'</span></div>';
					}else if(d.agent.level_num==2){
						conHtml+='<img src="/images/agent/yp.png" alt="" class="medal"/>&nbsp;';
						conHtml+='<span class="f12 color333  level">'+d.agent.level+'</span></div>';
					}else if(d.agent.level_num==3) {
						conHtml+='<img src="/images/agent/jp.png" alt="" class="medal"/>&nbsp;';
						conHtml+='<span class="f12 color333  level">'+d.agent.level+'</span></div>';
					}else {
						conHtml+='<img src="" alt="" class="medal"/>&nbsp;';
						conHtml+='<span class="f12 color333  level"></span></div>';
					};
					conHtml+='</div>';
					conHtml+='<p class="keyword fline pl-r  tags">';
					if(d.agent.tags){
						if(d.agent.tags.length>0){
							$.each(d.agent.tags, function(j,k) {
								if(k!=''){
									conHtml += '<span class="keywords m05 f11 color-years scale-1">'+k+'</span>';
								}
								
							});
						};
					}
					
					conHtml+='</p>';
					conHtml+='<div class="f16 color6 xm-sign ui-nowrap-multi  pl-r">“'+d.agent.sign+'”</div></div>';
					if(d.relation_id!=0){
						conHtml+='<p class="f12 color333 grade bgwhite mt1-33 pl-r"><span class="b bold f16 color333">关系</span><span class="relation f14 color666">'+d.confirm_relation+'</span></p>';
					};
					//判断是否派单关系
					if(d.brand_undetermined_data!=''){
						var n = d.brand_undetermined_data;
						conHtml+='<div class="personals bgwhite pb1-33 send_orders">';
						conHtml += '<p class="f16 fline ptb1-4 b keyword bold ">派单品牌</p>';
//						$.each(d.brands, function(m,n) {
							conHtml += '<div class="xm-acting xm-inb mt1-5" brand_ids="'+n.id+'">';
							conHtml += '<img src="'+n.logo+'" class="xm-acting-img mr05"/>';
							conHtml += '<div class="f1 xm-inb "><span class="f16 xm-b color333 mb1 brands_name">'+n.name+'</span>';
							conHtml += '<span class="f12 dark_gray xm-b mb08 color999">行业分类： </span>';
							conHtml += '<span class="f12 dark_gray xm-b mb08 color999">'+n.category_name+'</span><br />';
							conHtml += '<span class="f12 dark_gray xm-b color999">启动资金： </span>';
							conHtml += '<span class="f12 dark_gray xm-b color999">'+parseFloat(n.investment_min)+' ~ '+parseFloat(n.investment_max)+'万</span></div></div>';
//						});
					};
					conHtml+='</div>';
					if(d.score_count>0){
						conHtml+='<div class="bgwhite "><div class=" grade mt1-33 pl-r"><span class="bold f16 b color333">评价评分</span><p class=""><span class="color666 f14">已有'+d.score_count+'人参与评价</span></p></div>';
						conHtml+='<div class="ml1_3 fline"></div>';
						//评分
						//综合评分
						if(d.score.overall_score>0){
							conHtml+='<ul class="star pl-r">';
							conHtml+='<li><span class="f14 color666  mr2">综合评分</span></li>';
							conHtml+='<li class="composite score"><img src="/images/agent/ico_star_gray3.png" class="mr1"/>';
							conHtml+='<img src="/images/agent/ico_star_gray3.png" class="mr1"/>';
							conHtml+='<img src="/images/agent/ico_star_gray3.png" class="mr1"/>';
							conHtml+='<img src="/images/agent/ico_star_gray3.png" class="mr1"/>';	
							conHtml+='<img src="/images/agent/ico_star_gray3.png" class="mr1"/>';	
							conHtml+='<span class="f16  cffa300">'+d.score.overall_score+'.0</span></li>';		
							conHtml+='</ul>';
						};
						//服务态度
						if(d.score.service_score>0){
							conHtml+='<ul class="star pl-r">';
							conHtml+='<li><span class="f14 color666  mr2">服务态度</span></li>';
							conHtml+='<li class="service score"><img src="/images/agent/ico_star_gray3.png" class="mr1"/>';
							conHtml+='<img src="/images/agent/ico_star_gray3.png" class="mr1"/>';
							conHtml+='<img src="/images/agent/ico_star_gray3.png" class="mr1"/>';
							conHtml+='<img src="/images/agent/ico_star_gray3.png" class="mr1"/>';	
							conHtml+='<img src="/images/agent/ico_star_gray3.png" class="mr1"/>';	
							conHtml+='<span class="f16  cffa300">'+d.score.service_score+'.0</span></li>';		
							conHtml+='</ul>';
						};
						//专业能力
						if(d.score.ability_score>0){
							conHtml+='<ul class="star pl-r">';
							conHtml+='<li><span class="f14 color666  mr2">专业能力</span></li>';
							conHtml+='<li class="power score"><img src="/images/agent/ico_star_gray3.png" class="mr1"/>';
							conHtml+='<img src="/images/agent/ico_star_gray3.png" class="mr1"/>';
							conHtml+='<img src="/images/agent/ico_star_gray3.png" class="mr1"/>';
							conHtml+='<img src="/images/agent/ico_star_gray3.png" class="mr1"/>';	
							conHtml+='<img src="/images/agent/ico_star_gray3.png" class="mr1"/>';	
							conHtml+='<span class="f16  cffa300">'+d.score.ability_score+'.0</span></li>';		
							conHtml+='</ul>';
						};
						//响应及时
						if(d.score.timely_score>0){
							conHtml+='<ul class="star pl-r">';
							conHtml+='<li><span class="f14 color666  mr2">专业能力</span></li>';
							conHtml+='<li class="respond score"><img src="/images/agent/ico_star_gray3.png" class="mr1"/>';
							conHtml+='<img src="/images/agent/ico_star_gray3.png" class="mr1"/>';
							conHtml+='<img src="/images/agent/ico_star_gray3.png" class="mr1"/>';
							conHtml+='<img src="/images/agent/ico_star_gray3.png" class="mr1"/>';	
							conHtml+='<img src="/images/agent/ico_star_gray3.png" class="mr1"/>';	
							conHtml+='<span class="f16  cffa300">'+d.score.timely_score+'.0</span></li>';		
							conHtml+='</ul>';
						};
					}
					if(d.brands.length>0){
						conHtml+='<div class="personals  bgwhite pb1-33 agency ">';	
						conHtml += '<p class="f16 fline ptb1-4 b keyword bold ">代理品牌<span class="keyword-num">&nbsp;('+d.brands_count+')</span></p>';
						$.each(d.brands, function(s,t) {
							conHtml += '<div class="xm-acting xm-inb mt1-5" brand_ids="'+t.id+'">';
							conHtml += '<img src="'+t.logo+'" class="xm-acting-img mr05"/>';
							conHtml += '<div class="f1 xm-inb "><span class="f16 xm-b color333 mb1 brands_name">'+t.name+'</span>';
							conHtml += '<span class="f12 dark_gray xm-b mb08 color999">行业分类： </span>';
							conHtml += '<span class="f12 dark_gray xm-b mb08 color999">'+t.category_name+'</span><br />';
							conHtml += '<span class="f12 dark_gray xm-b color999">启动资金： </span>';
							conHtml += '<span class="f12 dark_gray xm-b color999">'+parseFloat(t.investment_min)+'~'+parseFloat(t.investment_max)+'万</span></div></div>';
						});
					}else {
						conHtml += '<div class="personals bgwhite pb1-33 agency mt1-5"><p class="pl-r f16 fline ptb1-4 b keyword bold ">代理品牌</p>';
						conHtml += '<div id="defind"><img src="/images/agent/defind_brand.png" style="height: 16rem;width: 16rem;margin:0 auto;display: inline-block;"/></div>';
					};
					conHtml+='</div>';
					if(d.relation_id!='0'){
						conHtml+='<div class="xm-btn">';
						conHtml+='<span class="mes" nicknames="'+d.agent.nickname+'"><img src="/images/agent/mes3.png" /></span>';
						conHtml+='<span class="tel" tel="'+d.agent.username+'"><img src="/images/agent/tel3.png" /></span>';
						conHtml+='</div>';
					};
					$('#container').html(conHtml);
					composite_f(d.score.overall_score);
					service_f(d.score.service_score);
					power_f(d.score.ability_score);
					respond_f(d.score.timely_score);
					if(isAndroid){
						$('.keywords').css({
							lineHeight:'2rem'
						});
					};
				};
			};
		});
	};
	getdetail(id);
	//综合评分
	function composite_f(score){
		var score = score;
		if(score % 1 == 0) {
			for(var i = 0; i < score; i++) {
				$('.composite img').eq(i).attr('src','/images/agent/ico_star_yellow3.png');
			}
		}else{
			var score = Math.floor(score);
			for(var i = 0; i < score; i++) {
				$('.composite img').eq(i).attr('src','/images/agent/ico_star_yellow3.png');
			}
			$('.composite img').eq(score).attr('src','/images/agent/ico_star_13.png');
		}
	};
	//服务评分
	function service_f(score){
		var score = score;
		if(score % 1 == 0) {
			for(var i = 0; i < score; i++) {
				$('.service img').eq(i).attr('src','/images/agent/ico_star_yellow3.png');
			}
		}else{
			var score = Math.floor(score);
			for(var i = 0; i < score; i++) {
				$('.service img').eq(i).attr('src','/images/agent/ico_star_yellow3.png');
			}
			$('.service img').eq(score).attr('src','/images/agent/ico_star_13.png');
		}
	};
	//专业能力评分
	function power_f(score){
		var score = score;
		if(score % 1 == 0) {
			for(var i = 0; i < score; i++) {
				$('.power img').eq(i).attr('src','/images/agent/ico_star_yellow3.png');
			}
		}else{
			var score = Math.floor(score);
			for(var i = 0; i < score; i++) {
				$('.power img').eq(i).attr('src','/images/agent/ico_star_yellow3.png');
			}
			$('.power img').eq(score).attr('src','/images/agent/ico_star_13.png');
		}
	};
	//响应及时
	function respond_f(score){
		var score = score;
		if(score % 1 == 0) {
			for(var i = 0; i < score; i++) {
				$('.respond img').eq(i).attr('src','/images/agent/ico_star_yellow3.png');
			}
		}else{
			var score = Math.floor(score);
			for(var i = 0; i < score; i++) {
				$('.respond img').eq(i).attr('src','/images/agent/ico_star_yellow3.png');
			}
			$('.respond img').eq(score).attr('src','/images/agent/ico_star_13.png');
		}
	};
	//跳转聊天
	function goChat(uType,uid,nickname) {
		if(isAndroid) {
			javascript: myObject.goChat(uType,uid,nickname);
		}
		else if(isiOS) {
			var data = {
				"uType": uType,
				"uid":uid,
				"nickname":nickname
			}
			window.webkit.messageHandlers.goChat.postMessage(data);
		}
	};
	//电话
	function callNum(tel) {
		if(isAndroid) {
			javascript: myObject.callNum( tel);
		}
		else if(isiOS) {
			var data = {
				"tel":tel
			}
			window.webkit.messageHandlers.callNum.postMessage(data);
		}
	};
	//跳转聊天
	$(document).on('click', '.mes', function() {
		var nickname = $(this).attr('nicknames');
		goChat("c",uid,nickname);
	});
	//电话
	$(document).on('click', '.tel', function() {
		var tel = $(this).attr('tel');
		callNum(tel);
	});
	//跳转品牌详情页
	$(document).on('click','.xm-acting',function(){
		var brand_ids = $(this).attr('brand_ids');
		window.location.href = labUser.path + '/webapp/brand/detail/_v020800?uid='+id+'&id='+brand_ids;
		
	});
	
	function yeardatetime(unix) {
	    var newDate = new Date();
	    newDate.setTime(unix * 1000);
	    var Y = newDate.getFullYear();
	    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
	    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
	    return Y + '/' + M + '/' + D;
	};
	function datetime(unix) {
	    var newDate = new Date();
	    newDate.setTime(unix * 1000);
	    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
	    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
	    var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
	    var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
	    return M + '/' + D + ' ' + h + ':' + m;
	};
})