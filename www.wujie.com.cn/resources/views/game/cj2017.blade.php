<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>天涯若比邻-年会抽奖</title>
    <link rel="stylesheet" type="text/css" href="../../choujiang/stylesheets/reset.css">
    <link rel="stylesheet" type="text/css" href="../../choujiang/stylesheets/cj2017.css">
    <script>
        var labUser = {
            'app_path': '{{URL::asset("/app/")}}',
            'api_path': '{{URL::asset("/api/")}}',
            'webapp_path': '{{URL::asset("/webapp/")}}',
            'path': '{{URL::asset("/")}}',
            'token': '{{ csrf_token() }}'
        };
    </script>
</head>
<body style="height: 100%;width:100%;" class="body">
<audio id="myaudio1" src="{{URL::asset('/')}}choujiang/images/win.mp3" controls="controls" hidden="true"></audio>
<audio id="myaudio2" src="{{URL::asset('/')}}choujiang/images/start.mp3" controls="controls" hidden="true"></audio>
<audio id="myaudio3" src="{{URL::asset('/')}}choujiang/images/play_fast.mp3" controls="controls" hidden="true"
       loop="loop"></audio>
<audio id="myaudio4" src="{{URL::asset('/')}}choujiang/images/play_slow.mp3" controls="controls" hidden="true"></audio>
<div class="container">
    <div class="head"></div>
    <div class="prizetype">
        <div class="prizecon">
            <ul id="prizetype"></ul>
        </div>
    </div>
    <div class="pick">
        <div class="picklist" id="demo">
            <ul id="demo1">
                <li class="default">
                    <div class="person_head"><img src="../../choujiang/images/2017default.png"></div>
                    <div class="person_infos">
                        <p style="font-size: 42px;margin-top:44px;text-align: center;width:280px;">敬请期待</p>
                        <p></p>
                    </div>
                </li>
            </ul>
            <ul id="demo2"></ul>
        </div>
    </div>
    <div class="cjbtn">
        <div class="choujiang" id="choujiang">点击抽奖(剩余<span class="totalNumber"></span>)</div>
        <div class="stop none" id="stop" style="display: none;">STOP</div>
        <div class="chuj none" id="chuj">出奖ing</div>
    </div>
    <div class="luckytitle"></div>
    <div class="luckylist">
        <ul id="luckylist">
            <li>
                <img src="{{URL::asset('/')}}choujiang/images/2017default.png" alt="">
                <p>暂无</p>
            </li>
            <li>
                <img src="{{URL::asset('/')}}choujiang/images/2017default.png" alt="">
                <p>暂无</p>
            </li>
            <li>
                <img src="{{URL::asset('/')}}choujiang/images/2017default.png" alt="">
                <p>暂无</p>
            </li>
            <li>
                <img src="{{URL::asset('/')}}choujiang/images/2017default.png" alt="">
                <p>暂无</p>
            </li>
            <li>
                <img src="{{URL::asset('/')}}choujiang/images/2017default.png" alt="">
                <p>暂无</p>
            </li>
            <li>
                <img src="{{URL::asset('/')}}choujiang/images/2017default.png" alt="">
                <p>暂无</p>
            </li>
            <li>
                <img src="{{URL::asset('/')}}choujiang/images/2017default.png" alt="">
                <p>暂无</p>
            </li>
            <li>
                <img src="{{URL::asset('/')}}choujiang/images/2017default.png" alt="">
                <p>暂无</p>
            </li>
            <li>
                <img src="{{URL::asset('/')}}choujiang/images/2017default.png" alt="">
                <p>暂无</p>
            </li>
            <li>
                <img src="{{URL::asset('/')}}choujiang/images/2017default.png" alt="">
                <p>暂无</p>
            </li>
        </ul>
    </div>
    <div class="nextStep"></div>
</div>

<!--中奖列表-->
<div class="tc_bg" id="tc_luckylist">
    <div class="winnerList">
        <ul id="zhongjiang">
            <li tel="12345679047" class="userlist_717" uid="717"><span class="head" style="background-image: url(https://test.wujie.com.cn/choujiang/images/small_default.png);">报名14...</span><span class="r">123****9047</span></li>
        </ul>
        <div class="closex">×</div>
    </div>
</div>
<!-- 奖项没抽完 -->
<div class="tc_bg" id="tc0">
    <div class="Tc">
        <img src="../../choujiang/images/2017jxcj.png" style="height: 574px;width:684px;margin-left:-340px;margin-top:-287px;">
    </div>
</div>
<!-- 此轮已抽完 -->
<div class="tc_bg" id="tc1">
    <div class="Tc">
        <img src="../../choujiang/images/tc_pic1.png">
    </div>
</div>
<!-- 此等奖已抽完 -->
<div class="tc_bg" id="tc2">
    <div class="Tc">
        <img src="../../choujiang/images/2017prizedown.png" style="height: 574px;width:684px;margin-left:-340px;margin-top:-287px;">
    </div>
</div>
<!-- 删除成功 -->
<div class="tc_bg" id="tc3">
    <div class="Tc">
        <img src="../../choujiang/images/2017deletesuccess.png" style="width:684px;height:574px;margin-left:-340px;margin-top:-285px;">
    </div>
</div>
<!-- 确定删除中奖者吗? -->
<div class="tc_bg" id="tc4">
    <div class="Tc">
        <img src="../../choujiang/images/2017confirms.png" style="width:684px;height:574px;margin-left:-340px;margin-top:-285px;">
        <button class="cancel">还是TA</button>
        <button class="sure suredelete">确定</button>
    </div>
</div>
<script type="text/javascript" src="../../choujiang/js/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="../../choujiang/js/cj2017.js"></script>
</body>
</html>