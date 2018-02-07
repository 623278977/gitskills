Zepto(function() {
	new FastClick(document.body);
	$('body').css('background', '#f2f2f2');
	var args = getQueryStringArgs(),
		urlPath = window.location.href,
		page=1,
    	pagesize=99;
	var shareFlag = urlPath.indexOf('/share') > 0 ? true : false;
	//获取导航
	function getdetail() {
		var param = {};
		param['page_size'] = pagesize;
		var url = labUser.agent_path + '/workspace/poster-list/_v010100';
		ajaxRequest(param, url, function(data) {
			if(data.status){
				var titleHtml = '';
				if(data.message!=''){
					$.each(data.message.keywords, function(i,v) {
						if(i==0){
							$('.getmore').attr('keywords_id',v.id);
							list(page,v.id);
							titleHtml+='<span class="choosen f13" keywords_id="'+v.id+'">'+v.contents+'</span>';
						}else {
							titleHtml+='<span keywords_id="'+v.id+'" class="f13">'+v.contents+'</span>';
						}
						
					});
					if(data.message.banner!=''){
						$('.banner').removeClass('none');
						$('.banner img').attr('src',data.message.banner);
					}else {
						$('.banner').addClass('none');
					};
					
				}
			};
			$('.poster_nav').html(titleHtml);
			

		});
	}
	getdetail();
	//获取列表
	var dataArr=[];
	function list(page,keywords_id){
		var param={};
            param['page']=page;
            param['page_size']=pagesize;
            param['keywords_id']=keywords_id;
        var url = labUser.agent_path + '/workspace/poster-list/_v010100';
        ajaxRequest(param, url, function(data) {
			if(data.status){
				var listHtml = '';
				if(data.message!=''){
					dataArr.push(data.message.data);
					console.log(dataArr);
					$.each(data.message.data, function(m,n) {
//						var dataJson={};
//						dataJson['id']=n.id;
//						dataJson['image']=n.image;
//						console.log(dataJson);
//						dataArr.push(JSON.stringify(dataJson));
						listHtml+='<p class="poster_detail" poster_id="'+n.id+'"><img src="'+n.image+'" alt="" class="poster_img" /><span class="font">'+n.title+'</span></p>';
					});
				}
			}
			
			if(param.page == 1) {
				$('.poster_list').html(listHtml);
				if(data.message.data.length == 0) {
					$('.poster_list').addClass('none');
					$('.getmore').addClass('none');
				}
				if(data.message.data.length < 99) {
					$('.getmore').text('没有更多了…').attr('disabled',true);
					$('.h_gif').attr('src','');
				}else{
                    $('.getmore').text('点击加载更多').removeAttr('disabled');
                    $('.h_gif').attr('src','/images/agent/h.gif');
                } 
			} else {
				$('.poster_list').append(listHtml);
				if(data.message.data.length < 99) {
					$('.getmore').text('没有更多了…').attr('disabled',true);
					$('.h_gif').attr('src','');
					return false;
				}
			};

		});    
	}
//$('.getmore').on('click',function(){
//	var keywords_id = $(this).attr('keywords_id');
//   page++;  
//   list(page,keywords_id); 
//});
var timers = null;
$(window).scroll(function() {
    //当时滚动条离底部0时开始加载下一页的内容
    if (($(window).height() + $(window).scrollTop() + 0) >= $(document).height()) {
        clearTimeout(timers);//timers 在外部初次定义为null
        timers = setTimeout(function() {
            page++;
            
            list(page,keywords_id);//加载列表的函数
            console.log("第" + page + "页");
        }, 300);
    }
});
//导航栏切换
$(document).on('click','.poster_nav span',function(){
	dataArr=[];
	var keywords_id = $(this).attr('keywords_id');
	$(this).addClass('choosen');
	$(this).siblings().removeClass('choosen');
	list(1,keywords_id);
	
});
//点击跳转海报详情
$(document).on('click','.poster_detail',function(){
	var poster_id = $(this).attr('poster_id');
//	var	data = $(this).attr('data');
	var dataJson = JSON.stringify(dataArr[0]);
	pasterDdtail(poster_id,dataJson);
	console.log(dataJson);
});
//poster_id:当前海报id   data:当前类型下的海报组（数组）
function pasterDdtail(poster_id,data){
//	console.log(data);
	if (isAndroid) {
        javascript:myObject.pasterDdtail(poster_id,data);
    } else if (isiOS) {
        var message = {
		    method : 'pasterDdtail',
		    params : {
		    	"poster_id":poster_id,
		    	"data":data
		    }
		}; 
        window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
    }
}

});