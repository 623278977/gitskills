<?php
if($user=\App\Models\CityPartner\Entity::getCurrentuser()){
    $user['avatar']=getImage($user['avatar'],'avatar','thumb');
}else{
    //return \Illuminate\Support\Facades\Redirect::route('user.login');
}
?>
<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <link rel="stylesheet" href="/css/citypartner/share.css"/>
    @yield('css')
    <script type="text/javascript" src="/js/citypartner/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" src="/js/citypartner/common.js"></script>
    <script>
        var labUser = {
            'uid':'<?php echo isset($user->uid)?$user->uid:0?>',
            'nickname':'<?php echo isset($user->realname)?$user->realname:''?>',
            'avatar': '<?php echo isset($user->uid)?getImage($user->avatar,'avatar','thumb'): URL::asset("/")."images/default/avator-m.png"?>'
        };
    </script>
    @yield('js')
    <title>
    @section('title')
    @stop
    </title>
    <script type="text/javascript">
        var uploadUrl = "{{url('citypartner/upload/index')}}";
        var url_prex="http://mt.wujie.com.cn/citypartner/";
    </script>
</head>
<body>
<header>
    <div class="header">
        <div class="login">
            <div>
                <ul>
                    <li>你好，<a href="">{{$user->nickname}}</a></li>
                    <li><a href="" >退出</a></li>
                    <li class="message">
                        <a href="" >消息通知</a>
                        <img src="/images/citypartner/img/xiaoxi.png" alt=""/>
                    </li>
                </ul>
            </div>
        </div>
        <div class="nav">
            <div class="nav_brand">
                <a href="#" >
                    <img src="/images/citypartner/img/logo_01.png" alt="城市合伙人"/>
                </a>
            </div>
            <div class="user">
                <div>
                    <a class="head" href="" style="background: url('{{$user['avatar']}}');">
                    </a>
                    <div class="user_name">
                        <div>
                            <p>{{$user->realname}}</p>
                            <a >城市合伙人</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="nav_menu">
                <ul>
                    <li><a href="">首页</a></li>
                    <li class="visited"><a href="{{url('citypartner/maker/index')}}">我的OVO中心</a></li>
                    <li class="third"><a href="#">我的团队</a></li>
                    <li ><a href="">我的收益</a></li>
                    <li ><a href="" >我的业务</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>
@yield('main')
<div class="footer">
    <div>
        <p>服务热线：400-033-0161</p>
        <p>版权所有：&copy;2012-2016 tyrbl.com,all rights reserved 杭州天涯若比邻网络信息服务有限公司浙ICP备案号：12021152号-2</p>
    </div>
</div>
@yield('endjs')
</body>
</html>