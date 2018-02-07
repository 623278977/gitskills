@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/_v020700/vod.css" rel="stylesheet" type="text/css"/>
    <style>
		.nocomment{
		    width: 13rem;
		    margin: 0 auto;
		    padding-top: 10rem;
		}

    </style>
@stop
@section('main')
	<section >
		<div class="conmments mt1-5 pb5 none">
				<img src="{{URL::asset('/')}}/images/novideo.png" alt="" class="nocomment none">
                <div class="comment none fline">
                    <ul id="comment" class="pr1-33" >
                    	<!-- <li>
                    		<img src="http://test.wujie.com.cn/attached/image/20170424/20170424094758_41819.jpg" alt="header" class="l">
                    		<div class="publisher r">
                    			<p class="f16 color666 b lh3-3 m0">愿是阳光
                    				<span class="r laub lh3-3">	
                    					<img src="/images/littlewz.png" data-zan="0" data-id="2473">
                    					<em data-zannum="0">0</em>
                    				</span></p>
                    			<p class="c8a f12">OK</p>
                    			<p class="time">04月24日 09:46</p>
                    		</div>
                    		<div class="clearfix"></div>
                    	</li> -->
                    	
                    </ul>
                </div>
                <button class="getMore f12 c8a ">点击加载更多</button>
        </div>
		<div id="comment_btn" class="comment_btn"><button type="button" class="tl" style="width: 30rem;">我来说两句...</button><span class="uploadpic1"></span><i class="uploadpictext f12">发表图片</i></div>
	</section>
@stop
@section('endjs')
	<script type="text/javascript">
			new FastClick(document.body);
			var args = getQueryStringArgs(),
        		id = args['id'] || '0',
        		uid = args['uid'] || '0',
        		shareFlag = location.href.indexOf('is_share') > 0 ? true:false;
        	var params ={
        		"page" : 1,
        		"pageSize" : 10,
        		"section" : 0,
        		"id" : id,
        		"uid" : uid,
        		"type" : "News",
        		'content':''
        		};
		var Comments ={
		        		//加载评论列表
					getComment:function(param){
						var params={};
							params['id']=param.id;
							params['uid']=param.uid;
							params['type']=param.type;
							params['page']=param.page;
							params['page_size']=param.pageSize;
							params['section']=param.section;
						var url=labUser.api_path+'/comment/list';
						ajaxRequest(params,url,function(data){
							if(data.status){
								var comHtml='';
								var obj=data.message.data;
								$('.com_num').removeClass('none').text(data.message.all_count);	
								if(data.message.all_count == 0){
									$('.nocomment').css('display','block');
									$('.getMore').addClass('none');
								}else{
									$.each(obj,function(i,item){
										comHtml+='<li><img src="'+item.avatar+'" alt="header" class="l"><div class="publisher r">';
										comHtml+='<p class="f16 color666 b lh3-3 m0">'+ item.c_nickname+'<span class="r laub lh3-3">';
										//评论人是否点赞
										if(item.is_zhan){
											comHtml +='<img src="/images/020502/zan.png"  data-zan="1" data-id="'+item.id+'"><em data-zannum="'+item.likes+'">'+zannum(item.likes)+'</em></span></p>';
										}else{
											comHtml +='<img src="/images/littlewz.png"  data-zan="0" data-id="'+item.id+'"><em data-zannum="'+item.likes+'">'+zannum(item.likes)+'</em></span></p>';
										};
										if(item.images.length > 0){
											var imgs = '';
											$.each(item.images,function(i,j){
												imgs+='<img src="'+j+'">';
											})
											comHtml+='<p class="c8a f12">'+item.content+'</p><p class="comment_pic">'+imgs+'</p>';
										}else{
											comHtml+='<p class="c8a f12">'+item.content+'</p>';
										}
										comHtml += '<p class="time">'+item.created_at+'</p></div><div class="clearfix"></div></li>';
									});
									if(params.page==1){
										$("#comment").html(comHtml);
									}else{
										$("#comment").append(comHtml);
										if(obj.length<10){
											$('.getMore').text('没有更多了...').attr('disabled','true');
										}
									}									
									if(data.message.all_count<= params.page_size){
										$('.getMore').addClass('none');
										// $("#comment").css('margin-bottom','11.1rem');
									}
									$('.nocomment').css('display','none');
									$('.comment').removeClass('none');

								}
								$('.conmments').removeClass('none');
							}

						})
					},
					// 发表评论
					addComment:function(param){
						var params={};
							params['post_id']=param.id;
							params['uid']=param.uid;
							params['type']=param.type;
							params['content']=param.content;
						var url=labUser.api_path+'/comment/add';
						ajaxRequest(params,url,function(data){
							if(data.status){
								Comments.getComment(param);
								$('#commentback').addClass('none');
								$('#comtextarea').val('');
							}else{
								alertShow('请填写评论内容')
							}
						})

					},
					// 评论点赞
					zan :function(uid,id,type,ele,em){
						var param ={};
							param['uid'] = uid ;
							param['id'] = id;
							param['type'] = type;
						var url = labUser.api_path + '/comment/zhan';
						ajaxRequest(param,url,function(data){
							if(data.status){
								if(type){  
									ele.attr('src','/images/020502/zan.png');//点赞
									ele.attr('data-zan',1);
									em.text(zannum(parseInt(em.attr('data-zannum'))+1));//点赞数加一
									em.attr("data-zannum",parseInt(em.attr('data-zannum'))+1) ;	
								}else{
									ele.attr('src','/images/littlewz.png');
									ele.attr('data-zan',0)
									em.text(zannum(parseInt(em.attr('data-zannum'))-1));//点赞数减一
									em.attr("data-zannum",parseInt(em.attr('data-zannum'))-1) ;
								}
							}
						})
					}
			};

			Comments.getComment(params);
			$('.getMore').click(function(){
				params.page++;
				Comments.getComment(params);
			})
			//upload pictures发表图文
            $('.uploadpic,.uploadpictext').on('click', function() {
                uploadpic(params.id, 'News', false);
            });
            //仅文字
            $('.comment_btn>button').on('click', function() {
                uploadpic(params.id, 'News', true);
            });

            //该用户对评论点赞或取消点赞
			$(document).on('tap','.laub',function(){
				var imgEle = $(this).children('img'),emEle = $(this).children('em');
				console.log(imgEle);
				var id = imgEle.attr('data-id');
				var type = imgEle.attr('data-zan');
				if(type == 1){
					Comments.zan(uid,id,0,imgEle,emEle);//已赞点击取消点赞
				}else{
					Comments.zan(uid,id,1,imgEle,emEle);//未赞点击点赞
				}
			})
	            //点赞数显示处理
			  function zannum(num){
			  	if(num >=0 && num <= 1000){
			  		 num = num ;
			  	}else if(num >1000 && num <= 10000){
			  		num = parseInt(num/1000)+'k';
			  	}else if(num > 10000){
			  		num = parseInt(num/10000)+'w';
			  	}
			  	 return num;
			  }
			function Refresh(){
				params.page = 1;
				Comments.getComment(params);
			}  
	</script>
@stop