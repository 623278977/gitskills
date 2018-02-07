var player;
var reg=/^\d{10,11}$/g;
var args=getQueryStringArgs(),
	  id = args['id'],
    uid = args['agent_id'],
    brand_id=args['is_brand'],
    page=1,
    pagesize=3;
var Param ={
            "video_id": id,
            "page":page,
            "page_size": pagesize
            }
var shareFlag = (window.location.href).indexOf('is_share=1') > 0 ? true : false;
  if(shareFlag){
    uid=0;
    }
var isBrand = (window.location.href).indexOf('is_brand') > 0 ? true : false;
var Video = {
	vodDetail:function(id,shareFlag){
		var param={};
			  param['id']=id;
    if(isBrand){
         var url=labUser.agent_path+'/video/study-video-detail/_v010000';
    }else{
         var url=labUser.agent_path+'/video/detail/_v010000';
    }
		ajaxRequest(param,url,function(data){
			if(data.status){
                var obj=data.message;
                if(obj.video_url){
                        $('.share_video').addClass('none');
                         getVod(obj.video_url, 0);
                    }else{
                        $('.share_video').removeClass('none');
                    }
				 $('#video_detail').attr({'title':obj.title,'data_des':obj.detail,'data_img':obj.share_img}); 
         $('.send-mes').data('brand_id',obj.brand_id);
				if(shareFlag){
					$('#installapp').removeClass('none');
					$('#App').removeClass('none');
					$('#share').addClass('none');
          $('.ui_share').removeClass('none');
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
                        var desptStr = removeHTMLTag(obj.detail);
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
                                        title: obj.title, // 分享标题
                                        link: location.href, // 分享链接
                                        imgUrl: obj.share_img, // 分享图标
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
                                        desc: despt,
                                        link: location.href,
                                        imgUrl: obj.share_img,
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
                            $(document).on('tap', '#openapp,#App', function () {
                                oppenIos();
                            });
                            /**下载app**/
                            $(document).on('tap', '#loadapp', function () {
                                window.location.href = 'https://itunes.apple.com/app/id981501194';
                            });
                            oppenIos();
                        }else if (isAndroid) {
                            $(document).on('tap', '#loadapp', function () {
                                window.location.href = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.tyrbl.wujiesq';
                            });
                            $(document).on('tap', '#openapp,#App', function () {
                               openAndroid();
                           });
                            openAndroid();
                        }
                };
				  }									
			
			//品牌信息
				if(obj.brand_id){	
					var brandHtml='';					
						brandHtml+='<div class="white-bg brand-company pl1-33" data-id="'+obj.brand_id+'"><img src="'+obj.brand_logo+'" alt="" class="company mr1-33 fl">';
						brandHtml+='<div class="fl width70 zone" data_brandid="'+obj.brand_id+'"><em class="service f12 mr1">'+obj.brand_category_name+'</em>'
						brandHtml+='<span class="f14 b">'+obj.brand_title+'</span>';
						brandHtml+='<div class="brand-desc f12 color999 mb05 ui-nowrap-multi">'+removeHTMLTag(obj.brand_summary)+'</div>';
						brandHtml+='<p class="f12 mb05"><span class="c8a">投资额：</span> <span class="color-red">'+parseInt(obj.brand_investment_min)+' ~ '+parseInt(obj.brand_investment_max)+'万</span>';
						brandHtml+='</p>';
						if(obj.brand_keywords.length>0){
							var aHtml='';
							for(i=0;i<obj.brand_keywords.length;i++){
								aHtml+='<a class="tags-key border-8a-radius">'+obj.brand_keywords[i]+'</a>';
							};
							brandHtml+=aHtml;
						};
						brandHtml+='</div><div class="clearfix"></div></div>';									
					$('#brand_info').html(brandHtml).attr('data_brandID',obj.brand_id);
				}else{
					$('.brand').addClass('none');
					if(shareFlag){
						$('#loadapp').css('width','100%');
					}
				};
				$('#basic_videoimg').attr('src',obj.list_img);
				$('#basic_videoinfo').html('<p class="f16 mb02 h5-2 color333 b">'+cutString(obj.title,25)+'</p><p class="f12 c8a">录制时间：<span>'+unix_to_mdhm(obj.created_at)+'</span></p>');

				if(obj.detail==''){
						$('#nocommenttip').removeClass('none');
				}else{	
					$('#nocommenttip').addClass('none');
                     $('.disVideo').html(removeHTMLTag(obj.detail));
				};
			    $(".containerBox").removeClass('none'); 
     }
		});
	},
    brand:function(){
        if(isBrand){
                    var param={};
                        param['page']=page;
                        param['page_size']=pagesize;
                        param['video_id']=id;
                    var url=labUser.agent_path+'/video/clock/_v010000';
                    ajaxRequest(param,url,function(data){
            if(data.status){
                var html='';
                $.each(data.message.data,function(k,v){
                        html+='<ul  class=" ui_listdetail ui-border-b">';
                        if(v.avatar){
                        html+='<li><img  class="nick_pict"  src="'+v.avatar+'"></li>';   
                        }else{
                        html+='<li><img  class="nick_pict"  src="/images/default/avator-m.png"></li>';  
                        }  
                        html+='<li>\
                                       <p class="b f16 color333 margin7">'+v.realname+'</p>\
                                       <p class="color999 f12 margin7">'+v.zone_name+' <span class="fr">'+Video.unix(v.created_at)+'</span></p>\
                                   </li>\
                               </ul>';
                          })
                        if(param.page==1){
                           $('.list_mumber').html(html); 
                           if(data.message.data.length == 0){
                            $('.list_mumber').addClass('none');
                            $('.getmore').addClass('none');
                            $('#nocommenttip2').removeClass('none');
                          }
                           if(data.message.data.length<3){
                            $('.getmore').text('没有更多了…').attr('disabled',true);
                           }else{
                            $('.getmore').text('点击加载更多').removeAttr('disabled');
                            }
                        }else{
                           $('.list_mumber').append(html);
                          if(data.message.data.length<3){
                           $('.getmore').text('没有更多了…').attr('disabled',true);
                            return;
                         }   
                        }
                      }
                  })
               }
  },
  unix:function(unix){
                      var newDate = new Date();
                      newDate.setTime(unix * 1000);
                      var Y = newDate.getFullYear();
                      var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
                      var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
                      var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
                      var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
                      var s = newDate.getSeconds() < 10 ? '0' + newDate.getSeconds() : newDate.getSeconds();
                      return Y + '年' + M + '月' + D + '日' + ' ' + h + ':' + m + ':' +s;
   },
  static:function(){
                   if(isBrand){
                      var param={};
                          param['agent_id']=uid;
                          param['brand_id']=brand_id;
                          param['type']='video';
                          param['post_id']=id;
                       var url=labUser.agent_path+'/brand/apply-status/_v010000';
                       ajaxRequest(param,url,function(data){})
                   }
   },
   changestyle:function(){
                   if(isBrand){
                        $('.ui_basicmes').addClass('none');
                        $('.ui_have_brand').removeClass('none')
                    }
                    $('.ui_have_brand li').on('click',function(){
                       $(this).addClass('clickbg').siblings().removeClass('clickbg');
                       var index=$(this).index();
                       $('.style').eq(index).removeClass('none').siblings('.style').addClass('none');
                    })
                    $('.ui_share li').eq(1).on('click',function(){
                       $('#brand-mes').removeClass('none');
                       $('.fixed-bg').removeClass('none');
                    })
                    $('.fixed-bg').on('click',function(){
                       $(this).addClass('none');
                       $('#brand-mes').addClass('none');
                    })
                   //提交加盟意向
                    $('.send-mes').on('click',function(){
                        var reg=/1[34578]\d{9}/;
                        var mobile=$('input[name="phone"]').val();
                        if(reg.test(mobile)){
                            Video.submitbrand();
                        }else{
                            alert('信息请填写正确')
                        }
                    });
   },
   submitbrand:function(){
                      var mobile=$('input[name="phone"]').val(),
                          consult=$('input[name="consult"]').val(),
                          name=$('input[name="realname"]').val(),
                          param={},
                          brandID=$('.send-mes').data('brand_id');
                          param['id'] =brandID ;
                          param['uid']=uid;
                          param['mobile'] = mobile;
                          param['realname'] = name;
                          param['consult'] = consult;
                      var url = labUser.api_path + '/brand/message/_v020500';
                      ajaxRequest(param, url, function(data) {
                      if(data.status){
                          $('#brand-mes').addClass('none');
                          $('.fixed-bg').addClass('none');
                          alert(data.message);
                      }
                    })
   }
};//Video最外层
 Video.vodDetail(id,shareFlag);
 Video.brand(Param);
 Video.static();
 Video.changestyle();
/**实例化点播**/
function getVod(video_url, stop_time) {
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
            "disable_full_screen": 0
        },
        {
            //播放状态
            // status 可为 {ready: "播放器已准备就绪",seeking:"搜索",
            // suspended:"暂停", playing:"播放中" , playEnd:"播放结束" , stop:"试看结束触发"
            'playStatus': function (status) {
                if (status == 'playing') { //播放中
                    console.log('playing');
                }
                if (status == "playEnd") { //播放结束
                    console.log('end');
                }
                if (status == "stop") { //试看结束
                   
                }
            },
        });
}
$('.getmore').on('click',function(){
         page++;  
         Video.brand(Param); 
 })
 //跳转品牌详情
$(document).on('tap','.brand_info, zone',function(){
    var id=$(this).attr('data_brandid');
    onAgentEvent('video_detail','',{'type':'video','id':id,'userId':uid,'position':'3'});
   window.location.href=labUser.path + "webapp/agent/brand/detail?agent_id=" + uid + "&id=" + id;
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
