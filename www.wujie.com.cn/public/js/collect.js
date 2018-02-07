var Collect;
Collect = $.extend({},{
	//收藏
	getCollect:function(id,model,type){
		var param = {};
		if(labUser.uid =='0'){
			var args = getQueryStringArgs(),
				_uid = args['uid'] || '0';
			param["uid"] = _uid;
		}
		else{
			param["uid"] = labUser.uid;
		}
		param["post_id"] = id;
		param["model"] = model;
		param['type'] = type;
		var url = labUser.api_path+'/favorite/deal';
		ajaxRequest(param,url,function(data){
			if(data.status){
				if(param['type'] == "1"){
					$(".isFavorite").attr("value",1);
					$('.collectbtn').text('取消收藏');
				}else{
					$(".isFavorite").attr("value",0);
					$('.collectbtn').text('收藏');
				}
			}
		});
	}
})