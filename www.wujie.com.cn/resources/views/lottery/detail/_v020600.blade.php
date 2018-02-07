@extends('layouts.default')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/iscroll.css"/>
    <link href="{{URL::asset('/')}}/css/_v020600/lotterydetail.css?v=20170511" rel="stylesheet" type="text/css"/>
@stop
@section('main')

    <section class="containerBox" >
        <!--背景-->
        <div class="background">
            <div class="masked hide">
            </div>

            <img src="{{URL::asset('/')}}/images/lottery/dollar.png"  class="dollar" />
            <span class="balancetext">积分余额</span>
            <span class="balancevalue"></span>
            <!--抽奖记录-->
            {{--<div class="record">--}}
                {{--<span class="recordText">--}}
                    {{--抽奖记录--}}
                {{--</span>--}}
            {{--</div>--}}
            <div class="recordButton">
            </div>
            <div class="clearfix"></div>

            <!--底盘-->
            <div class="dial">
                <img src="{{URL::asset('/')}}/images/lottery/bottom.png"   />
                <img src="{{URL::asset('/')}}/images/lottery/innercircle.png"  class="innercircle" />
            </div>

            <!--开始按钮-->
            {{--<div class="start">--}}
                {{--<span class="startText">--}}
                    {{--开始--}}
                {{--</span>--}}
            {{--</div>--}}

            <div class="startButton">
            </div>

            <!--剩余机会-->
            <div class="surplus">
                <span class="surplusText">当前还有</span>
                <span class="surplusNumber"></span>
                <span class="surplusAfterText">次抽奖机会</span>
                <!--问号-->
                {{--<span class="question">--}}
                    {{--<span class="questionText">--}}
                    {{--?--}}
                    {{--</span>--}}
                <span class="question">
                </span>
                {{--</span>--}}
            </div>




            {{--<!--抽奖记录-->--}}
            {{--<div class="recordArea Absolute-Center hide">--}}
                {{--<span class="recordTitle">--}}
                    {{--抽奖结果--}}
                {{--</span>--}}

                {{--<div class="contextbg">--}}

                    {{--<table>--}}
                        {{--<thead>--}}
                        {{--<tr>--}}
                            {{--<td colspan="4">--}}
                                {{--<img src="/images/lottery/tabletitle.png" class="titleimage">--}}
                            {{--</td>--}}
                        {{--</tr>--}}
                        {{--</thead>--}}
                        {{--<tr class="tabledata">--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                        {{--</tr>--}}
                        {{--<tr class="tabledata">--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                        {{--</tr>--}}
                        {{--<tr class="tabledata">--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                        {{--</tr>--}}
                        {{--<tr class="tabledata">--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                        {{--</tr>--}}
                        {{--<tr class="tabledata">--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                        {{--</tr>--}}
                        {{--<tr class="tabledata">--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                        {{--</tr>--}}
                        {{--<tr class="tabledata">--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                        {{--</tr>--}}
                        {{--<tr class="tabledata">--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                        {{--</tr>--}}
                        {{--<tr class="tabledata">--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--&nbsp;--}}
                            {{--</td>--}}
                        {{--</tr>--}}

                    {{--</table>--}}
                {{--</div>--}}






                {{--<!--关闭按钮-->--}}
                {{--<div class="closebutton">--}}
                {{--</div>--}}

                {{--<!--页码-->--}}
                {{--<span class="page hide">--}}
                    {{--1--}}
                {{--</span>--}}
            {{--</div>--}}





            <!--抽奖记录-->
            <div class="newRecordArea Absolute-Center hide">

                <div class="newContextbg">
                    <table cellspacing="0" cellpadding="0" border="0">
                        <tr class="tabledata">
                            <td>
                                &nbsp;
                            </td>
                            <td>
                                &nbsp;
                            </td>
                            <td>
                                &nbsp;
                            </td>
                            <td>
                                &nbsp;
                            </td>
                        </tr>
                        <tr class="tabledata">
                            <td>
                                <div class="firstTdDiv grayDiv">
                                    &nbsp;
                                </div>
                            </td>
                            <td>
                                <div class="grayDiv">
                                    &nbsp;
                                </div>
                            </td>
                            <td>
                                <div class="grayDiv">
                                    &nbsp;
                                </div>
                            </td>
                            <td>
                                <div class="lastTdDiv grayDiv">
                                    &nbsp;
                                </div>
                            </td>
                        </tr>
                        <tr class="tabledata">
                            <td>
                                &nbsp;
                            </td>
                            <td>
                                &nbsp;
                            </td>
                            <td>
                                &nbsp;
                            </td>
                            <td>
                                &nbsp;
                            </td>
                        </tr>
                        <tr class="tabledata">
                            <td>
                                <div class="firstTdDiv grayDiv">
                                    &nbsp;
                                </div>
                            </td>
                            <td>
                                <div class="grayDiv">
                                    &nbsp;
                                </div>
                            </td>
                            <td>
                                <div class="grayDiv">
                                    &nbsp;
                                </div>
                            </td>
                            <td>
                                <div class="lastTdDiv grayDiv">
                                    &nbsp;
                                </div>
                            </td>
                        </tr>
                        <tr class="tabledata">
                            <td>
                                &nbsp;
                            </td>
                            <td>
                                &nbsp;
                            </td>
                            <td>
                                &nbsp;
                            </td>
                            <td>
                                &nbsp;
                            </td>
                        </tr>
                        <tr class="tabledata">
                            <td>
                                <div class="firstTdDiv grayDiv">
                                    &nbsp;
                                </div>
                            </td>
                            <td>
                                <div class="grayDiv">
                                    &nbsp;
                                </div>
                            </td>
                            <td>
                                <div class="grayDiv">
                                    &nbsp;
                                </div>
                            </td>
                            <td>
                                <div class="lastTdDiv grayDiv">
                                    &nbsp;
                                </div>
                            </td>
                        </tr>
                        <tr class="tabledata">
                            <td>
                                &nbsp;
                            </td>
                            <td>
                                &nbsp;
                            </td>
                            <td>
                                &nbsp;
                            </td>
                            <td>
                                &nbsp;
                            </td>
                        </tr>
                        <tr class="tabledata">
                            <td>
                                <div class="firstTdDiv grayDiv">
                                    &nbsp;
                                </div>
                            </td>
                            <td>
                                <div class="grayDiv">
                                    &nbsp;
                                </div>
                            </td>
                            <td>
                                <div class="grayDiv">
                                    &nbsp;
                                </div>
                            </td>
                            <td>
                                <div class="lastTdDiv grayDiv">
                                    &nbsp;
                                </div>
                            </td>
                        </tr>
                        <tr class="tabledata">
                            <td>
                                &nbsp;
                            </td>
                            <td>
                                &nbsp;
                            </td>
                            <td>
                                &nbsp;
                            </td>
                            <td>
                                &nbsp;
                            </td>
                        </tr>

                    </table>
                </div>

                <!--关闭按钮-->
                <div class="closebutton">
                </div>

                <!--页码-->
                <span class="page hide">
                    1
                </span>
            </div>


            <!--中奖了-->
            <div class="winning hide">
                <!--中奖了的logo-->
                <div class="winninglogo">
                </div>

                <!--中奖了的名称-->
                <div class="winningname">
                </div>

                <!--关闭按钮-->
                <div class="winningclose">
                </div>
            </div>

            <!--没中奖-->
            <div class="losed hide">
                <div class="losedbutton">
                    <div class="losedtext">
                        再抽一次
                    </div>
                </div>
            </div>

            <div class="slide">

            </div>
        </div>
        <!-- <div class="module">
            <div class="tc foreshow">
               <p>敬请期待!</p> 
            </div>
        </div> -->

    </section>

    @stop
    @section('endjs')
    <script type="text/javascript" src="{{URL::asset('/')}}/js/iscroll.js"></script>
    <!-- <script type="text/javascript" src="{{URL::asset('/')}}/js/iscroll_touch.js"></script> -->
    <script>
        Zepto(function($) {
            // $('.startButton').addClass('none');
            

            //调用初始接口
            var url = labUser.api_path + "/user/surplus/_v020600";
            var param = {};

            function getUrlParam(name) {
                var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
                var r = window.location.search.substr(1).match(reg);  //匹配目标参数
                if (r != null) return unescape(r[2]);
                return null; //返回参数值
            }

            var uid = getUrlParam('uid');
            param["uid"] = uid;
            ajaxRequest(param, url, function (data) {
                if (data.status) {
                    var datas = data.message;
                    $(".balancevalue").text(data.message.score);
                    $(".surplusNumber").text(data.message.surplus);
                }
            })

            var start_url = labUser.api_path + "/user/lottery/_v020600";
            param["uid"] = uid;

//            $(".start").on('click',function(){
//                ajaxRequest(param, start_url, function (data) {
//                    if (data.status) {
//                        alert(data.message);
//                        ajaxRequest(param, url, function (data) {
//                            if (data.status) {
//                                var datas = data.message;
//                                $(".balancevalue").text(data.message.score);
//                                $(".surplusNumber").text(data.message.surplus);
//                            }
//                        })
//                    }else{
//                        alert(data.message);
//                        ajaxRequest(param, url, function (data) {
//                            if (data.status) {
//                                var datas = data.message;
//                                $(".balancevalue").text(data.message.score);
//                                $(".surplusNumber").text(data.message.surplus);
//                            }
//                        })
//                    }
//                })
//            });


//            $(".record").on('click', function () {
//                //加载数据
//                var url = labUser.api_path + "/user/records/_v020600";
//                param["uid"] = uid;
//                param["page_size"] = 10;
//                param["page"] = 1;
//
//                ajaxRequest(param, url, function (data) {
//                    if (data.status) {
//                        //替换数据
//                        var trs = $("tr[class='tabledata']");
//                        if (data.message.res.length <= 9) {
//                            trs.each(function (i, j) {
//                                $(this).children('td').eq(0).html(data.message.res[i].name);
//                                $(this).children('td').eq(1).html(data.message.res[i].spend);
//                                $(this).children('td').eq(2).html(data.message.res[i].created_at_format);
//                                $(this).children('td').eq(3).html(data.message.res[i].remark);
////                                if(data.message.res[i].is_entity){
////                                    $(this).children('td').eq(3).addClass('entity');
////                                }
//                            });
//                        } else {
//                            trs.remove();
//                            var html = '';
//                            for (x in data.message.res) {
//                                //构建tr
//                                html = '<tr class="tabledata">';
//                                html += '<td>' + data.message.res[x].name + '</td>';
//                                html += '<td>' + data.message.res[x].spend + '</td>';
//                                html += '<td>' + data.message.res[x].created_at_format + '</td>';
//                                html += '<td>' + data.message.res[x].remark + '</td>';
//
////                                if(data.message.res[x].is_entity){
////                                    html +='<td class="entity">'+data.message.res[x].remark+'</td>';
////                                }else{
////                                    html +='<td>'+data.message.res[x].remark+'</td>';
////                                }
//                                html += '</tr>';
//                                //添加到表格中
//                                $("table").append(html);
//                                html = '';
//                            }
//                        }
//                    }
//                });
//
//                $(".masked").removeClass('hide');
//                $(".recordArea").removeClass('hide');
//            });


            $(".recordButton").on('click', function () {
                //加载数据
                var url = labUser.api_path + "/user/records/_v020600";
                param["uid"] = uid;
                param["page_size"] = 10;
                param["page"] = 1;

                ajaxRequest(param, url, function (data) {
                    if (data.status) {
                        //替换数据
                        var trs = $("tr[class='tabledata']");
                        if (data.message.res.length <= 9) {
                            trs.each(function (i, j) {
                                $(this).children('td').eq(0).html(data.message.res[i].name);
                                $(this).children('td').eq(1).html(data.message.res[i].spend);
                                $(this).children('td').eq(2).html(data.message.res[i].created_at_format);
                                $(this).children('td').eq(3).html(data.message.res[i].remark);

                                //if(data.message.res[i].is_entity){
//                                    $(this).children('td').eq(3).addClass('entity');
//                                }
                            });
                        } else {
                            trs.remove();
                            //因为删除了9行，所以表格的奇偶性发生了变化，所以要额外加一行
                            var html = '<tr class="tabledata hide"><td></td><td></td><td></td><td></td></tr>';
                            for (x in data.message.res) {
                                //构建tr
                                if(x%2==0){
                                    html += '<tr class="tabledata">';
                                    html += '<td>' + data.message.res[x].name + '</td>';
                                    html += '<td>' + data.message.res[x].spend + '</td>';
                                    html += '<td>' + data.message.res[x].created_at_format + '</td>';
                                    html += '<td>' + data.message.res[x].remark + '</td>';
                                    html += '</tr>';
                                }else{
                                    html += '<tr class="tabledata">';
                                    html += '<td class="firstTdDiv grayDiv">' + data.message.res[x].name + '</td>';
                                    html += '<td class="grayDiv">' + data.message.res[x].spend + '</td>';
                                    html += '<td class="grayDiv">' + data.message.res[x].created_at_format + '</td>';
                                    html += '<td class="lastTdDiv grayDiv">' + data.message.res[x].remark + '</td>';
                                    html += '</tr>';
                                }


                                //if(data.message.res[x].is_entity){
//                                    html +='<td class="entity">'+data.message.res[x].remark+'</td>';
//                                }else{
//                                    html +='<td>'+data.message.res[x].remark+'</td>';
//                                }
                                html += '</tr>';

                                //添加到表格中
                                $("table").append(html);
                                html = '';
                            }
                        }
                    }
                });

                $(".masked").removeClass('hide');
                $(".newRecordArea").removeClass('hide');
            });

            $(".closebutton").on('click', function () {
                $(".recordArea").addClass('hide');
                $(".newRecordArea").addClass('hide');
                $(".masked").addClass('hide');
                $(".page").html(1);

            })


            //下拉到底部触发两次的问题如何解决？
            //设置一把锁
            var load = true;
//            $(".contextbg").scroll(function(){
//                var $this =$(this),
//                viewH =$(this).height(),
//                contentH =$(this).get(0).scrollHeight,//内容高度
//                scrollTop =$(this).scrollTop();//滚动高度
//
//                //拉到底部触发两次
//                if(contentH - viewH - scrollTop <= 0 && load) {
//                    load=false;
//
//                    //加载数据
//                    var url = labUser.api_path + "/user/records/_v020600";
//                    param["uid"] = uid;
//                    param["page_size"] = 10;
//                    param["page"] = parseInt($(".page").html())+1;
//
//                    ajaxRequest(param, url, function (data) {
//                        if (data.status) {
//                            //加载数据
//                            var html = '';
//                            for (x in data.message.res) {
//                                //构建tr
//                                html = '<tr class="tabledata">';
//                                html +='<td>'+data.message.res[x].name+'</td>';
//                                html +='<td>'+data.message.res[x].spend+'</td>';
//                                html +='<td>'+data.message.res[x].created_at_format+'</td>';
//                                html +='<td>'+data.message.res[x].remark+'</td>';
//                                html +='</tr>';
//                                //添加到表格中
//                                $("table").append(html);
//                                html = '';
//                            }
//                            $(".page").html(parseInt(data.message.page));
//                        }
//                        //回调完成了再把锁打开 关键是要在回调函数中把锁打开
//                        load=true;
//                    });
//                }
//            })

            function setDegree($obj, deg) {
                $obj.css({
                    'transform': 'rotate(' + deg + 'deg)',
                    '-moz-transform': 'rotate(' + deg + 'deg)',
                    '-o-transform': 'rotate(' + deg + 'deg)'
                });
            }


            function rotate(offset, result) {
                var $tar = $('.innercircle'),
                        i,
                        cnt = 100,                          //用做ratio的索引(10-29)
                        total = 0,                          //记录上一次的变化结果
                        ratio = [],                         //存放角度的变化比例，制造快慢过渡效果
                        amount = 18 - ( 0.18 * offset );     //每次每多出45/200=0.225度,200次就多偏转45度

                //通过两个平均数为1的数组，来控制指针的先快后慢
                ratio[1] = [0.2, 0.4, 0.6, 0.8, 1, 1, 1.2, 1.4, 1.6, 1.8];
                ratio[2] = [1.8, 1.6, 1.4, 1.2, 1, 1, 0.8, 0.6, 0.4, 0.2];

                for (i = 0; i < 200; i++) {
                    //设计为200次50ms的间隔，10s出结果感觉比较好
                    setTimeout(function () {
                        //计算每次偏转增量，对应阶段的增减比例最终造成快慢变化
                        var deg = amount * ( ratio[String(cnt).substr(0, 1)][String(cnt).substr(1, 1)] );
                        setDegree($tar, deg + total);//改变偏转
                        total += deg;//记录
                        cnt++;//依据次数用作ratio的索引，这里用到了闭包不能使用i
                    }, i * 50);
                }

                //10.5秒后弹出结果
//                setTimeout(function(){
//                    alert( result );//完成
//                },200*50+500);
            }


            var clicked = false;
            $(".innercircle, .startButton").on('click', function () {
                if (clicked == false) {
                    clicked = true;
                    ajaxRequest(param, start_url, function (data) {
                        if (data.status && data.message.index != 4) {
                            rotate(data.message.index, data.message.name);

                            setTimeout(function () {
                                $(".masked").removeClass('hide');
                                $(".winningname").text(data.message.name);
                                $(".winning").removeClass('hide');
                                ajaxRequest(param, url, function (data) {
                                    if (data.status) {
                                        $(".balancevalue").text(data.message.score);
                                        $(".surplusNumber").text(data.message.surplus);
                                    }
                                });
                            }, 10500);
                            setTimeout(function () {
                                clicked = false
                            }, 11000);

                        } else if (data.status && data.message.index == 4) {
                            rotate(data.message.index, data.message.name);

                            setTimeout(function () {
                                $(".masked").removeClass('hide');
                                $(".losed").removeClass('hide');
                                ajaxRequest(param, url, function (data) {
                                    if (data.status) {
                                        $(".balancevalue").text(data.message.score);
                                        $(".surplusNumber").text(data.message.surplus);
                                    }
                                });
                            }, 10500);
                            setTimeout(function () {
                                clicked = false
                            }, 11000);

                        } else {
                            setTimeout(function () {
                                clicked = false
                            }, 1000);
                            alert(data.message);
                        }

                        //11秒之后，再把变量设置为false
                    })
                }
            });


            $(".losedbutton").on('click', function () {
//                alert(1);
                $(".masked").addClass('hide');
                $(".losed").addClass('hide');
            })


            $(".winningclose").on('click', function () {
//                alert(2);
                $(".masked").addClass('hide');
                $(".winning").addClass('hide');
            })


            $(".slide").on('click', function () {
//                var h = $(document).height()-$(window).height();
//                alert(h);
//                $(".background").scrollTop(h);
//                var t = $(window).scrollTop;
//                $('body,html').animate({'scrollTop':t+200},100)
                var h = document.documentElement.scrollHeight || document.body.scrollHeight;
                window.scrollTo(0,(h-690));
//                $("html,body").animate({
//                        scrollTop:0
//                },300);

            });

        })

    </script>
@stop