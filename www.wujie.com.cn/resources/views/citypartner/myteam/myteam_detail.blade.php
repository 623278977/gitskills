@extends('citypartner.layouts.layout')
@section('title')
    <title>我的团队-详情页</title>
@stop
@section('styles')
    <link rel="stylesheet" type="text/css" href="/css/citypartner/share.css"/>
    <link rel="stylesheet" type="text/css" href="/css/citypartner/myteam_detail.css"/>
@stop
@section('content')
    <div class="container">
        <div class="font">
            <h2>
                我的团队
            </h2>
            <ul>
                <li>温馨提示：带有该标志<span></span>为拥有OVO中心的客户</li>
                <li><a href="/citypartner/myteam/index">返回我的团队</a></li>
            </ul>
        </div>
        <div class="main">
            <div>
                <div class="vertical">
                    NO.{{$no}}
                </div>
                <div class="intro">
                    <div class="intro_head">
                        <a style="background: url('{{getImage($detailInfo[1]->avatar,'avatar','')}}') no-repeat;background-size:100% 100%;
                                filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='{{getImage($detailInfo[1]->avatar,'avatar','')}}',sizingMethod='scale');
                                -ms-filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='{{getImage($detailInfo[1]->avatar,'avatar','')}}', sizingMethod='scale');">
                            @if($detailInfo[1]->network_id)
                                <img src="/images/citypartner/img/OVO.png" alt=""/>
                            @endif
                        </a>
                    </div>
                    <div class="intro_detail">
                        <p>成员姓名：{{$detailInfo[1]->realname}}</p>

                        <p>成员地区：中国&nbsp;&nbsp;&nbsp;&nbsp;{{str_replace('市','',$detailInfo[1]->zone_id)}}</p>

                        <p>联系方式：{{$detailInfo[1]->username}}</p>

                        <p>OVO中心：@if($detailInfo[1]->network_id)是@else否@endif</p>
                    </div>
                    <div class="intro_per">
                        <p>业绩总额 (万元)</p>

                        <p><span>{{bcdiv($totalamount,10000,2)}}</span></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="detail">
            <div>
                <h2>近期业绩明细</h2>
                <table>
                    <thead>
                    <tr>
                        <th>时间</th>
                        <th>名称</th>
                        <th>业绩额（万元）</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($detailInfo[0] as $item)
                        <tr>
                            <td>{{$item->arrival_at[0]}}<p>{{$item->arrival_at[1]}}</p></td>
                            <td>
                                <div title="">{{$item->title}}</div>
                            </td>
                            <td><span>{{bcdiv($item->amount,10000,2)}}</span></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <p>
                    @if($total>20)
                        @if($currentPage>1 && $currentPage==$totalPage)
                            <a href="/citypartner/myteam/detail?page=1&uid={{$detailInfo[1]->uid}}">&lt;&lt;首页</a>
                            <a href="@if(!empty($previosuPage)){{$previosuPage}}@else # @endif">&lt;上一页</a>
                        @endif
                    @endif
                    {{$startPage}}-{{$endPage}}条，共<span>{{$total}}
                        条</span>
                    @if($total>20)
                        @if($currentPage>1 && $currentPage!=$totalPage)
                            <a href="@if(!empty($previosuPage)){{$previosuPage}}@else # @endif">&lt;上一页</a>
                        @endif
                        @if($currentPage!=$totalPage)
                            <a href="@if(!empty($nextPageUrl)){{$nextPageUrl}}@else # @endif">下一页&gt;</a>
                                <a href="{{$detailInfo[0]->url($lastPage.'&uid='.$detailInfo[1]->uid)}}">尾页&gt;&gt;</a>
                        @endif
                </p>
                @endif
            </div>
        </div>
    </div>
@stop