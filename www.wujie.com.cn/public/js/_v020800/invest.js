Zepto(function(){
	//zhangxm
	new FastClick(document.body);
	var args = getQueryStringArgs(),
		id = args['uid'] || '0',
		urlPath = window.location.href;
	function getdetail(id){
		var param = {};
		param['investor_id'] = id;
		var url = labUser.api_path + '/user/detail/_v020800';
		ajaxRequest(param,url,function(data){
			
			if(data.status){
				if(data.message){
					var conHtml = '';
					var d = data.message;
					
					$('.avatar').attr('src',d.avatar);
					$('.nickname').html(d.nickname);
					
					if(d.gender == '女'){
						$('.gender').removeClass('none');
						$('.gender').attr('src','/images/agent/girl.png');
					}if(d.gender == '男'){
						$('.gender').removeClass('none');
						$('.gender').attr('src','/images/agent/boy.png');
					}
					$('.city').html(d.city);
					$('.last_login').html(datetime(d.last_login));
					$.each(d.keywords, function(i,v) {
						if(i!=3){
							conHtml +='<span class="keywords m05 f11 color-years scale-1">'+v+'</span>';
						}else if(i==3){
							$.each(v, function(j,k) {
								conHtml += '<span class="keywords m05 f11 color-years scale-1">'+k+'</span>';
							});
						};
						$('.keyword').html(conHtml);
					});
					$('.sign').html('"'+d.sign+'"');
					$('.zone').html(d.zone);
					$('.diploma').html(d.diploma);
					$('.position').html(d.position);
					$('.earning').html(d.earning+'元');
					$('.interested_industry').html(d.Interested_industry);
					if(d.invest_intention==0){
						$('.invest_intention').html('未知');
					}else if(d.invest_intention==1) {
						$('.invest_intention').html('近期有投资意向');
					}else if (d.invest_intention==2){
						$('.invest_intention').html('近期没有投资意向');
					}else if (d.invest_intention==3){
						$('.invest_intention').html('以观望为主');
					};
					$('.invest_quota').html(d.invest_quota);
					$('.invitor').html(d.invitor);
					$('.created_at').html(yeardatetime(d.created_at));
					if(isAndroid){
						$('.keywords').css({
							lineHeight:'2rem'
						});
					};
				};
			};
		})
	};
	getdetail(id);
	function yeardatetime(unix) {
    var newDate = new Date();
    newDate.setTime(unix * 1000);
    var Y = newDate.getFullYear();
    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
    
    return Y + '/' + M + '/' + D;
}
	function datetime(unix) {
    var newDate = new Date();
    newDate.setTime(unix * 1000);
    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
    var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
    var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
    return M + '/' + D + ' ' + h + ':' + m;
}
});