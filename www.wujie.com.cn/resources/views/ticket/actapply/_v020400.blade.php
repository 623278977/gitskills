@extends('layouts.default')
@section('css')
	<link href="{{URL::asset('/')}}/css/my_detial.css" rel="stylesheet" type="text/css" />
	<link href="{{URL::asset('/')}}/css/act_detail.css?v=1.0.0" rel="stylesheet" type="text/css"/>
	<link href="{{URL::asset('/')}}/css/w-pages.css" rel="stylesheet" type="text/css" />
@stop
@section('main')
        <!-- 打开APP -->
        <div class="app_install none" id="installapp">
            <i class="l">打开无界商圈APP，体验更多精彩内容 >></i>
            <span class="r" id="openapp"><img src="{{URL::asset('/')}}/images/install_btn.png" alt=""></span>
            <div class="clearfix"></div>
        </div>
	<section id="ticketSection">
	    <div class="container" id="ticketList" style="padding-bottom: 6rem">
	    		<div class="code-box">
		            <section class="section-box tc relative" id="ticket_free">
					</section>
				</div>
				<div class="ticket-live tc color999 none" id="fold-ticket" flag="fold">展开付费门票</div>
				<div class="code-box">
		            <section class="section-box tc relative" id="ticket_pay">
					</section>
				</div>
	    </div>
	   <!--  <div class="container" id="ticketList">
	        <div class="ticket-live">
	            <img src="{{URL::asset('/')}}/images/wujie_icon.png" alt="">直播票
	        </div>
	        <div class="code-box" id="fadepic">
	            <section class="section-box tc relative">
	                <a href="#">
	                    <div class="tc-box tc c">
	                        <div class="tc-border bd-orange">
	                            <div class="left">
	                                <span class=" f16">现场票</span>
	                                <p class="f12">含门票、VIP坐席、午餐。</p>
	                                <p class="f12">具体坐席、午餐安排依据现场工作安排为准。</p>
	                            </div>
	                            <div class="right f14">
	                                <div class="buy">
	                                    ¥<em class="f18">99</em>
	                                    <p>购票</p>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                </a>
	            </section>
	        </div>
	        <div class="ticket-live">
	            <img src="{{URL::asset('/')}}/images/address_icon2.png" alt="">现场票
	        </div>
	        <div class="code-box">
	            <section class="section-box tc relative">
	                <a href="#">
	                    <div class="tc-box tc c">
	                        <div class="tc-border bd-green">
	                            <div class="left">
	                                <span class=" f16">免费票</span>
	                                <p class="f12">报名此票需经过主办方审核</p>
	                                <p class="f12">原价99元无界币，活动期间免费</p>
	                            </div>
	                            <div class="right f14">
	                                <div class="buy">
	                                    <em class="f18">免费</em>
	                                    <p>购票</p>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                </a>
	            </section>
	        </div>
	        <div class="code-box">
	            <section class="section-box tc relative">
	                <a href="#">
	                    <div class="tc-box tc c">
	                        <div class="tc-border bd-blue">
	                            <div class="left">
	                                <span class=" f16">VIP票</span>
	                                <p class="f12">含门票、VIP坐席、午餐。</p>
	                                <p class="f12">具体坐席、午餐安排依据现场工作安排为准。</p>
	                            </div>
	                            <div class="right f14">
	                                <div class="buy">
	                                    ¥<em class="f18">199</em>
	                                    <p>购票</p>
	                                </div>
	                            </div>
	                        </div>
	                    </div>
	                </a>
	            </section>
	        </div>
	        <div class="code-box">
	            <section class="section-box tc relative">
	                <div class="tc-box tc c">
	                    <div class="tc-border bd-gray">
	                        <div class="left">
	                            <span class=" f16">VIP票</span>
	                            <p class="f12">含门票、VIP坐席、午餐。</p>
	                            <p class="f12">具体坐席、午餐安排依据现场工作安排为准。</p>
	                        </div>
	                        <div class="right f14">
	                            <div class="buy">
	                               <em class="f18">已截止</em>
	                            </div>
	                        </div>
	                    </div>
	                </div>
	            </section>
	        </div>
		</div> -->
		 <!--分享出去按钮-->
        <div class="fixed_btn weixin none" id="loadAppBtn" style="z-index: 99">
            <button class="signup" id="loadapp" ><span class="downloadapp"></span>下载APP</button>
        </div>
       
        <!--浏览器打开提示-->
        <div class="safari none">
            <img src="{{URL::asset('/')}}/images/safari.png">
        </div>
        <div class="none" id="video_title_none"></div>
        <div class="none" id="video_descript_none"></div>
        <div class="none" id="endtime_none"></div>
        <div class="isFavorite"></div>
	</section>
@stop

@section('endjs')
	<script>
		var act_id ={{$id}};
		 //是否在分享页面
	    var urlPath = window.location.href+'&is_share=1';
	    var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;   
	    function share(is_flag) {
	    	if (is_flag) {
                    $('#loadAppBtn').removeClass('none');
                    $('#installapp').removeClass('none');
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
                        var wxurl = labUser.api_path + '/weixin/js-config';
                        //活动详情描述
                        var desptStr = removeHTMLTag(selfObj.description);
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
                                        title: selfObj.subject, // 分享标题
                                        link: location.href, // 分享链接
                                        imgUrl: selfObj.detail_img, // 分享图标
                                        success: function () {
                                            // 用户确认分享后执行的回调函数
                                        },
                                        cancel: function () {
                                            // 用户取消分享后执行的回调函数
                                        }
                                    });
                                    wx.onMenuShareAppMessage({
                                        title: selfObj.subject,
                                        desc: despt,
                                        link: location.href,
                                        imgUrl: selfObj.detail_img,
                                        trigger: function (res) {
                                            // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
                                            console.log('用户点击发送给朋友');
                                        },
                                        success: function (res) {
                                            console.log('已分享');
                                        },
                                        cancel: function (res) {
                                            console.log('已取消');
                                        },
                                        fail: function (res) {
                                            console.log(JSON.stringify(res));
                                        }
                                    });
                                });
                            }
                        });
                    }
                    else {
                        if (isiOS) {
                            //打开本地app
                            $(document).on('tap', '#openapp', function () {
                                //var strPath = window.location.pathname.substring(1);
                                //var strParam = window.location.search;
                                //var appurl = strPath + strParam;
                                //var share = '&is_share';
                                //var appurl2 = appurl.substring(0, appurl.indexOf(share));
                               window.location.href = 'openwjsq://' + 'webapp/activity/detail?pagetag=02-2&uid=0&makerid=0&id='+act_id;
                            });
                            /**下载app**/
                            $(document).on('tap', '#loadapp,#a-tips', function () {
                                window.location.href = 'https://itunes.apple.com/app/id981501194';
                            });
                        }
                        else if (isAndroid) {
                            $(document).on('tap', '#loadapp,#a-tips', function () {
                                window.location.href = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.tyrbl.wujiesq';
                            });
                            $(document).on('tap', '#openapp', function () {
                                window.location.href = 'openwjsq://welcome' + '/webapp/activity/detail?pagetag=02-2&uid=0&makerid=0&id='+act_id;
                            });
                        }
                    }
            }
	    }
	    share(shareFlag);
	    
	</script>
	<script >
		Zepto(function () {
			 new FastClick(document.body);
			 var args=getQueryStringArgs(),
			 	 sharemark=args['share_mark']||'0',
			 	 code=args['code']||'0';

		    var param = {
		        "id":act_id

		    };
		    function fillTicket(result) {
		    	// var num=0;
		    	// for (var i = 0; i < result.length; i++) {
		    	// 	if (result[i].price == 0) {
		    	// 		num++;
		    	// 		console.log(num)
		    	// 	}
		    	// 	if (num ==0) {
		    	// 		$('#fold-ticket, .pay-ticket').addClass('none');
		    	// 	}
		    	// 	if (result[i].price!==0) {
		    	// 		$('#fold-ticket').removeClass('none');
		    	// 	}
		    	// }
		    	var num=0,num2=0,tclass='none';
		    	for (var i = 0; i < result.length; i++) {
		    		if (result[i].price == 0) {
		    			num++;
		    		}else if (result[i].price!==0) {
		    			num2++;
		    		}
		    	}
		    	console.log(num);
		    	console.log(num2);
		    	if (num2!=0) {
		    		$('#fold-ticket').removeClass('none');
		    	}
		    	if (num2==0) {
		    		$('#fold-ticket').addClass('none');
		    		tclass=''
		    	}
		    	if (num2!=0&num==0) {
		    		$('#fold-ticket').addClass('none');
		    		tclass='';
		    		$('.p-tips').removeClass('none')
		    	}
		    	$.each(result,function (i,item) {
		    		var typeArray=['免费票','现场票','直播票','VIP票'];
		            var colorArray=['bd-green','bd-orange','bd-orange','bd-blue'];
		             str3 = 	'<p class="p-tips f12 color999 none" style="text-align:left;margin-top:0.6rem;" >*收费门票只支持前往应用报名，点击<a style="color:#6bc24b" id="a-tips">下载无界商圈应用</a></p>';
		            if (item.name=='免费票') {
		            	typeNum = 0;
		            }else if(item.name=='现场票'){
		            	typeNum = 1
		            }else if (item.name=='直播票') {
		            	typeNum = 2
		            }
		            else if (item.name=='VIP票') {
		            	typeNum = 3
		            }else{
		            	typeNum = 1
		            }
		       
		    		if (item.score_price == 0) {
		    			var setHtml='';
		    			 
		    			if (item.is_over==1||item.left==0) {
                    		 setHtml+='<a href="javascript:;" >';
                    	}else{

                    		 setHtml+='<a href="'+labUser.path+'webapp/freecheck/detail/_v020400?id='+item.activity_id+'&ticket_id='+item.id+'&is_share=1&share_mark='+sharemark+'&code='+code+'">';
                    	}
                       
                        setHtml+='<div class="tc-box tc c" style="margin-bottom:1rem">';
                        if (item.left==0 || item.is_over==1) {
                        	setHtml+='<div class="tc-border bd-gray">';
                        }else {
                        	setHtml+='<div class="tc-border '+colorArray[0]+'">';
                        }
                        
                        setHtml+='<div class="left">';
                        setHtml+='<span class=" f16">'+typeArray[0]+'<em class="f12"> 还剩('+item.left+'份)</em>'+'</span>';                    
                        setHtml+='<p class="f12">'+item.remark+'</p>';
                        setHtml+='</div>';
                        if (item.left==0) {
                        	setHtml+='<div class="right f14"><div class="buy"><em class="f18">已售完</em></div></div>';
                        }else if (item.is_over==1) {
                        	 setHtml+='<div class="right f14"><div class="buy"><em class="f18">已截止</em></div></div>';
                        }else{
							setHtml+='<div class="right f14"><div class="buy"><em class="f18">免费</em><p>购票</p></div></div>';
                        }
                        setHtml+='</div></div></a>';
                        $('#ticket_free').append(setHtml);
		    		}else{
		    			var setHtml='';
		    			
    			        setHtml+='<a class=" pay-ticket '+tclass+'" onclick='+"alert('直播票及其他产生费用的现场票请登录无界商圈应用端进行购买')"+'>';
                        setHtml+='<div class="tc-box tc-box2 tc c mt05" >';

                        if (item.is_over==1||item.left==0) {
                        	setHtml+='<div class="tc-border bd-gray">';
                        }else{
                        	setHtml+='<div class="tc-border '+colorArray[typeNum]+'">';
                        }
                        setHtml+='<div class="left">';
                        setHtml+='<span class=" f16" id="sss">'+item.name+'<em class="f12"> 还剩('+item.left+'份)</em>'+'</span>';
                        if (item.remark==null) {
                             setHtml+='<p class="f12"></p>';
                        }else{
                            setHtml+='<p class="f12">'+item.remark+'</p>';
                        }
                        setHtml+='</div>';
                        if (item.left==0) {
                        	setHtml+='<div class="right f14"><div class="buy"><em class="f18">已售完</em></div></div>';
                        }else if (item.is_over==1) {
                        	 setHtml+='<div class="right f14"><div class="buy"><em class="f18">已截止</em></div></div>';
                        }else{
							setHtml+='<div class="right f14"><div class="buy"><em class="f18">'+item.score_price+'积分'+'</em><p>购票</p></div></div>';
                        }
                        setHtml+='</div></div></a>';
                        $('#ticket_pay').append(setHtml);
		    		}

		    	})
		    	 $('#ticket_pay').append(str3);
		    }
		    var actTicket = {
		        getList: function (id) {
		            var param = {};
		            param["id"] = id;
		            var url = labUser.api_path + '/activity/tickets/_v020400';
		            // var url = 'http://wjsq3.local/api' + '/activity/tickets';
		            ajaxRequest(param, url, function (data) {
		                if (data.status) {
		                    //html调整
		                    fillTicket(data.message);
		                    // $('#ticketList').html(html);
		                }
		            })
		        }
		       
		    }
    
		    /*页面加载时调用*/
		    actTicket.getList(param.id);  

		    //展开付费门票
		    $(document).on('click','#fold-ticket',function () {
		    	if ($(this).attr('flag')=='unfold') {
		    		$(this).text('展开付费门票');
		    		$('.pay-ticket').addClass('none');
		    		$('.p-tips').addClass('none');
		    		$(this).attr('flag','fold');
		    	}else{
			    	$(this).text('收起付费门票');
			    	$('.pay-ticket').removeClass('none');
			    	$(this).attr('flag','unfold');
			    	$('.p-tips').removeClass('none');
		    	}
		    });
		    //提示文字点击下载应用
		    $(document).on('click','#a-tips',function () {
		    	$('#loadapp').trigger('click');
		    });

		   

		});
	</script>

@stop