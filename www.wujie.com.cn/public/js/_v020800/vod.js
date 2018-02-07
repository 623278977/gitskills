var player;
var reg=/^\d{10,11}$/g;
var args=getQueryStringArgs(),
	id = args['id'] || '0',
    uid = args['uid'] || '0',
    origin_mark = args['share_mark'] || 0,//分销参数，分享页用
    code = args['code'] || 0;
var shareFlag = (window.location.href).indexOf('is_share') > 0 ? true : false;

var dataObj= {
    "id": id,
    "uid": uid,
    "section": 0,
    "commentType": 'Video',
    "content": '',
    "pageSize":5,
    "page":1
};
;var Video = {
   // 加载相关信息
	vodDetail:function(param,shareFlag){
		var params={};
			params['id']=param.id;
			params['uid']=param.uid;
		var url=labUser.api_path + '/video/detail/_v020800';
		ajaxRequest(params,url,function(data){
			if(data.status){
				var sel=data.message.self,
					rec=data.message.rec;
					tag_video = data.message.tag_video;
				var	ovoid=(sel.maker_ids==''||sel.maker_ids==undefined)?[]:sel.maker_ids.split('@');
				//获取share_mark	
				$('#sharemark').data('type_id',sel.id);
				// $('#sharemark').data('long',sel.watch_reward_long);
				var shareMark=$('#sharemark').data('mark');
				var relation_id=$('#sharemark').data('code');
				var long=$('#sharemark').data('long');
				// 获取视频详情（标题，概述等）
				$('#video_detail').attr({'title':sel.subject,'data_des':sel.description,'data_img':sel.image});
	            
				if(shareFlag){
					$('#installapp').removeClass('none');
					$('#distribution').addClass('none');
					$('#comment_btn').addClass('none');
					$('span[type="rel_video"]').addClass('none');
					$('#App').removeClass('none');
					$('#share').addClass('none');
					if(sel.score_price==0){
						$('.share_video').addClass('none');
						// 试看一分钟
						getVod(sel.video_url, 60,0,sel.score_price,long,shareFlag);
					}else{
						$('.share_video').removeClass('none');
						$('.share_text').text('该视频为有偿观看，请至app中购买观看');
					};
					
					$('.buy').removeClass('none').text('下载APP');
					// 点击视频预览试看一分钟
					$(document).on('tap ','#know_more',function(){
						$('.share_video').hide();
						$("#video_box").show();
						getVod(sel.video_url, 60,0,sel.score_price,long,shareFlag);

					})
					if(is_weixin()){
						$(document).on('tap','#openapp,#loadapp,#App',function(){					
                                var _height = $(document).height();
                                $('.safari').css('height', _height);
                                $('.safari').removeClass('none');                      
						});
						$(document).on('tap','.safari',function(){
							$('.safari').addClass('none');
						});
						var wxurl = labUser.api_path + '/weixin/js-config';
						//微信二次分享
                        var desptStr = removeHTMLTag(sel.description);
                        var despt = cutString(desptStr, 60);
                        ajaxRequest({url: location.href}, wxurl, function (data) {
                            if (data.status) {
                                wx.config({
                                    debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                                    appId: data.message.appId, // 必填，公众号的唯一标识
                                    timestamp: data.message.timestamp, // 必填，生成签名的时间戳
                                    nonceStr: data.message.nonceStr, // 必填，生成签名的随机串
                                    signature: data.message.signature, // 必填，签名，见附录1
                                    jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
                                });
                                wx.ready(function () {
                                    wx.onMenuShareTimeline({
                                        title: sel.subject, // 分享标题
                                        link: location.href, // 分享链接
                                        imgUrl: sel.image	, // 分享图标
                                        success: function () {
                                            // 用户确认分享后执行的回调函数
                                            
            //                                 if($('#share').data('reward')==1){
												// sencondShare('relay')
            //                                 }
                                            
                                        },
                                        cancel: function () {
                                            // 用户取消分享后执行的回调函数
                                        }
                                    });
                                    wx.onMenuShareAppMessage({
                                        title: sel.subject,
                                        desc: despt,
                                        link: location.href,
                                        imgUrl: sel.image,
                                        trigger: function (res) {
                                            // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
                                            console.log('用户点击发送给朋友');
                                        },
                                        success: function (res) {
                                        	 //  2017/10/19 注释 ，因经纪人后无分享赚佣规则
            //                             	if($('#share').data('reward')==1){
												// sencondShare('relay')
            //                                 }
                                        },
                                        cancel: function (res) {
                                            console.log('已取消');
                                        },
                                        fail: function (res) {
                                            console.log(JSON.stringify(res));
                                        }
                                    });
                                });
                                // regsiterWX(selfObj.v_subject,selfObj.detail_img,location.href,selfObj.v_description,'','');
                            }
                        });
					}else {
                        if (isiOS) {
                            //打开本地app
                            $(document).on('tap', '#openapp,#App', function () {
                                oppenIos();
                            });
                            /**下载app**/
                            $(document).on('tap', '#loadapp', function () {
                                window.location.href = 'https://itunes.apple.com/app/id981501194';
                            });
                            oppenIos();
                        }
                        else if (isAndroid) {
                            $(document).on('tap', '#loadapp', function () {
                                window.location.href = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.tyrbl.wujiesq';
                            });
                            $(document).on('tap', '#openapp,#App', function () {
                               openAndroid();
                           });
                            openAndroid();
                        }
                        
                    };

                    //点击相关视频跳转页面
         			$(document).on('tap',".more_video>li",function(){
         				var id=$(this).attr('data_id');
         				window.location.href=labUser.path+'webapp/vod/detail/_v020700?id='+id+'&uid=0&is_share=1';
         			});	

				}else {	
					
					getUserdetail(uid,uid);
					//是否收藏
                    $(".isFavorite").attr("value", sel.is_favorite);	
                    setFavourite(sel.is_favorite);
					// 是否免费
					if(sel.score_price == 0){
						$('.share_video').addClass('none');
						$('.top_left').addClass('none');
						getVod(sel.video_url, 0,param.uid,0,long,shareFlag);
					}else{
						if(sel.is_purchase==1){ //******购买过
							// $('.buy').addClass('none');
							$('.share_video').addClass('none');
							getVod(sel.video_url, 0,param.uid,0,long,shareFlag);
						}else if(sel.is_purchase==0){
							// $('.buy').removeClass('none');
							$('.needpay').removeClass('none');
							$('#score_price').text(sel.score_price);
							$('.share_video').removeClass('none');
							$('.gap').css('top','27.7rem');
							$('.videodetail_box').css('padding-top','27.7rem');
						};
					}
					//购买视频
					// var ovoid=sel.maker_ids.split('@')[0];
					 // var price=sel.score_price;	
					 // var v_id=sel.id;						
						// $(document).on('click','.buy',function(){
						// 	buyVideo(v_id, price);		
						// })
					 //点击相关视频跳转页面
         			$(document).on('tap ',"#brand_relvideo>div,#rel_like>div",function(){
         				var id=$(this).attr('data-id');
         				window.location.href=labUser.path+'webapp/vod/detail/_v020700?id='+id+'&uid='+param.uid+'&pagetag=05-4';
         			});
         			// 点击视频预览试看一分钟
					$(document).on('tap ','#know_more',function(){
						$('.share_video').hide();
						$("#video_box").show();
						getVod(sel.video_url, 60,param.uid,sel.score_price,long,shareFlag);
					})
         			
				};
									
			
			//品牌信息
				if(sel.with_brand==1){
				//品牌奖励金的相应展示
					if(data.message.fund == 0){
						$('#brand_award').addClass('none').siblings('div').css('width','50%');
					}else{
						$('#brand_fund').html('￥' + data.message.fund);
				        $('.b-fund').html(data.message.fund);
				        $('#brand_award').data('fund', data.message.fund);
				        $('#brand_award').data('fetch', data.message.fetched_fund);
					}
					
					var brandHtml='';					
						brandHtml+='<div class="white-bg brand-company pl1-33" data-id="'+sel.brand_id+'"><img src="'+sel.brand.logo+'" alt="" class="company mr1-33 fl">';
						brandHtml+='<div class="fl width70"><em class="service f12 mr1">'+sel.brand.category_name+'</em>'
						brandHtml+='<span class="f14 b">'+sel.brand.name+'</span>';
						brandHtml+='<div class="brand-desc f12 color999 mb05 ui-nowrap-multi">'+removeHTMLTag(sel.brand.detail)+'</div>';
						brandHtml+='<p class="f12 mb05"><span class="c8a">投资额：</span> <span class="color-red">'+sel.brand.investment_min+' ~ '+sel.brand.investment_max+'万</span></p>';
						if(sel.brand.keywords.length>0){
							var aHtml='';
							for(i=0;i<sel.brand.keywords.length;i++){
								aHtml+='<a class="tags-key border-8a-radius">'+sel.brand.keywords[i]+'</a>';
							};
							brandHtml+=aHtml;
						};
						brandHtml+='</div><div class="clearfix"></div></div>';									
					$('#brand_info').html(brandHtml).attr('data_brandID',sel.brand_id);
					if($('#distribution').hasClass('none')){
						$('.brand').addClass('mt7-133');
					}
					if(shareFlag){
						$('.brand-p').removeClass('none');
					}else{
						$('.brand-np').removeClass('none');
					}
				}else if(sel.with_brand==0){
					$('.brand').addClass('none');
					if(shareFlag){
						$('.brand-p').removeClass('none');
						$('#brand_suggest_share').remove();
						$('#loadapp').css('width','100%');
					}
				};
			//基本信息之视频信息
				$('#basic_videoimg').attr('src',sel.image);
				$('#basic_videoinfo').html('<p class="f16 mb02 h5-2 color333">'+cutString(sel.subject,20)+'</p><p class="f12 c8a">录制时间：<span>'+unix_to_mdhm(sel.created_at)+'</span></p>');
				if(sel.description==''){
						$('.disVideo').append('<p class="c8a">暂无详情<p>');
				}else{	
					$('.disVideo').html(sel.content);
				};
				if($('#distribution').hasClass('none') && $('.brand').hasClass('none')){
					$('#basicvideo_info').addClass('mt7-133');
				}

			//猜你喜欢
				//其他相关视频.
				var otherHtml ='';
					if(tag_video.length > 0){
						$.each(tag_video,function(i,j){
							otherHtml += '<div class="fline" data-id="'+j.id+'"><div class="l video_img "> <p class="playlogo mb0"><img src="/images/play.png" alt=""></p>';
							otherHtml += '<img src="'+j.image+'" alt=""></div><div class="video_intro">';
							otherHtml += '<p class="f16 mb02">'+j.subject+'</p>';
							otherHtml += '<p class="f14 color999">录制于<span>'+unix_to_yeardate(j.created_at)+'</span></p>';
							otherHtml += '<p class="mb0 relvideo_des">'+j.description+'</p></div><img src="/images/jump.png" alt="" class="r jump"><div class="clearfix"></div></div>';
						})
						$('#brand_relvideo').append(otherHtml);
					}else{
						$('#brand_relvideo').addClass('none');
					}

				var vidHtml='';
				$('#video_num').html(rec.length);
				if(rec.length > 0){
					$.each(rec,function(i,j){
						vidHtml += '<div class="fline" data-id="'+j.id+'"><div class="l video_img "> <p class="playlogo mb0"><img src="/images/play.png" alt=""></p>';
						vidHtml += '<img src="'+j.image+'" alt=""></div><div class="video_intro">';
						vidHtml += '<p class="f16 mb02">'+j.subject+'</p>';
						vidHtml += '<p class="f14 color999">录制于<span>'+unix_to_yeardate(j.created_at)+'</span></p>';
						vidHtml += '</div><img src="/images/jump.png" alt="" class="r jump"><div class="clearfix"></div></div>';
					})
					$('#rel_like').html(vidHtml);	
				}else{
					$('#rel_like').addClass('none');	
				}
				$(".containerBox").removeClass('none');
				var dis = $('.dis_coin');
				var spanHeight= parseFloat(dis.css('height')),spanLH=parseFloat(dis.css('line-height'));
				if(spanHeight > spanLH*2){
					dis.addClass('ui-nowrap-multi'); 
				}else{
					$('.more_icon').addClass('none');
					dis.addClass('pb1-5');
				}	 
			};
		});
	},
	//加载评论列表
	getComment:function(param,shareFlag){
		var params={};
			params['id']=param.id;
			params['uid']=param.uid;
			params['type']=param.commentType;
			params['page']=param.page;
			params['page_size']=param.pageSize;
			params['section']=param.section;
		var url=labUser.api_path+'/comment/list';
		ajaxRequest(params,url,function(data){
			if(data.status){
				var comHtml='';
				var obj=data.message.data;
				$('.com_num').text(data.message.all_count);	
				$.each(obj,function(index,item){
					comHtml+='<li><img src="'+item.avatar+'" alt="header" class="l"><div class="publisher r">';
					comHtml+='<p class="f16 color666 b lh3-3 m0">'+ item.c_nickname+'<span class="r laub lh3-3">';
					//评论人是否点赞
					if(item.is_zhan){
						comHtml +='<img src="/images/020502/zan.png"  data-zan="1" data-id="'+item.id+'"><em data-zannum="'+item.likes+'">'+zannum(item.likes)+'</em></span></p>';
					}else{
						comHtml +='<img src="/images/littlewz.png"  data-zan="0" data-id="'+item.id+'"><em data-zannum="'+item.likes+'">'+zannum(item.likes)+'</em></span></p>';
					};
					if(item.images.length > 0){
						var imgs='';
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
					if(obj.length<5){
						$('.getMore').text('没有更多了...').attr('disabled','true');
					}
				}									
				if(data.message.all_count<=5){
					$('.getMore').addClass('none');
					$("#comment").css('margin-bottom','11.1rem');
				}
			}else{
				if($('#comment>li').length==0){
					$('#comment').html('<p style="padding:1rem 0 2rem" class="c8a">暂无评论</p>').css('margin-bottom','11.1rem');
					// $('.com_num').addClass('none');
					$('.getMore').addClass('none');
				}else if($('#comment>li').length>5){
					$('.getMore').text('没有更多了...').attr('disabled','true');

				}
			}
		})
	},
	// 发表评论
	addComment:function(param,shareFlag){
		var params={};
			params['post_id']=param.id;
			params['uid']=param.uid;
			params['type']=param.commentType;
			params['content']=param.content;
		var url=labUser.api_path+'/comment/add';
		ajaxRequest(params,url,function(data){
			if(data.status){
				Video.getComment(param,shareFlag);
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

//提交意向加盟时获取用户详情
    function getUserdetail(uid,user_id){
        var param={};
            param['uid']=uid;
            param['user_outh']=user_id;
        var url=labUser.api_path+'/user/getuserdetail';
        ajaxRequest(param,url,function(data){
            if(data.status){
                var telnum=data.message[0].username||'';
                var nickname=data.message[0].nickname||'';
                $('#realname').val(nickname);
                $('#telnum').val(telnum);
            }
        })
    }
    

//点击在线客服跳转
	$(document).on('click','.brand_collect',function(){
		var brandID=$('.brand-company').attr('data-id');
		toRobot(brandID);
	})	
//分享佣金的展示与隐藏
		var dis_coin = $('.dis_coin'),more_icon =$('.more_icon img'),showMore = true;
		var spanHeight= parseInt(dis_coin.css('height')),spanLH=parseInt(dis_coin.css('line-height'));
		// console.log(spanHeight);
		// console.log(spanLH);
		// if(spanLH*2){

		// }
		
		$(document).on('click','.more_icon',function(){
		    if(showMore){
		       dis_coin.removeClass('ui-nowrap-multi');
		        more_icon.css({'transform':'rotate(180deg)',
		        				'-webkit-transform':'rotate(180deg)',
		        				'-o-transform':'rotate(180deg)'
		    					});
		        showMore = false ;
		    }else{
		        dis_coin.addClass('ui-nowrap-multi');
		        more_icon.css({'transform':'rotate(0deg)',
		        				'-webkit-transform':'rotate(0deg)',
		        				'-o-transform':'rotate(0deg)'});
		        showMore = true ;
		    }
		});
// 领取创业基金
		$(document).on('click', '#brand_award', function() {
		    var fetch = $(this).data('fetch');
		    var fund = $(this).data('fund');
		    var brandID = $('#brand_info').attr('data_brandID');
		    if (fetch) {
		        alertShow('您已经领取过了');
		        return false
		    } else {
		        award(brandID, uid, fund);
		        fadeBrand('.brand-packet', 'a-bouncein');
		        $('#brand_award').data('fetch', true);
		    }
		});
//发送加盟意向点击弹出
		$(document).on('click', '#brand_suggest,#brand_suggest_share', function() {
		    fadeBrand('.brand-message', 'a-fadeinT');
		    $('.brand-message').css('z-index', '99');
		});	

// 点击品牌跳转至品牌详情页
		$(document).on('tap','.brand-company',function(){
			var brandID =$('#brand_info').attr('data_brandID');
			if(shareFlag){
				window.location.href=labUser.path+'webapp/brand/detail/_v020800?id='+brandID+'&uid='+uid+'&is_share=1';
			}else{
				window.location.href=labUser.path+'webapp/brand/detail/_v020800?pagetag=08-9&id='+brandID+'&uid='+uid;
			}
		})
//点击提交意向
		$(document).on('click','#btn',function(){
			var brandId=$('#brand_info').attr('data_brandID');
				uid=uid,
				mobile=$('#telnum').val(),
				realname=$('#realname').val(),
				content=$('#will').val();
				// share_mark=data.message.share_mark;
			if(mobile==''||realname==''||content==''){
				alertShow('请完善相关内容');
				return false;
			}else if(!reg.test(mobile)||mobile.length>11){
				alertShow('号码格式不正确');
				return false;
			}else{
				if(shareFlag){
					sendWill(brandId,uid,mobile,realname,content,'html5');
				}else{
					sendWill(brandId,uid,mobile,realname,content,'app');
				}	
			}
		});
// 点击蒙层关闭
    $(document).on('click', '.fixed-bg', function() {
        $(this).addClass('none');
        $('.brand-message').css('z-index', '-1');
        $('.brand-message').removeClass('a-fadeinT').addClass('a-fadeoutT');
        $('.share-reset').click();
        $('#packet_close').click();
        $('.share-title').addClass('none').removeClass('a-fadeinB');
    });



//该用户对评论点赞或取消点赞
		$(document).on('click','.laub',function(){
			var imgEle = $(this).children('img'),emEle = $(this).children('em');
			console.log(imgEle);
			var id = imgEle.attr('data-id');
			var type = imgEle.attr('data-zan');
			if(type == 1){
				Video.zan(uid,id,0,imgEle,emEle);//已赞点击取消点赞
			}else{
				Video.zan(uid,id,1,imgEle,emEle);//未赞点击点赞
			}
		})
//跳至积分支付页面
		$(document).on('click','.tobuy',function(){
			var score = $('#score_price').text();
			toScore('video',score,id);
		})

// 收藏视频
function getCollect(id,model,type){
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
					
				}else{
					$(".isFavorite").attr("value",0);
					// $('.collectbtn').text('收藏');
				}
			}
		});
	};

// 点播页相关品牌提交意向
function sendWill(id,uid,mobile,realname,content,type){
	var param={};
		param['id']=id;
		param['uid']=uid;
		param['mobile']=mobile;
		param['realname']=realname;
		param['consult']=content;
		param['type']=type;
		// param['share_mark']=share_mark;
	var url=labUser.api_path+'/brand/message/_v020500';
	ajaxRequest(param,url,function(data){
		if(data.status){
			alertShow('提交成功');
			$('.brand-message').addClass('none');
         	$('.fixed-bg').addClass('none');
		}else{
			alertShow(data.message);
			$('.brand-message').addClass('none');
         	$('.fixed-bg').addClass('none');
		}
	})
};

// 品牌收藏与取消
	function collectBrand(id,uid,type){
		var param={};
			param.id=id;
			param.uid=uid;
			param.type=type;
		var url=labUser.api_path+'/brand/collect';
		ajaxRequest(param,url,function(data){
			if(data.status){
				if(param.type=='do'){
					alertShow('收藏成功！')
					$('#collect').text('取消收藏');
				}else if(param.type=='undo'){
					alertShow('取消成功！')
					$('#collect').text('收藏品牌');
				}
			}
		});
	};
//领取品牌创业基金
 function award(id, uid, fund) {
            var param = {};
            param['brand_id'] = id;
            param['uid'] = uid;
            param['fund'] = fund;
            var url = labUser.api_path + '/brand/fetch-fund/_v020500';
            ajaxRequest(param, url, function(data) {
                if (data.status) {
                    
                }
            })
        }


/**实例化点播**/
function getVod(video_url, stop_time, userid, price,reward_long,share) {
	var duation=0;
	var sharemark=$('#sharemark').data('mark');
	var relation_id=$('#sharemark').data('code');
	var reward_long=reward_long;
    player = new qcVideo.Player(
        //页面放置播放位置的元素 ID
        "video_box",
        {
            "file_id": video_url, //视频 ID (必选参数)
            "app_id": "1251768344", //应用 ID (必选参数)，同一个账户下的视频，该参数是相同的
            "auto_play": "0", //是否自动播放 默认值0 (0: 不自动，1: 自动播放)
            "width": 414, //播放器宽度，单位像素414
            "height": 232, //播放器高度，单位像素232
            "stop_time": stop_time,
            "coverpic":'http://test.wujie.com.cn/images/share_image.png',//新增
            "disable_full_screen": 0,
            'stretch_patch':true,//默认为 false ,设置为 true 支持将开始、暂停、结束时的图片贴片，铺满播放器
            'WMode': 'opaque',//默认 window 不支持其他页面元素覆盖在上面，如需要可以修改为 opaque 或其他 flash Vmode 的参数值
            'live':false
        },
        {
            //播放状态
            // status 可为 {ready: "播放器已准备就绪",seeking:"搜索",
            // suspended:"暂停", playing:"播放中" , playEnd:"播放结束" , stop:"试看结束触发"
            'fullScreen': function(isFullScreen){
				alert('fullScreen');
			},
            'playStatus': function (status) {
                if (status == 'playing') { //播放中

        //  2017/10/19 注释 ，因经纪人后无分享赚佣规则           	
              //       v_time=setInterval(function (){
	             //    		duation++; 
	             //    		console.log(duation);                             		
	             //    		if(duation==(reward_long*60)&&reward_long!=0){    
	             //    			console.log('奖励');
	             //    			if(share){
	             //    				getReward(origin_mark,'watch',userid,code);
	             //    			}else{
	             //    				getReward(sharemark,'watch',userid,code);
	             //    			}
	                					                				                			                		
	             //    			clearInterval(v_time);
	             //    		}
            		// },1000);//获取播放时长,数分钟后获得奖励   
                    // b_vedio=setTimeout(function (){getReward(sharemark,'watch',userid,relation_id)},60000);//十分钟后获得奖励      
                }
                if(status=='suspended'){ //播放暂停
                	// clearInterval(v_time);	
                }
                if (status == "playEnd") { //播放结束
                    console.log('end');
                   	// clearInterval(v_time);
                }
                if (status == "stop") { //试看结束
                    showMessage(userid, price, 'video');
                   	// clearInterval(v_time);
                }
            },
        });    
};



//试看结束
function showMessage(userid, price, type) {
	var is_share=(window.location.href).indexOf('is_share') >0 ? true : false;
    if (userid == 0) {
        if (price == 0) {
            $("#video_box").hide();
            $('.share_video .share_text').html('<button class="order none" id="loginbtn">登录</button></br>试看已结束,请登录app观看完整视频');
            $(".share_video").show();
        }
        else {
            $("#video_box").hide();
            $('.share_video .share_text').html('<button class="order none" id="loginbtn">登录</button></br>试看已结束,请登录app购买本视频');
            $(".share_video").show();
        }
        if (type == 'video') {
            var inapp = (window.location.href).indexOf('is_share') < 0 ? true : false;
            if (inapp) {
                $('#loginbtn').removeClass('none');
                $('#loginbtn').on('click', function () {
                    showLogin();
                });
            }
        }
    }
    else {
        $("#video_box").hide();
        $('.share_video .share_text').html('试看已结束<br>请点击"购买"按钮,购买本视频');
        $(".share_video").show();
    }
};
//打开本地--Android
function openAndroid(){
    var strPath = window.location.pathname;
    var strParam = window.location.search.replace(/is_share=1/g, '');
    var appurl = strPath + strParam;
    window.location.href = 'openwjsq://welcome' + appurl;
}
function oppenIos(){
    var strPath = window.location.pathname.substring(1);
    var strParam = window.location.search;
    var appurl = strPath + strParam;
    var share = '&is_share';
    var appurl2 = appurl.substring(0, appurl.indexOf(share));
    window.location.href = 'openwjsq://' + appurl2;
}

// 提示框
    function alertShow(content){
    	$(".alert>p").text(content);
        $(".alert").css("display","block");
        setTimeout(function(){$(".alert").css("display","none")},2000);
   };  

//二次分享先记录后奖励
	function sencondShare(type){
        var getcodeurl = labUser.api_path + '/index/code/_v020500';
        ajaxRequest({}, getcodeurl, function (data) {
            var newcode = data.message;//code
            var logsurl = labUser.api_path + "/share/share/_v020500";
            ajaxRequest({
                uid: '0',
                content: 'video',
                content_id: id,
                source: 'weixin',
                code:newcode,
                share_mark: origin_mark
            }, logsurl, function (data) {
                getReward(origin_mark, type, 0, newcode);
            });
        });

    };

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

  //弹出淡入效果
	function fadeBrand(ele, type) {
	    $('.fixed-bg').removeClass('none');
	    $(ele).removeClass('none').addClass(type);
	}
//跳转到移动端机器人客服
function toRobot(id) {
    if (isAndroid) {
        javascript:myObject.toRobot(id);
    } 
    else if (isiOS) {
        var data = {
           "id":id
        }
        window.webkit.messageHandlers.toRobot.postMessage(data);
    }
}

 //评论成功后刷新评论(移动端调用)
    function Refresh(){
        Video.getComment(dataObj,shareFlag);
    }