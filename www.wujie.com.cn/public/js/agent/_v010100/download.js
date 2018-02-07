//zhangxm
Zepto(function(){
	new FastClick(document.body);
	localStorage.setItem("need-refresh", true);
	console.log(localStorage );
	var args=getQueryStringArgs(),
        customer_id = args['customer_id'] || '0',
        has_register = args['has_register'],
        is_self_register = args['is_self_register'],
		urlPath = window.location.href,
        code = args['code'] || 0;
	var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
	function getdetail(customer_id){
		var param = {};
		param['uid'] = customer_id;
		var url = labUser.agent_path + '/user/register-customer-result/_v010100';
		ajaxRequest(param,url,function(data){
			if(data.status){
				$('.telNum').text(data.message.username);
				$('.noget').attr('redpacket_id',data.message.redpacket_id);
				$('.amount').text(parseInt(data.message.amount));
				getHongbao(data.message.redpacket_id,customer_id);
				$('.wrap').removeClass('none');
			};
		});
	};
	getdetail(customer_id);
	//点击领取红包
	$(document).on('click','.noget',function(){
		var redpacket_id = $(this).attr('redpacket_id');
		getHongbao(1,customer_id);
	});
	//领取红包
	function getHongbao(redpacket_id,customer_id){
		console.log(111)
		var param = {};
		param['redpacket_ids']=redpacket_id;
		param['uid']=customer_id;
		var url = labUser.agent_path + '/user/custom-receive-redpacket/_v010100';
		ajaxRequest(param,url,function(data){
			if(data.status){
				if(data.message){
					tips(data.message);
					$('.old_yiling').removeClass('none');
					// setTimeout(function(){
					// 	$('.ticket').removeClass('noget').addClass('get');	
					// },1500);
				}
			}else if(data.message=='has_draw'){
				$('.old_yiling').removeClass('none');
				tips('您已领取过了');
			}else {
				$('.bcg').removeClass('none')
				tips(data.message);
			}
		});
	};
	//点击跳转下载页
	$(document).on('click','.downbutton',function(){
		if (isiOS) {
            oppenIos();
            window.location.href = 'https://itunes.apple.com/app/id981501194';
        }
        else if (isAndroid) {
            openAndroid();
            window.location.href = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.tyrbl.wujiesq';
        }
	});
//打开本地--Android
function openAndroid(){
    var strPath = window.location.pathname;
    var strParam = window.location.search.replace(/is_share=1/g, '');
    var appurl = strPath + strParam;
    window.location.href = 'openagent://welcome' + appurl;
}
function oppenIos(){
    var strPath = window.location.pathname.substring(1);
    var strParam = window.location.search;
    var appurl = strPath + strParam;
    var share = '&is_share';
    var appurl2 = appurl.substring(0, appurl.indexOf(share));
    window.location.href = 'openagent://' + appurl2;
};	
});
