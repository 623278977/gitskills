@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/vod.css" rel="stylesheet" type="text/css"/>
    <style>
		.nocomment{
		    width: 13rem;
		    margin: 0 auto;
		    padding-top: 10rem;
		}
		/*1.0.5新增*/
		.comment_tip{
			background: #2a2a2a;
		    /*display: block;*/
		    width: 13rem;
		    height: 3.6rem;
		    border-radius: 1rem;
		    color: #fff;
		    text-align: center;
		    line-height: 3.6rem;
		    position: absolute; 
		    top: -7rem;
		    right: 0rem;
		}
		/*小三角*/
		.comment_tip::after{
			content: '';
			width:0;height: 0;
			border-top:0.9rem solid #2a2a2a;
			border-right:0.9rem solid transparent;
			border-bottom:0.9rem solid transparent;
			border-left:0.9rem solid transparent;
			position: absolute;
			bottom:-1.6rem;
			right:3rem;
		}
		.change_position::after{
			left:1rem;
		}
		.comment_tip em{
			display: inline-block;
			width: 50%;
			font-size: 1.3rem;
		}
		.reply{
			border-right:1px solid #fff;
		}

		.copy_suc{
			width:9rem;
			/*height: 9rem;*/
			border-radius:1rem;
			background: #2a2a2a;
			position: fixed;
			padding-top: 2em;
			top:50%;margin-top: -4.5rem;
			left:50%;margin-left: -4.5rem;
			z-index: 10;
			transition: all 0.5s;
			-webkit-transition:all 0.5;	
			-moz-transition:all 0.5;	
			-o-transition:all 0.5;	
		}
		.copy_suc img{
			width: 2.2rem;
			height: 2.2rem;
			margin-bottom: 1rem;
		}
		.comment_text{
			word-break: break-all;
			display: block;	
		}
		.break-word{
			word-wrap: break-word;
		}

    </style>
@stop
@section('main')
	<section >
		<div class="conmments mt2-5 pb5 none">
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
		<div id="comment_btn" class="comment_btn"><button type="button" class="tl" style="width: 26rem;">我来说两句...</button><span class="uploadpic1"></span><i class="uploadpictext f12">发表图片</i></div>
		 <div class="copy_suc tc none">
	      	<img src="/images/agent/success.png" style="">
	      	<p class="white f12 ">已复制</p>
	    </div>
	</section>
@stop
@section('endjs')
	<script type="text/javascript" src="{{URL::asset('/')}}/js/agent/_v010005/reply.js"></script>
	<script type="text/javascript">
			$(document).ready(function(){
	    		$('title').text('评论界面');  
	        });  
			new FastClick(document.body);
			var args = getQueryStringArgs(),
        		id = args['id'] || '0',
        		uid = args['agent_id'] || '0',
        		shareFlag = location.href.indexOf('is_share') > 0 ? true:false;
        	var params ={
        		"page" : 1,
        		"pageSize" : 10,
        		"section" : 0,
        		"id" : id,
        		"uid" : uid
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
						var url=labUser.agent_path+'/comment/assign-news-all-comment-list/_v010005';
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
										comHtml+='<p class="f16 color666 b lh3-3 m0"><span class="comment_name">'+ item.c_nickname+'</span><span class="r laub lh3-3">';
										//评论人是否点赞
										if(item.is_zhan){
											comHtml +='<img src="/images/agent/zan.png"  data-zan="1" data-id="'+item.id+'"><em data-zannum="'+item.likes+'">'+zannum(item.likes)+'</em></span></p>';
										}else{
											comHtml +='<img src="/images/littlewz.png"  data-zan="0" data-id="'+item.id+'"><em data-zannum="'+item.likes+'">'+zannum(item.likes)+'</em></span></p>';
										};
											comHtml +='<p class="c8a f12 inline-block comment_content relative"><span class="comment_text">'+item.content+'</span>';
										if(item.c_uid == param.uid){
					                        comHtml += '<span class="comment_tip none" style="width:6.5rem;"><em class="copy" style="width:100%;">复制</em></span></p>'
					                    }else{
					                        comHtml += '<span class="comment_tip none"><em class="reply" data-id="'+item.id+'" data-type="mes">回复</em><em class="copy">复制</em></span></p>';
					                    };

										if(item.images.length > 0){
											var imgs = '';
											$.each(item.images,function(i,j){
												imgs+='<img src="'+j+'">';
											})
											comHtml+='<p class="comment_pic">'+imgs+'</p>';											
										}
										if(item.pId){
											var pImg = '';
											if(item.pImages.length > 0){
												$.each(item.pImages,function(k,v){
													 pImg +='<img src ="'+v+'">';
												})	
											};
					                        comHtml += '<div class="bgcolor f13 p1 mb1"><p><span class="c2873ff">'+item.p_nickname+'</span>：</p><p class="color666 break-word ui-nowrap-multi">'+item.pContent+'</p><p>'+pImg+'</p></div>';
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
					// 评论点赞
					zan :function(uid,id,type,ele,em){
						var param ={};
							param['uid'] = uid ;
							param['id'] = id;
							param['type'] = type;
						var url = labUser.agent_path + '/comment/assign-user-comment-add-zan/_v010005';
						ajaxRequest(param,url,function(data){
							if(data.status){
								
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
            $('.uploadpic1,.uploadpictext').on('click', function() {
                uploadpic(params.id, 'messageDetail', false);
            });
            //仅文字
            $('.comment_btn>button').on('click', function() {
                uploadpic(params.id, 'messageDetail', true);
            });        
            //该用户对评论点赞或取消点赞
			$(document).on('tap','.laub',function(){
				var imgEle = $(this).children('img'),emEle = $(this).children('em');
				var id = imgEle.attr('data-id');
					if(imgEle.attr('src') == '/images/agent/zan.png'){
						imgEle.attr('src','/images/littlewz.png');
						emEle.text(zannum(emEle.text()-1));	
						Comments.zan(uid,id,0,imgEle,emEle);//已赞点击取消点赞
					}else{
						imgEle.attr('src','/images/agent/zan.png');
						emEle.text(zannum(emEle.text()-1+2))
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
		// 评论回复与复制
			commonReply(id);


	</script>
	
@stop