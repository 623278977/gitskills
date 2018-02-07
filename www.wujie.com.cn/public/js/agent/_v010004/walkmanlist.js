Zepto(function() {
	new FastClick(document.body);
	$('body').css('background', '#f2f2f2');
	var args = getQueryStringArgs(),
		urlPath = window.location.href,
		page=1,
    	pagesize=9;
	var shareFlag = urlPath.indexOf('/share') > 0 ? true : false;
	//获取详情
	function getdetail() {
		var param = {};
//		param['page_size'] = pagesize;
		var url = labUser.agent_path + '/talking_skill/list/_v010004';
		ajaxRequest(param, url, function(data) {
			if(data.status) {
				var conHtml = '';
				var listHtml = '';
				if(data.message) {
					if(data.message.banner != '') {
						conHtml += '<div class="walk_header mb1-2" link_url="'+data.message.banner.link_url+'">';
						conHtml += '<img src="' + data.message.banner.image + '" alt="" class="">';
						conHtml += '</div>';
					}
					conHtml += '<ul class="walk_list mt1-2 pl1-5">';
					$.each(data.message.list, function(i, v) {
//						var audio_len = timeStamp(v.audio_len);
						conHtml += '<li class="walkman_list" id="'+v.id+'" audio_len="'+v.audio_len+'">';
						conHtml += '<p>';
						conHtml += '<img src="/images/agent/walkman.png" alt="">';
						conHtml += '</p>';
						conHtml += '<div class="list_item fline">';
						conHtml += '<p style="overflow: hidden; white-space: nowrap; text-overflow: ellipsis;width:27rem;">' + v.subject + '</p>';
						conHtml += '<span>' + v.view + '人听过</span>';
						conHtml += '<span>时长' + v.audio_len + '</span>';
						conHtml += '<span>' + v.audio_size + '</span>';
						conHtml += '</div>';
						conHtml += '<div class="fline"></div>';
						conHtml += '</li>';
					});
					conHtml += '</ul>';
				}
				if(data.message.list.length>9){
					$('.getmore').removeClass('none');
				}
			};
			$('#container').html(conHtml);
			

		});
	}
	getdetail();
	
	function list(page){
		var param={};
            param['page']=page;
//          param['page_size']=pagesize;
        var url = labUser.agent_path + '/talking_skill/list/_v010004';
        ajaxRequest(param, url, function(data) {
			if(data.status) {
				var listHtml = '';
				if(data.message) {
					$.each(data.message.list, function(i, v) {
//						var audio_len = timeStamp(v.audio_len);
						listHtml += '<li class="walkman_list" id="'+v.id+'" audio_len="'+v.audio_len+'">';
						listHtml += '<p>';
						listHtml += '<img src="/images/agent/walkman.png" alt="">';
						listHtml += '</p>';
						listHtml += '<div class="list_item fline">';
						listHtml += '<p style="overflow: hidden; white-space: nowrap; text-overflow: ellipsis;width:25rem;">' + v.subject + '</p>';
						listHtml += '<span>' + v.view + '人听过</span>';
						listHtml += '<span>时长' + v.audio_len + '</span>';
						listHtml += '<span>' + v.audio_size + '</span>';
						listHtml += '</div>';
						listHtml += '<div class="fline"></div>';
						listHtml += '</li>';
					});
				}
			};
			if(param.page == 1) {
				$('.walk_list').html(listHtml);
				if(data.message.list.length == 0) {
					$('.walkman_list').addClass('none');
					$('.getmore').addClass('none');
				}
				if(data.message.list.length < 9) {
					$('.getmore').text('没有更多了…').attr('disabled',true);
					$('.h_gif').attr('src','');
				}else{
                    $('.getmore').text('正在加载...').removeAttr('disabled');
                } 
			} else {
				$('.walk_list').append(listHtml);
				if(data.message.list.length < 9) {
					$('.getmore').text('没有更多了…').attr('disabled',true);
					$('.h_gif').attr('src','');
					return false;
				}
			};

		});    
	}
//$('.getmore').on('click',function(){
//   page++;  
//   list(page,pagesize); 
// })
var timers = null;
$(window).scroll(function() {
    //当时滚动条离底部0时开始加载下一页的内容
    if (($(window).height() + $(window).scrollTop() + 0) >= $(document).height()) {
        clearTimeout(timers);//timers 在外部初次定义为null
        timers = setTimeout(function() {
            page++;
            list(page);//加载列表的函数
            console.log("第" + page + "页");
        }, 300);
    }
});  
	//点击跳转详情
	$(document).on('click', '.walkman_list', function() {
		var walkmanId = $(this).attr('id');
		var audio_len = $(this).attr('audio_len');
		onAgentEvent('audio','',{'type':'audio','id':walkmanId,'position':'2'})
		window.location.href = labUser.path + '/webapp/agent/walkman/detail/_v010004?id='+walkmanId+'&audio_len='+audio_len;
	});
	//点击banner跳转
	$(document).on('click', '.walk_header', function() {
		var link_url = $(this).attr('link_url');
		if(link_url!=''){
			window.location.href = link_url;
		}
		
	});
 	
});