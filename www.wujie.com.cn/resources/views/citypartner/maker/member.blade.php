@extends('citypartner.layouts.layout')

@section('styles')
    <link rel="stylesheet" href="/css/citypartner/share.css"/>
    <link rel="stylesheet" href="/css/citypartner/reset.css">
    <link rel="stylesheet" href="/css/citypartner/common.css">
    <link rel="stylesheet" href="/css/citypartner/w-pages.css">
    <link rel="stylesheet" href="/css/citypartner/w-ovo.css">
    <link rel="stylesheet" href="/css/citypartner/ovo_member.css">
    <link rel="stylesheet" href="/css/citypartner/account.css">
@stop
@section('title')
<title>我的OVO中心</title>
@stop
@section('content')
<div class="font">
          <h2>我的网点</h2>
</div>
<div class="g-ovo">
    <div class="container myovo">
        <div class="m-ovo-contain mt20">
            <div class="m-title tc">
                <ul class="tit f14">
                    @include('citypartner.maker.ovo_li')
                </ul>
            </div>
            <div>
                <div class="m-ovo-box ">
                    <div class="member">
                        <h3>目前会员总数：{{$memberCount}}人</h3>
                        <table class="detail">
                            <thead>
                            <tr>
                                <th>姓名</th>
                                <th>性别</th>
                                <th>联系方式</th>
                                <th>关注行业</th>
                                <th class="last">详情</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($data))
                                @foreach($data as $v)
                            <tr>
                                <td>{{$v['nickname']}}</td>
                                <td>{{$v['gender']}}</td>
                                <td>{{$v['username']}}</td>
                                <td>{{$v['industry']}}</td>
                                <td class="last"><a href="{{url('citypartner/maker/memberdetail?uid='.$v['uid'])}}">查看</a></td>
                            </tr>
                            @endforeach
                            @endif
                            </tbody>
                        </table>
                        <p>
                        @if($memberCount)
                        {!!$pageHtml;!!}
                        @else
                        没有记录
                        @endif
                        </p>
                    </div>
                </div>

            </div>


        </div>
    </div>
</div>
@stop
@section('scripts')
<script type="text/javascript" src="{{URL::asset('js/')}}/citypartner/jquery-1.8.3.min.js"></script>
<script src="/js/citypartner/index.js"></script>
<script src="/js/citypartner/myovo.js"></script>
<script src="/js/citypartner/gundongtiao.js"></script>
<script>
    $(function(){
        tabs($('.m-title ul.tit li '), $('.m-ovo-box'));
        tabs($('.m-ovo-box ul li'),$('.box-all'));
    });

</script>
@stop