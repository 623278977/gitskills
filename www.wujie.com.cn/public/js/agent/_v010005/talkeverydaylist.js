Zepto(function(){
	new FastClick(document.body);
	var args=getQueryStringArgs(),
		urlPath = window.location.href,
		agent_id = args['agent_id'] || '0',
		page=1,
    	page_size=9;
	var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
	function getdetail(page){
		var param = {};
			param['page']=page;
		var url = labUser.agent_path + '/talking_exercise/list/_v010100';
		ajaxRequest(param,url,function(data){
			if(data.status){
				var conHtml = '';
				if(data.message.length>0){
					$.each(data.message, function(i,v) {
						var created_at = unix_to_mdhm(v.created_at);
						conHtml+='<li class="talkeveryday_list fline" id="'+v.id+'">';
						conHtml+='<div class="list_item">';
						conHtml+='<img src="/images/agent/talkeveryday.png"/>';
						conHtml+='<span class="ml1 color333 title f15">'+v.title+'</span>';
						conHtml+='</div>';
						conHtml+='<img src="/images/agent/black_to.png" class="mr2"/>';
						conHtml+='</li>';
					});
				}else {
					$('.define').removeClass('none');
				};
				
			}else {
				$('.define').removeClass('none');
			};
			
			if(param.page == 1) {
				$('.talkeveryday').html(conHtml);
				if(data.message.length == 0) {
					$('.talkeveryday_list').addClass('none');
					$('.getmore').addClass('none');
				}
				if(data.message.length>9){
					$('.getmore').removeClass('none');
				}else {
					$('.getmore').text('没有更多了…').attr('disabled',true);
					$('.h_gif').attr('src','');
				} 
			} else {
				$('.talkeveryday').append(conHtml);
				if(data.message.length < 9) {
					$('.getmore').text('没有更多了…').attr('disabled',true);
					$('.h_gif').attr('src','');
					return false;
				}
			};
			
		})
	}
	getdetail(1);
//$(document).on('click','.getmore',function(){
//	page++;  
//  getdetail(page);
//});
var timers = null;
$(window).scroll(function() {
    //当时滚动条离底部0时开始加载下一页的内容
    if (($(window).height() + $(window).scrollTop() + 0) >= $(document).height()) {
        clearTimeout(timers);//timers 在外部初次定义为null
        timers = setTimeout(function() {
            page++;
            
            getdetail(page);//加载列表的函数
            console.log("第" + page + "页");
        }, 300);
    }
});

//点击跳转话术详情
$(document).on('click','.talkeveryday_list',function(){
	var id = $(this).attr('id');
	onAgentEvent('talking_exercise','',{'type':'talking_exercise','id':id,'position':'1'})
	window.location.href = labUser.path+'/webapp/agent/talkeveryday/particulars/_v010100?id='+id;
})
/*时间戳转换成月日*/
function unix_to_mdhm(unix) {
    var newDate = new Date();
    newDate.setTime(unix * 1000);
    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
    return M + '月' + D + '日 ';
};	
})
