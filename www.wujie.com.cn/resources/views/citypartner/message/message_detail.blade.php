@extends('citypartner.layouts.layout')
@section('title')
    <title>消息中心—消息内容</title>
@stop
@section('styles')
    <link rel="stylesheet" href="/css/citypartner/share.css" type="text/css"/>
    <link rel="stylesheet" href="/css/citypartner/message_detail.css" type="text/css"/>
@stop
@section('content')
   <div class="overlay">
    <div class="pop hidden">
        <h3>确定删除？</h3>
        <p>提示：删除后不可恢复</p>
        <a href="javascript:void(0);"  id="sure">确定</a> <a href="javascript:void(0);" id="cancel">取消</a>
    </div>
   </div>
    <div class="no_msg hidden">
           <img src="/images/citypartner/img/no_msg.png"/>
           <p>目前没有任何消息哦~</p>
   </div>
   <div class="container">
       <div class="font">
           <h2>
               消息详情
           </h2>
           <a href="/citypartner/message/list?uid={{ isset($message->uid) ? $message->uid : ''}}" >返回消息中心</a>
           <a href="javascript:void(0)" id="del">删除当前消息</a>
       </div>
       <div class="main">
           <a href="/citypartner/message/detail?id={{ $prePage }}" class="pre"></a>
           <a href="/citypartner/message/detail?id={{ $nextPage }}" class="next"></a>
           {{--@if($message->type == 'newActivity')--}}
           @if(isset($message->cityPartner))
           <div class="show">
               <input type="hidden" name="currentMsgId" value="{{$message->id}}">
               <input type="hidden" name="nextMsgId" value="{{$nextPage}}">
               <a href="/citypartner/message/list?uid={{ isset($message->uid) ? $message->uid : ''}}" class="close">×</a>
               <h4>尊敬的用户"<span>{{ $message->cityPartner->realname }}</span>"</h4>
               <p>{!! htmlspecialchars_decode($message->content) !!}</p>
               <div class="time">{{ date('Y-m-d  H:i',$message->created_at->getTimestamp()) }}</div>
           </div>
           @endif
       </div>
   </div>
@stop
@section('scripts')
    <script type="text/javascript" src="/js/citypartner/jquery-1.6.2.min.js"></script>
     <script>
       $(document).ready(function(){
          var n=$(".main>div").size();
           	if(n==0){
             $(".no_msg").removeClass("hidden");
           }
       });
         $('#del').click(function(){
             $(".pop").removeClass("hidden");
             $(".overlay").show();
         });
        $('#cancel').click(function(){
             $(".pop").addClass("hidden");
             $(".overlay").hide();
         });
         $('#sure').click(function(){
             var nextMsgId = $("input[name='nextMsgId']").val();
             var currentMsgId = $("input[name='currentMsgId']").val();
             $.post(
                     '/citypartner/message/delete',
                     {
                         nextMsgId:nextMsgId,
                         currentMsgId:currentMsgId
                     },
                     function(data){
                         window.location.href="/citypartner/message/list";
                     }
             );
         });
   </script>
@stop
</body>
</html>