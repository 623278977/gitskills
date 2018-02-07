Zepto(function(){
	new FastClick(document.body);
	$('body').css('background','#f2f2f2');
	var args = getQueryStringArgs(),
		agent_id = args['agent_id'] || 0,
		urlPath = window.location.href;
	var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
	//获取详情
	function getdetail(){
		var param = {};
		
		var url = labUser.agent_path + '/knowledge/types/_v010004';
		ajaxRequest(param,url,function(data){
			var conHtml = '';
			if(data.status){
				if(data.message){
					if(data.message.banner!=''){
						if(data.message.banner.image!=''){
							conHtml+='<div class="tree_header mb1-2" link_url="'+data.message.banner.link_url+'">';
							conHtml+='<img src="'+data.message.banner.image+'" alt="" class="">';
							conHtml+='</div>'; 
						}
					}
					conHtml+='<div class="tree_nav bgwhite">';
					$.each(data.message.list, function(i,v) {
						conHtml+='<div class="tree_item" id="'+v.id+'" is_num="'+v.contents+'">';
						conHtml+='<img src="'+v.icon+'" alt="">';
						conHtml+='<p class="f11 color999">'+v.contents+'</p>';
						conHtml+='</div>';
					});
					conHtml+='</div>';
				}
			};
			$('#container').html(conHtml);
		})
	}
	getdetail();
	//点击banner跳转
	$(document).on('click','.tree_header',function(){
		var link_url =$(this).attr('link_url');
		window.location.href = link_url;
	});
	//点击跳转详情
	$(document).on('click','.tree_item',function(){
		var knowledge_id = $(this).attr('id');
		var is_num = $(this).attr('is_num');
		window.location.href = labUser.path + 'webapp/agent/list/detail/_v010004?id='+knowledge_id+'&agent_id='+agent_id+'&is_num='+is_num;
	});

});
