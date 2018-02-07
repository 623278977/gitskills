@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/_v010100/christmas_activity.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section id="act_container" >
        <div class="rule">
                规则
        </div>
        <div class="chr_head">
            <img src="/images/agent/christmas/top.png" alt="">
            <img src="/images/agent/christmas/middle.png" alt="">
            <img src="/images/agent/christmas/bottom.png" alt="">
        </div>
<!-- 商圈任务书 -->
        <div class="chr_body pl1-5 pr1-5 pb1 relative">
            
            <div class="tc mb2">
                <span class="che_title f18 white">商圈任务书</span>
            </div>
            <div class="pl1-5 pr1-5 white f11 tc mb2-5">
                根据提示，完成无界商圈《商圈任务书》，获得丰厚奖金的时更能登顶”英雄榜“，开启更多意外惊喜！心动不如行动，小伙伴们，准备好了吗？
            </div>
            <div class="uniques none">
                <div>
                  <div class="flex_start">
                    <div class="skill">绝招1</div>
                        <div class="ml1">
                            <p class="f12 color666 mb0 mt1">分享到朋友圈，集满30个赞</p>
                            <p class="f15">奖励<span class="cf24d57">5</span>元</p>
                        </div>
                    </div>
                   <div class="skill_btn f14 " id="screenshot">
                        上传截屏
                    </div>
                    
                </div>
                <div>
                    <div class="flex_start">
                        <div class="skill">绝招2</div>
                        <div class="ml1">
                            <p class="f12 color666 mb0 mt1">邀请1位投资人参加OVO品牌招商会</p>
                            <p class="f15">奖励<span class="cf24d57">80</span>元</p>
                        </div>  
                    </div>
                    <div class="skill_btn f14" id="invite_act">
                            邀请活动
                    </div>
                </div>
                <div>
                    <div class="flex_start">   
                        <div class="skill">绝招3</div>
                         <div class="ml1">
                            <p class="f12 color666 mb0 mt1">邀请投资人实体考察，并交付订金</p>
                            <p class="f15">奖励<span class="cf24d57">200</span>元</p>
                        </div>
                     </div>
                    <div class="skill_btn f14" id="invitations">
                        邀请考察
                    </div>  
                </div>
                <div>
                    <div class="flex_start">
                        <div class="skill">绝招4</div>
                        <div class="ml1">
                            <p class="f12 color666 mb0 mt1">邀请投资人在平台成功加盟品牌</p>
                            <p class="f15">奖励<span class="cf24d57">1000</span>元</p>
                        </div>
                    </div>
                    <div class="skill_btn f14 " id="investors">
                        邀请投资人
                    </div>   
                </div>
            </div>
        <!-- 半透明大片雪花 -->
            <div>
                <div class="fir_snow"></div>
                <div class="sec_snow"></div>
                <div class="thi_snow"></div>
                <div class="fou_snow"></div>
            </div>
        
    <!-- 英雄榜 -->
            <div class="relative pl1-5 pr1-5 none" id="hero_reward">
                <div class="tc mb2">
                    <span class="che_title f18 white">英雄榜</span>
                </div>
                <div class="lh45 reward_list">
                    <span>经纪人用户</span>
                    <span>获得奖励</span>
                </div>
                <ul class="hero_list" id="hero_list">
                    <!-- <li class="flex_between fline white-bg pl2 pr1">
                       <div class="flex_start">
                           <div class="avatar">
                               <img src="" alt="">
                           </div>
                           <div class="ml1">
                               <p class="f12 mb0">无邪科技</p>
                               <p class="color999 f11 mb0">杭州浙江</p>
                           </div>
                       </div> 
                       <div class="tr">
                           <p class="f15 mb0">获得<span class="cf24d57">50</span>元</p>
                           <p class="f11 color666 mb0">完成绝招一：分享朋友圈，挤满30个赞</p>
                       </div>
                    </li> -->
                </ul>
            </div>

        <div class="chr_bottom">
            无界商圈祝您，2018年元旦快乐！<br>
            2018年，商圈与你，共成长！
        </div>
        <div class="loadapp none" id="loadapp">
            点击下载无界商圈APP
        </div>
     </div>  
     <div class="safari none">
            <img src="{{URL::asset('/')}}/images/safari.png">
     </div>
     <div class="tips none"></div> 
    </section>
@stop
@section('endjs')
    <script type="text/javascript" src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
    <script type='text/javascript'>
            new FastClick(document.body);
            var args = getQueryStringArgs(),
                agent_id = args['agent_id'] || '0';
            document.title = '邀请有奖，荐者有份';
            var shareFlag= location.href.indexOf('is_share') > 0 ? '&is_share=1' : '';
            
        function getData(id){
            var url = labUser.agent_path + '/activity/christmas-detail/_v010102';
            ajaxRequest({'agent_id':id},url,function(data){
                if(data.status){
                    if(data.message.my_infos.length >0){
                        $.each(data.message.my_infos,function(i,j){
                            if(j.type == 17 ){
                                if(j.status == 0){
                                    $('#screenshot').css({'backgroundColor':'#f24d57','padding':'0.5rem 1rem'}).text('审核中').attr('data-type',0);
                                }else if(j.status == 1){
                                    $('#screenshot').addClass('skill_done').text('已完成').attr('data-type',0);
                                }
                            }
                            if(j.type ==14 && j.status ==1){
                                $('#invite_act').addClass('skill_done').text('已完成').attr('data-type',0);
                            }
                            if(j.type ==15 && j.status ==1){
                                $('#invitations').addClass('skill_done').text('已完成').attr('data-type',0);
                            }
                            if(j.type ==6 && j.status ==1){
                                $('#investors').addClass('skill_done').text('已完成').attr('data-type',0);
                            }
                        })
                    }
                  $('.uniques').removeClass('none'); 
                  if(data.message.gather_infos.length >0){
                    var rewardHtml = '';
                    $.each(data.message.gather_infos,function(k,v){
                        rewardHtml += '<li class="flex_between fline white-bg pl2 pr1"><div class="flex_start"><div class="avatar">';
                        rewardHtml += '<img src="'+v.avatar+'" alt=""></div><div class="ml1 tl"><p class="f12 mb0 text_ellipsis width5">'+v.name+'</p>';
                        rewardHtml += '<p class="color999 f11 mb0 text_ellipsis width5">'+v.zone+'</p></div></div><div class="tr mt-05">';
                        if(v.type ==14){
                            rewardHtml += '<p class="f15 mb0">获得<span class="cf24d57">80</span>元</p><p class="f11 color666 mb0">邀请 1 位投资人参加OVO品牌招商会</p>';
                        }else if(v.type == 15){
                            rewardHtml += '<p class="f15 mb0">获得<span class="cf24d57">200</span>元</p><p class="f11 color666 mb0">邀请投资人进行实体考察，并交付订金</p>';
                        }else if(v.type == 6){
                            rewardHtml += '<p class="f15 mb0">获得<span class="cf24d57">1000</span>元</p><p class="f11 color666 mb0">邀请的投资人在平台成功加盟品牌</p>';
                        }else if(v.type == 17){
                            rewardHtml += '<p class="f15 mb0">获得<span class="cf24d57">5</span>元</p><p class="f11 color666 mb0">分享到朋友圈，集满30个赞</p>';
                        }
                        rewardHtml += ' </div></li>'                 
                    })
                    $('#hero_list').html(rewardHtml);
                    $('#hero_reward').removeClass('none');
                  } 
                }
            });
        }
        if(!shareFlag){
            getData(agent_id); 
            looper_dingdan = setInterval(function () {
                if ($('#hero_list li').length > 5) {
                    var firstTag=$('#hero_list').find('li:first');
                    var height=firstTag.height();
                    console.log(height);
                    firstTag.animate({'margin-top':'-5.4rem','opacity':0},500,function(){
                        $(this).clone().css({'margin-top':0,'opacity':1,'padding':'1rem 1rem 1rem 2rem'}).appendTo($('#hero_list'));
                        $('#hero_list li').first().remove();
                    });
                }
            }, 2000);
        }else{
            $('.loadapp').removeClass('none');
            $('.uniques').removeClass('none'); 
        }
        
        //微信二次分享
            //浏览器判断
            if (is_weixin()) {
                /**微信内置浏览器**/
                $(document).on('tap', '#loadapp', function() {
                    var _height = $(document).height();
                    $('.safari').css('height', _height);
                    $('.safari').removeClass('none');
                });
                // 点击隐藏蒙层
                $(document).on('tap', '.safari', function() {
                    $(this).addClass('none');
                });
                var sharetitle = '圣诞提钱过，拿钱有绝招！1000元现金红包大Fun送，等的就是你！';
                var wxurl = labUser.api_path + '/weixin/js-config';
                var share_logo = labUser.path + 'images/agent-share-logo.png'; 
                var des = '1000元现金礼包大Fun送，赶快抢！'
        
                ajaxRequest({ url: location.href }, wxurl, function(data) {
                    if (data.status) {
                        wx.config({
                            debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                            appId: data.message.appId, // 必填，公众号的唯一标识
                            timestamp: data.message.timestamp, // 必填，生成签名的时间戳
                            nonceStr: data.message.nonceStr, // 必填，生成签名的随机串
                            signature: data.message.signature, // 必填，签名，见附录1
                            jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage'] // \必填，需要使用的JS接口列表，所有JS接口列表见附录2
                        });
                        wx.ready(function() {
                            // 获取“分享到朋友圈”按钮点击状态及自定义分享内容接口
                            wx.onMenuShareTimeline({
                                title:sharetitle, // 分享标题
                                link: location.href, // 分享链接
                                imgUrl: share_logo, // 分享图标
                                success: function() {
                                
                                },
                                cancel: function() {
                                    // 用户取消分享后执行的回调函数
                                }
                            });
                            // 获取“分享给朋友”按钮点击状态及自定义分享内容接口
                            wx.onMenuShareAppMessage({
                                title: sharetitle,
                                desc: des,
                                link: location.href,
                                imgUrl: share_logo,
                                trigger: function(res) {
                                    // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
                                    console.log('用户点击发送给朋友');
                                },
                                success: function(res) {
                                    
                                },
                                cancel: function(res) {
                                    console.log('已取消');
                                },
                                fail: function(res) {
                                    console.log(JSON.stringify(res));
                                }
                            });
                        });
                    }
                });
            }else if(isiOS){
                $(document).on("click","#loadapp",function(){
                        window.location.href = 'https://itunes.apple.com/app/id1282277895';
                });
           }else if(isAndroid){
                $(document).on("click","#loadapp",function(){          
                        window.location.href = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.tyrbl.agent';
                });
           }
        //上传截图
            $(document).on('click','#screenshot',function(){
                if(shareFlag){
                    tips('请下载APP')
                }else{
                    var type = $(this).attr('data-type');
                    if(type == 0){
                        window.location.href = labUser.path + 'webapp/agent/mycharge/detail/_v010100?agent_id='+agent_id;
                    }else {
                        upLoadScreenShot();
                    }
                }
            });
        //邀请活动
            $(document).on('click','#invite_act',function(){
                if(shareFlag){
                    tips('请下载APP')
                }else{
                    var type = $(this).attr('data-type');
                        if(type == 0){
                            window.location.href = labUser.path + 'webapp/agent/mycharge/detail/_v010100?agent_id='+agent_id;
                        }else {
                            creatActInvite();
                        }
                }
                
            });
        //邀请考察
            $(document).on('click','#invitations',function(){
                if(shareFlag){
                    tips('请下载APP')
                }else{
                    var type = $(this).attr('data-type');
                    if(type == 0){
                        window.location.href = labUser.path + 'webapp/agent/mycharge/detail/_v010100?agent_id='+agent_id;
                    }else {
                        creatInvestigate()();
                    }
                }
            });
        //邀请投资人
            $(document).on('click','#investors',function(){
                if(shareFlag){
                    tips('请下载APP')
                }else{
                    var type = $(this).attr('data-type');
                    if(type == 0){
                        window.location.href = labUser.path + 'webapp/agent/mycharge/detail/_v010100?agent_id='+agent_id;
                    }else {
                        noti_invite();
                    }
                }
            });
        // var invePar = $('#investors').parent();
        // $(invePar).click(function(){
        //     if($('#investors').attr('data-type') == 0 && !shareFlag){
        //         window.location.href = labUser.path + 'webapp/agent/mycharge/detail/_v010100?agent_id='+agent_id;
        //     }
        // })
        //规则跳转
            $(document).on('tap','.rule',function(){
                window.location.href =labUser.path + 'webapp/agent/activityrule/detail';
            })
             
       //提示框
        function tips(e) {
            $('.tips').text(e).removeClass('none');
            setTimeout(function() {
                $('.tips').addClass('none ');
            }, 1500);

        }
        //分享到微信
        function showShare(){
            var  type = 'activity',
                 title = '圣诞提钱过，有奖你来拿？',
                 url = window.location.href,
                 img = labUser.path + 'images/agent-share-logo.png',
                 header = '活动',
                 content = '1000元礼包大Fun送，赶快抢！';
            agentShare(title, url, img, header, content,'activity');
        }  
    </script>
@stop