@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/iscroll.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/messages.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section id="container" class="">
        <div id="wrapper" style="font-family: 'Microsoft YaHei';top:0;bottom:0;">
            <div id="scroller">
                <div id="pullDown" class="none">
                    <span class="pullDownLabel">上拉加载更多...</span>
                </div>
                <div class="clearfix"></div>
                <div id="thelist">
                    <!--
                   <section class="messagelist">
                       <h1><em>专版名字</em>专版有新活动发布</h1>
                       <div class="fl f12 pubtime">2016-08-25</div>
                       <div class="clearfix"></div>
                       <div class="messcontent">
                           <div>查看活动详情，或前往<span class="orange pl05">专版名字</span>专版详情页查看更多活动：</div>
                           <div class="mess-box">
                               <img alt="loadfailed" class="messageimg" src="">
                               <div class="rightinfo">
                                   <p class="acttitle">活动名字</p>
                                   <p><span class="time_icon"></span><i class="pl1">08/25 13:00</i></p>
                                   <p class="green"><span class="wjb_icon"></span><i class="pl1">￥22元起</i></p>
                               </div>
                           </div>
                       </div>
                       <div class="seen_more">立即进入<a href="#">专版名字</a>专版详情页<span class="sj_icon"></span>
                       </div>
                   </section>
                   <section class="messagelist">
                       <h1>成功购买<em>专版会员</em>3个月套餐</h1>
                       <div class="fr f12 pubtime">年月日时分秒</div>
                       <div class="clearfix"></div>
                       <div class="messcontent">你已成功购买是是是套餐
                           <div class="mess-box">
                               <p>套餐名称：15131</p>
                               <p>有效期至：2017-02-01</p>
                           </div>
                       </div>
                       <div class="seen_more">点击了解
                           <a href="#">中美会员教育</a>
                           <a href="">会员权益说明</a>
                           <span class="sj_icon"></span>
                       </div>
                   </section>
                   <section class="messagelist">
                       <h1>您的<em>专版会员</em>已经到期，是否需要继续续费</h1>
                       <div class="fr f12 pubtime">年月日时分秒</div>
                       <div class="clearfix"></div>
                       <div class="messcontent">你已成功购买是是是套餐
                           <div class="mess-box">
                               <span>有效期至：2017-07-20 <em>还剩2天</em>  </span>
                               <a href="#">立即续费</a>
                           </div>
                       </div>
                       <div class="seen_more">点击了解 <a href="#">中美会员教育</a>
                       <a href="">会员权益说明</a><span class="sj_icon"></span></div>
                   </section>
                   -->
                </div>
                <div id="pullUp" data-pagenow="1">
                    <span class="pullUpLabel"></span>
                </div>
            </div>
        </div>
        <div id="nomessage" class="tc tipcontainer none">
            <div class="tipimage"></div>
            <div class="tiptext">抱歉！目前没有任何新的通知</div>
        </div>
    </section>
@stop
@section('endjs')
    <script type="text/javascript" src="{{URL::asset('/')}}/js/iscroll.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/iscroll_touch.js"></script>
    <script type="text/javascript">
        var pageNow = 1, pageSize = 10;
        var touch = $.extend({}, {
            getAjaxDownData: function () {
            },
            getAjaxUpData: function () {
                var page = $('#pullUp').data('pagenow');
                page++;
                var param = {
                    "uid": labUser.uid,
                    "page": page,
                    "page_size": pageSize
                };
                getMessageList(param);
                myScroll.refresh();
            }
        });
        function getMessageList(param) {
            var url = labUser.api_path + '/message/vipremind';
            // var url = '/api/message/officialmessage';
            ajaxRequest(param, url, function (data) {
                if (data.status) {
                    //判断缺省0为false
                    if (data.message.total == 0) {
                        $('#nomessage').removeClass('none');
                        $('#container').removeClass('none');
                    }
                    else {
                        $('#nomessage').addClass('none');
                        //html调整
                        if (data.message.vipmessages.length > 0) {
                            var messageHtml = '';
                            $.each(data.message.vipmessages, function (index, item) {
                                if (item.type == 1) {//直播预告
                                    var liveHtml = '';
                                    messageHtml += '<section class="messagelist">';
                                    messageHtml += '<h1><em class="pr05">' + item.vip_name + '</em>专版有新直播即将开始</h1>';
                                    messageHtml += '<div class="fl f12 pubtime">' + item.send_time + '</div>';
                                    messageHtml += '<div class="clearfix"></div>';
                                    messageHtml += '<div class="messcontent">';
                                    messageHtml += '<div><span>' + item.vip_name + '</span>专版有新直播预告上线，点击关注，将活动纳入"订阅"</div>';
                                    $.each(item.messages, function (index, item) {
                                        var liveaddress = labUser.path + '/webapp/live/detail?id=' + item.live_id + '&makerid=' + item.maker_id + '&uid=' + param.uid + '&pagetag=04-9';
                                        liveHtml += '<a href="' + liveaddress + '">';
                                        liveHtml += '<div class="mess-box">';
                                        liveHtml += '<img alt="loadfailed" class="messageimg" src="' + item.list_img + '">';
                                        liveHtml += '<div class="rightinfo">';
                                        liveHtml += '<p class="acttitle">' + cutString(item.subject,14) + '</p>';
                                        liveHtml += '<span class="time_icon fl"></span>';
                                        liveHtml += '<span class="pl1 fl timespan">' + item.begin_time + '</span>';
                                        liveHtml += '<div class="clearfix"></div>';
                                        liveHtml += '<p class="pl2 mt05"><i class="pl1">开始直播</i></p>';
                                        liveHtml += '</div>';
                                        liveHtml += '</div>';
                                        liveHtml += '</a>';
                                    });
                                    messageHtml += liveHtml;
                                    messageHtml += '</div>';
                                    messageHtml += '</section>';
                                }
                                if (item.type == 2) {//直播即将开始
                                    var liveHtml = '';
                                    messageHtml += '<section class="messagelist">';
                                    messageHtml += '<h1><em class="pr05">' + item.vip_name + '</em>专版即将有直播开播，请火速围观</h1>';
                                    messageHtml += '<div class="fl f12 pubtime">' + item.send_time + '</div>';
                                    messageHtml += '<div class="clearfix"></div>';
                                    messageHtml += '<div class="messcontent">';
                                    messageHtml += '<div><span>' + item.vip_name + '</span>专版有新直播即将在1小时后开始，点击查看更多直播信息：</div>';
                                    $.each(item.messages, function (index, item) {
                                        var liveaddress = labUser.path + '/webapp/live/detail?id=' + item.live_id + '&makerid=' + item.maker_id + '&uid=' + param.uid + '&pagetag=04-9';
                                        liveHtml += '<a href="' + liveaddress + '">';
                                        liveHtml += '<div class="mess-box">';
                                        liveHtml += '<img alt="loadfailed" class="messageimg" src="' + item.list_img + '">';
                                        liveHtml += '<div class="rightinfo">';
                                        liveHtml += '<p class="acttitle">' + cutString(item.subject,14) + '</p>';
                                        liveHtml += '<span class="time_icon fl"></span>';
                                        liveHtml += '<span class="pl1 fl timespan">' + item.begin_time + '</span>';
                                        liveHtml += '<div class="clearfix"></div>';
                                        liveHtml += '<span class="wjb_icon fl mt06"></span>';
                                        liveHtml += '<span class="pl1 fl timespan green mt07">' + item.price + '</span>';
                                        liveHtml += '<div class="clearfix"></div>';
//                                        liveHtml += '<p><span class="time_icon"></span><i class="pl1">' + item.begin_time + '</i></p>';
//                                        liveHtml += '<p><span class="wjb_icon"></span><i class="pl1 green">' + item.price + '</i></p>';
                                        liveHtml += '</div>';
                                        liveHtml += '</div>';
                                        liveHtml += '</a>';
                                    });
                                    messageHtml += liveHtml;
                                    messageHtml += '</div>';
                                    messageHtml += '</section>';
                                }
                                if (item.type == 3) {  //专版活动提醒
                                    var actAddress = labUser.path + '/webapp/activity/detail?makerid=' + item.maker_id + '&id=' + item.activity_id + '&uid=' + param.uid + '&position_id=' + param.position_id + '&pagetag=02-2';
                                    var zbAddress = labUser.path + '/webapp/special/detail?vip_id=' + item.vip_id + '&uid=' + param.uid + '&position_id=' + param.position_id + '&pagetag=02-1';
                                    messageHtml += '<section class="messagelist">';
                                    messageHtml += '<h1><em class="pr05">' + item.vip_name + '</em>专版有新活动发布</h1>';
                                    messageHtml += '<div class="fl f12 pubtime">' + item.send_time + '</div>';
                                    messageHtml += '<div class="clearfix"></div>';
                                    messageHtml += '<div class="messcontent">';
                                    messageHtml += '<div>点击查看活动详情，或前往<span>' + item.vip_name + '</span>专版详情页查看更多活动：</div>';
                                    messageHtml += '<a href="' + actAddress + '">';
                                    messageHtml += '<div class="mess-box">';
                                    messageHtml += '<img alt="loadfailed" class="messageimg" src="' + item.list_img + '">';
                                    messageHtml += '<div class="rightinfo">';
                                    messageHtml += '<p class="acttitle">' + cutString(item.subject,14) + '</p>';
                                    messageHtml += '<span class="time_icon fl"></span>';
                                    messageHtml += '<span class="pl1 fl timespan">' + item.begin_time + '</span>';
                                    messageHtml += '<div class="clearfix"></div>';
                                    messageHtml += '<span class="wjb_icon fl mt06"></span>';
                                    messageHtml += '<span class="pl1 fl timespan green mt07">' + item.price + '</span>';
                                    messageHtml += '<div class="clearfix"></div>';
//                                    messageHtml += '<p><span class="time_icon"></span><i class="pl1">' + item.begin_time + '</i></p>';
//                                    messageHtml += '<p class="green"><span class="wjb_icon"></span><i class="pl1">' + item.price + '</i></p>';
                                    messageHtml += '</div>';
                                    messageHtml += '</div>';
                                    messageHtml += '</a>';
                                    messageHtml += '</div>';
                                    messageHtml += '<a href="' + zbAddress + '">';
                                    messageHtml += '<div class="seen_more">立即进入<span class="pl05">' + item.vip_name + '</span>专版详情页<span class="sj_icon"></span></div>';
                                    messageHtml += '</a>';
                                    messageHtml += '</section>';
                                }
                            });
                            $('#thelist').append(messageHtml);
                            $('#container').removeClass('none');
                            $('#pullUp').data('pagenow', param.page);
                        }
                    }

                }
                else {
                    if (param.page > 1) {
                        $('#pullUp').data('pagenow', param.page - 1);
                    }
                }
            });
        }
        Zepto(function () {
            var param = {
                "uid": labUser.uid,
                "position_id":{{$position_id}},
                "page": 1,
                "page_size": pageSize
            };
            getMessageList(param);
        });
    </script>
@stop