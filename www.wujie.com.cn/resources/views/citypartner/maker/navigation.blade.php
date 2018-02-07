<div class="font">
          <h2>我的网点</h2>
</div>
<div class="m-ovo-contain mt20 mb20">
    <div class="m-title tc">
        <ul class="tit f14">
            @include('citypartner.maker.ovo_li')
        </ul>
    </div>
    <div>
        <div class="m-ovo-box">
            <ul>
                <li @if(!isset($type))class="cur"@endif><a href="{{url('citypartner/maker/index')}}">全部活动</a></li>
                <em>|</em>
                <li @if(isset($type)&&$type==1)class="cur"@endif><a href="{{url('citypartner/maker/index?type=1')}}">合办的活动</a></li>
                <em>|</em>
                <li @if(isset($type)&&$type==2)class="cur"@endif><a href="{{url('citypartner/maker/index?type=2')}}">未合办的活动</a></li>
                <em>|</em>
                <li @if(isset($type)&&$type==3)class="cur"@endif><a href="{{url('citypartner/maker/index?type=3')}}">创建的活动</a></li>
                <a class="btn btn-ovo-create" href="{{url('citypartner/maker/storeactivity')}}">
                    创建本地活动
                </a>
            </ul>