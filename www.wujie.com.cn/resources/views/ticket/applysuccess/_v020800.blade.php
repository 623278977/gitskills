@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/_v020800/apply_suss.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <!-- 打开APP -->
    <div class="app_install none" id="installapp" style="position: absolute;z-index: 99">
        <i class="l">打开无界商圈APP，体验更多精彩内容 >></i>
        <span class="r" id="openapp"><img src="{{URL::asset('/')}}/images/install_btn.png" alt=""></span>
        <div class="clearfix"></div>
    </div>
    <section class="succ-sec">
        <div class="head"></div>
        <div class="cont  ml3 mr3 pt4">
        	<div class="design mb4 pl3">
        		<span class="des_name bold f16 color333 b mb2"></span><br />
        		<span class="medium f14 color333 mr-108 begin_time"></span>
        	</div>
        	<p class="lines mb4">
        		<span class="line_round lines_l "></span>
        		<span class="deshed"></span>
        		<span class="line_round lines_r"></span>
        	</p>
        	<ul class="pl3 pr3">
        		<li class=" pb3 dis_bet"><span class="f14 color333 medium">参会人姓名</span><span class="f14 c8a869e medium names"></span></li>
        		<li class=" pb3 dis_bet"><span class="f14 color333 medium">手机号</span><span class="f14 c8a869e medium tel"></span></li>
        		<li class=" pb3 dis_bet"><span class="f14 color333 medium">公司</span><span class="f14 c8a869e medium company"></span></li>
        		
        		<li class=" pb1-5 dis_bet"><span class="f14 color333 medium">职位</span><span class="f14 c8a869e medium job"></span></li>
        		<li class="dis_bet"><span class="deshed"></span></li>
        		<li class="dis_bet pt1-5 pb3"><span class="f14 color333 medium">票务类型</span><span class="cff4d64 f14 medium type"></span></li>
        		<li class="dis_bet pb4 invite_way"><span class="f14 color333 medium">邀请人</span>
        			<div class="">
        				<span class="cff4d64 f14 medium agent_name"></span>
        				<span class="cff4d64 f14 medium">(经纪人)</span>
        			</div>
        		</li>
        	</ul>
        </div>
        <div class="apply_text mr5 ml5 mt1-5 mb2 ">
        	<p class="texta f12 medium color999">
        		A.报名订单已经放入你的账号，请登录无界商圈查看报名订单。现场签到出示订单信息或提供无界商圈账号二维码即可完成签到工作。
        	</p>
        	<p class="textb f12 medium color999">
        		B.活动开始前一天及活动当天会有相应推送通知，请保持手机畅通。请尽量不要迟到，准时或提前20分钟达到相应会场，感谢你的配合和支持！
        	</p>
        </div>
        <div class="member bgwhite pl1-5 none">
        	<p class="fline mem_p"><span class="mem_text bold f14 color333">已报名成员</span>&nbsp;<span class="mem_num f14 bold c00a0ff"></span></p>
        	<div class="pt1 pb1 mem_ava">
        		<!--<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>
        		<img src="/images/default.jpg" class="mr1 avaters mb1"/>-->
        	</div>
        	<p class="mem_more pt1-5 pb1-5 none"><span class="c8a869e f12 medium ">查看更多</span></p>
        </div>
        <div id="succ_id" class="none" data-id=''></div>
        <img src="" id="succ_shareimg" class="none">
        <div id="succ_des" class="none"></div>
        <div id="citys" class="none"></div>
        <p class="foot1 ml5 pt2-5 pb2"><span class="f12 medium cff4d64">*</span>&nbsp;<span class="f12 color999 medium">如有报名疑问请拨打客服热线 400-011-0061</span></p>
        <p class="foot_red">
        	<span class="f12 medium cff4d64">分享报名信息，邀请好友报名活动</span><br />
        	<span class="mt1 mb1 f12 medium cff4d64">赚取无界积分，获得活动报名绿色通道</span>
        </p>
        <div class="btn act_id" actId="">
    		<img src="/images/agent/icon-share3.png" class="mr1 share"/>
    		<span class="f16 medium " >分享报名</span>
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
	<script src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
    <script src="{{URL::asset('/')}}/js/_v020800/applysuccess.js"></script>
@stop