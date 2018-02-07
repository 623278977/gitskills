@extends('layouts.default')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/iscroll.css"/>
    <link href="{{URL::asset('/')}}/css/act_detail.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/brand.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/_v020700/vod.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section class="containerBox none" >
        <!--打开app-->
        <div class="install none" id="installapp">
            <p class="l">打开无界商圈APP，观看完整高清视频 >> </p>
            <span class="r" id="openapp"><img src="{{URL::asset('/')}}/images/020502/install_btn.png" alt=""></span>
            <div class="clearfix"></div>
        </div> 
        <!--视频分享-->
        <div class="share_video ">
            <div class="top_left">
                付费
            </div>
            <img src="{{URL::asset('/')}}/images/live.png" alt="">
            <p class="share_text f12">
                该视频为有偿观看，观看前请先支付费用
            </p>
            <button class="know_more" id="know_more">视频预览</button>
        </div>
        <div class="needpay f16 pl1-33 pr1-33 none">
            当前录制视频为单独付费 &nbsp;<span class="color-red"><em class="b" id="score_price">20</em><em class="f12">积分</em></span>&nbsp;&nbsp;/人 
            <a class="r tobuy">购买</a>
        </div>
        <div id="video_box"></div>
        <div class="gap"></div>
        <section class="videodetail_box" style="padding-bottom:4.5rem;">
        <!--基本信息、相关品牌、相关视频分栏-->     
            <nav class=" threeColumn fline column">
                <span class="org" type='basic_info'>基本信息</span>
                <span type='conmments'>评论(<em id='discuss_num' class="com_num"></em>)</span>
                <span type="rel_video">猜你喜欢</span>
                <span class="none" id="App">App观看</span>
            </nav>
        <!-- 基本信息 -->
            <div class="mb10 basic_info ">   
                <!-- 2.7新增分销赚佣 -->
               
                <div>
                <!-- 品牌信息 -->
                    <div class=" pl1-33  pb05 mb1-5 brand">
                        <p class=" f16 color333 fline b mb0">品牌信息</p>
                         <div class="brand_info " id='brand_info'>
                
                        </div>
                    </div>
                <!-- 视频信息 -->
                    <div class="intro pl1-33  pb05 " id="basicvideo_info">
                        <p class=" f16 color333 fline b">视频信息</p>
                        <div class=" f12 color666 pr1-33">
                            <div class="l video_img "><img src="" alt="" id="basic_videoimg"></div>
                            <div class="video_intro" id="basic_videoinfo">
                                <!-- <p class="f16 mb02">标题</p>
                                <p class="f12 color999">录制时间：<span>2016-11-12</span></p> -->
                            </div>
                            <div class="clearfix"></div>
                            <p style="margin-top: 1rem; height:1px;background: #f3f4f8;transform: scale(1,0.5);"></p>
                            <div class="f12 color999">
                                <p class="mt2 mb05">详情</p>
                                <div class="disVideo pb3 ">      
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <!-- 评论 -->
            <div class="conmments mt7-133 none">
                <div class="comment  fline">
                    <p class="f16 fline b">评论 <span class="com_num ff5 f14"></span><span class="publish  ff5 f14 r pr1-33" id="publish">发表评论</span></p>
                    <ul id='comment' class="pr1-33">
                         
                    </ul>
                </div>
                <button class="getMore f12 c8a">点击加载更多</button>
                 <div id="comment_btn" class="comment_btn"><button type="button" class="tl" style="width: 30rem;">我来说两句...</button><span class="uploadpic1"></span><i class="uploadpictext f12">发表图片</i></div>
            </div>
        <!-- 猜你喜欢 -->
            <div class="rel_video none">
            <!-- 品牌相关视频 -->
                <div class="more_video mb1-5 white-bg " id="brand_relvideo">    
                    <p class="f16 b fline pl1-33 recom">推荐理由：<span ></span>同场品牌招商会品牌推荐</p>
                   <!--  <div class="pl1-33">
                        <div class="l video_img ">
                            <p class="playlogo mb0"><img src="http://www.wjsq3.com//images/play.png" alt=""></p>
                            <img src="" alt="">
                        </div>
                        <div class="video_intro">
                            <p class="f16 mb02">标题</p>
                            <p class="f14 color999">录制于<span>2016-11-12</span></p>
                            <p class="mb0"></p>
                        </div>
                        <div class="clearfix"></div>
                    </div>   -->      
                </div>
            <!-- 同类热门品牌 -->
               <!--  <div class="">
                    <p class="f16 b fline  recom mb0 white-bg">推荐理由：同分类下热门品牌</p>
                    <div class="brand_info mb1-5 white-bg ">
                        <div class="fline brand-company pl1-33">
                            <img src="/images/livetips.png" alt="" class="company mr1-33 fl">
                            <div class="fl width70">
                                <em class="service f12 mr1">品牌名称</em>
                                <span class="f14 b">'品牌的名字</span>
                                <div class="brand-desc f12 color999 mb05 ui-nowrap-multi">品牌详情</div>
                                <p class="f12 mb05"><span class="c8a">投资额：</span> <span class="color-red">2.5 ~ 10万</span></p>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="video_bas  pl3-8 fline">
                            <div class="l video_img ">
                                <p class="playlogo mb0"><img src="http://www.wjsq3.com//images/play.png" alt=""></p>
                                <img src="/images/livetips.png" alt="">
                            </div>
                            <div class="width56 l" id="">
                                <p class="f16 ">标题</p>
                                <p class="f14 color999">录制时间：<span>2016-11-12</span></p>   
                            </div>
                            <img src="/images/jump.png" alt="" class="r jump">
                            <div class="clearfix"></div>
                        </div>
                        <div class="video_bas  pl3-8 ">
                            <div class="l video_img ">
                                <p class="playlogo mb0"><img src="http://www.wjsq3.com//images/play.png" alt=""></p>
                                <img src="/images/livetips.png" alt="">
                            </div>
                            <div class="width56 l" id="">
                                <p class="f16 ">标题</p>
                                <p class="f14 color999">录制时间：<span>2016-11-12</span></p>
                            </div>
                            <img src="/images/jump.png" alt="" class="r jump">
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    
                </div> -->
            <!-- 二级猜你喜欢 -->
                <div class="">
                    <p class="f16 b fline  recom mb0 white-bg">猜你喜欢</p>
                    <div class="brand_info mb1-5 white-bg " id="rel_like">
                    <!--     <div class="fline brand-company pl1-33">
                            <img src="/images/livetips.png" alt="" class="company mr1-33 fl">
                            <div class="fl width70">
                                <em class="service f12 mr1">品牌名称</em>
                                <span class="f14 b">'品牌的名字</span>
                                <div class="brand-desc f12 color999 mb05 ui-nowrap-multi">品牌详情</div>
                                <p class="f12 mb05"><span class="c8a">投资额：</span> <span class="color-red">2.5 ~ 10万</span></p>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="video_bas f12 color666 pr1-33 pl3-8 fline">
                            <div class="l video_img ">
                                <p class="playlogo mb0"><img src="http://www.wjsq3.com//images/play.png" alt=""></p>
                                <img src="/images/livetips.png" alt="">
                            </div>
                            <div class="video_intro" id="video_intro">
                                <p class="f16 ">标题</p>
                                <p class="f14 color999">录制时间：<span>2016-11-12</span></p>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                       -->
                    </div>
                </div>
            </div>
           
         </section>
       
         <!-- 视频相关信息用于分享出去 -->
         <input class="none" id="video_detail">
         <!-- 评论框 -->
         <div class="commentback none" id="commentback">
            <div style="width: 100%;height:100%;position:absolute;bottom:15rem;left:0;" id="tapdiv"></div>
            <div class="textareacon">
                <textarea class="f12" name="comment" id="comtextarea" cols="30" rows="10" style="resize: none;" placeholder="请输入评论内容"></textarea>
                <button class="fr subcomment f16" id="subcomments">发表</button>
            </div>
        </div>

        <!-- 底部按钮 -->
         <div class="brand-btns fixed width100  brand-np none brand-s " id="bottom_btn">
             <div class="btn fl width33 brand_collect"  >
                <span class="brand-collect-contact_27  brand-collect-contact" >  </span>      
            </div>
           <div class="btn fl width33 pt05" id="brand_award" data-fund="">
                <p class="tc color-red f16">领创业基金</p>
                <p class="tc color-yellow f16 " id="brand_fund" style="margin-top: -0.5rem">￥500</p>  
            </div>
            <div class="btn fl width33 pt05 bc_fe" id="brand_suggest">
                <p class="tc color-white">发送加盟意向</p>
                <p class="tc color-yellow f12">*获取更多资料</p>
            </div>
        </div>
        <!-- 分享出去 -->
        <div class="brand-btns fixed width100  brand-p  brand-s none" >
            <div class="btn fl width50 pt05 bc_fe" id="brand_suggest_share">
                <p class="tc color-white">发送加盟意向</p>
                <p class="tc color-yellow f12">*获取更多资料</p>
            </div>
            <div class="btn fl width50 pt05 tc" id="loadapp" style="line-height: 4rem;">
                <img src="{{URL::asset('/')}}/images/dock-logo2.png" alt="" style="width:2rem;height:2rem;vertical-align: sub;">
                <span class="c8a f16">下载APP</span>
            </div>
        </div>
        <!-- 公用-品牌发送加盟意向 -->
        <div class="brand-message fixed bgcolor none">
            <form action="">
                <p class="fline f14 margin0 ">
                    <label for=""> 姓名：</label>
                    <input type="text" placeholder="" id="realname">
                </p>
                <p class="f14 margin0  mb5">
                    <label for=""> 手机号：</label>
                    <input type="text" placeholder="" id="telnum"  onkeyup='this.value=this.value.replace(/\D/gi,"")'>
                </p>
                <p class="mt1-5">
                    <label for="" class="f14 color666">咨询：</label>
                    <textarea name="" id="will" class="f14 width80" placeholder="请输入您要咨询的事项，项目专员会与你取得联系"></textarea>
                </p>
                <a  class="btn f14 " id="btn" style='background-color:#ff5a00 '>提交</a>    
                <input type="reset" class="none share-reset" >      
            </form>
        </div>
        <input type="hidden" id="sharemark">
        <input type="hidden" id="share">
        <!-- 蒙层 -->
        <div class="fixed-bg none"></div>
        <div class="alert none">
            <p></p>
        </div>
         <!--浏览器打开提示-->
        <div class="safari none">
            <img src="{{URL::asset('/')}}/images/safari.png">
        </div>
        <div class="isFavorite"></div>
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
                        <p class="f14 tc color-white mt2">具体使用规则参考<a href="javascript:;" class="toFound" style="text-decoration: underline;">创业基金使用说明</a></p>
                    </div>
                </div>
                <div class="close absolute f20 tc" id="packet_close">
                    ×
                </div>
            </div>
        </div>

       
        <!-- <button id='col' onclick='favourite()'>收藏按钮</button> -->
    </section>
@stop
@section('endjs')
    <script src="https://qzonestyle.gtimg.cn/open/qcloud/video/h5/h5connect.js" charset="utf-8"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/_v020800/vod.js"></script>
    <script type="text/javascript">
        Zepto(function(){
            new FastClick(document.body);
        // Tab也切换
            //基本信息 
            $(document).on('tap','.column>span',function(){
                var type = $(this).attr('type');
                if(!($('#bottom_btn').hasClass('none'))){
                     if(type == 'conmments'){
                        $('.brand-np').hide();
                    }else{
                         $('.brand-np').show();
                    }
                }        
                $(this).addClass('org').siblings('span').removeClass('org');
                $('.'+type).removeClass('none').siblings('div').addClass('none');
                $('html').scrollTop('0');
                document.documentElement.scrollTop = 0;
                document.body.scrollTop =0;
                window.pageYOffset = 0;
            });
        // 获取参数，调用接口
            var shareFlag = (window.location.href).indexOf('is_share') > 0 ? true : false;

            var param = {
                "id": "<?php echo $id;?>",
                "uid": "<?php echo isset($user->uid) && $user->uid > 0 ? $user->uid : $uid;?>",
                "code":'<?php echo $code;?>',
                "section": 0,
                "commentType": 'Video',
                "commentid": '',
                "content": '',
                "upid": '',
                "nickname": labUser.nickname,
                "p_nickname": '',
                "pContent": '',
                "created_at": unix_to_datetime(new Date().getTime()),
                "likes": 0,
                "urlPath": window.location.href,
                "shareStr": 'is_share',
                "pageSize":5,
                "page":1

            };
            Video.vodDetail(param,shareFlag);
            Video.getComment(param,shareFlag);

        //upload pictures发表图文
            $('.uploadpic1,.uploadpictext').on('click', function() {
                uploadpic(param.id, 'video', false);
            });
            //仅文字
            $('.comment_btn>button').on('click', function() {
                uploadpic(param.id, 'video', true);
            });
    
        //了解详细规则
            $(document).on('click','#knowdetail',function(){
                window.location.href = labUser.path+'webapp/protocol/moreshare/_v020700';
            })
        //我要佣金按钮
            $(document).on('click','.getcoin',function(){
                showShare()
            })
       
         // 发表评论
            $(document).on('click','#publish',function(){
                if(shareFlag){
                    $('#commentback').removeClass('none');
                    $('#comtextarea').focus();
                    if($('#comtextarea').val()==''){
                         $('#subcomments').css('backgroundColor','#999');
                    }
                }else{
                    uploadpic(param.id,'Video',false);
                }
            });
            $(document).on('click ','#subcomments',function(){
                param.content=$('#comtextarea').val();
                console.log(param.content);
                param.page=1;
                if(shareFlag){
                    param.uid=0;
                }
                Video.addComment(param,shareFlag);
            })
        //评论按钮颜色变化
            $('#comtextarea').on('keyup',function(){
                $('#subcomments').css('backgroundColor','#ff5a00');
                if($('#comtextarea').val()==''){
                     $('#subcomments').css('backgroundColor','#999');
                }
            })
         // 点击灰框评论消失
            $(document).on('tap','#tapdiv',function(){
                $('#commentback').addClass('none');
            })
         //点击加载更多
            $(document).on('click','.getMore',function(){
                param.page++;
                console.log(param.page);
                Video.getComment(param,shareFlag);
            })
         // //点击意向加盟
         //     $(document).on('tap','#intent',function(){
         //        $('.brand-message').removeClass('none');
         //        $('.fixed-bg').removeClass('none');
         //     });
         //点击灰框提交加盟消失
             $(document).on('tap','.fixed-bg',function(){
                $('.brand-message').addClass('none');
                $('.fixed-bg').addClass('none');
             });
         //关闭分享机制提醒
             $(document).on('tap','.close_share',function(){
                $('.share').addClass('none');
             });
        //了解更多分享机制
            $(document).on('tap','.understand',function(){
                window.location.href=labUser.path+'webapp/protocol/moreshare/_v020700?pagetag=025-4';
            })
        //防止输入框被遮挡
            $('#comtextarea').on('focus', function () {
                setTimeout(function () {
                    var c = window.document.body.scrollHeight;
                    window.scroll(0, c);
                }, 500);
                return false;
            });

            //查看我的红包
            $(document).on('click','.toPacket',function () {
                toPacket();
            });
            //创业基金使用说明
            $(document).on('click','.toFound',function () {
                window.location.href= labUser.path + 'webapp/protocol/venture/_v020500?pagetag=025-3';
                return false;
            });

            //创业基金的关闭按钮
            $(document).on('click tap', '#packet_close', function() {
                    $('.brand-packet').removeClass('a-bouncein').addClass('a-bounceout');
                    $('.fixed-bg').addClass('none');
                    setTimeout(function() {
                        $('.brand-packet').removeClass('a-bounceout').addClass('none');
                    }, 1000);
                });
        });
    </script>
    <script type='text/javascript'>
         /**app调用web方法****/
        //分享
            function showShare() {
                var title = $('#video_detail').attr('title');//点播的标题            
                var img = $('#video_detail').attr('data_img').replace(/https:/g, 'http:');
                var header = '点播';
                var des=removeHTMLTag($('#video_detail').attr('data_des')).replace(/&nbsp;/g,'');
                var content = delHtmlTag(cutString(des, 18));//点播的描述
                var type='video';
                var id=$('#sharemark').data('type_id');
                var url = window.location.href;
                 shareOut(title, url, img, header, content,'','','','video','','','share','video',id);     
                
            }; 
         //刷新
        function reload() {
            location.reload();
        };
        function delHtmlTag(str){
            return str.replace(/<{FNXX=]+>/g,"");//去掉所有的html标记
        }
        //收藏/取消收藏
        function favourite() {
            var isFavorite = $(".isFavorite").attr("value");
            var id = <?php echo $id;?>;
            if (isFavorite == "1") {
                setFavourite('0');
                isFavorite = 0;
                getVideoFavorite(id, isFavorite);
            } else if (isFavorite == "0") {
                setFavourite('1');
                isFavorite = 1;
                getVideoFavorite(id, isFavorite);
            }
            getCollect(id, "video", isFavorite);
        };

        //针对安卓，ios另写了方法
        function getVideoFavorite(id, isFavorite) {
            if (isAndroid) {
                javascript:myObject.getVideoFavorite(id, isFavorite);
            } 
            
        }

        function toPacket() {
            if (isAndroid) {
                javascript:myObject.toPacket();
            } else if (isiOS) {
                var data = {
                };
                window.webkit.messageHandlers.toPacket.postMessage(data);
            }
        }

    </script>

@stop