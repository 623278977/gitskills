<!--zhangxm-->
@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/notice.css" rel="stylesheet" type="text/css"/>
    <style type="text/css">
    	body {background: #F2F2F2;}
    	/*.act-1 {
			align-items: center;
			font-size:1.3rem;
			padding-bottom: 1.1rem;
		}*/
    </style>
@stop
@section('main')
    <section class="containerBox pl1-5 medium" id="containerBox" >
      <!--<div class="act mt2-25">  
      	<p class="top ">
      		<img src="/images/agent/time_03.png" class="mr1 "/><span class="time c2873ff b f13">09/23</span>
      	</p>
      	<div class="bord-l ml08">
      		<div class="act-cont bgwhite mb1-2"> 
      			<div class="act-1 fline f13">
      				<span class="f13 b color333">无界商圈免费送创业红包，为你创业添砖加瓦哇哇哇哇哇</span>
      			</div>
      			<div class="not-2 ">
      				<div class="act-2l">
      					<img src="" class="not-2limg mr1"/>
      					<span class="not-area f11 color999">自定义文字自定义文字自定义文字自定义文字</span>
      				</div>
      				<a href=""><img src="/images/jump.png"/></a>
      			</div>
      		</div>
      	</div>
      	
      	<p class="top">
      		<img src="/images/agent/time_03.png" class="mr1 "/><span class="time c2873ff b f13">09/23</span>
      	</p>
      	<div class="bord-l ml08">
      		<div class="act-cont bgwhite mb1-2">
      			<div class="act-1 fline f13">
      				<span class="over-text color666 f13 b color333">无界商圈免费送创业红包，为你创业添砖加瓦哇哇哇哇哇</span>
      			</div>
      			<div class="not-2 ">
      				<div class="act-2l">
      					<img src="" class="not-2limg mr1"/>
      					<span class="not-area f11 color999">后台展示后台编辑，运营自定义</span>
      				</div>
      				<img src="/images/jump.png"/>
      			</div>
      		</div>
      	</div>-->
      	
      	<!--抢单成功-->
      	<!--<p class="top">
      		<img src="/images/agent/time_03.png" class="mr1 "/><span class="time c2873ff b f13">09/23</span>
      	</p>
      	<div class="bord-l ml08">
      		<div class="act-cont bgwhite mb1-2">
      			<div class="act-1 fline f13">
      				<span class="f13 b color333">【抢单成功】与投资人：Xiaomeng 达成跟单关系！</span>
      			</div>
      			<p class="mt1-5"><span class="f11 color999">意向品牌：</span><span class="f11 color999 monad_brand">喜茶 - 不一样的体验</span></p>
      			<p class=""><span class="f11 color999">投资人：</span><span class="f11 color999 monad_name">Xavier（浙江 杭州）</span></p>
      			<p class="mb1-5"><span class="f11 color999">接单时间：</span><span class="f11 color999 monad_time">2017-10-11</span></p>
      			<p class="f11 color999">您可以尝试与投资人联系，了解他的投资具体意向。</p>
      			<p class="f11 color999">通过无界商圈平台发送活动邀请、考察邀请，最终促使投资人加盟品牌，在无界商圈App线上同意加盟协议，并交付首付订金。</p>
      			<p class="f11 color999">促单成功，您将获得最多XXXX元的佣金奖励。</p>
      			<p class="f11 color999">(小编偷偷的告诉你，不要让机会跑走，赶紧联系投资人吧~）</p>
      			<p class="c2873ff f11 mt1-5 investor_detail">投资人详情页>></p>
      		</div>
      	</div>-->
      	<!--佣金结清-->
      	<!--<p class="top">
      		<img src="/images/agent/time_03.png" class="mr1 "/><span class="time c2873ff b f13">09/23</span>
      	</p>
      	<div class="bord-l ml08">
      		<div class="act-cont bgwhite mb1-2">
      			<div class="act-1 fline f13">
      				<span class="f13 b color333">【2017年第三季度】本季度佣金结清啦！</span>
      			</div>
      			<p class="mt1-5"><span class="f11 color999">结算周期：</span><span class="f11 color999 monad_brand">2017年7月1日 - 9月30日</span></p>
      			<p class=""><span class="f11 color999">结算范围：</span><span class="f11 color999 monad_name">a、促单佣金；b、邀请奖励；c、团队分佣；d、其他奖励。</span></p>
      			<p class="mb1-5"><span class="f11 color999">本季度佣金总额：</span><span class="f11 color999 monad_time">71,000 元整</span></p>
      			<p class="f11 color999">提现须知，请绑定有效银行卡，200元以上不收取手续费用；200元以下收取10元手续费。</p>
      			<p class="f11 color999">如提现或佣金结算存疑，请联系无界商圈客服人员。</p>
      			<p class="c2873ff f11 mt1-5 my_wallet">点击查看金额详情说明>></p>
      		</div>
      	</div>-->
      	<!--活动报名成功-->
      	<!--<p class="top">
      		<img src="/images/agent/time_03.png" class="mr1 "/><span class="time c2873ff b f13">09/23</span>
      	</p>
      	<div class="bord-l ml08">
      		<div class="act-cont bgwhite mb1-2">
      			<div class="act-1 fline f13">
      				<span class="f13 b color333">【活动报名】成功报名：123454656活动名称</span>
      			</div>
      			<p class="mt1-5"><span class="f11 color999">活动时间：</span><span class="f11 color999 activity_time">2017-10-10  12:12:12</span></p>
      			<p class=""><span class="f11 color999">参与场地：</span><span class="f11 color999 activity_place">杭州（杭州OVO智创中心）</span></p>
      			<p class="f11 color999 mt1-5">注意事项：活动开始前30分钟到场，并完成活动签到，请妥善安排好自己行程，感谢配合。</p>
      			<p class="c2873ff f11 mt1-5 activity_tickets">点击查看我的门票>></p>
      		</div>
      	</div>
      	
      	
      	
      		
      </div>-->
      <!--<div class="default">
      		<img src="/images/agent/news_notice.png"/>
      </div>-->
      
    </section>
    <!--<section class="pl1-5 none notice_brand">
	    	
    	
    </section>-->
    <section class="pl1-5">
    	<!--<div class="none notice_brand">-->
	    	<!--<p class="top ">
	      		<img src="/images/agent/time_03.png" class="mr1 "/><span class="time c2873ff b brand_time"></span>
	      	</p>-->
	      	<!--<div class="bord-l ml08">
	      		<div class="act-cont bgwhite mb1-2">
	      			<div class="act-1 fline f13">
	      				<span class=" color666 f13 b color333">成功解锁新技能，成功代理品牌</span>
	      				<span class=" color666 f13 b color333 brand_name"></span>
	      				<span class=" color666 f13 b color333 brand_slogan"></span>
	      			</div>
	      			<div class="not-2">
	      				<div class="act-2l">
	      					<div>
	      						<p class="mb05 not-area">
	      							<span class="not-area f12 color999">您成功的成为了品牌</span>
	      							<span class="f12 color999 brand_name"></span>
	      							<span class="f12 color999 brand_slogan"></span>
	      							<span class="f12 color999">】的代理经纪人。</span>
	      						</p>
	      						<p class="not-area mb05">
		      						<span class=" f12 color999">成为代理经纪人后，品牌意向投资人将会通过无界商圈系统派单至您。您可以通过投资人的个人描述以及接单意向，最终选择是否对其进行跟单操作。</span>
		      					</p>
		      					<p class="not-area mb05">
		      						<span class=" f12 color999">确认跟单关系后，您将成为该投资人的品牌跟进人，品牌的活动、考察、资讯、报价等均由您进行跟进和管理。</span>
		      					</p>
		      					<p class="not-area mb05">
		      						<span class=" f12 color999">我们希望最终您能邀请投资人在无界商圈平台上加盟品牌，最终为您创造丰厚的佣金提成。</span>
		      					</p>
		      					<p class="not-area mb05">
		      						<span class=" f12 color999">跟单途中如有疑问，请致电无界商圈客服人员进行相关询问。</span>
		      					</p>
		      					<p class="not-area mb05">
		      						<span class=" f12 color999 headline text_line" headline_id="">了解更多无界商圈经纪人玩法</span>
		      					</p>
	      					</div>
	      				</div>
	      			</div>
	      		</div>
	      	</div>-->
	    <!--</div>-->
    	
    	
    	<!--<p class="top ">
      		<img src="/images/agent/time_03.png" class="mr1 "/><span class="time c2873ff b agent_time"></span>
      	</p>-->
      	<!--<div class="bord-l ml08">
      		<div class="act-cont bgwhite mb1-2">
      			<div class="act-1 fline f13">
      				<span class="over-text color666 f13 b color333">从今天起，你就是无界商圈专业的经纪人！</span>
      			</div>
      			<div class="not-2 ">
      				<div class="act-2l">
      					<div>
      						<p style="display: flex;" class="mb05">
      							<span class="not-area f12 color999">Hi，</span><span class=" f12 color999 nickname"></span>
      						</p>
	      					<p class="not-area mb05">
	      						<span class=" f12 color999">欢迎加入无界商圈，并选择我们作为成长的平台。</span>
	      					</p>
	      					<p class="not-area mb05">
	      						<span class=" f12 color999">无界商圈OVO品牌招商推广平台，隶属于天涯若比邻网络信息服务有限公司，成立于2012年，总部位于杭州。下设四大区域运营中心（杭州、广州、成都、北京）和100多个城市运营中心。是一家互联网+综合商业服务与跨域资源共享平台。 公司运用国际领先的天涯云网真视频会议系统和互联网直播技术，独创OVO（online-video-offline）场景化招商服务模式，解决跨域（时间和空间）信息传递，提供了一套综合化的解决方案，让信息得以高效快速地实现连接、共享、传播，实现优质资源匹配。目前无界商圈服务有：品牌招商服务、培训服务、政府智慧服务、投融资对接服务、海外项目服务、第三方服务 等6大行业服务方向。</span>
	      					</p>
	      					<p class="not-area mb05">
	      						<span class=" f12 color999">成为一名专业的经纪人，成为优质品牌的代理，向无界商圈投资客进行品牌宣传、包装，邀请投资客参加无界商圈OVO活动、品牌实地考察，最终邀请成单。</span>
	      					</p>
	      					<p class="not-area mb05">
	      						<span class=" f12 color999">经纪人将作为中间力量，衔接品牌和投资人，在无界商圈的平台上碰撞出火花。经纪人通过投资人邀请、成单，获得邀请、促单奖励。无界商圈为经纪人提供佣金保障，海量佣金赚不停。</span>
	      					</p>
	      					<p class="not-area mb05">
	      						<span class=" f12 color999 headline text_line" headline_id="">了解更多无界商圈经纪人玩法</span>
	      					</p>
	      					<p class="not-area mb05">
	      						<span class=" f12 color999 brand_list text_line ">尝试代理第一个品牌</span>
	      					</p>
	      					<p class="not-area mb05">
	      						<span class=" f12 color999 letter text_line" agent_id="">邀请好友注册无界商圈投资人</span>
	      					</p>
      					</div>
      				</div>
      			</div>
      		</div>
      	</div>-->
    </section>
@stop
@section('endjs')
<script src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
<script src="{{URL::asset('/')}}/js/agent/_v010100/notice.js"></script>
@stop