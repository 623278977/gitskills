
@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/dist/swiper.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/_v020900/brandpro.css" rel="stylesheet" type="text/css"/>
    <!-- <link href="{{URL::asset('/')}}/css/brand.css" rel="stylesheet" type="text/css"/> -->
<style> 
    .tr{
        text-align: right;
    }
    .swiper-pagination{
        right: 0;
        left: auto;
        width: auto;
    }
    #swiper-container1{
        width:100%;
        overflow: hidden;
        position: relative;
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
        <div class="install-app install-app2 none" id="installapp">
            <img src="{{URL::asset('/')}}/images/dock-logo2.png" alt="">
            <div class="fl pl1">
                <span>无界商圈</span><br>
                <span>用无界商圈找无限商机</span>
            </div>
            <a href="javascript:;" class="install-close f24">×</a>
            <a href="javascript:;" class="install-open" id="openapp">立即开启</a>
        </div>  
            <p class="none" id="brand_name"></p>             
            <!-- 品牌详情 -->
            <section  class="pb8" id="swiper-1">
                <!-- 第一部分，品牌大图和名称 -->
                <div class="brand-head white-bg mb1-5 " >
                    <!-- 品牌的轮播图 -->
                    <div class="relative">
                        <div  id="swiper-container1" >
                            <div class="swiper-wrapper swiper-brand">
                               
                            </div>
                            <div class="swiper-pagination  pr1-33 white tr"></div>
                        </div>
                        <div class="pl1-33 pr1-33 packet_module" id="brand_award">
                            <img src="/images/packet_logo.png" class="l packet_logo mr1">
                            <div class="l" >
                                <p class="f16 white mb0">点击抢红包</p>
                                <p class="color-yellow f12 mb0">赢取全场红包、品牌专场红包</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="name_intro">
                        <span id="company_name" data-mark="" data-code="" class="b f16 brand_name"></span>
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
                <div class=" mb1-5">
                    <div class="fline white-bg">
                        <div class="info pl1-33">
                            <div class="lh45 " >
                                <span class="fl f16  ">投资额度</span> <span class="fr f14  color8a pr1-33" id="brand_investment"><em class="color-red"></em>万元</span>
                            </div>
                            <div class="lh45 " id="brand_sort2">
                                <span class="fl f16  ">标语</span> <span class="fr f14  color8a pr1-33" id="solgan"></span>
                            </div>
                            <div class="lh45 ">
                                <span class="fl f16  ">行业分类</span> <span class="fr f14  color8a pr1-33" id="industry_class"></span>
                            </div>
                            <div class="lh45 none" id="join_area">
                                <span class="fl f16  ">加盟区域</span> <span class="fr f14  color8a pr1-33" ></span>
                            </div>
                            <div class="lh45 none " id="shop_area">
                                <span class="fl f16  ">店铺面积</span> <span class="fr f14  color8a pr1-33" ></span>
                            </div>  
                            <div class="lh45 none " id="contract_period">
                                <span class="fl f16  ">合同期限</span> <span class="fr f14  color8a pr1-33" ></span>
                            </div>                            
                            <div class="lh45 ">
                                <span class="fl f16 " id="brand_num">店铺数量</span> 
                                <span class="fr f14  color8a pr1-33" id="shop_num"></span>         
                            </div>
                            <div class="lh45">
                                <span class="fl f16">主营产品</span> <span class="fr f14  color8a pr1-33" id="brand_products"></span>
                            </div>                 
                        </div>            
                    </div>
                    <div class="lh45 tc white-bg">
                        <button class="btn-detail " id="toMoreDetail">更多详情</button>
                    </div>
                </div>  
                <!-- 第三部分,盈利预估 -->
                 <div class="white-bg mb1-5 pl1-33 none" id="profit">
                    <div class="lh45 fline" >
                            <span class="tl f16 b">盈利预估</span>
                    </div>
                    <div class="pt1 pb1 pr1-33">
                         <table class="forecast f14 mb1">
                             <tr>
                                 <td>初始投资总额</td>
                                 <td id="initial_investment"></td>
                             </tr>
                              <tr>
                                 <td>客单价</td>
                                 <td id="single_customer_price"></td>
                             </tr>
                              <tr>
                                 <td>日客流量</td>
                                 <td id="day_flow"></td>
                             </tr>
                              <tr>
                                 <td>预估月销售额</td>
                                 <td id="month_sales_mount"></td>
                             </tr>
                             <tr>
                                 <td>毛利率</td>
                                 <td id="margin_rate"></td>
                             </tr>
                             <tr>
                                 <td>回报周期</td>
                                 <td id="return_period"></td>
                             </tr>
                         </table>
                         <div class="color-yellow f10">*仅供参考，以实际开店为准</div>       
                    </div>
                </div> 
                <!-- 第四部分 主打产品 -->
                <div class="white-bg mb1-5 pl1-33 none" id="product">
                    <div class="lh45 fline" >
                        <span class="tl f16 b">主打产品</span>
                    </div>
                    <div class="mt1 pr1-33">
                        <ul class="pic_list" id="product_imgs">
                                     
                        </ul>
                        <div class="clearfix"></div>               
                    </div>
                </div>
                <!-- 第五部分 门店实景 -->
                <div class="white-bg mb1-5 pl1-33 none" id="stores">
                    <div class="lh45 fline" >
                        <span class="tl f16 b">门店实景</span>
                    </div>
                    <div class="mt1 pr1-33">
                        <ul class="pic_list" id="store_imgs">
                                 
                        </ul>
                        <div class="clearfix"></div>               
                    </div>
                </div>

                <!-- 第六部分，品牌视频 -->
                <div class="white-bg mb1-5 pl1-33 none" id="brand_video">
                    <div class="lh45 fline" >
                        <span class="tl f16 b">品牌视频</span>
                    </div>
                    <div class="mt1 pr1-33">
                        <ul class="pic_list" id="video_imgs">
                           
                        </ul>
                        <div class="clearfix"></div>               
                    </div>
                </div>
                <!-- 第七部分 项目问答 -->
                <div class=" white-bg mb1-5" id="brand_question">
                    <div class="fline" style="padding-left:1.33rem;">
                        <div class=" fline lh45" id="brand_toquestion">
                            <span class="tl f16 b">项目问答</span><span class="sj_icon mt1-5"></span>
                        </div>      
                        <div class="fline lh45 ques-asks">
                            <em class="brand-ask f12 fl">问</em><span class="f16 color333 no-wrap fl width80" id="brand_ques"></span>
                        </div>  
                        <div class="clearfix"></div>
                        <div class=" lh45 ques-asks">
                            <span class="brand-answer f12 fl">答</span><span class="f14 color8a no-wrap fl width80" id="brand_ans"></span>
                        </div>   
                    </div>
                    <div class="tf tc lh45 ">
                            <button class='my-ques btn-detail'>我要提问</button>
                    </div>  
                </div>
            
              
                <!--相关活动展示 -->
                <div class="white-bg rel_activity mb1-5 none">
                    <div class=" fline ml1-33" >
                        <span class="tl f16 b lh45 ">相关活动</span>
                    </div>
                    <div class="brand_act">
                       <!--  <div class="fline toAct" >
                            <div class="l act_img ">
                            <button class="f14 act_ing "><span></span>报名中</button>
                            <img src="'+item.list_img+'" alt="" style="height:100%"></div>
                            <div class="act_intro"> <p class="f16 mb0 b">活动标题</p>
                            <p class="f12 color8a mb0">开始时间：<span>2017/10/10</span></p>
                            <p class="f12 color8a">活动场地：<span>杭州 北京 上海</span></p></div>
                            <img src="/images/more_icon.png" class="to_act"><div class="clearfix"></div>
                        </div> -->
                    </div>
                </div>
                 <!--  评价 -->
                <div class=" white-bg mb1-5">
                   <div class="pl1-33  relative" id="brand_judge">
                        <div class=" fline lh45" >
                            <span class="tl f16 b">评价</span><span class="color666 f12 ml05">(2)</span>
                        </div>
                        <div>
                            <p class="mt1-33">
                                <img src="/images/dock-logo2.png" alt="" class="judger_head mr1-33"><span class="f16">姓名</span>
                            </p>
                            <p class="f12 color8a">啊撒旦法法是的</p>
                        </div>      
                        <div class="tf fline tc lh45 ">
                            <button class='toAlljudge btn-detail'>查看全部评价</button>
                        </div> 
                    </div>
                </div>
                <!-- 分享出去才会出现的品牌推荐 -->
                <!-- <div id="brand_more_share" class="none ">   
                    <div class="brand-info white-bg pl1-33" id="brand_brands">
                        <div class=" pl1-33 fline"style="margin-bottom: 1px">
                            <span class="tleft f16w lh45 ">同类推荐</span>
                        </div>
                    </div>
                </div> -->
                <!-- <div style="height: 15rem;"></div> -->
            </section> 
            <!-- 查看大图  -->
            <section class="lookBigPic none">   
                    <div class="swiper-container mb1-5 b-radius03 width38 " id="swiper2">
                        <div class="swiper-wrapper " id="product_swiper">
                           <!--  <div class="swiper-slide"><img src="/images/act_banner.png" style="max-height: 100%;max-width:100%;"></div>-->
                        </div>
                        
                        <div class="swiper-pagination pr1-33 white tr" id="bigPic_pag"></div>
                        <div class="change_slide">
                            <img src="/images/right_btn.png" style="width:2.2rem;height: 2.2rem;transform: rotate(180deg)" class="swiper-prev-btn">
                            <img src="/images/right_btn.png" style="width:2.2rem;height: 2.2rem;" class="swiper-next-btn">
                        </div>
                    </div>                   
                <div class="white-bg pl1-33 pr1-33 f16 pt1-5 pb1-5 b-radius03" id="swiper3">
                   <div class="swiper-wrapper " >
                        <div class="swiper-slide"></div>
                    </div>
                </div>
            </section>   
    </section>
    <!-- 公共部分 弹窗等 -->
    <section id="bottom_public">
        <!-- 公用-底部按钮 -->
       
        <div class="brand-btns fixed width100 brand-np  brand-s fixed-bottom-iphoneX none" id="brand_btns_app">
            <div class="btn fl pt05 width100" id="brand_suggest">
                <p class="tc color-white f16">客服咨询</p>
                <p class="tc color-yellow f12">了解品牌加盟最佳方案，获得更多优惠</p>
            </div>
        </div>
        <!-- 公用-分享出去的底部按钮 -->
        <div class="brand-btns fixed width100 none fixed-bottom-iphoneX" id="brand_btns_share">
            <div class="btn fl width50 tc color-red lh45 brand-share-ask" data-type="data">获取更多资料</div>
            <div class="btn fl width50 tc color-white bg-red lh45"><a href=" tel:4000110061" class="blocks color-white">电话咨询</a></div>
        </div>
        <!-- 公用-发送加盟意向 -->
        
        <!-- app内部 咨询弹窗 -->
        <div class="seekmodule none">
            <p class="tc b f16">提醒</p>
            <div class="f14 seektips">
                经纪人对接前，我们将安排无界商圈客服小Q与您沟通！
            </div>
            <p>
                <a class="btn_makesure f16"> 知道了 </a>
            </p>
        </div>
        <!-- 提问框 -->
        <div class="commentback none" id="commentback">
            <div style="width: 100%;height:100%;position:absolute;bottom:15rem;left:0;" id="tapdiv"></div>
            <div class="textareacon fixed-bottom-iphoneX">
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
        </div> 
        <!-- 无界商圈红包砸中你 -->
        <div class="packet_v29 fixed brand-packet none">
            <div class="close_v29 tr mb1" style="margin-right: -1.5rem;">
                <img src="/images/close.png" class="close_img" id="packet_close">
            </div>
            <div class="packet-body">
                <div style="color:#8f694d;" class="f24 tc pt12">
                    <p class="mb05">恭喜无界商圈</p>
                    <p style="font-weight: bold;">专属红包砸中了你</p>
                </div>
                <div class="tc mt5">
                    <button class="receive" id="receive">立即领取</button>
                </div>
            </div>
             <!-- 领取的数额 -->
            <div class="packet_style none">
                <div class="tc white pt1 pb1">
                    <p class="f20 mb0">恭喜你获得价值<span id="packet_total"></span>元好礼</p>
                    <p class="f14">确认领取后，可咨询经纪人具体使用方式</p>
                </div>
                <ul id="packet_list" style="max-height: 45rem;overflow: scroll;">
                    <li class="envelopes">
                        <div class="color999">
                            <div class="f13 l  fund_num">
                                <p class="mb0">￥<span class="f24">500</span></p>
                                <p class="mb0">全场无条件红包</p>
                            </div>
                            <div class="l fund_type">
                                <p class="mb05 f18 ">不限品脾</p>
                                <p class="mb0 f13 colorccc">加盟抵扣券</p>
                            </div>
                        </div>
                    </li>
                </ul>
                <div class="tc mt2">
                    <a  class="f14 white toFound" style="text-decoration: underline;">点击了解红包使用方式</a>
                </div>

            </div>
        </div>
        <div class="share-title fixed color666 f14 none fixed-bottom-iphoneX">
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
	<script src="{{URL::asset('/')}}/js/_v020900/brand.js"></script>
@stop