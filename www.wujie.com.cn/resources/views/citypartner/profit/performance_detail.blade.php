@extends('citypartner.layouts.layout')
@section('title')
    <title>我的收益-本期业务明细</title>
@stop
@section('styles')
    <link rel="stylesheet" href="/css/citypartner/share.css"/>
    <link rel="stylesheet" href="/css/citypartner/total_performance.css"/>
@stop
@section('content')
<div class="container">
    <div class="font">
        <h2>
            查看总业绩额
        </h2>
        <a href="/citypartner/profit/list?uid={{$uid}}">返回我的收益</a>
        <a href="/citypartner/profit/rule?uid={{$uid}}">收益规则说明</a>
        <input type="hidden" name="uid" value="{{$uid}}">
    </div>
    <div class="total">
        <div class="bill">
            <h3 id="peroid">{{ $peroid }}</h3>

            <ul>
                <li>
                    <p><span>{{ sprintf("%.2f",floor(($totalMyAchievement + $totalTeamAchievement)/100)/100) }}</span>万元</p>
{{--                    <p><span>{{ $totalMyAchievement + $totalTeamAchievement }}</span>元</p>--}}
                    <p>总业绩额</p>
                </li>
                <li>
                    <p><span>{{sprintf("%.2f",floor($totalMyAchievement/100)/100)}}</span>万元</p>
{{--                    <p><span>{{$totalMyAchievement}}</span>元</p>--}}
                    <p>我的业绩额</p>
                </li>
                <li>
                    <p><span>{{sprintf("%.2f",floor($totalTeamAchievement/100)/100)}}</span>万元</p>
{{--                    <p><span>{{$totalTeamAchievement}}</span>元</p>--}}
                    <p>团队成员业绩额</p>
                </li>
            </ul>
        </div>
    </div>
    <div class="performance">
        <table class="per_month" >
        <caption>每月业绩额</caption>
        <thead>
        <tr>
            <th>月份</th>
            <th>业绩总额（万元）</th>
            <th>我的业绩额（万元）</th>
            <th>团队成员业绩额（万元）</th>
        </tr>
        </thead>
        <tbody>
        @foreach($monthAchievement as $ma)
            <tr>
                <td>{{ $ma['month'] }}</td>
                <td><span>{{ sprintf("%.2f",floor(($ma['my'] + $ma['team'])/100)/100)  }}</span></td>
                <td><span>{{ sprintf("%.2f",floor($ma['my']/100)/100)  }}</span></td>
                <td><span>{{ sprintf("%.2f",floor($ma['team']/100)/100)  }}</span></td>
            </tr>
        @endforeach
        </tbody>
    </table>
    </div>
    <div id="part">
        <div class="member">
        <div class="performance">
            <h2>业绩明细</h2>
            <form>
                <label for="">选择成员</label>
             <input type="text" class="select" id="member" value="自己" code="{{$uid}}" readOnly="true" />
                <ul class="option hide">
                    <li value="{{$uid}}"> 自己 </li>
                    @foreach($members as $m)
                        <li value="{{$m->uid}}"> {{ $m->realname }} </li>
                    @endforeach
                </ul>
            </form>
            <p class="num"><span>{{ sprintf("%.2f",floor($totalMyAchievement/100)/100) }}</span>万元</p>
            <p>总业绩额</p>
            <table>
                <caption>{{$name}}（{{ $peroid }}）每月业绩额</caption>
                <thead>
                <tr>
                    <th>月份</th>
                    @for($i=$begin;$i<=$end;$i++)
                        <th>{{ $i }}</th>
                        @endfor
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>业绩额（万元）</td>
                    @foreach($monthAchievement as $ma)
                        <td><span>{{ sprintf("%.2f",floor($ma['my']/100) /100) }}</span></td>
                    @endforeach
                </tr>
                </tbody>
            </table>
            <table>
                <caption><span id="">{{ $name }}</span>--业绩明细</caption>
                <thead>
                <tr>
                    <th>时间</th>
                    <th class="middle">名称</th>
                    <th >业绩额（万元）</th>
                </tr>
                </thead>
                <tbody>
                @foreach($lists as $list)
                    <tr>
                        <td>{{ date("Y.m.d",$list->arrival_at) }}<p>{{ date("H:i",$list->arrival_at) }}</p></td>
                        <td  class="middle"><div title="">{{ $list->title }}</div></td>
                        <td><span>{{ sprintf("%.2f",floor($list->amount/100)/100)}}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p>@if( $lists->total() > $lists->perPage())
                @if($lists->currentPage()!=1)
                    <a href="{{ $lists->url(1).'#001'}}">&lt;&lt;首页</a>
                    <a href="{{ $lists->url($lists->currentPage() >1 ? $lists->currentPage()-1:1 ).'#001'}}">&lt;上一页</a>
                @endif
                {{$lists->currentPage()==1 ? 1:($lists->currentPage()-1)*$lists->perPage()+1}}-{{$lists->currentPage()*($lists->perPage())}}条，共<span>{{ $lists->total() }}条</span>
            @if($lists->currentPage()!=$lists->lastPage())
                <a href="{{$lists->nextPageUrl().'#001'}}">下一页&gt;</a><a href="{{ $lists->url($lists->lastPage()).'#001' }}">尾页&gt;&gt;</a>
            @endif
            @else
                {{$lists->currentPage()==1 ? 1:($lists->currentPage()-1)*$lists->perPage()+1}}-{{$lists->currentPage()*($lists->perPage())}}条，共<span>{{ $lists->total() }}条</span>
            @endif</p>
            <a name="001"></a>
    </div>
    </div>
</div>
@stop
@section('scripts')
    <script src="/js/citypartner/jquery.min.js"></script>
    <script src="/js/citypartner/common.js"></script>
    <script>
        $('#member').change(function(){
            var member = $(this).val();
            var peroid = $('#peroid').html();
            var member = $('#member').val();
            var uid =$("input[name='uid']").val();
            ajaxRequest({member:member,peroid:peroid,uid:uid,member:member},'/citypartner/profit/detail',function(data){
                $("#part").html(data.message);
            });
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
            var code=$(this).val();
//            $(this).parent("ul").prev(".select").val( text);
            $(this).parent("ul").prev(".select").attr('code',code);
            $(this).parent("ul").addClass("hide");
            var peroid = $('#peroid').html();
            var member = $('#member').attr('code');
            var uid =$("input[name='uid']").val();
            ajaxRequest({member:member,peroid:peroid,uid:uid,member:member},'/citypartner/profit/detail',function(data){
                $("#part").html(data.message);
            });
        });
        $(document).click(function(){
            $(".option").addClass("hide");
        })
    });
   //-------  //    
    </script>
@stop