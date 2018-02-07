
@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/brand.css" rel="stylesheet" type="text/css"/>
     <link href="{{URL::asset('/')}}/css/dist/swiper.min.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section >
        <div class="white-bg mb1-33">
            <div class="name_intro fline">
                <span id="brand_name" data-mark="" data-code="" class="b f16 ">喜茶® HEEKCAA</span>
                 <dl >
                    <dt>转发</dt>
                    <dd id="zhuan"></dd>
                </dl>
                 <dl class="mr3">
                    <dt>收藏</dt>
                    <dd id="fav"></dd>
                </dl>
                <dl class="mr3">
                    <dt>浏览</dt>
                    <dd id="view"></dd>
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
        <!-- 公用蒙层 -->
         <div class="fixed-bg none" style="z-index: 99"></div>
         <!-- 分享时的提示语 -->
         <div class="share-title fixed color666 f14 none">
            <ul id="ul_share_t">
              
            </ul>
        </div>
        
        <!-- 公用-底部按钮 -->
        <div class="brand-btns fixed width100 none brand-p brand-s" id="brand_btns_app">
            
            <div class="btn fl width33 brand_collect"  >
                    <span class="brand-collect-contact_27  brand-collect-contact" >  </span>      
            </div>
            <div class="btn fl width33 brand_collect" id="brand_award" data-fund="">
                <p class="tc color-red f16">领创业基金</p>
                <p class="tc color-yellow f16 brand_fund">￥500</p>     
            </div>
            <div class="btn fl width33 pt05" id="brand_suggest">
                <p class="tc color-white f16">发送加盟意向</p>
                <p class="tc color-yellow f12">*获取更多资料</p>
            </div>
        </div>
        <div class="brand-btns fixed width100 none brand-np  brand-s" id="brand_btns_app ">
            <!-- 收藏 -->
           <!--  <div class="btn fl width33 " class="brand_collect">
                <span class=" b-collect" data-fav="">  </span>      
            </div> -->
            <div class="btn fl width33 " class="brand_collect" >
                    <span class=" brand-collect-contact_27  brand-collect-contact" >  </span>      
            </div>
            <div class="btn fl width33" id="get_coin">
                <p class="tc color8a f16">我要佣金</p>
                <p class="tc color-yellow f12">分享转发，邀请好友</p>
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
         <!-- 释放查看更多 -->
        <!-- <div id="morebox" style="opacity: 0">释放查看更多</div> -->
    </section>
@stop

@section('endjs')
	<script src="{{URL::asset('/')}}/js/_v020700/brand.js"></script>
     <!-- <script src="{{URL::asset('/')}}/js/dist/swiper.min.js"></script> -->
    <script>
        $(function () {
            // var mySwiper = new Swiper('.swiper-container', {
            //     onSlideChangeEnd: function (swiper) {
            //         var j=mySwiper.activeIndex;
            //         $('.brand-tab li, .brand-tab2 li').removeClass('active').eq(j).addClass('active');
            //     }
            // })
            /*列表切换*/
            // $(document).on('.brand-tab li','click',function(){
            //     alert('s');
            // })
            $('.brand-tab li').on('click', function (e) {
                e.preventDefault();
                //得到当前索引
                var i=$(this).index();
                $('.brand-tab li, .brand-tab2 li').removeClass('active').eq(i).addClass('active');
                // mySwiper.slideTo(i,200,false);
                $('.swiper-wrapper .swiper-slide ').addClass('none').eq(i).removeClass('none');
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
            //品牌详情下拉加载更多
            function toBrands(id) {
                if (isAndroid) {
                    javascript:myObject.toBrands(id);
                } 
                else if (isiOS) {
                    var data = {
                        'id':id
                    }
                    window.webkit.messageHandlers.toBrands.postMessage(id);
                }
            }
            //返回品牌详情
            function backBrands(id) {
                if (isAndroid) {
                    javascript:myObject.backBrands(id);
                } 
                else if (isiOS) {
                    var data = {
                        'id':id
                    }
                    window.webkit.messageHandlers.backBrands.postMessage(id);
                }
            }
            //返回品牌项目(detail文件)
            $(document).on('click', '#backTodetail', function() {
                backBrands(0);
            })
    
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
            $(window).on("scroll", function(){
            //函数内判断，距离底部50px的时候则进行数据加载
                if (getScrollTop() + getWindowHeight() -100>= getScrollHeight()) {
                    toBrands(2);
                    // var start,delta;
                    // $(document).on('touchstart',function(e){
                    //      var touches = e.touches[0];
                    //     start = { 
                    //         x: touches.pageX, // 横坐标
                    //         y: touches.pageY  // 纵坐标
                    //     };
                   
                    // });
                    // $(document).on('touchmove',function(e){
                    //      var touches = e.touches[0];
                    //       delta = {
                    //              x: touches.pageX - start.x,
                    //              y: touches.pageY - start.y
                    //         };
                    //     if (Math.abs(delta.x) > Math.abs(delta.y)) {
                    //         event.preventDefault();
                    //     }else{
                    //         if(-(delta.y/30) > 4.5){
                    //             $('#morebox').css({'bottom':'4.5rem','opacity':1});
                    //              toBrands(2);
                    //              console.log(1);
                    //         }else{
                    //             $('#morebox').css('bottom',(-delta.y/30+'rem'));
                    //             $('#morebox').css('opacity',-1/4.5*(delta.y/30))
                    //         }
                    //     }
                    // });
                    // $(document).on('touchend',function(){
                    //     $('#morebox').css({'bottom':'-1rem','opacity':0});       
                    // })   
                }
            });
        })
    </script>
@stop