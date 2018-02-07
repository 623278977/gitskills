@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/wjload.css" rel="stylesheet" type="text/css"/>
@stop
@section('beforejs')
   <script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan class='hide' id='cnzz_stat_icon_1261401820'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s95.cnzz.com/z_stat.php%3Fid%3D1261401820' type='text/javascript'%3E%3C/script%3E"));
   </script>
@stop
@section('main')
    
    <section id="container" class="container">
        <!-- <p class="sub">你离<span class="newcolor">成功</span>只有一个</p> -->
        <!-- <p class="sub"><span class="newcolor">App</span>的距离</p> -->
        <!--<img src="{{URL::asset('/')}}/images/wjsq_load.png" alt="背景" class="big_bg">-->
        <img src="{{URL::asset('/')}}/images/wjsq_load.png" alt="背景" class="big_bg">
        <div class="load">
          <!--<span class="left "><img class="ios " src="{{URL::asset('/')}}/images/ios_load.png" alt="IOS下载"></span>
          <span class="right "><img class="android " src="{{URL::asset('/')}}/images/anzhuo_load.png" alt="安卓下载"></span>     -->
          <span class="left "><img class="ios " src="{{URL::asset('/')}}/images/agent/ios3.png" alt="IOS下载"></span>
          <span class="right "><img class="android " src="{{URL::asset('/')}}/images/agent/android3.png" alt="安卓下载"></span>
        </div>
   
    <div class="module hide">
        <img src="{{URL::asset('/')}}/images/weixinopen.png" alt="">
        <button type="button" class="close">关闭</button>
    </div>
    </section>
@stop

@section('endjs')
<script> 

  Zepto(function(){
    new FastClick(document.body);
    var args=getQueryStringArgs(),
        city=decodeURI(args['city']); 
        // name=decodeURI(args['name'])||0,
        // // group=args['group']||0,
    // var detail='运营'+group+'组-'+city+'-'+name;
    var local=window.location.href.indexOf('is_local')>0?true:false; 
    if(isiOS){
        $('.right').remove();
    }else if(isAndroid){
        $('.left').remove();
    }
//  var cookies=readCookie ('xiazai');
       if(is_weixin()){
            $(document).on("click",".ios,.android",function(){
                $(".module").removeClass('hide');
            });
            $(".close").tap(function(){
                $(".module").addClass("hide");
            })

       }else if(isiOS){
            $(document).on("click",".ios,.android",function(){
//            if(cookies){
//                console.log('已下载');
//            }else{
//              if(local){
//                  _czc.push(﻿["_trackEvent",city,'local','ios',1]);      
//              }else{
//                  _czc.push(﻿["_trackEvent",'无界商圈Agent','APP','ios',1]);
//              }
//              writeCookie ('xiazai', '1', 24);
//            }  
//https://itunes.apple.com/app/id1282277895
            window.location.href = 'https://www.pgyer.com/AyoQ';
//            setTimeout(function(){},800)
            });
       }else if(isAndroid){
            $(document).on("click",".ios,.android",function(){
//            if(cookies){
//                console.log('已下载');
//            }else{             
//              if(local){
//                  _czc.push(﻿["_trackEvent",city,'local','安卓',1]);        
//              }else{
//                  _czc.push(﻿["_trackEvent",'无界商圈','APP','安卓',1]);     
//              }  
//              writeCookie ('xiazai', '1', 24);
//            }  
//            setTimeout(function(){   },800);
              window.location.href = 'http://passport.wujie.com.cn/down/Wujiesq-self-release-2.8.0.10.apk';
            });
       }else{
          $(document).on("click",".android",function(){
            console.log('PC click android');
            window.location.href = 'http://passport.wujie.com.cn/down/Wujiesq-self-release-2.8.0.10.apk';
          });
          $(document).on("click",".ios",function(){
            console.log('PC click ios');
            window.location.href = 'https://www.pgyer.com/AyoQ';
          });
       }
       //写入cookie
//      function writeCookie (name, value, hours)
//      {
//          var expire = "";
//          if (hours != null)
//          {
//              expire = new Date ((new Date ()).getTime () + hours * 3600000);
//              expire = "; expires=" + expire.toGMTString ();
//          }
//          document.cookie = name + "=" + escape (value) + expire;
//      };

         //读取cookie
//      function readCookie (name)
//      {
//          var cookieValue = null;
//          var search = name + "=";
//          if (document.cookie.length > 0)
//          {
//              offset = document.cookie.indexOf (search);
//              if (offset != -1)
//              {
//                  offset += search.length;
//                  end = document.cookie.indexOf (";", offset);
//                  if (end == -1)
//                      end = document.cookie.length;
//                  cookieValue = unescape (document.cookie.substring (offset, end))
//              }
//          }
//          return cookieValue;
//      } ;

        //二次分享
        function weixinShare(){       
            var wxurl = labUser.api_path + '/weixin/js-config';
              var title='无界商圈Agent: 让你“赚钱”的APP';
              var desc='能加盟的品牌都在这里了！小本创业当老板，为你实现创业梦。'
              var src=$('.big_bg').attr('src');
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
                              title: title, // 分享标题
                              link:location.href, // 分享链接
                              imgUrl: src, // 分享图标
                              success: function () {
                                  // 用户确认分享后执行的回调函数
                                 
                              },
                              cancel: function () {
                                  // 用户取消分享后执行的回调函数
                              }
                          });
                          wx.onMenuShareAppMessage({
                              title: title,
                              desc: desc,
                              link: location.href,
                              imgUrl: src,
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
        };
        weixinShare();
     });
</script>
@stop