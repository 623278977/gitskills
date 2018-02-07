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
        <div class="m-ovo-contain mt20 mb20">
            <div class="m-title tc">
                <ul class="tit f14">
                    @include('citypartner.maker.ovo_li')
                </ul>
            </div>
            <div>

                <div class="m-ovo-box ">


                     <div >
                            <div class="up">
                                <div class="member_detail">
                                    <div class="intro_head">
                                        <a  style="background: url('{{$user['avatar']}}') no-repeat;background-size:100% 100%;
                                        filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='{{$user['avatar']}}',sizingMethod='scale');
                                        -ms-filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='{{$user['avatar']}}', sizingMethod='scale');">
                                        </a>
                                        @if($is_maker)<img src="/images/citypartner/img/OVO.png" alt=""/>@endif
                                    </div>
                                    <div class="intro_detail">
                                        <p>成员姓名：{{$user['nickname']}}</p>
                                        <p>成员地区：{{$user['zone']}}</p>
                                        <p>联系方式：{{$user['username']}}</p>
                                        <p>关注行业：{{$user['industry']}}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="member">
                                <h3>参与的活动<span>产生的总业绩：{{$price?$price/10000:0}}万</span> </h3>
                                <table class="act">
                                    <thead>
                                    <tr>
                                        <th>时间</th>
                                        <th>动态</th>
                                        <th>报名费用（元）</th>
                                    </tr>
                                    </thead>
                                    <tbody id="tbody">
                                        @foreach($lists as $item)
                                        <tr>
                                            <td>{{$item->created_at}}</td>
                                            <td>@if($item->activity)报名-{{$item->activity->subject}}@endif</td>
                                            <td>{{$item->price?$item->price/10000:0}}万</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <p id="pageControl">
                                    @if($lists->count())
                                    {{$first=($lists->currentPage()-1)*($lists->perPage())+1}}-{{$first+$lists->perPage()-1}}条，
                                    @endif
                                    共<span>{{$lists->total()}}条</span>
                                    @if($lists->currentPage() > 1)
                                    <a href="{{$lists->url(1)}}">&lt;&lt;首页</a>
                                    <a href="{{$lists->url($lists->currentPage()-1)}}">&lt;上一页</a>
                                    @endif
                                    @if ($lists->lastPage() > $lists->currentPage())
                                    <a href="{{$lists->url($lists->currentPage()+1)}}">下一页&gt;</a>
                                    <a href="{{$lists->url($lists->lastPage())}}">尾页&gt;&gt;</a>
                                    @endif
                                </p>
                            </div>
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
    @if($applyCount)
    var params={
        page:1,
        uid:'{{$user['uid']}}'
    };
    myovo.getapplylist(params);
    @endif
    $(document).on("click",".ajaxPage",function(){
        params.page=$(this).attr('page');
        myovo.getapplylist(params);
        return false;
    });
</script>
@stop
