@extends('layouts.default')
@section('css')
    <link href="/css/_v020902/fudai.css" rel="stylesheet" type="text/css"/>
    <link href="/css/agent/_v010300/sharered.css" rel="stylesheet" type="text/css"/>
    <link href="/css/animate.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section class="container con-bg" style="overflow: hidden;">
        <div class="share">
            <p class="share-p">您的好友<span class="invite_name"></span>赠了一份新年大礼 </p>
            <div class="packet">
                <div class="packet-txt">
                    <p class="con-1">愿你在新的一年里,马到成功,心想事成 ~</p>
                    <p class="con-2">无界商圈为你提供更优质的品牌加盟
                        服务，更丰厚的创业助力基金！
                    </p>
                    <p class="con-3">只因为你，与你一起在2018年，一起进步！</p>
                </div>
            </div>
            <div>
             <button class="btn btn1" type="button" id="open">开启红包</button>
            </div>
        </div>
        <div class="module none">
            <!-- //注册弹窗 -->
             <div class="loginup none ">
                <p class="f18 mb2">登录无界商圈</p>
                <form action="">
                    <input type="text" placeholder="输入手机号" id="phoneNum">
                    <div class="mt1 mb1-33" style="position: relative" >
                        <input type="text" placeholder="输入手机验证码" id="mesCode">
                        <button class="btn btn2" type="button" id="getCode">获取验证码
                        </button>
                    </div>
                    <p class="color999 f12" style="text-align: left">新用户我们将为您自动创建账户，请届时下载应用查看红包，及使用方法。</p>
                    <button class="btn btn3" id="login" type="button">登录领取红包</button>
                    <input type="reset" style="display: none" id="reset">
                </form>
            </div>
        <!-- 红包样式弹窗 -->
            <div class="got none animated bounceIn ">
               <p class="f20 white">恭喜获得红包</p>
               <p class="white f11 tl pt1 pb1">成功打开了来自经纪人：<span class="invite_name"></span><span id="agent_num"></span>的红包大礼！</p>
               <div class="packetBox bgwhite border-radius03 pt05 pb05">
                   <div class="l cff0 w35">
                       <p class="mt1 f14">￥<span class="f20 amount"></span></p>
                       <p class="f11">通用红包</p>
                   </div>
                   <div class="l color999 f10 tl w65">
                       <p class="color333 f20 tl">新手通用红包</p>
                       <p class="tl" >不限使用期限，全场通用红包</p>
                       <p class="tl" >可用于考察订金抵扣、品牌加盟费用抵扣</p>
                   </div>
                   <div class="clearfix"></div>
               </div>
                <button type="button" class="Ikonw">朕知道了</button>
            </div>
        
        </div>
        <div class="bgwrap none">
        </div>
        <div class="tips none"></div>
    </section>
    <section class="openPacket none">
         <!-- 打开红包 -->
        <div>
            <header class="animated zoomInLeft">
                 <img class="ui-size1" src="/images/default/avator-m.png">
             </header>
             <p class="ui-a color333 f16 b mt33 animated wobble">经纪人：<span class="invite_name"></span></span>的红包</p>
             <p class="ui-b color999 f12 b animated wobble mb1">为你加盟创业加油助力！</p>
             <div class="packetBox bgwhite border-radius03 pt05 pb05 w96">
                   <div class="l cff0 w35">
                       <p class="f14">￥<span class="f20 amount"></span></p>
                       <p class="f11">通用红包</p>
                   </div>
                   <div class="l color999 f10 tl w65">
                       <p class="color333 f18 tl">新手通用红包</p>
                       <p class="tl" >不限使用期限，全场通用红包</p>
                       <p class="tl" >可用于考察订金抵扣、品牌加盟费用抵扣</p>
                   </div>
                   <div class="clearfix"></div>
               </div>
             <div class="ui-tips f11 color999 animated zoomInLeft">红包自动存入您的红包库，请在有效期内尽快使用</div>
             <div class="pt1  flex_between shareout w96">
                    <button class="btns bg_blue">打开应用查看红包</button>
                    <button class="btns bg_yellow">下载无界商圈投资人</button>
            </div>
            <div style="width:3.3rem;height: 1px;margin:2rem auto;background: #b7b7b7;"></div>
             <div class="ui-how-use animated zoomInLeft">
                 <div class="common pl2 pr1-5 pb2">
                    <p class ="pt2 pb2 tc cf13 f16 mb0">
                        <img src="/images/title_left.png" alt="" class="title_img mr1"><span class="use_type">如何使用通用红包</span>
                        <img src="/images/title_left.png" alt="" class="title_img ml1 overturn">
                    </p>
                    <div class="flex_between align_center mb05">
                        <img src="/images/title_left.png" alt="" class="img_left mr1">
                        <div class="f12 color666">
                            <p class="mb05 lh1-5 tl">在线上进进行考察订金、加盟首付款支付的时候，可以使用品牌红包进行抵扣。</p>
                            <p class="mb05 lh1-5 tl">通用红包不限品牌。</p>
                            <p class="mb05 lh1-5 tl">部分通用红包支持考察订金的抵扣。</p>
                            <p class="mb05 lh1-5 tl">仅支持线上抵扣，不支持线下签约付款使用。</p>
                        </div>
                    </div>
                </div>
             </div>
             <div class="ui-how-use animated zoomInLeft">
                 <div class="packet_lump mt2 pl2 pr1-5 pb2">
                    <p class ="pt2 pb2 tc cf13 f16 mb0">
                        <img src="/images/title_left.png" alt="" class="title_img mr1"><span class="get_type">如何获得通用红包</span>
                        <img src="/images/title_left.png" alt="" class="title_img ml1 overturn">
                    </p>
                    <div class="com_brand">
                        <div class="flex_between align_center mb05" >
                            <img src="/images/title_left.png" alt="" class="img_left l mr1 mb05">
                            <div class="f12 color666">
                                <p class="mb05 method">方法一</p>
                                <p class="mb05 lh1-5">关注无界商圈，我们定期会组织线上活动，为你发放通用红包。</p>
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
                        <div class="flex_between align_center mb05" >
                            <img src="/images/title_left.png" alt="" class="img_left l mr1 mb05">
                            <div class="f12 color666">
                                <p class="mb05 method">方法三</p>
                                <p class="mb05 lh1-5">获得经纪人的邀请码，输入手机号获得相应的通用红包！</p>
                        
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
                </div>
             </div>
             <center class="mt2"><img class="ui-size6" src="/images/wujie.png"></center>
             <div style="width:100%;height:10rem"></div>
             <footer class="f14 f28"><a href="tel:400-011-0061"></a>联系我们</footer>
        </div>
    </section>
    
@stop
@section('endjs')
   <script>
   	    $(document).ready(function(){
   	    	$('title').text('无界商圈红包')
   	    })
    Zepto(function(){
        new FastClick(document.body);
        var reg=/^\d{10,11}$/;
        var args = getQueryStringArgs();
        var agent_id = args['agent_id'] || 0;
        var url = labUser.agent_path + '/user/invite-slogan/_v010300';
        ajaxRequest({'agent_id':agent_id,'type':'customer'},url,function(data){
            if(data.status){
                $('.invite_name').text(data.message.is_public_realname == 1?data.message.realname : data.message.nickname);
                $('#agent_num').text('('+data.message.phone+')');
                $('.ui-size1').attr('src',data.message.avatar);
            }
        })

        $(document).on('click','#open,.packet',function(){
            if($('.amount').text() == ''){
                $('.bgwrap').removeClass('none');
                $('.module').removeClass('none');
                $('.loginup').removeClass('none');
            }else{
                $('.con-bg').removeClass('none')
                $('.openPacket').removeClass('none');
            }
            
        })

        $('.bgwrap').on('tap',function(){
            $('.bgwrap').addClass('none');
            $('.module>div').addClass('none');
            $('#reset').click();
        })

        //发送验证码
        function sendCode(username,type,nation_code,app_name){
            var param = {};
                param.username = username;
                param.type = type;
                param.nation_code = nation_code;
                param.app_name = app_name;
            var url = labUser.api_path + '/identify/sendcode/_v020900';
            ajaxRequest(param,url,function(data){
                if(data.status){
                    console.log('发送验证码成功');
                }else{
                     tips(data.message)
                }
            })

        }

        //注册
        //
        function signIn(nickname,username,agent_id,code){
            var param = {};
                param.nickname = nickname;
                param.username = username;
                param.agent_id = agent_id;
                param.code =code;
                param.type = 'standard';
                param.phone_code= '86';
            var url = labUser.agent_path + '/user/customer-register/_v010300'
            ajaxRequest(param,url,function(data){
                if(data.status){
                    var uid = data.message.uid;
                    getPacket(uid);
                }else if(data.message=='has_register' || data.message=='is_self_register'){
                    tips('您已注册过，无法领取新人红包');
                    $('#login').text('登陆领取红包');
                    $('#reset').click();
                }else{
                    tips(data.message);
                     $('#login').text('登陆领取红包')
                }
            })
        }
    // 领红包
        function getPacket(uid){
            var agentUrl = labUser.agent_path + '/user/register-customer-result/_v010300';
            ajaxRequest({'uid':uid},agentUrl,function(data){
                if(data.status){
                    $('#reset').click();
                    $('.loginup').addClass('none');
                    $('.module').removeClass('none');
                    $('.got').removeClass('none');
                    $('.amount').text(parseInt(data.message.amount));
                    $('#login').text('登陆领取红包')
                }
            })
        }

    //获取验证码
        $('#getCode').on('tap',function(){
            var username = $('#phoneNum').val();
            var app_name = 'wjsq';
            if(username == ''){
                tips('手机号码不能为空');
                return;
            }else if(!reg.test(username)){
                tips('手机格式不正确');
                return;
            }else{
                time($('#getCode'))
                sendCode(username,'standard','86',app_name);
            }
        })
    // 登陆并领取红包
        $('#login').on('tap',function(event){
            event.stopPropagation() ;
            var username = $('#phoneNum').val();
            var code = $('#mesCode').val();
            if(username == ''){
                tips('手机号码不能为空');
                return;
            }else if(!reg.test(username)){
                tips('手机格式不正确');
                return;
            }else if(code == ''){
                tips('验证码不能为空');
                return;
            }else{
                $('#login').text('请稍等，正在为您分配红包...')
                signIn('',username,agent_id,code)
            }
            return;

        })
        //朕知道了
        $(document).on('click','.Ikonw',function(){
            $('.loginup').addClass('none');
            $('.module').addClass('none');
            $('.got').addClass('none');
            $('.bgwrap').addClass('none');
            $('.container').addClass('none');
            $('.openPacket').removeClass('none');

        })
        //打开应用查看
        $('.bg_blue').click(function(){
            if(isiOS){
                oppenIos();
            }else if(isAndroid){
                openAndroid();
            }
        })
        //下载App
        $('.bg_yellow').click(function(){
            if (isiOS) {
                window.location.href = 'https://itunes.apple.com/app/id981501194';
            }else if(isAndroid){
                window.location.href = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.tyrbl.wujiesq';
            }
        })
         //提示框
         function tips(e) {
            $('.tips').text(e).removeClass('none');
            setTimeout(function() {
              $('.tips').addClass('none ');
            }, 1500);
        }
    //验证码倒计时
        var wait = 60;
        function time(o){
            if (wait == 0) {
                o.removeAttr("disabled");
                o.html("重新发送");
                o.css({
                  "font-size":"15px",
                  "color":'color999',
                  "background":"#ffffff"
                });
                wait = 60;
            }else {
                o.attr("disabled", true);
                o.css({
                  "font-size":"15px",
                  "color":'color999',
                  "background":"#ffffff"
                });
                o.html('重新发送(' + wait + 's)');
                wait--;
                tt = setTimeout(function () {
                       time(o)
                    },
                    1000)
            }
        };

        //打开本地--Android
        function openAndroid(){
            var strPath = window.location.pathname;
            var strParam = window.location.search.replace(/is_share=1/g, '');
            var appurl = strPath + strParam;
            window.location.href = 'openwjsq://welcome' + appurl;
        }
        function oppenIos(){
            var strPath = window.location.pathname.substring(1);
            var strParam = window.location.search;
            var appurl = strPath + strParam;
            var share = '&is_share';
            var appurl2 = appurl.substring(0, appurl.indexOf(share));
            window.location.href = 'openwjsq://' + appurl2;
        }
    
    })

   </script>
@stop