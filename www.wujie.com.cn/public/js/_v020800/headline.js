Zepto(function(){
		var args=getQueryStringArgs(),
            id = args['id'] || '0',
            uid = args['uid'] || '0',
			urlPath = window.location.href,
            origin_mark = args['share_mark'] || 0,//分销参数，分享页用
            code = args['code'] || 0;
		var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
        // 获取详情
		function getdetail(id,uid){
			var param={};
			param['id']=id;
            param['uid']=uid;
            if(shareFlag){
                param['guess']=1;
            }       
		var	url=labUser.api_path + '/news/detail/_v020800';
		ajaxRequest(param,url,function(data){
			if(data.status){
				if(data.message){
                //资讯详情
                    var conHtml='';
                    if((data.message.score_price == 0 || data.message.is_purchase == 1) && data.message.banner!=''){
                       conHtml+='<div class="banner mb0"><img class="banner_img" src="'+data.message.banner+'" /></div>';
                    };
                    conHtml+='<div class="intro"><h1 class="title fline" style="font-size:1.6rem">'+data.message.title+'</h1>';
                    conHtml+='<p class="some06"><span >作者：'+data.message.author+'</span><i>'+unix_to_fulltime(data.message.created_at)+'</i></p>';
                    conHtml+=' <p class="space"></p>';//灰色间隔
                    conHtml+=' <div class="detail detail0206"><p class=" f16 fline ptb1-4 b">详情介绍</p>';
                    conHtml+='<div class="content mb1 pr1-33" id="content">'+data.message.detail+'</div>';
                    if(data.message.score_price == 0 || data.message.is_purchase == 1){
                        conHtml+='<div class="tc c8a f12 ptb1-4 spread none" data-statu="0"> 剩余文章 (<span class="percent"></span>)</div></div></div>';
                        $('.fixed_btn').removeClass('none');
                    }else{
                        $('.banner').addClass('none');
                        conHtml+='<div class="tc c8a f12 ptb1-4 spread needbuy none" data-statu="1">阅读剩余文章请购买本文</div></div></div>';
                        conHtml+=' <div class="needpay f16 pl1-33 pr1-33 ">当前资讯为单独付费 &nbsp;<span class="color-red">';
                        conHtml+='<i class="b" id="scorePrice" >'+ data.message.score_price+'</i><i class="f12">积分</i></span>&nbsp;&nbsp;/人 <a class="r tobuy">立即购买</a></div>';
                    }
					if(data.message.banner==''){
						$('.banner').addClass('none');
					}else{
						$('.banner').html('<img src='+data.message.banner+' alt="banner">');
					}
                //点赞数量、评论数量
                    $('#zannum').text(data.message.count_zan);
                    $('#comment_num').html('评论('+data.message.count_comment+')');
                //相关品牌   
                    var brandHtml='';
                    if(data.message.type=='brand'){
                        brandHtml+='<div class="white-bg mt1-33 pl1-33 brand_rel"><p class="f16 fline ptb1-4 b">相关品牌</p>';
                        brandHtml+='<div class=" brand-company pl1-33 " data-brand='+data.message.brand.id+'>';
                        brandHtml+='<img src="'+data.message.brand.logo+'" alt="" class="company mr1-33 fl">';
                        brandHtml+='<div class="fl width70 "><em class="service f12 mr1">'+data.message.brand.category_name+'</em>';
                        brandHtml+='<span class="f14 b">'+data.message.brand.name+'</span><div class="brand-desc f12 color999 mb05 ui-nowrap-multi">'+data.message.brand.brand_summary+'</div>';
                        brandHtml+='<p class="f12 mb05"><span class="c8a">投资额：</span><span class="color-red">'+data.message.brand.investment_min+'~'+data.message.brand.investment_max+'万</span></p>';
                        if(data.message.brand.keywords.length>0){
                            $.each(data.message.brand.keywords,function(i,j){
                                brandHtml+=' <a class="tags-key border-8a-radius">'+j+'</a>';
                            })
                        };
                        brandHtml+='</div><div class="clearfix"></div></div></div>'
                    }
                    $('#container').html(conHtml+brandHtml).removeClass('none');
                    $('.brand_rel').css("margin-bottom",'10rem');
                    if(data.message.type!='brand'){
                        $('.detail').css('margin-bottom','10rem');
                    }
                    
                //猜你喜欢
                    if(shareFlag){
                        $('.needpay').addClass('none'); //分享页不显示购买按钮
                        if(data.message.type=='brand'){
                            var enjHtml='';
                            enjHtml+='<div class="white_bg  mt1-33"><div class="pl1-33"><p class="b f16 fline ptb1-4 mb0">猜你喜欢</p></div>';
                            $.each(data.message.guess_brands,function(i,j){
                                enjHtml+='<div class=" brand-company fline pl133" data-brand="'+j.id+'">';
                                enjHtml+='<img src="'+j.logo+'" alt="" class="company mr1-33 fl">';
                                enjHtml+=' <div class="fl width70 "><em class="service f12 mr1">'+j.category_name+'</em>';
                                enjHtml+='<span class="f14 b">'+j.name+'</span><div class="brand-desc f12 color999 mb05 ui-nowrap-multi">'+j.details+'</div>';
                                enjHtml+='<p class="f12 mb05"><span class="c8a">投资额：</span><span class="color-red">'+j.investment_min+'~'+j.investment_max+'</span></p>';
                                if(j.keywords.length>0){
                                    $.each(j.keywords,function(index,item){
                                        enjHtml+='<a class="tags-key border-8a-radius">'+item+'</a>';
                                    });  
                                };
                                enjHtml+=' </div><div class="clearfix"></div></div>';
                            });
                            enjHtml+='</div>';
                            $('.enjoy').append(enjHtml).removeClass('none'); 
                            $('.brand_rel').css("margin-bottom",'0');
                        }   
                        $('.loadapp').removeClass('none');
                        $('.fixed_btn').addClass('none');
                    };
                //是否点赞
                    if (data.message.is_zan=='1') {
                        $('.headzan').attr('disabled',true).removeClass('weizan_07').addClass('yizan_07');
                    }
                    $('#container').data('sharemark',data.message.share_mark);
                    $('#container').data('logo',data.message.logo);
//                  if(data.message.distribution_id==0 ){
//                      $('#share').addClass('none');
//                      $('#share').data('reward',0);
//                  }else{
//                      $('#share').removeClass('none');
//                      $('#share').data('reward',1);
//                  }
                    if(shareFlag){
//                      var headline='headID'+id;
//                      if($('#share').data('reward')==1&&(!localStorage.getItem(headline))){
//                          getReward(origin_mark,'view',0,code);
//                          localStorage.setItem(headline,id);    
//                      };
                        $('#installapp').removeClass('none');
                        $('#share').addClass('none');
                        $('#container').css('padding-top','3.2rem');
                        $('.loadapp').removeClass('none');
                        weixinShare(data.message,shareFlag);
                     }

                     twoMore();
                    
				}else{
					$('#container').html('null');
				}
			}	
		 })
		};

        //赞-活动
            $('.headzan').on('click', function () {
                var param = {};
                console.log(id);
                console.log(uid);
                param["id"] = id;
                param["uid"] = uid;
                var url = labUser.api_path + '/news/zan/_v020600';
                ajaxRequest(param, url, function (data) {
                    if (data.status) {
                        $('.headzan').attr('disabled',true).removeClass('weizan_07').addClass('yizan_07');
                        $('#zannum').text(data.message);
                    }
                    
                });
            });
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
             //提示 购买框
                $('.spread').click(function(){
                    var statu = $(this).attr('data-statu');
                        if(shareFlag){
                            alertShow('请至App中查看或购买');
                        }else if(statu == 0){
                            $('.content').html(conHtml);
                            $('.spread').addClass('none');
                        }else if(statu == 1){
                            return;
                        }
                });
        }
       

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
                                    wx.onMenuShareTimeline({
                                        title: obj.title, // 分享标题
                                        link:location.href, // 分享链接
                                        imgUrl: obj.logo, // 分享图标
                                        success: function () {
                                            // 用户确认分享后执行的回调函数
//                                          if($('#share').data('reward')==1){
//                                              sencondShare('relay')
//                                          }
                                        },
                                        cancel: function () {
                                            // 用户取消分享后执行的回调函数
                                        }
                                    });
                                    wx.onMenuShareAppMessage({
                                        title: obj.title,
                                        desc: nowhitespaceStr,
                                        link: location.href,
                                        imgUrl: obj.logo,
                                        trigger: function (res) {
                                            // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
                                         
                                        },
                                        success: function (res) {},
                                        cancel: function (res) {},
                                        fail: function (res) {}
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
                                window.location.href = 'https://itunes.apple.com/app/id981501194';
                            });
                            oppenIos();
                    }
                    else if (isAndroid) {
                        $(document).on('tap ', '#loadapp', function () {
                            window.location.href = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.tyrbl.wujiesq';
                        });
                        $(document).on('tap', '#openapp', function () {
                           openAndroid();
                       });
                        openAndroid();
                    }
            }

		};

		getdetail(id,uid);

        //点击相关品牌跳转
        $(document).on('tap','.brand-company',function(){
            var brand_id=$(this).attr('data-brand');
            if(shareFlag){
                window.location.href = labUser.path + "webapp/brand/detail/_v020700?id=" + brand_id + "&uid=" + uid + "&pagetag=08-9&is_share=1";
            }else{
                window.location.href = labUser.path + "webapp/brand/detail/_v020700?id=" + brand_id + "&uid=" + uid + "&pagetag=08-9";
            }
            
        })
        //评论页面
        $(document).on('click' ,'#comment_num' ,function(){
            window.location.href = labUser.path + 'webapp/headline/chat/_v020700?id='+id;
        })
        //关闭分享机制提醒
             $(document).on('tap','.close_share',function(){
                $('.share').addClass('none');
             });
        //了解更多分享机制
            // $(document).on('tap','.understand',function(){
            //     window.location.href=labUser.path+'webapp/protocol/moreshare/_v020700?pagetag=025-4';
            // })

        //积分购买
            $(document).on('tap','.tobuy',function(){
                var score = $('#scorePrice').text();
                toScore('news',score,id);
            })
       
        // 提示框
            function alertShow(content){
                $(".alert>p").text(content);
                $(".alert").css("display","block");
                setTimeout(function(){$(".alert").css("display","none")},2000);
           }; 

        // 二次分享先记录后奖励
            function sencondShare(type){
                var getcodeurl = labUser.api_path + '/index/code/_v020500';
                ajaxRequest({}, getcodeurl, function (data) {
                    var newcode = data.message;//code
                    var logsurl = labUser.api_path + "/share/share/_v020500";
                    ajaxRequest({
                        uid: '0',
                        content: 'news',
                        content_id: id,
                        source: 'weixin',
                        code:newcode,
                        share_mark: origin_mark
                    }, logsurl, function (data) {
                        getReward(origin_mark, type, 0, newcode);
                    });
                });
            };

        // var ifram = document.getElementById('ifram');
        //     if (isiOS) {
        //       var iframe_box = document.getElementById('iframe-box');
        //       iframe_box.style.width = 100 + '%';
        //       iframe_box.style.overflowX = 'hidden';
        //       iframe_box.style.overflowY = 'scroll';
        //       iframe_box.style.webkitOverflowScrolling = 'touch';
        //       ifram.setAttribute('scrolling', 'no');
        //       iframe_box.appendChild(ifram);
        //     }
	})
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