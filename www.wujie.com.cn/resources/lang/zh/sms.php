<?php
/**
 * 短信模板
 * Created by PhpStorm.
 * User: yaokai
 * Date: 2017/10/12 0027
 * Time: 18:06
 */
return array(
    'standard' => ':code（动态验证码），请在15分钟内填写',
    /**注册-获取手机验证码（02-0）||找回密码等需要手机验证码的地方通用**/
    'registerCode' => ":code（动态验证码），请在15分钟内填写",

    'agent_invite_register' => ":code（动态验证码），请在15分钟内填写",

    'sms_loginCode' => "你正在登录无界商圈账号，验证码:code，请在15分钟内填写",

    /**注册-注册成功（02-2）**/
    'registerSuccess' => '恭喜你已经成功注册无界商圈账号，有更多需求可致电客服热线：400-011-0061',

    'forget_passwordCode' => '手机验证码:code（15分钟内有效），创业梦想由无界商圈助你实现！',

    'login' => "手机验证码:code（15分钟内有效），创业梦想由无界投融助你实现！",

    'activity_remind' => '你报名参加的活动[:subject]将于:time开始，届时请准时赴会。',

    'ovouserapply' => '感谢你提交OVO跨域对接申请，客服人员将与你取得联系确保OVO跨域对接的进行。如有疑问，请致电服务热线400-011-0061。',

    'ovoruzhi' => '你已成功更换OVO中心，当前入驻：:maker。如有疑问，请致电服务热线400-011-0061。',

    'tendaysnologin' => '咦？已经很久没有翻我牌了。快来看看最近即将举办的跨域活动、直播吧。',
    'activitySiteSign' => '报名活动成功。活动：:name将于:time举办。届时请准时赴会，感谢你的信赖和合作。点击查看:url，如有疑问，请致电服务热线400-011-0061。',
    'activityLiveSign' => '报名活动成功。活动：:name将于:time举办。届时请准时观看直播，感谢你的信赖和合作。点击查看:url，如有疑问，请致电服务热线400-011-0061。',
    'liveBegin' => '你预定的直播即将1小时后准点开始。直播名称：:name。届时请准时观看直播：:url。快去无界商圈APP上观看直播吧！如有疑问，请致电服务热线400-011-0061。',
    'h5LiveBegin' => '您关注的:name还有30分钟就要开始啦，手头上的事已经安排妥当了吗，准备好和我一起观看直播了吗~',
    'activitySiteBegin' => '你报名的活动将在明天准点举办。活动：:name；活动开始时间：:time。届时请准时赴会，感谢你的信赖和合作。如有疑问，点击查看:url或请致电服务热线400-011-0061。',
    'activityLiveBegin' => '你报名的活动将在1小时后准点举办。活动：:name。届时请准时观看直播：:url。感谢你的信赖和合作。如有疑问，请致电服务热线400-011-0061。',
    'buyVip' => '成功购买无界商圈:vip_name :vip_term_name。你的:vip_name会员截止日期累积至:expire。如有疑问，请致电服务热线400-011-0061',

    /**活动申请提交**/
    'activity_apply_submit' => '感谢你提交活动申请，客服人员将会与你取得联系确定活动细节，我们愿为你提供一流的会议服务，联动全国各地OVO中心，将商机带到你身边。如有疑问，请致电服务热线400-011-0061。',

    'yearQianDao' => '你已签到成功，签到活动为:subject。如有疑问请联系会场工作人员，或致电客服人员400-011-0061。',
    //通过分享页面订阅直播
    'live_share_subscribe' => 'Hi~您已成功订阅，下载无界商圈APP，更多视频资讯，倾情为您献上http://api.wujie.com.cn/webapp/wjload/detail。',
    //直播分享 发送验证码 已注册
    'live_share_registered' => '亲爱的用户您好，动态验证码：:code，请在15分钟内填写。感谢您快速登录“无界商圈APP”账号，下载“无界商圈APP”可查看更多高清视频。',
    //直播分享 发送验证码 未注册
    'live_share_unregister' => '亲爱的用户您好，动态验证码：:code，请在15分钟内填写。感谢您快速注册“无界商圈APP”账号，查看更多视频请下载“无界商圈APP”并完善账号信息。',

    //添加好友
    'addbuddy' => '（:nickname）等待您通过好友验证，这样你们就可以聊天了。',
    //直播分享 发送验证码 未注册
    'brand_enter' => '你好，:brand_name已提交审核，1-2工作日内会有客服人员和您进行联系，请保证手机的畅通。',

    //购买招商会立即加盟产品后
    'brand_prepay' => '你已经加盟:brand_name，订单编号为：:order_no，1-2工作日内会有客服人员和你进行联系，请保证手机的畅通。',
    /**2015-2016 年会抽奖**/
    'yearPrize' => '祝贺:name，手机号:tel，在天涯若比邻年终大会上获得礼品:prize；请及时领取，过期作废哦！',

    'invite_score' => '恭喜你,(:name)填写了你的邀请码并注册了账号,100积分已到账户中,打开app查看我的积分',
    'vipExpiration' => '你的“:name”会员将于2天后到期，请前往无界商圈App及时续费。如有疑问，请致电服务热线400-011-0061',
    'receiveTicket' => '活动现场门票领取成功。活动:name将于:time举办。届时请准时赴会，感谢你的信赖和合作。如有疑问，请致电服务热线400-011-0061',
    'welcome' => '亲爱的用户，欢迎加入无界商圈，我们致力将优质的资源带到你的城市！点击下载无界商圈。如有疑问，请致电服务热线400-011-0061',
    'obtainTicket' => '恭喜你获得“:name”的门票:num张。请前往无界商圈完成现场门票的领取。直播门票已放入你手机号对应的账户门票，请及时查收。如有疑问，请致电服务热线400-011-0061。',


    //投资人模板
    'agent_registerCode' => ":code（动态验证码），请在15分钟内填写", //15343
    'agent_sms_loginCode' => "你正在登录无界商圈账号，验证码:code，请在15分钟内填写",
    'agent_get_passwordCode' => ":code（动态验证码），请在15分钟内填写",//15343

 
    //经纪人注册成功发送 51772
    // 'agent_register_success_info' => '恭喜成为无界商圈经纪人！无界商圈为你提供宽广平台，体验更IN品牌加盟模式！',
    //线上新手教程地址  https://api.wujie.com.cn/webapp/agent/headline/detail?id=257&agent_id=2&is_share=1&from=singlemessage&isappinstalled=0
    'agent_register_success_info' => '恭喜您成为无界商圈经纪人！无界商圈为你提供宽广平台，体验更IN品牌加盟模式！了解更多经纪人玩法，详情点击（:url）回复T退订',


    //向投资人推送数据
    'rob_bill_success_info' => "您好，我是:brand的代理经纪人:name。很高兴为你进行品牌加盟跟进服务，我会为你提供最佳的加盟服务。",
    'user_info' => '为了更好的与你取得联系和进行业务沟通，请点击对话窗右上按钮，对我公开您的联系方式。',
    'contract_success_info' => '感谢你选择无界商圈并成功加盟品牌【:brand - 新体验slogan】。无界商圈，只做有实效的招商，为你提供OVO场景化招商服务。请对我的服务做出评价。点击：:url 对我进行评价反馈。',


    ######## 投资人 -- 经纪人发送的相关信息 #############

    //活动相关信息提示
    'activity_consent_notice'         => '该活动邀请函您已接受过了，请勿重复操作',
    'activity_reject_notice'          => '该活动邀请函您已拒绝了，请联系您的关系经纪人',
    'activity_tui_consent_notice'     => '投资人:name接受了您的活动邀请函',
    'activity_tui_reject_notice'      => '投资人:name拒绝了您的活动邀请函',
    'activity_rong_consent_notice_01' => "接受了经纪人：:agent_name 发起的【OVO活动邀请】。\r\n活动名称：:activity_name\r\n活动时间：:time\r\n活动场地：:place_zone \r\n点击：:url 查看活动邀请函。",
    'activity_rong_reject_notice_01'  => "拒绝了经纪人：:agent_name 发起的【OVO活动邀请】。\r\n拒绝理由：:statement，很遗憾无法到场。\r\n\r\n点击：:url 查看活动邀请函。",
    'activity_rong_consent_notice_02' => ":customer_name 接受了经纪人：:agent_name 发起的【OVO活动邀请】。\r\n活动名称：:activity_name\r\n活动时间：:time\r\n活动场地：:place_zone \r\n点击：:url 查看活动邀请函。",
    'activity_rong_reject_notice_02'  => ":customer_name 拒绝了经纪人：:agent_name 发起的【OVO活动邀请】。\r\n拒绝理由：:statement，很遗憾无法到场。\r\n\r\n点击：:url 查看活动邀请函。",

    //考察相关信息提示
    'inspect_consent_notice'          => '该考察邀请函您已接受过了，请勿重复操作',
    'inspect_reject_notice'           => '该考察邀请函您已拒绝了，请联系您的关系经纪人',
    'inspect_tui_consent_notice'      => "投资人:name接受了您的考察邀请函",
    'inspect_tui_reject_notice'       => "投资人:name拒绝了您的考察邀请函",
    'inspect_rong_content_notice_01'  => "接受了经纪人：:agent_name 发起的【考察邀请】。\r\n品牌：:brand_name  - 不一样的体验 \r\n订金：¥ :money \r\n考察门店：:store_name \r\n所在地区：:place \r\n考察时间：:time \r\n点击：:url 查看考察邀请函。",
    'inspect_rong_reject_notice_01'   => "拒绝了经纪人：:agent_name 发起的【考察邀请】。\r\n品牌：:brand_name  - 不一样的体验 \r\n订金：¥ :money \r\n拒绝理由：:statement。\r\n点击：:url 查看考察邀请函。",
    'inspect_rong_content_notice_02'  => ":customer_name 接受了经纪人：:agent_name 发起的【考察邀请】。\r\n品牌：:brand_name  - 不一样的体验 \r\n订金：¥ :money \r\n考察门店：:store_name \r\n所在地区：:place \r\n考察时间：:time \r\n点击：:url 查看考察邀请函。",
    'inspect_rong_reject_notice_02'   => ":customer_name 拒绝了经纪人：:agent_name 发起的【考察邀请】。\r\n品牌：:brand_name  - 不一样的体验 \r\n订金：¥ :money \r\n拒绝理由：:statement。\r\n点击：:url 查看考察邀请函。",

    //合同相关信息提示
    'contract_consent_notice'         => '该份合同您已签订过了，请勿重复操作',
    'contract_reject_notice'          => '该份合同您已拒绝了，请联系您的关系经纪人',
    'contract_tui_consent_notice'     => '投资人:name接受了您的合同',
    'contract_tui_reject_notice'      => '投资人:name拒绝了您的合同',
    'contract_rong_content_notice_01' => "接受了经纪人：:agent_name 发起的【付款协议】。\r\n合同号：:contract_no \r\n品牌：:brand_name - 不一样的体验 \r\n总加盟费：¥ :money \r\n线上首付：¥ :first_money \r\n首付支付状态：已支付 \r\n首付支付时间：:time \r\n* 考察邀请如有支付订金，则按照抵扣金额在首付支付中进行相应抵扣。\r\n* 尾款支付情况请在付款协议详情中查看。\r\n点击：:urls 查看付款协议。",
    'contract_rong_reject_notice_01'  => "拒绝了经纪人：:agent_name 发起的【付款协议】。\r\n品牌：:brand_name - 不一样的体验 \r\n总加盟费：¥ :money \r\n拒绝理由：:statement。\r\n\r\n点击：:urls 查看付款协议。",
    'contract_rong_content_notice_02' => ":customer_name 接受了经纪人：:agent_name 发起的【付款协议】。\r\n合同号：:contract_no \r\n品牌：:brand_name - 不一样的体验 \r\n总加盟费：¥ :money \r\n线上首付：¥ :first_money \r\n首付支付状态：已支付 \r\n首付支付时间：:time \r\n* 考察邀请如有支付订金，则按照抵扣金额在首付支付中进行相应抵扣。\r\n* 尾款支付情况请在付款协议详情中查看。\r\n点击：:urls 查看付款协议。",
    'contract_rong_reject_notice_02'  => ":customer_name 拒绝了经纪人：:agent_name 发起的【付款协议】。\r\n品牌：:brand_name - 不一样的体验 \r\n总加盟费：¥ :money \r\n拒绝理由：:statement。\r\n\r\n点击：:urls 查看付款协议。",


    ############ 向投资人推送邀请函提示信息  #############

    'activity_info_notice' => ':start_a 经纪人：:name（:zone_name）邀请你参加 [ :activity_name - 不一样的体验 ]实地门店考察。请尽快确认考察邀请函，可别错过哟~ :end_a',
    'inspect_info_notice'  => ':start_a 经纪人：:name（:zone_name）邀请你参加 [ :brand_name ]。请尽快确认活动邀请函，可别错过哟~ :end_a',
    'invite_info_notice'   => '温馨提示，你邀请了投资人：:customer_name（:zone_name）参加 [ :brand_name ] 门店考察将于明天（:times）进行。记得尽早联系品牌商务和投资人确认哟！',


    ############### 经纪人邀请投资人成功后需要发送的短信提示信息 ##################

    //活动邀请成功后发送短信 48214
    'invite_activity_success_info' => "您成功邀请投资人：:customer_name（:customer_tel）参加[:activity_name]。活动将于:activity_times举办，请及时通知投资人准时赴会。戳 :urls 访问活动邀请函详情页！",

    //合同首付成功给经纪人发送短信 48206
    'contract_pre_pay' => "恭喜促单成功！投资人：:name（:username）成功签约您发出的付款协议，加盟品牌：[:brand_title]；加盟费用：:amount人民币整；加盟时间：:pay_time。请前往无界商圈经纪人端查看佣金分成情况。戳 :shorturl 访问付款协议详情页！",
    //邀请函支付成功后给经纪人发送短信 48208
    'inspect_invitation_pay' => "您成功邀请投资人：:name（:username）对[:brand_title]进行门店考察。考察门店：[:store_title]；考察时间：:inspect_time；投资人支付订金：:amount元人民币整。考察前请及时和品牌商务代表取得确认。戳 :shorturl 访问考察邀请函详情页！",
    //合同首付成功给客户发送短信 47910
    'contract_pre_pay_customer' => "恭喜加盟成功！成功签约来自经纪人：:name（:zone）（:username）发送的付款协议，并加盟品牌：:brand_title-:slogan；加盟费用：:amount万人民币整；加盟时间：:pay_time。戳 :shorturl 访问付款协议详情页！",


    //邀请函支付成功后给客户发送短信 50089
    'inspect_invitation_pay_customer' => "您成功接受了来自经纪人：:name（:zone）的品牌考察邀请函。考察品牌：:brand_title-:slogan；考察门店：[:store_title]；考察时间：:inspect_time；已支付订金：:amount元人民币整。考察前请记得与经纪人取得联系。戳 :shorturl 访问考察邀请函详情页！",
    //'red_deduction_info_notice'       => '您成功使用了无界商圈投资人邀请红包支付考察订金，我们温馨的提醒您，请在一个月内时间完成该品牌的加盟事宜，如果逾期将无法正常使用考察订金抵扣加盟费用的优惠操作。如有疑问，请咨询您的经纪人或无界商圈客服人员。【无界商圈】',
    'red_deduction_info_notice'       => '您成功使用了无界商圈投资人邀请红包支付考察订金，我们温馨的提醒您，请在一个月内时间完成该品牌的加盟事宜，如果逾期将无法正常使用考察订金抵扣加盟费用的优惠操作。如有疑问，请咨询您的经纪人或无界商圈客服人员。',
    'time_soon_info_notice'           => "为了不影响您正常使用考察订金抵扣加盟费，请及时前往无界商圈查看心仪品牌吧！~",

    //完成线下尾款支付后，引导用户进行评价, 给客户发送短信 47937
    'contract_tail_pay_customer' => "感谢你的信赖与支持，无界商圈与你一同携手把你心爱的店铺开起来！别忘了对你的成单经纪人：:name（:zone）进行服务点评哟！无界商圈也会对你进行后续服务跟进！戳 :shorturl 对经纪人进行服务评价！",

    ############  短信补充：成为邀请投资人进行短信提示  ##############
   // 'invite_customer_note_inform' => "您成功邀请投资人：:user_name（:user_tel）加入无界商圈！您获得一张”邀请红包“，当邀请投资人:user_name在无界商圈平台成功加盟品牌，您即可兑现”邀请红包“，获得邀请奖励1000元。",
    //'to_join_success_note_inform' => "您的邀请投资人：:user_names（:user_tels）在无界商圈平台成功加盟品牌【:brand_names】，您的”邀请红包“成功激活，获得邀请奖励1000元。戳:urls，查看具体情况~",
    'invite_customer_note_inform'   => "您成功邀请投资人：:user_name（:user_tel）加入无界商圈！您获得一张”邀请红包“，当邀请投资人:user_name在无界商圈平台成功加盟品牌，您即可兑现”邀请红包“，获得邀请奖励1000元。",
);