@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/headline_detail.css" rel="stylesheet" type="text/css"/>
@stop

@section('main')
    <section >
        <!--安装app-->
        <div class="app_install fixed none" id="installapp">
            <i class="l">打开无界商圈APP，体验更多精彩内容 >></i>
            <span class="r" id="openapp"><img src="{{URL::asset('/')}}/images/020502/install_btn.png" alt=""></span>
            <div class="clearfix"></div>
        </div>
        <div class="share pl1-33 pr1-33 share_0502 none" id="share">
            <p class="f12 l">分享资讯，立即获得100积分</p>
            <button class="ff5 l f12 understand"><img src="{{URL::asset('/')}}/images/020502/notice.png" alt="">了解更多分享机制</button>
            <span class="fff f16 r close_share"><img src="{{URL::asset('/')}}/images/share_close.png" alt=""></span>
        </div>
         <!--浏览器打开提示-->
        <div class="safari none">
            <img src="{{URL::asset('/')}}/images/safari.png">
        </div>
        <button class="buy f16 none" id="loadapp">下载APP</button>
    </section>
	<section id='container'>
		
	</section>

@stop
@section('endjs')
	<script>
	Zepto(function(){
		var args=getQueryStringArgs(),
            id = args['id'] || '0',
            uid = args['uid'] || '0',
			urlPath = window.location.href,
            origin_mark = args['share_mark'] || 0,//分销参数，分享页用
            code = args['code'] || 0;
		var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
		function getdetail(id,uid){
			var param={};
			param['id']=id;
            param['uid']=uid;
		var	url=labUser.api_path + '/news/detail/_v020500'
		ajaxRequest(param,url,function(data){
			if(data.status){
				if(data.message){

					$('#container').html('<div class="banner"></div><div class="intro"><h1 class="title"></h1><p class="some"></p><p class="detail"></p></div>');
					if(data.message.banner==''){
						$('.banner').addClass('none');
					}else{
						$('.banner').html('<img src='+data.message.banner+' alt="banner">');
					}
					$('.title').text(data.message.title).addClass('fline');
					if(data.message.type=='brand'){
						$('.some').html(data.message.author+'<span>'+data.message.created_at_format+'</span><i>'+data.message.brand.name+'<a>|</a>'+data.message.brand.category_name+'</i>');
					}else if(data.message.type=='none'){
						$('.some').html(data.message.author+'<span>'+data.message.created_at_format+'</span>');
					}
                    $('.some').addClass('fline');
					$('.detail').html(data.message.detail);
                    $('#container').data('sharemark',data.message.share_mark);
                    $('#container').data('logo',data.message.logo);
                    if(data.message.distribution_id==0){
                        $('#share').addClass('none');
                        $('#share').data('reward',0);
                    }else{
                        $('#share').removeClass('none');
                        $('#share').data('reward',1);
                    }
                    if(shareFlag){
                        var headline='headID'+id;
                        if($('#share').data('reward')==1&&(!localStorage.getItem(headline))){
                            getReward(origin_mark,'view',0,code);
                            localStorage.setItem(headline,id);    
                        };
                        $('#installapp').removeClass('none');
                        $('#share').addClass('none');
                        $('#container').css('padding-top','3.2rem');
                        $('.buy').removeClass('none');
                        weixinShare(data.message,shareFlag);
                     }
                    
				}else{
					$('#container').html('null');
				}
                
			}	
		 })
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
                                    wx.onMenuShareTimeline({
                                        title: obj.title, // 分享标题
                                        link:location.href, // 分享链接
                                        imgUrl: obj.banner, // 分享图标
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
                                        title: obj.title,
                                        desc: nowhitespaceStr,
                                        link: location.href,
                                        imgUrl: obj.banner,
                                        trigger: function (res) {
                                            // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
                                            console.log('用户点击发送给朋友');
                                        },
                                        success: function (res) {
                                            console.log('已分享');
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

        //关闭分享机制提醒
             $(document).on('tap','.close_share',function(){
                $('.share').addClass('none');
             });
        //了解更多分享机制
            $(document).on('tap','.understand',function(){
                window.location.href=labUser.path+'webapp/protocol/moreshare/_v020500?pagetag=025-4';
            })

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
	</script>
	 <script>	
   		//分享
        function showShare() {
            // shareOut('title', window.location.href, '', 'header', 'content');
            var type='news';
            var title = $('.title').text();
            var url = window.location.href;
            var img =  $('#container').data('logo');
            var header = '头条';
            var content = cutString(removeHTMLTag($('.detail').text()), 18);
            var id={{$id}};
            var share_mark=$('#container').data('sharemark');
            var url = window.location.href+'&share_mark='+share_mark;
            var p_url=labUser.api_path+'/index/code/_v020500';
                ajaxRequest({},p_url,function(data){
                    if(data.status){
                        var code=data.message;
                        url+= '&code=' +code;
                        if($('#share').data('reward')==1){   
                            shareOut(title, url, img, header, content,'','','',type,share_mark,code,'share','news',id);
                        }else{
                            shareOut(title, url, img, header, content,'','','','','','','','','');
                        }                    
                    }
                    
                })
            
        };

       

       
   </script>
@stop