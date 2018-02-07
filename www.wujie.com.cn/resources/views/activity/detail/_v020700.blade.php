@extends('layouts.default')
@section('css')
    <!-- <link href="{{URL::asset('/')}}/css/_v020700/brand.css" rel="stylesheet" type="text/css"/> -->
    <link rel="stylesheet" href="{{URL::asset('/')}}/css/swiper.min.css">
    <link href="{{URL::asset('/')}}/css/_v020700/actdetail.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/_v020700/act020700.css" rel="stylesheet" type="text/css"/>  
    <style>
        .ui-nowrap-multi {
       display: -webkit-box;
      overflow: hidden;
    text-overflow: ellipsis;
    -webkit-box-orient: vertical;
    -webkit-line-clamp: 2
}
.distribution p img{
    width:1.33rem;
    margin-right:0.6rem;
    vertical-align: baseline;
}
.distribution p {
    padding-top: 1.5rem;
    padding-bottom: 1rem;
    padding-right: 1.33rem;
    font-size: 1.4rem;
    margin: 0;
}
.distribution p a{
    display: inline-block;
    width:8.6rem;
    height:2.666rem;
    line-height: 2.5rem;
    border:1px #ffac00 solid;
    color:#ffac00;
    font-size: 1.2rem;
    border-radius: 0.2rem;
    text-align: center;
    margin-top:-0.3rem;
}
.dis_coin {
    width:31rem;
    float: right;
    transition: 0.5s all;
}
.my-ques{
    width:11.73rem;
    height:2.66rem;
    border:1px #ffac00 solid;
    color:#ffac00;
    font-size: 1.2rem;
    border-radius: 0.2rem;
    text-align: center;
    background-color: #fff;
    margin-top:-0.74rem;
}
.tf.fline::after{
    top:-1px;
    bottom: 0;
}

.brand_act>div{
    padding:1.7rem 1.33rem;
}
.pb1-5 {
    padding-bottom: 1.5rem;
}
.tc {
    text-align: center;
}
    </style>
@stop
@section('beforejs')
   <script type="text/javascript">
   var args = getQueryStringArgs(),
        uid = args['uid'],
         id = args['id'],
        urlPath = window.location.href;
    var origin_mark = args['share_mark'] ;//分销参数，分享页用
    var origin_code = args['code'] || 0;
    var shareFlag = urlPath.indexOf('is_share=1') > 0 ? true : false;
    var shareUrl = urlPath.indexOf('is_share') > 0 ? '&is_share=1' : '';
   if(!(isiOS||isAndroid)){
        window.location.href = labUser.path + 'webapp/activity/sharepc/_v020700?id='+id+'&uid='+uid;
   }
   </script>
@stop
@section('main')
    <section id="act_container" class="none">
       <!--  提交意见 -->
       <div class="brand-message brand-message2 fixed bgcolor none " id="brand-mes" style="top:0;z-index:100000000000000000">
            <form action="">
                <p class="fline f14  name ">
                    <label for="">姓名：</label>
                    <input type="text" placeholder="请输入您的姓名" name="realname">
                </p>
                <p class="f14 name">
                    <label for="">手机号：</label>
                    <input type="text" placeholder="请输入您的手机号" name="phone">
                </p>
                <p class="name textarea">
                    <label for="" class="f14 color333">咨询：</label>
                    <input  class="f14 width80" name="consult" placeholder="请输入您要咨询的事项">
                </p>
                <a href="javascript:;" class="btn f14 send-mes" >提交</a>  
                <input type="reset" class="none b-reset" >   
            </form>
        </div>
        <!-- 公用蒙层 -->
         <div class="tips none"></div>
         <div class="fixed-bg none"></div>
        <!--安装app-->
        <div class="app_install fixed none" id="installapp">
            <i class="l">打开无界商圈APP，体验更多精彩内容 >></i>
            <span class="r" id="openapp"><img src="{{URL::asset('/')}}/images/install_btn1.png" alt=""></span>
            <div class="clearfix"></div>
        </div>
        <!--邀请好友-->
        <div class="app_install fixed none" id="yaoqing">
            <i class="l">邀请好友注册无界商圈，获得免费门票</i>
            <span class="r" id="yaoqingbtn"><img src="{{URL::asset('/')}}/images/yaoqing.png" alt=""></span>
            <div class="clearfix"></div>
        </div>

        <div id="share" >
            <!-- <p class="f12 l">分享活动，立即获得100积分</p>
            <button class="c00a0ff l f12 understand"><img src="{{URL::asset('/')}}/images/020700/notice.png" alt="">了解更多分享机制</button>
            <span class="fff f16 r close_share"><img src="{{URL::asset('/')}}/images/share_close.png" alt=""></span> -->
        </div>
       <!--  活动图片 -->
        <section>
            <div class="swiper-container">
                <div class="swiper-wrapper"></div>
                <div class="swiper-pagination swiper-pagination-fraction"></div>
            </div>
        </section>
      <!--   直播图片 -->
      <div id="livephoto"  class="livephoto none"></div>
        <!-- 活动名称 -->
        <section id="act_intro" class="mt0" style="padding-bottom: 7rem;">
            <div class="actitle f16 fline color333" style="margin-bottom:1rem;">
                    <span id="act_name"  class="b">活动标题</span>
                    <div id="baoming" class="baoming"></div>
            </div>
            <!-- 分销出现 -->
            <div class="distribution white-bg pl1-33 mb1-5" id="distribution">
                        <p class="fline">
                            <img src="{{URL::asset('/')}}/images/cash.png" alt="">
                            <span>赚取佣金拿现金，分享邀请好友全搞定!</span><span class="f12 color666 r" id="knowdetail" style='margin-top:0.1rem'>了解详细规则&gt;&gt;</span>
                        </p>
                        <p class="fline">
                            <img src="{{URL::asset('/')}}/images/commision.png" alt="">
                            <span>邀请好友观看视频或成单，拿佣金及返利。</span>
                            <a  class="r getcoin">我要佣金</a>
                        </p>
                        <p class="f12 color8a" style="color:#8a869e">
                            <span class="b">分享佣金：</span>
                            <span class="dis_coin ui-nowrap-multi"></span>
                        </p>
                        <div class="clearfix"></div>
                        <div class="tc pb1-5 more_icon" ><img src="{{URL::asset('/')}}/images/more_icon.png" alt="" style="width:1.33rem;"></div>
            </div>
            <!-- <div class="sharetomake none">
                <div class="actitle f16 fline color333  relative">
                    <span id="share_m"></span>
                    <h6 class="crash  absolute position1">赚取佣金拿现金，一键分享好友全搞定！</h6>
                    <samp id="colorrole"></samp>
                </div>
                 <div class="actitle f16 fline color333  relative">
                    <span id="share_f"></span>
                    <h6 class="crash  absolute position1">邀请好友观看视频或成单，拿佣金及返利。</h6>
                    <samp id="rolef"></samp>
                </div>
                 <div class=" f16 fline color999 sharecrash">
                     <samp style="position: relative;top:1.7rem;left:1rem;font-size:1.2rem">分享赚佣：</samp>
                     <div class="sharemoeny"></div>
                     <p id="arrow"></p>
                </div>
            </div> -->
            <!-- 活动录播视频 -->
            <div id="video" class="video none">
                    <div style="width:100%;height:2rem; "></div>
                    <div class="loginvideo"></div>
                    <ul class="videolist">
                      <!-- <li class="videoli fline relative">
                         <img class="absolute" src="{{URL::asset('/')}}/images/020700/m7.png" alt=""/>
                         <span id="circlebtn" class="absolute"><img src="{{URL::asset('/')}}/images/020700/m14.png" alt=""></span>
                         <span class="acttitle"><b>第十三届杭州商业特许经营连锁加盟展览会</b></span>
                         <span class="videotime">录制时间：04/30 09:00</span>
                         <span class="videodetail">视频描述：这里的直播控制单行……</span>
                         <div class="cleararrow"></div>
                      </li> -->
                    </ul>    
            </div>
            <!-- 活动介绍 -->
            <section class="bgwhite act_desp tline" id="actdescription">
            </section>
            <!-- 品牌展示 -->
            <section id="pinpai" class="brandcontain">
                <div class="brandtext f16 fline b">
                   相关品牌
                </div>
            </section>
            
            <!--热度-->
            <section class="hotness f14 mt1 color666" id="hotness">
                <div class="hotnum color333 fline b" id="hotnum">热度</div>
                <div class="seen fline">浏览<span id="seen" class="color999">22次</span></div>
                <div class="dianzan fline">点赞<span id="dianzan" class="color999">33次</span></div>
                <div class="plun fline ">评论<span id="plun" class="color999">43条</span></div>
                <div class="zhuan">转发<span id="zhuan" class="color999">234次</span></div>
            </section>
            <div class="act_intro pl1-33 fline bgwhite" style="margin-top:1.2rem;">     
                <div class="act_address" style="height: auto;">
                    <div class="time" id="aty_time">
                        <span class="time_icon_v5 mt2-25"></span>
                        <div class="infor fline">
                            <p id="act_time">12/14 10:00 - 15:00</p>
                            <p class="c8a">活动时间</p>
                            <span class="sj_icon mt2-25"></span>
                    
                        </div>
                    </div>
                    <div class="wjb" id="aty_hostcitys">
                        <span class="city_icon_v5"></span>
                        <div class="infor fline"><p id="citys">北京、上海、杭州、温州</p>
                            <p class="c8a">活动地点</p><span class="sj_icon"></span></div>
                    </div>
                    <div class="wjb wjbrk" id="aty_ticket">
                        <span class="wjb_icon_v5"></span>
                        <div class="infor fline"><p id="wjbNum">20元起</p>
                            <p id="tickettype" class="c8a">购买直播、现场门票</p><span class="sj_icon"></span></div>
                    </div>
                    <div class="wjb" id="aty_signs">
                        <span class="head_icon_li_v5"></span>
                        <div class="infor no-bottom"><p id="bmNum">共57人已报名</p>
                            <p class="c8a">报名人数</p><span class="sj_icon"></span></div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <!--赞-->
            <section class="hotness f14 mt1 color666" id="zancontain">
                <div class="hotnum fline color333 b">赞&nbsp;<em id="zan-number" class="c00a0ff "></em></div>
                <div class="headicon" id="zan-images">
                </div>
                <div class="moremig none" id="moremig">
                    <div class="fr color999 f12" id="morezanimg" data-showdown="1">更多 ∨</div>
                </div>
            </section>
            <!--留言评论-->
            <section id="comment" class="mt1 pl1-33 bgwhite fline" style='padding:0 0 0 1.333rem;'>
                <div class="commentnum f14 color333 b fline">评论&nbsp;<span id="commentnum" class="c00a0ff ">1029</span></div>
                <div id="thelist" class="bgfont">
                    <ul class="pr1-33" id="allComment" style="margin-top: 0;">

                    </ul>
                </div>
                <div id="pullUp" data-pagenow="1" style="display: none;">
                    <span class="pullUpLabel" style="display: none;"></span>
                </div>
                
            </section>
            <button class="getMore f12 c8a">点击加载更多</button>
            <!-- 评论框 -->
             <div class="commentback none" id="commentback">
                <div style="width: 100%;height:100%;position:absolute;bottom:15rem;left:0;" id="tapdiv"></div>
                <div class="textareacon">
                    <textarea class="f12" name="comment" id="comtextarea" cols="30" rows="10" style="resize: none;" placeholder="请输入评论内容"></textarea>
                    <button class="fr subcomment f16" id="subcomments">发表</button>
                </div>
            </div>
            <input type="hidden" data-src="" id="share_img">
            <div id="act_des" class="none" data-begintime="" data-collected="0"></div>
            <input type="hidden" data-maxid="0" data-minid="0" id="commentflag">
            <input type="hidden" id="sharemark">
        </section>

        <!--分享按钮-->
        <div class="fixed_btn weixin none" id="loadAppBtn">
            <span class="downsapp width60 fl" id="loadapp"><img src="{{URL::asset('/')}}/images/020700/downapp.png" alt=""></span>
            <span class="downsapp width40 f16 greenbc r" id="signnow">立即报名</span>
        </div>
        <!--浏览器打开提示-->
        <div class="safari none">
            <img src="{{URL::asset('/')}}/images/safari.png">
        </div>
    </section>
@stop
@section('endjs')
    <script src="{{URL::asset('/')}}/js/swiper.min.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/_v020700/activity_020700.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/myscrollLoading.js"></script>
    <script type='text/javascript'>
    //百度统计浏览量
     var _hmt = _hmt || [];
    (function() {
      var hm = document.createElement("script");
      hm.src = "https://hm.baidu.com/hm.js?8ccbe24f04075d8872631d15b0ec83fb";
      var s = document.getElementsByTagName("script")[0]; 
      s.parentNode.insertBefore(hm, s);
    })();
        //评论按钮颜色变化
            $('#comtextarea').on('keyup',function(){
                $('#subcomments').css('backgroundColor','#ea5520');
                if($('#comtextarea').val()==''){
                     $('#subcomments').css('backgroundColor','#999');
                }
            });
         // 点击灰框评论消失
            $(document).on('tap','#tapdiv',function(){
                $('#commentback').addClass('none');
            });
        //关闭分享机制提醒
             $(document).on('tap','.close_share',function(){
                $('.share').addClass('none');
             });
        //了解更多分享机制
            $(document).on('tap','.understand',function(){
                window.location.href=labUser.path+'webapp/protocol/moreshare/_v020500?pagetag=025-4';
            })
        //输入框
            $('#comtextarea').on('focus', function () {
                setTimeout(function () {
                    var c = window.document.body.scrollHeight;
                    window.scroll(0, c);
                }, 500);
                return false;
            });
       Zepto(function() {
        var args = getQueryStringArgs();
        var uid = args['uid'] || 0,
        id = args['id'];
        var urlPath = window.location.href;
        var shareFlag = urlPath.indexOf('is_share=1') > 0 ? true : false;
        function videos(id,uid){
            var param = {};
            param['id'] = id;
            param['uid'] = uid;
            var url = labUser.api_path + '/activity/detail/_v020700';
            ajaxRequest(param, url, function(data) {
                if (data.status) {  
                    var str=data.message.videos;
                    if(str.length>0){
                       $('#video').removeClass('none')
                    }
                    getVideos(data.message);    
                }
            });
        };
        videos(id,uid);
        function getVideos(result){
            var video = result.videos;
            $.each(video, function(i, item){
                var str = '';
                    str+='<li class="videoli fline relative" data-id="'+item.id+'">';
                    str+='<img class="absolute" src="' + item.image + '" alt=""/>';
                    str+='<span id="circlebtn" class="absolute"><img src="/images/020700/m14.png" alt=""></span>';
                    str+='<span class="acttitle" style="font-size:1.6rem"><b>'+item.subject+'</b></span>';
                    str+='<span class="videotime"style="font-size:1.2rem;color:#8a869e">录制时间：'+unix_to_datetime(item.created)+'</span>';
                    str+='<span class="videodetail" style="font-size:1.2rem;color:#8a869e">视频描述：'+item.description.substring(0,10)+'……'+'</span>';
                    str+='<div class="cleararrow"></div>';
                    str+='</li>';
                $('.videolist').append(str);
            });
        }     
          $(document).on('click','.videoli',function () {
            var ids = $(this).data('id');
            if(shareFlag){
                window.location.href=labUser.path+'webapp/vod/detail/_v020700?id='+ids+'&uid='+uid+'&pagetag=05-4&is_share=1';
            }else{
                window.location.href=labUser.path+'webapp/vod/detail/_v020700?id='+ids+'&uid='+uid+'&pagetag=05-4';
            }  
        }) 
          //提交留言方法；
                function liuyantijiao(id,uid){
                    var reg=/1[34578]\d{9}/;
                    var mobile=$('input[name="phone"]').val();
                    var consult=$('input[name="consult"]').val();
                    var name=$('input[name="realname"]').val();
                    var param={};
                        param['id'] = id;
                        param['uid']=uid;
                        param['mobile'] = mobile;
                        param['realname'] = name;
                        param['consult'] = consult;
                 var url = labUser.api_path + '/brand/message/_v020500';
                  ajaxRequest(param, url, function(data) {
                    if(data.status){
                        $('#brand-mes').addClass('none');
                        $('input[name="phone"]').val('');
                        $('input[name="consult"]').val('');
                        $('input[name="realname"]').val('');
                    }
                  })
                };
                $('.send-mes').on('click',function(){
                     var reg=/1[34578]\d{9}/;
                     var mobile=$('input[name="phone"]').val();
                    if(reg.test(mobile)){
                       var brandID=$('.chakan').attr('data-brand_id');
                       liuyantijiao(brandID,uid);
                       $('.fixed-bg').hide();
                       $('#brand-mes').hide();
                       tips('提交成功');
                    }else{
                         tips('信息请填写正确')
                    }
                });
            //通用函数；
                function getSubscribe(id,subscribe) {
                 if (isAndroid) {
                    javascript:myObject.getSubscribe(id,subscribe);
                   } else if (isiOS) {
                      var data = {
                    'id':id,
                    'subscribe':subscribe
                    };
                window.webkit.messageHandlers.getSubscribe.postMessage(data);
               }
              };
        //品牌收藏方法      
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
           $(document).on("click", ".shoucang", function(){
                        var subscribe = $(this).data("subscribe");
                        var ID = $(this).data("brand_id");
                    if(shareFlag){
                       tips('请先登录APP')
                     } else {
                        console.log(ID);
                        if($(this).hasClass('cang')) {
                            subscribe = 'do';
                        } else {
                            subscribe = 'undo';
                        }
                        collectbrand(ID,uid,subscribe); 
                     }       
                });

        //跳转分佣规则
            $('#colorrole').on('click', function () { 
            window.location.href = labUser.path + 'webapp/protocol/moreshare/_v020700';
             });
        //跳转直播详情
             $('#livephoto').on('click',function(){
                var ids = $(this).data('id');
               if(shareFlag){
                  window.location.href=labUser.path+'webapp/live/detail/_v020700?id='+ids+'&uid='+uid+'&pagetag=04-9&is_share=1';
               }else{
                window.location.href=labUser.path+'webapp/live/detail/_v020700?id='+ids+'&uid='+uid+'&pagetag=04-9';
               }
             })
            $('#arrow').on('click',function(){   
                        $('.sharemoeny>p').css('display','block');
                    });
            $('.sharemoeny').on('click',function(){
                $('.sharemoeny :not(:first-child)').css('display','none')
            })
            function tips(e) {
                 $('.tips').text(e).removeClass('none');
                setTimeout(function() {
                    $('.tips').addClass('none');
                }, 1500);

            }; 
    $('#rolef').on('click', function (){
           if(shareFlag){
                       tips('请先登录APP')
            }else{
                    showShare();
                    hotChange();   
                }
                    
            }); 
    $(document).on('click','#hotness',function(){
        $('#brand-mes').addClass('none');
    })
      
})//Zepto最外层

    </script>

@stop