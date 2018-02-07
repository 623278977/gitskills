<header>
    <div class="header">
        <div class="login">
            <div>
                <ul>
                    <li>你好，<a href="/citypartner/account/list?uid={{$partner->uid}}">{{ $partner->realname ?: $partner->username }}</a></li>
                    <li><a href="{{url('citypartner/public/loginout')}}" >退出</a></li>
                    <li class="message">
                    	<span>|</span>
                        <a href="{{url('citypartner/message/list')}}" >消息通知</a>
                        @if($count>0) <img src="/images/citypartner/img/xiaoxi.png" alt=""/> @endif
                    </li>
                </ul>
            </div>
        </div>
        <div class="nav">
            <div class="nav_brand">
                <a href="/citypartner/account/list?uid={{$partner->uid}}" >
                    <img src="/images/citypartner/img/logo_01.png" alt="城市合伙人"/>
                </a>
            </div>
            <div class="user">
                <div>
                    <a class="head" href="/citypartner/account/list?uid={{$partner->uid}}" >
                        <img  src="{{ getImage($partner->avatar,'avatar','') }}" alt="">
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
                    <li ><a href="{{url('citypartner/business/list')}}">我的业务</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>