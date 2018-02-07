Zepto(function(){
	new FastClick(document.body);
	var args=getQueryStringArgs(),
	    agent_id = args['agent_id'] || '0',
		urlPath = window.location.href,
		page=1,
    	page_size=9;
	var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
	function getdetail(agent_id,page){
		var param = {};
			param['agent_id']=agent_id;
			param['page']=page;
		var url = labUser.agent_path + '/comment/reply-list/_v010005';
		ajaxRequest(param,url,function(data){
			if(data.status){
				var conHtml = '';
				if(data.message.length>0){
					$.each(data.message, function(i,v) {
						var created_at = unix_to_mdhm(v.created_at);
						conHtml+='<a href="'+v.url+'">'
						conHtml+='<div class="wrap pt2 pl1 " comment_id="'+v.comment_id+'">';
						conHtml+='<div class="message mb1">';
						conHtml+='<div class="message_l">';
						conHtml+='<p class="naver mr1"><img src="'+v.avatar+'"/></p>';
						conHtml+='<p class="name">';
						conHtml+='<span class="f13 c2873ff">'+v.nickname+'</span>&nbsp;';
						if(v.type=='zan'){
							conHtml+='<span class="f13 color666 praise">赞了你的评论</span>';
						}else {
							conHtml+='<span class="f13 color666 praise">回复了你的评论</span>';
						}
						conHtml+='</p></div>';
						conHtml+='<p class="date f11 color999 mr2">'+created_at+'</p>';
						conHtml+='</div>';
						if(v.type=='zan'){
							conHtml+='<div class="fline ml4"><span class="f15 color333 pb2 comment_text mr2 ">'+v.comment+'</span></div>';
						}else{
							conHtml+='<div class="fline ml4">';
							conHtml+='<span class="f15 color333 pb2 comment_text mr2">'+v.reply+'</span>';
							conHtml+='<p class="reply mr2 mb2">';
							conHtml+='<span class="reply_name f13 c2873ff mb1">'+v.my_nickname+'：</span>';
							conHtml+='<span class="reply_text ui-nowrap-multi f13 color666">'+v.comment+'</span>';
							conHtml+='</p>';
							conHtml+='</div>';
						}
						conHtml+='</div></a>';
					});
				}else {
					$('.define').removeClass('none');
				};
				
			}else {
				$('.define').removeClass('none');
			};
			
			if(param.page == 1) {
				$('#container').html(conHtml);
				if(data.message.length == 0) {
					$('.walkman_list').addClass('none');
					$('.getmore').addClass('none');
				}
				if(data.message.length>9){
					$('.getmore').removeClass('none');
				}else {
					$('.getmore').text('没有更多了…').attr('disabled',true);
					$('.h_gif').attr('src','');
				} 
			} else {
				$('#container').append(conHtml);
				if(data.message.length < 9) {
					$('.getmore').text('没有更多了…').attr('disabled',true);
					$('.h_gif').attr('src','');
					return false;
				}
			};
			
		})
	}
	getdetail(agent_id,1);
//$(document).on('click','.getmore',function(){
//	page++;  
//  getdetail(agent_id,page);
//});
var timers = null;
$(window).scroll(function() {
    //当时滚动条离底部0时开始加载下一页的内容
    if (($(window).height() + $(window).scrollTop() + 0) >= $(document).height()) {
        clearTimeout(timers);//timers 在外部初次定义为null
        timers = setTimeout(function() {
            page++;  
    		getdetail(agent_id,page);
            console.log("第" + page + "页");
        }, 300);
    }
});
//$(document).on('click','.wrap',function(){
//	var urls = $(this).attr('url');
//	console.log(urls);
//	window.location.href = urls;
//});
/*时间戳转换成月日*/
function unix_to_mdhm(unix) {
    var newDate = new Date();
    newDate.setTime(unix * 1000);
    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
    return M + '月' + D + '日 ';
};	
})
