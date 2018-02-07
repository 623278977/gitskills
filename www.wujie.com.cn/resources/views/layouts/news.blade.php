<?php
if (Auth::check()) {
    $user = Auth::user();
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>
        @section('title')
            无界商圈
        @show
    </title>
    <meta name="viewport" content="initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="format-detection" content="telephone=no">
    <meta name="format-detection" content="email=no">
    <link rel="stylesheet" href="{{URL::asset('/')}}/css/common.css">
    <link rel="stylesheet" href="{{URL::asset('/')}}/css/frozen.css">
    <link rel="stylesheet" href="{{URL::asset('/')}}/css/global.css">
    <link rel="stylesheet" href="{{URL::asset('/')}}/css/override.css">
    @yield('css')
    <script src="{{URL::asset('/')}}/js/lib/zeptojs/zepto.min.js"></script>
    <script src="{{URL::asset('/')}}/js/frozen.js"></script>
    <script type="text/javascript" src="https://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/common.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/dist/fontsize.min.js"></script>
    <script>
                <?php if (isset($user)) {
                    $nickname = $user->nickname;
                    $username = $user->username;
                } else {
                    $realname = '';
                    $username = '';
                }?>
        var labUser = {
                    'path': '{{URL::asset("/")}}',
                    'api_path': '{{URL::asset("/api")}}',
                    'token': '{{ csrf_token() }}',
                    'uid': '<?php echo isset($user->uid) ? $user->uid : 0?>',
                    'nickname': '<?php echo isset($user->nickname) ? $user->nickname : ''?>',
                    'username': '<?php echo isset($user->username) ? $user->username : ''?>',
                    'avatar': '<?php echo isset($user->uid) ? getImage($user->avatar, 'avatar', 'thumb') : URL::asset("/") . "images/default/avator-m.png"?>'
                };
    </script>
    @yield('beforejs')
</head>
<body ontouchstart="">
@yield('main')
</body>
@yield('endjs')
</html>

