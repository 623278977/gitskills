
@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/dist/swiper.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/brand.css" rel="stylesheet" type="text/css"/>
<style>

    #swiper-container1{
        overflow: auto;
    }
    #swiper-container2{
        width:100%;
        overflow: hidden;
        position: relative;
    }
    .swiper-pagination{
        color:#fff;
        text-align: right;
        padding-right:1.33rem;
    }
    .swiper-slide{
        overflow: auto;
        -webkit-overflow-scrolling: touch;
    }
</style>
@stop
@section('beforejs')
   <script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan class='hide' id='cnzz_stat_icon_1261401820'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s95.cnzz.com/z_stat.php%3Fid%3D1261401820' type='text/javascript'%3E%3C/script%3E"));
   var args = getQueryStringArgs(),
        uid = args['uid'] || 0,
        id = args['id'],
        urlPath = window.location.href;
    var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
    var shareUrl = urlPath.indexOf('is_share') > 0 ? '&is_share=1' : '';
   if(!(isiOS||isAndroid)){
        window.location.href = labUser.path + 'webapp/brand/pc/_v020700?id='+id+'&uid='+uid+shareUrl;
   }
   </script>
@stop
@section('main')
    <section id="brand_detail" class="bgcolor none">
        <!--安装app-->
        <div class="install-app install-app2 none" id="installapp">
            <img src="{{URL::asset('/')}}/images/dock-logo2.png" alt="">
            <div class="fl pl1">
                <span>无界商圈</span><br>
                <span>用无界商圈找无限商机</span>
            </div>
            <a href="javascript:;" class="install-close f24">×</a>
            <a href="javascript:;" class="install-open" id="openapp">立即开启</a>
        </div>
        <div id="wrapper">
            <div  id="swiper-container1">
                <div class="swiper-wrapper" id="wrapper1">
                    <!-- 品牌详情 -->
                    <section  class="swiper-slide pb8" id="swiper-1">
                        <!-- 第一部分，品牌大图和名称 -->
                        <div class="brand-head white-bg mb1-5 " >
                            <!-- 品牌的轮播图 -->
                            <div  id="swiper-container2">
                                <div class="swiper-wrapper swiper-brand">
                                 
                                </div>
                                <div class="swiper-pagination swiper-pagination-fraction"></div>
                            </div>
                            <div class="name_intro">
                                <span id="brand_name" data-mark="" data-code="" class="b f16 brand_name"></span>
                                 <dl >
                                    <dt>转发</dt>
                                    <dd class="zhuan"></dd>
                                </dl>
                                 <dl class="mr3">
                                    <dt>收藏</dt>
                                    <dd class="fav"></dd>
                                </dl>
                                <dl class="mr3">
                                    <dt>浏览</dt>
                                    <dd class="view"></dd>
                                </dl>  
                               
                            </div>
                        </div>
                        <!-- 第二部分，基本信息 -->
                        <div class="brand-info white-bg mb1-5 ">
                            <div class="brand-pl">
                                <div class="info ">
                                    <div class="lh45 none" id="brand_sort2">
                                        <span class="fl f16  ">标语</span> <span class="fr f14  color8a pr1-33" id="category_name2"></span>
                                    </div>
                                    <div class="lh45" id="brand_sort">
                                        <span class="fl f16  ">分类</span> <span class="fr f14  color8a pr1-33" id="category_name"></span>
                                    </div>
                                    <div class="lh45">
                                        <span class="fl f16 " >启动资金</span> 
                                        <div>
                                            <span class="fr f14  color8a pr1-33 brand-info-start"><em class="tr f14" id="brand_investment"></em> <em class="color-yellow f12 fr">*费用以实际为准</em></span>
                                        </div>
                                    </div>
                                    <div class="lh45">
                                        <span class="fl f16  ">主营产品</span> <span class="fr f14  color8a pr1-33" id="brand_products"></span>
                                    </div>
                                    <div class="lh45 ">
                                        <span class="fl f16 " id="brand_num">店铺数量</span> 
                                         <div>
                                            <span class="fr f14  color8a pr1-33 brand-info-start" style="margin-top:0.4rem;"><em class="tr f14">中国大陆地区<strong class="f14 color-red" id="brand_shops"> </strong>家门店</em> <em class="color8a f12 tr"  ><span id="brand_click" class="none">   </span></em></span>
                                        </div>
                                    </div>
                                    <div class="lh45 none">
                                        <span class="fl f16  ">分类</span> <span class="fr f14  color8a pr1-33">生活便利</span>
                                    </div>
                                </div>
                            </div>
                            <div class="fline"> </div>
                            <div class="brand-concern lh45 relative none" id="brand_asks"> 
                                <a href="javascript:;" class="fl blocks width50 tc color999 f14 brand-share-ask" data-type="discount">咨询加盟优惠</a>
                                <span class="thin"></span>
                                <a href="javascript:;" class="fl blocks width50 tc color999 f14 brand-share-ask" data-type="floor_price">咨询加盟底价</a>
                                <div class="clearfix">  </div>
                            </div>
                        </div>  
                        <!-- 第三部分，品牌标签 -->
                        <div class="brand-info white-bg mb1-5" id="brand_tag_none">
                            <div class="fline" id="tag_line" >
                                <span class="tleft f16w lh45 pl1-33">品牌标签</span>
                            </div>
                            <ul class="bgcolor brand-tags" id="brand_tags">     
                            </ul>
                            <div class="clearfix"></div>
                        </div>
                        <!-- 2.7新增分销赚佣 -->
                        <!-- <div class="distribution white-bg pl1-33 mb1-5 none" id="distribution">
                            <p class="fline">
                                <img src="{{URL::asset('/')}}/images/cash.png" alt="">
                                <span>赚取佣金拿现金，分享邀请好友全搞定!</span><span class="f12 color666 r" id="knowdetail" style='margin-top:0.1rem'>了解详细规则&gt;&gt;</span>
                            </p>
                            <p class="fline" id="creat_fund">
                               <img src="{{URL::asset('/')}}/images/coin.png" alt="">
                                通过平台加盟品牌，送你 <em class="colorf4">500元</em> 创业基金
                                <a class="r" id="join">立即加盟</a>
                            </p>
                            <p class="fline">
                                <img src="{{URL::asset('/')}}/images/commision.png" alt="">
                                <span>邀请好友观看视频或成单，拿佣金及返利。</span>
                                <a  class="r getcoin">我要佣金</a>
                            </p>
                            <p class="f12 color8a">
                                <span class="b">分享佣金：</span>
                                <span class="dis_coin ">每次转发奖励<em class="colorf4">50积分</em> ，最多得得 <em class="colorf4">50积分</em>；转发后每产生一次阅读，即送<em class="colorf4"> 10积分</em>，最多可得 <em class="colorf4">1000</em>积分每产生一</span>
                            </p>
                            <div class="clearfix"></div>
                            <div class="tc pb1-5 more_icon" ><img src="{{URL::asset('/')}}/images/more_icon.png" alt="" style="width:1.33rem;"></div>
                        </div> -->
                        <!-- 2.8 新增 评价 -->
                        <div class="brand-info white-bg mb1-5">
                           <div class="brand-pl  relative" id="brand_judge">
                                <div class=" fline lh45" >
                                    <span class="tleft f16w ">评价</span><span class="color666 f12 ml05">(2)</span>
                                </div>
                                <div>
                                    <p class="mt1-33">
                                        <img src="" alt="" class="judger_head mr1-33"><span class="f16">姓名</span>
                                    </p>
                                    <p class="f12 color8a">啊撒旦法法是的</p>
                                </div>      
                                <div class="tf fline tc lh45 ">
                                    <button class='toAlljudge'>查看全部评价</button>
                                </div> 
                            </div>
                        </div>
                        <!-- 第四部分，项目问答 -->
                        <div class="brand-info white-bg mb1-5" id="brand_question">
                            <div class="brand-pl  relative">
                                <div class=" fline lh45" id="brand_toquestion">
                                    <span class="tleft f16w ">项目问答</span><span class="sj_icon mt1-5"></span>
                                </div>      
                                <div class="fline lh45 ques-asks">
                                    <em class="brand-ask f12 fl">问</em><span class="f16 color333 no-wrap fl width80" id="brand_ques"></span>
                                </div>  
                                <div class="clearfix"></div>
                                <div class="fline-none lh45 ques-asks">
                                    <span class="brand-answer f12 fl">答</span><span class="f14 color8a no-wrap fl width80" id="brand_ans"></span>
                                </div>   
                            </div>
                            <div class="tf fline tc lh45 ">
                                    <button class='my-ques '>我要提问</button>
                            </div>  
                        </div>
                        <!-- 第五部分，公司的名片 -->
                        <div class="white-bg pl1-33 pb1-33 mb1-5 brand-company-h none">
                            <div class="fline  company-head" >
                                <span class="tleft f16w ">公司详情</span>
                            </div>
                            <div class="pr1-33 pt1-2">
                                <img src="" alt="" class="company mr1-33 fl" id="brand_logo">
                                <div class="fl width70">
                                    <em class="prove f12 mr1" id="brand_auth">诚信认证</em><span class="f16w" id="brand_company"></span>
                                    <div class="brand-address f14 color999" id="brand_company_add">
                                        
                                    </div>
                                    <a href="javascript:;" id="company_details" class="btn-detail f14">
                                        查看公司详情
                                    </a>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            
                        </div>
                        <!-- 第六部分，相关活动展示（2.7新增） -->
                        <div class="white-bg rel_activity none">
                            <div class=" fline ml1-33" >
                                <span class="tleft f16w lh45 ">相关活动</span>
                            </div>
                            <div class="brand_act">
                            </div>
                            
                        </div>
                        <!-- 分享出去才会出现的品牌推荐 -->
                        <div id="brand_more_share" class="none ">   
                            <div class="brand-info white-bg pl1-33" id="brand_brands">
                                <div class=" pl1-33 fline"style="margin-bottom: 1px">
                                    <span class="tleft f16w lh45 ">同类推荐</span>
                                </div>
                            </div>
                        </div>
                        <!-- <div style="height: 15rem;"></div> -->
                    </section>
                    <section class="swiper-slide pb8" id='swiper-2'>
                         <div class="white-bg mb1-33 ">
                            <div class="name_intro fline">
                                <span  data-mark="" data-code="" class="b f16 brand_name"></span>
                                 <dl >
                                    <dt>转发</dt>
                                    <dd class="zhuan"></dd>
                                </dl>
                                 <dl class="mr3">
                                    <dt>收藏</dt>
                                    <dd class="fav"></dd>
                                </dl>
                                <dl class="mr3">
                                    <dt>浏览</dt>
                                    <dd class="view"></dd>
                                </dl>  
                            </div>
                            <div class="tc pb1-5 upback pt1-5" id="backTodetail">
                                <img src="{{URL::asset('/')}}/images/upback.png" alt="" style="width:1.33rem;">
                            </div>
                        </div> 
                        <div class="brand_detail ">
                                <!-- 图文详情 -->
                                <div class=" mb1-33 white-bg pl1-33">
                                    <p class="mb1-33  f16 b fline ">图文详情</p>
                                    <div class="pic_text pr1-33 color666 pb1-33 f12"></div>
                                </div>
                                <!-- 项目介绍 -->
                                <div class=" mb1 white-bg pl1-33">
                                    <p class="mb1-33  f16 b fline">加盟简介</p>
                                    <div class="join_intro pr1-33 color666 pb1-33 f12" id='brand_j_1'> </div>
                                </div>
                                <div class=" mb1 white-bg pl1-33">
                                    <p class="mb1-33  f16 b fline">加盟优势</p>
                                    <div class="join_adv pr1-33 color666 pb1-33 f12" id='brand_j_2'> </div>                    
                                    
                                </div>
                                <div class="mb1-33 white-bg pl1-33">
                                    <p class="mb1-33  f16 b fline">加盟条件</p>
                                    <div class="join_term pr1-33 color666 pb1-33  f12" id='brand_j_3'></div>
                                     
                                </div>
                                 <div class="mb1-33 white-bg pl1-33">
                                    <p class="mb1-33  f16 b fline">产品图片</p>
                                    <div class="product_imgs" id="brand_images">
                                        
                                    </div>
                                </div>
                        </div>
                    </section>
                    <section class="swiper-slide pb8" id="brand_video" >
                      <div style="min-height:101%">
                        <div class="white-bg mb1-33">
                            <div class="name_intro fline">
                                <span  data-mark="" data-code="" class="b f16 brand_name"></span>
                                 <dl >
                                    <dt>转发</dt>
                                    <dd class="zhuan">200</dd>
                                </dl>
                                 <dl class="mr3">
                                    <dt>收藏</dt>
                                    <dd class="fav">1000</dd>
                                </dl>
                                <dl class="mr3">
                                    <dt>浏览</dt>
                                    <dd class="view">1000</dd>
                                </dl>    
                            </div>
                            <div class="tc pb1-5  pt1-5" id="backTomore">
                                <img src="{{URL::asset('/')}}/images/upback.png" alt="" style="width:1.33rem;">
                            </div>
                        </div>
                        <div class="videoss none" style="padding-top:10rem;">
                             <img id="novideo"  src="{{URL::asset('/')}}/images/novideo.png" alt="" style="width: 13rem;display: block;margin: 0 auto;">
                        </div>
                            <!-- <div class="brand-title f16">相关视频</div> -->
                            <ul class="more_video white-bg pl1-33" id="relativevideo">
                                <!-- <li class="ui-border-t">
                                    <div class="l video_img">
                                        <p class="playlogo "><img src="{{URL::asset('/')}}/images/play.png" alt=""></p>
                                        <img src="{{URL::asset('/')}}/images/livetips.png" alt="">
                                    </div>
                                    <div class="video_intro ">
                                        <p class="f16 mb0 h45">第十三届杭州商业特许经营连锁加盟展览会</p>
                                        <p class="f12 mb0 color8a">录制时间：<span>04/09 9：00</span></p>
                                        <div class="f12 video_dis color8a">视频描述：<span>这里的描述控制为但这里的描述控制为但这里的描述控制为但这里的描述控制为但行</span></div>
                                    </div>
                                    <div class="clearfix"></div>
                                </li> -->
                            </ul>
                            <div class="tc no-data none">
                                <p class="color999 f12 mt3-5">没有更多数据咯</p>
                                <p class="mt3-5 mb10"><a class="color-orange f12 " href="javascript:void(0)" id='moreVideo'>想要了解更多精彩视频？立即前往查看吧！</a></p>
                            </div>
                      </div>
                    </section>
                </div>
            </div>
         </div>
    </section>
    <section>
        <!-- 公用-底部按钮 -->
        <div class="brand-btns fixed width100 none brand-p brand-s" id="brand_btns_app">
            <div class="btn fl width50 brand_collect pt05" id="brand_award" data-fund="">
                <p class="tc color-red f16">领创业基金</p>
                <p class="tc color-yellow f16 brand_fund" style="margin-top:-0.5rem">￥500</p>     
            </div>
            <div class="btn fl width50 pt05" id="brand_suggest">
                <p class="tc color-white f16">客服咨询</p>
                <p class="tc color-yellow f12">为您匹配经纪人进行业务服务</p>
            </div>
        </div>
        <div class="brand-btns fixed width100 none brand-np  brand-s" id="brand_btns_app ">
            <div class="btn fl pt05 width100" id="brand_suggest">
                <p class="tc color-white f16">客服咨询</p>
                <p class="tc color-yellow f12">为您匹配经纪人进行业务服务</p>
            </div>
        </div>
        <!-- 公用-分享出去的底部按钮 -->
        <div class="brand-btns fixed width100 none " id="brand_btns_share">
            <div class="btn fl width50 tc color-red lh45 brand-share-ask" data-type="data">获取更多资料</div>
            <div class="btn fl width50 tc color-white bg-red lh45"><a href=" tel:4000110061" class="blocks color-white">电话咨询</a></div>
        </div>
        <!-- 公用-发送加盟意向 -->
        <div class="brand-message brand-message2 fixed bgcolor none " id="brand-mes" style="top:0">
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
        <!-- 提问框 -->
        <div class="commentback none" id="commentback">
            <div style="width: 100%;height:100%;position:absolute;bottom:15rem;left:0;" id="tapdiv"></div>
            <div class="textareacon">
                <textarea class="f12" name="comment" id="comtextarea" cols="30" rows="10" maxlength="150" style="resize: none;"
                          placeholder="请输入5-150字的项目问题，请尽量描述"></textarea>
                <button class="fr subcomment f16" id="subcomments" >提问</button>
            </div>
        </div>
        <!-- 公用-分享出去的发送加盟意向 -->
        <div class="brand-message brand-message2 fixed bgcolor  brand-message-share none" style="bottom:0">
            <div class="f16 color-blue pl1-33 mt1 mb1 color-orange" >如果您对该项目感兴趣，欢迎给企业留言</div>
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
                        <p class="f14 tc color-white mt2">具体使用规则参考<a href="javascript:;" class="toFound" style="text-decoration: underline;">创业基金使用说明</a></p>
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
        <!--浏览器打开提示-->
        <div class="safari none">
            <img src="{{URL::asset('/')}}/images/safari.png">
        </div>
        <div id="morebox" style="opacity: 0">释放查看更多</div>
    </section>
@stop

@section('endjs')
    <script src="{{URL::asset('/')}}/js/dist/swiper-3.4.2.min.js"></script>
   <!--  <script src="{{URL::asset('/')}}/js/dist/finger-mover.min.js"></script> -->
    <!-- <script src="{{URL::asset('/')}}/js/dist/simulation-scroll-y.min.js"></script> -->
	<script src="{{URL::asset('/')}}/js/_v020800/brand.js"></script>
    <script>  
            var shareFlag = window.location.href.indexOf('is_share') > 0 ? true : false;  
            var morebox = $('#morebox');
            //改变移动端标题
                function changeBrandTitle(index) {
                    if (isAndroid) {
                        javascript:myObject.changeBrandTitle(index);
                    } 
                    else if (isiOS) {
                        var data = {
                            'index':index
                        }
                        window.webkit.messageHandlers.changeBrandTitle.postMessage(data);
                    }
                }
            //点击移动端改变相应页面
                function changeBrandPage(index){
                    mySwiper.slideTo(index,200,false);
                }

                $(document).on('click','#backTodetail',function(){
                   $('#swiper-1').scrollTop(0);
                   // window.scroll(0,0);
                    mySwiper.slideTo(0,200,false);
                    if(!shareFlag){
                        changeBrandTitle(0);
                    };
                })
                $(document).on('click','#backTomore',function(){
                   $('#swiper-2').scrollTop(0);
                    // window.scroll(0,0);
                    mySwiper.slideTo(1,200,false);
                    if(!shareFlag){
                        changeBrandTitle(1);
                    };
                });
                 function getScrollTop() {
                    //滚动条在Y轴上的滚动距离
                    var scrollTop = 0, bodyScrollTop = 0, documentScrollTop = 0;
                    if (document.body) {
                        bodyScrollTop = document.body.scrollTop;
                    }
                    if (document.documentElement) {
                        documentScrollTop = document.documentElement.scrollTop;
                    }
                    scrollTop = (bodyScrollTop - documentScrollTop > 0) ? bodyScrollTop : documentScrollTop;
                    return scrollTop;
                }

                //浏览器视口的高度
                function getWindowHeight() {
                    var windowHeight = 0;
                    if (document.compatMode == "CSS1Compat") {
                        windowHeight = document.documentElement.clientHeight;
                    } else {
                        windowHeight = document.body.clientHeight;
                    }
                    return windowHeight;
                }
               
               $('#swiper-container1').css('height',getWindowHeight());//解决各页面高度不一致
                //文档的总高度
                function getScrollHeight() {
                    var scrollHeight = 0, bodyScrollHeight = 0, documentScrollHeight = 0;
                    if (document.body) {
                        bodyScrollHeight = document.body.scrollHeight;
                    }
                    if (document.documentElement) {
                        documentScrollHeight = document.documentElement.scrollHeight;
                    }
                    scrollHeight = (bodyScrollHeight - documentScrollHeight > 0) ? bodyScrollHeight : documentScrollHeight;
                    return scrollHeight;
                }
          
                //函数内判断，距离底部50px的时候则进行数据加载
                
                    var start,delta;
                    $(document).on('touchstart',function(e){
                         var touches = e.touches[0];
                        start = { 
                            x: touches.pageX, // 横坐标
                            y: touches.pageY  // 纵坐标
                        };
                    });
                    $(document).on('touchmove',function(e){

                         var touches = e.touches[0];
                          delta = {
                                 x: touches.pageX - start.x,
                                 y: touches.pageY - start.y
                            };
                        if (Math.abs(delta.x) > Math.abs(delta.y)) {
                            event.preventDefault();
                        }else if(getScrollTop() >50 && !(mySwiper.isEnd)){
                            if(-(delta.y/30) > 4.5){
                                morebox.css({'bottom':'4.5rem','opacity':1});
                            }else{
                                morebox.css('bottom',(-delta.y/30+'rem'));
                                morebox.css('opacity',-1/4.5*(delta.y/30))
                            }
                        }
                    });
                    $(document).on('touchend',function(){
                        var opa =  morebox.css('opacity');
                         morebox.css({'bottom':'1rem','opacity':0}); 
                        if(opa > 0.8){
                            if(mySwiper.activeIndex == 0){
                                $('#swiper-2').scrollTop(0);
                                // window.scroll(0,0);                   
                                mySwiper.slideTo(1,200,false);
                                changeBrandTitle(1);        
                            }else if(mySwiper.activeIndex == 1){
                                $('#brand_video').scrollTop(0);
                                // window.scroll(0,0);
                                mySwiper.slideTo(2,200,false);
                                changeBrandTitle(2);
                                
                            }  
                        }  
                    });
    </script>
@stop