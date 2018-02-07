@extends('citypartner.layouts.layout')
@section('title')
    <title>我的团队</title>
@stop
@section('styles')
    <link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}css/citypartner/share.css"/>
    <link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}css/citypartner/myteam.css"/>
    @stop
@section('content')
    <div class="no_msg hidden">
           <img src="/images/citypartner/img/no_msg.png"/>
           <p>抱歉！您还没有成员哦~</p>
   </div>
<div class="container">
    <div class="font">
        <h2>
            我的团队
        </h2>
        <ul>
            <li>温馨提示：带有该标志<span></span>为拥有OVO中心的客户</li>
            <li><a href="">业绩排行</a></li>
        </ul>
    </div>
    <div class="main">
        <ul>
            @foreach($teaminfo as $k =>$item)
                <li>
                    <div class="vertical">
                        NO.{{$k+1}}
                    </div>
                    <div class="intro">
                        <dl>
                            <dt>
                                <a href="detail?uid={{$item->uid}}&no={{$k+1}}"
                                   style="background: url('{{getImage($item->avatar,'avatar','')}}') no-repeat;background-size:100% 100%; 
                                   	filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='{{getImage($item->avatar,'avatar','')}}',sizingMethod='scale');
                   					-ms-filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='{{getImage($item->avatar,'avatar','')}}', sizingMethod='scale');"></a>
                               <div>
                                 <p><a > <b>{{$item->realname}}</b> @if($item->network_id)<span></span>@endif</a> </p>
                                 <p><a >中国</a>&nbsp;&nbsp;<a >{{$item->zone_id}}</a></p>
                              </div>
                            </dt>
                            <dd>业绩总额<a >@if($item->amount > 9999){{$item->amount2[0]}}.<span style="font-size: 16px">{{$item->amount2[1]}}&nbsp;万</span>@else{{explode('.',$item->amount)[0]?:0}} <span style="font-size: 16px">元</span>@endif </a></dd>
                        </dl>
                    </div>
                </li>

            @endforeach

        </ul>
    </div>
</div>
@stop
@section('scripts')
    <script type="text/javascript" src="/js/citypartner/jquery-1.6.2.min.js"></script>
    <script> 
    	 $(function(){
            $(".main ul li").click(function(){
                $("dt>a",this)[0].click();
            });
            if($(".main ul>li").size()==0){
                $(".no_msg").removeClass("hidden");
                $("body").css("backgroundColor","#fff");
            }else{
                $("body").css("backgroundColor","#f0f1f3");
            }
        });
    </script>
 @stop