@if(!is_null($partner))
        <!DOCTYPE html >
<html>
<head lang="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>推荐阅读</title>
    <link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}css/citypartner/share.css"/>
    <link rel="stylesheet" href="/css/citypartner/reset.css">
    <link rel="stylesheet" href="/css/citypartner/common.css">
    <link rel="stylesheet" href="/css/citypartner/w-pages.css">
</head>
<body>
<header>
    <div class="header">
        <div class="login">
            <div>
                <ul>
                    <li>你好，<a href="/citypartner/account/list?uid={{$partner->uid}}">{{ $partner->realname ?: $partner->username}}</a></li>
                    <li><a href="{{url('citypartner/public/loginout')}}">退出</a></li>
                    <li class="message">
                    	 <span>|</span>
                        <a href="{{url('citypartner/message/list')}}">消息通知</a>
                        @if($count>0) <img src="/images/citypartner/img/xiaoxi.png" alt=""/> @endif
                    </li>
                </ul>
            </div>
        </div>
        <div class="nav">
            <div class="nav_brand">
                <a href="/citypartner/account/list?uid={{$partner->uid}}">
                    <img src="/images/citypartner/img/logo_01.png" alt="城市合伙人"/>
                </a>
            </div>
            <div class="user">
                <div>
                    <a class="head" href="/citypartner/account/list?uid={{$partner->uid}}">
                        <img src="{{ getImage($partner->avatar,'avatar','') }}" alt="">
                    </a>

                    <div class="user_name">
                        <div>
                            <p>{{ $partner->realname ?: $partner->username}}</p>
                            <a>城市合伙人</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="nav_menu">
                <ul>
                    <li><a href="{{url('citypartner/public/index')}}">首页</a></li>
                    <li><a href="{{url('citypartner/maker/index')}}">我的OVO中心</a></li>
                    <li class="third"><a href="{{url('citypartner/myteam/index')}}">我的团队</a></li>
                    <li><a href="{{url('citypartner/profit/list')}}">我的收益</a></li>
                    <li><a href="{{url('citypartner/business/list')}}">我的业务</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>

@else
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>关于我们</title>
        <link rel="stylesheet" href="/css/citypartner/reset.css">
        <link rel="stylesheet" href="/css/citypartner/common.css">
        <link rel="stylesheet" href="/css/citypartner/w-pages.css">
    <body>
    <div class="m-banner-bg" id="m-banner-bg" style="height: 120px;overflow: hidden;">
        <!--头部-->
        <div class="g-hd ">
            <div class="container">
                <div class="m-logo ">
                    <img src="http://test.wujie.com.cn/images/citypartner/logo.png" alt="">

                    <div class="m-about fr f14 ">
                        <a href="/citypartner/public/index" style="margin-right:650px;">首页</a>
                        <a href="/citypartner/public/index?login=now">立即登录</a>
                        <a href="/citypartner/public/index?register=now" class="register">立即注册</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
    <div class="g-suggested">
        <div class="clearfix"></div>
        <div class="container suggested mt20">
            <h2 class="tc">{{$article?$article->title:''}}</h2>

            <div class="line"><img src="/images/citypartner/line.png" alt=""></div>
            <img src="{{$article?$article->image:''}}" alt="" style="display:block;margin:0 auto;margin-bottom:20px;max-width:900px;">

            <p>
                {!! $article?$article->content:'' !!}
            </p>
        </div>
    </div>
    <div class="g-ft">
        <div class="container">
            <div class="m-foot tc">
                <p>服务热线：400-033-0161</p>

                <p>版权所有：©2012 - 2016 tyrbl.com, all rights reserved 杭州天涯若比邻网络信息服务有限公司浙ICP备案号：12021152号-2 </p>
            </div>
        </div>
    </div>
    </body>
    </html>