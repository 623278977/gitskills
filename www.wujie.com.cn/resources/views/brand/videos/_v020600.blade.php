

@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/brand.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/_v020500/livedetail.css?v=01120954" rel="stylesheet" type="text/css"/>
        <style>
            ul.more_video li:first-child{
                border-top:0;
            }
            .brand-message a.btn{
            background-color: #ff5a00;
         }
        </style>

@stop
@section('main')
    <!-- <section >
        <div class="mt1-5">
            <ul class="white-bg" id="brand_videos">
                <li class="fline">
                    <div class="white-bg brand-company pl1-33 tleft">
                        <div class="relative video-img fl mr1-33">
                            <img src="{{URL::asset('/')}}/images/act_banner.png" alt="">
                            <div class="absolute video">
                                <span class="cir f12"></span><span class="time f12 color-white">08:22</span>
                            </div>
                        </div>
                        <div class="fl width60">
                            <span class="f14">上海有限公司</span>
                            <div class="brand-address f12 color999 mb05 min-h3">
                                录制于<span>2016-05-05</span>
                            </div>
                            <a class="tags-key">罗森便利店</a><a class="tags-key">便利店</a><a class="tags-key">罗森</a>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </li>
                
            </ul>
         
        </div>
    </section> -->
     <!--相关视频列表-->
                <section class="bgwhite " id="sec_video">
                    <div class="videoss none" style="padding-top:10rem;">
                        <img id="novideo" class="none" src="{{URL::asset('/')}}/images/novideo.png" alt="" style="width: 13rem;display: block;margin: 0 auto;">
                    </div>
                    <!-- <div class="brand-title f16">相关视频</div> -->
                    <ul class="more_video white-bg" id="relativevideo" style="margin-top: 1.5rem">
                        <!-- <li class="ui-border-t">
                            <div class="l video_img">
                                <p class="f14 videotime ui-border-radius"><span class="whitepoint"></span>08:02</p>
                                <img src="{{URL::asset('/')}}/images/livetips.png" alt="">
                            </div>
                            <div class="video_intro">
                                <p class="f16 mb02 text_black">标题</p>
                                <p class="f14 color999">录制于<span>2016-11-12</span></p>
                                <div class="f12 keyword"><span class="ui-border-radius ui-border-radius-8a">罗森便利店</span><span
                                            class="ui-border-radius ui-border-radius-8a">品牌加盟</span><span
                                            class="ui-border-radius ui-border-radius-8a">便利店</span></div>
                            </div>
                            <div class="clearfix"></div>
                        </li>
                        <li class="ui-border-t">
                            <div class="l video_img">
                                <p class="f14 videotime ui-border-radius"><span class="whitepoint"></span>08:02</p>
                                <img src="{{URL::asset('/')}}/images/livetips.png" alt="">
                            </div>
                            <div class="video_intro">
                                <p class="f16 mb02 text_black">标题</p>
                                <p class="f14 color999">录制于<span>2016-11-12</span></p>
                                <div class="f12 keyword"><span class="ui-border-radius ui-border-radius-8a">罗森便利店</span><span
                                            class="ui-border-radius ui-border-radius-8a">品牌加盟</span><span
                                            class="ui-border-radius ui-border-radius-8a">便利店</span></div>
                            </div>
                            <div class="clearfix"></div>
                        </li> -->

                    </ul>
                     <div class="fixed-bg none" style="z-index: 99"></div>
                     <div class="share-title fixed color666 f14 none">
                        <ul id="ul_share_t">
                          
                        </ul>
                    </div>

                    <!-- 公用-底部按钮 -->
       <!-- 公用-底部按钮 -->
        <div class="brand-btns fixed width100 none brand-p brand-s" id="brand_btns_app">
            <div class="btn fl width25 " id="brand_collect">
                    <span class=" b-collect" data-fav="">  </span>      
            </div>
            <div class="btn fl width25 " id="brand_collect" >
                    <span class="  brand-collect-contact" >  </span>      
            </div>
            <div class="btn fl width25 lh45" id="brand_award" data-fund="">
                <p class="tc color-red f16">创业红包</p>
                <!-- <p class="tc color-yellow brand_fund" id="brand_fund"></p> -->
            </div>
            <div class="btn fl width25 pt05" id="brand_suggest">
                <p class="tc color-white f16">发送加盟意向</p>
                <p class="tc color-yellow f12">*获取更多资料</p>
            </div>
        </div>
        <div class="brand-btns fixed width100 none brand-np  brand-s" id="brand_btns_app ">
            <div class="btn fl width33 " id="brand_collect">
                <span class=" b-collect" data-fav="">  </span>      
            </div>
             <div class="btn fl width33 " id="brand_collect" >
                    <span class="  brand-collect-contact" >  </span>      
            </div>
           
            <div class="btn fl width33 pt05" id="brand_suggest">
                <p class="tc color-white">发送加盟意向</p>
                <p class="tc color-yellow f12">*获取更多资料</p>
            </div>
        </div>
        <!-- 公用-分享出去的底部按钮 -->
        <div class="brand-btns fixed width100 none " id="brand_btns_share">
            <div class="btn fl width50 tc color-red lh45 brand-share-ask" data-type="message_more">获取更多资料</div>
            <div class="btn fl width50 tc color-white bg-red lh45"><a href=" tel:4000110061" class="blocks color-white">电话咨询</a></div>
        </div>
        <!-- 公用-发送加盟意向 -->
        <div class="brand-message fixed bgcolor none " id="brand-mes" style="top:0">
            <form action="">
                <p class="fline f14 margin0 ">
                    <label for=""> 姓名：</label>
                    <input type="text" placeholder="请输入您的姓名" name="realname">
                </p>
                <p class="f14 margin0  mb5">
                    <label for=""> 手机号：</label>
                    <input type="text" placeholder="请输入您的手机号" name="phone">
                </p>
                <p class="mt1-5">
                    <label for="" class="f14 color333">咨询：</label>
                    <textarea id="" class="f14 width80" name="consult" placeholder="请输入您要咨询的事项，项目专员会与你取得联系"></textarea>
                </p>
                <a href="javascript:;" class="btn f14 send-mes" >提交</a>  
                <input type="reset" class="none b-reset" >   
            </form>
        </div>
        <!-- 公用-分享出去的发送加盟意向 -->
        <div class="brand-message fixed bgcolor  brand-message-share none" style="bottom:0">
            <div class="f16 color-blue pl1-33 mt1 mb1" >如果您对该项目感兴趣，欢迎给企业留言</div>
            <form action="">
                <p class="fline f14 margin0 ">
                    <label for=""> 姓名：</label>
                    <input type="text" placeholder="请输入您的姓名" name="realnames">
                </p>
                <p class="f14 margin0  mb5">
                    <label for=""> 手机号：</label>
                    <input type="text" placeholder="请输入您的手机号" name="phones">
                </p>
                <p class="mt1-5">
                    <label for="" class="f14 color333">咨询：</label>
                    <textarea  id="" class="f14 width80 color8a" name="consults" style="height: 7rem;" placeholder="请输入您要咨询的事项，项目专员会与你取得联系"></textarea>
                </p>
                <a href="javascript:;" class="btn f14 share-send-mes" >提交</a>  
                <input type="reset" class="none share-reset" >   
            </form>
        </div>
        <!-- 公用-红包 -->
        <div class="brand-packet fixed none">
            <div class="relative">
                <div class="packet-body relative">
                    <span class="title">创业基金</span>
                    <span class="award b-fund"></span>
                    <div class="packet-front absolute">
                        <p class="f16 color-white tc">恭喜您获得<span class="b-fund"></span>元创业基金</p>
                        <p class="f16 color-white tc mb5">已自动存入您的创业账户</p>

                        <p class="tc"><a  class="f18 mt2 mb2 tc toPacket">查看我的红包>></a></p>
                        <p class="f14 tc color-white mt2">具体使用规则参考<a href="##" class="toFound" style="text-decoration: underline;">创业基金使用说明</a></p>
                    </div>
                </div>
                <div class="close absolute f20 tc" id="packet_close">
                    ×
                </div>
            </div>
        </div>
        <div class="share-title fixed color666 f14 none">
            <ul id="ul_share_t">
              
            </ul>
        </div>
        <!-- 公用-蒙层 -->
        <div class="fixed-bg none" ></div>
        <div class="tips none"></div>
         <div id="brand_logo" class="none"></div>
        <!--浏览器打开提示-->
        <div class="safari none">
            <img src="{{URL::asset('/')}}/images/safari.png">
        </div>
        <div id="brand_name" class="none"></div>
        <div id="category_name" class="none"></div>
    </section>
@stop

@section('endjs')
	<script src="{{URL::asset('/')}}/js/brand2.js"></script>
    <script>
    Zepto(function() {
        var args = getQueryStringArgs();
        var uid = args['uid'] || 0,
        id = args['id'];
        function videos(id, uid) {
            var param = {};
            param['id'] = id;
            param['uid'] = uid;
            var url = labUser.api_path + '/brand/detail/_v020500';
            ajaxRequest(param, url, function(data) {
                if (data.status) {   
                    getVideos(data.message);               
                }
            });
        };
        videos(id,uid);
        function getVideos(result){
            var videos = result.videos;
            $.each(videos, function(i, item){
                var str = '';
                str+='<li class="ui-border-t videoLi" data-id="'+item.id+'">';
                    str+='<div class="l video_img" style="margin-left:1.33rem;">';
                        if (item.duration=='00:00'){
                            str+='';
                        }else{
                            str+='<p class="f14 videotime ui-border-radius"><span class="whitepoint"></span>'+item.duration + '</p>';
                        }   
                        str+='<img src="' + item.image + '" alt="">';
                    str+='</div>';
                    str+='<div class="video_intro">';
                        str+='<p class="f16 mb02 text_black  no-wrap w80">' + item.subject + '</p>';
                        str+='<p class="f14 color999">录制于<span>' + unix_to_yeardate(item.created_at) + '</p>';
                        str+='<div class="f12 keyword">';
                        for (var i = 0; i < item.keywords.length; i++) {
                            str+='<span class="border-8a-radius ui-border-radius-8a">' + item.keywords[i] + '</span>';
                        }
                        str+='</div>';
                    str+='</div>';
                    str+='<div class="clearfix"></div>';
                str+='</li>';
                $('#relativevideo').append(str);
            });
            console.log(videos.length);
            if (videos==''||videos==undefined||videos.length==0) {
                $('#sec_video').removeClass('bgwhite');
                $('.videoss').removeClass('none');
                console.log('novideo')
            }
        };
        $(document).on('click','.videoLi',function () {
            var ids = $(this).data('id');
            window.location.href=labUser.path+'webapp/vod/detail/_v020500?id='+ids+'&uid='+uid+'&pagetag=05-4';
        })

      }); 
    </script>
@stop