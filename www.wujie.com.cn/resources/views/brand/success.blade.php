@extends('layouts.default')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/iscroll.css"/>
    <link href="{{URL::asset('/')}}/css/act_detail.css?v=1.0.0" rel="stylesheet" type="text/css"/>
     <link href="{{URL::asset('/')}}/css/w-pages.css?v=1.0.0" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <!-- 打开APP -->
    <div class="app_install none" id="installapp" style="position: absolute;z-index: 99">
        <i class="l">打开无界商圈APP，体验更多精彩内容 >></i>
        <span class="r" id="openapp"><img src="{{URL::asset('/')}}/images/install_btn.png" alt=""></span>
        <div class="clearfix"></div>
    </div>
    <section class="succ-sec">
        <div class="head relative" >
            <img src="{{URL::asset('/')}}/images/join_success.png" alt="" style="margin-top: 0.5rem;">
            <div class="info_head absolute">
                品牌信息
            </div>
        </div>
        <div class="succ-body">
            <ul>
                <li class="fline succ-li">
                    <span class="fl">品牌名称</span><span class="fr" id="brand_name"></span>
                </li>
                <li class="fline succ-li fline-none">
                    <span class="fl">分类</span><span class="fr" id="brand_sort"></span>
                </li>
                <li class="fline succ-li ">
                    <hr class="succ-bd">
                    <span class="fl">订单编号</span><span class="fr" id="order_no"></span>
                </li>
               
                <li class="fline succ-li">
                    <span class="fl">预付金额</span><span class="fr" id="good_price"></span>
                </li>
                 <li class="fline succ-li">
                    <span class="fl">积分使用</span><span class="fr" id="good_use"></span>
                </li>
                 <li class="fline succ-li">
                    <span class="fl">实际付款</span><span class="fr" id="good_pay"></span>
                </li>
                 <li class="fline succ-li">
                    <span class="fl">订单生成时间</span><span class="fr" id="good_btime"></span>
                </li>
                <li class="fline succ-li fline-none">
                    <span class="fl">支付时间</span><span class="fr" id="good_ptime"></span>
                </li>
                <li class="fline succ-li ">
                    <hr class="succ-bd">
                    <span class="fl">支付人</span><span class="fr" id="good_man"></span>
                </li>
                <li class="fline succ-li ">
                    <span class="fl">手机号</span><span class="fr" id="good_phone"></span>
                </li>
                <li class="fline succ-li ">
                    <span class="fl">所在地</span><span class="fr" id="good_add"></span>
                </li>
                <li class="fline succ-li fline-none">
                    <span class="fl">提交时间</span><span class="fr" id="good_stime"></span>
                </li>
                <div class="clearfix"></div>
            </ul>
        </div>
       
        <div class="succ-share" >
            <div class="share-bot">
                
            </div>
            <p class="color999 tc f12" style="margin-bottom: 0.5rem;">分享品牌给更多的人，让你获得更多创业基金</p>
            <p class="color999 tc f12">活动解释权归无界商圈所有</p>
            
        </div>
        <div style="width: 100%;height: 5rem;"></div>
        <div class="none">
            <img src="" alt="" id="brand_logo">;
            <div id="brand_sum"></div>

        </div>
    </section>

    <!--浏览器打开提示-->
    <div class="safari none">
        <img src="{{URL::asset('/')}}/images/safari.png">
    </div>
    <div class="none" id="video_title_none"></div>
    <div class="none" id="video_descript_none"></div>
    <div class="none" id="endtime_none"></div>
    <div class="isFavorite"></div>
    
@stop

@section('endjs')
    <script>
        Zepto(function () {
             new FastClick(document.body);
            var args= getQueryStringArgs()
            var uid =args['uid'],
                order_no = args['order_no'];
            var urlPath = window.location.href;
            var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;   
            //是否在分享页面
            function share(is_flag) {
                if (is_flag) {
                        $('#loadAppBtn').removeClass('none');
                        $('#installapp').removeClass('none');
                        $('.succ-share').addClass('none');
                        //浏览器判断
                        if (is_weixin()) {
                            /**微信内置浏览器**/
                            $(document).on('tap', '#loadapp,#openapp', function () {
                                var _height = $(document).height();
                                $('.safari').css('height', _height);
                                $('.safari').removeClass('none');
                            });
                            //点击隐藏蒙层
                            $(document).on('tap', '.safari', function () {
                                $(this).addClass('none');
                            });
                        }
                        else {
                            if (isiOS) {
                                //打开本地app
                                $(document).on('tap', '#openapp', function () {
                                    var strPath = window.location.pathname.substring(1);
                                    var strParam = window.location.search;
                                    var appurl = strPath + strParam;
                                    var share = '&is_share';
                                    var appurl2 = appurl.substring(0, appurl.indexOf(share));
                                    window.location.href = 'openwjsq://' + appurl2;
                                });
                                /**下载app**/
                                $(document).on('tap', '#loadapp', function () {
                                    window.location.href = 'https://itunes.apple.com/app/id981501194';
                                });
                            }
                            else if (isAndroid) {
                                $(document).on('tap', '#loadapp', function () {
                                    window.location.href = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.tyrbl.wujiesq';
                                });
                                $(document).on('tap', '#openapp', function () {
                                    // window.location.href = 'openwjsq://welcome'+ '/webapp/activity/detail?pagetag=02-2&uid=0&makerid=0&id='+act_id;
                                    window.location.href = 'openwjsq://welcome';
                                });
                            }
                        }
                }
            };
            //将手机号的第6位到第9位用*替换
            function phoneReplace(phone) {
                var phone = phone.toString();
                var phone1 = phone.substring(0,4),
                    phone2 = phone.substring(8),
                    ping = '****';
                phone3 = phone1+ping+phone2;
                return phone3;
            }
            //页面点击分享
            $(document).on('tap','.share-bot',function () {
                showShare()
            })
            //返回订单详情的函数
            function getDetail(result) {
                if (!shareFlag) {
                    setPageTitle('提交成功');
                }
                var res = result.entities[0],
                    brand = res.brand,
                    order =res.order;
                $('#brand_name').text(brand.brand_name);
                $('#brand_sort').text(brand.categorys_name);
                $('#order_no').text(order.order_no);
                $('#good_price').text(order.amount+'元');
                $('#good_use').text(order.score);
                $('#good_pay').text(order.online_money+'元');
                $('#good_btime').text(order.created_at_format);
                $('#good_ptime').text(order.pay_at);
                $('#good_man').text(order.realname);
                $('#good_phone').text(phoneReplace(order.mobile));
                $('#good_add').text(order.zone_name);
                $('#good_stime').text(order.created_at_format);  

                //隐藏的部分（分享的时候要调用这里的内容）
                $('#brand_logo').attr('src',brand.logo);
                $('#brand_sum').html(brand.summary).data('id',brand.id);
            }           
            // 返回接口的数据
            var joinSuccess = {
                //获得活动详情
                detail:function (uid,order_no) {
                    var param = {
                        "uid":uid,
                        "order_no":order_no
                    };
                    var url = labUser.api_path + '/order/verify';
                    ajaxRequest(param, url, function (data) {
                        if (data.status) {
                            //html调整
                            getDetail(data.message);
                        }
                    });
                },
            };
            joinSuccess.detail(uid,order_no);
            share(shareFlag);
        });
    </script>
    <script>
        //分享
        function showShare() {
            var title = $('#brand_name').text();
            var url = labUser.path+'webapp/brand/detail/_v020400?id='+$('#brand_sum').data('id')+'&is_share=1';
            var img = $('#brand_logo').attr('src');
            var header = '品牌';
            var content = cutString($('#brand_sum').text(), 25);
            shareOut(title, url, img, header, content);
        }
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
    </script>
@stop