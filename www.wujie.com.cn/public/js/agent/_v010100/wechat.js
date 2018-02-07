Zepto(function(){


		new FastClick(document.body);
		var args=getQueryStringArgs(),
	        agent_id = args['agent_id'];
	    var Wechat={
	    	   init:function(){
                               var param={};
                                   param['agent_id']=agent_id;
	    	   	               var url=labUser.agent_path+'/workspace/we-chat-list/_v010100';
	    	   	               ajaxRequest(param,url,function(data){
                                   if(data.status){
                                               Wechat.data(data.message);
                                               Wechat.getlist(data.message);
                                               Wechat.getothers();
                                               $('.containerBox').removeClass('none');
                                   }else{

                                   }
	    	   	               })

	    	   },
	    	   data:function(obj){
	    	   	                $(document).ready(function(){$('title').text('微信营销')});
                                $('.ui-pictcon').css('background','url('+obj.banner+') no-repeat center');
                                $('.ui-pict').attr('src',(obj.avatar?obj.avatar:'/images/default/avator-m.png'));
                                if(obj.keywords!=''){
                                	for(var i=0; i<obj.keywords.length;i++){
                                		var html='';
                                		    html+='<div data-id="'+obj.keywords[i].id+'">'+obj.keywords[i].contents+'</div>';
                                		$('.flex-around').append(html);
                                	}
                                }else{
                                	$('.flex-around').addClass('none');
                                	$('.ui-bg').css('background','#f2f2f2')
                                }
                                // $('.flex-around>div').eq(0).addClass('choosen');
                                
	    	   },
	    	   getothers:function(){
		    	   	         	$(document).on('click','.flex-around>div',function(){
		    	   	         	  var id=$(this).data('id');
		    	   	         	   $('footer').empty();  
		    	   	         	   Wechat.gettype(agent_id,id);
						          $(this).addClass('choosen').siblings('div').removeClass('choosen');
						       })
	    	   },
	    	   getlist:function(obj){
                                for(var i=0;i<obj.data.length;i++){
                                    var html='';
                                        html+='<div class="go" data-id="'+obj.data[i].id+'"><ul class="con-list">\
									                <li><img class="ui-picture" src="'+(obj.data[i].teacher_avatar?obj.data[i].teacher_avatar:'/images/default/avator-m.png')+'"/></li>\
									                <li>\
									                    <p class="f13 b c2873ff">'+obj.data[i].teacher+'</p>\
									                    <p class="f13 b color333 ">'+obj.data[i].summary+'</p>\
									                    <ul class="newcontain">\
									                         <li><img  src="'+obj.data[i].image+'"/></li>\
									                    </ul>\
									                    <div class="clear" style="width:100%;height:0.5rem;"></div>\
									                    <p  class=" f11 color666 ">\
									                       已转发'+obj.data[i].share_count+'次<button class="r  f12 shareout" data-id="'+obj.data[i].id+'" data-img="'+obj.data[i].share_image+'" data-con="'+obj.data[i].summary+'" data-title ="'+obj.data[i].title+'" data-sum="'+obj.data[i].share_summary+'" >立即转发</button> \
									                    </p>\
									                </li>\
									           </ul>\
									           <div class="clear fline style"></div></div>';
									$('footer').append(html);
                                }
	    	   },
	    	   gettype:function(agent_id,keywords){
	    	   				         var param={};
	    	   				             param['agent_id']=agent_id;
	    	   				             param['keywords']=keywords;
	    	   				         var url=labUser.agent_path+'/workspace/we-chat-list/_v010100';
	    	   				         ajaxRequest(param,url,function(data){
                                   if(data.status){  
                                                  Wechat.getlist(data.message);  
                                   }
	    	   	               })
	    	   }
	    }
        Wechat.init();
        
		// function shareout(event){
  //       	if(event.stopPropagation){
  //       		event.stopPropagation()
  //       	}else{
  //       		window.event.cancelBubble = true;
  //       	}
        		
  //       		 // || window.event.stopPropagation;
  //   			alert(1);
  //   			// return false;
  //       	    var id=$(this).data('id'),
  //       	    	img = $(this).attr('data-img'),
  //       	    	title = $(this).attr('data-title'),
  //       	    	content = $(this).attr('data-sum') || $(this).attr('data-con');
  //       	    	console.log(id);
  //       	    	console.log(img);
  //       	    	console.log(title);
  //       	    	console.log(content);
  //       	        onAgentEvent('we_chat','',{'type':'we_chat','id':id,'userId':agent_id,'position':'4'})
  //       	        showShare(id,title,img,content);
  //      	}   

       	$('footer').on('click','.shareout',function(event){
        		event.stopPropagation()
        	    var id=$(this).data('id'),
        	    	img = $(this).attr('data-img') || labUser.path + 'images/agent-share-logo.png',
        	    	title = $(this).attr('data-title'),
        	    	content = $(this).attr('data-sum') || $(this).attr('data-con');
        	        onAgentEvent('we_chat','',{'type':'we_chat','id':id,'userId':agent_id,'position':'4'})
        	        showShare(id,title,img,content);
       	})  
        	     

        $(document).on('click','.go',function(event){
        	var id=$(this).data('id');
        	window.location.href=labUser.path+'webapp/agent/wechatdetail/detail/_v010100?id='+id+'&agent_id='+agent_id; 

        })

        function showShare(id,title,img,content) {
            var type='WeChat',
                title = title,
                img =  img,
                header = '',
                content = content,
                id=id,
                url = labUser.path+'webapp/agent/wechatdetail/detail/_v010100?id='+id+'&agent_id='+agent_id,
                weibo=title+'-'+'更多玩转朋友圈的新花样点击一键获取！',
                wechat=title+'-'+'更多玩转朋友圈的新花样点击一键获取！';     
            agentShare(title, url, img, header, content,type,id,weibo,wechat);   
        };

})