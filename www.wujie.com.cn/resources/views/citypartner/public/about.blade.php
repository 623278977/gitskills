@if(!is_null($partner))
        <!DOCTYPE html >
<html>
<head lang="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>关于我们</title>
    <link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}css/citypartner/share.css"/>
    <link rel="stylesheet" href="/css/citypartner/reset.css">
    <link rel="stylesheet" href="/css/citypartner/common.css">
    <link rel="stylesheet" href="/css/citypartner/w-pages.css">
</head>
<body>
<header>
    <div class="header">
        <div class="login">
            <div>
                <ul>
                    <li>你好，<a href="/citypartner/account/list?uid={{$partner->uid}}">{{ $partner->realname ?: $partner->username}}</a></li>
                    <li><a href="{{url('citypartner/public/loginout')}}">退出</a></li>
                    <li class="message">
                        <span>|</span>
                        <a href="{{url('citypartner/message/list')}}">消息通知</a>
                        @if($count>0) <img src="/images/citypartner/img/xiaoxi.png" alt=""/> @endif
                    </li>
                </ul>
            </div>
        </div>
        <div class="nav">
            <div class="nav_brand">
                <a href="/citypartner/account/list?uid={{$partner->uid}}">
                    <img src="/images/citypartner/img/logo_01.png" alt="城市合伙人"/>
                </a>
            </div>
            <div class="user">
                <div>
                    <a class="head" href="/citypartner/account/list?uid={{$partner->uid}}">
                        <img src="{{ getImage($partner->avatar,'avatar','') }}" alt="">
                    </a>

                    <div class="user_name">
                        <div>
                            <p>{{ $partner->realname ?: $partner->username}}</p>
                            <a >城市合伙人</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="nav_menu">
                <ul>
                    <li><a href="{{url('citypartner/public/index')}}">首页</a></li>
                    <li><a href="{{url('citypartner/maker/index')}}">我的OVO中心</a></li>
                    <li class="third"><a href="{{url('citypartner/myteam/index')}}">我的团队</a></li>
                    <li><a href="{{url('citypartner/profit/list')}}">我的收益</a></li>
                    <li><a href="{{url('citypartner/business/list')}}">我的业务</a></li>
                </ul>
            </div>
        </div>
    </div>
</header>

@else
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>关于我们</title>
    <link rel="stylesheet" href="/css/citypartner/reset.css">
    <link rel="stylesheet" href="/css/citypartner/animation.css">
    <link rel="stylesheet" href="/css/citypartner/common.css">
    <link rel="stylesheet" href="/css/citypartner/w-pages.css">
</head>
<body>
<div class="m-banner-bg" id="m-banner-bg" style="height: 120px;overflow: hidden;">
    <!--头部-->
    <div class="g-hd ">
        <div class="container">
            <div class="m-logo ">
                <img src="http://test.wujie.com.cn/images/citypartner/logo.png" alt="">

                <div class="m-about fr f14 ">
                    @if(empty($userinfo))
                        <a href="/citypartner/public/index" style="margin-right:650px;">首页</a>
                        <a href="/citypartner/public/index?login=now">立即登录</a>
                        <a href="/citypartner/public/index?register=now" class="register">立即注册</a>
                    @else
                        <a href="{{url('citypartner/account/list')}}">{{$userinfo->nickname?:$userinfo->username}}</a>
                        <a href="{{url('citypartner/message/list')}}">消息中心</a>
                        <a href="/citypartner/public/loginout">退出账号</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endif

<div class="g-about">
    <div class="container aboutUs clearfix">
        <div class="m-side fl">
            <ul class="tc">
                <span>关于我们</span>
                <li @if($type==1) class="cur" @endif><a href="#">技术</a></li>
                <li @if($type==2) class="cur" @endif><a href="#">布局</a></li>
                <li @if($type==3) class="cur" @endif><a href="#">招商</a></li>
                <li @if($type==4) class="cur" @endif><a href="#">创投</a></li>
                <li @if($type==5) class="cur" @endif><a href="#">PPP</a></li>
                <li @if($type==6) class="cur" @endif><a href="#">海外</a></li>
                <li @if($type==7) class="cur" @endif><a href="#">教育</a></li>
                <li @if($type==8) class="cur" @endif><a href="#">金融</a></li>
                <li @if($type==9) class="cur" @endif><a href="#">收益</a></li>
                <li @if($type==10) class="cur" @endif><a href="#">加入我们</a></li>
            </ul>
        </div>
        <div class="m-context fl ">
            <div class="box @if($type==1) @else hide @endif">
                <h2>技术</h2>

                <div class="line"><img src="/images/citypartner/line.png" alt=""></div>
                <p>
                    天涯若比邻整合了国际领先的视频会议和互联网技术，独创OVO（online-video-offline）模式，提供综合化解决方案，让信息得以高效快速地实现连接、共享、传播。
                </p>

                <p>
                    <b>Online</b>
                </p>

                <p>
                    线上沟通平台。包括网站、移动端APP，可提供信息聚合、信息检索、即时通讯、在线社区、视频点播、视频直播、线上支付、交易等多种多样的产品和沟通形态。
                </p>

                <p>
                    <b>Video</b>
                </p>

                <p>
                    天涯云视频会议沟通平台。高可靠性和灵活扩充性的综合性云技术；线下运营中心支撑、移动端支撑、PC端支撑；支持会议接入、会议视频录制、直播与点播；
                </p>

                <p>
                    <b>
                        Offline
                    </b>
                </p>

                <p>
                    线下服务落地。通过遍布国内各省市及海外的各个OVO运营中心落地服务，可以开展包括路演、对接、培训等等各种形式的交流和互动。
                </p>
            </div>
            <div class="box @if($type==2) @else hide @endif">
                <h2>布局</h2>

                <div class="line"><img src="/images/citypartner/line.png" alt=""></div>
                <p>
                    <b>城市布局</b>
                </p>

                <p>
                    在杭州、深圳、北京等国内一线城市相继落地OVO运营中心，并且在美国硅谷、韩国首尔等地开设国外运营点，目前已经布局全球100余个运营网点。真正打破了地域和空间的限制，为资源的交互提供了基础。

                </p>
                <br>
                <br>

                <p>
                    <b>行业布局</b>
                </p>

                <p>
                    平台陆续布局和细分出6大行业：
                </p>

                <p>

                    招商服务、PPP服务、教育/培训服务、创投服务、新三板投资服务、海外服务。在多年对这6大行业的布局、运营中，天涯若比邻积累了丰富的政府、项目、资金、人脉圈层等资源，并在2016年天涯推出“城市合伙人”计划，让一二线城市和三四五线城市实现信息流通、人才流通、资金流通、圈层流通。
                </p>


            </div>
            <div class="box @if($type==3) @else hide @endif">
                <h2>招商</h2>

                <div class="line"><img src="/images/citypartner/line.png" alt=""></div>
                <p>
                    通过天涯若比邻遍布全球的百余个运营中心和网真视频会议系统打通国际通道，为政府、园区、企业和专业机构提供一对多点的跨域招商、人才引进服务，通过引进项目、人才为政企无缝对接，从根本上解决招商问题，提供精准高效的对接服务，实现常态化的招商，从而提高招商效率，推动区域经济发展。
                </p>
                <table class="mt20">
                    <thead>
                    <tr style="font-weight: bold">
                        <td style="width: 120px">政府招商</td>
                        <td colspan="2">服务内容</td>
                        <td>目标客户群</td>
                        <td>市场定价</td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>招商引资服务</td>
                        <td colspan="2">提供国内外跨域招商推介服务，为政府、园区、企业对接，
                            提供足不出户的多点互动的招商服务
                        </td>
                        <td>招商局、商务局、
                            商协会
                        </td>
                        <td>20-50万/场</td>
                    </tr>
                    <tr>
                        <td>
                            人才引进服务
                        </td>
                        <td colspan="2">
                            提供国内外人才、项目引进服务，以网真视频会议系统
                            为服务通道，为政府、园区、专业机构等客户举办国内、
                            国际线上路演活动，协助海内外人才、项目落地
                        </td>
                        <td>
                            科技局、人才办、猎头公司、投资机构、个人投资者、众创空间、创业者、企业等
                        </td>
                        <td>一事一议</td>
                    </tr>
                    <tr>
                        <td>咨询培训</td>
                        <td colspan="2">提供国内外跨域咨询与培训活动，为政府、园区、企业等
                            提供海内外跨域专题培训等服务
                        </td>
                        <td>
                            政府、园区、孵化器、投资机构、个人投资者、众创空间、创业者、企业等
                        </td>
                        <td>3万/场起</td>
                    </tr>
                    <tr>
                        <td rowspan="3">增值服务</td>
                        <td style="width: 80px">产业规划</td>
                        <td>通过商圈汇聚的海量资源，为政府、园区精准
                            对接专业的规划机构，提供园区发展规划、
                            招商引资方案编制、区域产业规划等服务
                        </td>
                        <td>人民政府、招商局、商务局、园区、建设机构等</td>
                        <td>一事一议</td>
                    </tr>
                    <tr>
                        <td>国内考察</td>
                        <td>
                            实现项目和资方的快速沟通、对接
                            只要投递，24小时内必有回应
                        </td>
                        <td>招商局、商务局、科技局、投资机构、企业等</td>
                        <td>就当地情况
                            政府给予考察补助
                        </td>
                    </tr>
                    <tr>

                        <td>媒体宣传</td>
                        <td>信息高度匹配的在创投圈内传播</td>
                        <td>
                            招商局、商务局、人才办、科技局、
                            企业、投资机构等
                        </td>
                        <td>
                            一事一议
                        </td>
                    </tr>

                    </tbody>
                </table>
            </div>
            <div class="box @if($type==4) @else hide @endif">
                <h2>创投</h2>

                <div class="line"><img src="/images/citypartner/line.png" alt=""></div>
                <p>
                    在双创的浓厚创业氛围中，“给资本寻觅项目，给项目对接资本”成了一个主旋律。结合天涯若比邻跨域视频设备系统的创投服务，打破路演本地化局限，促进项目跨域沟通，实现资本高效对接。
                </p>
                <table class="mt20">
                    <thead>
                    <tr style="font-weight: bold">
                        <td style="width: 120px;">创投服务</td>
                        <td>服务内容</td>
                        <td>目标客户群</td>
                        <td>市场定价</td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>常规路演活动</td>
                        <td>每月常态化路演活动，策划相应路演活动主题，
                            筛选项目和定向邀约投资人，可供当地项目方、
                            投资机构以及众创空间观摩和学习
                        </td>
                        <td>政府部门中创业创新相关机构，众创空间创业项目</td>
                        <td>免费</td>
                    </tr>
                    <tr>
                        <td>专场项目融资路演</td>
                        <td>集项目方或创客空间提出的项目融资需求，
                            由天涯若比邻组织投资人、投资机构参加的融资路演活动
                        </td>
                        <td>政府部门中创业创新相关机构，众创空间创业项目</td>
                        <td>1-8万/场</td>
                    </tr>
                    <tr>
                        <td>封闭式路演</td>
                        <td>定向投融资对接的多地跨域路演，对投资人有准入要求</td>
                        <td>投资机构/人
                            个人投资者
                        </td>
                        <td>0.1-0.5万/场</td>
                    </tr>
                    <tr>
                        <td>投融资开放日</td>
                        <td>主题式在线交流与分享活动</td>
                        <td>投资机构、
                            众创空间、创业者
                        </td>
                        <td>免费</td>
                    </tr>
                    <tr>
                        <td>资方直通车</td>
                        <td>实现项目和资方的快速沟通、对接
                            只要投递，24小时内必有回应
                        </td>
                        <td>投资机构、
                            众创空间、创业者
                        </td>
                        <td>免费</td>
                    </tr>
                    <tr>
                        <td>宣传推广服务</td>
                        <td>信息高度匹配的在创投圈内传播</td>
                        <td>众创空间、
                            项目方、资方
                        </td>
                        <td>一事一议</td>
                    </tr>
                    <tr>
                        <td>股权众筹</td>
                        <td>发展线上渠道，打造股权众筹平台，
                            撬动社会闲散资金，创造财富价值
                        </td>
                        <td>资方、投资机构、
                            高净值人群
                        </td>
                        <td>一事一议</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="box @if($type==5) @else hide @endif">
                <h2>PPP项目服务</h2>

                <div class="line"><img src="/images/citypartner/line.png" alt=""></div>
                <p>PPP项目是未来10年的又一个政府红利区。平台提供PPP项目的综合咨询、培训和风控筛选指导，使项目在资金安全、信息披露、项目风险和交易上，力求透明化和阳光化。</p>

                <p>并且，基于天涯云跨域视频设备系统，为PPP项目与资本搭建一对多点跨域路演平台，解决传统项目路演过程中社会资源浪费及效率低下等问题。</p>
                <table class="mt20">
                    <thead>
                    <tr style="font-weight: bold">
                        <td style="width: 120px">PPP服务</td>
                        <td>服务内容</td>
                        <td>目标客户群</td>
                        <td>市场定价</td>
                    </tr>
                    <tr>
                        <td>PPP咨询</td>
                        <td>PPP项目全生命周期综合咨询服务</td>
                        <td>PPP项目相关单位、
                            政府部门、中介服务机构、资方等
                        </td>
                        <td>就具体项目报价</td>
                    </tr>
                    <tr>
                        <td>PPP培训</td>
                        <td>PPP领域各类培训服务</td>
                        <td>PPP项目相关单位、
                            政府部门、中介服务机构、资方等
                        </td>
                        <td>1-8万/场</td>
                    </tr>
                    <tr>
                        <td>PPP项目识别</td>
                        <td>筛选可包装成PPP模式的项目，供政府参考</td>
                        <td>政府官员、
                            企业高管、项目代表
                        </td>
                        <td>按地方实际
                            情况定价
                        </td>
                    </tr>
                    <tr>
                        <td>PPP项目路演</td>
                        <td>为PPP项目与资本搭建一对多点跨域路演平台，
                            优选精准匹配后的PPP项目参与路演
                        </td>
                        <td>PPP项目相关单位、
                            政府部门、央企、
                            国企、律会所、服务机构、投资机构等
                        </td>
                        <td>8-20万/场</td>
                    </tr>
                    <tr>
                        <td>PPP金融通道</td>
                        <td>设立创新型PPP互联网金融通道，开展路演活动，
                            对接全球优质资本及高净值人群
                        </td>
                        <td>PPP项目相关单位、
                            央企、国企、
                            投资机构等
                        </td>
                        <td>就具体项目报价</td>
                    </tr>
                    <tr>
                        <td rowspan="2">PPP项目考察交流</td>
                        <td>考察当地PPP项目，就建设协调管理模式、
                            项目实施机构、招标，促进PPP项目落地
                        </td>
                        <td>PPP项目相关单位、
                            政府部门、央企、
                            国企、律会所、服务机构、投资机构等
                        </td>
                        <td>就具体项目报价</td>
                    </tr>
                    <tr>
                        <td>交流学习海外PPP项目和话题，组织考察团，
                            对接海外优质资源，开展海外考察交流
                        </td>
                        <td>PPP项目相关单位、
                            政府部门、央企、
                            国企、律会所、服务机构、投资机构等
                        </td>
                        <td>5-25万/人</td>
                    </tr>
                    </thead>
                </table>
            </div>
            <div class="box @if($type==6) @else hide @endif">
                <h2>海外</h2>

                <div class="line"><img src="/images/citypartner/line.png" alt=""></div>
                <p>汇聚全球优质资源，以全新的技术和模式打造海外OVO跨域智慧服务平台，可以与国内各个城市、园区直接线上无缝对接。</p>
                <table class="mt20" style="width: 100%">
                    <thead>
                    <tr style="font-weight: bold;">
                        <td>政府商圈</td>
                        <td>服务内容</td>
                        <td>目标客户群</td>
                        <td>市场定价</td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td rowspan="4">海外项目服务</td>
                        <td>提供海外城市、园区的实体展览宣传“名片”</td>
                        <td>政府园区孵化器</td>
                        <td>5万</td>
                    </tr>
                    <tr>
                        <td>提供海外城市创业大赛合作方</td>
                        <td>政府园区孵化器</td>
                        <td>一事一议</td>
                    </tr>
                    <tr>
                        <td>提供硅谷人才项目推介会及考察团一站式服务</td>
                        <td>政府园区孵化器</td>
                        <td>一事一议</td>
                    </tr>
                    <tr>
                        <td>提供海外媒体宣传服务</td>
                        <td>政府园区孵化器</td>
                        <td>一事一议</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="box @if($type==7) @else hide @endif">
                <h2>教育</h2>

                <div class="line"><img src="/images/citypartner/line.png" alt=""></div>
                <p>
                    天平台整合了一线城市的优质资源，为需求方提供各个行业领域的培训与咨询服务，培养本土专业人才。提供的个性化需求包括：电子商务专题、创客孵化与辅导、新三板全面咨询与培训服务等。比如在新三板方面，平台不定期邀请北上广深等地的证监会、银监会领导和干部，进行政策分析和宣导；请硅谷等海内外一线财经专家、大型券商资深从业者阐述新三板实操经验。</p>
                <table class="mt20" style="width: 100%;">
                    <thead>
                    <tr style="font-weight: bold">
                        <td>服务名称</td>
                        <td>服务内容</td>
                        <td>目标客户群</td>
                        <td>市场定价</td>
                    </tr>

                    </thead>
                    <tbody>
                    <tr>
                        <td>投资讲堂</td>
                        <td>个性化需求对接</td>
                        <td>本地企业
                            本地大众用户
                        </td>
                        <td>具体根据第三方
                            报价而定
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="box @if($type==8) @else hide @endif">
                <h2>金融（新三板投融资)</h2>

                <div class="line"><img src="/images/citypartner/line.png" alt=""></div>
                <p>
                    筛选全国最热门的新三板项目，促进投资人对拟挂牌企业或者是已挂牌企业的投资；平台不定期邀请北上广深等地的证监会、银监会领导和干部，进行政策分析和宣导；请硅谷等海内外一线财经专家、大型券商资深从业者阐述新三板实操经验。</p>
                <table class="mt20" style="width: 100%">
                    <thead>
                    <tr style="font-weight: bold">
                        <td>新三板服务</td>
                        <td>服务内容</td>
                        <td>目标客户群</td>
                        <td>市场定价</td>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>投资讲堂</td>
                        <td>大型券商或资产管理公司的新三板投资知识培训</td>
                        <td>合格投资人
                            投资机构
                        </td>
                        <td>免费</td>
                    </tr>
                    <tr>
                        <td>项目投资</td>
                        <td>投资人对拟挂牌企业或者是已挂牌企业的投资</td>
                        <td>投资机构
                            新三板企业
                            证券公司
                        </td>
                        <td>融资金额的3-5%</td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="box @if($type==9) @else hide @endif">
                <h2>收益</h2>

                <div class="line"><img src="/images/citypartner/line.png" alt=""></div>
                <b>1.人脉收益</b>

                <p>
                    成为城市合伙人，在当地必定是已经拥有了良好的人脉关系。事实上所谓的“六度人脉”原理，也是会受到地域的限制。而加入合伙人计划之后，“二维”的人脉关系将变成“四维”。跨越地域、时间和信息的限制，实现人脉的二次扩张和裂变，助力您的视野和事业。</p>
                <br>
                <b>2.现金收益</b>

                <p>
                    除了“人脉变银脉”之外，城市合伙人体系中主导的各项业务，您将获得很大一部分分成。同时，“金字塔形”的合伙人拓展机制，让您的下线渠道获得收益外，您作为上线渠道同样可以获得业绩和现金提成。双重收益的模式，让您获得更多的佣金叠加。</p>
                <br>
                <b>3.股权收益</b>

                <p>成为城市合伙人，并且达成年度指标后，您将得到天涯若比邻公司的期权股份，升级成为天涯的合伙人，分享公司估值提升后的股权收益。</p>
                <br>
                <b>4.拓展收益</b>

                <p>
                    事实上OVO的模式才是真正意义上O2O模式的第二次升级和飞跃。它是一条信息、沟通、资金、人才等等资源的高速公路。可以承载的业务和内容有着无限可能。合伙人在完成平台主导的业务之余，同样可以独立运作各种其他的项目和业务，或者把当地的资源放到这个高速公路中，变现给全国甚至全球。</p>
            </div>
            <div class="box @if($type==10) @else hide @endif">
                <h2>加入我们</h2>

                <div class="line"><img src="/images/citypartner/line.png" alt=""></div>
                <p>
                    2016年天涯推出“城市合伙人”计划，集合旗下6大行业的信息、资源和服务产品。通过OVO服务模式，让一二线城市和三四五线城市实现信息流通、人才流通、资金流通、圈层流通。未来还将会有更多的行业，在这个“服务业”的阿里巴巴平台中整合和输出。</p>

                <p>
                    基于天涯平台，城市合伙人可以提供政府智慧服务、海外项目服务、创投服务、新三板投融资、PPP项目服务以及第三方服务，并收获各项回报。
                </p>

                <p>加入我们，你可以交往到本地以外的优秀人士，实现行业的跨界，带来事业的飞升；也可以运营各项业务，参与金融业务的分销；在业绩达标以后，将得到天涯若比邻公司的期权，成为城市合伙人。</p>
                <br>

                <p>
                    <b>加入条件</b>
                </p>

                <p>
                    为提高城市合伙人质量，您只有获得邀请码，才能注册成为城市合伙人，成为城市合伙人以后，您亦会被分配到一个固定且唯一的新邀请码，该邀请码可用于邀请其他合作伙伴加入，并为您带更多收益；如果没有邀请码，请联系天涯若比邻官方客服。

                </p>
                <br>

                <p>
                    <b>OVO运营中心</b>
                </p>

                <p>OVO运营中心，是基于天涯若比邻海量资源和网真跨域视频设备系统而运作的线下运营中心。运营中心的装修和设计，按照公司的统一UI和标准进行。</p>

                <p>OVO运营中心开通以后，您便可选择性地接入天涯若比邻或者其它运营中心组织的跨域活动；一地多点，更好地实现资源的配置与共享。</p>

                <p>您可以将优质的活动信息，个性化地推荐给您的会员；</p>

                <p>您可以根据需求定制本地化活动，更好地维护和发展会员；</p>

                <p>会员将会给城市合伙人带来丰厚、可观的业绩；</p>

                <p>天涯若比邻诚挚地期待您的加入，一起共享资源和收益。</p>
                <br>

                <p style="text-align: right">联系我们</p>

                <p style="text-align: right">客服电话：400-033-0161</p>
            </div>

        </div>

        <div class="clearfix"></div>
    </div>
</div>
<div class="g-ft">
    <div class="container">
        <div class="m-foot tc">
            <p>服务热线：400-033-0161</p>

            <p>版权所有：©2012 - 2016 tyrbl.com, all rights reserved 杭州天涯若比邻网络信息服务有限公司浙ICP备案号：12021152号-2 </p>
        </div>
    </div>
</div>

<script src="/js/citypartner/jquery-1.8.3.min.js"></script>
<script src="/js/citypartner/PCASClass.js"></script>

<script src="/js/citypartner/index.js"></script>

</body>
</html>