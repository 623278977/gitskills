@extends('citypartner.layouts.layout')
@section('title')
    <title>我的收益-总业绩额</title>
@stop
@section('styles')
    <link rel="stylesheet" href="/css/citypartner/share.css" type="text/css"/>
    <link rel="stylesheet" href="/css/citypartner/total_performance.css" type="text/css"/>
@stop
@section('content')
    <div class="container">
        <div class="font">
            <h2>
                查看总业绩额
            </h2>
            <a href="{{ url('citypartner/profit/list?uid='.$uid) }}">返回我的收益</a>
            <a href="{{ url('citypartner/profit/rule?uid='.$uid) }}">收益规则说明</a>
        </div>
        <div class="total">
            <div class="bill">
                <h3>{{ date("Ym",$partner->created_at->getTimestamp()) }}-至今</h3>
                <ul>
                    <li>
                        <p><span>{{ sprintf("%.2f",floor($totalAchievement/100)/100) }}</span></p>
                        <p>总业绩额(万元)</p>
                    </li>
                    <li>
                        <p><span>{{ sprintf("%.2f",floor($myAchievement/100)/100) }}</span></p>
                        <p>我的业绩额(万元)</p>
                    </li>
                    <li>
                        <p><span>{{sprintf("%.2f",floor($teamAchievement/100)/100) }}</span></p>
                        <p>团队成员业绩额(万元)</p>
                    </li>
                </ul>
            </div>
        </div>
        <div class="detail">
            <div class="performance ">
                <h2>业绩明细</h2>
                <form action='{{url('citypartner/profit/achievement')}}' method="get" >
                <label for="cycle">选择周期</label>
             	<input type="text" class="select"  value="{{ isset($selPeroid) ? $selPeroid:$peroid[0]}}" readOnly="true" />
                <ul class="option hide peroid">
                    @foreach($peroid as $p)
                    <li value="{{$p}}" @if(isset($selPeroid) && $selPeroid == $p) style="display:block" @endif >{{$p}}</li>
                    @endforeach
                </ul>
                 <label for="">选择成员</label>
                 <input type="text" class="select" value="{{isset($membername) ? $membername:'自己'}}" readOnly="true" />
                <ul class="option hide member" style="left: 638px">
                    <li value="{{$uid}}" @if($member == $uid) style=" display:block" @endif>自己</li>
                    <li value="-2" @if($member == -2) style=" display:block" @endif>所有成员</li>
                    <li value="-1" @if($member == -1) style=" display:block" @endif>我的团队</li>
                    @foreach($members as $m)
                        <li value="{{$m->uid}}" @if($member == $m->uid) style=" display:block" @endif>{{$m->realname}}</li>
                    @endforeach
                </ul>
                    <input type="hidden" name="search" value="1">
                    <input type="hidden" name="uid" value="{{ $uid }}">
                    <input type="hidden"   name="member" value="{{isset($member) ? $member :$uid}}"/>
                    <input type="hidden" name="peroid" value="{{ isset($selPeroid) ? $selPeroid:$peroid[0]}}" />
                    <button  type="submit" id="btnSearch" style="display: none">查询</button>
                    <a href="javascript:void(0)" id="search">查询</a>
                </form>
                <p class="num"><span>{{ sprintf("%.2f",floor($peroidAchievement/100)/100) }}</span>万元</p>
                <p>总业绩额</p>
                <table>
                    <caption>{{isset($name) ? $name:$partner->realname}}（{{$begin .'--'. $end}}）每月业绩额</caption>
                    <thead>
                    <tr>
                        <th>月份</th>
                        @for($i=$begin;$i<=$end;$i++)
                            <th>{{$i}}</th>
                        @endfor
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>业绩额（万元）</td>
                        @foreach($monthAchievement as $ma)
                            <td><span>{{ sprintf("%.2f",floor($ma/100)/100) }}</span></td>
                        @endforeach
                    </tr>
                    </tbody>
                </table>
                <table>
                    <caption>{{isset($name) ? $name:$partner->realname}}--业绩明细</caption>
                    <thead>
                    <tr>
                        <th>时间</th>
                        <th class="middle">名称</th>
                        <th>业绩额（万元）</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($lists as $list)
                        <tr>
                            <td>{{ date("Y.m.d",$list->arrival_at) }}<p>{{ date("H:i",$list->arrival_at) }}</p></td>
                            <td  class="middle"><div title="">{{ $list->title }}</div></td>
                            <td><span>{{ sprintf("%.2f",floor($list->amount/100)/100) }}</span></td>
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
@stop
@section('scripts')
    <script src="/js/citypartner/jquery.min.js"></script>
    <script src="/js/citypartner/common.js"></script>
    <script>
        $("#search").click(function(){
            $("#btnSearch").trigger('click');
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
        $(".member>li").click(function(){
            var text=$(this).text();
            $(this).parent("ul").prev(".select").val( text);
            $(this).parent("ul").addClass("hide");
            $("input[name='member']").val($(this).val());
        });
         $(".peroid>li").click(function(){
             var text=$(this).text();
             $(this).parent("ul").prev(".select").val( text);
             $(this).parent("ul").addClass("hide");
             $("input[name='peroid']").val(text);
         });
        $(document).click(function(){
            $(".option").addClass("hide");
        })
    });
   //-------  //    
    </script>

@stop
