<!-- Created by wcx -->

@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/dist/swiper.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/agent/_v010004/branddetail.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/agent/mybrand.css" rel="stylesheet" type="text/css"/>
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

@section('main')
    <section id="brand_detail" class="bgcolor none">
        <!--安装app-->
        <!-- <div class="install-app install-app2 none" id="installapp">
            <img src="{{URL::asset('/')}}/images/dock-logo2.png" alt="">
            <div class="fl pl1">
                <span>无界商圈</span><br>
                <span>用无界商圈找无限商机</span>
            </div>
            <a href="javascript:;" class="install-close f24">×</a>
            <a href="javascript:;" class="install-open" id="openapp">立即开启</a>
        </div> -->
        <!-- 顶部课程提示 -->
       <!--  <div class="tc lessontip none">
            请尽快对课程中的视频、资讯进行阅读，以便尽早获得代理！
            <a class="closetip" >X</a>
        </div> -->
        

        <div id="wrapper">
            <div  id="swiper-container1">
                <div class="swiper-wrapper" id="wrapper1">
                <!-- 项目 -->
                    <section  class="swiper-slide pb8 " id="swiper-1">

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
                                 <dl class="mr2">
                                    <dt>收藏</dt>
                                    <dd class="fav"></dd>
                                </dl>
                                <dl class="mr2">
                                    <dt>浏览</dt>
                                    <dd class="view"></dd>
                                </dl>  
                               
                            </div>
                        </div>
                        <!-- 第二部分，基本信息 -->
                        <div class="brand-info white-bg mb1-5   ">
                            <div class="">
                                <div class="info f12">
                                    <div class="lh45 none dashline pl1-5 pr1-5" id="brand_sort2">
                                        <span class="fl   ">标语</span> <span class="fr color999 " id="category_name2"></span>
                                    </div>
                                    <div class="lh45 dashline pl1-5 pr1-5" id="brand_sort">
                                        <span class="fl   ">分类</span> <span class="fr color999 " id="category_name"></span>
                                    </div>
                                    <div class="lh45 dashline pl1-5 pr1-5">
                                        <span class="fl  " >启动资金</span> 
                                        <div>
                                            <span class="fr f14  color999  brand-info-start"><em class="tr" id="brand_investment"></em> 
                                            <em class="color-yellow f12 fr">*费用以实际为准</em></span>
                                        </div>
                                    </div>
                                    <div class="lh45 dashline pl1-5 pr1-5">
                                        <span class="fl">主营产品</span> <span class="fr color999 " id="brand_products"></span>
                                    </div>
                                    <div class="lh45  dashline pl1-5 pr1-5">
                                        <span class="fl  " id="brand_num">店铺数量</span> 
                                         <div>
                                            <span class="fr   color999  brand-info-start" style="margin-top:0.4rem;"><em class="tr">中国大陆地区 <i  id="brand_shops"></i> 家店</em> <em class="color8a f12 tr"  ><span id="brand_click" class="none"> </span></em></span>
                                        </div>
                                    </div>
                                    <div class="lh45 dashline none pl1-5 pr1-5">
                                        <span class="fl   ">分类</span> <span class="fr color8a pr1-5">生活便利</span>
                                    </div>
                                    <div class="pt1 pb1 pl1-5 pr1-5 flex-bet">
                                        <div class="keywords" id='keywords'>
                                        </div>
                                        <div class="r tr">
                                            <p class="agent-red f18 mb0 commission b" id='max_account'></p>
                                            <p class="mb0 f10 color999">成单提成最高金额</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <!--第三部分: 申请代理流程 -->
                        <div class="flow bgwhite pl1-5 pb1-5 mb1-5 none">
                            <div class="fline" id="tag_line" >
                                <span class="tleft f16w lh45 ">申请代理流程</span>
                            </div>
                            <div class="steps tc pr1-5 pb1-5 pt1-5">
                                <div>
                                    <p class="lh-1-2 color999">发送<br>申请</p>
                                    <p class="step">1</p>
                                </div>
                                <div class="grayline"></div>
                                <div>
                                    <p class="lh-1-2 color999">参与<br>培训</p>
                                    <p class="step">2</p>
                                </div>
                                <div class="grayline"></div>
                                <div>
                                    <p class="lh-1-2 color999">等待<br>测试</p>
                                    <p class="step">3</p>
                                </div>
                                <div class="grayline"></div>
                                 <div>
                                    <p class="lh-1-2 color999">获得<br>代理权</p>
                                    <p class="step">4</p>
                                </div>
                            </div>
                            <p class="f12 cfd4d4d pr1-5 mb05">提醒:</p>
                            <p  class="color666 f12 pr1-5 mb05">1、发送申请意向后，请尽快阅读、观看完无界商圈提供的品牌培训文档和视频。</p>
                            <p class="color666 f12 pr1-5 mb05">2、完成文档和视频学习，我们将电话与你取得联系并进行相关资质考核。</p>
                        </div> 
                        <!--第三部分 品牌代理信息 -->
                        
                        <div class="bgwhite mb1-5 pl1-5 pr1-5 pb1-5 none" id="brandAgent">
                            <div class="fline" id="tag_line" >
                                <span class="tleft f16w lh45 ">品牌代理信息</span>
                            </div>
                        </div>   
                        
                        <!-- 第四部分，公司的名片 -->
                        <div class="white-bg pl1-5 pb1-33 mb1-5 brand-company-h none">
                            <div class="fline  company-head" >
                                <span class="tleft f16w ">公司详情</span>
                            </div>
                            <div class="pr1-5 pt1-2">
                                <img src="" alt="" class="company mr1-33 fl" id="brand_logo">
                                <div class="fl width70">
                                    <em class="prove f11 mr1" id="brand_auth">诚信认证</em><span class="f14 b" id="brand_company"></span>
                                    <div class="brand-address f12 color999" id="brand_company_add">
                                        
                                    </div>
                                    <a href="javascript:;" id="company_details" class="btn-detail f12">
                                        查看公司详情
                                    </a>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            
                        </div>
                        <!-- 第五部分，相关活动展示 -->
                        <div class="white-bg rel_activity none">
                            <div class=" fline ml1-5" >
                                <span class="tleft f16w lh45 ">相关活动</span>
                            </div>
                            <div class="brand_act pl1-5">
                            </div>
                            
                        </div>
                        <!-- 分享出去才会出现的品牌推荐 -->
                        <div id="brand_more_share" class="none ">   
                            <div class="brand-info white-bg pl1-5" id="brand_brands">
                                <div class=" pl1-5 fline" style="margin-bottom: 1px">
                                    <span class="tleft f16w lh45 ">同类推荐</span>
                                </div>
                            </div>
                        </div>
                    </section>
                
                <!-- 章节 -->
                    <section class="swiper-slide" id="brand_chapter">
                         <div style="min-height: 101%;" class="pb8 brand_chapter" > 
                             
                         </div>
                    </section>
                <!-- 问答 -->
                    <section class="swiper-slide brand_QA pb8" id="brand_QA">
                      <div style="min-height: 101%" class="pb8">
                        <div class="flex-bet bgwhite pl1-5 pr1-5 mb1-33">
                            <div class="f15 lh45">百问百答，为你提供对话新策略。</div>
                           <!--  <div class="">
                                <div class="color999 f12">已学完30%</div>
                                <div class="progress" style="width:8rem;height: 0.4rem;border-radius: 0.2rem;overflow: hidden;background: #ccc;">
                                    <div class="pro_bg" style="height: 100%;width: 2.4rem;background: #ffac00;"></div>
                                </div>
                            </div> -->
                            <div><span class="f12 color999 pb05" style="display: inline-block;">已学完</span><span class="f12 color999 progress_num"></span><div class="progress"><div class="progressBar" style="width: 0px;"></div></div></div>
                        </div>
                        <div >
                            <ul class="QA_list">
                                <!-- <li class="tl bgwhite mb1-33 pl1-5">
                                    <div class="fline f15 pr1-5 lh45">Q：关于云栖大会首页的几个效果问题？</div>
                                    <div class="f14 color999 pt1-5 pr1-5 pb1-5 ">
                                        天空像大钟， 新月是钟舌。 我的母亲是故土， 我是布尔什维克。 为着全人类 普世之博爱， 你死亡的歌 颇让我欢快。 深蓝的大钟 坚固而洪亮， 我用新月敲打 宣告你的灭亡。 尘世的弟兄， 我的歌给你。 在雾中我听到 明亮的信
                                    </div>

                                </li> -->     
                            </ul>
                        </div>
                        
                      </div>
                    </section>    
                <!-- 资料 为代理时才有的状态-->
                    <section class="swiper-slide " id="brand_data">
                        <div style="min-height: 101%;" class="pb8">
                            <div class="mb1-33">
                                <nav class="datas_sel lh45">
                                    <div class="stepblue">百问百答</div>
                                    <div>章节</div>
                                </nav>
                            </div>
                            <div class="">
                                <div class="">
                                    <ul class="QA_list ">
                                    <!-- <li class="tl bgwhite mb1-33 pl1-5">
                                        <div class="fline f15 pr1-5 lh45">Q：关于云栖大会首页的几个效果问题？</div>
                                        <div class="f14 color999 pt1-5 pr1-5 pb1-5 ">
                                            天空像大钟， 新月是钟舌。 我的母亲是故土， 我是布尔什维克。 为着全人类 普世之博爱， 你死亡的歌 颇让我欢快。 深蓝的大钟 坚固而洪亮， 我用新月敲打 宣告你的灭亡。 尘世的弟兄， 我的歌给你。 在雾中我听到 明亮的信
                                        </div>

                                    </li> -->     
                                    </ul>
                                </div>
                                <div class="brand_chapter none">
                                    
                                </div>
                                
                            </div>
                        </div>
                    </section>
                <!-- 客户 -->
                    <section class="swiper-slide " id='brand_customer'>
                      <div style="min-height: 101%">
                        <div class="white-bg mb1-33 ">
                            <div class="name_intro fline">
                                <div data-mark="" data-code="" class="f16 lesson_name">
                                     <span class="brand_title b"></span>
                                     <span class="cffa300 ml1 f11"><img src="/images/agent/dai.png" alt="" class="lesson">已代理</span>
                                </div>
                                 <div class="r mt05">
                                     <p class="color999 mb0 f11">最高提成</p>
                                     <p class="f18 agent-red commission"></p>
                                 </div>
                            </div>
                            <div class="tc pb1-5 upback pt1-5 backTopre" id="backTodetail">
                                <img src="{{URL::asset('/')}}/images/upback.png" alt="" style="width:1.33rem;">
                            </div>
                        </div> 
                        <!-- 跟进中客户 -->
                        <div class="mb1-33 white-bg pl1-5 nocustomer">
                            <p class="mb1-33 lh45 f16 b fline ">跟进中的客户 <span id="fol_num" class="c2873ff " ></span></p>
                            <div class="pr1-5 followcustomers pb1-5 pt1-5">
                                <!-- <div class="customer_flex p1 mb1">
                                    <div>
                                        <img src="" alt="header" class="l mr1 customer_img">
                                         <div class="l">  
                                            <p class="f15 mb05"><span>华容</span><img src="/images/agent/girl.png" alt="性别" class="gender"></p>
                                            <p class="f12 color999 mb05">上海 徐家汇</p>
                                        </div>
                                    </div>
                                    <div class="tr f11">
                                        <p class="mb05">7月11日 开始跟单</p>
                                        <p class="mb05">已跟单19天</p>
                                    </div>
                                </div> -->
                                    
                            </div> 
                        </div>
                         <!-- 我的成单客户 -->
                        <div class="mb1-33 white-bg pl1-5 nocustomer">
                            <p class="mb1-33 lh45 f16 b fline ">我成单的客户 × <span class="brand_title" style='vertical-align: bottom;'></span> <span id='suc_num' class="c2873ff ">(1)</span></p>
                            <div class="pr1-5 pb1-5" id='suc_customer'>
                                <!-- <div class="bgf5 mb1-33 is_show_suc">
                        
                                </div> -->
                                
                            </div>     
                        </div>
                       </div> 
                    </section>
                <!-- 详情 -->
                    <section class="swiper-slide pb8 " id='swiper-2'>
                         <div class="white-bg mb1-33 ">
                            <div class="name_intro fline">
                                <span  data-mark="" data-code="" class="b f16 brand_name"></span>
                                 <div class="r mt05">
                                     <p class="color999 mb0 f11">最高提成</p>
                                     <p class="f18 agent-red commission"></p>
                                 </div>
                            </div>
                            <div class="tc pb1-5 upback pt1-5 backTopre" id="backTodetail">
                                <img src="{{URL::asset('/')}}/images/upback.png" alt="" style="width:1.33rem;">
                            </div>
                        </div> 
                        <div class="brand_detail ">
                                <!-- 图文详情 -->
                                <div class=" mb1-33 white-bg pl1-5">
                                    <p class="mb1-33  f16 b fline ">图文详情</p>
                                    <div class="pic_text pr1-5 color666 pb1-33 f12"></div>
                                </div>
                                <!-- 项目介绍 -->
                                <div class=" mb1 white-bg pl1-5">
                                    <p class="mb1-33  f16 b fline">加盟简介</p>
                                    <div class="join_intro pr1-5 color666 pb1-33 f12" id='brand_j_1'> </div>
                                </div>
                                <div class=" mb1 white-bg pl1-5">
                                    <p class="mb1-33  f16 b fline">加盟优势</p>
                                    <div class="join_adv pr1-5 color666 pb1-33 f12" id='brand_j_2'> </div>                    
                                    
                                </div>
                                <div class="mb1-33 white-bg pl1-5">
                                    <p class="mb1-33  f16 b fline">加盟条件</p>
                                    <div class="join_term pr1-5 color666 pb1-33  f12" id='brand_j_3'></div>
                                     
                                </div>
                                 <div class="mb1-33 white-bg pl1-5">
                                    <p class="mb1-33  f16 b fline">产品图片</p>
                                    <div class="product_imgs" id="brand_images">
                                        
                                    </div>
                                </div>
                        </div>
                    </section>
                <!-- 视频 -->
                    <section class="swiper-slide pb8 " id="brand_video" >
                      <div style="min-height:101%">
                        <div class="white-bg mb1-33">
                            <div class="name_intro fline">
                                <span  data-mark="" data-code="" class="b f16 brand_name"></span>
                                <div class="r mt05">
                                     <p class="color999 mb0 f11">最高提成</p>
                                     <p class="f18 agent-red commission"></p>
                                </div>
                            </div>
                            <div class="tc pb1-5  pt1-5 backTopre " id="backTomore">
                                <img src="{{URL::asset('/')}}/images/upback.png" alt="" style="width:1.33rem;">
                            </div>
                        </div>
                        <div class="videoss none" style="padding-top:10rem;">
                             <img id="novideo"  src="{{URL::asset('/')}}/images/novideo.png" alt="" style="width: 13rem;display: block;margin: 0 auto;">
                        </div>
                            <!-- <div class="brand-title f16">相关视频</div> -->
                            <ul class="more_video white-bg pl1-5" id="relativevideo">
                               
                            </ul>
                            <div class="tc no-data none">
                                <p class="color999 f12 mt3-5">没有更多数据咯</p>
                               <!--  <p class="mt3-5 mb10"><a class="color-orange f12 " href="javascript:void(0)" id='moreVideo'>想要了解更多精彩视频？立即前往查看吧！</a></p> -->
                            </div>
                      </div>
                    </section>
                </div>
            </div>
         </div>
    </section>
    <section id="bottom_public">
        <!-- 商务代表 -->
        <div class="tc business none">
            <img src="/images/agent/white_head.png" class="white_head">
            <p class="" style="margin-top: 0.2rem;">商务代表</p>
        </div>
        <div class="businessTip none">
            <img src="/images/agent/error.png" alt="" class="close_business">
            <p class="lh45 f15 color333 b tc fline">品牌商务代表</p>
            <div class="pl3 pr3 pb1-5">
                <p class="f13 mb05">品牌名称：<span class="brand_title" style="vertical-align: text-top;"></span></p>
                <p class="f13">商务代表：<span class="deputy"></span></p>
                <div class="fun_btn mb05">
                    <a class="l send_mes" >短信</a>
                    <a class="r call_tel" >电话</a>
                    <div class="clearfix"></div>
                </div>
                <p class="color999 mb0 f11">* 考察邀请、付款协议等创建前，请联系商务代表，确定相关细则。</p>
                <p class="color999 mb0 f11">* 如没有及时确定而造成的损失或意外情况，我们将取消经纪人资质。</p>
            </div>     
        </div>
        <!-- 公用-底部按钮 -->
        <div id="brand_btns_app" class="none">
           <button class="applyAgent fixed-bottom-iphoneX">
               <p class="f16 mb0">申请品牌代理</p>
               <p class="f12 mb0">获得品牌代理权限，对投资人进行跟单服务</p>
           </button>
        </div>
        <div id="brand_seek" class="none">
            <button class="applyAgent fixed-bottom-iphoneX" >
               <p class="f16 mb0">查看咨询任务</p>
           </button>
        </div>
        <div id="inter_learn" class="inter_learn none">
           <button class="applyAgent fixed-bottom-iphoneX">
               <p class="f16 mb0">进入学习模块</p>
               <p class="f12 mb0">完成章节内容的学习，成为更优秀经纪人</p>
           </button>
        </div>
        <div id="learn_all" class="inter_learn none">
           <button class="applyAgent fixed-bottom-iphoneX">
               <p class="f16 mb0">已完成学习，请等待电话回访</p>
           </button>
        </div>
    <!-- 进行实名认证提醒 -->
       <div class="certification none">  
            <p class="lh45 f15 color333 b tc fline">提醒</p>
            <div class="pl3 pr3 pb1-5">
                <p class="f13 mt1-5 mb1-5">你还没有进行实名认证</p>         
                <div class="fun_btn mb05">
                    <a class="l cancel" >取消</a>
                    <a class="r makesure" >确定</a>
                    <div class="clearfix"></div>
                </div>      
            </div>     
        </div>
      <!-- 分享的标语 -->
        <div class="share-title fixed color666 f14 fixed-bottom-iphoneX none">
            <ul id="ul_share_t">
              <li class="share-li tl lh45 white-bg border-8a-b"><em></em>品牌加盟,投资小,利润高,加盟即赚！</li>
              <li class="share-li tl lh45 white-bg border-8a-b"><em></em>新手加盟，选对品牌很重要</li>
              <li class="share-li tl lh45 white-bg border-8a-b"><em></em>品牌加盟，开店之选！</li>
              <li class="share-li tl lh45 white-bg border-8a-b"><em></em>总部全程扶持，轻松加盟创业。</li>
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
    <!-- <script type="text/javascript" src="{{URL::asset('/')}}/js/dist/jquery-1.8.3.min.js"></script> -->
    <!-- <script type="text/javascript" src="{{URL::asset('/')}}/js/dist/jquery.lazyload.min.js"></script> -->
    <script type="text/javascript" src="{{URL::asset('/')}}/js/agent/_v010004/brand.js"></script>
	<script type="text/javascript" src="{{URL::asset('/')}}/js/agent/src/mTouch.js"></script>
    <script type="text/javascript">
        //悬浮球
        var assistiveLeft, assistiveRight, timerid;
        var stickEdge = function (el) {
            var left = parseInt(el.offsetLeft) || 0,
                top  = parseInt(el.offsetTop) || 0,
                width = parseInt(el.offsetWidth) || 0,
                height = parseInt(el.offsetHeight) || 0,
                windowWith = (document.documentElement || document.body).offsetWidth;
                windowHeight = (document.documentElement || document.body).offsetHeight;
        // 左右越界
            if (left > (windowWith - width) / 2) {
                // left = windowWith - width - 2;为什么减2
                left = windowWith - width ;
            } else {
                left = 0;
            };
        //上下越界
            if( top< 0 ){
                top = 0;
            }else if(top+height > windowHeight){
                top = windowHeight -height-2;
            }
            el.style.transition = 'all .2s';
            el.style['-webkit-transition'] = 'all .2s';
            el.style.left = left + 'px';
            el.style.top = top + 'px';
            timerid = setTimeout(function () {
                el.style.transition = 'all .5s';
                el.style['-webkit-transition'] = 'all .5s';
                el.style.opacity = '1';
            }, 2000);
        };

        mTouch('.business').on('swipestart', function () {
            clearTimeout(timerid);
            this.style.transition = 'none';
            this.style['-webkit-transition'] = 'none';
            this.style.opacity = '.8';
            assistiveLeft = parseInt(this.offsetLeft) || 0;
            assistiveTop = parseInt(this.offsetTop) || 0;
            return false;
        })
        .on('swiping', function (e) {
            this.style.left =  assistiveLeft + e.mTouchEvent.moveX + 'px';
            this.style.top = assistiveTop + e.mTouchEvent.moveY + 'px';
        })
        .on('swipeend', function () {
            stickEdge(this);
        });
    //点击商务代表
       mTouch('.business').on('tap',function(){
            $('.fixed-bg').removeClass('none');
            $('.businessTip').removeClass('none');   
        });
        //关闭商务代表弹窗
        mTouch('.close_business').on('tap',function(){
            $('.fixed-bg').addClass('none');
            $('.businessTip').addClass('none');
        });

    </script>
    <script>  
            var urlPath = window.location.href;
            var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;  
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
                    mySwiper.slideTo(index,200,true);
                }

                $(document).on('click','.backTopre',function(){
                    var pre_index =$(this).parents('.swiper-slide').index()-1;
                   $(this).parents('.swiper-slide').eq(pre_index).scrollTop(0);
                   // window.scroll(0,0);
                    mySwiper.slideTo(pre_index,200,false);
                    if(!shareFlag){
                        changeBrandTitle(pre_index);
                    };
                })

            // 查看咨询任务
                function checkConsults(id,agent_id){
                    if (isAndroid) {
                        javascript:myObject.checkConsults(id,agent_id);
                    } 
                    else if (isiOS) {
                        var data = {
                            'id':id,
                            'agent_id':agent_id
                        }
                        window.webkit.messageHandlers.checkConsults.postMessage(data);
                    }
                }


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
                    $(document).on('touchstart','#brand_detail',function(e){
                         var touches = e.touches[0];
                        start = { 
                            x: touches.pageX, // 横坐标
                            y: touches.pageY  // 纵坐标
                        };
                    });
                    $(document).on('touchmove','#brand_detail',function(e){

                         var touches = e.touches[0];
                          delta = {
                                 x: touches.pageX - start.x,
                                 y: touches.pageY - start.y
                            };
                            
                        if (Math.abs(delta.x) > Math.abs(delta.y)) {
                            event.preventDefault();
                        }else if(getScrollTop() > 50 && !(mySwiper.isEnd)){
                            if($('#brand_btns_app').hasClass('none')){
                                morebox.css('bottom','-3.5rem');
                                if(delta.y/20 < -3.5 ){
                                     morebox.css({'bottom':0,'opacity':1});
                                }else{
                                    morebox.css('bottom',(delta.y/20+'rem'));
                                    morebox.css('opacity',0);
                                }
                            }else{
                                if(-(delta.y/20) > 5.5){
                                    morebox.css({'bottom':'5.5rem','opacity':1});
                                }else{
                                    morebox.css('bottom',(-delta.y/20+'rem'));
                                    morebox.css('opacity',-1/5.5*(delta.y/20))
                                }
                            }
                            
                        }
                    });
                    $(document).on('touchend','#brand_detail',function(){
                        var opa =  morebox.css('opacity');
                         morebox.css({'bottom':'1rem','opacity':0}); 
                         var activeIndex = mySwiper.activeIndex;
                        if(opa > 0.8){
                            mySwiper.slideTo(activeIndex + 1,200,true);
                            changeBrandTitle(activeIndex + 1); 
                            $('#wrapper1>.swiper-slide').eq(activeIndex+1).scrollTop(0);    
                            }
                       
                    });
    </script>
@stop