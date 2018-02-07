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
    <section class="containerBox ">
            <!-- 活动介绍 -->
            <section id="act_intro" class="mt0">
               <!--  <div class="act_intro block mt0">
                    <dl>
                        <dt class="act_pics"><img src="" alt="" id="act_picsrc"></dt>
                        <dd class="act_name" id="act_name">活动标题</dd>
                        <div class="clearfix"></div>
                        <div class="act_address">
                            <dd class="time">
                                <span class="time_icon"></span>
                                <div class="infor" id="timetop">
                                    <p id="act_time">09/25 09:00 -  17:30</p>
                                </div>
                            </dd>
                            <div id="address_flag">
                                <dd class="address_list"><span class="address_icon"></span> <div class="infor"><p class="nameflag">杭州乐富智汇园OVO运营中心</p><p class="addressflag">杭州市拱墅区祥园路28号乐富智汇园8号楼1楼</p><span class="sj_icon"></span><div class="none ovodesp">利用线上平台（APP应用和WEB官网）将投资人、项目方及众创空间进行全面联盟。线下通过OVO高清视频设备进行跨域连接，实现远程资方面对面、专业导师创业辅导，跨域路演展示，直播录播大型活动等高品质服务，并提供录播剪辑视频分享。</div></div></dd>
                            </div>
                            <div>
                                <span class="wjb_icon"></span>  
                                <div class="infor bd0" id="timetop">
                                    <p >免费票 &nbsp;<em class="green"> 免费</em></p>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </dl>
                </div> -->
                <!-- <div class="applysuccess"></div> -->
            </section>       
        <!--分享出去按钮-->
        <!-- <div class="fixed_btn weixin none " id="loadAppBtn">
            <button class="signup" id="loadapp"><span class="downloadapp"></span>下载APP</button>
        </div> -->
       
        <!--浏览器打开提示-->
        <div class="safari none">
            <img src="{{URL::asset('/')}}/images/safari.png">
        </div>
        <div class="none" id="video_title_none"></div>
        <div class="none" id="video_descript_none"></div>
        <div class="none" id="endtime_none"></div>
        <div class="isFavorite"></div>
    </section>
@stop

@section('endjs')
    <script>
        Zepto(function () {
            var act_id ={{$id}};
            var urlPath = window.location.href+'&is_share=1';
            var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;   
            var isRegister = urlPath.indexOf('register=0') > 0 ? true : false;   
             //是否在分享页面
            function share(is_flag) {
                if (is_flag) {
                        $('#loadAppBtn').removeClass('none');
                        $('#installapp').removeClass('none');
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
                            var wxurl = labUser.api_path + '/weixin/js-config';
                            //活动详情描述
                            var desptStr = removeHTMLTag(selfObj.description);
                            var despt = cutString(desptStr, 60);
                            ajaxRequest({url: location.href}, wxurl, function (data) {
                                if (data.status) {
                                    wx.config({
                                        debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                                        appId: data.message.appId, // 必填，公众号的唯一标识
                                        timestamp: data.message.timestamp, // 必填，生成签名的时间戳
                                        nonceStr: data.message.nonceStr, // 必填，生成签名的随机串
                                        signature: data.message.signature, // 必填，签名，见附录1
                                        jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
                                    });
                                    wx.ready(function () {
                                        wx.onMenuShareTimeline({
                                            title: selfObj.subject, // 分享标题
                                            link: location.href, // 分享链接
                                            imgUrl: selfObj.detail_img, // 分享图标
                                            success: function () {
                                                // 用户确认分享后执行的回调函数
                                            },
                                            cancel: function () {
                                                // 用户取消分享后执行的回调函数
                                            }
                                        });
                                        wx.onMenuShareAppMessage({
                                            title: selfObj.subject,
                                            desc: despt,
                                            link: location.href,
                                            imgUrl: selfObj.detail_img,
                                            trigger: function (res) {
                                                // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
                                                console.log('用户点击发送给朋友');
                                            },
                                            success: function (res) {
                                                console.log('已分享');
                                            },
                                            cancel: function (res) {
                                                console.log('已取消');
                                            },
                                            fail: function (res) {
                                                console.log(JSON.stringify(res));
                                            }
                                        });
                                    });
                                }
                            });
                        }
                        else {
                            if (isiOS) {
                                //打开本地app
                                $(document).on('tap', '#openapp', function () {
                                    //var strPath = window.location.pathname.substring(1);
                                    //var strParam = window.location.search;
                                    //var appurl = strPath + strParam;
                                    //var share = '&is_share';
                                    //var appurl2 = appurl.substring(0, appurl.indexOf(share));
                                    window.location.href = 'openwjsq://' + 'webapp/activity/detail?pagetag=02-2&uid=0&makerid=0&id='+act_id;
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
                                    window.location.href = 'openwjsq://welcome'+ '/webapp/activity/detail?pagetag=02-2&uid=0&makerid=0&id='+act_id;
                                });
                            }
                        }
                }
            }
           
            var param ={
                "id" : {{$id}},
                "maker_id":{{$maker_id}}
            };
            var applySuccess = {
                //获得活动详情
                detail:function (id,maker_id,is_register) {
                    var param = {
                        "id":id,
                        "maker_id":maker_id
                    };
                    var url = labUser.api_path + '/activity/detail';
                    ajaxRequest(param, url, function (data) {
                        if (data.status) {
                            //html调整
                            getDetail(data.message,is_register);
                        }
                    });
                },
            };
            function getDetail(result,is_register) {
                var item= result.self;
                var ids = item.maker_ids.split('@');
                var addresses = item.address.split('@');
                var names = item.name.split('@');
                var maker_index;
                for (var i = 0; i < ids.length; i++) {
                    if (ids[i]==param.maker_id) {
                        maker_index=i;
                    }
                }
                var str=[
                '<div class="act_intro block mt0">',
                    '<dl>',
                        '<dt class="act_pics"><img src="'+item.list_img+'" alt="" id="act_picsrc"></dt>',
                        '<dd class="act_name" id="act_name">'+item.subject+'</dd>',
                        '<div class="clearfix"></div>',
                        '<div class="act_address" style="height:16rem;">',
                            '<dd class="time">',
                                '<span class="time_icon"></span>',
                                '<div class="infor" id="timetop">',
                                    '<p id="act_time">'+unix_to_datetime(item.begin_time)+'</p>',
                                '</div>',
                            '</dd>',
                            '<div id="address_flag">',
                                '<dd class="address_list">',
                                '<span class="address_icon"></span>',
                                 '<div class="infor"><p class="nameflag">'+names[maker_index]+'</p>',
                                 '<p class="addressflag">'+addresses[maker_index]+'</p>',
                                 '<span class="sj_icon"></span></div></dd>',
                            '</div>',
                            '<div>',
                                '<span class="wjb_icon"></span> ' ,
                                '<div class="infor bd0" id="timetop">',
                                    '<p >免费票 &nbsp;<em class="green"> 免费</em></p>',
                                '</div>',
                            '</div>',
                        '</div>',
                        '<div class="clearfix"></div>',
                    '</dl>',
                '</div>',
                '<div class="applysuccess"><img src="{{URL::asset('/')}}/images/wjerwei.png" alt="" / style="opacity:0"></div>'
                       
                ].join('');
                if(is_register) {
                    str+='<p class="appsuccess">欢迎成为无界商圈一员，账号、密码为报名所填手机号码。</p><p class="appsuccess">请及时<strong>登录无界商圈</strong>并对密码进行修改</p>'
                }               
                $('#act_intro').html(str); 
            }
       
            applySuccess.detail(param.id,param.maker_id,isRegister);
            $(document).on('tap','#address_flag',function(){
                // var maker_id=$(this).siblings('span').attr('data-id');
                window.location.href=labUser.path+'/webapp/activity/bmap?id='+param.id+'&maker_id='+param.maker_id;
            });
             share(shareFlag);
        });
    </script>
    <script>
        //分享
        // function showShare() {
        //     // shareOut('title', window.location.href, '', 'header', 'content');
        //     var title = $('#act_name').text();
        //     var url = window.location.href;
        //     var img = $('#act_picsrc').attr('src');
        //     var header = '报名成功';
        //     var content = cutString($('.addressflag').text(), 18);
        //     shareOut(title, url, img, header, content);
        // };                    
    </script>
@stop