@extends('citypartner.layouts.layout')
@section('title')
    <title>消息中心</title>
@stop
@section('styles')
    <link rel="stylesheet" href="/css/citypartner/share.css"/>
    <link rel="stylesheet" href="/css/citypartner/message.css"/>
@stop
@section('content')
	<div class="no_msg hidden">
           <img src="/images/citypartner/img/no_msg.png"/>
           <p>目前没有任何消息哦~</p>
   </div>
  <div class="container">
    <div class="font">
        <h2>
            消息中心
        </h2>
     </div>
      <div class="detail">
          @foreach($message as $m)
          <p  @if($m->is_read==1) class="open" @else class="close" @endif  ><a href="/citypartner/message/detail?id={{ $m->id }}"><span ></span>【无界商圈】&nbsp;{{$m->title}}</a>
              {{--@if($m->type=='newActivity') 活动推送通知  @elseif($m->type=='cooActivity') 合办活动通知  @elseif($m->type=='official') 官方通知  @elseif($m->type=='newTeamer') 新团队成员通知 @elseif($m->type=='newMember') 新会员通知 @elseif($m->type=='statusChanged') 业务状态有更新 @elseif($m->type=='monthBills') 月账单 @elseif($m->type=='periodBills') 期账单 @endif--}}
              <label>{{ date('Y-m-d',$m->created_at->getTimestamp()) }}<i>{{ date('H:i',$m->created_at->getTimestamp()) }}</i></label></p>
          @endforeach
          <div>@if( $message->total() > $message->perPage()){{$message->currentPage()==1 ? 1:($message->currentPage()-1)*$message->perPage()+1}}-{{$message->currentPage()*($message->perPage())}}条，共<span>{{ $message->total() }}条</span><a href="{{ $message->url($message->currentPage() >1 ? $message->currentPage()-1:1 )}}">&lt;上一页</a><a href="{{$message->nextPageUrl()}}">下一页&gt;</a><a href="{{ $message->url($message->lastPage()) }}">尾页&gt;&gt;</a>@else {{$message->currentPage()==1 ? 1:($message->currentPage()-1)*$message->perPage()+1}}-{{$message->currentPage()*($message->perPage())}}条，共<span>{{ $message->total() }}条</span> @endif</div>
      </div>

  </div>
@stop
@section('scripts')
    <script type="text/javascript" src="/js/citypartner/jquery-1.6.2.min.js"></script>
     <script>
       $(document).ready(function(){
          var n=$(".detail>p").size();
           	if(n==0){
             $(".no_msg").removeClass("hidden");
             $(".detail").addClass("hidden");
           }
       });
    </script>
@stop