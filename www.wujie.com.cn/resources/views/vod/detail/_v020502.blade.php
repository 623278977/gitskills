@extends('layouts.default')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/iscroll.css"/>
    <link href="{{URL::asset('/')}}/css/act_detail.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/_v020500/vod.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section class="containerBox none" >
        <!-- <div class="videotoptip f12">本视频为专版收费视频，建议购买专版会员，享受更多优惠</div> -->
        <!--打开app-->
        <div class="install " id="installapp">
            <p class="l">打开无界商圈APP，观看完整高清视频 >> </p>
            <span class="r" id="openapp"><img src="{{URL::asset('/')}}/images/020502/install_btn.png" alt=""></span>
            <div class="clearfix"></div>
        </div>
        <div class="share pl1-33 pr1-33 " id="share" style='background-color:rgba(255,90,0,0.7) '>
            <p class="f12 l">分享视频，立即获得100积分</p>
            <button class="ff5 l f12 understand"><img src="{{URL::asset('/')}}/images/020502/notice.png" alt="">了解分享规则介绍</button>
            <span class="fff f16 r close_share"><img src="{{URL::asset('/')}}/images/share_close.png" alt=""></span>
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
        <div id="video_box"></div>
        <div class="gap"></div>
        <section class="videodetail_box">
            <!--基本信息、相关品牌、相关视频分栏-->
            
            <nav class=" threeColumn fline column">
                <span class="org" type='basic_info'>基本信息</span>
                <span type='rel_brand'>相关品牌 </span>
                <span type="rel_video">相关视频 (<em id='video_num'></em>)</span>
            </nav>
                 <div class="basic_info ">
                <div class="act pl1-33 pr1-33 pb05">
               
                    <img src="{{URL::asset('/')}}/images/rightjt.png" alt="">
                </div>
                <div class="guest pl1-33  pb05">
                    <p class=" f16 color333 fline b">相关嘉宾</p>
                    
                </div>
                <div class="intro pl1-33  pb05 ">
                    <p class=" f16 color333 fline b">视频概况</p>
                    <div class="video_bas f12 color666 pr1-33">
                            
                    </div>
                </div>
                <div class="comment  fline">
                    <p class="f16 fline b">评论 <span class="com_num ff5 f14">1024</span><span class="publish  ff5 f14 r pr1-33" id="publish">发表评论</span></p>
                    <ul id='comment' class="pr1-33">
                        
                    </ul>
                </div>
                <button class="getMore f12 c8a">点击加载更多</button>
            </div>
            <div class="rel_brand none">
                
            </div>
            <div class="rel_video none">
                <ul class="more_video">
                    <li class="fline">
                        <div class="l video_img "><img src="" alt=""></div>
                        <div class="video_intro">
                            <p class="f16 mb02">标题</p>
                            <p class="f14 color999">录制于<span>2016-11-12</span></p>
                            <p class="mb0"><a class="tags-key c8a">罗宾汉</a><a class="tags-key c8a">便利店</a><a class="tags-key c8a">品牌加盟</a></p>
                        </div>
                        <div class="clearfix"></div>
                    </li>
                </ul>
            </div>
           
         </section>
         <button class="buy f16" id="loadapp">购买本视频</button>
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
            </form>
        </div>
        <input type="hidden" id="sharemark">
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
        <!-- <button id='col' onclick='favourite()'>收藏按钮</button> -->
    </section>
@stop
@section('endjs')
    <script src="https://qzonestyle.gtimg.cn/open/qcloud/video/h5/h5connect.js" charset="utf-8"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/_v020500/vod_02.js?v=01091139"></script>
    <script type="text/javascript">
        Zepto(function(){
            new FastClick(document.body);
        // Tab也切换
            $(document).on('tap','.column>span[type="basic_info"]',function(){
                $(this).addClass('org').siblings('span').removeClass('org');
                $('.basic_info').removeClass('none').siblings('div').addClass('none');
                // $('html').scrollTop('0');
                // document.documentElement.scrollTop = 0;
                document.body.scrollTop =0;
                window.pageYOffset = 0;
            
            });
            $(document).on('tap','.column>span[type="rel_brand"]',function(){
                $(this).addClass('org').siblings('span').removeClass('org');
                $('.rel_brand').removeClass('none').siblings('div').addClass('none');
                // $('html').scrollTop('0');
                // document.documentElement.scrollTop = 0;
                window.pageYOffset = 0;
                document.body.scrollTop =0;
                
            });
            $(document).on('tap','.column>span[type="rel_video"]',function(){
                $(this).addClass('org').siblings('span').removeClass('org');
                $('.rel_video').removeClass('none').siblings('div').addClass('none');
               // $('html').scrollTop('0');
               // document.documentElement.scrollTop = 0;
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
       
         // 发表评论
            $(document).on('click','#publish',function(){
                // uploadpic(param.id,'Video',true);
                $('#commentback').removeClass('none');
                $('#comtextarea').focus();
                if($('#comtextarea').val()==''){
                     $('#subcomments').css('backgroundColor','#999');
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
         //点击意向加盟
             $(document).on('tap','#intent',function(){
                $('.brand-message').removeClass('none');
                $('.fixed-bg').removeClass('none');
             });
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
                window.location.href=labUser.path+'webapp/protocol/moreshare/_v020502?pagetag=025-4';
            })
        //防止输入框被遮挡
            $('#comtextarea').on('focus', function () {
                setTimeout(function () {
                    var c = window.document.body.scrollHeight;
                    window.scroll(0, c);
                }, 500);
                return false;
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
                var share_mark=$('#sharemark').data('mark');
                var relation_id=$('#sharemark').data('code');
                var id=$('#sharemark').data('type_id');
                var url = window.location.href+'&share_mark='+share_mark;
                var p_url=labUser.api_path+'/index/code/_v020500';
                ajaxRequest({},p_url,function(data){
                    if(data.status){
                        var code=data.message;
                        url+= '&code=' +code;
                        if($('#share').data('reward')==1){   
                            shareOut(title, url, img, header, content,'','','',type,share_mark,code,'share','video',id);
                        }else{
                            shareOut(title, url, img, header, content,'','','','','','','','','');
                        }
                        
                    }
                    
                })
                
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
            // else if (isiOS) {
            //     var data = {
            //         'id':id,
            //         'isFavorite':isFavorite
            //     };
            //     window.webkit.messageHandlers.getVideoFavorite.postMessage(data);
            // }
        }
    </script>

@stop