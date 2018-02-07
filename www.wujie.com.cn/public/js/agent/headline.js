//zhangxm
Zepto(function(){
		new FastClick(document.body);
		$('body').css('background','#f2f2f2');
		var args=getQueryStringArgs(),
            id = args['id'] || '0',
            uid = args['agent_id'] || '0',
            brand_id = args['is_brand'],
            isShare = args['is_share'],
			urlPath = window.location.href,
            origin_mark = args['share_mark'] || 0,//分销参数，分享页用
            code = args['code'] || 0;
		var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
		var isBrand = urlPath.indexOf('is_brand') > 0 ? true : false;
		if(shareFlag){
			$(document).ready(function(){
				$('title').text('资讯详情');
			});
		}
		if(isBrand){
			var paramd = {};
			paramd['type']='news';
			paramd['agent_id']= uid;
			paramd['brand_id']= brand_id;
			paramd['post_id'] = id;
			var url = labUser.agent_path + '/brand/apply-status/_v010000';
			ajaxRequest(paramd,url,function(data){
				if(data.status){
					if(data.message){
					}
				}
			});
		};
		
        // 获取详情
		function getdetail(id,uid){
			var param={};
			param['id']=id;
            param['uid']=uid;
            if(shareFlag){
                param['guess']=1;
            }       
		var	url=labUser.agent_path + '/news/detail/_v010000';
		ajaxRequest(param,url,function(data){
			if(data.status){
				if(data.message){
                //资讯详情
                    var conHtml='';
                    if(data.message.banner!=''){
                       conHtml+='<div class="banner mb0"><img class="banner_img" src="'+data.message.banner+'" /></div>';
                    };
                    conHtml+='<div class="intro"><h1 class="title f15 fline  b">'+data.message.name+'</h1>';
                    conHtml+='<p class="some06 f12"><span >作者：'+data.message.author+'</span><i>'+data.message.created_at+'</i></p>';
                    conHtml+=' <p class="space"></p>';//灰色间隔
                    conHtml+=' <div class="details ">';
                    conHtml+='<div class="content pr1-33 f12 medium color999 detail0206" id="content">'+data.message.detail+'</div>';
      
					conHtml+='<div class="fline" style="width:100%"></div>';
                
                //相关品牌   
                    var brandHtml='';
                    if(data.message.type=='brand'){
//                      brandHtml+='<div class="white-bg mt1-33 pl1-33 brand_rel"><p class="f16 fline ptb1-4 b">相关品牌</p>';
                        brandHtml+='<div class="brand-company pl1-33 " data-brand='+data.message.brand.id+'>';
                        brandHtml+='<img src="'+data.message.brand.logo+'" alt="" class="mr1-33 xm-company">';
                        brandHtml+='<div class="mb1-5 f1"><em class="service f12 mr1 xm-service">'+data.message.brand.category_name+'</em>';
                        brandHtml+='<span class="f14 b">'+data.message.brand.name+'</span><div class="f12 mt05 color999 mb05 ui-nowrap-multi">'+data.message.brand.brand_summary+'</div>';
                        brandHtml+='<p class="f12 mb1 "><span class="c8a">投资额：</span><span class="color-red">'+data.message.brand.investment_min+'~'+data.message.brand.investment_max+'万</span></p>';
                        if(data.message.brand.keywords){
                        	if(data.message.brand.keywords.length>0){
	                            $.each(data.message.brand.keywords,function(i,j){
	                                brandHtml+=' <a class="xm-tags-key f12 color999 mr1 ui-border-radius-8a border-8a-radius">'+j+'</a>';
	                            })
	                        };
                        }
                        
                        brandHtml+='</div></div><div class="clearfix"></div>';
                        brandHtml+='<div class="fline" style="width:100%"></div></div>';
                    }
                    $('#container').html(conHtml+brandHtml).removeClass('none');
                    $('.brand_rel').css("margin-bottom",'10rem');
                    if(data.message.type!='brand'){
                        $('.detail').css('margin-bottom','10rem');
                    };
                //是否点赞
                    if (data.message.is_zan=='1') {
                        $('.headzan').attr('disabled',true).removeClass('weizan_07').addClass('yizan_07');
                    }
//                  $('#container').data('sharemark',data.message.share_mark);
                    if(data.message.logo!=''){
                    	$('#container').attr('logo',data.message.logo);
                    }else {
                    	$('#container').attr('logo',data.message.banner);
                    };
                    //点赞
                    $('.fixed_btn').removeClass('none');
                    //点赞数量、评论数量
                    $('#zannum').text(data.message.zans);
                    $('#comment_num').html('评论('+data.message.comments+')');
                    //点赞
                    getpict('.content');
		            $('.headzan').on('click', function () {
		                var param = {};
		                var zan_num = $('#zannum').text();
		                param["id"] = id;
		                param["uid"] = uid;
		                var url = labUser.agent_path + '/comment/news-add-zan/_v010001';
		                ajaxRequest(param, url, function (data) {
		                    if (data.status) {
		                        $('.headzan').attr('disabled',true).removeClass('weizan_07').addClass('yizan_07');
								zan_num++;	
		                        $('#zannum').html(zan_num);
		                    }
		                    
		                });
		            });
                    
                    if(shareFlag){
                    	if(isShare==1){
                    		$('#installapp').removeClass('none');
	                        $('#share').addClass('none');
	                        $('#container').css('padding-top','3.2rem');
	                        $('.loadapp').removeClass('none');
	                        weixinShare(data.message,shareFlag);
                    	}
                        
                    };
                    $('#content').attr('summary',data.message.share_summary);
//                  if(id==257){
//                      $('#content').css('position','relative').append('<div class="load_now"></div>')
//                  };
                    
				}else{
					$('#container').html('null');
				}
			}else {
				tips(data.message);
			}
		 })
		};
            //转发 分享
            $('.zhuan').on('click', function () {
                showShare();
            });
   		//默认展示两屏，超过两屏显示展示全文
        function twoMore(){
            var imgLength=$('#content img').length;
            var conHtml=$('#content').html();
            if(imgLength > 0){
                $('#content img').each(function(){
                    $(this).load(function(){
                        imgLength--;
                        console.log(imgLength);
                        if(imgLength==0){
                           overHide();
                        }
                    })
                });
            }else{
                overHide();
            }
            

            //超过两屏隐藏
            function overHide(){
                var showHeight=2*window.screen.height;//屏幕两倍高度
                var eleArr=$('#content').children();
                var contentHeight=$('#content').outerHeight(true);
                // var imgArr=$('#content').find('img');
                var conHtml=$('#content').html();
                var eleHeight=0;
                var eleHtml=conHtml , percent= 0;
                if(eleArr.length == 0){
                    if(contentHeight > showShare){
                        percent = parseInt(1-(showShare/contentHeight)*100);
                        $('.percent').html(percent+'%');
                        $('.spread').removeClass('none');
                        $('#content').css({'height':showShare,'overhide':'hide'});
                    }else{
                        return;
                    };
                }else{
                    eleHtml = '';
                    for(var i=0;i<eleArr.length;i++){
                        eleHeight+=$(eleArr[i]).outerHeight(true);  
                        if(eleHeight>showHeight){ //元素高度累加大于两屏时
                            if($(eleArr[i]).is(':has(img)')){
                                eleHeight-=$(eleArr[i]).outerHeight(true);
                            };
                            percent=parseInt((1-(eleHeight/contentHeight))*100); //显示的元素高度占总高度的百分比
                            if(percent>0){
                                $('.percent').html(percent+'%');
                                $('.spread').removeClass('none');
                            }
                            break;
                        }else{
                            $('.spread').addClass('none');
                        }
                        eleHtml+=eleArr[i].outerHTML;
                    };
                }
                $('.content').html(eleHtml);
            }
        };
       
       

		//二次分享
		function weixinShare(obj,is_share){
			if(is_share&&is_weixin()){
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
				var wxurl = labUser.api_path + '/weixin/js-config';
                        //详情描述
                        var desptStr = removeHTMLTag(obj.detail);
                        var nowhitespace = desptStr.replace(/&nbsp;/g,'');
                        var despt = cutString(desptStr, 60);
                        var nowhitespaceStr =cutString(nowhitespace, 60);
                        // var num=window.location.href.indexOf('from=singlemessage');
                        // var w_url=window.location.href.substring(0,num-1);
                        // var w_url=encodeURIComponent(window.location.href);
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
                                	//分享到朋友圈
                                    wx.onMenuShareTimeline({
                                        title: obj.title, // 分享标题
                                        link:location.href, // 分享链接
                                        imgUrl: obj.logo, // 分享图标
                                        success: function () {
                                            // 用户确认分享后执行的回调函数
                                            
                                        },
                                        cancel: function () {
                                            // 用户取消分享后执行的回调函数
                                        }
                                    });
                                    //分享给朋友
                                    wx.onMenuShareAppMessage({
                                        title: obj.title,
                                        desc: nowhitespaceStr,
                                        link: location.href,
                                        imgUrl: obj.logo,
                                        trigger: function (res) {
                                            // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
//                                          console.log('用户点击发送给朋友');
                                        },
                                        success: function (res) {
                                            console.log('已分享');
                                            
                                        },
                                        cancel: function (res) {
//                                          console.log('已取消');
                                        },
                                        fail: function (res) {
//                                          console.log(JSON.stringify(res));
                                        }
                                    });
                                });
                            }
                        });
			}else{
                if (isiOS) {
                            //打开本地app
                            $(document).on('tap', '#openapp', function () {
                                oppenIos();
                            });
                            /**下载app**/
                            $(document).on('tap', '#loadapp', function () {
                                window.location.href = 'https://itunes.apple.com/cn/app/id1282277895';
                            });
//                          oppenIos();
                    }
                    else if (isAndroid) {
                        $(document).on('tap ', '#loadapp', function () {
                            window.location.href = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.tyrbl.agent';
                        });
                        $(document).on('tap', '#openapp', function () {
                           openAndroid();
                       });
//                      openAndroid();
                    }
            }

		};

		getdetail(id,uid);

        //资讯为首页介绍时，点击立即下载
        $(document).on('click','.load_now',function(){
            if(is_weixin()){
                 var _height = $(document).height();
                    $('.safari').css('height', _height);
                    $('.safari').removeClass('none');
            }
            if(isAndroid){
                // window.location.href = 'http://passport.wujie.com.cn/down/Wujiesq-self-release-2.8.0.10.apk';
                window.location.href = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.tyrbl.agent';
            }else if(isiOS){
                window.location.href = 'https://itunes.apple.com/cn/app/id1282277895';
            };

        })
        //点击隐藏蒙层
        $(document).on('tap', '.safari', function () {
            $(this).addClass('none');
        });
        //点击相关品牌跳转
        $(document).on('click','.brand-company',function(){
            var brand_id=$(this).attr('data-brand');
            if(shareFlag){
                window.location.href = labUser.path + "webapp/agent/brand/detail?id=" + brand_id + "&agent_id=" + uid + "&is_share=1";
            }else{
            	onAgentEvent('brand_detail','',{'type':'brand','id':brand_id,'userId':uid,'position':'5'})
                window.location.href = labUser.path + "webapp/agent/brand/detail?id=" + brand_id + "&agent_id=" + uid;
            }
        });

        //评论页面
        $(document).on('click' ,'#comment_num' ,function(){
            window.location.href = labUser.path + 'webapp/agent/headline/agentchat?id='+id+'&agent_id='+uid;
        })
        //关闭分享机制提醒
             $(document).on('tap','.close_share',function(){
                $('.share').addClass('none');
             });
        //了解更多分享机制
//          $(document).on('tap','.understand',function(){
//              window.location.href=labUser.path+'webapp/protocol/moreshare/_v020700?pagetag=025-4';
//          })

       
        // 提示框
            function alertShow(content){
                $(".alert>p").text(content);
                $(".alert").css("display","block");
                setTimeout(function(){$(".alert").css("display","none")},2000);
           }; 

//禁用长按弹出菜单事件
$('img').bind('contextmenu', function(e) {
  e.preventDefault();
})        
	});
//打开本地--Android
function openAndroid(){
    var strPath = window.location.pathname;
    var strParam = window.location.search.replace(/is_share=1/g, '');
    var appurl = strPath + strParam;
    window.location.href = 'openagent://welcome' + appurl;
}
function oppenIos(){
    var strPath = window.location.pathname.substring(1);
    var strParam = window.location.search;
    var appurl = strPath + strParam;
    var share = '&is_share';
    var appurl2 = appurl.substring(0, appurl.indexOf(share));
    window.location.href = 'openagent://' + appurl2;
};
            