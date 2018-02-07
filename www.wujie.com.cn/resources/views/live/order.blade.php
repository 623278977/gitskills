@extends('layouts.default')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/order.css"/>
    <link rel="stylesheet" href="{{URL::asset('/')}}/dist/css/barrager.css" type="text/css">
@stop
@section('main')
    <section class="containerBox" id="containerBox" style='width:100%;height:100%;'>
        <div class="head">
            <img src="{{URL::asset('/')}}/images/wujie_head.png" alt="">
        </div>
        <!-- 完成订单/在线互动 -->
        <div class="order_num">
            <img src="{{URL::asset('/')}}/images/pendant.png" class="pendant" alt="pendant.png">
            <span class='order_finish'><img src="{{URL::asset('/')}}/images/order_finish.png" alt=""><i
                        id="ordercount"></i></span>
            <span class="interaction"><img src="{{URL::asset('/')}}/images/interaction.png" alt=""><i
                        id="hudongnum"></i></span>
        </div>
        <!-- 订单构成 -->
        <div class="order_form">
            <img src="{{URL::asset('/')}}/images/order_form.png" alt="">
            <div class="form_detail" id="form_detail">

            </div>
        </div>
        <!-- 交易金额    -->
        <div class="money">
            <img src="{{URL::asset('/')}}/images/money.png" alt="">
            <p>￥<span class="numberRun2">1500</span></p>
        </div>
        <!-- 下单动态 -->
        <div class="order_move">
            <img src="{{URL::asset('/')}}/images/order_move.png" alt="">
            <ul id="orderlist">
            </ul>
        </div>
        <!--实时互动-->
        <img src="{{URL::asset('/')}}/images/words_left.png" alt="" class="p words_l">
        <img src="{{URL::asset('/')}}/images/words_right.png" alt="" class="p words_r">
        <div class="words">
            <img src="{{URL::asset('/')}}/images/real_time02.png" alt="">
        </div>
        <input type="hidden" id="comflag" data-realid="0" data-shamid="0" data-fromid="0">
        <audio id="myaudio1" src="{{URL::asset('/')}}/images/maleo.mp3" hidden="true" controls="controls"></audio>
    </section>
@stop
@section('endjs')
    <script type="text/javascript" src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
    <script src="{{URL::asset('/')}}/dist/js/jquery.barrager.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/comment.js?v=11242123"></script>
    <script type="text/javascript">
        ;
        (function ($) {
            $.fn.numberAnimate = function (setting) {
                var defaults = {
                    speed: 1000,//动画速度
                    num: "", //初始化值
                    iniAnimate: true, //是否要初始化动画效果
                    symbol: '',//默认的分割符号，千，万，千万
                    dot: 0 //保留几位小数点
                }
                //如果setting为空，就取default的值
                var setting = $.extend(defaults, setting);

                //如果对象有多个，提示出错
                if ($(this).length > 1) {
                    alert("just only one obj!");
                    return;
                }

                //如果未设置初始化值。提示出错
                if (setting.num == "") {
                    alert("must set a num!");
                    return;
                }
                var nHtml = '<div class="mt-number-animate-dom" data-num="{num}">\
            <span class="mt-number-animate-span">0</span>\
            <span class="mt-number-animate-span">1</span>\
            <span class="mt-number-animate-span">2</span>\
            <span class="mt-number-animate-span">3</span>\
            <span class="mt-number-animate-span">4</span>\
            <span class="mt-number-animate-span">5</span>\
            <span class="mt-number-animate-span">6</span>\
            <span class="mt-number-animate-span">7</span>\
            <span class="mt-number-animate-span">8</span>\
            <span class="mt-number-animate-span">9</span>\
            <span class="mt-number-animate-span">.</span>\
          </div>';

                //数字处理
                var numToArr = function (num) {
                    num = parseFloat(num).toFixed(setting.dot);
                    if (typeof(num) == 'number') {
                        var arrStr = num.toString().split("");
                    } else {
                        var arrStr = num.split("");
                    }
                    //console.log(arrStr);
                    return arrStr;
                }

                //设置DOM symbol:分割符号
                var setNumDom = function (arrStr) {
                    var shtml = '<div class="mt-number-animate">';
                    for (var i = 0, len = arrStr.length; i < len; i++) {
                        if (i != 0 && (len - i) % 3 == 0 && setting.symbol != "" && arrStr[i] != ".") {
                            shtml += '<div class="mt-number-animate-dot">' + setting.symbol + '</div>' + nHtml.replace("{num}", arrStr[i]);
                        } else {
                            shtml += nHtml.replace("{num}", arrStr[i]);
                        }
                    }
                    shtml += '</div>';
                    return shtml;
                }

                //执行动画
                var runAnimate = function ($parent) {
                    $parent.find(".mt-number-animate-dom").each(function () {
                        var num = $(this).attr("data-num");
                        num = (num == "." ? 10 : num);
                        var spanHei = $(this).height() / 11; //11为元素个数
                        var thisTop = -num * spanHei + "px";
                        if (thisTop != $(this).css("top")) {
                            if (setting.iniAnimate) {
                                //HTML5不支持
                                if (!window.applicationCache) {
                                    $(this).animate({
                                        top: thisTop
                                    }, setting.speed);
                                } else {
                                    $(this).css({
                                        'transform': 'translateY(' + thisTop + ')',
                                        '-ms-transform': 'translateY(' + thisTop + ')', /* IE 9 */
                                        '-moz-transform': 'translateY(' + thisTop + ')', /* Firefox */
                                        '-webkit-transform': 'translateY(' + thisTop + ')', /* Safari 和 Chrome */
                                        '-o-transform': 'translateY(' + thisTop + ')',
                                        '-ms-transition': setting.speed / 1000 + 's',
                                        '-moz-transition': setting.speed / 1000 + 's',
                                        '-webkit-transition': setting.speed / 1000 + 's',
                                        '-o-transition': setting.speed / 1000 + 's',
                                        'transition': setting.speed / 1000 + 's'
                                    });
                                }
                            } else {
                                setting.iniAnimate = true;
                                $(this).css({
                                    top: thisTop
                                });
                            }
                        }
                    });
                }
                //初始化
                var init = function ($parent) {
                    //初始化
                    $parent.html(setNumDom(numToArr(setting.num)));
                    runAnimate($parent);
                };
                //重置参数
                this.resetData = function (num) {
                    var newArr = numToArr(num);
                    var $dom = $(this).find(".mt-number-animate-dom");
                    if ($dom.length < newArr.length) {
                        $(this).html(setNumDom(numToArr(num)));
                    } else {
                        $dom.each(function (index, el) {
                            $(this).attr("data-num", newArr[index]);
                        });
                    }
                    runAnimate($(this));
                }
                //init
                init($(this));
                return this;
            }
        })(jQuery);
        $(function () {
            var dm_run_once = true;
            var dd_run_once = true;
            var numRun2 = $(".numberRun2").numberAnimate({num: '0', speed: 3000, symbol: ","});
            var looper_danmu;
            var looper_dingdan;
            var looper_time = 3000;
            var dm_array = [];
            var param = {
                "id": "<?php echo $id;?>",
                "uid": "<?php echo $uid;?>",
                "section": 0,
                "commentType": 'Live',
                "type": 'Live',
                "use": 'big_screen',
                "urlPath": window.location.href,
                "page": 1,
                "page_size": 15,
                "update": "new",
                "fecthSize": 15,
                "real_order_max_id": 0,
                "sham_order_max_id": 0,
                "fromId": 0,
                "case": 'mix'
            };
            if (param.uid > 0) {
                param.case = 'real';
            }
            var method = {
                getFreshList: function (param) {
                    var params = {};
                    params['type'] = param.type;
                    params['use'] = param.use;
                    params['uid'] = param.uid;
                    params['id'] = param.id;
                    params['fromId'] = param.fromId;
                    params['update'] = param.update;
                    params['fecthSize'] = param.fecthSize;
                    var url = labUser.api_path + '/comment/fresh-list';
                    ajaxRequest(params, url, function (data) {
                        if (data.status) {
                            var resObj = data.message;
                            // var content=cutString(item.content,28);
                            if (resObj.data.length > 0) {
                                $.each(resObj.data, function (index, item) {
                                    var content = cutString(item.content, 20);
                                    var dm_item = {"info": item.c_nickname + "：" + content, "img": item.avatar};
                                    dm_array.push(dm_item);
                                });
                            }
                            $('#comflag').attr("data-fromid", resObj.max_id);
                            if (dm_run_once) {
                                barrager();
                                dm_run_once = false;

                            }
                        }
                    });
                },
                getOrderList: function (param) {
                    var params = {};
                    params['live_id'] = param.id;
                    params['sham_order_max_id'] = param.sham_order_max_id;
                    params['real_order_max_id'] = param.real_order_max_id;
                    params['type'] = param.type;
                    var url = labUser.api_path + '/live/order-list';
                    ajaxRequest(params, url, function (data) {
                        if (data.status) {
                            var returnObj = data.message;
                            $('#comflag').attr("data-realid", returnObj.real_order_max_id);
                            $('#comflag').attr("data-shamid", returnObj.sham_order_max_id);
                            numRun2.resetData(returnObj.all_amount);
                            if (returnObj.all_count > 99) {
                                $('#ordercount').html(99 + '<em>+</em>');
                            } else {
                                $('#ordercount').text(returnObj.all_count);
                            }
                            if (returnObj.online_count > 9999) {
                                $('#hudongnum').html(9999 + '<em>+</em>');
                            } else {
                                $('#hudongnum').text(returnObj.online_count);
                            }
                            var ddgcHtml = '';//订单构成
                            $.each(returnObj.orders_structure, function (index, item) {
                                ddgcHtml += '<p><span class="flower"><img src="{{URL::asset('/')}}/images/flower.png" alt=""></span>';
                                ddgcHtml += '<span class="num">' + item.orders_count + '</span>';
                                ddgcHtml += '<span class="brand_name">' + item.title + '</span></p>';
                            });
                            $('#form_detail').html(ddgcHtml);
                            if (returnObj.orders_dynamic.length > 0) {
                                //下单动态
                                var orderdtHtml = '';
                                $.each(returnObj.orders_dynamic, function (index, item) {
                                    orderdtHtml += '<li><label class="label_26"><span class="order_name">' + item.title + '</span></label>';
                                    orderdtHtml += '<label class="label_30"><span class="customer">' + item.zone_name + '/' + item.realname + '</span></label>';
                                    orderdtHtml += '<label class="label_30"><span class="tel">' + item.mobile + '</span></label></li>';
                                });
                                $('#orderlist').append(orderdtHtml);
                                playMaleo();
                            }
                            if (dd_run_once) {
                                orderAnimate();
                                dd_run_once = false;

                            }
                        }
                    });
                }
            };

            function barrager() {
                var itme_index = 4;
                var looper_index = 0;
                if (dm_array.length > 0) {
                    looper_danmu = setInterval(function () {
                        if (dm_array.length > 0) {
                            dm_array[0].bottom = $(window).height() * 0.02 + $(window).height() * 0.07 * (looper_index % itme_index + 1);
                            dm_array[0].speed = getNumber(3, 9);
                            $('body').barrager(dm_array[0]);
                            dm_array.splice(0, 1);
                            looper_index++;
                        }
                    }, 2000);
                }
            }

            //返回一个[min,max]的整数
            function getNumber(min, max) {
                return Math.round(Math.random() * (max - min) + min);
            }

            //下单动态
            function orderAnimate() {
                var orLenEle = $('#orderlist li');
                if (orLenEle.length > 8) {
                    looper_dingdan = setInterval(function () {
                        if ($('#orderlist li').length > 8) {
                            playMaleo();
                            $('#orderlist li').first().remove();
                        }
                    }, looper_time);
                }
            }

            //获取订单数据
            method.getOrderList({
                'id': param.id,
                'sham_order_max_id': '0',
                'real_order_max_id': '0',
                'type': param.case
            });
            //获取评论数据
            method.getFreshList(param);
            //定时刷新
            setInterval(function () {
                //获取订单数据
                method.getOrderList({
                    'id': param.id,
                    'sham_order_max_id': $('#comflag').attr("data-shamid"),
                    'real_order_max_id': $('#comflag').attr("data-realid"),
                    'type': param.case
                });
                //获取评论数据
                param.fromId = $('#comflag').attr("data-fromid");
                method.getFreshList(param);
            }, 12000);
            //清弹幕
            function clearAllDm(){
                $.fn.barrager.removeAll();
            }
            //切页面
            document.addEventListener("visibilitychange", function() {
                if(document.visibilityState=='visible'){
                    orderAnimate();
                    barrager();
                }
                else if(document.visibilityState=='hidden'){
                    clearAllDm();
                    clearInterval(looper_dingdan);
                    clearInterval(looper_danmu);
                }
            });
            //maleo
            function playMaleo(){
                var myAuto_win = document.getElementById('myaudio1');
                myAuto_win.play();
            }
        });
    </script>
@stop