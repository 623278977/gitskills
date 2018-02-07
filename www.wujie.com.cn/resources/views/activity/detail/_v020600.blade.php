@extends('layouts.default')
@section('css')
    <link rel="stylesheet" href="{{URL::asset('/')}}/css/swiper.min.css">
    <link href="{{URL::asset('/')}}/css/_v020600/actdetail.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/_v020600/actdetail_02.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/_v020600/activity.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
<section id="act_container" class="none"  >
     <div class="app_install fixed none" id="installapp">
            <i class="l">打开无界商圈APP，体验更多精彩内容 >></i>
            <span class="r" id="openapp"><img src="{{URL::asset('/')}}/images/020502/install_btn.png" alt=""></span>
            <div class="clearfix"></div>
     </div>
        <!--邀请好友-->
    <div class="app_install fixed none" id="yaoqing">
            <i class="l">邀请好友注册无界商圈，获得免费门票</i>
            <span class="r" id="yaoqingbtn"><img src="{{URL::asset('/')}}/images/020502/yaoqing.png" alt=""></span>
            <div class="clearfix"></div>
    </div>
    <div class="share pl1-33 pr1-33 a_share" id="share" style='background-color:rgba(255,90,0,0.7) '>
            <p class="f12 l">分享活动，立即获得100积分</p>
            <button class="ff5 l f12 understand"><img src="{{URL::asset('/')}}/images/020502/notice.png" alt="">了解分享规则介绍</button>
            <span class="fff f16 r close_share"><img src="{{URL::asset('/')}}/images/share_close.png" alt=""></span>
    </div>
    <section>
            <div class="swiper-container">
                <div class="swiper-wrapper"></div>
                <div class="swiper-pagination swiper-pagination-fraction"></div>
            </div>
    </section>
    <section id="act_intro" class="mt0" style="padding-bottom:;">
            <div class="actitle f16 fline color333"><span id="act_name" class="b">活动标题</span></div>
            <div class="act_intro pl1-33 fline bgwhite" style="padding-top:0;">     
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
                            <p class="c8a overNum">报名人数</p><span class="sj_icon"></span></div>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
    </section>
    <div id="beforeactivity" style="width:100%;height:1rem;background:#f2f2f2"></div>
    <article id="article"> 
           <ul class="article">
               <li id="actback"  style="color:#ff5a00;">活动回放</li>
               <li id="actdetial">活动详情</li>
           </ul>
    </article>
    <div id="nishui" style="width:100%;height:1rem;background:#f2f2f2"></div>
    <div id="woshui" style="width:100%;height:1rem;background:#f2f2f2;display:none"></div>
    <section class="bgwhite " id="sec_video">
                    <div class="ui-border-b text">相关视频</div>
                    <center>
                     <div class="videoss none" style="padding-top:0rem;">
                        <img id="novideo" class="none" src="{{URL::asset('/')}}/images/novideo.png" alt="" style="width: 13rem;display: block;margin: 0 auto;">
                    </div>
                    </center>
                    <ul class="more_video white-bg" id="relativevideo">
                    <!--   <li class="ui-border-t">
                            <div class="l video_img" style="margin-left:3% ">
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
    </section>
      <!--  <section class="relatedvideo" id="relatedvideo">
                <div class="text">相关视频</div>
                <div class="video border" data-id="">
                    <img src="{{URL::asset('/')}}/images/020600/video.png">
                    <div class="message">
                        <ul class="storename">
                            <li class="rightborder">LAWSON</li>
                            <li>罗森便利店</li>  
                        </ul>
                        <span>录制于2016-07-17</span>
                        <ul class="store">
                            <li>罗森便利店</li>
                            <li>品牌加盟</li>
                            <li>便利店</li>
                        </ul>
                    </div>
                </div>
           </section> -->
    <div id="bgip" style="width:100%;height:1rem;background:#f2f2f2"></div>
    <div id="hason" style="width:100%;height:1rem;background:#f2f2f2;display:none"></div>
    <div class="zixun" id="zixun">
            <div id="addbg" class="ui-border-b text">相关资讯</div>
           <!--  <div class="content">
                <div class="contenttitle">
                    <h5>新商业时代企业转型电商之路经典“武学”三部曲</h5>
                    <div class="contentbox">今天一出门就感觉乖乖的，在挤成鲨鱼罐头的地铁里，挤在我边上的人都尽量不和别人说话,哈哈哈哈哈哈哈加急急急急机安检机就安静安静家教机安检机哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈这样一来就可以避免非常糟糕的使其没那个发生呢个</div>
                    <div class="row"></div>
                    <div class="writer">
                        作者：无界商圈
                    </div>
                </div>
                <div class="img" style="display">
                    <img src="{{URL::asset('/')}}/images/020600/message.png">
                </div>
            </div>
            <div class="content nopictheight">
                <div class="contenttitle nopict">
                    <h5>新商业时代企业转型电商之路经典“武学”三部曲</h5>
                    <div class="contentbox">今天一出门就感觉乖乖的，在挤成鲨鱼罐头的地铁里，挤在我边上的人都尽量不和别人说话,这样一来就可以,
                        今天一出门就感觉乖乖的，在挤成鲨鱼罐头的地铁里，挤在我边上的人都尽量不和别人说话,这样一来就可以避免非常糟糕的使其没那个发生呢个
                    </div>
                    <div class="row"></div>
                    <div class="writer">
                        作者：无界商圈
                    </div>
                </div>
                <div class="img" style="display:none;">
                    <img src="images/v_020600/message.png">
                </div> -->
            </div>
    </div>
    <div id="actdetailone"  style="display:none">
             <!-- 活动介绍 -->
            <section class="bgwhite act_desp tline" id="actdescription"></section>
             <div  style="width:100%;height:1rem;background:#f2f2f2"></div>
            <!-- 品牌展示 -->
            <section id="pinpai" class="brandcontain">
                <div class="brandtext f16 fline b">
                   相关品牌
                </div>
            </section>
            <div id="bgbrand" style="width:100%;height:1rem;background:#f2f2f2"></div>
            <!--热度-->
            <section class="hotness f14 mt1 color666" id="hotness">
                <div class="hotnum color333 fline b" id="hotnum">热度</div>
                <div class="seen fline">浏览<span id="seen" class="color999">22次</span></div>
                <div class="dianzan fline">点赞<span id="dianzan" class="color999">33次</span></div>
                <div class="plun fline ">评论<span id="plun" class="color999">43条</span></div>
                <div class="zhuan">转发<span id="zhuan" class="color999">234次</span></div>
            </section>
            <div  style="width:100%;height:1rem;background:#f2f2f2"></div>
            <!--赞-->
            <section class="hotness f14 mt1 color666" id="zancontain">
                <div class="hotnum fline color333 b">赞&nbsp;<em id="zan-number" class="ff5 "></em></div>
                <div class="headicon" id="zan-images">
                </div>
                <div class="moremig none" id="moremig">
                    <div class="fr color999 f12" id="morezanimg" data-showdown="1">更多 ∨</div>
                </div>
            </section>
            <div  style="width:100%;height:1rem;background:#f2f2f2"></div>
            <!--留言评论-->
            <section id="comment" class="mt1 pl1-33 bgwhite " style='padding:0 0 0 1.333rem;'>
                <div class="commentnum f14 color333 b fline">评论&nbsp;<span id="commentnum" class="ff5 ">1029</span></div>
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
            <input type="hidden" data-src="" id="share_img"/>
            <div id="act_des" class="none" data-begintime="" data-collected="0"></div>
            <input type="hidden" data-maxid="0" data-minid="0" id="commentflag"/>
            <input type="hidden" id="sharemark"/>
    </div> 
        <div class="downzixun"></div>
        <!--分享按钮-->
        <div class="fixed_btn weixin none " id="loadAppBtn">
            <span class="downsapp width60 fl" id="loadapp"><img src="{{URL::asset('/')}}/images/020502/downapp.png" alt=""></span>
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
    <script type="text/javascript" src="{{URL::asset('/')}}/js/_v020600/actydetail_02.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/myscrollLoading.js"></script>
    <script type='text/javascript'>
    // 浏览量百度统计
    var _hmt = _hmt || [];
    (function() {
      var hm = document.createElement("script");
      hm.src = "https://hm.baidu.com/hm.js?8ccbe24f04075d8872631d15b0ec83fb";
      var s = document.getElementsByTagName("script")[0]; 
      s.parentNode.insertBefore(hm, s);
    })();
    //时间戳转化为分秒
    function unix_to_minsec(unix) {
    var newDate = new Date();
    newDate.setTime(unix * 1000);
    var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
    var s = newDate.getSeconds() < 10 ? '0' + newDate.getSeconds() : newDate.getSeconds();
    return m + ':' + s ;
     }
    $('#comtextarea').on('keyup',function(){
                $('#subcomments').css('backgroundColor','#ff5a00');
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
        var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
        function videos(id,uid){
            var param = {};
            param['id'] = id;
            param['uid'] = uid;
            var url = labUser.api_path + '/activity/detail/_v020600';
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
                        str+='<li class="ui-border-b videoLi" data-id="'+item.id+'">';
                        str+='<div class="l video_img" style="margin-left:1.33rem;">';
                        if (item.duration==''||item.duration==undefined||item.duration==0){
                            str+='';
                        }else{
                            str+='<p class="f14 videotime ui-border-radius"><span class="whitepoint"></span>'+unix_to_minsec(item.duration)+ '</p>';
                        }   
                        str+='<img src="' + item.image + '" alt="">';
                        str+='</div>';
                        str+='<div class="video_intro">';
                        str+='<p class="f16 mb02 text_black  no-wrap w80">' + item.subject + '</p>';
                        str+='<p class="f14 color999">录制于<span>' + item.created+ '</p>';
                        str+='<div class="f12 keyword">';
                        if(item.keywords==''||item.keywords==undefined||item.keywords.length==0){
                            str+='';
                        }else{
                             for (var i = 0; i < item.keywords.length; i++) {
                            str+='<span class="border-8a-radius ui-border-radius-8a">' + item.keywords[i] + '</span>';
                        }
                        };
                        str+='</div>';
                        str+='</div>';
                        str+='<div class="clearfix"></div>';
                        str+='</li>';
                        $('#relativevideo').append(str);
            });
            // console.log(videos.length);
            if (videos==''||videos==undefined||videos.length==0){
                 $('#sec_video').removeClass('bgwhite');
                 $('.videoss').removeClass('none');
                 $("#sec_video").attr("style","display:none");
                 $("#actdetailone").attr("style","display:block");
                 $("#actdetial").css("color","#ff5a00").siblings().css("color","#999");
                 $('#zixun').attr("style","display:none");
                 $("#bgip").attr("style","display:none");
                 $("#hason").css("display","block");
                 $("#nishui").attr("style","display:none");
                 $("#actback").on("click",function(){
                   $("#woshui").css("display","block"); 
                 });
                 $("#actdetial").on("click",function(){
                   $("#woshui").css("display","none"); 
                 });
                 
            }
        };
          $(document).on('click','.videoLi',function () {
            var ids = $(this).data('id');
            if(shareFlag){
                window.location.href=labUser.path+'webapp/vod/detail/_v020600?id='+ids+'&uid='+uid+'&pagetag=05-4&is_share=1';
            }else{
                window.location.href=labUser.path+'webapp/vod/detail/_v020600?id='+ids+'&uid='+uid+'&pagetag=05-4';
            }
            
        })
        //获取资讯数据
        function Zixun(id,uid){
            var param = {};
            param['id'] = id;
            param['uid'] = uid;
            var url = labUser.api_path + '/activity/detail/_v020600';
            ajaxRequest(param, url, function(data){
                if (data.status){   
                    getZixun(data.message);            
                }
            });
        };
        Zixun(id,uid);
        function getZixun(result){
            var news=result.news;
            $.each(news,function(i,item){
                var line='';
                var s='';
                line+='<div class="ui-border-b content" data-id="'+item.id+'">';
                line+='<div class="contenttitle">';
                line+='<h5>'+item.title.substring(0,26)+"..."+'</h5>';
                var s=removeHTMLTag(item.detail);
                line+=' <div class="contentbox">'+s.substring(0,60)+"..."+'</div>';
                line+='<div class="ui-border-b row"></div>';
                line+=' <div class="writer">作者:'+item.author+'</div>';
                line+=' <div class="img"><img src="' + item.logo + '" alt=""></div>';
                line+='</div>';
                line+='</div>';
                $('#zixun').append(line);
            });
           if(news==''||news==undefined||news.length==0){
             $("#zixun .text").attr("style","display:none");
           }
        }//资讯结尾
        $(document).on('click','.content',function(){
            var ids = $(this).data('id');
            if(shareFlag){
              window.location.href=labUser.path+'webapp/headline/detail/_v020600?id='+ids+'&uid='+uid+'&pagetag=02-4&is_share=1';   
            }else{
               window.location.href=labUser.path+'webapp/headline/detail/_v020600?id='+ids+'&uid='+uid+'&pagetag=02-4';  
            }
           
        });
      })//最外层 
    </script>
@stop