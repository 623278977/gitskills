<?php 
if(Auth::check()){
	$user = Auth::user();
}

$route = URL::full();

//是经纪人页面并且不是经纪人邀请投资人页面
if(strpos($route, 'webapp/agent') && !strpos($route, 'webapp/agent/register/detail')&& !strpos($route, 'webapp/agent/register/registercustomer')){
    $title = '无界商圈经纪人';
    $fontsize = 'agentfontsize';
}else{
    $title = '无界商圈';
    $fontsize = 'fontsize';
}


?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>
        @section('title')
            {{$title}}
        @show
    </title>
	<meta charset="UTF-8">
    <!-- 屏幕的缩放及初始大小 -->
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, viewport-fit=cover">
    <!-- 苹果设备自动识别电话、邮箱相关 -->
    <meta content="telephone=no" name="format-detection"/>
    <meta content="email=no" name="format-detection"/>
	<!--是否下载app
	<meta name='apple-itunes-app' content='app-id=981501194'>
	-->
	<link href="{{URL::asset('/')}}/css/reset.css" rel="stylesheet" type="text/css"/>
	<link href="{{URL::asset('/')}}/css/common.css" rel="stylesheet" type="text/css"/>
	@yield('css')
	<script type="text/javascript" src="{{URL::asset('/')}}/js/lib/zeptojs/zepto.min.js"></script>
	<script type="text/javascript" src="{{URL::asset('/')}}/js/zepto/fastclick.js"></script>
	<script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
	<script type="text/javascript" src="{{URL::asset('/')}}/js/common.js?v=02281402"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/dist/{{$fontsize}}.min.js"></script>

    <script>
	<?php if(isset($user)){
			$nickname=$user->nickname;
			$username = $user->username;
	}else{
			$realname='';
			$username='';
		}?>
		var labUser = {
			'path':'{{URL::asset("/")}}',
			'api_path':'{{URL::asset("/api")}}',
        	'agent_path':'{{URL::asset("/agent")}}',
			'token':'{{ csrf_token() }}',
			'uid':'<?php echo isset($user->uid)?$user->uid:0?>',
			'nickname':'<?php echo isset($user->nickname)?$user->nickname:''?>',
			'username':'<?php echo isset($user->username)?$user->username:''?>',
			'avatar': '<?php echo isset($user->uid)?getImage($user->avatar,'avatar','thumb'): URL::asset("/")."images/default/avator-m.png"?>'
//			'download_url':'http://a.app.qq.com/o/simple.jsp?pkgname=com.tyrbl.wujietr'
		};
	</script>


    @yield('beforejs')
  </head>
<body class="bgfont">
@yield('main')

</body>
<script type="text/javascript">

	if (window.upLogger){
		upLogger.tracking({action:'save',method:'beacon',appParams:JSON.stringify({uid:labUser.uid,id:0,table:''})});
	}
	// 用户行为统计代码
	function recordStaticLogerr(uid,type,msg){
		if (window.upLogger){
			var params = {
				action:'save',
				method:'click',
				type:msg,
				clickTarget:type,
				uid:uid
			};
			upLogger.clickLog(params);
		}
	}
</script>

@yield('endjs')
</html>

