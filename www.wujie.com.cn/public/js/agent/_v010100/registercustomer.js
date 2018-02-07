//zhangxm
Zepto(function(){
		new FastClick(document.body);
		
		$(document).ready(function () { $('body').css({'height':$(window).height()})});
		var needRefresh = localStorage.getItem("need-refresh");
		console.log(needRefresh );
	    if(needRefresh){
	        localStorage.removeItem("need-refresh");
	        window.location.reload();
	    } 
		var args=getQueryStringArgs(),
            uid = args['agent_id'] || '0',
			urlPath = window.location.href,
            origin_mark = args['share_mark'] || 0,//分销参数，分享页用
            code = args['code'] || 0;
		var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
        // 获取详情
		function getdetail(uid){
			var param={};
			param['type']='customer';
            param['agent_id']=uid;
            if(shareFlag){
                param['guess']=1;
            }       
		var	url=labUser.agent_path + '/user/invite-slogan/_v010003';
		ajaxRequest(param,url,function(data){
			if(data.status){
				$('#container').removeClass('none');
				if(data.message){
					if(data.message.is_public_realname==0){
						$('.realname').text(data.message.nickname);
					}else {
						$('.realname').text(data.message.realname);
					};
					$('.phone').text(data.message.phone);
				}
//				weixinShare(data.message,shareFlag)
			}else {
//				$('#container').addClass('none');
				tips(data.message);
			};
			$('#zone').intlTelInput();
			 
		});
		};
		
		function valNull(obj,val){
			if(val==''){
				tips('请输入'+obj+'!');
			}
		}
		//电话非空
		$('.tel').blur(function(){
			var telVal = $('.tel').val();
			valNull('手机号',telVal);
		});
		//昵称非空
		$('#name').blur(function(){
			valNull('昵称',$(this).val());
		});
		//验证码非空
		$('#code').blur(function(){
			valNull('验证码',$(this).val());
		});
		//图形验证码非空
		$('#picture_code').blur(function(){
			valNull('图形验证码',$(this).val());
		});
		var tt;
    	var wait = 60;
		function styles(){
             var ret = /^\d{10,11}$/;
             $(document).on('click','.foot_btn',function(){
             	var phone_code =$('#zone').val().replace(/[^0-9]/ig,"");
             	var phone = $('.tel').val();
             	
             if(ret.test(phone)){
//              $('.bg-model').removeClass('none');
//				console.log(phone,phone_code);
                submit(phone,phone_code);
              }else{
                tips('请输入正确手机号码');
              }
             });  
                     
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
	              "font-size":"15px",
	              "color":'color999',
	              "background":"#ffffff"
	            });
	            wait = 60;
	        }else {
	            o.attr("disabled", true);
	            o.css({
	              "font-size":"15px",
	              "color":'color999',
	              "background":"#ffffff"
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
//           time(that);
            var phone_code = $('#zone').val().replace(/[^0-9]/ig,"");
            var phone = $('.tel').val();
//           console.log(phone_code)
            var param={};
                param['username']=phone;
                param['type']='agent_invite_register';
                param['app_name']='wjsq';
                param['nation_code']=phone_code;
                param['captcha']=$('#picture_code').val();
            var url = labUser.api_path+'/identify/picverify-before-sendcode/_v010003';  //发送验证码
            ajaxRequest(param,url,function(data){
	            if(data.status){
	            	time(that);
	            	tips(data.message);
	            }else {
	            	tips(data.message);
	            	$('.yanzhengma').click();
	            	$('.tel').val('');
			        $('#code').val('');
			        $('#picture_code').val('');
	            };
            });
           })
        };
//      function makesure(){
//        $('.foot_btn').on('click',function(){
//        	var phone =$('#zone').val().split(' ')[1];
//          if($('#code').val()){
//               submit(phone,phone_code);
//          }else{
//               tips('请输入验证码');
//          }
//        })
//      };
        
    getdetail(uid);   
    styles();
    getcode();
//  makesure();
    function submit(phone,phone_code){
          var param={};
          param['username']=phone;
          param['phone_code']=phone_code;
          param['code']=$('#code').val();
          param['type']='agent_invite_register';
          param['agent_id']=uid;
          param['app_name']='wjsq';
          var url=labUser.agent_path+'/user/customer-register/_v010003'; //校验 验证码
          ajaxRequest(param,url,function(data){
            if(data.status){ 
//               $('.fixbg').removeClass('none');
				tips('注册成功');
				$('.getcode').text('获取验证码');
				$('.tel').val('');
		        $('#code').val('');
		        $('#picture_code').val('');
				window.location.href = labUser.path + '/webapp/agent/register/download/_v010100?customer_id='+data.message.uid;
            }else if(data.message=='has_register'){
		            tips('您已经注册过了');
		            $('.getcode').text('获取验证码');
		            $('.tel').val('');
			        $('#code').val('');
			        $('#picture_code').val('');
		             setTimeout(function(){
		             	window.location.href = labUser.path+'/webapp/agent/register/download/_v010100?customer_id='+data.message.uid+'&has_register='+1;
		             },2000);
		    }else if(data.message.is_self_register == 1){
		    	tips('您已经注册过了');
		    	$('.getcode').text('获取验证码');
		    	$('.tel').val('');
		        $('#code').val('');
		        $('#picture_code').val('');
		    	setTimeout(function(){
		             	window.location.href = labUser.path+'/webapp/agent/register/download/_v010100?customer_id='+data.message.uid+'&is_self_register='+1;
		             },2000);
		    	
		    }else {
		    	$('.tel').val('');
		        $('#code').val('');
		        $('#picture_code').val('');
		    	tips(data.message);
		    	$('.yanzhengma').click();
		    };
        });
    };
//  function yanzheng(phone,phone_code){
//      var param={};
//      	param['phone_code'] = phone_code;
//          param['phone']=phone;
//          param['type']=2;
//      var url=labUser.agent_path+'/user/isregister/_v010003';
//      ajaxRequest(param,url,function(data){
//	        if(data.status){
//	          if(data.message=='ok'){
//	            $('.fixbg').removeClass('none') 
//	          }else if(data.message=='has_register'){
//	             tips('您已经注册过了');
//	             setTimeout(function(){
//	             	window.location.href = labUser.path+'/webapp/wjload/detail';
//	             },2000)
//	          }
//	        }
//      });
//  };
	
    $(document).on('click','.be_sure',function(){
      window.location.href = labUser.path + '/webapp/wjload/detail';
    });
	//点击跳转应用宝
	$(document).on('click','.myApp',function(){
		window.location.href = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.tyrbl.agent';
	});
	//点击展开收起引导
	$(document).on('click','.realize',function(){
		if($('.show_up').attr('src')=='/images/agent/yellowshow.png'){
			$('.show_up').attr('src','/images/agent/yellowshowup.png');
			$('.section_two').addClass('none');
		}else {
			$('.show_up').attr('src','/images/agent/yellowshow.png');
			$('.section_two').removeClass('none');
		}
	});
		//二次分享
//		function weixinShare(obj,is_share){
//			if(is_share&&is_weixin()){
//              /**微信内置浏览器**/
//              $(document).on('tap', '#loadapp,#openapp', function () {
//                  var _height = $(document).height();
//                  $('.safari').css('height', _height);
//                  $('.safari').removeClass('none');
//              });
//              //点击隐藏蒙层
//              $(document).on('tap', '.safari', function () {
//                  $(this).addClass('none');
//              });
//				var wxurl = labUser.api_path + '/weixin/js-config';
//                      ajaxRequest({url: location.href}, wxurl, function (data) {
//                          if (data.status) {
//                              wx.config({
//                                  debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
//                                  appId: data.message.appId, // 必填，公众号的唯一标识
//                                  timestamp: data.message.timestamp, // 必填，生成签名的时间戳
//                                  nonceStr: data.message.nonceStr, // 必填，生成签名的随机串
//                                  signature: data.message.signature, // 必填，签名，见附录1
//                                  jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
//                              });
//                              wx.ready(function () {
//                                  wx.onMenuShareTimeline({    //分享到朋友圈
//                                      title: '我在【无界商圈Agent】发现好多不错的项目，快来看看吧！', // 分享标题
//                                      link:location.href, // 分享链接
//                                      imgUrl: obj.logo, // 分享图标
//                                      success: function () {
//                                          // 用户确认分享后执行的回调函数
//                                          
//                                      },
//                                      cancel: function () {
//                                          // 用户取消分享后执行的回调函数
//                                      }
//                                  });
//                                  wx.onMenuShareAppMessage({  //分享给朋友
//                                      title:'我在【无界商圈Agent】发现好多不错的项目，快来看看吧！',
//                                      desc: '[ 注册无界商圈Agent，好礼享不停 ]',
//                                      link: location.href,
//                                      imgUrl: obj.logo, // 分享图标
//                                      trigger: function (res) {
//                                          // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
////                                          console.log('用户点击发送给朋友');
//                                      },
//                                      success: function (res) {
//                                          console.log('已分享');
//                                          
//                                      },
//                                      cancel: function (res) {
////                                          console.log('已取消');
//                                      },
//                                      fail: function (res) {
////                                          console.log(JSON.stringify(res));
//                                      }
//                                  });
//                              });
//                          }
//                      });
//			}
//		};

        
	});

