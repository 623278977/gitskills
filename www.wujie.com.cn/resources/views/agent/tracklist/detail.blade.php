@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/v010000/tracklist.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="{{URL::asset('/')}}/css/v010000/swiper.min.css">
@stop
@section('main')
    <section id="act_container" class="none">
    <!-- 公用蒙层 -->
    <div class="tips none"></div>
    <div class ="bg-model none">
　　   <div class ='ui_content'>
		<div class="ui_task ui-border-b relative">
			<span class="f14">任务单</span>
			<img  class="ui_bi  absolute haha"  src="{{URL::asset('/')}}/images/020700/w20.png">
		</div>
		<div class="ui_task_detail f14 color666">
			<p>我们列举了在跟单过程中最典型的几个事件</p>
			<p>点，为你梳理当前的跟进情况，并依次做判</p>
			<p>断跟进效果。</p>
			<p>
			    <span >
			         <img  class="ui_bi p5"  src="{{URL::asset('/')}}/images/020700/w22.png">
			    </span>获得投资人电话及其他通讯方式
			    <span class="fr color666 time1 none">2017/5/21</span>
			</p>
			<p> 
			    <span>
			         <img  class="ui_bi p2"  src="{{URL::asset('/')}}/images/020700/w22.png">
			    </span>邀请用户参加OVO发布会<span class="fr color666 time2 none">2017/5/21</span>
			</p>
			<p> 
				<span>
			         <img  class="ui_bi p3"  src="{{URL::asset('/')}}/images/020700/w22.png">
			    </span>邀请用户总部或门店考察<span class="fr color666 time3 none">2017/5/21</span>
			</p>
			<p> 
				<span>
			         <img  class="ui_bi p4"  src="{{URL::asset('/')}}/images/020700/w22.png">
			    </span>签署<span id="ui_brand">喜茶</span>付款协议,促单成功<span class="fr color666 time4 none">2017/5/21</span>
			</p>
		</div>
       </div>
    </div>

   <!-- 头部信息栏 -->
   <div class="ui_track_top">
   		<ul class="ui_track_top_text" id="com_infor">
   			<li>
   				<img id="nickpict" class="ui_img1"  src="{{URL::asset('/')}}/images/default/avator-m.png">
   			</li>
   			<li>
   				<div class="ui_nickname">
   					<span class="c_nickname">路人甲</span><img id="sex" class="ui_img2 "  src="{{URL::asset('/')}}/images/020700/person.png">
   				</div>
   				<div style="width:100%;height:0.5rem"></div>
   				<div class="ui_trackname">跟进品牌：<span class="ui_brandname">哈哈哈</span></div>
   			</li>
   			<li>
   				<a class="ui-border-radius-8a common">短信</a><a class="ui-border-radius-8a common">电话</a><a class="ui-border-radius-8a common">进入聊天窗</a>
   			</li>
   		</ul>
   </div>
   <div class="ui_track_status clear">
   	<span class="color333 f12">跟单状态</span>
   	<span class="slogan color999 f12 fr">xxxxxxxxxx</span>
   </div>
   <div class="ui_pict_show">
   	  <ul class="ui_pict_infor">
   	  	<li class="ui-border-r">
   	  		<div class="ui_left">
   	  		    <span class="f12 color333 absolute left12 trackday">已经跟进46天</span>
   	  			<ul class="ui_left_infor">
   	  				<li>
   	  					<img class="ui_img3"  src="{{URL::asset('/')}}/images/020700/track1.png">
   	  					<div style="width:100%;height:0.5rem"></div>
   	  					<p class="f11" style="color:#ff4d64;width:3.5rem">跟进中</p>
   	  				</li>
   	  				<li>
   	  					<span class="ui_progressred"></span>
   	  				</li>
   	  				<li>
   	  					<span class="ui_progressgrey"></span>
   	  				</li>
   	  				<li>
   	  					<img class="ui_img3"  src="{{URL::asset('/')}}/images/020700/track2.png">
   	  					<div style="width:100%;height:0.5rem"></div>
   	  					<p class="f11 left">促单成功</p>
   	  				</li>
   	  			</ul>
   	  			<span id="tracktime" style="color:#ff4d64" class="f12 marginleft fl">2017年1月15日</span>
   	  		</div>
   	  	</li>
   	  	<li id="motai" style="width:25%;text-align:center;padding: 1rem 1.5rem">
   	  	    <div class="ui_juzhong">
   	  		<img class="ui_img3" style="height:3.5rem;width:3rem"  src="{{URL::asset('/')}}/images/020700/track3.png">
   	  		<div style="width:100%;height:0.5rem"></div>
   	  		<p class="f11">任务单</p>
   	  		<div>
   	  	</li>
   	  </ul>
   </div>
   <!-- 备注 -->
   <div class="ui_track_status clear" style="height:3rem;line-height: 3rem;padding: 0rem  1.5rem">
   	<span class="color333 f12">备注</span>
   </div>
   <div class="ui_remark">
   	     <div class="ui_noremark none">
   	        <div style="width:100%;height:2.5rem"></div>
   	     	<p class="f12 a6a6a6">暂时没有添加相关备注</p>
   	     	  <button class="button">立即添加相关备注</button>
   	     </div>
   	     <div class="ui_remarkdata none">
   	      <div class="swiper-container ">
                <div class="swiper-wrapper">
	               <!--  <ul class="ui_remark_detail">
		   	     		<li>
		   	     			<div class="ui_text f11 color666 ui-nowrap-multi">我就是的哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈我就是的哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈
		   	     			我就是的哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈我就是的哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈
		   	     			我就是的哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈我就是的哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈
		   	     			我就是的哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈我就是的哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈
		   	     			</div>
		   	     		</li>
		   	     		<li class="transformone" >
		   	     			<img class="ui_img10"  src="{{URL::asset('/')}}/images/down.png">
		   	     		</li>
	   	     	   </ul>
		   	     	<p class=" f12 color333 margin05 clear">相关品牌：喜茶</p>
		   	     	<p class=" f12 color333">相关品牌：喜茶<span class="fr color666">2017年1月15日 18:00:00</span></p> -->
   	     	
                </div>
            <div class="swiper-pagination swiper-pagination-fraction"></div>
                <div class="swiper-button-prev opcity"></div>
                <div class="swiper-button-next opcity"></div>
                <div class="ui-border-t ui_remark_bottom">
   	     		<div class="swiper-button-prev pre fl  common_ ">上一条</div>
   	     		<div class="swiper-button-next next fl  common_ ">下一条</div>
   	     		<button class="button width68 fr topll ui_once">立即添加</button>
   	     	</div>
           </div>	
   	     	
   	     </div>
   </div>
   <div class="ui_track_status clear">
   	<span class="color333 f12">跟单情况(<span id="ui_track_"></span>)</span>
   </div>
   <!-- tab切换栏 -->
    <ul class="ui_tab">
    	<li class="ownbg">全部</li>
    	<li>邀请活动</li>
    	<li>考察邀请</li>
    	<li>付款协议</li>
    </ul>
    <ul class="ui_tabs">
    	<li><span></span></li>
    	<li></li>
    	<li></li>
    	<li></li>
    </ul>
	<!-- 绑定和经纪人的关系 -->
	<div class="ui-border-t ui_bind ">
		<div class="ui_track_status clear no height4">
		   	<span class="color333 f12">确立促单经纪人关系</span>
		   	<span id="ui_bind_time_" class="color666 f12 fr">2017年1月15日 18:00:00</span>
	    </div>
	    <div class="ui_track_bindagent">
	    	<ul class="ui_bind_agent">
	    		<li>
	    			<img id="boss" class="ui_img1"  src="{{URL::asset('/')}}/images/default/avator-m.png">
	    			<p id="bossnickname" class="f15 b margin0">陈总</p>
	    			<p id="boss_wanted" class="m7d7 f11">喜茶投资人</p>
	    		</li>
	    		<li>
	    			<img  class="ui_img4"  src="{{URL::asset('/')}}/images/020700/track4.png">
	    		</li>
	    		<li>
	    			<img id="agentself" class="ui_img1"  src="{{URL::asset('/')}}/images/default/avator-m.png">
	    			<p id="agentselfnickname" class="f15 b margin0">我</p>
	    			<p id="agentself_wanted" class="m7d7 f11">陈总</p>
	    		</li>
	    	</ul>
	    </div>
	</div>
	<!-- 全部 -->
	<div class="container">
		<section  class="ui_all pb"></section>
		<!-- 活动邀请-->
		<section id="ui_activity" class="ui_activity none pb">
		    <!-- <div class="ui_activity_wait ui-border-t">
				<div class="ui_track_status clear no height4">
				   	<span class="color333 f12">活动邀请(确认中)</span>
				   	<span class="color666 f12 fr">2017年1月15日 18:00:00</span>
			    </div>	
			    <div class="ui_common">
			    	<ul class="ui_common_con">
			    		<li>
			    			<img class="ui_img5"  src="{{URL::asset('/')}}/images/default/avator-m.png">
			    		</li>
			    		<li>
			    			<p style="text-align:left" class="f13 b">哈啊哈哈哈哈哈哈哈哈哈哈哈</p>
			    			<p style="text-align:left" class="margin07 f12">活动时间：2017年4月15日 18:00:00</p>
			    			<p style="text-align:left" class="margin07 f12">活动地点：杭州OVO运营中心</p>
			    			<p style="text-align:left" class="margin07 f12">邀请状态：<span class="ffa300">待确认</span>
			    				<span class="b" style="padding-left:1rem">还剩4天9小时21时秒</span>
			    			</p>
			    		</li>
			    	</ul>
			    	<div style="width:100%;height:1rem"></div>
			    	<div class="ui_send_again">
			    		<button>再次发送</button>
			    	</div>
			    </div>
			</div> -->
			<!-- <div class="ui_activity_accept ui-border-t">
				<div class="ui_track_status clear no height4">
				   	<span class="color333 f12">活动邀请(已接受)</span>
				   	<span class="color666 f12 fr">2017年1月15日 18:00:00</span>
			    </div>
				<div class="ui_common">
			    	<ul class="ui_common_con">
			    		<li>
			    			<img class="ui_img5"  src="{{URL::asset('/')}}/images/default/avator-m.png">
			    		</li>
			    		<li>
			    			<p style="text-align:left" class="f13 b">哈啊哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈</p>
			    			<p style="text-align:left" class="margin07 f12">活动时间：2017年4月15日 18:00:00</p>
			    			<p style="text-align:left" class="margin07 f12">活动地点：杭州OVO运营中心</p>
			    			<p style="text-align:left" class="margin07 f12">邀请状态：<span class="be74">已接受</span></p>
			    			<p style="text-align:left" class="margin07 f12">确认时间：2017年4月15日 18:00:00</p>
			    		</li>
			    	</ul>
			    	<div style="width:100%;height:2rem"></div>
			    </div>
			</div> -->
			<!-- <div class="ui_activity_refuse ui-border-t">
				<div class="ui_track_status clear no height4">
				   	<span class="color333 f12">活动邀请(已拒绝)</span>
				   	<span class="color666 f12 fr">2017年1月15日 18:00:00</span>
			    </div>
			    <div class="ui_common">
			    	<ul class="ui_common_con">
			    		<li>
			    			<img class="ui_img5"  src="{{URL::asset('/')}}/images/default/avator-m.png">
			    		</li>
			    		<li>
			    			<p style="text-align:left" class="f13 b">哈啊哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈</p>
			    			<p style="text-align:left" class="margin07 f12">活动时间：2017年4月15日 18:00:00</p>
			    			<p style="text-align:left" class="margin07 f12">活动地点：杭州OVO运营中心</p>
			    			<p style="text-align:left" class="margin07 f12">邀请状态：<span class="fc6262">已拒绝</span></p>
			    			<p style="text-align:left" class="margin07 f12">拒绝理由：<span class="color333 b f12">时间太忙，过不去时间太忙</span></p>
			    			<p style="text-align:left" class="margin07 f12">确认时间：2017年4月15日 18:00:00</p>
			    		</li>
			    	</ul>
			    	<div style="width:100%;height:4.5rem"></div>
			    </div>
			</div> -->

		</section>
		<!-- 考察邀请 -->
		<section id="ui_invest" class="ui_invest none pb">
			<!-- <div class="ui_invest_wait ui-border-t">
				<div class="ui_track_status clear no height4">
				   	<span class="color333 f12">考察邀请函(确认中)</span>
				   	<span class="color666 f12 fr">2017年1月15日 18:00:00</span>
			    </div>
			    <div class="ui_common">
			    	<ul class="ui_common_con">
			    		<li>
			    			<img class="ui_img5"  src="{{URL::asset('/')}}/images/default/avator-m.png">
			    		</li>
			    		<li>
			    			<p style="text-align:left" class="f13 b">哈啊哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈</p>
			    			<p style="text-align:left" class="margin07 f12">考察门店：杭州喜茶@black</p>
			    			<p style="text-align:left" class="margin07 f12">所在地区：杭州</p>
			    			<p style="text-align:left" class="margin07 f12">详细地址：杭州市拱墅区祥园路乐富智汇园</p>
			    			<p style="text-align:left" class="margin07 f12">考察时间：2017年2月17日</p>
			    			<p style="text-align:left" class="margin07 f12">定金金额：3000元整人民币</p>
			    			<p style="text-align:left" class="margin07 f12">支付方式：支付宝</p>
			    			<p style="text-align:left" class="margin07 f12">邀请状态：<span class="ffa300">待确认</span>
			    				<span class="b" style="padding-left:1rem">还剩4天9小时21时10秒</span>
			    			</p>
			    		</li>
			    	</ul>
			    	<div style="width:100%;height:10.5rem"></div>
			    	<div class="ui_send_again ">
			    		<button>再次发送</button>
			    	</div>
			    </div>
			</div> -->
			<!-- <div class="ui_invest_accept ui-border-t">
				<div class="ui_track_status clear no height4">
				   	<span class="color333 f12">考察邀请函(已接受)</span>
				   	<span class="color666 f12 fr">2017年1月15日 18:00:00</span>
			    </div>
			    <div class="ui_common">
			    	<ul class="ui_common_con">
			    		<li>
			    			<img class="ui_img5"  src="{{URL::asset('/')}}/images/default/avator-m.png">
			    		</li>
			    		<li>
			    			<p style="text-align:left" class="f13 b">哈啊哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈</p>
			    			<p style="text-align:left" class="margin07 f12">考察门店：杭州喜茶@black</p>
			    			<p style="text-align:left" class="margin07 f12">所在地区：杭州</p>
			    			<p style="text-align:left" class="margin07 f12">详细地址：杭州市拱墅区祥园路乐富智汇园</p>
			    			<p style="text-align:left" class="margin07 f12">考察时间：2017年2月17日</p>
			    			<p style="text-align:left" class="margin07 f12">定金金额：3000元整人民币</p>
			    			<p style="text-align:left" class="margin07 f12">支付方式：支付宝</p>
			    			<p style="text-align:left" class="margin07 f12">邀请状态：<span class="be74">已接受</span></p>
			    			<p style="text-align:left" class="margin07 f12">确认时间：2017年4月15日 18:00:00</p>
			    		</li>
			    	</ul>
			    	<div style="width:100%;height:11.8rem"></div>
			    </div>
			</div> -->
			<!-- <div class="ui_invest_refuse ui-border-t">
				<div class="ui_track_status clear no height4">
				   	<span class="color333 f12">考察邀请函(已拒绝)</span>
				   	<span class="color666 f12 fr">2017年1月15日 18:00:00</span>
			    </div>
			    <div class="ui_common">
			    	<ul class="ui_common_con">
			    		<li>
			    			<img class="ui_img5"  src="{{URL::asset('/')}}/images/default/avator-m.png">
			    		</li>
			    		<li>
			    			<p style="text-align:left" class="f13 b">哈啊哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈</p>
			    			<p style="text-align:left" class="margin07 f12">考察门店：杭州喜茶@black</p>
			    			<p style="text-align:left" class="margin07 f12">所在地区：杭州</p>
			    			<p style="text-align:left" class="margin07 f12">详细地址：杭州市拱墅区祥园路乐富智汇园</p>
			    			<p style="text-align:left" class="margin07 f12">考察时间：2017年2月17日</p>
			    			<p style="text-align:left" class="margin07 f12">定金金额：3000元整人民币</p>
			    			<p style="text-align:left" class="margin07 f12">支付方式：支付宝</p>
			    			<p style="text-align:left" class="margin07 f12">邀请状态：<span class="fc6262">已拒绝</span></p>
			    			<p style="text-align:left" class="margin07 f12">确认时间：2017年4月15日 18:00:00</p>
			    		</li>
			    	</ul>
			    	<div style="width:100%;height:11.8rem"></div>
			    </div>	
			</div> -->
		</section>
		<!--加盟合同-->
		<section id="ui_contrack" class="ui_contrack none pb">
			<!-- <div class="ui_contrack_refuse ui-border-t">
				<div class="ui_track_status clear no height4">
				   	<span class="color333 f12">合同加盟(已拒绝)</span>
				   	<span class="color666 f12 fr">2017年1月15日 18:00:00</span>
			    </div>
			    <div class="ui_common_contrack">
			    	<div class="f13 ui_contrack_top ui-border-b">
			    		X大拒绝加盟合同[喜茶]已经拒绝
			    		<span class="fc6262 fr">加盟失败</span>
			    	</div>
			    	<div class="ui_contrack_middle ui-border-b">
			    		<p style="text-align:left" class="margin07 f12">加盟合同<span class="fr">合同名称</span></p>
			    		<p style="text-align:left" class="margin07 f12">合同号<span class="fr">123456789</span></p>
			    		<p style="text-align:left" class="margin07 f12">加盟品牌<span class="fr">喜茶</span></p>
			    		<p style="text-align:left" class="margin07 f12">合同撰写<span class="fr">无界商圈法务人员</span></p>
			    		<p style="text-align:left" class=" f12"><span class="fr">喜茶法务人员</span></p>
			    		<div style="width:100%;height:1.5rem"></div>
			    	</div>
			    	<div class="ui_contrack_bottom">
			    		<p style="text-align:left" class="margin07 f12">加盟费用<span class="fr">￥120000</span></p>
			    		<p style="text-align:left" class="margin07 f12">合同文本</p>
			    		<ul class="ui_contrack_detail">
			    			<li>
			    				<img class="ui_img6"  src="{{URL::asset('/')}}/images/020700/bargain2.png">
			    			</li>
			    			<li>
			    				<p class="f14 b textleft color333 margin05">喜茶加盟电子合同</p>
			    				<p class="f11 textleft color666">合同编号：</p>
			    			</li>
			    			<li>
			    				<img class="ui_img7"  src="{{URL::asset('/')}}/images/020700/m9.png">
			    			</li>
			    		</ul>
			    	</div>
			    </div>	
			</div> -->
			<!-- <div class="ui_contrack_accept ui-border-t">
			 	<div class="ui_track_status clear no height4">
				   	<span class="color333 f12">合同加盟(已接受)</span>
				   	<span class="color666 f12 fr">2017年1月15日 18:00:00</span>
			    </div>
			    <div class="ui_common_contrack">
			    	<div class="f13 ui_contrack_top ui-border-b">
			    		X大拒绝加盟合同[喜茶]已经拒绝
			    		<span class="be74 fr">交易成功</span>
			    	</div>
			    	<div class="ui_contrack_middle">
			    		<p style="text-align:left" class="margin07 f12">加盟合同<span class="fr">合同名称</span></p>
			    		<p style="text-align:left" class="margin07 f12">合同号<span class="fr">123456789</span></p>
			    		<p style="text-align:left" class="margin07 f12">加盟品牌<span class="fr">喜茶</span></p>
			    		<p style="text-align:left" class="margin07 f12">合同撰写<span class="fr">无界商圈法务人员</span></p>
			    		<p style="text-align:left" class=" f12"><span class="fr">喜茶法务人员</span></p>
			    		<div style="width:100%;height:1.5rem"></div>
			    	</div>
			    	<ul class="ui_border_flex ui_pR color333 f12">
			                <li style="width:40%"><div class="ui-border-b ui_row"></div></li>
			                <li style="width:20%"><span>首付情况</span></li>
			                <li style="width:40%"><div class="ui-border-b ui_row"></div></li>
		            </ul>
			    	<div class="ui_bg">
			    		<p style="text-align:left" class="margin07 f12">首次支付<span class="fr">￥120000</span></p>
			    		<p style="text-align:left" class="margin07 f12">定金抵扣<span class="fr">-￥120000</span></p>
			    		<p style="text-align:left" class="margin07 f12">创业基金抵扣<span class="fr">-￥120000</span></p>
			    		<p style="text-align:left" class="margin07 f12">实际支付<span class="fr">-￥120000</span></p>
			    		<p style="text-align:left" class="margin07 f12">支付状态<span class="fr">已支付</span></p>
			    		<p style="text-align:left" class="margin07 f12">支付方式<span class="fr">支付宝</span></p>
			    		<p style="text-align:left" class="margin07 f12"><span class="fr">123456789@qq.com</span></p>
			    		<div style="width:100%;height:0.7rem;clear:both"></div>
			    		<p style="text-align:left" class="margin07 f12">支付时间<span class="fr">2015/12/12 18:000000</span></p>
			    	</div>
			    	<div style="width:100%;height:0.7rem;clear:both"></div>
			    	<ul class="ui_border_flex ui_pR color333 f12">
			                <li style="width:40%"><div class="ui-border-b ui_row"></div></li>
			                <li style="width:20%"><span>尾款情况</span></li>
			                <li style="width:40%"><div class="ui-border-b ui_row"></div></li>
		            </ul>
			    	<div class="ui_bg">
			    		<p style="text-align:left" class="margin07 f12">尾款补齐<span class="fr">￥120000</span></p>
			    		<p style="text-align:left" class="margin07 f12">尾款状态<span class="fr fc6262">未支付</span></p>
			    		<p style="text-align:left" class="margin7 f12 clear"><span class="fr">*请投资人尽快支付尾款费用</span></p>
			    		<p style="text-align:left" class="margin7 f12 clear"><span class="fr">支付方式为线下对公账号转账</span></p>
			    		<p style="text-align:left" class="margin7 f12 clear"><span class="fr ff">了解尾款补齐操作方法</span></p>
			    	</div>
			    	<div class="ui_contrack_bottom">
			    		<p style="text-align:left" class="margin07 f12">合同文本</p>
			    		<ul class="ui_contrack_detail">
			    			<li>
			    				<img class="ui_img6"  src="{{URL::asset('/')}}/images/020700/bargain2.png">
			    			</li>
			    			<li>
			    				<p class="f14 b textleft color333 margin05">喜茶加盟电子合同</p>
			    				<p class="f11 textleft color666">合同编号:</p>
			    			</li>
			    			<li>
			    				<img class="ui_img7"  src="{{URL::asset('/')}}/images/020700/m9.png">
			    			</li>
			    		</ul>
			    	</div>
			    </div>	
			</div> -->
			<!-- <div class="ui_contrack_wait ui-border-t">
				<div class="ui_track_status clear no height4">
				   	<span class="color333 f12">合同加盟(确认中)</span>
				   	<span class="color666 f12 fr">2017年1月15日 18:00:00</span>
			    </div>
			    <div class="ui_common_contrack">
			    	<div class="f13 ui_contrack_top ui-border-b">
			    		X大拒绝加盟合同[喜茶]已经拒绝
			    		<span class="ffa300 fr">等待中</span>
			    	</div>
			    	<div class="ui_contrack_middle ui-border-b">
			    		<p style="text-align:left" class="margin07 f12">加盟合同<span class="fr">合同名称</span></p>
			    		<p style="text-align:left" class="margin07 f12">合同号<span class="fr">123456789</span></p>
			    		<p style="text-align:left" class="margin07 f12">加盟品牌<span class="fr">喜茶</span></p>
			    		<p style="text-align:left" class="margin07 f12">合同撰写<span class="fr">无界商圈法务人员</span></p>
			    		<p style="text-align:left" class=" f12"><span class="fr">喜茶法务人员</span></p>
			    		<div style="width:100%;height:1.5rem"></div>
			    	</div>
			    	<div class="ui_contrack_bottom">
			    		<p style="text-align:left" class="margin07 f12">加盟总费用<span class="fr">￥120000</span></p>
			    		<p style="text-align:left" class="margin07 f12">线上首付<span class="fr">￥120000</span></p>
			    		<p style="text-align:left" class="margin07 f12">线下尾款<span class="fr">￥120000</span></p>
			    		<p style="text-align:left" class="margin05 f12">缴纳方式<span class="fr">线上首付一次性结清</span></p>
			    		<p style="text-align:left" class="margin07 f12 "><span class="fr">线下尾款银行转账</span></p>
			    		<div style="width:100%;height:0.3rem;clear:both"></div>
			    		<p style="text-align:left" class="margin07 f12"><span class="fr ff">了解尾款补齐操作方式</span></p>
			    		<p style="text-align:left" class="margin07 f12 clear">合同文本</p>
			    		<ul class="ui_contrack_detail">
			    			<li>
			    				<img class="ui_img6"  src="{{URL::asset('/')}}/images/020700/bargain2.png">
			    			</li>
			    			<li>
			    				<p class="f14 b textleft color333 margin05">喜茶加盟电子合同</p>
			    				<p class="f11 textleft color666">合同编号：</p>
			    			</li>
			    			<li>
			    				<img class="ui_img7"  src="{{URL::asset('/')}}/images/020700/m9.png">
			    			</li>
			    		</ul>
			    	</div>
			    	<div class="color333">
			    		<div style="width:100%;height:1.8rem"></div>
			    		<p style="text-align:left" class="margin07 f12">邀请状态：
			    			<span><span class="ffa300">待确认</span><span class="b color333 padding">还剩3天5小时6分</span></span> 
			    			<span class="fr ui_send">再次发送</span>
			    		</p>
		    	    </div>	
			    </div>
			</div> -->
		</section>
	</div> <!--class= container -->
   <!--  无信息图片	 -->
    <div class="tc none nocomment" id="nocommenttip3">
        <img src="{{URL::asset('/')}}/images/nomessage_icon1.png" style="height: 16rem;width: 16rem;margin:0 auto;display: inline-block;">
    </div>         
    </section>
@stop
@section('endjs')
	<script src="{{URL::asset('/')}}/js/swiper.min.js"></script>
	<script type="text/javascript" src="{{URL::asset('/')}}/js/agent/tool.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/agent/track.js"></script>
@stop