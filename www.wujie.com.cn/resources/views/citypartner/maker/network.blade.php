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

                <div class="m-ovo-box">
                    <div class="intro OVO_intro" >
                        <div class="basic">
                            <div class="left">基础信息</div>
                            <div class="right">
                                <p><label for="">运营中心名称</label><input type="text" value="{{$data['name']}}" readonly="true"/></p>
                                <p><label for="">运营中心地址</label><input type="text" value="{{$data['addres']}}" readonly="true"/></p>
                                <p><label for="">会议规格</label><input type="text" value="{{$data['boardroom_size']}}" readonly="true"/></p>
                                <p><label for="">设备规格</label><input type="text" value="{{$data['device_size']?array_get(App\Models\CityPartner\Network::$_DEVICES,$data['device_size']):''}}" readonly="true"/></p>
                            </div>
                        </div>
                        <div class="contact">
                            <div class="left">联系方式</div>
                            <div class="right">
                                <p><label for="">城市合伙人</label><input type="text" value="{{$data['institution']}}" readonly="true"/></p>
                                <p><label for="">联系方式</label><input type="text" value="{{$data['phone']}}" readonly="true"/></p>
                                <p><label for="">合伙人证件类型</label><input type="text" value="{{$data['credentials']}}" readonly="true"/></p>
                                <p><label for="">合伙人证件编号</label><input type="text" value="{{$data['credentials_num']}}" readonly="true"/></p>
                                <p><label for="">运营中心负责人</label><input type="text" value="{{$data['director']}}" readonly="true"/></p>
                                <p><label for="">联系方式</label><input type="text" value="{{$data['director_phone']}}" readonly="true"/></p>
                                <p><label for="">业务扩展对接人</label><input type="text" value="{{$data['expand']}}" readonly="true"/></p>
                                <p><label for="">客户经理对接人</label><input type="text" value="{{$data['manager']}}" readonly="true"/></p>
                            </div>
                        </div>
                        <div class="introduce">
                            <div class="left">运营中心背景介绍</div>
                            <div class="right">
                                <p><label for="">城市合伙人背景介绍</label><textarea name=""  class="text_intro"  readonly="true">{{$data['context']}}</textarea></p>
                                <p><label for="">运营中心团队情况</label><textarea name=""  class="text_intro"  readonly="true">{{$data['team']}}</textarea></p>
                                <p><label for="">城市合伙人人脉渠道资源情况</label><textarea name="" class="text_intro"  readonly="true">{{$data['resource']}}</textarea></p>
                                <p><label for="">城市合伙人当地媒体渠道资源情况</label><textarea name="" class="text_intro" readonly="true">{{$data['media']}}</textarea></p>
                                <p><label for="">培训业务扩展计划</label><textarea name="" class="text_intro" readonly="true">{{$data['train_expand']}}</textarea></p>
                                <p><label for="">招商扩展计划</label><textarea name="" class="text_intro" readonly="true">{{$data['attraction_expand']}}</textarea></p>
                                <p><label for="">投融业务扩展计划</label><textarea name="" class="text_intro" readonly="true">{{$data['investment_expand']}}</textarea></p>
                                <p><label for="">其他业务扩展计划</label><textarea name="" class="text_intro" readonly="true">{{$data['other_expand']}}</textarea></p>
                            </div>
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
        $("body").css("background-color","#f0f1f3");
    });
    
</script>
@stop
