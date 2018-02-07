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
                        <h1>成功购买<em>专版会员</em>3个月套餐</h1>
                        <div class="fr f12 pubtime">年月日时分秒</div>
                        <div class="clearfix"></div>
                        <div class="messcontent">你已成功购买是是是套餐
                            <div class="mess-box">
                                <p>套餐名称：15131</p>
                                <p>有效期至：2017-02-01</p>
                            </div>
                        </div>
                        <div class="seen_more">点击了解 <a href="#">中美会员教育</a>  <a href="">会员权益说明</a><span class="sj_icon"></span></div>
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
                //判断缺省0为false
                if(data.total==0){
                    $('#nomessage').removeClass('none');
                    $('#container').removeClass('none');
                }
                else{
                    $('#nomessage').addClass('none');
                }
                if (data.status) {
                    //html调整
                    if(data.message.length>0){
                        var messageHtml='';
                        $.each(data.message,function(index,item){
                            messageHtml+='<section class="messagelist" data-messageid='+item.id+'>';
                            messageHtml+='<h1>'+item.title+'</h1>';
                            messageHtml+='<div class="fr f12 pubtime">'+item.created_at+'</div>';
                            messageHtml+='<div class="clearfix"></div>';
                            messageHtml+='<div class="messcontent">'+item.content+'</div>';
                            messageHtml+='</section>';
                        });
                        $('#thelist').append(messageHtml);
                        $('#container').removeClass('none');
                        $('#pullUp').data('pagenow',param.page);
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
                "page": 1,
                "page_size": pageSize
            };
            getMessageList(param);
        });
    </script>
@stop