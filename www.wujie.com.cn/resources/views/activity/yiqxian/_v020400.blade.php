@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/dist/swiper.min.css" rel="stylesheet" type="text/css"/>
    <style>
		.brandinfo{
			width:84%;
			position: absolute;
			left:50%;margin-left: -42%;
			top:-4.2rem;
		}
		.height1{
		   height: 100%;
		}
		.page_bg{
			background: #ffdf27;
		}
		.rolling{
			text-align: center;
			background: #fff3b7;
			font-size: 1.5rem;
			width:28.5rem;
			position: absolute;
			top:0rem;
			left:50%;margin-left: -14.25rem;
			border-radius: 0.3rem;
			padding:0.2rem 1rem;
            height: 2.5rem;
            overflow: hidden;
            
		}
        .rolling li{
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
        }
		.zixun{
			width:13.8rem;
			height: 5rem;
			position: absolute;
			bottom: 3rem;
			left: 50%;margin-left: -6.9rem;
		}
        .godown {
            width:2.3rem;
            height: 1.25rem;
            position: absolute;
            bottom: 1rem;
            left: 50%;margin-left: -1.15rem;
        }
    </style>
@stop
@section('main')
	<section class="height1 page_bg swiper-container">
	<div class="swiper-wrapper">
		<div class="height1 swiper-slide">
			<img src="/images/act/01.png" alt="">
			<div class="relative">
				<img src="/images/act/brandinfo.png" alt="" class="brandinfo">
			</div>
			<img src="/images/act/02.png" alt="">
			<div class="relative">
				<ul class="rolling none">
                    <!-- <li></li> -->
                </ul>
			</div>
			<img src="/images/act/03.png" alt="">
			<div class="zixun">
				<img src="/images/act/zixun.png" alt="">
			</div>
            <div class="godown">
                <img src="/images/act/godown.png" alt="">
            </div>
		</div>
		<div class="height1 swiper-slide">
			<img src="/images/act/04.png" alt="">
			<img src="/images/act/05.png" alt="">
			<img src="/images/act/06.png" alt="">
			<div class="zixun">
				<img src="/images/act/zixun.png" alt="">
			</div>
		</div>
	</div>	
	<div class="safari none">
            <img src="{{URL::asset('/')}}/images/safari.png">
     </div>
     <div class="tips none"></div> 
	</section>
@stop

@section('endjs')
<script type="text/javascript" src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
<script src="{{URL::asset('/')}}/js/dist/swiper-3.4.2.min.js"></script>
 <script type="text/javascript">
	Zepto(function () {
        document.title='壹Q鲜品牌盛典';
		var arg=getQueryStringArgs(),
			id=arg['id'] || 0;
			uid=arg['uid'] || 0,
            shareFlag = location.href.indexOf('is_share') > 0 ?'&is_share=1': '';

		var  mySwiper = new Swiper('.swiper-container',{
			direction : 'vertical'
        });

        var reUrl = labUser.agent_path + '/temporary/temporary-brand-activitys/_v010200'
        ajaxRequest({},reUrl,function(data){
            if(data.status){
                if(data.message.length > 0){
                    var liHtml = ''
                    $.each(data.message,function(i,j){
                        liHtml += '<li>'+j.user_name+'已成功加盟品牌</li>'
                    })
                    $('.rolling').html(liHtml).removeClass('none');
                }else{
                    $('.rolling').remove();
                }
                looper_dingdan = setInterval(function () {
                if ($('.rolling li').length > 1) {
                    var firstTag=$('.rolling').find('li:first');
                    var height=firstTag.height();
                    console.log(height);
                    firstTag.animate({'marginTop':'-'+height,'opacity':0},500,function(){
                        $(this).clone().css({'marginTop':0,'opacity':1}).appendTo($('.rolling'));
                        $('.rolling li').first().remove();
                    });
                }
            }, 2000);
               
            }
        })

        $(document).on('click','.brandinfo',function(){
        	window.location.href = labUser.path + 'webapp/brand/detail/_v020901?id='+id+'&uid='+uid+shareFlag;
        })

        $(document).on('click','.zixun',function(){
            if(!shareFlag){
                customerService(id);
            }else if(is_weixin()){
                var _height = $(document).height();
                $('.safari').css('height', _height);
                $('.safari').removeClass('none');
               
            }
        })

        $(document).on('click', '.godown', function() {
            mySwiper.slideTo(1,500,false);
        });

        $(document).on('click', '.safari', function() {
            $(this).addClass('none');
            return;
        });

    //立即咨询
        function customerService(id){
		    if (isAndroid) {
		        javascript:myObject.customerService(id);
		    } 
		    else if (isiOS) {
		        var message = {
		                method : 'customerService',
		                params : {
		                	id:id
		                }
		            }; 
		        window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
		    }
		}

		//微信二次分享
            //浏览器判断
        if(shareFlag){
            if (is_weixin()) {
                /**微信内置浏览器**/
                
                // 点击隐藏蒙层
                
                var sharetitle = '商机来了 “钱”力 无限';
                var wxurl = labUser.api_path + '/weixin/js-config';
                var share_logo = labUser.path + 'images/agent-share-logo.png'; 
                var des = '壹Q鲜品牌盛典，你开店我出钱，名额仅有10个，先到先得！'
        
                ajaxRequest({ url: location.href }, wxurl, function(data) {
                    if (data.status) {
                        wx.config({
                            debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                            appId: data.message.appId, // 必填，公众号的唯一标识
                            timestamp: data.message.timestamp, // 必填，生成签名的时间戳
                            nonceStr: data.message.nonceStr, // 必填，生成签名的随机串
                            signature: data.message.signature, // 必填，签名，见附录1
                            jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage'] // \必填，需要使用的JS接口列表，所有JS接口列表见附录2
                        });
                        wx.ready(function() {
                            // 获取“分享到朋友圈”按钮点击状态及自定义分享内容接口
                            wx.onMenuShareTimeline({
                                title:'壹Q鲜品牌盛典，你开店我出钱，疯狂让利，劲爆低价，名额仅有10个，点击速抢！', // 分享标题
                                link: location.href, // 分享链接
                                imgUrl: share_logo, // 分享图标
                                success: function() {
                                
                                },
                                cancel: function() {
                                    // 用户取消分享后执行的回调函数
                                }
                            });
                            // 获取“分享给朋友”按钮点击状态及自定义分享内容接口
                            wx.onMenuShareAppMessage({
                                title: sharetitle,
                                desc: des,
                                link: location.href,
                                imgUrl: share_logo,
                                trigger: function(res) {
                                    // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
                                    console.log('用户点击发送给朋友');
                                },
                                success: function(res) {
                                    
                                },
                                cancel: function(res) {
                                    console.log('已取消');
                                },
                                fail: function(res) {
                                    console.log(JSON.stringify(res));
                                }
                            });
                        });
                    }
                });
            }else if(isiOS){
                oppenIos();
                //商圈下载地址
                $(document).on('click','.zixun',function(){
                    window.location.href = 'https://itunes.apple.com/app/id981501194';
                })  
            }else if(isAndroid){
                openAndroid() 
                $(document).on('click','.zixun',function(){
                    window.location.href = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.tyrbl.wujiesq';          
                 })
            }  
        } 

	})
//微信分享
	function showShare() {
	    var args = getQueryStringArgs(),
	        id = args['id'] || '0';
        var  type = 'activity',
             title = '商机来了 “钱”力 无限',
             pageUrl = window.location.href,
             img = labUser.path + 'images/agent-share-logo.png',
             header = '',
             content = '壹Q鲜品牌盛典，你开店我出钱，名额仅有10个，先到先得！',
             weibo = wechat ='壹Q鲜品牌盛典，你开店我出钱，疯狂让利，劲爆低价，名额仅有10个，点击速抢！'      
        investorShare(title, pageUrl, img, header, content,'','',id,type,weibo,wechat);//分享
	};
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
 </script>
@stop