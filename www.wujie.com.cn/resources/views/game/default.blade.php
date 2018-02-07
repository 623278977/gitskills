<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>{{ $act->title }}-抽奖页面</title>
    <link rel="stylesheet" type="text/css"  href="{{URL::asset('/')}}css/game/web/page.css">
    <link rel="stylesheet" type="text/css"  href="{{URL::asset('/')}}css/game/web/animated.css">
    <script src="{{URL::asset('/')}}js/jquery-1.11.1.min.js?v=1.9.1"></script>
</head>
<body>
   <div class="relative sm_maiCon">
     <div class="sm_mainWrap">
     	<ul class="relative">
            @foreach($prizes as $key => $prize)
                <li class="itemList{{$key+1}}" li_id="{{$key+1}}">
                    <!-- 气球gif -->
                    <div class="balloon absolute">
                        <img src="">
                    </div>
                    <!-- 二维码 -->
                    <div class="absolute codeLt anmated">
                        <img src="{{ \App\Models\ActPrize::QrCode($prize->id,$key+1) }}" post_id="{{ $prize->id }}">
                    </div>
                    <!-- 用户信息 -->
                    <div class="userBlock absolute none">
                        <div class="userImg">
                            <img src="">
                        </div>
                        <p class="userName"></p>
                        <p class="userAddress"></p>
                    </div>
                </li>
            @endforeach

     		<div class="clearfix"></div>
     	</ul>
     </div>
     <div class="jiantouBlock absolute"><a class="jiantou" href="{{ createUrl('game/gameresult',array('id' => $act->id)) }}"></a></div>
   </div>
</body>
<script type="text/javascript" src="{{URL::asset('/')}}js/common.js"></script>
<script type="text/javascript" src="{{URL::asset('/')}}js/game.js"></script>
  <script type="text/javascript">
    $(function(){
       
        $(".itemList1 .balloon").children('img').attr({src:" /images/game/web/big/1.gif"});
        $(".itemList2 .balloon").children('img').attr({src:" /images/game/web/big/2.gif"});
        $(".itemList3 .balloon").children('img').attr({src:" /images/game/web/big/3.gif"});
        $(".itemList4 .balloon").children('img').attr({src:" /images/game/web/big/4.gif"});
        $(".itemList5 .balloon").children('img').attr({src:" /images/game/web/big/5.gif"});
        $(".itemList6 .balloon").children('img').attr({src:" /images/game/web/big/6.gif"});
        $(".itemList7 .balloon").children('img').attr({src:" /images/game/web/big/7.gif"});
        $(".itemList8 .balloon").children('img').attr({src:" /images/game/web/big/8.gif"});

     setInterval(function(){
         var params = {};
         params['act_id'] = {{ $act->id }}
         initGame.listen(params);
     },10000);

        $(".jiantouBlock").click(function() {
            $(this).children("a").addClass('jiantou2');
        });
    });
  </script>
</html>