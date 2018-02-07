@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/iscroll.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/messages.css?v=03162033" rel="stylesheet" type="text/css"/>
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
            <!--
            <div class="tiptext">抱歉！目前没有任何新的官方通知</div>
            -->
        </div>
    </section>
@stop
@section('endjs')
    <script type="text/javascript" src="{{URL::asset('/')}}/js/iscroll.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/iscroll_touch.js"></script>
    <script type="text/javascript">
        var args = getQueryStringArgs(),
                uid = args['uid'] || '0';
        var pageNow = 1, pageSize = 10;
        var touch = $.extend({}, {
            getAjaxDownData: function () {
            },
            getAjaxUpData: function () {
                var page = $('#pullUp').data('pagenow');
                page++;
                var param = {
                    "uid": uid,
                    "page": page,
                    "page_size": pageSize
                };
                getMessageList(param);
                myScroll.refresh();
            }
        });
        function getMessageList(param) {
            var url = labUser.api_path + '/message/officialmessage/_v020502';
            // var url = '/api/message/officialmessage';
            ajaxRequest(param, url, function (data) {
                //判断缺省0为false
                if (data.total == 0) {
                    $('#nomessage').removeClass('none');
                    $('#container').removeClass('none');
                }
                else {
                    $('#nomessage').addClass('none');
                }
                if (data.status) {
                    //html调整
                    if (data.message.length > 0) {
                        var messageHtml = '';
                        $.each(data.message, function (index, item) {
                            if (item.type == 1) {
                                messageHtml += '<section class="messagelist" data-messageid=' + item.id + '>';
                                messageHtml += '<h1>' + item.title + '</h1>';
                                messageHtml += '<div class="fl f12 pubtime">' + item.created_at + '</div>';
                                messageHtml += '<div class="clearfix"></div>';
                                messageHtml += '<div class="messcontent">' + item.content + '</div>';
                                messageHtml += '</section>';
                            }
                            else if (item.type == 7) {
                                var zbtip = labUser.path + '/webapp/rights/detail?vip_id=' + item.vip_id + '&uid=' + param.uid;
                                messageHtml += '<section class="messagelist">';
                                messageHtml += '<h1>成功购买<em class="pl05">' + item.vip_name + '</em>' + item.term_name + '</h1>';
                                messageHtml += '<div class="fl f12 pubtime">' + item.created_at + '</div>';
                                messageHtml += '<div class="clearfix"></div>';
                                messageHtml += '<div class="messcontent">';
                                messageHtml += '<div class="color666">你已成功购入<span class="pl05">' + item.vip_name + '</span>' + item.term_name + '</div>';
                                messageHtml += '<div class="mess-box f12">';
                                messageHtml += '<p>套餐名称：' + item.term_name + '</p>';
                                messageHtml += '<p>有效期至：' + item.deadline + '</p>';
                                messageHtml += '</div>';
                                messageHtml += '</div>';
                                messageHtml += '<a href="' + zbtip + '">';
                                messageHtml += '<div class="seen_more">点击了解 <span class="acolor pl05">'+item.vip_name+'</span><span class="acolor pl05">会员权益说明</span><span class="sj_icon"></span></div>';
                                messageHtml += '</a>';
                                messageHtml += '</section>';
                            }
                            else if (item.type == 8) {
                                var zbtip = labUser.path + '/webapp/rights/detail?vip_id=' + item.vip_id + '&uid=' + param.uid;
                                if (item.is_over == 1) {
                                    //已过期
                                    messageHtml += '<section class="messagelist">';
                                    messageHtml += '<h1>您的<em class="pl05">'+item.vip_name+'会员</em>已经到期，是否需要继续续费</h1>';
                                    messageHtml += '<div class="fl f12 pubtime">' + item.created_at + '</div>';
                                    messageHtml += '<div class="clearfix"></div>';
                                    messageHtml += '<div class="messcontent">';
                                    messageHtml += '<div class="color666"><span class="">' + item.vip_name + '会员</span>于' + item.deadline + '到期，是否继续续费享受会员福利?</div>';
                                    messageHtml += '<div class="mess-box">';
                                    messageHtml += ' <span class="f12">有效期累积至：'+item.deadline+'<em class="orange">（已过期）</em></span>';
                                    messageHtml += '<a href="javascript:;" class="buyvip f12" data-vipid="'+item.vip_id+'">立即续费</a></p>';
                                    messageHtml += '</div>';
                                    messageHtml += '</div>';
                                    messageHtml += '<a href="' + zbtip + '">';
                                    messageHtml += '<div class="seen_more">点击了解 <span class="acolor pl05">'+item.vip_name+'</span><span class="acolor pl05">会员权益说明</span><span class="sj_icon"></span></div>';
                                    messageHtml += '</a>';
                                    messageHtml += '</section>';
                                }
                                else{
                                    messageHtml += '<section class="messagelist">';
                                    messageHtml += '<h1>您的<em class="pl05">'+item.vip_name+'会员</em>即将到期，是否需要继续续费</h1>';
                                    messageHtml += '<div class="fl f12 pubtime">' + item.created_at + '</div>';
                                    messageHtml += '<div class="clearfix"></div>';
                                    messageHtml += '<div class="messcontent">';
                                    messageHtml += '<div class="color666"><span class="">' + item.vip_name + '会员</span>将于' + item.deadline + '到期，是否继续续费享受会员福利?</div>';
                                    messageHtml += '<div class="mess-box">';
                                    messageHtml += ' <span class="f12">有效期累积至：'+item.deadline+'<em class="orange">'+'&nbsp;&nbsp;'+item.available_time+'</em></span>';
                                    messageHtml += '<a href="javascript:;" class="buyvip f12" data-vipid="'+item.vip_id+'">立即续费</a></p>';
                                    messageHtml += '</div>';
                                    messageHtml += '</div>';
                                    messageHtml += '<a href="' + zbtip + '">';
                                    messageHtml += '<div class="seen_more">点击了解 <span class="acolor pl05">'+item.vip_name+'</span><span class="acolor pl05">会员权益说明</span><span class="sj_icon"></span></div>';
                                    messageHtml += '</a>';
                                    messageHtml += '</section>';
                                }

                            }
                        });
                        $('#thelist').append(messageHtml);
                        $('#container').removeClass('none');
                        $('#pullUp').data('pagenow', param.page);
                    }
                }
//                else {
//                    if (param.page > 1) {
//                        $('#pullUp').data('pagenow', param.page - 1);
//                    }
//                }
            });
        }
        Zepto(function () {
            var param = {
                "uid": uid,
                "page": 1,
                "page_size": pageSize
            };
            getMessageList(param);
            $(document).on('tap', '.buyvip', function () {
                var vip_id = $(this).data('vipid');
                buyVip(vip_id);
            });
        });
    </script>
@stop