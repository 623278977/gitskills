@extends('app.layouts.default')
@section('css')
    <link rel="stylesheet" href="{{URL::asset('/')}}css/game/swiper.min.css">
    <link rel="stylesheet" href="{{URL::asset('/')}}css/game/common.css">
    <link rel="stylesheet" href="{{URL::asset('/')}}css/game/animate.css">
    <style>
        html, body {
            position: relative;
            height: 100%;
        }

        .swiper-container {
            width: 100%;
            height: 100%;
        }
        .swiper-slide {
            z-index: -10;
            text-align: center;
            /* Center slide text vertically */
            display: -webkit-box;
            display: -ms-flexbox;
            display: -webkit-flex;
            display: flex;
            -webkit-box-pack: center;
            -ms-flex-pack: center;
            -webkit-justify-content: center;
            justify-content: center;
            -webkit-box-align: center;
            -ms-flex-align: center;
            -webkit-align-items: center;
            align-items: center;
        }
    </style>
    @stop
    @section('main')
            <!-- Swiper -->
    <div class="swiper-container">
        <div class="swiper-wrapper">
            @if($status==1)
                    <!-- 等待开奖-->
            <div id="waitting" class="swiper-slide relative section1 profile" style="display:block">
                <div class="title absolute">
                    <img src="{{URL::asset('/')}}images/game/title.png">
                    <p class="no-wrap">王者归来！BAT三军争霸谁与争锋？？争锋争锋</p>
                </div>
                <div class="ing absolute ingLR" id="myDIV">
                    <img class="l yun1" src="{{URL::asset('/')}}images/game/yun1.png">
                    <img class="l yun2" src="{{URL::asset('/')}}images/game/yun2.png">
                    <img class="l yun3" src="{{URL::asset('/')}}images/game/yun1.png">
                </div>
                <div class="pride absolute"><img src="{{ URL::asset('/').$prize->image }}"></div>
                <div class="button-box absolute">
                    <img alt="Next Page" class="b-next" src="{{URL::asset('/')}}images/game/jit.png">
                </div>
            </div>
            
            <div class="swiper-slide profile section2 relative" style="display:block">
                <!--  提交姓名 手机号 -->
                <div id="form">
                    <div class="submit_bg relative">
                        <form class="formBlock absolute">
                            <p>
                                <label>姓名：</label>
                                <input name="username">
                            </p>
                            <p>
                                <label>手机号：</label>
                                <input name="tel">
                            </p>
                        </form>
                    </div>
                    <!-- 提交按钮 -->
                    <div class="submit relative">
                        <img src="{{URL::asset('/')}}images/game/submit.png">
                    </div>
                </div>
                <!-- 提交后 奖品送出html -->
                <div class="none" id="send">
                    <div class="prideTxt2 absolute">
                        <img src="{{URL::asset('/')}}images/game/send_prideBg.png">
                    </div>
                    <div class="prideTxt1 absolute">
                        <img src="{{URL::asset('/')}}images/game/send_pride.png">
                    </div>
                    <div class="earth absolute">
                        <img src="{{URL::asset('/')}}images/game/earth.png">
                    </div>
                    <div class="hoston absolute rocketDiv" id="rocketDiv">
                        <img id="rocket" class="rocket" src="{{URL::asset('/')}}images/game/hot.png">
                    </div>
                    <div class="target" id="targetDiv"></div>
                    <!-- <div class="button-box absolute">
                     <img alt="Next Page" class="b-next" src="{{URL::asset('/')}}images/game/jit.png">
                    </div> -->
                </div>
            </div>
        @elseif($status==0)
                <!--  没有扫到奖品 继续加油哦 -->
            <div class="swiper-slide profile section4 relative">
            <div class="page04">
                <img class="absolute page04_1" src="{{URL::asset('/')}}images/game/page04_1.png">
                <a href="javascript:;" class="inline-block absolute page04_3">
                    <img src="{{URL::asset('/')}}images/game/page04_3.png">
                </a>
                <img class="absolute page04_2" src="{{URL::asset('/')}}images/game/page04_2.png">
            </div>
            </div>
        @endif
        </div>
    </div>
    <div id="tc" class="none">
      <div class="mceng"></div>
      <div class="popou">
          <h2>温馨提示</h2>
          <p id="text"></p>
          <button id="btnsub">好</button>
     </div>
    </div>
    @stop
    @section('endjs')
            <!-- Swiper JS -->
    <script src="{{URL::asset('/app/')}}/js/common.js"></script>
    <script src="{{URL::asset('/app/')}}/js/parabolas.js"></script>
    <script src="{{URL::asset('/')}}js/swiper.min.js"></script>
    <script src="{{URL::asset('/')}}js/jquery-1.11.1.min.js"></script>

    <!-- Initialize Swiper -->
    <script>
        var swiper = new Swiper('.swiper-container', {
            direction: 'vertical'
        });
       </script>
    <script>
        // 云朵特效
        var x = document.getElementById("myDIV")

        function myFunction() {
            x.style.WebkitAnimation = "mying 2s 1 forwards"; // Chrome, Safari 和 Opera 代码
            x.style.animation = "mying 2s 1 forwards";
        }
        myFunction();
        //  Chrome, Safari 和 Opera
        x.addEventListener("webkitAnimationEnd",myEndFunction);

        x.addEventListener("animationend",myEndFunction);
        function myEndFunction(){
            x.style.WebkitAnimation = "myingLR 4s linear infinite"; // Chrome, Safari 和 Opera 代码
            x.style.animation = "myingLR 4s linear infinite";
        }
      // 火箭特效
        var a = Math.floor($(document).width()/100);
        var curvature = "-0.005";
        if(parseInt($(document).width()) <= 320){
            curvature = "-0.007";
        }
        var element = document.getElementById("rocketDiv");
        var rocket = document.getElementById("rocket");
        var target = document.getElementById("targetDiv");
        var options = {
            speed:"70",
            curvature:curvature,    
            progress:function(x,y){
                $("#rocket").css({
                    "transform":"rotate("+(100-x/a)+"deg)",
                    "-ms-transform":"rotate("+(100-x/a)+"deg)",
                    "-moz-transform":"rotate("+(100-x/a)+"deg)",
                    "-webkit-transform":"rotate("+(100-x/a)+"deg)",
                    "-o-transform":"rotate("+(100-x/a)+"deg)"
                });  
            }
        }
    </script>
    <script type="text/javascript">

        $(function(){
            $("#btnsub").click(function(event) {
                $("#tc").hide();
            });
            // var docWidth = $(window).width();
            // alert(docWidth);
            setTitle('开奖页面');
            $(".submit").click(function(){
                var params = {};
                params['prize_arr_id'] = {{ $prize_arr_id }};
                params['username'] = $("#form input[name='username']").val();
                params['tel'] = $("#form input[name='tel']").val();
                var url = '/game/userinfo';
                if(!params['username'] || !params['tel']){
                    $("#text").html("请输入姓名和联系方式，工作人员马上联系您！");
                    $("#tc").show();
                    return ;
                }
                var reg=/^(\+\d{2,3}\-)?\d{11}$/;
                if(!reg.test(params['tel'])){
                    $("#text").html("请输入正确的移动电话！");
                    $("#tc").show();
                    return ;
                }
               ajaxRequest(params,url,function(data){
                var myParabola = funParabola(element, target, options);
                    if(data.status==true){
                        $(".submit").addClass('active');
                        $(".submit_bg").addClass('active');
                        setTimeout(function(){
                            $("#form").remove();
                            $("#send").show();
                        },1300);
                        setTimeout(function(){
                            $("#rocketDiv").css({"opacity":"1"});
                            myParabola.init();
                        },3000);
                    }else{
                        alert(data.message);
                    }
                });
            });
        });

    </script>
@stop
