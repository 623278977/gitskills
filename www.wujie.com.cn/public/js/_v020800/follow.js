//zhangxm
Zepto(function() {
	new FastClick(document.body);
	var args = getQueryStringArgs(),  
		id = args['uid'] || args['id'] || '0', //用户id            
		urlPath = window.location.href,
		origin_mark = args['share_mark'] || 0, //分销参数，分享页用
		code = args['code'] || 0;
	var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
	// 获取详情
	function getdetail(id) {
		var param = {};
		param['uid'] = id;
		if(shareFlag) {
			param['guess'] = 1;
		};
		var url = labUser.api_path + '/user/followed-brands/_v020800';
		ajaxRequest(param, url, function(data) {
			if(data.status){
				var conHtml = '';
				if(data.message){
					if(data.message.brand.length>0){
						$.each(data.message.brand, function(i,v) {
						conHtml += '<div class="fellow bgwhite">';
						conHtml += '<div class="fellow_brand mt1-33" brand_id="'+v.brand_id+'"><div class="fellow_mes"><img src="'+v.logo+'" class="fellow_logo mr1"/>';
						conHtml += '<p class="fellow_mark"><span class="bold color333 f13 mb08">'+v.title+'</span><br />';
						conHtml +='<span class="f11 medium c8a869e">行业分类：</span><span class="f11 medium cffac00 mr2">'+v.category_name+'</span>';
						conHtml += '<span class="f11 medium c8a869e">启动资金：</span><span class="f11 medium cff4d64">'+v.investment_min+' ~ '+v.investment_max+'万</span></p></div>';
						conHtml += '<img src="/images/agent/black_to.png" class="fellow_jump" /></div>';
						conHtml += '<div class="fline"></div>';
						conHtml += '<div class="cont "><div class="cont_l"><img src="/images/agent/downs.png" class="down"/><span class="f12 color666 medium">跟进经纪人：'+v.followed_agents+'人</span></div>';
						conHtml += '<span class="f12 color666 medium">最早跟进时间：'+yeardate(v.created_at)+'</span></div>';
						conHtml += '<div class="fline"></div>';
						if(v.agent_list.length>0){
							conHtml+='<div class="pl1 none fel_agent">'
							$.each(v.agent_list, function(j,k) {
								conHtml+='<div class="fellow_agent fline" fellow_agent_id="'+k.id+'">';
								conHtml+='<img src="'+k.avatar+'" class="avater mr1"/>';
								conHtml+='<div class=""><p class="name_gen mb05">';
								if(k.is_public_realname==0){
									conHtml += '<span class="f16 bold b color333 mr05">'+k.nickname+'</span>';
								}else {
									conHtml += '<span class="f16 bold b color333 mr05">'+k.realname+'</span>';
								}
								
								if(k.gender=='0'){
									conHtml+='<img src="/images/agent/girl.png" class="gender"/></p>';
								}else if(k.gender=='1'){
									conHtml+='<img src="/images/agent/boy.png" class="gender"/></p>';
								};
								conHtml += '<span class="f14 color666 medium">'+k.city+'</span></div></div>';
							});
							conHtml+='</div>';
						}
						conHtml+='</div>';
					});
					}else {
						conHtml += '<div class="define"><img src="/images/agent/no_brand.png" class="no_brand"/></div>';
						
					}
					
					
				};
				$('#container').html(conHtml);
			}
		});
	};
	getdetail(id);
	//点击显示跟进经纪人
	$(document).on('click','.cont_l',function(){
		$(this).parent().siblings('.fel_agent').toggleClass('none');
	});
	//跳转对应得经纪人详情
	$(document).on('click','.fellow_agent',function(){
		var fe_agent_id = $(this).attr('fellow_agent_id');
		window.location.href = labUser.path + '/webapp/myagent/agent_detail/_v020800?agent_id='+fe_agent_id+'&uid='+id+'&customer_id='+id;
	});
	//跳转品牌id
	$(document).on('click','.fellow_brand',function(){
		var brand_id = $(this).attr('brand_id');
		window.location.href = labUser.path + '/webapp/brand/detail/_v020800?uid='+id+'&id='+brand_id;
	})
	
	//时间戳转换
	function yeardate(unix) {
		var newDate = new Date();
		newDate.setTime(unix * 1000);
		var Y = newDate.getFullYear();
		var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
		var D = newDate.getDate() < 10 ? ('0' + newDate.getDate()) : newDate.getDate();
		return Y + '/' + M + '/' + D;
	};
	/*时间戳转换成月日时分*/
	function unix_to_mdhm(unix) {
		var newDate = new Date();
		newDate.setTime(unix * 1000);
		var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
		var D = newDate.getDate() < 10 ? ('0' + newDate.getDate()) : newDate.getDate();
		var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
		var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
		return M + '月' + D + '日 ' + h + ':' + m;
	};

});