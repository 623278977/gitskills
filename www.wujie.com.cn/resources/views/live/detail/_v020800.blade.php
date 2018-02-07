@extends('layouts.default')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/iscroll.css"/>
    <link href="{{URL::asset('/')}}/css/_v020700/livedetail.css?v=03162002" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/_v020700/intlTelInput.css" rel="stylesheet" type="text/css"/>
    <style>
        .intl-tel-input.inside input[type="text"], .intl-tel-input.inside input[type="tel"] {
            padding-left: 7.5rem;
        }
        .intl-tel-input {
            position: relative;
            display: block;
            height: 100%;
        }
        .intl-tel-input .flag-dropdown {
            left: 3rem;
        }
        .fixed-bg {
           width: 100%;
           height: 100%;
           background: rgba(0,0,0,0.3);
          position: fixed;
          top: 0;
          z-index: 10;
            }
    </style>
@stop
@section('main')
    <section class="containerBox" id="containerBox"  style="visibility: hidden">
     <!-- 公用蒙层 -->
         <div class="tips none"></div>
         <div class="fixed-bg none"></div>
       <!-- 顶部提交按钮 -->
        <div class="brand-message brand-message2 fixed bgcolor none" id="brand-mes" style="top:0">
            <form action="">
                <p class="fline f14  name ">
                    <label for=""> 姓名：</label>
                    <input type="text" placeholder="请输入您的姓名" name="realname">
                </p>
                <p class="f14 name">
                    <label for=""> 手机号：</label>
                    <input type="text" placeholder="请输入您的手机号" name="phone">
                </p>
                <p class="name textarea">
                    <label for="" class="f14 color333">咨询：</label>
                    <input  class="f14 width80" placeholder="请输入您要咨询的事项" name="consult">
                </p>
                <a href="javascript:;" class="btn f14 send-mes" >提交</a>  
                <input type="reset" class="none b-reset" >   
            </form>
        </div>
        <!--预告、收费提示-->
        <div class="share_video top0 f12" id="share_video">
              <!--  积分购买按钮 -->
                <div id="share_need"  class="share_need none">
                        <span class="f12" style="color:#333;">收费直播</span>
                        <b><span id="fee" style="padding-left:0.7rem;color:#ff4d64" class="fee"></span></b>
                        <span style="color:#ff4d64">积分</span>
                        <span style="color:#666;">/人</span> 
                        <div class="buyrule ">
                        <span class="span1">立即购买，获取直播门票</span><span class="span2">先买先得，不可错过</span></div>
                        <div class="fixed_btn_meet  border1"><button class="buy_btn l ">购买</button></div>
                </div>  
        </div>
        <div id="video_box" class="share_video none" style="top:3.5rem;"></div>
        <section class="live_detail pt23-2875" id="live_detail">
          <!--大家说table切换栏-->
            <div id="nav_every_say" class="nav_add none">
                <ul>
                    <li id="nav_de_" class="nav_de_ nav_color">详情</li>
                    <li id="nav_eve_say" class="nav_de_">大家说</li>
                </ul>
            </div>
           <!-- 评论列表及其相关-->
            <div class="conmments mt7-133  none">
                <div class="comment  fline ">
                    <p id="com_text" class="f16 fline b padding-left2">评论<span style="color:#ff5a00;padding-left:1rem" class="com_num ff5 f14"></span><span class="publish  ff5 f14 r pr1-33" id="publish">发表评论
                    </span></p>
                   <!--  <div id="_hudong" class="pr1-33"></div> -->
                    <ul id="comm" class="pr1-33">
                   </ul>
                    <div class="no_data none" >
                        <center>
                            <img src="{{URL::asset('/')}}/images/020502/no_message.png" style='width:15rem;height:15rem' alt="">
                        </center>
                    </div>
                </div>
                <button class="getMore f12 c8a">点击加载更多</button>
                <!-- <div id="comment_btn" class="comment_btn"><button type="button" class="tl" style="width: 30rem;">我来说两句...</button><span class="uploadpic1">  
                </span><i class="uploadpictext f12">发表图片</i></div> -->

            </div>
            <div class="commentback none" id="commentback">
                <div style="width: 100%;height:100%;position:absolute;bottom:15rem;left:0;" id="tapdiv"></div>
                <div class="textareacon">
                    <textarea class="f12" name="comment" id="comtextarea" cols="30" rows="10" style="resize: none;" placeholder="请输入评论内容"></textarea>
                    <button class="fr subcomment f16" id="subcomments">发表</button>
                </div>
           </div> 
            <!--直播概况-->
            <section id="live_introduce" class="">
                <!--活动介绍-->
                <div id="bind_activity" class="activity_info f14  none  bgwhite">
                </div>
                <!--嘉宾结束-->
                <div id="bind_guest" class="bgwhite pl1-33 mt1-33 guest none">
                    <div class="brand-title fline f16">相关嘉宾</div>
                </div>
                <!--相关品牌-->
                <div id="bind_brand" class="bgwhite pl1-33 mt1-33 none">
                    <div class="brand-title f16">相关品牌</div>
                </div>
                <!--直播介绍-->
                <div id="intro_live" class="mt1-33 bgwhite pl1-33">
                    <div class="brand-title fline f16">直播介绍</div>
                    <div class="live_info f12" id="live_info">
                    </div>
                </div>
                <div id="morebrand_detail" class="f12 color999 tc pt4 pb4">没有更多了</div>
            </section>
            <!--立即加盟-->
            <section id="barnd_list" class="none">

            </section>
            <!--互动栏-->
            <section id="comment" class="none">
                <div id="wrapper" class="top31-7875" style="padding-bottom: 4rem;">
                    <div id="scroller">
                        <div id="pullDown" style="display: none;">
                            <span class="pullDownLabel" style="display: none;"></span>
                        </div>
                        <div class="clearfix"></div>
                        <div id="thelist" class="bgfont">
                            <div class="tc none nocomment" id="nocommenttip">
                                <img src="{{URL::asset('/')}}/images/nomessage_icon1.png" style="height: 16rem;width: 16rem;margin:0 auto;display: inline-block;">
                            </div>
                            <div class="comment_tit amazeComment none">精彩评论（<em class="num">0</em>）</div>
                            <div class="block none" id="amazeComment"></div>
                            <div class="comment_tit allComment none">全部评论（<em class="num">0</em>）</div>
                            <div class="livecommentblock none" id="allComment" style="display:none!important">

                            </div>
                        </div>
                        <div id="pullUp" data-pagenow="1" style="display: none;" class="">
                            <span class="pullUpLabel" style="display: none;"></span>
                        </div>
                        <div class="morecomment none" id="morecm">加载更多</div>
                    </div>
                    <div class="refreshpic1"></div>
                </div>
            </section>
            <!--直播回放栏-->
            <section id="live_video" class="mt1-33 none">
                <!--相关视频列表-->
                <section class="bgwhite pl1-33">
                    <div class="brand-title f16">相关视频</div>
                    <ul id="relativevideo" class="more_video">
                        <li class="ui-border-t" style="padding-top:0;padding-bottom: 0;">
                            <div class="novideo"><img src="/images/liveflag.png"/>
                                <p class="color999">视频还在制作中，请耐心等待 ~ </p></div>
                        </li>
                    </ul>
                </section>
                <!--相关资讯-->
                <section class="bgwhite pl1-33 mt1-33 none" id="messagecont">
                    <div class="brand-title f16">相关资讯</div>
                    <ul id="relativemessage" class="relativemessage">

                    </ul>
                </section>
                <section>
                    <div class="f12 color999 tc pt4 pb4">没有更多了</div>
                </section>
            </section>

            <!--分享图片-->
            <input type="hidden" data-src="" id="share_img"/>
            <input type="hidden" data-maxid="0" data-minid="0" id="commentflag"/>
            <div class="none" id="livesubject" data-begintime="" data-livenum="0" data-share_mark=""
                 data-relation_id=""></div>

            <!-- 分享页快速注册 -->
            <div class="remindpart none" id="registerpart" style="z-index:197;">
                <div class="content">
                    <div class="tiptitle f18 tc remindfontcolor">快速登录/注册,观看直播：</div>
                    <div class="userinput remindcolor">
                        <div class="putdiv remindcolor f12 successtitle">登录无界商圈APP，更多高清视频等你来观看</div>
                        <div class="putdiv remindcolor height06"><input type="text" name="phonenumber" value="+86 " placeholder="手机号" id="zcphone"/></div>
                        <div class="putdiv remindcolor height06"><input type="text" name="mescode" placeholder="短信验证码" id="zcyzm"/><button class="ident_code" id="mescode">获取验证码</button></div>
                        <div class="putdiv remindcolor tc"><button class="subbtn f16" id="registerbtn">提交</button></div>
                    </div>
                    <div class="closepic"></div>
                </div>
            </div>
            <div class="remindpart none" id="liveremind" style="z-index:196;">
                    <div class="content">
                        <div class="tiptitle f18 tc remindfontcolor">设置直播提醒</div>
                        <div class="userinput remindcolor">
                            <div class="f12 putdiv">
                                <div class="pdiv">
                                    <p id="livename"></p>
                                    <p id="livetime"></p>
                                </div>
                            </div>
                            <div class="putdiv remindcolor f12 tiptexts">
                                可以订阅该场直播，我们将在 直播开始前30分钟 以短信发送直播提醒消息
                            </div>
                            <div class="putdiv remindcolor height06">
                                <input type="text" name="phonenumber" value="+86 " placeholder="手机号" id="yyphone"/>
                            </div>
                            <div class="putdiv remindcolor height06">
                                <input type="text" name="mescode" placeholder="短信验证码" id="yyyzm"/>
                                <button class="ident_code" id="getcode">获取验证码</button>
                            </div>
                            <div class="putdiv remindcolor tc">
                                <button class="subbtn f16" style="margin-top: 1rem;" id="yysubmit">提交</button>
                            </div>
                        </div>
                        <div class="closepic"></div>
                    </div>
            </div>
        </section>
        <div id="comment_btn" class="comment_btn none">
            <span id="_zan_"></span> 
            <span id="dian_zan_number" class="dian_zan_number"></span>
            <button type="button" class="tl" style="width:30rem;">我来说两句...</button>
            <span class="uploadpic1" id="chuan_pict" style="float:right;margin-right:1rem;margin-top:0.5rem"></span>
        </div>
    </section>
    @stop
    @section('endjs')
    <script src="https://qzonestyle.gtimg.cn/open/qcloud/video/live/h5/live_connect.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/iscroll.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/iscroll_touch.js"></script>
    <script src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
    <script src="{{URL::asset('/')}}/js/intlTelInput.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/comment.js?v=12290930"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/_v020800/live.js?v=03162003"></script>
    <script> 
        $('#zcphone').intlTelInput();
        $('#yyphone').intlTelInput();
        //加载
        var touch = $.extend({}, {
            getAjaxDownData: function () {
                // myScroll.refresh();
            },
            getAjaxUpData: function () {
                //myScroll.refresh();
            }
        });
        // //点击评论框
        // $(document).on('click','#publish',function(){
        //     if(shareFlag){
        //     $('#commentback').removeClass('none');
        //     $('#comtextarea').focus();
        //     if($('#comtextarea').val()==''){
        //     $('#subcomments').css('backgroundColor','#999');
        //     }
        //     }else{
        //         uploadpic(param.id,'Live',false);
        //         $('#commentback').removeClass('none');
        //         }

        //     });
        //     $(document).on('click ','#subcomments',function(){
        //         param.content=$('#comtextarea').val();
        //         console.log(param.content);
        //         param.page=1;
        //         if(shareFlag){
        //             param.uid=0;
        //         }
        //         addComment(param,shareFlag);
        //     })
        //评论按钮颜色变化
         //点击加载更多

          function Refresh() {
            if (live_state == 'future') {
                var args = getQueryStringArgs(),
                    id = args['id'] || '0',
                    uid = args['uid'] || '0';
                var params={};
                params['page'] = 1;
                params['page_size']=10;
                params['section']=0;
                getComments(params,id,uid,'Live');
                $('body').scrollTop(0);
            } else if (live_state == 'is_living') {
            var parameter = {
                "type": param.type,
                "uid": param.uid,
                "id": param.id,
                "fromId": $('#commentflag').data('maxid'),
                "update": "new",
                "fecthSize": 0
            };
            Comment.getFreshList(parameter);
        }
    }
       
        //分享
        function showShare() {
            var title = $('#livesubject').text();//直播标题
            var img = $('#share_img').data('src');
            var header = '直播';
            var summary = cutString($('#containerBox').attr('summary'), 18);
            var content = cutString($('#live_info').text(), 18);//直播介绍
            if(summary!=''){
		    	content = summary;
		    };
            if($('#livesubject').data('distribution_id') > 0){
                var args = getQueryStringArgs(),
                        live_id = args['id'] || '0';
                var pageUrl = window.location.href + '&share_mark=' + $('#livesubject').data('share_mark');//用来追踪原始分享者
                var share_mark = $('#livesubject').data('share_mark');
                var url = labUser.api_path + '/index/code/_v020500';
                ajaxRequest({}, url, function (data) {
                    var code = data.message;//code
                    pageUrl = pageUrl + '&code=' + code;
                    shareOut(title, pageUrl, img, header, content, '', '', '', '', share_mark, code, 'share', 'live', live_id);//分享
                });
            }
            else{
                var pageUrl = window.location.href;
                shareOut(title, pageUrl, img, header, content,'','','','','','','share','live',live_id);//分享
            }
        }
        //刷新
        function reload() {
            location.reload();
        }
        
      //样式切换
      function table(){
         $('.nav_add ul li').on('click',function(){
            $(this).addClass('nav_color').siblings().removeClass('nav_color'); 

         })
         
         $('#nav_eve_say').on('click',function(){
          $('.conmments').removeClass('none');
          $('#live_introduce').addClass('none');
          $('#comment_btn').show(); 
        })
         //详情
         $('#nav_de_').on('click',function(){
          $('#comment_btn').hide();
          $('.conmments').addClass('none');
          $('#live_introduce').removeClass('none');
        })
        $('#comtextarea').on('keyup',function(){
         $('#subcomments').css('backgroundColor','#ff5a00');
            if($('#comtextarea').val()==''){
            $('#subcomments').css('backgroundColor','#999');
                }
            })
         // 点击灰框评论消失
        $(document).on('click','#tapdiv',function(){
                $('#commentback').addClass('none');
            })
      };
      table(); 
      function tips(e) {
            $('.tips').text(e).removeClass('none');
            setTimeout(function() {
                $('.tips').addClass('none ');
            }, 1500);
        };
      //点击蒙层消失
      function hideshow(){
                var urlPath = window.location.href;
                var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
                 $('#share_video').on('click',function(){
                    $('.back').addClass('none');
                })
                $('#bind_activity').on('click',function(){
                    $('.back').addClass('none');
                })
                $('#intro_live').on('click',function(){
                    $('.back').addClass('none');
                })
                 $('#morebrand_detail').on('click',function(){
                    $('.back').addClass('none');
                })
                $('.brand-title').on('click',function(){
                    $('.back').addClass('none');
                })
                $('#knowdetail').on('click', function () { 
                window.location.href = labUser.path + 'webapp/protocol/moreshare/_v020700';
                })
                if($('.addbackgd').length==0){
                    $('.brand-title').addClass('none');
                }
      }
      hideshow();
     var urlPath = window.location.href;
     var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
          function collectbrand(id,uid,subscribe){
                var param = {};
                param["id"] = id;
                param["uid"] = uid;
                param["type"] = subscribe;
                console.log(param); 
                var url = labUser.api_path + "/brand/collect";
                ajaxRequest(param, url, function(data) {
                    if (data.status) {  
                        if (param["type"] == 'do'){
                             $('.shoucang').addClass('shou').removeClass('cang').next().text('已收藏');
                          
                        } else  {
                             $('.shoucang').addClass('cang').removeClass('shou').next().text('收藏');
                        }
                    }
                });
           } 
       //收藏事件  
       $(document).on("click", ".shoucang_requ", function(){
                    var subscribe = $(this).find('.shoucang').data("subscribe");
                    var ID = $(this).find('.shoucang').data("brand_id");
                if(shareFlag){
                   tips('请先登录APP')
                 } else {
                    console.log(ID);
                    if($(this).find('.shoucang').hasClass('cang')) {
                        subscribe = 'do';
                    } else {
                        subscribe = 'undo';
                    }
                    collectbrand(ID,uid,subscribe); 
                 }       
            });
     $(document).on('click', '.activity', function() {
                  var id = $(this).data('activity_id');
                if(shareFlag){
                   window.location.href = labUser.path + "webapp/activity/detail/_v020700?id=" + id + "&uid=" + uid + "&makerid=0&position_id=0&is_share=1";
                 }else{
                   window.location.href = labUser.path + "webapp/activity/detail/_v020700?id=" + id + "&uid=" + uid + "&makerid=0&position_id=0"; 
                 }
                
            });
    </script>
@stop