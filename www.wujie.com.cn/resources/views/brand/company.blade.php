
@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/brand.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/dist/swiper.min.css" rel="stylesheet" type="text/css"/>
    <style>

        .swiper-pagination-bullet {
            width: 8px;
            height: 8px;
            display: inline-block;
            border-radius: 100%;
            background: #fff;
            opacity: 0.7;
        }
         .swiper-pagination-bullet-active{
            background-color: #fff;
            opacity: 1;
        }
    </style>
@stop
@section('main')
    <section>
         <!--安装app-->
        <div class="install-app none" id="installapp">
            <img src="{{URL::asset('/')}}/images/dock-logo.png" alt="">
            <div class="fl pl1">
                <span>无界商圈</span><br>
                <span>用无界商圈找无限商机</span>
            </div>
            <a href="javascript:;" class="install-close f24">×</a>
            <a href="javascript:;" class="install-open" id="openapp">立即开启</a>
        </div>
        <div class="brand-info white-bg mb1-5 mt1-5 pl1-33" id="company_con">
            <div class="info-head fline">
                <span class="tleft f16w lh45 ">公司摘要</span>
            </div>
            <div class="box white-bg" id="company_sum">
                
            </div>
           
        </div>
        <div class="brand-info white-bg mb5 " id="brand_com">
            <div class="info-head ">
                <span class="tleft f16w lh45 pl1-33">公司资质</span>
            </div>
            <div class=" white-bg">
                <div class="swiper-container">
                    <div class="swiper-wrapper" id="company_wrap">
                        <!-- <div class="swiper-slide"><img src="{{URL::asset('/')}}/images/act_banner.png" alt=""></div>
                        <div class="swiper-slide"><img src="{{URL::asset('/')}}/images/act_banner.png" alt=""></div>
                        <div class="swiper-slide">Slide 3</div> -->
                    </div>
                    <div class="swiper-pagination"></div>
                </div>
            </div>
           
        </div>
         <!--浏览器打开提示-->
        <div class="safari none">
            <img src="{{URL::asset('/')}}/images/safari.png">
        </div>
    </section>
@stop

@section('endjs')
    <!-- <script src="{{URL::asset('/')}}/js/zepto/swiper.min.js"></script> -->
     <script src="{{URL::asset('/')}}/js/dist/swiper.min.js"></script>
     <!-- <script src="{{URL::asset('/')}}/js/brand.js"></script> -->
    <script>
     //判断版本来调整顶部的悬浮条
        // if (window.location.href.indexOf('_v020502')!=-1) {
        //     $('.install-app').addClass('install-app2');
        //     $('#installapp img').attr('src','{{URL::asset('/')}}/images/dock-logo2.png')
        // }
        window.document.title ='公司详情';
        var args = getQueryStringArgs();
    var uid = args['uid'] || 0,
        id = args['id'];
    var urlPath = window.location.href;
    var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
    var shareUrl = urlPath.indexOf('is_share') > 0 ? '&is_share=1' : '';
    if (shareFlag) {
        uid=0;
        $('.install-app').removeClass('none');
        if (is_weixin()) {
                /**微信内置浏览器**/
                $(document).on('tap', '#brand_loadAPP,#openapp', function() {
                    var _height = $(document).height();
                    $('.safari').css('height', _height);
                    $('.safari').removeClass('none');
                });
                // 点击隐藏蒙层
                $(document).on('tap', '.safari', function() {
                    $(this).addClass('none');
                });
        }else {
                if (isiOS) {
                    //打开本地a
                    $(document).on('tap', '#openapp', function() {
                        var strPath = window.location.pathname.substring(1);
                        var strParam = window.location.search;
                        var appurl = strPath + strParam;
                        var share = '&is_share';
                        var appurl2 = appurl.replace(/is_share=1/g, '');
                        window.location.href = 'openwjsq://' + appurl2;
                    });
                    /**下载app**/
                    $(document).on('tap', '#brand_loadAPP', function() {
                        window.location.href = 'https://itunes.apple.com/app/id981501194';
                    });
                } else if (isAndroid) {
                    $(document).on('tap', '#brand_loadAPP', function() {
                        window.location.href = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.tyrbl.wujiesq';
                    });
                    $(document).on('tap', '#openapp', function() {
                        var strPath = window.location.pathname;
                        var strParam = window.location.search.replace(/is_share=1/g, '');
                        var appurl = strPath + strParam;
                        window.location.href = 'openwjsq://welcome' + appurl;
                    });
                }
            };
    }
        var company = {
            detail: function(id, uid) {
                var param = {};
                param['id'] = id;
                param['uid'] = uid;
                var url = labUser.api_path + '/brand/detail/_v020500';
                ajaxRequest(param, url, function(data) {
                    if (data.status) {
                        getDetail(data.message);
                       var mySwiper = new Swiper('.swiper-container',{
                        pagination : '.swiper-pagination',
                        paginationType : 'bullets',
                        //pagination : '#swiper-pagination1',
                        })
                    }
                })
            },
        };
        function getDetail(result) {
            var brand = result.brand;
            // console.log('dd');
            console.log(brand.qualifyImages.length);
            console.log(brand.qualifyImages.business_licence);
            $('#company_sum').html(brand.summary);
            
            $.each(brand.qualifyImages, function(i, item) {
                var str = '';
                // for (var i = 0; i < brand.qualifyImages.length; i++) {
                    if (item=='') {
                        str+='';
                    }else{
                         str += ' <div class="swiper-slide"><img src="' + item + '" alt=""></div>';
                    }
                   
                // }
                $('#company_wrap').append(str);
            });

            if (brand.qualifyImages.business_licence==undefined) {
                $('#brand_com').addClass('none');
            }
        };
        company.detail(id,uid);
      
        
        
        // title
        function setPageTitle(title) {
            if (isAndroid) {
                javascript:myObject.setPageTitle(title);
            } 
            else if (isiOS) {
                var data = {
                   "title":title
                }
                window.webkit.messageHandlers.setPageTitle.postMessage(data);
            }
        }
        setPageTitle('公司详情');
    </script>
@stop