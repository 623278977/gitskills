<?php
if (Auth::check()) {
    $login_user = Auth::user();
}
$winning = ['special', 'one', 'two', 'three', 'four' ,'five' ,'six' ,'seven' ,'eight','nine','ten' ,'eleven' ,'twelve' ,'thirteen','fourteen']; //奖品项
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>天涯若比邻-{{$title}}</title>
        <link rel="stylesheet" type="text/css" href="../../choujiang/stylesheets/index.css">
        <script>
<?php
if (isset($login_user)) {
    $realname = $login_user->realname ? $login_user->realname : $login_user->nickname;
    $login_username = $login_user->username;
    $role = $login_user->roles->toArray();
    $role_id = isset($role[0]) ? $role[0]['id'] : 1;
} else {
    $realname = '';
    $login_username = '';
    $role_id = 1;
}
?>

            var labUser = {
                'app_path': '{{URL::asset("/app/")}}',
                'api_path': '{{URL::asset("/api/")}}',
                'webapp_path': '{{URL::asset("/webapp/")}}',
                'path': '{{URL::asset("/")}}',
                'token': '{{ csrf_token() }}',
                'uid': '<?php echo isset($login_user->uid) ? $login_user->uid : 0 ?>',
                'realname': '<?php echo $realname ?>',
                'username': '<?php echo $login_username ?>',
                'institution_name': '<?php echo isset($login_user->userinfo->institution_name) ? $login_user->userinfo->institution_name : '' ?>',
                'avatar': '<?php echo isset($login_user->avatar) ? getImage($login_user->avatar) : '' ?>',
                'role_id': '<?php echo $role_id; ?>',
                'is_ztc': '<?php echo isset($login_user->is_ztc) ? $login_user->is_ztc : '' ?>',
                'business_card': '<?php echo isset($login_user->userinfo->business_card) ? $login_user->userinfo->business_card : '' ?>',
            };
        </script>

    </head>
    <body style="background-image:@if($background) url({{$background}}) @else url(/choujiang/images/bg.png) @endif ;background-size: cover;">
        <audio id="myaudio1" src="{{URL::asset('/')}}choujiang/images/win.mp3" controls="controls" hidden="true"></audio>
        <audio id="myaudio2" src="{{URL::asset('/')}}choujiang/images/start.mp3" controls="controls" hidden="true"></audio>
        <audio id="myaudio3" src="{{URL::asset('/')}}choujiang/images/play_fast.mp3" controls="controls"  hidden="true" loop="loop"></audio>
        <audio id="myaudio4" src="{{URL::asset('/')}}choujiang/images/play_slow.mp3" controls="controls"  hidden="true"></audio>
        <style>
            div.con_top div.steps div.five_step div.list-prize{
                display:inline-block;
                position: relative;
                width: 115px;
                color: #f7fa54;
                height:45px;
                bottom: 0px;
                overflow: visible;
                left: 60px;
            }
            div.con_top div.steps div.five_step div.list-prize.active{
                color: #207ee6;
            }
            div.list-prize:before,div.list-prize:after{
                background: #f7fa54;
                content: '';
                display:inline-block;
                position: absolute;
            }
            div.list-prize.active:before,div.list-prize.active:after{
                background: #207ee6;
            }
            div.list-prize:before{
                height: 4px;
                width: 190px;
                right: 83px;
                bottom: 12px;
            }
            div.list-prize:after{
                width: 20px;
                height: 20px;
                border-radius: 50%;
                right: 83px;
                bottom: 4px;
            }
            div.list-prize:last-child:before{
                width: 155px;
                right: 118px;
            }
            div.list-prize:last-child:after{
                background-image: url(/choujiang/images/yellow_tdj.png) ;
                border-radius: 0;
                background-color: transparent;
                width:40px;
                height: 32px;
                bottom: -4px;
                right: 75px;
            }

            div.list-prize.active:last-child:after{
                background-image: url(/choujiang/images/blue_tdj.png) ;
            }

            .prize_show{
                display: block;
            }

            .prize_hide{
                display: none;
            }
            div.list-prize:before{
                width:123px;
            }
            div.list-prize:last-child:before{
                width:83px;
            }
            .prize_show :last-child{
                left:105px;
            }
            div.con_top div.steps div.five_step div.list-prize:last-child{
                left:70px;
            }
            div.con_top div.steps div.five_step div.list-prize{
                right:50px;
            }
            div.list-prize:before{
                right:50px;
            }
            div.list-prize:after{
                right:50px;
            }
            div.list-prize:last-child:before{
                right:100px;
            }
            div.list-prize:last-child:after{
                right:37px;
            }

        </style>
        <div class="container">
            <div class="top">
                <!--<img src="../../choujiang/images/title.png">-->
            </div>
            <div class="content" style="background-image:url(/choujiang/images/bg-front.png);padding-top: 50px;">
                <!-- 头部 -->
                <div class="con_top">
                    <div class="steps">
                        @if(count($data)>5)
                            <div id="prize_arrow">
                            <span class="l"><img src="../../choujiang/images/step_left.png"></span>
                            <span class="r"><img src="../../choujiang/images/step_right.png"></span>
                            </div>
                        @endif
                        <div class="five_step" style="width:620px;margin-left:50px;margin-top:-10px;">
                            @foreach($data as $k => $item)
                                <div imgsrc="{{$winning[$item['type']]}}" class="@if($k<5) prize_show list-prize  @else prize_hide @endif <?= $currentType <= $item['type'] ? 'active' : '' ?>"  num="{{$item['num']}}" good_id="{{$item['good_id']}}"  prize_id="{{$item['prize_id']}}">{{$item['name']}}</div>
                            @endforeach
                        </div>
                    </div>
                    <button class="next l  nextStep">进入NEXT奖项</button>
                    <div class="current  currentPrize">
                        @foreach($winning as $key => $value)
                            @if($key == $currentType)
                                <?php $needle = $key ;?>

                            @endif
                        @endforeach

                        @if(isset($data))
                            @foreach($data as $item)
                                    @if($item['type'] == $needle)
                                        <img src="{{$item['image']}}">

                                    @endif
                                @endforeach
                            @else
                                <img src="../../choujiang/images/{{$winning[$currentType]}}_j.png">
                        @endif
                    </div>
                </div>
                <!-- 跑马灯 -->
                <div class="l pmd_con">
                    <div class="l pmd">
                        <div class="pmd_z"></div>
                        <div class="pmd_f" id="imgId" style="visibility:hidden"></div>
                        <div class="pmd_top">
                            <span class="renshu totalNumber">0</span>
                        </div>
                        <div class="pmd_top_f" id="imgId2" style="visibility:hidden">
                            <span class="renshu totalNumber">0</span>
                        </div>
                        <div class="pmd_person" id="demo">
                            <ul id="demo1">
                                <li class="default">
                                    <div class="person_head"><img src="../../choujiang/images/start_default.png"></div>
                                    <div class="default_r"><img src="../../choujiang/images/default_r.png"></div>
                                </li>
                            </ul>
                            <ul id="demo2"></ul>
                        </div>
                        <div class="pmd_btn  choujiang"><img src="../../choujiang/images/chouj_btn.png"></div>
                        <div class="pmd_btn  stop"><img src="../../choujiang/images/stop.png"></div>
                        <div class="pmd_btn  chuj"><img src="../../choujiang/images/stop_hover.png"></div>
                    </div>
                    <div class="l gan"></div>
                </div>
                <!-- 中奖名单 -->
                <div class="r winnerList">
                    <ul id="zhongjiang">
                    </ul>
                </div>
            </div>
        </div>
        <!-- 奖项没抽完 -->
        <div class="tc_bg" id="tc0">
            <div class="Tc">
                <img src="../../choujiang/images/tc_pic.png">
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
                <img src="../../choujiang/images/tc_pic2.png">
            </div>
        </div>
        <!-- 删除成功 -->
        <div class="tc_bg" id="tc3">
            <div class="Tc">
                <img src="../../choujiang/images/delete_success.png">
            </div>
        </div>
        <!-- 删除人员 -->
        <div class="tc_bg" id="tc4">
            <div class="Tc">
                <img src="../../choujiang/images/delete.png">
                <button class="sure suredelete"></button>
                <button class="cancel"></button>
            </div>
        </div>
        <script type="text/javascript" src="../../choujiang/js/jquery-1.11.1.min.js"></script>
        <script type="text/javascript" src="../../choujiang/js/jquery.cookie.js"></script>  
        <script type="text/javascript" src="../../choujiang/js/index.js"></script>
        <script type="text/javascript" src="../../choujiang/js/choujiang.js"></script>
        <script type="text/javascript" src="{{URL::asset('/')}}js/plugins/labcore/dist/js/labcore.js"></script>
        <script type="text/javascript" src="{{URL::asset('/')}}js/plugins/labcore/dist/js/labui.js"></script>
        <script type="text/javascript" src="{{URL::asset('/')}}js/common.js"></script>
        <script type="text/javascript">
            var activity_id = "<?php echo $activity_id ?>";
            /**每隔5秒的抽奖定时器**/
            var ds = '';
            /**转动的定时器**/
            var MyMar = '';
            /**转动的定时器是否开启**/
            var MyMar_ismoving = false;
            /**快速音乐定时器**/
            var Fast = '';
            var limit = '';
            /***是否在旋转中***/
            var zhuandong = false;
            /**要删除人的uid**/
            var deleteParam = {};
            var pmd_ismoving = false;
            var param = {
                activity_id: activity_id,
                number: 0,
                prize_id: 0,
                good_id: 0,
                count: 1,
                selected: 0,
                apply_id: 0
            };
            $(document).on("click", ".nextStep", function () {
                var active_div_arr = [];
                var active_div = $(".five_step > div");
                active_div.each(function (index,domEle){
                    if($(domEle).hasClass('active')){
                        active_div_arr.push($(domEle));
                    }
                });

                if (active_div_arr.length >=4 ){
                    clickRight();
                }

                if (!$('.stop').is(":hidden")) {
                    choujiang.showMessage('#tc0');
                    return false;
                }
                // $('#zhongjiang').html('');
                choujiang.nextStep();
                this.blur();
            });

            var _dethis = '';
            $(document).on("click", "#zhongjiang li", function () {
                var uid = $(this).attr('uid');
                choujiang.showMessage('#tc4');
                _dethis = $(this);
                deleteParam = {
                    activity_id: activity_id,
                    uid: uid,
                    prize_id: $('.five_step .active:last').attr('prize_id'),
                    good_id: $('.five_step .active:last').attr('good_id'),
                    tel: $(this).attr('tel')
                };
            });
            $('.suredelete').click(function () {
                var deleteUrl = labUser.api_path + '/game/delete';
                choujiang.deleteUser(deleteParam, deleteUrl, _dethis);
            });
            /**抽奖的按钮hover**/
            $('.stop,.chuj').hide();
            $('.choujiang').click(function () {
                is_winning = false;
                /**先判断奖项有没有抽完***/
                choujiang.getNowinNumber({prize_id: $('.five_step .active:last').attr('prize_id')}, "beginCheck");
            });
            $('.choujiang').hover(function () {
                $(".choujiang").children('img').attr("src", "../../choujiang/images/yj_hover.png");
            }, function () {
                $(".choujiang").children('img').attr("src", "../../choujiang/images/chouj_btn.png");
            });
            $('body').keydown(function(event){
                if(event.keyCode==13 && is_winning===false && $("#tc0,#tc1,#tc2,#tc3").is(':hidden')){
                    if($('.stop').is(':hidden')){
                        $('.choujiang').click();
                    }else{
                        $('.stop').click();
                    }
                }else{
                    $("#tc0,#tc1,#tc2,#tc3").hide();
                }
            });
            /**停止的按钮点击**/
            $('.stop').click(function () {
                is_winning = true;
                is_start(1, "");
                $(".choujiang,.stop").hide();
                $(".chuj").show();
                myAuto_playSlow();
                pause_playFast();
            });
            /**出奖去掉鼠标手**/
            $(".chuj").hover(function () {
                $(this).css("cursor", "none")
            })
            /**获取某一个奖项还有几个人未中奖***/
            choujiang.getNowinNumber({
                prize_id: $('.five_step .active:last').attr('prize_id')
            }, "changeNumber");
            /***获取未中奖用户**/
            function ds_choujiang() {
                choujiang.getUsers(param);
            }
            //获奖音乐
            function myAuto_win() {
                var myAuto_win = document.getElementById('myaudio1');
                myAuto_win.play();
            }
            //开始音乐
            function myAuto_start() {
                var myAuto_start = document.getElementById('myaudio2');
                myAuto_start.play();
            }
            //进行中音乐
            function myAuto_playFast() {
                var myAuto_playFast = document.getElementById('myaudio3');
                myAuto_playFast.play();
            }

            //停止进行中音乐
            function pause_playFast() {
                var myAuto_playFast = document.getElementById('myaudio3');
                myAuto_playFast.pause();
            }

            //慢下来音乐
            function myAuto_playSlow() {
                var myAuto_playSlow = document.getElementById('myaudio4');
                myAuto_playSlow.play();
            }

            function is_start(start, flag) {
                //	var start;
                param.count = 1;
                param.number = $('.five_step .active:last').attr('num');
                param.prize_id = $('.five_step .active:last').attr('prize_id');
                param.good_id = $('.five_step .active:last').attr('good_id');
                choujiang.getUsers(param, flag);
                zhuanDong(start);
                /**默认li隐藏**/
//                $(".default").hide();
            }
//            $(window).keydown(function (event) {
//                if (event.keyCode == 33) {// Page Up
//                    $(".choujiang").click();
//                } else if (event.keyCode == 34) {//Page Down
//                    $(".stop").click();
//                }
//            })

            /**关闭弹窗**/
            close("#tc0");
            close("#tc1");
            close("#tc2");
            close("#tc3");
            function close(obj) {
                var obj = $(obj);
                obj.click(function () {
                    obj.hide();
                })
            }

            $("#tc4 .cancel").click(function (event) {
                $("#tc4").hide();
            });

            var show_arr = [];
            var hide_arr = [];
            var divs = $('.five_step > div');
            divs.each(function (index,domEle){
                if($(domEle).hasClass('prize_show')){
                    show_arr.push($(domEle));
                }else{
                    hide_arr.push($(domEle));
                }
            });

//            $('#prize_arrow .r').click(function () {
//
//
//                if(hide_arr.length == 0){
//                    return false;
//                }
//
//                if(show_arr.length > 0){
//                    show_arr[0].removeClass('prize_show').removeClass('list-prize').addClass('prize_hide');
//                    show_arr.shift();
//                }
//
//                hide_arr[0].removeClass('prize_hide').addClass('prize_show').addClass('list-prize');
//                hide_arr.shift();
//
//            });

            function clickRight(){
                var divs_l = $('.five_step > div');
                var is_return = 0;

                divs_l.each(function (index,domEle){

                    if(index == (divs_l.length -1)){
                        if($(domEle).hasClass('prize_show')){
                            is_return = 1;
                            return false;
                        }
                    }
                    if($(domEle).hasClass('prize_hide') && index != 0){
                        $(domEle).removeClass('prize_hide').addClass('prize_show').addClass('list-prize');
                        return false;
                    }
                });

                if(is_return){
                    return false;
                }

                divs_l.each(function (index,domEle){

                    if($(domEle).hasClass('prize_show')){
                        $(domEle).removeClass('prize_show').removeClass('list-prize').addClass('prize_hide');
                        return false;
                    }
                });
            }


            $('#prize_arrow .r').click(function () {

                clickRight();
            });


            $('#prize_arrow .l').click(function () {
                var lastEle;
                var lastS;
                var is_return;
                var divs_l = $('.five_step > div');
                var show_arr = [];
                var hide_arr = [];

                divs_l.each(function (index,domEle){
                    if($(domEle).hasClass('prize_show') && index == 0){
                        is_return = 1;
                        return false;
                    }
                    if($(domEle).hasClass('prize_show')){
                        lastEle = divs_l[index-1];
                        return false;
                    }

                });

                if(is_return){
                    return false;
                }

                divs_l.each(function (index,domEle){
                    if($(domEle).hasClass('prize_show')){
                        show_arr.push($(domEle));
                    }
                });

                $(lastEle).removeClass('prize_hide').addClass('list-prize').addClass('prize_show');

                lastS = show_arr.pop();
                lastS.removeClass('prize_show').removeClass('list-prize').addClass('prize_hide');

            });



        </script>
</html>