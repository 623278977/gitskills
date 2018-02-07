<!-- Created by wcx -->

@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/packet.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section class="pt2 pb5 container none">
    <!-- 红包展示 -->
        <div class="packet_lump pl1 pr1 pb2">
            <p class ="pt2 pb2 tc cf13 f16 mb0">
                <img src="/images/title_left.png" alt="" class="title_img mr1">无界商圈投资人
                <img src="/images/title_left.png" alt="" class="title_img ml1 overturn">
            </p>
            <!-- 通用 -->
            <div id="packet_box">
                
            </div>
            <!-- <div class="packet_bg mb1">
                <div class="l cfefefe">
                    <p class="f14 b ">胖哥肉蟹煲</p>
                    <p class="mb0">加盟优惠抵扣红包名称</p>
                    <p class="mb0">有效期至2018.06.30</p>
                </div>
                <div class="r amount tc">
                    <p class="f28 cfefefe mb0"><span class="f14">￥</span>200</p>
                    <p class="cf13 f12 fullcut">满1000减20</p>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="" style="width:2.7rem;height: 0.14rem;border-radius: 0.07rem;background: #f13335;margin: auto;margin-bottom: 1rem">
            </div> -->
            <!-- 品牌 -->
           <!--  <div class="packet_bg mb1">
                <div class="l mr1">
                    <img src="/images/title_left.png" alt="" class="brand_img">
                </div>
                <div class="l cfefefe">
                    <p class="f14 b ">胖哥肉蟹煲</p>
                    <p class="mb0">加盟优惠抵扣红包名称</p>
                    <p class="mb0">有效期至2018.06.30</p>
                </div>
                <div class="r amount tc">
                    <p class="f28 cfefefe mb0"><span class="f14">￥</span>200</p>
                    <p class="cf13 f12 fullcut">满1000减20</p>
                </div>
                <div class="clearfix"></div>
            </div>
             -->
            <div>
                <div class="mt1-5 flex_between inapp">
                    <button class="btns bg_red" id="tobarnd">去【胖哥俩】品牌页面</button>
                    <button class="btns bg_red" id="manage_bag">管理我的红包</button>
                </div>
                <div class="mt1-5 flex_between shareout">
                    <button class="btns bg_blue" id="openApp">打开应用，访问品牌</button>
                    <button class="btns bg_yellow" id="loadApp">下载无界商圈App</button>
                </div>
                
            </div>
        </div>
        <div>
             <div class="cfefefe f12 tc pt1 pb2 common_title">
                通用红包在支付加盟费用首付款的时候可以进行现金抵扣<br>通用红包不支持转账、提现，仅用于支付抵扣
            </div>
            <div class="cfefefe f12 tc pt1 pb2 brand_title">
                品牌红包在支付加盟费用首付款的时候可以进行现金抵扣<br>品牌红包不支持转账、提现，仅用于支付抵扣
            </div>
            <div class="cfefefe f12 tc pt1 pb2 reward_title">
                奖励红包仅用于该品牌加盟时进行金额抵扣<br>该红包不支持转账、提现，仅用于支付抵扣
            </div>
        </div>
       
    <!-- 如何使用 -->
       
        <div class="packet_lump pl2 pr1-5 pb2">
         <!-- 通用红包 -->
            <div class="common none">
                <p class ="pt2 pb2 tc cf13 f16 mb0">
                    <img src="/images/title_left.png" alt="" class="title_img mr1"><span class="use_type">如何使用通用红包</span>
                    <img src="/images/title_left.png" alt="" class="title_img ml1 overturn">
                </p>
                <div class="flex_between align_center mb05">
                    <img src="/images/title_left.png" alt="" class="img_left mr1">
                    <div class="f12 color666">
                        <p class="mb05 lh1-5">在线上进进行考察订金、加盟首付款支付的时候，可以使用品牌红包进行抵扣。</p>
                        <p class="mb05 lh1-5">通用红包不限品牌。</p>
                        <p class="mb05 lh1-5">部分通用红包支持考察订金的抵扣。</p>
                        <p class="mb05 lh1-5">仅支持线上抵扣，不支持线下签约付款使用。</p>
                    </div>
                </div>
            </div>
        <!-- 品牌红包 -->
            <div class="brand none">
                <p class ="pt2 pb2 tc cf13 f16 mb0">
                    <img src="/images/title_left.png" alt="" class="title_img mr1">如何使用品牌红包
                    <img src="/images/title_left.png" alt="" class="title_img ml1 overturn">
                </p>
                <div class="flex_between align_center mb05">
                    <img src="/images/title_left.png" alt="" class="img_left l mr1 mb05">
                    <div class="f12 color666">
                        <p class="mb05 lh1-5">接受经纪人发送的“加盟合同”，同意“支付协议”。</p>
                        <p class="mb05 lh1-5">在线上进进行加盟首付款支付的时候，可以使用品牌红包进行抵扣。</p>
                        <p class="mb05 lh1-5">品牌红包仅可以抵扣该品牌的加盟首付费用。其他品牌无法抵扣。</p>
                        <p class="mb05 lh1-5">仅支持线上抵扣，不支持线下签约付款使用。</p>
                    </div>
                </div>
            </div>
        <!-- 奖励红包 -->
            <div class="reward none">
                <p class ="pt2 pb2 tc cf13 f16 mb0">
                    <img src="/images/title_left.png" alt="" class="title_img mr1">如何使用奖励红包
                    <img src="/images/title_left.png" alt="" class="title_img ml1 overturn">
                </p>
                <div class="flex_between align_center mb05">
                    <img src="/images/title_left.png" alt="" class="img_left l mr1 mb05">
                    <div class="f12 color666">
                        <p class="mb05 lh1-5">线上进行品牌加盟，支付首付款，除了常规的通用红包、品牌红包进行首付款金额抵扣，还有另一种奖励红包</p>
                        <p class="mb05 lh1-5">基于有效的门店考察，部分品牌会抽出奖励金额用户反馈用户，给予用户考察的车旅费用抵扣。</p>
                        <p class="mb05 lh1-5">这部分费用将用于加盟首付款的支付抵扣，与品牌红包或通用红包进行叠加使用。</p>
                    </div>
                </div>
            </div>
            <div class="f12 color666 none">
                <p class="mb05 lh1-5 pt05">请在红包有效期内进行使用，超过期限，则无法正常使用。</p>
                <p class="mb05 lh1-5">请确认红包是否支持叠加使用。</p>
                <p class="mb0 lh1-5">红包使用如有问题，请联系无界商圈客服人员或您的经纪人。</p>
            </div>
            
        </div>
        
    <!-- 如何获得 -->
        <div class="packet_lump mt2 pl2 pr1-5 pb2 " id="blend">
            <p class ="pt2 pb2 tc cf13 f16 mb0">
                <img src="/images/title_left.png" alt="" class="title_img mr1"><span class="get_type">如何获得品牌红包</span>
                <img src="/images/title_left.png" alt="" class="title_img ml1 overturn">
            </p>
            <!-- 通用和品牌 -->
            <div class="com_brand none">
                <div class="flex_between align_center mb05" >
                    <img src="/images/title_left.png" alt="" class="img_left l mr1 mb05">
                    <div class="f12 color666">
                        <p class="mb05 method">方法一</p>
                        <p class="mb05 lh1-5">关注无界商圈，我们定期会组织线上活动，为你发放品牌红包。</p>
                        <p class="mb05 lh1-5">数量有限，先到先得</p>
                
                    </div>
                </div>
                <div class="flex_between align_center mb05" >
                    <img src="/images/title_left.png" alt="" class="img_left l mr1 mb05">
                    <div class="f12 color666">
                        <p class="mb05 method">方法二</p>
                        <p class="mb05 lh1-5">我们会定期发放红包，当你打开无界商圈应用，会惊喜的发现，有红包——降临了！</p>
                        <p class="mb05 lh1-5">对！赶紧领取红包，加盟品牌优惠更多！</p>
                
                    </div>
                </div>
            <!-- 通用红包时 -->
                <div class="flex_between align_center mb05 common none" >
                    <img src="/images/title_left.png" alt="" class="img_left l mr1 mb05">
                    <div class="f12 color666">
                        <p class="mb05 method">方法三</p>
                        <p class="mb05 lh1-5">获得经纪人的邀请码，输入手机号获得相应的通用红包！</p>
                
                    </div>
                </div>
            <!-- 品牌时 -->
                <div class="flex_between align_center mb05 brand none" >
                    <img src="/images/title_left.png" alt="" class="img_left l mr1 mb05">
                    <div class="f12 color666">
                        <p class="mb05 method">方法三</p>
                        <p class="mb05 lh1-5">分享红包，让红包滚动起来！</p>
                        <p class="mb05 lh1-5">通过“红包”按钮分享，集齐邀请筹码，获得品牌红包！</p>
                        <p class="mb05 lh1-5">更有机会获得大红包！</p>
                
                    </div>
                </div>
                <div class="flex_between align_center mb05" >
                    <img src="/images/title_left.png" alt="" class="img_left l mr1 mb05">
                    <div class="f12 color666">
                        <p class="mb05 method">方法四</p>
                        <p class="mb05 lh1-5">经纪人获得无界商圈给予的“福袋”，可以将福袋中的红包分享至投资人</p>
                        <p class="mb05 lh1-5">快，赶紧联系你的经纪人，让他看看福袋里是否已经装满了惊喜！</p>
                        <p class="mb05 lh1-5">* 如经纪人福袋无红包，属于正常情况。红包为随机派发，存在暂无红包情况</p>
                    </div>
                </div>
                <div class="color666 f12">
                    <p class="mb05 lh1-5">赶紧获得无界商圈品牌红包！</p>
                    <p class="mb0 lh1-5">让你的创业加盟，更轻松！</p>
                </div>
            </div>
            <!-- 奖励红包 -->
            <div class="reward_pack none">
                <div class="flex_between align_center mb05" >
                    <img src="/images/title_left.png" alt="" class="img_left l mr1 mb05">
                    <div class="f12 color666">
                        <p class="mb05 lh1-5">奖励红包的获得，是通过接受经纪人发送的考察邀请函，并对品牌进行有效的门店考察</p>
                        <p class="mb05 lh1-5">* 以实际品牌为准，部分品牌未设置奖励红包。用户进行考察，并不会获得相应奖励。</p>
                        <p class="mb05 lh1-5">如有异议，请联系无界商圈客服。</p>
                    </div>
                </div>
            </div>
            
        </div>
    <!-- 无界商圈logo -->
        <div class="tc mt2">
            <img src="/images/wjsq_logo.png" alt="" style="width:10.6rem;height: 3rem;">
        </div>
    </section>
@stop
@section('endjs')
    <script src="{{URL::asset('/')}}/js/packet.js"></script>
    <script>
      var $body = $('body');
      document.title = "无界商圈红包";
      // hack在微信等webview中无法修改document.title的情况
      var $iframe = $('<iframe ></iframe>').on('load', function() {
      setTimeout(function() {
      $iframe.off('load').remove()
      }, 0)
      }).appendTo($body);
  </script> 
    <script type="text/javascript">
      Zepto(function(){
         new FastClick(document.body);
         var reg=/^\d{10,11}$/;
         var args = getQueryStringArgs();
         var ids = args['id'] || 0;
         var uid = args['uid'] || 0;
         var shareFlag = window.location.href.indexOf('is_share') >0 ? true : false;
            ids= ids.substring(1,ids.length-1);
         var rurl = labUser.api_path + '/redpacket/receive-success/_v020902';
         ajaxRequest({'uid':uid,'id':ids},rurl,function(data){
            if(data.status){
                if(data.message.length > 0){
                    var normal = '',brand = '',reward = '';
                    $.each(data.message,function(i,j){
                        if(j.type== 1){
                            normal += '<div class="packet_bg mb1"><div class="l cfefefe"><p class="f14 b ">'+j.name+'</p>';
                            normal += '<p class="mb0">'+j.description+'</p><p class="mb0">'+j.expire_at+'</p></div>';
                            normal += '<div class="r amount tc"><p class="f28 cfefefe mb0"><span class="f14">￥</span>'+j.amount+'</p>';
                            if(j.min_consume){
                                normal += '<p class="cf13 f12 fullcut">满'+j.min_consume+'减'+j.amount+'</p>';
                            }
                            normal += '</div><div class="clearfix"></div> </div>';
                            
                        }
                        if(j.type == 2){
                            brand +=' <div class="packet_bg mb1"><div class="l mr1"><img src="'+j.brand_logo+'" alt="" class="brand_img"></div><div class="l cfefefe"><p class="f14 b ">'+j.brand_name+'</p><p class="mb0">'+j.description+'</p><p class="mb0">'+j.expire_at+'</p></div><div class="r amount tc"><p class="f28 cfefefe mb0"><span class="f14">￥</span>'+j.amount+'</p>'
                            if(j.min_consume){
                                brand += '<p class="cf13 f12 fullcut">满'+j.min_consume+'减'+j.amount+'</p>'
                            }
                            brand += '</div><div class="clearfix"></div></div>';
                            data.message.length == 1 ? $('#tobarnd').text('去['+j.brand_name+']品牌页面').attr('data-id',j.id) : '';
                        }
                        if(j.type == 4){
                           reward += '<div class="packet_bg mb1"><div class="l cfefefe"><p class="f14 b ">'+j.name+'</p>';
                            reward += '<p class="mb0">'+j.description+'</p><p class="mb0">'+j.expire_at+'</p></div>';
                            reward += '<div class="r amount tc"><p class="f28 cfefefe mb0"><span class="f14">￥</span>'+j.amount+'</p>';
                            if(j.min_consume){
                                reward += '<p class="cf13 f12 fullcut">满'+j.min_consume+'减'+j.amount+'</p>';
                            }
                            reward += '</div><div class="clearfix"></div> </div>';
                        }

                    })

                    if(normal != ''){
                        $('.common').removeClass('none');
                        $('.brand').removeClass('flex_between');
                        $('.com_brand').removeClass('none');
                        $('.common_title').removeClass('none');
                        $('.common_title').siblings().css('display','none');
                        $('.get_type').text('如何获得通用红包')
                    }
                    if(brand != ''){
                        $('.brand').removeClass('none');
                        $('.common').removeClass('flex_between');
                        $('.com_brand').removeClass('none');
                        $('.brand_title').removeClass('none');
                        $('.brand_title').siblings().css('display','none');
                        $('.get_type').text('如何获得品牌红包')

                    }
                    if(reward != ''){
                        $('.reward_title').removeClass('none');
                        $('.reward_title').siblings().css('display','none');
                        $('.reward').removeClass('none');
                        $('.reward_pack').removeClass('none');

                    }

                    if(normal != '' && brand != ''){
                        var lineHtml = '<div class="" style="width:2.7rem;height: 0.14rem;border-radius: 0.07rem;background: #f13335;margin: auto;margin-bottom: 1rem"></div>';
                        $('#packet_box').html(normal + lineHtml + brand + reward);
                        $('.brand_title').css('display','none');
                        $('#blend').addClass('none');
                    }else{
                        $('#packet_box').html(normal + brand + reward);
                    }
                    
                };
                if(shareFlag){
                    $('.inapp').css('display','none');
                }else{
                    $('.shareout').css('display','none');
                }

            }
            $('.container').removeClass('none');
         })
    //去品牌
        $(document).on('click','#tobarnd',function(){
            var id = $(this).attr('data-id');
            if(id){ 
                window.location.href = labUser.path + '/webapp/brand/detail/_v020902?id='+id+'&uid='+uid;
            }else{
                toBrandList();
            }
        })

    //管理我的红包
       $(document).on('click','#manage_bag',function(){
            toPacketList()
       })

    //打开应用
      $(document).on('click','#openApp',function(){
            openApp();
      })
    //下载APP
      $(document).on('click','#loadApp',function(){
        if(isiOS){
            window.location.href = 'https://itunes.apple.com/app/id981501194';
        }else if(isAndroid){
            window.location.href = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.tyrbl.wujiesq';
        }
      })
    //去品牌列表
        function toBrandList(){
            if (isAndroid) {
                javascript:myObject.toBrandList();
            } 
            else if (isiOS) {
                var message = {
                    method : 'toBrandList',
                    params:{}
                }; 
                window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
            }
        }
    //去红包列表
        function toPacketList(){
            if (isAndroid) {
                javascript:myObject.toPacketList();
            } 
            else if (isiOS) {
                var message = {
                    method : 'toPacketList',
                    params:{}
                }; 
                window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
            }
        }

    //打开本地
        function openApp(){
            var strPath = window.location.pathname;
            var strParam = window.location.search.replace(/is_share=1/g, '');
            var appurl = strPath + strParam; 
            if(isiOS){
                 window.location.href = 'openwjsq://' + appurl2;
            }else if(isAndroid){
                window.location.href = 'openwjsq://welcome/' + appurl;
            }
        }
        // function openAndroid(){
        //     var strPath = window.location.pathname;
        //     var strParam = window.location.search.replace(/is_share=1/g, '');
        //     var appurl = strPath + strParam;
        //     console.log(appurl);
        //     // window.location.href = 'openwjsq://welcome' + appurl;
        // }
        // function oppenIos(){
        //     var strPath = window.location.pathname.substring(1);
        //     var strParam = window.location.search;
        //     var appurl = strPath + strParam;
        //     var share = '&is_share';
        //     var appurl2 = appurl.substring(0, appurl.indexOf(share));
        //     console.log(appurl);
        //     // window.location.href = 'openwjsq://' + appurl2;
        // }
        
      })
    </script>
@stop