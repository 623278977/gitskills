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
            text-align: center;
            font-size: 18px;
            /* background: #4a9cf1;*/

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
            <div class="swiper-slide profile section2 relative" style="display:block">
                <section>
                    <!-- 提交后 奖品送出html -->
                    
                    <div class="prideTxt1 absolute">
                        <img src="{{URL::asset('/')}}images/game/send_pride.png">
                    </div>
                    <div class="prideTxt2 absolute">
                        <img src="{{URL::asset('/')}}images/game/send_prideBg.png">
                    </div>
                    <div class="earth absolute">
                        <img src="{{URL::asset('/')}}images/game/earth.png">
                    </div>
                    <div class="hoston absolute stylie">
                        <img src="{{URL::asset('/')}}images/game/hot.png">
                    </div>
                </section>
            </div>
        </div>
    </div>
@stop
@section('endjs')
    <!-- Swiper JS -->
    <script src="{{URL::asset('/app/')}}/js/common.js"></script>
    <script src="{{URL::asset('/')}}js/swiper.min.js"></script>
    <script src="{{URL::asset('/')}}js/jquery-1.11.1.min.js"></script>

    <!-- Initialize Swiper -->
    <script>
        setTitle('开奖成功');
    var swiper = new Swiper('.swiper-container', {
        direction: 'vertical'
    });
    </script>
@stop
