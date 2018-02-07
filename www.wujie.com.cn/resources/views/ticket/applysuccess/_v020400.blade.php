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
    <section class="succ-sec none">
        <div class="head relative" >
            <img src="{{URL::asset('/')}}/images/apply_success.png" alt="" style="margin-top: 0.6rem;">
            <div class="info_head absolute">
                报名信息
            </div>
        </div>
        <div class="succ-body">
            <ul>
                <li class="fline succ-li">
                    <span class="fl">活动名称</span><span class="fr" id="succ_subject"></span>
                </li>
                <li class="fline succ-li fline-none">
                    <span class="fl">开始时间</span><span class="fr" id="succ_begin"></span>
                </li>
                <li class="fline succ-li ">
                    <hr class="succ-bd">
                    <span class="fl">参会人姓名</span><span class="fr" id="succ_name"></span>
                </li>
                <li class="fline succ-li fline-none">
                    <span class="fl">手机号</span><span class="fr" id="succ_phone"></span>
                </li>
                <li class="fline succ-li">
                    <span class="fl">公司</span><span class="fr" id="succ_company"></span>
                </li>
                <li class="fline succ-li fline-none">
                    <span class="fl">职位</span><span class="fr" id="succ_job"></span>
                </li>
                <li class="fline succ-li fline-none">
                    <hr class="succ-bd">
                    <span class="fl">票务类型</span><span class="fr" ><span id="succ_tic">什么票</span><em id="succ_price"></em></span>
                </li>
                <div class="clearfix"></div>
                <div id="succ_id" class="none" data-id=''></div>
                <img src="" id="succ_shareimg" class="none">
                <div id="succ_des" class="none"></div>
                <div id="citys" class="none"></div>
            </ul>

        </div>
        <p class="color999 f12 mt05">*报名订单已经放入你的账号，请登录无界商圈查看报名订单现场签到出示订单信息或提供无界商圈账号二维码即可完成签到工作</p>
        <div class="succ-foot">
            <div class="info_head ">
                已报名成员 (<span id="join_num"></span>)
            </div>
            <div class="succ-ava">
                <div class="ava-box">
                    <div id="avas-con" style="display: inline-block;"></div>
                    <!-- <div class="avas avas-more">
                        <div class="avas-head avas-head-more"></div>
                        <div class="avas-name"> </div>
                    </div> -->
                </div>
                <p class="tc f12 color999">如有报名疑问请拨打客服热线</p>
                <p class="tc f12 " style="color:#f63;padding-bottom: 2rem">400-011-0061</p>
               
            </div>
        </div>
        <div class="succ-share">
            <div class="share-bot">
                
            </div>
            <p class="color999 tc f12" style="margin-bottom: 0.5rem;">分享报名信息，邀请好友报名活动</p>
            <p class="color999 tc f12">赚取无界积分，获得活动绿色报名通道</p>
        </div>
        <div style="width: 100%;height: 5rem;"></div>
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

            if(!is_weixin()&&!isiOS&&!isAndroid){
                $('.succ-sec').css({'width':'740px','margin':'auto'});
                $('#installapp').remove();
            };
            // if(is_weixin()){
            //   $('.avas-head').css({'background':'url(../images/default/avator-m.png) no-repeat center','background-size':'100%  100%'});  
            // }
            window.document.title='报名成功';
            new FastClick(document.body);
            var args= getQueryStringArgs();
            var order_no =args['order_no'],
                uid = args['uid']||0,
                activity_id = args['activity_id'],
                ticket_id=args['ticket_id'];
            var sharemark = args['share_mark'];
            var code = args['code'];

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
                                    // var strPath = window.location.pathname.substring(1);
                                    // var strParam = window.location.search;
                                    // var appurl = strPath + strParam;
                                    // var share = '&is_share';
                                    // var appurl2 = appurl.substring(0, appurl.indexOf(share));
                                    // window.location.href = 'openwjsq://' + appurl2;
                                    window.location.href = 'openwjsq://'+ 'webapp/activity/detail/_v020400?pagetag=02-2&uid=0&makerid=0&id='+activity_id;
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
                                    window.location.href = 'openwjsq://welcome'+ '/webapp/activity/detail/_v020400?pagetag=02-2&uid=0&makerid=0&id='+activity_id;
                                });
                            }
                        }
                }
            };
            //页面点击分享
            $(document).on('tap','.share-bot',function () {
                showShare();
            })
            //活动详情列表的函数
            function getDetail(result) {
                if (!shareFlag) {
                    setPageTitle('报名成功');
                }
                result.job = result.job==''?'/':result.job;
                result.company = result.company==''?'/':result.company;
                result.name = result.name==''?'/':result.name;
                result.tel = result.tel==''?'/':result.tel;

                $('#succ_subject').text(cutString(result.subject,20));
                $('#succ_begin').text(result.begin_time);
                $('#succ_name').text(result.name);
                $('#succ_phone').text(result.tel);
                $('#succ_company').text(result.company);
                $('#succ_job').text(result.job);
                $('#succ_tic').text(result.type);
                $('#succ_id').data('id',result.id);
                $('#succ_price').html((result.score_price == '免费'||'' ? '免费' : result.score_price+'积分'));
                // if (result.score_price) {
                //     $('#succ_price').text(result.score_price+'积分');
                // }else{
                //     $('#succ_price').text('免费'); 
                // }
                // if (result.price=='免费') {
                //     $('#succ_price').text(result.price);
                // }
                // if(result.score_price==0||result.score_price=''){
                //     $('#succ_price').text('免费');
                // };
                // if(result.score_price>0){
                //     $('#succ_price').text(result.score_price+'积分');
                // }
            }
            //活动报名人数方法
            function joinDetail(result) {
                var joinNum = result.images.length;
                 $('#join_num').text(joinNum);
                var strMore= ['<div class="avas avas-more">',
                        '<div class="avas-head avas-head-more"></div>',
                        '<div class="avas-name"> </div>',
                    '</div>'].join('');
                if (joinNum<15) { //小于15人不显示人数
                    $('.succ-foot').addClass('none');
                }else{
                   
                    $.each(result.images,function (i,item) {
                        if (joinNum<19) {
                            $('.avas-head-more').addClass('none');
                        }else if(i>=19){
                            return false;
                        }
                        var str = [
                            '<div class="avas">',
                                '<div class="avas-head"><img id="image" src="'+item.image+'" alt=""></div>',
                                // '<div class="avas-name">'+cutString(item.name,3)+'</div>',
                            '</div>'
                        ].join('');
                        $('#avas-con').append(str);         
                    });
                    if (joinNum>=19) {
                        $('#avas-con').append(strMore);
                    }
                }
            }
            //获取活动的缩略图
            function actDetail(result) {
                $('#succ_shareimg').attr('src',result.share_image);
                $('#succ_des').text(result.description);
                $('#citys').text(result.activity_location);
            }
            //点击查看更多的报名人数（跳转到另一个页面）
            // $(document).on('click','.avas-head-more',function () {
            //     if (shareFlag) {
            //         window.location.href=labUser.path+'/webapp/activity/enrollment/_v020400?id='+$('#succ_id').data('id')+'&is_share=1';
            //     }else{
            //         window.location.href=labUser.path+'/webapp/activity/enrollment/_v020400?id='+$('#succ_id').data('id');
            //     }
                
            // })

            // 返回接口的数据
            var applySuccess = {
                //获得活动列表详情
                detail:function (order_no) {
                    var param = {
                        "order_no":order_no,
                    };
                    var url = labUser.api_path + '/activity/check-and-apply/_v020700';
                    ajaxRequest(param, url, function (data) {
                        if (data.status) {
                            //html调整
                            getDetail(data.message);
                            joinDetail(data.message);
                            $('.succ-sec').removeClass('none');
                        }
                    });
                },
                //获取活动的缩略图和简介
                act:function (id) {
                   var param = {
                        "id":id,
                    };
                    var url = labUser.api_path + '/activity/detail/_v020500';
                    ajaxRequest(param, url, function (data) {
                        if (data.status) {
                           actDetail(data.message);
                           // if (data.message.share_reward_unit != 'none') {
                           //       getReward(sharemark,'enroll',0,code);
                           // }
                        }
                    }); 
                }
            };
            applySuccess.detail(order_no);
            applySuccess.act(activity_id);
            // applySuccess.join(order_no);
            share(shareFlag);
             
        });

    </script>
    <script>
       
        //分享
        function showShare() {
            var type = 'Activity',
             title = $('#succ_subject').text();
             url = labUser.path+'webapp/activity/detail/_v020400?pagetag=02-2&id='+$('#succ_id').data('id')+'&is_share=1';
             img =  $('#succ_shareimg').attr('src');
             header = '活动',
             content = '我在无界商圈发现了一个不错的活动，想邀请你一起参加！';
             begintime = $('#succ_begin').text(),
             citys = $('#citys').text(),
             actid=$('#succ_id').data('id');
            shareOut(title, url, img, header, content, begintime, citys,actid,type);
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