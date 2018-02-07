
@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/dist/swiper.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/brand.css" rel="stylesheet" type="text/css"/>
    <style> 
        .swiper-pagination-fraction {
    text-align: right;
    padding-right: 1.2rem;
    color: #fff;
}
    </style>

@stop
@section('beforejs')
   <script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan class='hide' id='cnzz_stat_icon_1261401820'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s95.cnzz.com/z_stat.php%3Fid%3D1261401820' type='text/javascript'%3E%3C/script%3E"));
   </script>
@stop
@section('main')
    <section id="brand_detail " class="bgcolor">
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
    	<!-- 第一部分，品牌大图和名称 -->
    	<div class="brand-head white-bg mb1-5">
    		<!-- 品牌的轮播图 -->
            <div class="swiper-container">
                <div class="swiper-wrapper swiper-brand">
                    
                </div>
                <div class="swiper-pagination swiper-pagination-fraction"></div>
            </div>
    		<h1><span id="brand_name" data-mark="" data-code=""></span></h1>
    		<p class="tc pb1">
                <span class="brand-collect-small mr05"></span><span class="f12 color8a" id="brand_fav"></span>
                <span class="brand-collect-small ml05 mr05 seen"></span><span class="f12 color8a" id="brand_fav2"></span>
            </p>
    	</div>
    	<!-- 第二部分，基本信息 -->
    	<div class="brand-info white-bg mb1-5	">
    		<div class="brand-pl">
    			<div class="info-head fline">
    				<span class="tleft f16w lh45">基本信息</span>
    			</div>
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
                            <span class="fr f14  color8a pr1-33 brand-info-start" style="margin-top:0.4rem;"><em class="tr f14">中国大陆地区<strong class="f14 color-red" id="brand_shops"> </strong>家店</em> <em class="color8a f12 tr"  ><span id="brand_click" class="none">   </span></em></span>
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
    		<div class="info-head fline" >
				<span class="tleft f16w lh45 pl1-33">品牌标签</span>
			</div>
			<ul class="bgcolor brand-tags"  >
                <div id="brand_tags" >   
                    <!-- <li class="width33 fl lh45   white-bg ui-border"  > <span><em class="brand-tags"></em>是是是</span></li>
                    <li class="width33 fl lh45  white-bg  " > <span>是是是</span></li>
                    <li class="width33 fl lh45  white-bg " > <span>是是是</span></li>
                    <li class="width33 fl lh45  white-bg "  > <span>是是是</span></li>
                    <li class="width33 fl lh45  white-bg "  > <span>是是是</span></li> -->
                    <!-- <div class="clearfix"></div> -->
                </div>
				
				<div class="clearfix"></div>
			</ul>
    	</div>
    	<!-- 第四部分，项目问答 -->
    	<div class="brand-info white-bg mb1-5" id="brand_question">
    		<div class="brand-pl  relative">
    			<div class="info-head fline lh45" id="brand_toquestion">
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
    	</div>
    	<!-- 第五部分，公司的名片 -->
    	<div class="white-bg brand-company pl1-33 mb1-5 brand-company-h none">
        <div class="pl1-33">
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
        <!-- 分享出去才会出现的图文详情和品牌推荐 -->
        <div id="brand_more_share" class="none ">   
            <div class="white-bg mb1-5 f14" id="brand_more_detail" style="padding:1.2rem;" >
                        
            </div>
            <div class="brand-info white-bg pl1-33" id="brand_brands">
                <div class="info-head pl1-33 fline"style="margin-bottom: 1px">
                    <span class="tleft f16w lh45 ">同类推荐</span>
                </div>

            </div>
        </div>
        <div style="height: 15rem;">  </div>
    	<!-- 公用-底部按钮 -->
    	<div class="brand-btns fixed width100 none brand-p brand-s" id="brand_btns_app">
    		<div class="btn fl width25 " id="brand_collect">
                    <span class=" b-collect" data-fav="">  </span>      
            </div>
            <div class="btn fl width25 " id="brand_collect" >
                    <span class="  brand-collect-contact" >  </span>      
            </div>
    		<div class="btn fl width25 lh45" id="brand_award" data-fund="">
    			<p class="tc color-red f16">创业红包</p>
    			<!-- <p class="tc color-yellow brand_fund" id="brand_fund"></p> -->
    		</div>
    		<div class="btn fl width25 pt05" id="brand_suggest">
    			<p class="tc color-white f16">发送加盟意向</p>
    			<p class="tc color-yellow f12">*获取更多资料</p>
    		</div>
    	</div>
        <div class="brand-btns fixed width100 none brand-np  brand-s" id="brand_btns_app ">
            <div class="btn fl width33 " id="brand_collect">
                <span class=" b-collect" data-fav="">  </span>      
            </div>
             <div class="btn fl width33 " id="brand_collect" >
                    <span class="brand-collect-contact" > </span>      
            </div>
           
            <div class="btn fl width33 pt05" id="brand_suggest">
                <p class="tc color-white">发送加盟意向</p>
                <p class="tc color-yellow f12">*获取更多资料</p>
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
    </section>
@stop

@section('endjs')
    <script src="{{URL::asset('/')}}/js/dist/swiper.min.js"></script>
	<script src="{{URL::asset('/')}}/js/brand2.js"></script>
    <script>  
    //  var args = getQueryStringArgs();
    // var uid = args['uid'] || 0,
    //     id = args['id'];
        // var swiper = new Swiper('.swiper-container', {
        //     pagination : '.swiper-pagination',
        //     paginationType : 'custom',
        //     autoplay:'5000',
        //     paginationCustomRender: function (swiper, current, total) {
        //         return '<span class="f16">'+current+'</span>' + ' / ' + total;
        //     }
        // });
        
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
        if (getScrollTop() + getWindowHeight() -100 >= getScrollHeight()) {
            // window.location.href=labUser.path+'webapp/brand/more?id=' + id + '&uid=' + uid +'&pagetag=08-9'
            toBrands(1);
            return false;
            // alert(1)
        }
    });

        
    </script>
@stop