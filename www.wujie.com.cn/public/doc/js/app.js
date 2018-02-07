/**
 * Created by Administrator on 2016/3/1.
 */
var myApp = angular.module("myApp", ['ui.router']);
myApp.controller('versionCtrl', function ($scope, $state) {
    var lists=$('#version-lists');
    if($state.current.versions){
        $('#version-lists :gt(0)').remove();
        var key=0;
        $.each($state.current.versions,function(k,v){
            if(v!==false){
                v==$state.current.current && (key=k);
                lists.append('<li><a href="javascript:void(0);">'+(v?v:'原始版本')+'</a></li>');
            }
        });
        lists.find('li>a').click(function(){
            $state.current.current=/^v\d+$/.test(this.innerHTML)?this.innerHTML:'';
            $state.go($state.current.name,null,{
                reload:true
            });
        }).filter(':eq('+key+')').addClass('current');
        lists.show();
    }else{
        lists.hide();
    }
    var lists=$('a[ui-sref]').removeClass('current');
    var ul=$('a[href="#'+$state.current.url+'"]').addClass('current').parent();
    if(ul.is(':hidden')){
        ul.parent().click();
    }
});
myApp.config(function ($stateProvider, $urlRouterProvider) {
    $urlRouterProvider.when("", "/ajaxlogin");
    $stateProvider.decorator('self',function(state){
        if(state.self.versions && state.self.versions.length){
            var url=state.self.templateUrl;
            if(!state.self.current){
                state.self.current=state.self.versions[0];
            }
            state.self.templateUrl= function(){
                return state.self.current?url.replace(/\/([^\/]+)$/,'/'+state.self.current+'/$1'):url;
            };
        }
        state.self.controller='versionCtrl';
        return state;
    })
//    $stateProvider
        /**********************fengzq*****************/
        /*****登陆注册**********/
        .state("indexsearch", {
            url: "/indexsearch",
            templateUrl: "/doc/other/indexsearch.html",
            versions: ["v020700","v020500",""]
        })
        .state("indexlist", {
            url: "/indexlist",
            templateUrl: "/doc/Index/indexlist.html",
            versions: ["v020700","v020600","v020500",""]

        })
        .state("getcode", {
            url: "/getcode",
            templateUrl: "/doc/Index/getcode.html",
            versions: ["v020500",""]

        })

        .state("hotwords", {
            url: "/hotwords",
            templateUrl: "/doc/other/hotwords.html",
            versions: ["v020500"]
        })
        .state("ajaxlogin", {
            url: "/ajaxlogin",
            templateUrl: "/doc/login/ajaxlogin.html"
        })
        .state("logout", {
            url: "/logout",
            templateUrl: "/doc/login/logout.html"
        })
        .state("register", {
            url: "/register",
            templateUrl: "/doc/login/register.html",
            versions: ["v020400",""]
        })
        .state("registeraccount", {
            url: "/registeraccount",
            templateUrl: "/doc/login/registeraccount.html",
            versions: ["v020602","v020500"]
        })
        .state("outhlogin", {
            url: "/outhlogin",
            templateUrl: "/doc/login/outhlogin.html"
        })
        .state("outhbang", {
            url: "/outhbang",
            templateUrl: "/doc/login/outhbang.html"
        })
        .state("dopassword", {
            url: "/dopassword",
            templateUrl: "/doc/login/dopassword.html"
        })
        .state("checkuser", {
            url: "/checkuser",
            templateUrl: "/doc/login/checkuser.html"
        })
        .state("allowRegister", {
            url: "/allowRegister",
            templateUrl: "/doc/login/allowRegister.html"
        })
        .state("inviteregister", {
            url: "/inviteregister",
            templateUrl: "/doc/login/inviteregister.html"
        })
        .state("userinfo", {
            url: "/userinfo",
            templateUrl: "/doc/login/userinfo.html"
        })
        /*******短信********/
        .state("sendcode", {
            url: "/sendcode",
            templateUrl: "/doc/code/sendcode.html"
        })
        .state("checkcode", {
            url: "/checkcode",
            templateUrl: "/doc/code/checkcode.html"
        })
        /*******图形验证码********/
        .state("sendcaptcha", {
            url: "/sendcaptcha",
            templateUrl: "/doc/captcha/sendcaptcha.html"
        })
        .state("piccaptcha", {
            url: "/piccaptcha",
            templateUrl: "/doc/captcha/piccaptcha.html"
        })

        .state("checkcaptcha", {
            url: "/checkcaptcha",
            templateUrl: "/doc/captcha/checkcaptcha.html"
        })
        .state("verifycaptcha", {
            url: "/verifycaptcha",
            templateUrl: "/doc/captcha/verifycaptcha.html"
        })
        /****用户*******/
        .state("favorite", {
            url: "/favorite",
            templateUrl: "/doc/user/favorite.html"
        })
        .state("edituser", {
            url: "/edituser",
            templateUrl: "/doc/user/edituser.html"
        })
        .state("feedback", {
            url: "/feedback",
            templateUrl: "/doc/user/feedback.html"
        })
        .state("userApply", {
            url: "/userApply",
            templateUrl: "/doc/user/userApply.html"
        })
        .state("remindReply", {
            url: "/remindReply",
            templateUrl: "/doc/user/remindReply.html"
        })
        .state("scoresum", {
            url: "/scoresum",
            templateUrl: "/doc/user/scoresum.html"
        })
        .state("currencysum", {
            url: "/currencysum",
            templateUrl: "/doc/user/currencysum.html",
            versions: ["v020500"]
        })
        .state("scorelist", {
            url: "/scorelist",
            templateUrl: "/doc/user/scorelist.html",
            versions: ["v020700",""]
        })
        .state("myfavorite", {
            url: "/myfavorite",
            templateUrl: "/doc/user/myfavorite.html",
            versions: ["v020500",""]
        })
        .state("myorders", {
            url: "/myorders",
            templateUrl: "/doc/user/myorders.html",
            versions: ["v020700","v020500",""]
        })
        .state("myorderinfo", {
            url: "/myorderinfo",
            templateUrl: "/doc/user/orderinfo.html",
            versions: ["v020700","v020500",""]
        })
        .state("mybrandlist", {
            url: "/mybrandlist",
            templateUrl: "/doc/user/mybrandlist.html",
            versions: ["v020500",""]
        })
        .state("mybrandoperation", {
            url: "/mybrandoperation",
            templateUrl: "/doc/user/brandoperation.html"
        })
        .state("business_card", {
            url: "/business_card",
            templateUrl: "/doc/user/business_card.html"
        })
        .state("recommendusers", {
            url: "/recommend",
            templateUrl: "/doc/user/recommend.html"
        })
        .state("getuserdetail", {
            url: "/getuserdetail",
            templateUrl: "/doc/user/getuserdetail.html",
            versions: ["v020500",""]
        })
        .state("userbusinesscarddetail", {
            url: "/userbusinesscarddetail",
            templateUrl: "/doc/user/userbusinesscarddetail.html"
        })
        .state("applyActivityLists", {
            url: "/applyActivityLists",
            templateUrl: "/doc/user/applyActivityLists.html"
        })
        .state("intentBrands", {
            url: "/intentBrands",
            templateUrl: "/doc/user/intentBrands.html",
            versions: ["v020500","v020400"]
        })
        .state("brandsBrowse", {
            url: "/brandsBrowse",
            templateUrl: "/doc/user/brandsBrowse.html",
            versions: ["v020500","v020400"]
        })

        .state("addBrowse", {
            url: "/addBrowse",
            templateUrl: "/doc/user/addBrowse.html",
            versions: ["v020400"]
        })
        .state("addShareCount", {
            url: "/addShareCount",
            templateUrl: "/doc/user/addShareCount.html",
            versions: ["v020400"]
        })
        .state("myfundlist", {
            url: "/myfundlist",
            templateUrl: "/doc/user/myfundlist.html",
            versions: ["v020500"]
        })
        .state("withdraw", {
            url: "/withdraw",
            templateUrl: "/doc/user/withdraw.html",
            versions: ["v020500"]
        })
        .state("withdrawdetail", {
            url: "/withdrawdetail",
            templateUrl: "/doc/user/withdrawdetail.html",
            versions: ["v020500"]
        })
        .state("withdrawlist", {
            url: "/withdrawlist",
            templateUrl: "/doc/user/withdrawlist.html",
            versions: ["v020500"]
        })
        .state("withdrawrecord", {
            url: "/withdrawrecord",
            templateUrl: "/doc/user/withdrawrecord.html",
            versions: ["v020500"]
        })
        .state("writecode", {
            url: "/writecode",
            templateUrl: "/doc/user/writecode.html",
            versions: ["v020500"]
        })
        .state("userinfoext", {
            url: "/userinfoext",
            templateUrl: "/doc/user/userinfoext.html",
            versions: ["v020700", "v020500"]
        })
        .state("userlottery", {
            url: "/userlottery",
            templateUrl: "/doc/user/lottery.html",
            versions: ["v020600"]
        })
        .state("userdoubt", {
            url: "/userdoubt",
            templateUrl: "/doc/user/doubt.html",
            versions: ["v020600"]
        })
        .state("zan", {
            url: "/zan",
            templateUrl: "/doc/user/zan.html",
        })

        /****ovo*******/
        .state("fmakerlist", {
            url: "/fmakerlist",
            templateUrl: "/doc/maker/list.html"
        })
        .state("makerdetail", {
            url: "/makerdetail",
            templateUrl: "/doc/maker/detail.html"
        })
        .state("groupchat", {
            url: "/groupchat",
            templateUrl: "/doc/maker/groupchat.html"
        })
        .state("deletegroupchat", {
            url: "/deletegroupchat",
            templateUrl: "/doc/maker/deletegroupchat.html"
        })
        .state("memberlist", {
            url: "/memberlist",
            templateUrl: "/doc/maker/memberlist.html"
        })
        .state("member-industry-list", {
            url: "/member/industry/list",
            templateUrl: "/doc/maker/member_industry_list.html"
        })
        .state("makermember", {
            url: "/makermember",
            templateUrl: "/doc/maker/makermember.html"
        })
        .state("switchmaker", {
            url: "/switchmaker",
            templateUrl: "/doc/maker/switchmaker.html"
        })
        /*** 视频点播***/
        .state("videotype", {
            url: "/videotype",
            templateUrl: "/doc/video/videotype.html"
        })
        .state("videolist", {
            url: "/videolist",
            templateUrl: "/doc/video/videolist.html",
            versions: ["v020700", "v020500" ,""]
        })
        .state("videodetail", {
            url: "/videodetail",
            templateUrl: "/doc/video/videodetail.html",
            versions: ["v020700","v020500" ,""]
        })
        .state("videocomment", {
            url: "/videocomment",
            templateUrl: "/doc/video/videocomment.html"
        })
        /****其他*******/
        .state("adslist", {
            url: "/adslist",
            templateUrl: "/doc/other/adslist.html",
            versions: ["v020502" ,"v020500" ,"v020400",""]

        })
        .state("zonelist", {
            url: "/zonelist",
            templateUrl: "/doc/other/zonelist.html"
        })
        .state("makerzone", {
            url: "/makerzone",
            templateUrl: "/doc/other/makerzone.html"
        })
        .state("industry", {
            url: "/industry",
            templateUrl: "/doc/other/industry.html"
        })
        .state("upload", {
            url: "/upload",
            templateUrl: "/doc/other/upload.html"
        })
        .state("sendmessage", {
            url: "/sendmessage",
            templateUrl: "/doc/other/sendmessage.html"
        })
        .state("ifliving", {
            url: "/ifliving",
            templateUrl: "/doc/other/ifliving.html",
            versions: ["v020600"]

        })
        /****票券，订单*******/
        .state("ticketlist", {
            url: "/ticketlist",
            templateUrl: "/doc/user/ticketlist.html",
            versions: ['v020700','v020502',"v020500",""]
        })
        .state("ticketdetail", {
            url: "/ticketdetail",
            templateUrl: "/doc/user/ticketdetail.html"
        })
        .state("livedata", {
            url: "/livedata",
            templateUrl: "/doc/user/livedata.html"
        })
        .state("deadline", {
            url: "/deadline",
            templateUrl: "/doc/order/deadline.html"
        })
        .state("payorder", {
            url: "/payorder",
            templateUrl: "/doc/order/payorder.html"
        })
        .state("thirdresult", {
            url: "/thirdresult",
            templateUrl: "/doc/order/thirdresult.html"
        })
        .state("payorderandsign", {
            url: "/payorderandsign",
            templateUrl: "/doc/order/orderandsign.html",
            versions: ["v020700","v020400",""]
        })
        .state("paysign", {
            url: "/paysign",
            templateUrl: "/doc/order/sign.html"
        })
        .state("paycheck", {
            url: "/paycheck",
            templateUrl: "/doc/order/check.html"
        })
        .state("payverify", {
            url: "/payverify",
            templateUrl: "/doc/order/verify.html"
        })
        .state("continuepay", {
            url: "/continuepay",
            templateUrl: "/doc/order/continuepay.html",
            versions: ["v020500"]
        })
        .state("orderandpay", {
            url: "/orderandpay",
            templateUrl: "/doc/order/orderandpay.html",
            versions: ["v020700"]
        })
        .state("deleteticket", {
            url: "/deleteticket",
            templateUrl: "/doc/user/deleteticket.html"
        })
        .state("getuserbasic", {
            url: "/getuserbasic",
            templateUrl: "/doc/user/getuserbasic.html"
        })
        /*******活动********/
        .state("enrollInfos", {
            url: "/enrollInfos",
            templateUrl: "/doc/activity/enrollinfo.html",
            versions: ["v020700","v020500"]
        })
        .state("activityList", {
            url: "/a_list",
            templateUrl: "/doc/activity/list.html",
            versions: ["v020500" , "v020400",""]
        })
        .state("activityDetail", {
            url: "/a_detail",
            templateUrl: "/doc/activity/detail.html",
            versions: ["v020700","v020600","v020500","v020400",""]
        })
        .state("activityApply", {
            url: "/activityApply",
            templateUrl: "/doc/activity/apply.html"
        })
        .state("activityTickets", {
            url: "/tickets",
            templateUrl: "/doc/activity/tickets.html",
            versions: ["v020400",""]
        })
        .state("activityApplyactivity", {
            url: "/applyactivity",
            templateUrl: "/doc/activity/applyactivity.html"
        })
        .state("activityMakerlist", {
            url: "/makerlist",
            templateUrl: "/doc/activity/makerlist.html"
        })
        .state("activitySign", {
            url: "/sign",
            templateUrl: "/doc/activity/sign.html",
            versions: ["v020700","v020400",""]
        })
        .state("activityTempSign", {
            url: "/tempsign",
            templateUrl: "/doc/activity/tempsign.html",
            versions: ["v020400",""]
        })
        .state("activitySignuserlist", {
            url: "/signuserlist",
            templateUrl: "/doc/activity/signuserlist.html",
            versions: ["v020400",""]
        })
        .state("activityPadsign", {
            url: "/padsign",
            templateUrl: "/doc/activity/padsign.html"
        })
        .state("pkpass", {
            url: "/pkpass",
            templateUrl: "/doc/order/pkpass.html"
        })
        .state("activityApplyNoPay", {
            url: "/activityApplyNoPay",
            templateUrl: "/doc/activity/applynopay.html",
            versions: ["v020500","v020400",""]
        })
        .state("activityCheckAndApply", {
            url: "/activitycheckandapply",
            templateUrl: "/doc/activity/check.html",
            versions: ["v020700","v020400",""]

        })
        .state("activityIncre", {
            url: "/activityIncre",
            templateUrl: "/doc/activity/incre.html"
        })
        .state("applyList", {
            url: "/applyList",
            templateUrl: "/doc/activity/userapply.html"
        })
        .state("activityInvite", {
            url: "/activityInvite",
            templateUrl: "/doc/activity/invite.html"
        })
        .state("activityMakers", {
            url: "/activityMakers",
            templateUrl: "/doc/activity/makers.html"
        })
        .state("activityReceive", {
            url: "/activityReceive",
            templateUrl: "/doc/activity/receive.html"
        })
        .state("activityRecordshare", {
            url: "/activityRecordshare",
            templateUrl: "/doc/activity/recordshare.html"
        })
        .state("activityRecordcontent", {
            url: "/activityRecordcontent",
            templateUrl: "/doc/activity/recordcontent.html"
        })
        .state("activitySharename", {
            url: "/activitySharename",
            templateUrl: "/doc/activity/sharename.html"
        })
        .state("activityListthree", {
            url: "/activityListthree",
            templateUrl: "/doc/activity/listthree.html",
            versions: ["v020500" , "v020400",""]
        })

        .state("activityScrolls", {
            url: "/activityScrolls",
            templateUrl: "/doc/activity/scrolls.html"
        })

        .state("activityZan", {
            url: "/zan",
            templateUrl: "/doc/activity/zan.html",
            versions: ["v020400",""]
        })


        .state("telApplied", {
            url: "/telApplied",
            templateUrl: "/doc/activity/telapplied.html",
            versions: ["v020400"]
        })
        .state("applyandpay", {
            url: "/applyandpay",
            templateUrl: "/doc/activity/applyandpay.html",
            versions: ["v020700"]
        })

    /*******直播********/
        .state("liveList", {
            url: "/l_list",
            templateUrl: "/doc/live/list.html",
            versions: ["v020700", "v020600", "v020500", ""]
        })
        .state("liveDetail", {
            url: "/l_detail",
            templateUrl: "/doc/live/detail.html",
            versions: ["v020700","v020500",""]
        })
        .state("liveSubscribe", {
            url: "/subscribe",
            templateUrl: "/doc/live/subscribe.html"
        })
        .state("liveUserSubscribe", {
            url: "/user-subscribe",
            templateUrl: "/doc/live/user_subscribe.html"
        })

        .state("liveToday", {
            url: "/live-today",
            templateUrl: "/doc/live/today.html"
        })
        .state("liveOrder", {
            url: "/liveOrder",
            templateUrl: "/doc/live/order.html"
        })
        .state("liveIncre", {
            url: "/liveIncre",
            templateUrl: "/doc/live/incre.html"
        })
        .state("liveShare", {
            url: "/liveShare",
            templateUrl: "/doc/live/share.html"
        })

        .state("shareSubscribe", {
            url: "/shareSubscribe",
            templateUrl: "/doc/live/share_subscribe.html"
        })

        .state("liveSendcode", {
            url: "/liveSendcode",
            templateUrl: "/doc/live/send_code.html"
        })


        .state("liveOrders", {
            url: "/liveOrders",
            templateUrl: "/doc/live/orders.html"
        })
        .state("viewers", {
            url: "/viewers",
            templateUrl: "/doc/live/viewers.html",
            versions: ["v020400"]
        })

        .state("wallinfo", {
            url: "/wallinfo",
            templateUrl: "/doc/live/wallinfo.html",
            versions: ["v020400"]
        })
        .state("buyinfo", {
            url: "/buyinfo",
            templateUrl: "/doc/live/buyinfo.html",
            versions: ["v020700"]
        })
        /*******专版********/
        .state("vipList", {
            url: "/vipList",
            templateUrl: "/doc/vip/list.html"
        })
        .state("vipDetail", {
            url: "/vipDetail",
            templateUrl: "/doc/vip/detail.html"
        })
        .state("vipRecords", {
            url: "/vipRecords",
            templateUrl: "/doc/vip/records.html"
        })

        .state("vipRecommend", {
            url: "/vipRecommend",
            templateUrl: "/doc/vip/recommend.html"
        })
        .state("vipSearch", {
            url: "/vipSearch",
            templateUrl: "/doc/vip/search.html"
        })

        /*****商机*****/
        .state("opportunityPublic", {
            url: "/public",
            templateUrl: "/doc/opportunity/public.html"
        })
        .state("opportunitylist", {
            url: "/opportunitylist",
            templateUrl: "/doc/opportunity/opportunitylist.html"
        })
        .state("opportunitydetail", {
            url: "/opportunitydetail",
            templateUrl: "/doc/opportunity/opportunitydetail.html"
        })
        .state("opportunityactivity", {
            url: "/activity",
            templateUrl: "/doc/opportunity/activity.html"
        })
        .state("opportunityapply", {
            url: "/opportunityapply",
            templateUrl: "/doc/opportunity/apply.html"
        })
        /*****关注的活动主办方*****/
        .state("organizerMyfollow", {
            url: "/myfollow",
            templateUrl: "/doc/organizer/myfollow.html"
        })
        .state("organizerInfo", {
            url: "/info",
            templateUrl: "/doc/organizer/info.html"
        })
        .state("organizerActivityList", {
            url: "/activityList",
            templateUrl: "/doc/organizer/activityList.html"
        })
        .state("organizerFollow", {
            url: "/follow",
            templateUrl: "/doc/organizer/follow.html"
        })

        /*****消息通知*****/
        .state("messageIndex", {
            url: "/index",
            templateUrl: "/doc/message/index.html"
        })
        .state("messageOfficialmessage", {
            url: "/officialmessage",
            templateUrl: "/doc/message/officialmessage.html"
        })
        .state("messageLiveremind", {
            url: "/liveremind",
            templateUrl: "/doc/message/liveremind.html"
        })
        .state("messageLiveremindmessages", {
            url: "/liveremindmessages",
            templateUrl: "/doc/message/liveremindmessages.html"
        })
        .state("messageActivityremind", {
            url: "/activityremind",
            templateUrl: "/doc/message/activityremind.html"
        })
        .state("messageActivityremindmessages", {
            url: "/activityremindmessages",
            templateUrl: "/doc/message/activityremindmessages.html"
        })
        .state("messageList", {
            url: "/messagelist",
            templateUrl: "/doc/message/messagelist.html"
        })
        .state("messageUnreadcounts", {
            url: "/unreadcounts",
            templateUrl: "/doc/message/unreadcounts.html"
        })
        .state("messageReadmessage", {
            url: "/readmessage",
            templateUrl: "/doc/message/readmessage.html"
        })
        /*****评论*****/
        .state("commentList", {
            url: "/commentlist",
            templateUrl: "/doc/comment/list.html"
        })

        .state("commentFreshList", {
            url: "/commentFreshList",
            templateUrl: "/doc/comment/freshlist.html"
        })
        .state("commentZhan", {
            url: "/zhan",
            templateUrl: "/doc/comment/zhan.html"
        })
        .state("commentDelete", {
            url: "/delete",
            templateUrl: "/doc/comment/delete.html"
        })
        .state("commentAdd", {
            url: "/add",
            templateUrl: "/doc/comment/add.html",
            versions: ["v020700",""]
        })

        .state("singleComment", {
            url: "/singleComment",
            templateUrl: "/doc/comment/singlecomment.html",
            versions: ["v020400"]

        })
        .state("setuserguard", {
            url: "/setuserguard",
            templateUrl: "/doc/user/setuserguard.html"
        })
        .state("checkuserguard", {
            url: "/checkuserguard",
            templateUrl: "/doc/user/checkuserguard.html"
        })
        /****群聊管理*******/
        .state("creategroupchat", {
            url: "/creategroupchat",
            templateUrl: "/doc/groupchat/creategroupchat.html"
        })
        .state("editgroupchat", {
            url: "/editgroupchat",
            templateUrl: "/doc/groupchat/editgroupchat.html"
        })
        .state("getgroupinfo", {
            url: "/getgroupinfo",
            templateUrl: "/doc/groupchat/getgroupinfo.html"
        })
        .state("deletegroup", {
            url: "/deletegroup",
            templateUrl: "/doc/groupchat/deletegroupchat.html"
        }).state("addordelmember", {
            url: "/addordelmember",
            templateUrl: "/doc/groupchat/addordelgroupmember.html"
        }).state("changeavatar", {
            url: "/changeavatar",
            templateUrl: "/doc/groupchat/changeavatar.html"
        })
        .state("setremark", {
            url: "/setremark",
            templateUrl: "/doc/user/setremark.html"
        })
        .state("friendlist", {
            url: "/friendlist",
            templateUrl: "/doc/user/friendlist.html"
        })
        /*版本管理*/
        .state("version-new", {
            url: "/version/new",
            templateUrl: "/doc/version/new.html"
        })


        /*个推*/
        .state("push-receive", {
            url: "/receive",
            templateUrl: "/doc/push/receive.html"
        })


        /*******品牌********/
        .state("brandDetail", {
            url: "/brandDetail",
            templateUrl: "/doc/brand/detail.html",
            versions: ["v020700","v020500","v020400",""]
        })

        .state("brandNews", {
            url: "/brandNews",
            templateUrl: "/doc/brand/news.html"
        })

        .state("brandCollect", {
            url: "/brandCollect",
            templateUrl: "/doc/brand/collect.html",
            versions: ["v020700",""]
        })

        .state("brandAsk", {
            url: "/brandAsk",
            templateUrl: "/doc/brand/ask.html",
            versions: ["v020700",""]
        })

        .state("brandMessage", {
            url: "/brandMessage",
            templateUrl: "/doc/brand/message.html",
            versions: ["v020500",""]

        })

        .state("brandCategories", {
            url: "/brandCategories",
            templateUrl: "/doc/brand/categories.html"
        })

        .state("brandEnter", {
            url: "/brandEnter",
            templateUrl: "/doc/brand/enter.html"
        })

        .state("brandLists", {
            url: "/brandLists",
            templateUrl: "/doc/brand/lists.html",
            versions: ["v020700" ,"v020500" ,"v020400",""]
        })

        .state("brandConsult", {
            url: "/brandConsult",
            templateUrl: "/doc/brand/consult.html"
        })


        .state("brandGoods", {
            url: "/brandGoods",
            templateUrl: "/doc/brand/goods.html"
        })



        .state("brandTodayGoods", {
            url: "/brandTodayGoods",
            templateUrl: "/doc/brand/todayGoods.html"
        })

        .state("fetchfund", {
            url: "/fetchfund",
            templateUrl: "/doc/brand/fetchfund.html",
            versions: ["v020500" ,""]
        })


        .state("brandQuestion", {
            url: "/brandQuestion",
            templateUrl: "/doc/brand/question.html",
            versions: ["v020700","v020500" ]
        })

        .state("brandWall", {
            url: "/brandWall",
            templateUrl: "/doc/brand/wall.html",
            versions: ["v020700" ]
        })

        .state("showdetail", {
            url: "/showdetail",
            templateUrl: "/doc/brand/showdetail.html",
            versions: ["v020700" ]
        })
        .state("brandtel", {
            url: "/brandtel",
            templateUrl: "/doc/brand/brandtel.html",
            versions: ["v020700" ]
        })

        /*资讯*/
        .state("newsList", {
            url: "/newsList",
            templateUrl: "/doc/news/list.html",
            versions: ["v020700","v020500" ,""]
        })

        .state("newsDetail", {
            url: "/newsDetail",
            templateUrl: "/doc/news/detail.html",
            versions: ['v020700','v020600',"v020500" ,""]
        })


        /*分享*/
        .state("collectScore", {
            url: "/collectScore",
            templateUrl: "/doc/share/collectScore.html",
            versions: ["v020500"]
        })

        .state("collectShare", {
            url: "/collectShare",
            templateUrl: "/doc/share/collectShare.html",
            versions: ["v020500"]
        })

        .state("myShare", {
            url: "/myShare",
            templateUrl: "/doc/share/myShare.html",
            versions: ["v020700","v020500"]
        })
        .state("shareDetail", {
            url: "/shareDetail",
            templateUrl: "/doc/share/shareDetail.html",
            versions: ["v020700","v020500"]
        })

        .state("shareList", {
            url: "/shareList",
            templateUrl: "/doc/share/shareList.html",
            versions: ['v020700','v020600',"v020500"]
        })
        .state("shortUrl", {
            url: "/shortUrl",
            templateUrl: "/doc/share/shortUrl.html",
            versions: ["v020500"]
        }).state("userSign", {
            url: "/userSign",
            templateUrl: "/doc/user/sign.html",
            versions: ["v020700","v020600"]
        }).state("DuiBaLogin", {
            url: "/DuiBaLogin",
            templateUrl: "/doc/user/duiba.html",
            versions: ["v020600"]
        }).state("special-lists", {
            url: "/special-lists",
            templateUrl: "/doc/video/special-lists.html",
            versions: ["v020600"]
        }).state("special-detail", {
            url: "/special-detail",
            templateUrl: "/doc/video/special-detail.html",
            versions: ["v020700","v020600"]
        }).state("shareUrl", {
            url: "/shareUrl",
            templateUrl: "/doc/share/shareUrl.html",
            versions: ["v020600"]
        }).state("newsZan", {
            url: "/newsZan",
            templateUrl: "/doc/news/zan.html",
            versions: ["v020600"]
        })
        .state("news-buyinfo", {
            url: "/news-buyinfo",
            templateUrl: "/doc/news/buyinfo.html",
            versions: ["v020700"]
        })
        .state("index-data", {
            url: "/index-data",
            templateUrl: "/doc/Index/data.html",
            versions: ["v020700","v020600"]
        }).state("video-curriculum", {
            url: "/video-curriculum",
            templateUrl: "/doc/video/curriculum.html",
            versions: ["v020600"]
        })
        .state("video-buyinfo", {
            url: "/video-buyinfo",
            templateUrl: "/doc/video/buyinfo.html",
            versions: ["v020700"]
        })
        .state("share-profit", {
            url: "/share-profit",
            templateUrl: "/doc/share/profit.html",
            versions: ["v020700","v020600"]
        }).state("share-subordinates", {
        url: "/share-subordinates",
        templateUrl: "/doc/share/subordinates.html",
        versions: ["v020700"]
    })
        .state("score-goods", {
            url: "/score-goods",
            templateUrl: "/doc/score/goods.html",
            versions: ["v020700"]
        })
    ;
});
