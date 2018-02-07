<li @if(Request::is('citypartner/maker/index')) class="cur"@endif><a href="{{url('citypartner/maker/index')}}">我的活动（<i>{{$total}}</i>） </a></li>
<li @if(Request::is('citypartner/maker/member','citypartner/maker/memberdetail')) class="cur"@endif><a href="{{url('citypartner/maker/member')}}">我的会员</a></li>
<li @if(Request::is('citypartner/maker/network')) class="cur"@endif><a href="{{url('citypartner/maker/network')}}">网点信息</a></li>
<span></span>