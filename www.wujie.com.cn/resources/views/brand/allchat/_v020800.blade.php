@extends('layouts.default')
@section('css')
    <style>
		.nocomment{
		    width: 13rem;
		    margin: 0 auto;
		    padding-top: 10rem;
		}
		.avavr{
			width:3.6rem;
			height: 3.6rem;
			border-radius: 50%;
		}
		.flexbox{
			display: flex;
			justify-content: space-between;
			align-items: center;
		}
		.l3-6{
			line-height: 3.6rem;
		}
		.xing img{
			width: 1rem;
			height: 1rem;
		}
		.zan {
			width:1.2rem;
			height: 1.2rem;
		}
		
		.getMore {
		    width: 100%;
		    background: #fff;
		    height: 5.2rem;
		    text-align: center;
		    border: none;
		    margin-bottom: 11.1rem;
		}
		.contentimg img{
			width:12.5rem;
			height: 12.5rem; 
			margin-bottom: 0.5rem;
			margin-right: 0.5rem;
		}
		.contentimg img:last-child{
			margin-right: 0;
		}
		.flex_warp{
			flex-wrap: wrap;
		}
		

    </style>
@stop
@section('main')
	<section >
		<div class="conmments mt1-5 pb5 ">
				<img src="{{URL::asset('/')}}/images/novideo.png" alt="" class="nocomment none">
                <div class="comment  bgwhite pl1-33">
                    <ul id="comment" >
              

                    <!-- 	<li class="fline">
                    		<div class="flexbox">
                    			<p>
                    				<img src="http://test.wujie.com.cn/attached/image/20170424/20170424094758_41819.jpg" alt="header" class="l avavr">
                    				<span class="f16 color666 ml1 l3-6">评论名称</span>
                    			</p>
                    			<p class="f10 color999">
                    				2017/02/02
                    			</p>
                    		</div>  
                    		<div class="tl f12 c8a mb1">
                    			<p>评论的内容</p>
                    			<div class="flexbox contentimg flex_warp">
                    				<img src="" alt="">
                    				<img src="" alt="">
                    				<img src="" alt="">
                    				<img src="" alt="">
                    			</div>
                    		</div>  
                    		<div class="flexbox color999">
                    			<p class="xing">
                    				<span>评分</span>
                    				<img src="/images/agent/ico_star_yellow3.png" alt="">
                    				<img src="/images/agent/ico_star_yellow3.png" alt="">
                    				<img src="/images/agent/ico_star_yellow3.png" alt="">
                    				<img src="/images/agent/ico_star_gray3.png" alt="">
                    				<img src="/images/agent/ico_star_gray3.png" alt="">
                    			</p>
                    			<p >
                    				<img src="/images/littlewz.png" alt="" class="zan"><span class="f10 ">60</span>
                    			</p>
                    		</div>          		
                    	</li>  -->
                    	
                    </ul>
                </div>
                <button class="getMore f12 c8a ">点击加载更多</button>
        </div>
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
        		"id" : id,
        		"uid" : uid
        		};
		var Comments ={
		        		//加载评论列表
					getComment:function(param){
						var params={};
							params['brand_id']=param.id;	
							params['uid'] = param.uid;
							params['page']=param.page;
							params['page_size']=param.pageSize;
							
						var url=labUser.api_path+'/brand/comments/_v020800';
						ajaxRequest(params,url,function(data){
							if(data.status){
								var conHtml='';
								if(data.message.length > 0){
									$.each(data.message,function(i,j){
										conHtml += '<li class="fline"><div class="flexbox mt1 pr1-33"><p><img src="'+j.avatar+'" alt="header" class="l avavr">';
										conHtml += '<span class="f16 color666 ml1 l3-6">'+j.nickname+'</span></p><p class="f10 color999">'+unix_to_yeardate(j.created_at)+'</p></div>';
										conHtml += '<div class="tl f12 c8a mb1 pr1-33"><p >'+j.content+'</p>'
										if(j.img_url.length>0){
											conHtml +='<div class="flexbox contentimg flex_warp" style="justify-content:flex-start;">';
											$.each(j.img_url,function(index,item){
												conHtml += '<img src="'+item+'" alt="">';
											})
                    						conHtml += '</div>'                   			
										}
											conHtml += '</div>';
										conHtml += '<div class="flexbox color999 pr1-33"><p class="xing"><span class="mr05">评分</span>';
										for(var x=0;x<5;x++){
											if(x<j.grade){
												conHtml +='<img src="/images/agent/ico_star_yellow3.png" alt="">';
											}else{
												conHtml +='<img src="/images/agent/ico_star_gray3.png" alt="">';
											};										
										};	
										if(j.is_zan == 0){
											conHtml += '</p><p><img src="/images/020700/zan.png" alt="" class="zan yizan" data-id="'+j.id+'"><span class="f10 ">'+j.likes+'</span></p></div></li>';
										}else{
											conHtml += '</p><p><img src="/images/020700/zan1.png" alt="" class="zan" data-id="'+j.id+'"><span class="f10 ">'+j.likes+'</span></p></div></li>';
										}								
										
									});
								};
								if(data.message.length < param.pageSize){
									$('.getMore').text('没有更多了...').attr('disabled',true);
								};
								$('#comment').append(conHtml);
								$('.conmments').removeClass('none');
							}
						});
					}
					
			};
			Comments.getComment(params);
			$('.getMore').click(function(){
				params.page++;
				Comments.getComment(params);
			})
    	//点赞
    		$(document).on('click','.zan',function(){
    			var com_id = $(this).attr('data-id');
    			var sel = $(this);
    			var url = labUser.api_path + '/comment/zhan/_v020800';
    			var type;
    			if($(this).hasClass('yizan')){
    				type = '0';
    			}else{
    				type='1';
    			}
    			var param = {};
    				param.id = com_id;
    				param.uid = uid;
    				param.type = type;
    			ajaxRequest(param,url,function(data){
    				if(data.status){
    					changezannum(type,sel);
    				}
    			})
    		});
    	//改变点赞状态和数量
    		function changezannum(type,sel){		
    			var zannum = sel.siblings('span').text();
    			if(type== '0'){
    				sel.attr('src','/images/020700/zan1.png').removeClass('yizan');
    				zannum --;
    				sel.siblings('span').text(zannum);
    			}else if(type =='1'){
    				sel.attr('src','/images/020700/zan.png').addClass('yizan');
    				zannum++;
    				sel.siblings('span').text(zannum);
    			}
    		};

			function Refresh(){
				params.page = 1;
				Comments.getComment(params);
			}  
	</script>
@stop