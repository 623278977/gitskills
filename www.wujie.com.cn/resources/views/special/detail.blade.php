@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/page_detail.css?v=1.0.0" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/government_detail.css?v=1.0.0" rel="stylesheet" type="text/css"/>

@stop
@section('main')
    <section id="act_container" class="none">
     <!--安装app-->
        <div class="app_install none" id="installapp">
            <i class="l">打开无界商圈APP，体验更多精彩内容 >></i>
            <span class="r" id="openapp"><img src="{{URL::asset('/')}}/images/install_btn.png" alt=""></span>
            <div class="clearfix"></div>
        </div>
	    <!-- 专版详情 -->
	    <!-- 广告bannner -->
	    <div class="zb_banner relative">
	        <!-- <img src="{{URL::asset('/')}}/images/act_banner.png" alt="" > -->
	        <img src="" alt="" >
	        <div class="zb_bannerBg">
	            <div id="zb_bannerBg-title">
	            	
	            </div>
	            
	        </div>
	    </div>
	    <!-- 简介 -->
	    <section class="zb">
	        <div class="zb_title">
	            <span class="l zb_t"><em></em>简介</span>
	            <span class="r f12 c9">点击 <a href="javascript:;" class="f12 c-blue a-rights">了解会员权益 </a>&nbsp;</span>
	        </div>
	        <p class="zb_p">
	           <span>
	          </span>
	        </p>
	        <div class="zb-fold hide" data-fold="0">
	        	展开 <i class="fold"></i>
	        </div>
	    </section>
	    <!-- 直播LIVE -->
	    <section class="zb zb-live">
	        <div class="zb_title">
	            <span class="l zb_t"><em></em>直播LIVE</span>
	        </div>
	        <div id="live-section">
	        	<div class="live_empty hide"></div>
	        </div>      
	        <div class="seen_more f14" id="zb-liveMore">更多直播<span class="sj_icon"></span></div>
	       
	    </section>
	    <!-- 活动预览 -->
	    <section class="zb">
	        <div class="zb_title">
	            <span class="l zb_t"><em></em>活动预览</span>
	        </div>
	        <div id="activity-section">
	            <div class="activity_empty hide"></div>
	         </div>
	        <div class="seen_more f14" id="zb-activityMore">更多活动<span class="sj_icon"></span></div>
	    </section>
	    <!-- 录播视频 -->
	    <section class="zb " id="">
	        <div class="zb_title">
	            <span class="l zb_t"><em></em>录播视频</span>
	        </div>
	        <div id="record-section">
	            <div class="video_empty hide"></div>
	        </div>  
	        <div class="seen_more f14" id="zb-videoMore">更多视频<span class="sj_icon"></span></div>
	    </section>
	    <!-- 其他专版 -->
	    <section class="zb" id="vips-section">
	        <div class="zb_title">
	            <span class="l zb_t"><em></em>其他专版</span>
	        </div>
	    </section>


	     <!--分享出去按钮-->
        <div class="fixed_btn weixin none" id="loadAppBtn">
            <button class="signup" id="loadapp">下载APP</button>
        </div>

        <!--浏览器打开提示-->
        <div class="safari none">
            <img src="{{URL::asset('/')}}/images/safari.png">
        </div>
        
    </section>
@stop

@section('endjs')
   <script>
   Zepto(function () {
	     new FastClick(document.body);
   	    $('.zb-fold').click(function () {
   	    	var me = $(this),
   	    	    atr = me.attr("data-fold");
   	    	if (atr ==0) {
   	    		$('section p.zb_p').css('max-height','none');
   	    		me.html("收起 <i class='unfold'></i>");
   	    		me.attr("data-fold",1);
   	    	}else{
   	    		$('section p.zb_p').css('max-height','13rem');
   	    		me.html("展开 <i class='fold'></i>");
   	    		$('.zb-fold').removeClass('aa');
   	    		me.attr("data-fold",0)
   	    	}
   	    });
   	   
   	    
   	    var urlPath = window.location.href,
   	        uid = labUser.uid;   
        var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;	       
   	    var param = {
            "vip_id":{{$vip_id}},
            "uid": "<?php echo isset($user->uid) && $user->uid > 0 ? $user->uid : $uid;?>",
            "position_id":{{$position_id}}

        }
            $('#zb-videoMore').data('video_id',param.vip_id);
            $('#zb-liveMore').data('video_id',param.vip_id);
            $('#zb-activityMore').data('video_id',param.vip_id);
       
       
		var specialDetail = {
            detail: function (vip_id, uid ,agreement,isshare) {
                var param = {};
                param["vip_id"] = vip_id;
                param["uid"] = uid;
                param['attach']=1;
                param["agreement"] = agreement;
                var url = labUser.api_path + '/vip/detail';
                // var url = '/api/vip/detail';
                ajaxRequest(param, url, function (data) {
                    if (data.status) {
                        //html调整
                        getSpecialDetail(data.message,isshare);
                    }
                });
            },
            resourse:function (vip_id,uid,actnum,videonum,livenum,vipnum,position_id,isshare) {
            	var param = {};
                param["vip_id"] = vip_id;
                param["uid"] = uid;
                param['resource']={
                	activity:actnum,
                	video:videonum,
                	live:livenum,
                	vip:vipnum
                };
                param["position_id"] = position_id;
                var url = labUser.api_path + '/vip/recommend';
                // var url = '/api/vip/recommend';
                ajaxRequest(param, url, function (data) {
                	if (data.status) {
                		getResourseDteail(data.message,isshare);
                	}
                })
            }
        };


        specialDetail.detail(param.vip_id,param.uid,1,shareFlag);
       specialDetail.resourse(param.vip_id,param.uid,4,4,4,4,param.position_id,shareFlag);	

        function getSpecialDetail(result,is_flag) {
        	// setPageTitle(result.name);
        	if (is_flag) {
            	$('#loadAppBtn').removeClass('none');
                $('#installapp').removeClass('none');
           	    str+= '<a href="javascript:;" class="bemember ">成为会员</a>';
           	    //浏览器判断
                if (is_weixin()) {
                    /**微信内置浏览器**/
                    $(document).on('tap', '#loadapp,#openapp', function () {
                        var _height = $(document).height();
                        $('.safari').css('height', _height);
                        $('.safari').removeClass('none');
                    });
                    //点击隐藏蒙层
                    $(document).on('tap', '.safari', function () {
                        $(this).addClass('none');
                    });
                }
                else {
                    if (isiOS) {
                        //打开本地app
                        $(document).on('tap', '#openapp', function () {
                            var strPath = window.location.pathname.substring(1);
                            var strParam = window.location.search;
                            var appurl = strPath + strParam;
                            var share = '&is_share';
                            var appurl2 = appurl.substring(0, appurl.indexOf(share));
                            window.location.href = 'openwjsq://' + appurl2;
                        });
                        /**下载app**/
                        $(document).on('tap', '#loadapp', function () {
                            window.location.href = 'https://itunes.apple.com/app/id981501194';
                        });
                    }
                    else if (isAndroid) {
                        $(document).on('tap', '#loadapp', function () {
                            window.location.href = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.tyrbl.wujiesq';
                        });
                        $(document).on('tap', '#openapp', function () {
                            window.location.href = 'openwjsq://welcome';
                        });
                    }
                }
                $('.zb_banner img').attr('src',result.poster);
                // var url2 = window.location.href.split('special')[0];
                //     url2=url2+'rights/detail'+'?'+'vip_id='+param.vip_id;
                $('.a-rights').click(function () {
                    alert('请打开app查看');
                });
                var str='';
                    str+= '<div class="l">';
                    str+= '<span id="resultName">'+result.name+'</span>';
                    str+= '<span>'+result.subtitle+'</span>';
                    str+= '</div>';
                    str+= '<div class="r">';
                    
                    str+= '<a href="javascript:;" class="bemember ">成为会员</a>';
                   
                    str+='</div>';
                $('#zb_bannerBg-title').html(str);
                $('.bemember').click(function () {
                    alert('请打开app查看');
                });
                $('p.zb_p span').html(result.detail);   
                if ($('p.zb_p span').height()>=118) {
                    $('.zb-fold').removeClass('hide');
                }
               
                //如果没有视频，显示为空图
                if (result.live_count ==0) {
                    $('.live_empty').removeClass('hide');
                    $('#zb-liveMore').addClass('hide');
                    $('#live-section').css('padding-bottom','3rem');
                };
                if (result.activity_count ==0) {
                    $('.activity_empty').removeClass('hide');
                    $('#zb-activityMore').addClass('hide');
                    $('#activity-section').css('padding-bottom','3rem');
                };
                if (result.video_count ==0) {
                    $('.video_empty').removeClass('hide');
                    $('#zb-videoMore').addClass('hide');
                    $('#record-section').css('padding-bottom','3rem');
                };      
            }
            else{
                 setPageTitle(result.name);
                $('.zb_banner img').attr('src',result.poster);
                var url2 = window.location.href.split('special')[0];
                    url2=url2+'rights/detail'+'?'+'vip_id='+param.vip_id;
                $('.a-rights').attr('href',url2);
                var str='';
                    str+= '<div class="l">';
                    str+= '<span id="resultName">'+result.name+'</span>';
                    str+= '<span>'+result.subtitle+'</span>';
                    str+= '</div>';
                    str+= '<div class="r">';
                   
                        if (result.is_valid == 0 || param.uid==0){
                            str+= '<a href="javascript:;" class="bemember ">成为会员</a>';
                        }else if (result.is_valid == 1) {
                           str+= '<a href="javascript:;" class="unmember zb "><i class="icon_zb-logo"></i>专版会员</a>';
                        }
                    
                    str+='</div>';
                $('#zb_bannerBg-title').html(str);
                $('p.zb_p span').html(result.detail);   
                if ($('p.zb_p span').height()>=118) {
                    $('.zb-fold').removeClass('hide');
                }
               
                //如果没有视频，显示为空图
                if (result.live_count ==0) {
                    $('.live_empty').removeClass('hide');
                    $('#zb-liveMore').addClass('hide');
                    $('#live-section').css('padding-bottom','3rem');
                };
                if (result.activity_count ==0) {
                    $('.activity_empty').removeClass('hide');
                    $('#zb-activityMore').addClass('hide');
                    $('#activity-section').css('padding-bottom','3rem');
                };
                if (result.video_count ==0) {
                    $('.video_empty').removeClass('hide');
                    $('#zb-videoMore').addClass('hide');
                    $('#record-section').css('padding-bottom','3rem');
                };      
            }
           
        	
        }       
        //返回数据
        function getResourseDteail(result,is_flag) {
            
        	//返回直播数据
        	$.each(result.lives,function (i,item) {
                var timestamp=new Date().getTime();
                if (item.end_time_stamp < timestamp) {
                   $('.live_empty').removeClass('hide');
                    $('#zb-liveMore').addClass('hide');
                    $('#live-section').css('padding-bottom','3rem');
                }else{
                    var str = '';
                    str+='<div class="massive" data-live="'+item.id+'">';
                    str+='<img src="'+item.list_img+'" alt="">';
                    str+='<div class="l">';
                    str+='<div class="name">'+item.subject+'</div>';
                    str+='<div class="clearfix"></div>';
                    if (item.is_being == 1) {
                        str+= '<i class="icon icon_zblive"></i><i class="seen_icon"></i>'+item.view+'';
                    }else if (item.is_being == 0) {
                        str+='<span>'+item.begin_time_format +' </span> <span> 开始直播</span>';
                    }
                    str+='</div>';
                    str+='<div class="clearfix"></div>';
                    str+='</div>';
                    $('#live-section').append(str);
                }
        	});
        	
        	//返回录播数据
        	$.each(result.videos,function (i,item) {
        		var strvideo='';
	        	strvideo+=[
	        	    '<div class="massive" data-video="'+item.id+'">',
			            '<img src="'+item.image+'" alt="">',
			            '<div class="l">',
			                '<div class="name">'+item.subject+'</div>',
			                '<div class ="clearfix"></div>',
			                '<p class="p-over">'+item.description+'</p>',
			                '<div class="clearfix"></div>',
			            '</div>',
			            '<div class="clearfix"></div>',
			            '<div class="more">',
			                '<div class="l"><div class="lubo-img"><img src="'+item.small_image+'" alt=""></div>'+item.type+'</div>',
			                '<div class="r"><i class="seen_icon"></i>'+item.view+' <i class="collect_icon"></i>'+item.favorite_count+'</div>',
			            '</div>',
			            '<div class="clearfix"></div>',
			        '</div>'
			    ].join('');
			    $('#record-section').append(strvideo);
			   
        	});

    		//返回活动预览数据
    		$.each(result.activities,function (i,item) {
                if (uid==0) {
                    var str='';
                    str+= '<div class="massive relative about_act" data-act="'+item.id+'" data-maker="'+item.maker_id+'">';
                    str+='<img src="'+item.list_img+'" alt="">';
                    str+='<div class="l">';
                    str+='<div class="act_name">'+item.subject+'</div>';
                    str+='<div class="clearfix"></div>';
                    for (var i = 0; i < (item.host_cities).length; i++) {
                        if(item.city==item.host_cities[i]){
                            item.host_cities.splice(i,1);
                        }
                    };
                    item.host_cities = (item.host_cities).join(" ").replace(/,/gi," ");
                  
                    str+='<p class=" f12">'+item.begin_time+ ' <span class="">'+item.city+'</span> <span class="cities">'+item.host_cities+'</span></p>';
                    str+='<div class="money_collect">';
                    if (item.price == 0) {
                        str+='<span class="money green"><em class="f18">免费 </em></span>'
                    }else{
                        str+='<span class="money orange">￥<em class="f18">'+item.price+'</em>起 </span>';
                    }
                    str+='<span class="collect"><i class="seen_icon"></i><em>'+item.view+'</em></span>';
                    str+='</div>';
                    str+='</div>';
                    // if (item.category=='A') {
                    //     str+='<div class="mas-act"><img src="{{URL::asset('/')}}/images/ovopic.png">活动场地举办有你入驻的本地商圈 <span class="green">'+result.maker_name+'</span></div>';
                    // }else if (item.category=='B') {
                    //     str+='<div class="mas-act"><img src="{{URL::asset('/')}}/images/ovopic.png">活动场地举办有你的城市 <span class="green">'+item.city+'</span> 快来看看吧</div>';
                    // }
                    if (item.is_recommend == 1) {
                        str+='<div class="recommend"></div>';
                    }else{
                        str+='';
                    }
                    str+='<div class="clearfix"></div>';
                    str+=' </div>';
                    $('#activity-section').append(str);   
                }else{
                    var str='';
                    str+= '<div class="massive relative about_act" data-act="'+item.id+'" data-maker="'+item.maker_id+'">';
                    str+='<img src="'+item.list_img+'" alt="">';
                    str+='<div class="l">';
                    str+='<div class="act_name">'+item.subject+'</div>';
                    str+='<div class="clearfix"></div>';
                    for (var i = 0; i < (item.host_cities).length; i++) {
                        if(item.city==item.host_cities[i]){
                            item.host_cities.splice(i,1);
                        }
                    };
                    item.host_cities = (item.host_cities).join(" ").replace(/,/gi," ");
                    if (item.category=='C') {
                        str+='<p class="dark_gray f12">'+item.begin_time+ ' <span class="">'+item.city+'</span> <span class="cities">'+item.host_cities+'</span></p>';
                    }else{
                        str+='<p class=" f12">'+item.begin_time+ ' <span class="green">'+item.city+'</span> <span class="cities">'+item.host_cities+'</span></p>';
                    }
                    str+='<div class="money_collect">';
                    if (item.price == 0) {
                        str+='<span class="money green"><em class="f18">免费 </em></span>'
                    }else{
                        str+='<span class="money orange">￥<em class="f18">'+item.price+'</em>起 </span>';
                    }
                    str+='<span class="collect"><i class="seen_icon"></i><em>'+item.view+'</em></span>';
                    str+='</div>';
                    str+='</div>';
                    if (item.category=='A') {
                        str+='<div class="mas-act"><img src="{{URL::asset('/')}}/images/ovopic.png">活动场地举办有你入驻的本地商圈 <span class="green">'+result.maker_name+'</span></div>';
                    }else if (item.category=='B') {
                        str+='<div class="mas-act"><img src="{{URL::asset('/')}}/images/ovopic.png">活动场地举办有你的城市 <span class="green">'+item.city+'</span> 快来看看吧</div>';
                    }
                    if (item.is_recommend == 1) {
                        str+='<div class="recommend"></div>';
                    }else{
                        str+='';
                    }
                    str+='<div class="clearfix"></div>';
                    str+=' </div>';
                    $('#activity-section').append(str);     
                    }				       	       
                	
    		});
    	    
            // 返回其他专版数据           
        	$.each(result.vips,function (i,item) {
        		if (result.vips.length ==0 ) {
	        		$('#vips-section').addClass('hide');
	        	}
        		var strvips='';
	        	strvips += [
	        	     '<div class="massive relative" data-vips="'+item.id+'">',
			            '<img src="'+item.poster+'" alt="">',
			            '<div class="l">',
			                '<div class="act_name">'+item.name+'</div>',
			                '<div class="clearfix"></div>',
			                '<p class="dark_gray f12">'+item.subtitle+'</p>',
                            '<div class="other-zb">',
			                '<i class="icon icon_hd"></i> 活动:'+item.activity_count+'场&nbsp&nbsp&nbsp&nbsp;<i class="icon icon_zb"></i> 直播:'+item.live_count+'场&nbsp&nbsp&nbsp&nbsp;<i class="icon icon_lb"></i> 录播:'+item.video_count+'场',
                            '</div>',
			                '<div class="clearfix"></div>',
			            '</div>',
			            '<div class="clearfix"></div>',
			        '</div>'
			    ].join('');
			    $('#vips-section').append(strvips);
        	});

            if (is_flag) {
                  //点击更多视频
                $(document).on('click', '#zb-videoMore', function () {
                   alert('请打开app查看');
                });

                //更多活动
                $(document).on('click', '#zb-activityMore', function () {
                  alert('请打开app查看');
                });
                 //更多详情（直播）
                $(document).on('click', '#zb-liveMore', function () {
                  alert('请打开app查看');
                });
                //直播跳转
                $(document).on('click', '#live-section .massive', function () {
                    var live_id = $(this).data('live');
                    window.location.href=labUser.path+'webapp/live/detail?&pagetag=04-9&id='+live_id+'&uid='+param.uid+'&is_share=1';
                });
                // 活动跳转
                $(document).on('click', '#activity-section .massive', function () {
                    var live_id = $(this).data('act');
                    var live_makerid = $(this).data('maker');
                    window.location.href=labUser.path+'webapp/activity/detail?&pagetag=02-2&id='+live_id+'&makerid='+live_makerid+'&uid='+param.uid+'&position_id='+param.position_id+'&is_share=1';
                });
                // 录播跳转
                $(document).on('click', '#record-section .massive', function () {
                    var live_id = $(this).data('video');
                    window.location.href=labUser.path+'webapp/vod/detail?&pagetag=05-4&id='+live_id+'&uid='+param.uid+'&is_share=1';
                });
                // 其他跳转
                $(document).on('click', '#vips-section .massive', function () {
                    var live_id = $(this).data('vips');
                    window.location.href=labUser.path+'webapp/special/detail?&pagetag=02-1&vip_id='+live_id+'&uid='+param.uid+'&is_share=1';
                });

            }else{
                //点击更多视频
                $(document).on('click', '#zb-videoMore', function () {
                   var zb_video = $(this).data('video_id');
                   var zb_name = $('#resultName').html();
                   zbVideoMore(zb_video,zb_name);
                });

                //更多活动
                $(document).on('click', '#zb-activityMore', function () {
                   var zb_activity = $(this).data('video_id');
                   var zb_position=param.position_id;
                   var zb_name = $('#resultName').html();
                   zbActivityMore(zb_activity,zb_name,zb_position);
                });
                 //更多详情（直播）
                $(document).on('click', '#zb-liveMore', function () {
                   var zb_live = $(this).data('video_id');  
                   var zb_name = $('#resultName').html();
                   zbLiveMore(zb_live,zb_name);
                });
                // 点击列表里的模块跳转到详情
                //直播跳转
                $(document).on('click', '#live-section .massive', function () {
                    var live_id = $(this).data('live');
                    window.location.href=labUser.path+'webapp/live/detail?&pagetag=04-9&id='+live_id+'&uid='+param.uid;
                });
                // 活动跳转
                $(document).on('click', '#activity-section .massive', function () {
                    var live_id = $(this).data('act');
                    var live_makerid = $(this).data('maker');
                    window.location.href=labUser.path+'webapp/activity/detail?&pagetag=02-2&id='+live_id+'&makerid='+live_makerid+'&uid='+param.uid+'&position_id='+param.position_id;
                });
                // 录播跳转
                $(document).on('click', '#record-section .massive', function () {
                    var live_id = $(this).data('video');
                    window.location.href=labUser.path+'webapp/vod/detail?&pagetag=05-4&id='+live_id+'&uid='+param.uid;
                });
                // 其他跳转
                $(document).on('click', '#vips-section .massive', function () {
                    var live_id = $(this).data('vips');
                    window.location.href=labUser.path+'webapp/special/detail?&pagetag=02-1&vip_id='+live_id+'&uid='+param.uid;
                });
            }

            //如果没有视频，显示为空图
                if (result.lives.length ==0) {
                    $('.live_empty').removeClass('hide');
                    $('#zb-liveMore').addClass('hide');
                    $('#live-section').css('padding-bottom','3rem');
                };
                // if (result.activities.length ==0) {
                //     $('.activity_empty').removeClass('hide');
                //     $('#zb-activityMore').addClass('hide');
                //     $('#activity-section').css('padding-bottom','3rem');
                // };
                if (result.videos.length ==0) {
                    $('.video_empty').removeClass('hide');
                    $('#zb-videoMore').addClass('hide');
                    $('#record-section').css('padding-bottom','3rem');
                };      
        
        
        }
      

        //成为会员
        $(document).on('click','.bemember ,.unmember',function () {
        	var zb_id = $('#zb-videoMore').data('video_id');
        	zbMember(zb_id);
        });
        function zbMember(video_id) {
		    if (isAndroid) {
		        javascript:myObject.zbMember(video_id);
		    } else if (isiOS) {
		        var data = {
		            "moreId": video_id,
		            // "moreName":zb_name
		        }
		        window.webkit.messageHandlers.zbMember.postMessage(data);
		    }
		};
         $('#act_container').removeClass('none');

		
       
   })
   </script>
   <script>	
   		//分享
        function showShare() {
            // shareOut('title', window.location.href, '', 'header', 'content');
            var title = $('#resultName').text();
            var url = window.location.href;
            var img = $('.zb_banner>img').attr('src');
            var header = '专版';
            var content = cutString($('.zb_p span').text(), 18);
            shareOut(title, url, img, header, content);
        };
        // title
        function setPageTitle(title) {
            if (isAndroid) {
                javascript:myObject.setPageTitle(title);
            } 
            else if (isiOS) {
                var data = {
                   "title":title
                }
                window.webkit.messageHandlers.setPageTitle.postMessage(data);
            }
        }
   </script>
@stop