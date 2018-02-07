<!DOCTYPE html>
<html>
<head lang="en">
    <meta charset="UTF-8">
    <title>抽奖页面_获奖页面</title>
    <link rel="stylesheet" type="text/css"  href="{{URL::asset('/')}}css/game/web/page.css">
</head>
<body>
   <div class="prideBlock relative">
   	 <!-- 一等奖 -->

         <div class="prideOne" id="winners">
			 @if(!empty($prize))
				 <h1 id="title">{{ $prize->name }}</h1>
				 <ul>
					 @foreach($winners as $key => $winner)
						 <li class="itemBlock @if($count==1) itemB @elseif($count==2) itemB_1 @elseif($count==3) itemB_2 @else itemB_3 @endif">
							 <div class="@if($count>3) uImg_2 @else uImg_1 @endif">
								 <img src="{{ $winner['avatar'] }}">
							 </div>
							 <div class="@if($count>3) uTxt_2 @else uTxt_1 @endif">
								 <p class="userName">{{ $winner['username'] }}</p>
								 <p class="userAddress">{{ $winner['city'] }}会场</p>
							 </div>
						 </li>
						 <?php if($key==13) break; ?>
					 @endforeach
				 </ul>
				 <input type="hidden" post_time="0" id="ajax_time">
				 <input type="hidden" prize_id="{{ $prize->id }}" id="prize">
			 @endif
         </div>
         <div class="jiantouBlock absolute"><a class="jiantou" id="click" href="javascript:;"></a></div>

   </div>
</body>
<script type="text/javascript" src="{{URL::asset('/')}}js/common.js"></script>
<script type="text/javascript" src="{{URL::asset('/')}}js/jquery-1.11.1.min.js"></script>
<script>
    $(function(){
        $("#click").click(function(){
            var params = {};
            params['prize_id'] = $("#prize").attr("prize_id");
            params['post_time'] = $("#ajax_time").attr("post_time");
            var url = 'getresult';
            ajaxRequest(params,url,function(data){
                if(data.status==true){
                    var winners = data.message.winners;
                    var post_time = data.message.post_time;
                    var count = data.message.count;
                    var prize = data.message.prize;
					if(winners.length==0){
						alert("展示完毕");
						return ;
					}
					var winner_html = '';
					$.each(winners,function(i,n){
						winner_html +='<li class="itemBlock ';
						if(count==1) winner_html+='itemB'; else if(count==2) winner_html+='itemB_1'; else if(count==3) winner_html+='itemB_2'; else winner_html+='itemB_3';
						winner_html +='">';
						winner_html +='<div class="';
						if(count>3) winner_html +='uImg_2'; else winner_html +='uImg_1';
						winner_html +='">';
						winner_html +='<img src="'+n.avatar+'">';
						winner_html +='</div>';
						winner_html +='<div class="';
						if(count>3) winner_html +='uTxt_2';else winner_html +='uTxt_1';
						winner_html +='">';
						winner_html +='<p class="userName ">'+ n.username+'</p>';
						winner_html +='<p class="userAddress">'+ n.city+'会场</p>';
						winner_html +='</div>';
						winner_html +='</li>';
					});
					$("#prize").attr("prize_id",prize.id);
					$("#title").html(prize.name);
					$("#ajax_time").attr("post_time",post_time);
					$("#winners ul").html(winner_html);
                }
            });
        });
    });
</script>
</html>