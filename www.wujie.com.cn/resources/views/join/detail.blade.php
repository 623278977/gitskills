@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/invite.css" rel="stylesheet" type="text/css"/>
@stop
@section('beforejs')
  <script>
    if(!is_weixin()&&!isiOS&&!isAndroid){
        var code="{{$uid}}";
        window.location.href= labUser.path +'/webapp/join/detailpc?code='+uid;
    };
  </script>
@stop
@section('main')
    <section id="container" class="">
    <div class="header_join"> 
        <div class="found">
            <p>欢迎 <span id="nickname">Xavier</span> 来到「无界商圈」</p>
        </div>
    </div>
    <div>
        <p class="hundred"><span id="score">100</span> &nbsp;积分</p>
        <p class="account">已放入您「无界商圈」账号<br/>赶紧前往应用商店下载App</p>
    </div>

    <div class="intro">
        <p class="wjsq ">      
            <span>无界商圈</span>
        </p>
        <p class="intro_del">创业讲座、大咖分享、产品发布等高端活动可参与可观看，<br>
            打破本地限制，可跨域互动交流！
        </p>
        <img src="{{URL::asset('/')}}/images/iPhone6.png" alt="背景图片"/>
    </div>
    <div class="module hide">
        <img src="{{URL::asset('/')}}/images/safari.png" alt="点击右上角，浏览器中打开">
    </div>
    <a type="button" class="btn-foot" href="javascript:void">下载App</a>

    </section>
@stop

@section('endjs')
<script>
    var $body = $('body');
    document.title = "成功加入「无界商圈」";
    // hack在微信等webview中无法修改document.title的情况
    var $iframe = $('<iframe ></iframe>').on('load', function() {
    setTimeout(function() {
    $iframe.off('load').remove()
    }, 0)
    }).appendTo($body)
</script>
<script> 
  Zepto(function(){
        var uid="{{$uid}}";
    //获取昵称与积分    
        function getinfo(uid){
            var param={};
            param['uid']=uid;
            var url=labUser.api_path+"/login/userinfo";
            ajaxRequest(param,url,function(data){
                if(data.status){
                    $("#nickname").html(data.message.nickname);
                    $("#score").html(data.message.score);
                };
            });
        };
        getinfo(uid);
   //下载App     
        $(".btn-foot").click(function(){
            if(is_weixin()){
                    $(".module").removeClass("hide");
                    $(".module").click(function(){
                     $(this).addClass("hide");
                    });
            }else if (isiOS) {
                 window.location.href = 'https://itunes.apple.com/app/id981501194'; 
              }else if (isAndroid) {
                window.location.href = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.tyrbl.wujiesq';
            };
        });
     });
</script>
@stop