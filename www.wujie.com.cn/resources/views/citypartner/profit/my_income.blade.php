@extends('citypartner.layouts.layout')
@section('title')
    <title>我的收益-总盈利</title>
@stop
@section('styles')
    <link rel="stylesheet" href="/css/citypartner/share.css" type="text/css"/>
    <link rel="stylesheet" href="/css/citypartner/total_performance.css" type="text/css"/>
@stop
@section('content')
<div class="container">
    <div class="font">
        <h2>
            我的收益
        </h2>
        <a href="/citypartner/profit/list?uid={{ $partner->uid }}&peroid={{implode('--',$month)}}">返回我的收益</a>
        <a href="/citypartner/profit/rule?uid={{ $partner->uid }}">收益规则说明</a>
    </div>
    <div class="total">
        <div class="income">
             <p><span>{{ sprintf("%.2f",$partner->partnerIncome->sum('amount'))  }}</span></p>
             <p>收益总额(元)</p>
        </div>
    </div>
    <div class="detail">
        <div class="performance ">
            <h2>收益账单</h2>
            <form>
                <label for="cycle">选择周期</label>
                <input type="hidden" name="uid" value="{{ $partner->uid }}">
              	<input type="text" class="select" value="{{implode('--',$month)}}" readOnly="true" />
                <ul class="option hide">
                    @foreach($peroid as $k=>$p) <li>{{$p}}</li>

                    @endforeach
                </ul>
            </form>
            <p class="num">@if($month[1]  < date("Ym",time()) ) <span >{{ sprintf("%.2f",array_sum($month_profit)+$extra_profit+$team_profit+$special_profit)  }}</span>@else <span>周期未结束</span> @endif</p>
            <p>周期总提成(元)</p>
            <div class="taste">
                <ul>
                    <li>
                        <p class="num"><span>{{ sprintf("%.2f",array_sum($month_profit)) }}</span></p>
                        <p>保底提成(元)</p>
                    </li>
                    <li>
                        <p class="num">@if($month[1]  < date("Ym",time()) ) <span >{{ $extra_profit  }}</span>@else <span>周期未结束</span> @endif</p>
                        <p>超额提成(元)</p>
                    </li>
                    <li>
                        <p class="num">@if($month[1]  < date("Ym",time()) ) <span >{{ $team_profit   }}</span>@else <span>周期未结束</span> @endif</p>
                        <p>团队提成(元)</p>
                    </li>
                    <li>
                        <p class="num">@if($month[1]  < date("Ym",time()) ) <span >{{  $special_profit  }}</span>@else <span>周期未结束</span> @endif</p>
                        <p>特别奖励(元)</p>
                    </li>
                </ul>
            </div>
            <table style="padding-bottom:100px;">
                <caption id="tbl_title">{{$partner->realname}}(<span>{{implode('--',$month)}}</span>)每月收益</caption>
                <thead>
                <tr>
                    <th>月份</th>
                    @for($i = $month[0] ; $i<= $month[1]; $i++)
                        @if( $i < date('Ym',time()))
                            <th>{{ $i }}</th>
                        @else
                            <th>{{ $i }}</th>
                        @endif
                    @endfor
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>保底提成（元）</td>
                    @foreach($month_profit as $mp)
                        <td><span>{{ $mp }}</span></td>
                        @endforeach
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@stop
@section('scripts')
    <script src="/js/citypartner/jquery.min.js"></script>
    <script src="/js/citypartner/common.js"></script>
    <script>
        $('#cycle').change(function(){
            var uid=$("input[name='uid']").val();
            var peroid =$(this).val();
            location.href='/citypartner/profit/income?uid='+uid+'&peroid='+peroid;
        });
        
            //新添js      
     $(function(){
        $(".select").click(function(event){
            event.stopPropagation();
           $(this).next("ul").toggleClass("hide");
        })
        $(".option>li").mouseover(function(){
            $(this).addClass("choose");
            $(this).siblings("li").removeClass("choose")
        });
        $(".option>li").click(function(){
            var text=$(this).text();
            $(this).parent("ul").prev(".select").val( text);
            $(this).parent("ul").addClass("hide");
            var uid=$("input[name='uid']").val();
            var peroid =text;
            location.href='/citypartner/profit/income?uid='+uid+'&peroid='+peroid;
        });
        $(document).click(function(){
            $(".option").addClass("hide");
        })
    });
   //-------  //    
    </script>
@stop