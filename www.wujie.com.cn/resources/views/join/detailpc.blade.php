@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/invite-pc.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section id="container" class="">
    <div class="head">
    <h1>成功加入「无界商圈」</h1>
    <p class="time"> <span class="date"></span> &nbsp; &nbsp;<span>无界商圈</span></p>
</div>
<div class="main">
    <div class="fixed">
        <div class="erwei">
            <img src="{{URL::asset('/')}}/images/erweima.png" alt="公众号二维码"/>
            <p>微信扫一扫</p>
            <p>关注该公众号</p>
        </div>   
    </div>
    <div class="header_join">
        <div class="found">
            <p >欢迎&nbsp;<span id="nickname">Xavier</span>&nbsp;来到「无界商圈」</p>
        </div>
    </div>
    
      <!--   <div class="erwei">
            <img src="{{URL::asset('/')}}/images/erweima.png" alt="公众号二维码"/>
            <p>微信扫一扫</p>
            <p>关注该公众号</p>
        </div> -->
    <div>
        <p class="hundred"><span id="score">100</span> &nbsp;积分</p>
        <p class="account">已放入您「无界商圈」账号<br/>赶紧前往应用商店下载App</p>
        <div class="App-erwei">
            <img src="{{URL::asset('/')}}/images/wjerwei.png" alt="二维码"/>
            <p>扫一扫<br/>下载无界商圈APP</p>
        </div>
    </div>
    <div class="intro">
        <p class="wjsq">          
            <span>无界商圈</span>
        </p>
        <p class="intro_del">创业讲座、大咖分享、产品发布等高端活动可参与可观看，<br>
            打破本地限制，可跨域互动交流！
        </p>
        <img src="{{URL::asset('/')}}/images/iPhone6.png" alt="背景图"/>
    </div>
</div>
</section>
@stop

@section('endjs')
<script>
    Zepto(function(){
        //pc端获得当前日期
    var date=new Date();
    var formatDate = function (date) {
        var y = date.getFullYear();
        var m = date.getMonth() + 1;
        m = m < 10 ? '0' + m : m;
        var d = date.getDate();
        d = d < 10 ? ('0' + d) : d;
       // return y + '-' + m + '-' + d;
        $(".date").html(y + '-' + m + '-' + d);
    };
    formatDate(date);

    //获取昵称与积分
        var uid="{{$uid}}";
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
     });
</script>
@stop