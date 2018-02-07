var player;
var reg=/1[34578]\d{9}/g;
var args=getQueryStringArgs(),
	id = args['id'] || '0',
    uid = args['uid'] || '0',
    origin_mark = args['share_mark'] || 0,//分销参数，分享页用
    code = args['code'] || 0;

;var Video = {
   // 加载相关信息
	vodDetail:function(param,shareFlag){
		var params={};
			params['id']=param.id;
			params['uid']=param.uid;
		var url=labUser.api_path + '/video/detail/_v020500';
		ajaxRequest(params,url,function(data){
			if(data.status){
				var sel=data.message.self,
					rec=data.message.rec;
				var	ovoid=(sel.maker_ids==''||sel.maker_ids==undefined)?[]:sel.maker_ids.split('@');
				//获取share_mark	
				$('#sharemark').data('mark',data.message.share_mark);
				$('#sharemark').data('code',data.message.code);
				$('#sharemark').data('type_id',sel.id);
				$('#sharemark').data('long',sel.watch_reward_long);
				var shareMark=$('#sharemark').data('mark');
				var relation_id=$('#sharemark').data('code');
				var long=$('#sharemark').data('long');
				// 获取视频详情（标题，概述等）
				$('#video_detail').attr({'title':sel.subject,'data_des':sel.description,'data_img':sel.image});
				if(sel.distribution_id==0){
                	$('#share').addClass('none');
                	$('#share').data('reward',0);
	            }else{
	                $('#share').data('reward',1);
	            }
				if(shareFlag){
					if(sel.price==0){
						$('.share_video').addClass('none');
						// 试看一分钟
						getVod(sel.video_url, 60,0,sel.price,long);
					}else{
						$('.share_video').removeClass('none');
						$('.share_text').text('该视频为有偿观看，请至app中购买观看');
						
					};
					var videos='vodID'+id;
					if($('#share').data('reward')==1&&(!localStorage.getItem(videos))){
						getReward(origin_mark,'view',0,code);
						localStorage.setItem(videos,id);
					};
					$('#share').addClass('none');
					$('.buy').removeClass('none').text('下载APP');
					// 点击视频预览试看一分钟
					$(document).on('tap ','#know_more',function(){
						$('.share_video').hide();
						$("#video_box").show();
						getVod(sel.video_url, 60,0,sel.price,long);

					})
					if(is_weixin()){
						$(document).on('tap','#openapp,.buy',function(){					
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
                                            if($('#share').data('reward')==1){
												sencondShare('relay')
                                            }
                                            
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
                                        	if($('#share').data('reward')==1){
												sencondShare('relay')
                                            }
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
                            $(document).on('tap', '#openapp', function () {
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
                            $(document).on('tap', '#openapp', function () {
                               openAndroid();
                           });
                            openAndroid();
                        }
                        
                    };

                    //点击相关视频跳转页面
         			$(document).on('tap',".more_video>li",function(){
         				var id=$(this).attr('data_id');
         				window.location.href=labUser.path+'webapp/vod/detail/_v020500?id='+id+'&uid=0&is_share='+data.message.share_mark;
         			});	

				}else {	
					//是否收藏
                    $(".isFavorite").attr("value", sel.is_favorite);	
                    setFavourite(sel.is_favorite);

					$('#installapp').addClass('none');
					// 是否免费
					if(sel.price==0){
						$('.buy').addClass('none');
						$('.share_video').addClass('none');
						$('.getMore').css('margin-bottom','0rem');
						$("#comment").css('margin-bottom','0rem');
						$('.top_left').addClass('none');
						getVod(sel.video_url, 0,param.uid,0,long);

					}else{
						if(sel.is_purchase==1){ //******购买过
							$('.buy').addClass('none');
							$('.share_video').addClass('none');
							getVod(sel.video_url, 0,param.uid,0,long);
						}else if(sel.is_purchase==0){
							$('.buy').removeClass('none');
							$('.share_video').removeClass('none');
							
						};
					}
					//购买视频
					// var ovoid=sel.maker_ids.split('@')[0];
					 var price=sel.price;	
					 var v_id=sel.id;						
						$(document).on('click','.buy',function(){
							buyVideo(v_id, price);		
						})
					 //点击相关视频跳转页面
         			$(document).on('tap ',".more_video>li",function(){
         				var id=$(this).attr('data_id');
         				window.location.href=labUser.path+'webapp/vod/detail/_v020500?id='+id+'&uid='+param.uid+'&pagetag=05-4';
         			});
         			// 点击视频预览试看一分钟
					$(document).on('tap ','#know_more',function(){
						$('.share_video').hide();
						$("#video_box").show();
						getVod(sel.video_url, 60,param.uid,sel.price,long);
					})
         			//点击电话
         			$(document).on('click','#tel',function(){
         				var num='4000110061';
         				callNum(num);
         			});
         			//点击收藏品牌
	         		$(document).on('click','#collect',function(){
	         			console.log($(this).text());
	         			if($(this).text()=='收藏品牌'){
	         				collectBrand(sel.brand.id,params.uid,'do');
	         			}else{
	         				collectBrand(sel.brand.id,params.uid,'undo');
	         			}
	        		 });
				};
									
			// 是否有关联的活动
				if(sel.with_activity==1){
					var actHtml='',
					// addArr=sel.city.split('@'),
					addHtml='',
					keyHtml='';
					
					// console.log(keyHtml)	;
					actHtml+='<p class="f16 color333 b">'+sel.activity.subject+'</p>';
					actHtml+='<p class="act_time f14 color999">活动开始时间：'+unix_to_datetime(sel.activity.begin_time)+'</p>';
					actHtml+='<p class="act_add f14 color999 ">活动场地：'+sel.activity.city.replace(/@/g,' ')+'</p>';
					if(sel.activity.keywords.length>0){
						$.each(sel.activity.keywords,function(i,item){
							keyHtml+='<span>'+item+'</span>';
						});
						actHtml+='<p class="act_keywords f12 ">'+keyHtml+'</p>'
					}else{
						$('.act>img').css('top','5rem');
					};
					$('.act').prepend(actHtml);						
				}else {
					$('.act').remove();
					$('.guest').css('margin-top','5.333rem');
					if(sel.guests.length==0){
						$('.intro').css('margin-top','5.333rem');
					}
				};
				var gueHtml='';
					if(sel.guests.length>0){
						$.each(sel.guests,function(i,item){
							gueHtml+='<div class="pr1-33"><img class="guest_img" src="'+item.image+'" alt="头像"><p class="guest_name f14">'+item.name+'</p>';
							gueHtml+='<p class="guest_intro f12 c8a">'+item.brief+'</p><div class="clearfix"></div></div>';
						})
						$('.guest').append(gueHtml);
					}else{
						// $('.guest').append('<p class="c8a">暂无相关嘉宾</p>');
						$('.guest').remove();
					};
				if(sel.description==''){
						$('.intro').append('<p class="c8a">暂无概况<p>');
				}else{
						
						$('.video_bas').html(sel.description);
				};
				if(sel.with_brand==1){
					var brandHtml='';					
						brandHtml+='<div class="white-bg brand-company pl1-33"><img src="'+sel.brand.logo+'" alt="" class="company mr1-33 fl">';
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
						var is_col;
						if(sel.brand.is_favorite=='0'){
							is_col='收藏品牌';
						}else {
							is_col='取消收藏';
						}
						if(shareFlag||params.uid==0){
							brandHtml+='<div class="choose_btn two_btn tline"><button id="tel"><a href="tel:4000110061" >电话咨询</a></button><button id="intent">意向加盟</button></div>';
						}else{
							brandHtml+='<div class="choose_btn three_btn tline"><button id="tel"><a href="tel:4000110061" >电话咨询</a></button><button id="collect">'+is_col+'</button><button id="intent">意向加盟</button></div>';
						}									
						
					$('.rel_brand').html(brandHtml);
				}else if(sel.with_brand==0){
					$('.column').removeClass('threeColumn').addClass('twoCloumn');
					$('.column>span[type="rel_brand"]').remove();
				};
				var vidHtml='';
				$('#video_num').html(rec.length);
				$.each(rec,function(i,item){
					if(item.duration=='00:00'||item.duration==''){
						vidHtml+='<li class="fline" data_id="'+item.id+'"><div class="l video_img "><img src="'+item.image+'" alt=""></div>';
					}else{
						vidHtml+='<li class="fline" data_id="'+item.id+'"><div class="l video_img "><button class="f14 videotime ui-border-radius"><span></span>'+item.duration+'</button><img src="'+item.image+'" alt=""></div>';
					}
					
					vidHtml+='<div class="video_intro"><p class="f16 mb02 b">'+cutString(item.subject,10)+'</p>';
					vidHtml+='<p class="f14 color999">录制于：<span>'+unix_to_yeardate(item.created_at)+'</span></p>';
					if(item.keywords.length>0){
						var vHtml='';
						for(j=0;j<item.keywords.length;j++){
							vHtml+='<a class="tags-key border-8a-radius">'+item.keywords[j]+'</a>';						
						};	
						vidHtml+='<p class="mb0">'+vHtml+'</p>';
					}
					vidHtml+='</div><div class="clearfix"></div></li>';
				})
				$('.more_video').html(vidHtml);	
				$(".containerBox").removeClass('none');
				
				
			// 点击活动跳转至活动详情页
				$(document).on('tap','.act',function(){
					if(shareFlag){
						window.location.href=labUser.path+'webapp/activity/detail/_v020500?id='+sel.activity_id+'&uid='+params.uid+'&is_share=1&sharemark='+shareMark;
					}else{
						window.location.href=labUser.path+'webapp/activity/detail/_v020500?id='+sel.activity_id+'&uid='+params.uid+'&pagetag=02-2';
					}
	        	
		         });
			// 点击品牌跳转至品牌详情页
				$(document).on('tap','.brand-company',function(){
					if(shareFlag){
						window.location.href=labUser.path+'webapp/brand/detail/_v020500?id='+sel.brand_id+'&uid='+params.uid+'&is_share='+shareMark;
					}else{
						window.location.href=labUser.path+'webapp/brand/detail/_v020500?pagetag=08-9&id='+sel.brand_id+'&uid='+params.uid;
					}
					
				})
			//点击提交意向
     			$(document).on('click','#btn',function(){
     				var brandId=sel.brand.id,
     					uid=param.uid,
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
				$('.com_num').removeClass('none').text(data.message.all_count);	
				$.each(obj,function(i,item){
					comHtml+='<li><img src="'+item.avatar+'" alt="header" class="l"><div class="publisher r">';
					comHtml+='<p class="f16 color666 b lh3-3 m0">'+item.c_nickname+'<span class="r time lh3-3">'+item.created_at+'</span></p>';
					comHtml+='<p class="c8a f12">'+item.content+'</p></div><div class="clearfix"></div></li>';
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
					$('.com_num').addClass('none');
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

	}

};

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
/**实例化点播**/
function getVod(video_url, stop_time, userid, price,reward_long) {
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
            "width": 414, //播放器宽度，单位像素
            "height": 232, //播放器高度，单位像素
            "stop_time": stop_time,
            "disable_full_screen": 0,
            'live':false
        },
        {
            //播放状态
            // status 可为 {ready: "播放器已准备就绪",seeking:"搜索",
            // suspended:"暂停", playing:"播放中" , playEnd:"播放结束" , stop:"试看结束触发"
            
            'playStatus': function (status) {
            	
                if (status == 'playing') { //播放中            	
                    console.log('playing'); 
                    console.log(reward_long);        
                    v_time=setInterval(function (){
	                		duation++; 
	                		console.log(duation);                             		
	                		if(duation==(reward_long*60)&&reward_long!=0){    
	                			console.log('奖励');
	                			getReward(sharemark,'watch',userid,code);		                				                			                		
	                			clearInterval(v_time);
	                		}
            		},1000);//获取播放时长,数分钟后获得奖励   
                    // b_vedio=setTimeout(function (){getReward(sharemark,'watch',userid,relation_id)},60000);//十分钟后获得奖励      
                }
                if(status=='suspended'){
                	clearInterval(v_time);	
                }
                if (status == "playEnd") { //播放结束
                    console.log('end');
                   	clearInterval(v_time);
                }
                if (status == "stop") { //试看结束
                    showMessage(userid, price, 'video');
                   	clearInterval(v_time);
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
        $('.share_video .share_text').html('试看已结束<br>请点击屏幕底部"购买"按钮,购买本视频');
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