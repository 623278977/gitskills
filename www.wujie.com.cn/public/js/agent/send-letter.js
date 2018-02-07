//zhangxm
Zepto(function(){
		new FastClick(document.body);
//		$(document).ready(function () {
//　　			$('body').height($('body')[0].clientHeight-22);
//		});
		
		$(document).ready(function () { $('body').css({'height':$(window).height()})});
		var args=getQueryStringArgs(),
            id = args['customer_id'] || '0',
            uid = args['agent_id'] || '0',
			urlPath = window.location.href,
            origin_mark = args['share_mark'] || 0,//分销参数，分享页用
            code = args['code'] || 0;
		var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
		
		
        // 获取详情
		function getdetail(id,uid){
			var param={};
			param['type']='agent';
            param['agent_id']=uid;
            if(shareFlag){
                param['guess']=1;
            }       
		var	url=labUser.agent_path + '/user/invite-slogan/_v010002';
		ajaxRequest(param,url,function(data){
			if(data.status){
				if(data.message){
					var conHtml = '';
					conHtml += '<div class="head"><div class="head-cont">';
					conHtml += '<p><img src="'+data.message.avatar+'" class="mr1 avatar"/></p>';
					if(data.message.is_public_realname==1){
						conHtml += '<div class="text"><span class="cffa300 f13 bold b">'+data.message.realname+'：</span>';
					}else {
						conHtml += '<div class="text"><span class="cffa300 f13 bold b">'+data.message.nickname+':</span>';
					}
					
					conHtml += '<span class="f13 bold b color333">“一起来用无界商圈经纪人版，代理品牌、邀请投资人、管理加盟客户，轻松签大单，佣金享不停！”</span></div></div></div>';
					conHtml += '<div class="foot">';
//					conHtml += '<select name="" class="select_id"><option value="">+86</option><option value="">+1</option></select>';
					conHtml += '<input type="tel" name="phone" id="zone" value="+86 " class="mobile f15 color999 medium" placeholder="请输入手机号"/>';
					conHtml += '<span id="moileMsg"></span>';
//					conHtml +='<div class="tips">qing输入手机号</div>';
					conHtml += '<span class="submit f15">接受邀请，注册无界商圈经纪人</span>';
				}
				weixinShare(data.message,shareFlag)
			};
			$('#container').html(conHtml);
			$('#zone').intlTelInput();
			 
		});
		};
		
		var tt;
    	var wait = 60;
//  	$(document).on('change','.select_id',function(){
//  		var checkText = $()
//  	})
//  	$('.select_id').change()
//  	var checkText=$(".select_id  option:selected").text();
//  	
//  	console.log(checkText)
		
		function styles(){
             var ret = /^\d{10,11}$/;
             $(document).on('click','.submit',function(){
             	var phone =$('#zone').val().split(' ')[1];
             	var phone_code = $('#zone').val().split(' ')[0];
             if(ret.test(phone)){
//              $('.bg-model').removeClass('none');
//				console.log(phone,phone_code);
                yanzheng(phone,phone_code);
              }else{
                tips('请输入正确手机号码');
              }
             });
            $('.cancel').on('click',function(){
              $('.bg-model').addClass('none')
            })    
                     
        };
       function tips(e){
            $('.common_pops').text(e).removeClass('none');
            setTimeout(function() {
                $('.common_pops').addClass('none');
            }, 1500);
        };
       function time(o){
                    if (wait == 0) {
                        o.removeAttr("disabled");
                        o.html("重新发送");
                        o.css({
                          "font-size":"12px",
                          "background":"#f2f2f2"
                        });
                        wait = 60;
                           } else {
                        o.attr("disabled", true);
                        o.css({
                          "font-size":"12px",
                          "background":"#f2f2f2"
                        });
                        o.html('重新发送(' + wait + 's)');
                        wait--;
                        tt = setTimeout(function () {
                               time(o)
                            },
                            1000)
            }
        };
       function getcode(){
          $('.getcode').on('click',function(){
            var that=$('.getcode');
             time(that);
             var phone =$('#zone').val().split(' ')[1];
             var phone_code = $('#zone').val().split(' ')[0];
             var param={};
                 param['username']=phone;
                 param['type']='agent_register';
                 param['app_name']='agent';
                 param['nation_code']=phone_code;
            var url=labUser.api_path+'/identify/sendcode/_v010000';  //发送验证码
            ajaxRequest(param,url,function(data){
	            if(data.status){
	            	
	            };
            });
           })
        };
        function makesure(){
          $('.makesure').on('click',function(){
          	var phone =$('#zone').val().split(' ')[1];
            if($('#code').val()){
                 submit(phone);
            }else{
                 tips('请输入验证码')
            }
          })
        };
        
    getdetail(id,uid);   
    styles();
    getcode();
    makesure();
    function submit(phone){
          var param={};
          param['username']=phone;
//        param['username']=$('input[name="phone"]').val();
          param['code']=$('#code').val();
          param['type']='agent_register';
          param['agent_id']=uid;
          var url=labUser.agent_path+'/user/agent-register/_v010002'; //校验 验证码
          ajaxRequest(param,url,function(data){
            if(data.status){ 
                  $('.fixbg').removeClass('none');
                  $('input[name="wrirecode"]').val('');
                  $('input[name="phone"]').val('');
                  $('.bg-model').addClass('none');
            }else{
                  tips(data.message);
            };
        });
    };
    function yanzheng(phone,phone_code){
            var param={};
            	param['phone_code'] = phone_code;
                param['phone']=phone;
                param['type']=1;
            var url=labUser.agent_path+'/user/isregister/_v010000';
              ajaxRequest(param,url,function(data){
            if(data.status){
              if(data.message=='ok'){
                $('.bg-model').removeClass('none') 
              }else if(data.message=='has_register'){
                 tips('您已经注册过了');
                 setTimeout(function(){
                 	window.location.href = labUser.path+'/webapp/wjload/agentdetail';
                 },2000)
              }
            }else if(data.message=='has_register'){
                 tips('您已经注册过了');
                 setTimeout(function(){
                 	window.location.href = labUser.path+'/webapp/wjload/agentdetail';
                 },2000)
              }
        });
   };
	
    $(document).on('click','.be_sure',function(){
      window.location.href = labUser.path + '/webapp/wjload/agentdetail';
    })
		
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
                                    wx.onMenuShareTimeline({    //分享到朋友圈
                                        title: '我在【无界商圈Agent】发现好多不错的项目，快来看看吧！', // 分享标题
                                        link:location.href, // 分享链接
                                        imgUrl: obj.logo, // 分享图标
                                        success: function () {
                                            // 用户确认分享后执行的回调函数
                                            
                                        },
                                        cancel: function () {
                                            // 用户取消分享后执行的回调函数
                                        }
                                    });
                                    wx.onMenuShareAppMessage({  //分享给朋友
                                        title:'我在【无界商圈Agent】发现好多不错的项目，快来看看吧！',
                                        desc: '[ 注册无界商圈Agent，好礼享不停 ]',
                                        link: location.href,
                                        imgUrl: obj.logo, // 分享图标
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
			}
		};

//		function checkSubmitMobil() {
//				if ($(".mobile").val() == "") {
//				$("#moileMsg").html("<font color='white'>* 手机号码不能为空！</font>");
//				$(".mobile").focus();
//				return false;
//				}
//				
//				if (!$(".mobile").val().match(/^(((13[0-9]{1})|159|153)+\d{8})$/)) {
//				$("#moileMsg").html("<font color='white'>* 手机号码格式不正确！请重新输入！</font>");
//				$(".mobile").focus();
//				return false;
//				}
//				return true;
//		}
        
	});
//打开本地--Android
// function openAndroid(){
//     var strPath = window.location.pathname;
//     var strParam = window.location.search.replace(/is_share=1/g, '');
//     var appurl = strPath + strParam;
//     window.location.href = 'openwjsq://welcome' + appurl;
// };
// function oppenIos(){
//     var strPath = window.location.pathname.substring(1);
//     var strParam = window.location.search;
//     var appurl = strPath + strParam;
//     var share = '&is_share';
//     var appurl2 = appurl.substring(0, appurl.indexOf(share));
//     window.location.href = 'openwjsq://' + appurl2;
// }
