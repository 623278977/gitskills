@extends('citypartner.layouts.layout')
@section('title')
    <title>我的收益</title>
@stop
@section('styles')
    <link rel="stylesheet" href="/css/citypartner/share.css" type="text/css"/>
    <link rel="stylesheet" href="/css/citypartner/income.css" type="text/css"/>
@stop
@section('content')
    <div class="container">
        <div class="font">
            <h2>
                我的收益
            </h2>
            <a href="/citypartner/profit/rule?uid={{ $uid }}">收益规则说明</a>
        </div>
        <div class="total">
            <div class="bill">
                <h3>总账单</h3>
                <ul>
                    <li>
                        <p><span>{{ $totalProfit }}</span></p>
                        <p>总盈利(元)</p>
                        <a href="/citypartner/profit/income?uid={{$uid}}">查看</a>
                    </li>
                    <li>
                        <p><span>{{ sprintf("%.2f",floor($totalAmount/100)/100) }}</span></p>
                        <p>总业绩额(万元)</p>
                        <a href="/citypartner/profit/achievement?uid={{$uid}}">查看</a>
                    </li>
                </ul>
            </div>
            <div class="record">
                <h3>本期业绩额</h3>
                <ul>
                    <li>
                        <p><span>{{ sprintf("%.2f",floor($currentAchievement/100)/100) }}</span></p>
                        <p>已达业绩额(万元)</p>
                        <a href="/citypartner/profit/current?uid={{$uid}}&peroid={{ $peroid }}">本期业绩明细</a>
                    </li>
                    <li>
                        <p class="time">本期时间：<time>{{ $peroid }}</time></p>
                        <p>已达成比例：<time>{{$proportion}}%</time></p>
                    </li>
                </ul>
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
                        <th>来源</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($achievement as $a)
                    <tr>
                        <td>{{ date("Y.m.d",$a->arrival_at) }}<p>{{ date("H:i",$a->arrival_at) }}</p></td>
                        <td><div title=""> {{ $a->title }}</div></td>
                        <td><span>{{ sprintf("%.2f",floor($a->amount/100)/100) }}</span></td>
                        <td>@if($a->partner_uid == $uid && $a->range=='personal') 自己 @elseif($a->p_uid == $uid && $a->range=='personal') {{$a->cityPartner->realname}} @else 团队 @endif</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
                <p>@if( $achievement->total() > $achievement->perPage())
                        @if( $achievement->currentPage()!=1)<a href="{{ $achievement->url(1).'#001'}}">&lt;&lt;首页</a><a href="{{ $achievement->url($achievement->currentPage() >1 ? $achievement->currentPage()-1:1 ).'#001'}}">&lt;上一页</a>@endif
                        {{$achievement->currentPage()==1 ? 1:($achievement->currentPage()-1)*$achievement->perPage()+1}}-{{$achievement->currentPage()*($achievement->perPage())}}条，共<span>{{  $achievement->total() }}条</span>
                        @if($achievement->currentPage()!=$achievement->lastPage())<a href="{{$achievement->nextPageUrl().'#001'}}">下一页&gt;</a><a href="{{ $achievement->url($achievement->lastPage()).'#001' }}">尾页&gt;&gt;</a>@endif
                        @else{{$achievement->currentPage()==1 ? 1:($achievement->currentPage()-1)*$achievement->perPage()+1}}-{{$achievement->currentPage()*($achievement->perPage())}}条，共<span>{{  $achievement->total() }}条</span>
                    @endif</p>
                <a name="001"></a>
            </div>
        </div>
    </div>
@stop