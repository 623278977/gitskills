
@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/brand.css" rel="stylesheet" type="text/css"/>
     <link href="{{URL::asset('/')}}/css/dist/swiper.min.css" rel="stylesheet" type="text/css"/>
     <style>
         .brand-tab .active a{
            color:#00a0FF;
         }
         .brand-message a.btn{
            background-color: #00a0FF;
         }
     </style>
@stop
@section('main')
    <section >
        <!--列表切换选项卡-->
        <div class="brand-tab mb1-5">
            <ul class="lh45">
                <li class="active width33 "><a href="##" >图文详情</a></li>
                <li class="width33 "><a href="##">项目介绍</a></li>
                <li class="width33 "><a href="##">项目图集</a></li>
            </ul>
        </div>

        <!--列表内容-->
        <div class="swiper-container">
            <div class="swiper-wrapper">
                <!-- 图文详情 -->
                <div class="swiper-slide tab-content-1">
                    <div class="white-bg mb1-5 f14" id="brand_more_detail" style="padding:1.2rem; ">
                        
                    </div>
                    <div class="brand-info white-bg pl1-33 mb10" id="brand_brands">
                        <div class="info-head fline"style="margin-bottom: 1px">
                            <span class="tleft f16w lh45">同类推荐</span>
                        </div>
        
                    </div>
                </div>
                <!-- 项目介绍 -->
                <div class="swiper-slide tab-content-2 ">
                    <div class="brand-info white-bg mb1-5 pl1-33">
                        <div class="info-head fline ">
                            <span class="tleft f16w lh45">加盟简介</span>
                        </div>
                        <div class="white-bg box" id="brand_j_1">
                            
                        </div>  
                    </div>
                    <div class="brand-info white-bg mb1-5 pl1-33">
                        <div class="info-head fline ">
                            <span class="tleft f16w lh45 ">加盟优势</span>
                        </div>
                        <div class="white-bg box" id="brand_j_2">
                            
                        </div>
                    </div>
                    <div class="brand-info white-bg mb1-5 pl1-33">
                        <div class="info-head fline ">
                            <span class="tleft f16w lh45">加盟条件</span>
                        </div>
                        <div class="white-bg box" id="brand_j_3">
                        
                        </div>
                    </div>
       
                </div>
                <div class="swiper-slide tab-content-3" style="padding:0rem 1.33rem">
                    <div class="box-img" id="brand_images">
                        <!-- <img src="{{URL::asset('/')}}/images/act_banner.png" alt="">
                        <img src="{{URL::asset('/')}}/images/share_image.png" alt="">
                        <img src="{{URL::asset('/')}}/images/apply_success.png" alt=""> -->

                    </div>
                     
                    
                </div>
            </div>
        </div>
         <div class="fixed-bg none" style="z-index: 99"></div>
         <div class="share-title fixed color666 f14 none">
            <ul id="ul_share_t">
              
            </ul>
        </div>
        
        <!-- 公用-底部按钮 -->
        <div class="brand-btns fixed width100 none brand-p brand-s" id="brand_btns_app" style="z-index: 99">
            <div class="btn fl width33 " id="brand_collect">
                    <span class=" b-collect" data-fav="">  </span>      
            </div>
            <div class="btn fl width33 pt05 " id="brand_award" data-fund="">
                <p class="tc color-red f16">领取创业基金</p>
                <p class="tc color-yellow brand_fund" id="brand_fund"></p>
            </div>
            <div class="btn fl width33 pt05" id="brand_suggest">
                <p class="tc color-white f16">发送加盟意向</p>
                <p class="tc color-yellow f12">*了解更多意向</p>
            </div>
        </div>
        <div class="brand-btns fixed width100 none brand-np  brand-s" id="brand_btns_app " style="z-index: 99">
            <div class="btn fl width50 " id="brand_collect">
                <span class=" b-collect" data-fav="">  </span>      
            </div>
           
            <div class="btn fl width50 pt05" id="brand_suggest">
                <p class="tc color-white f16">发送加盟意向</p>
                <p class="tc color-yellow f12">*了解更多意向</p>
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
                    <input type="text" placeholder="" name="realname">
                </p>
                <p class="f14 margin0  mb5">
                    <label for=""> 手机号：</label>
                    <input type="text" placeholder="" name="phone">
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

                        <p class="tc"><a href="##" class="f18 mt2 mb2 tc">查看我的红包>></a></p>
                        <p class="f14 tc color-white mt2">具体使用规则参考<a href="##" style="text-decoration: underline;">创业基金使用说明</a></p>
                    </div>
                </div>
                <div class="close absolute f20 tc" id="packet_close">
                    ×
                </div>
            </div>
        </div>

        <div id="brand_logo" class="none"></div>
        <div class="tips none"></div>
        <div id="brand_name" class="none"></div>
        <div id="category_name" class="none"></div>

        <!-- 公用-底部按钮 -->
        <!-- <div class="brand-btns fixed width100 ">
            <div class="btn fl width33  " id="brand_collect"></div>
            <div class="btn fl width33 pt05">
                <p class="tc color-red">领取创业基金</p>
                <p class="tc color-yellow">￥555</p>
            </div>
            <div class="btn fl width33 pt05" id="brand_suggest">
                <p class="tc color-white">发送加盟意向</p>
                <p class="tc color-yellow f12">*了解更多意向</p>
            </div>
        </div> -->
    </section>
@stop

@section('endjs')
	<script src="{{URL::asset('/')}}/js/brand.js"></script>
     <script src="{{URL::asset('/')}}/js/dist/swiper.min.js"></script>
    <script>
        $(function () {
            var mySwiper = new Swiper('.swiper-container', {
                onSlideChangeEnd: function (swiper) {
                    var j=mySwiper.activeIndex;
                    $('.brand-tab li, .brand-tab2 li').removeClass('active').eq(j).addClass('active');
                }
            })
            /*列表切换*/
            $('.brand-tab li, .brand-tab2 li').on('click', function (e) {
                e.preventDefault();
                //得到当前索引
                var i=$(this).index();
                $('.brand-tab li, .brand-tab2 li').removeClass('active').eq(i).addClass('active');
                mySwiper.slideTo(i,200,false);
            });
           
        });
    </script>
@stop