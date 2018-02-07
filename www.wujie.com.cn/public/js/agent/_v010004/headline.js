
Zepto(function(){
	new FastClick(document.body);
	var args=getQueryStringArgs(),
		id = args['id'] || '0',
        agent_id = args['agent_id'] || '0',
        section_id = args['section_id'],
        brand_id = args['brand_id'],
        urlPath = window.location.href;
    var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
    
    function apply_status(agent_id,brand_id,type,post_id){
    	var param={};
    		param['agent_id'] = agent_id;
    		param['brand_id'] = brand_id;
    		param['type'] = type;
    		param['post_id'] = post_id;
    	var url =  labUser.agent_path + '/brand/apply-status/_v010000';
    	ajaxRequest(param,url,function(){});
    }
    
    
    function getdetail(id,agent_id){
    	var param={};
			param['id']=id;
            param['agent_id']=agent_id;
            if(shareFlag){
                param['guess']=1;
            }       
		var	url=labUser.agent_path + '/news/detail/_v010004';
		ajaxRequest(param,url,function(data){
			if(data.status){
				var conHtml = '';
				if(data.message){
					$('.section_title').text(section_id);
					$('.ui-fixed-button').attr('brand_id',data.message.brand_id);
					$('.headline_title').text(data.message.name);
					conHtml += '<div class="content">'+data.message.detail+'</div>';
          getpict('.content');
					if(data.message.is_complete==1){
						apply_status(agent_id,brand_id,'news',id);//调用打卡小接口
						$('.again_xuexi').removeClass('none');
					}
					else {
						$('.tipsfor').removeClass('none');
						$('.triangle').removeClass('none');
						$('.ui-fixed-button').removeClass('none');
					}
				}
			}
			$('.detail').html(conHtml);
		})
    };
    getdetail(id,agent_id);
    setTimeout(function(){
      $('.tipsfor,.triangle').addClass('none');
    },5000);
    //点击跳转小测试
    $(document).on('click','.ui-fixed-button',function(){
    	var brand_id = $(this).attr('brand_id');
    	window.location.href = labUser.path+'webapp/agent/exam/detail/_v010004?id='+brand_id+'&type=2&type_id='+id+'&agent_id='+agent_id;
    });

})
