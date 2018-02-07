//zhangxm
Zepto(function() {
	new FastClick(document.body);
	var args = getQueryStringArgs(),
		id = args['customer_id'] || '0', //被查看的经纪人id
		uid = args['agent_id'] || '0', //登录的经纪人id              
		urlPath = window.location.href,
		origin_mark = args['share_mark'] || 0, //分销参数，分享页用
		code = args['code'] || 0;
	var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
	// 获取详情
	function getdetail(id, uid) {
		var param = {};
		param['customer_id'] = id;
		param['agent_id'] = uid;
		if(shareFlag) {
			param['guess'] = 1;
		};
		var url = labUser.agent_path + '/customer/detail-infos/_v010000';
		ajaxRequest(param, url, function(data) {
			if(data.status) {
				var conHtml = '';
				var k = data.message;
				if(data.message) {
					var created_at = unix_to_yeardate(data.message.created_at);
					conHtml += '<div class="datum bcg-f"><img src="'+k.avatar+'" class="photo"/>';
					conHtml += '<div class="datum-l "><span class="f18 b text_black bold">'+k.realname+'</span><br />';
					conHtml += '<span class="dark_gray f13 mt05">昵称：'+k.nickname+'</span><br />';
					conHtml += '<span class="dark_gray f13 mt05">备注：'+k.remark+'</span><span class="ml2 dark_gray f13 c2873ff mt05 amend">修改</span><br />';
					conHtml += '<span class="dark_gray f14 mt05">联系电话：'+k.relation_tel+'</span></div></div>';
					conHtml += '<div class="relation bcg-f "><span class="f15 color3 ">关系</span><p><span class="color999 f12 ">'+k.relation+'</p></div>';
					conHtml += '<div class="content bcg-f"><p class="fline"><span class="color3 f15">地区</span><span class="color999 f12">'+k.city+'</span></p>';
					conHtml +='<p class="fline"><span class="color3 f15">学历</span><span class="color999 f12">'+k.diploma+'</span></p>';
					conHtml += '<p class="fline"><span class="color3 f15">职位</span><span class="color999 f12">'+k.positions+'</span></p>';
					conHtml += '<p class="fline"><span class="color3 f15">收入</span><span class="color999 f12">'+k.earning+'</span></p>';
					conHtml += '<p class="fline"><span class="color3 f15">感兴趣行业</span><span class="color999 f12">'+k.interest_industries+'</span></p>';
					conHtml += '<p class="fline"><span class="color3 f15">投资意向</span><span class="color999 f12">'+k.invest_intention+'</span></p>';
					conHtml += '<p class="fline"><span class="color3 f15">投资额度</span><span class="color999 f12">'+k.invest_quota+'</span></p></div>';
					conHtml += '<div class="content bcg-f"><p class="fline"><span class="color3 f15">邀请人</span><span class="color999 f12">'+k.invite_agent+'</span></p>';
					conHtml += '<p class="fline"><span class="color3 f15">注册时间</span><span class="color999 f12">'+created_at+'</span></p></div>';
				}
				$('#container').html(conHtml);
			};
			
		});
		
	};
	getdetail(id, uid);
	//时间戳转换
	function unix_to_yeardate(unix) {
    var newDate = new Date();
    newDate.setTime(unix * 1000);
    var Y = newDate.getFullYear();
    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
    return Y + '年' + M + '月' + D + '日';
};
	//修改跳转编辑客户页面
	$(document).on('click','.amend',function(){
		window.location.href = labUser.path + '/webapp/agent/customer/remark?agent_id='+uid+'&customer_id='+id;
	});
});