@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/accountDetail.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/agent/mycharges.css" rel="stylesheet" type="text/css"/>
@stop


<!--具体明细-->
@section('main')
	<section class="containerBox bgcolor" style="min-height: 100%;" id="containerBox" >
	<!-- 成单提成 -->
		<div class="none">
			<div class="account2 pt2 pb2">
				<p class="p100">促单提成（元）</p>
				<span class="time"></span>
				<div class="account2-1">
					<p class="p102 commission" ></p>
					<p class="p103">*按照实际到账情况计算</p>
				</div>
				<div class="fline ml1-5"></div>
			</div>
			<div class="list pl1-5 pr1-5 pb2">
				<p>
					<span>投资人：<span>
					<span class="tzr-name" style="float: right;color: #666666;"></span>
				</p>
				<p>
					<span>加盟品牌：</span>
					<span class="zmpp-name" style="float: right;color: #666666;"></span>
				</p>
				<p>
					<span>加盟费用：</span>
					<span class="money" style="float: right;color: #666666;"></span>
				</p>
				<p>
					<span>线上首付：</span>
					<span class="pre_pay" style="float: right;color: #666666;"></span>
				</p>
				<p>
					<span>尾款支付：</span>		
					<span class="end_pay" style="float: right;color: #666666;"></span>
				</p>
				<p class="overhide ">
					<span>支付情况：</span>
					<span class="" style="float: right;color: #666666;">优惠抵扣：¥<em class="discount"></em></span>
				</p>
				<p class="overhide ">
					<span class="" style="float: right;color: #666666;">线上首付实际支付：¥<em class="actual_pay"></em></span>
				</p>
				<p class="overhide ">
					<span class="" style="float: right;color: #666666;">支付方式：<em class="pay_way"></em></span>
				</p>
				<p class="overhide">
					<span class="" style="float: right;color: #666666;">线上付款时间：<em class="start_pay_time"></em></span>
				</p>
				<p class="border-dash mb05 mt05"></p>
				<p class="overhide">
					<span class="" style="float: right;color: #666666;">尾款实际支付：¥<em class="actual_end_pay"></em></span>
				</p>
				<p class="overhide">
					<span class="" style="float: right;color: #666666;">支付方式：<em class="end_pay_way">2</em></span>
				</p>
				<p class="overhide">
					<span class="" style="float: right;color: #666666;">线下付款时间：<em class="end_pay_time"></em></span>
				</p>
				<p >
					<span>成单经纪人：</span>
					<span class="agent_name" style="float: right;color: #666666;"></span>
				</p>	
			</div>
		</div>
	<!-- 团队分佣 -->
		<div class="none">
			<div class="account2 pt2 ">
				<p class="p100" >团队分佣：<em id='team_sub'></em></p>
				<span class="time"></span>
				<div class="account2-1">
					<p class="p102" class="commission">+</p>
					<p class="color999 f12">*已结清</p>
				</div>
				<div class="fline ml1-5"></div>
			</div>
			<div class="list2 bgwhite pl1-5">
				<div class="fline ">
                    <div class="pt2-5 pb2-5 f12 lh2 l w60">
                      <p >当前业绩总和（单）<span id="total_order">3</span></p>
                      <p><span class="yellowcircle"></span><em class="agent_name"></em> <span id="my_order"></span></p>
                      <p><span class="bluecircle"></span>下线成员 <span id="mydown_order"></span></p>
                    </div>
                    <div class="r tr w40 pr1-5">
                      <div class="chart-container r" style="position: relative; height:11rem; width:11rem">
                          <canvas id="mychart" ></canvas>
                      </div>
                    </div>
                    <div class="clearfix"> </div>              
                </div>
                 <div class="pt1-5 pb1-5 color666 f13 lh2 pr1-5">
                  <p id="intro">
                  	 根据梯度说明，当前处在<span class="cffa300">梯度C（15 ~ 20）</span>  ，提成以 20% 计算。当前Q3（7月-9月），我的团队共获得佣金奖励 ￥56K 整。其中，我获得 ￥16K 整,我的下属成员获得 ￥40K 整
                  </p>
                </div>	
			</div>
		</div>
	<!-- 邀请提成 -->
		<div class="none">
			<div class="account2 pt2">
				<p class="p100">邀请提成（元）</p>
				<span class="time"></span>				
				<div class="account2-1 pb2">
					<p class="p102 commission"></p>
					<p class="p103">*按照实际到账情况计算</p>
				</div>
			</div>
			<div class="list pt2 pb2 pr1-5 pl1-5">
				<p>
					<span>投资人：<span>
					<span class="tzr-name" style="float: right;color: #666666;"></span>
				</p>
				<p>
					<span>加盟品牌：</span>
					<span class="zmpp-name" style="float: right;color: #666666;"></span>
				</p>
				<p>
					<span>加盟费用：</span>
					<span class="money" style="float: right;color: #666666;"></span>
				</p>
				<p>
					<span>线上首付：</span>
					<span class="pre_pay" style="float: right;color: #666666;"></span>
				</p>
				<p>
					<span>尾款支付：</span>		
					<span class="end_pay" style="float: right;color: #666666;"></span>
				</p>
				<p class="overhide ">
					<span>支付情况：</span>
					<span class="" style="float: right;color: #666666;">优惠抵扣：¥<em class="discount"></em></span>
				</p>
				<p class="overhide ">
					<span class="" style="float: right;color: #666666;">线上首付实际支付：¥<em class="actual_pay"></em></span>
				</p>
				<p class="overhide ">
					<span class="" style="float: right;color: #666666;">支付方式：<em class="pay_way"></em></span>
				</p>
				<p class="overhide">
					<span class="" style="float: right;color: #666666;">线上付款时间：<em class="start_pay_time"></em></span>
				</p>
				<p class="border-dash mb05 mt05"></p>
				<p class="overhide">
					<span class="" style="float: right;color: #666666;">尾款实际支付：¥<em class="actual_end_pay"></em></span>
				</p>
				<p class="overhide">
					<span class="" style="float: right;color: #666666;">支付方式：<em class="end_pay_way"></em></span>
				</p>
				<p class="overhide">
					<span class="" style="float: right;color: #666666;">线下付款时间：<em class="end_pay_time"></em></span>
				</p>
				<p >
					<span>成单经纪人：</span>
					<span class="agent_name" style="float: right;color: #666666;"></span>
				</p>	

			</div>
		</div>
	<!-- 提现 -->
		<div class="none">
			<div class="account2 pt2">
				<p class="p100">提现（元）</p>
				<!-- <span class="time">2017/10/22</span> -->
				<div class="account2-1 pb2">
					<p class="p102 commission"></p>
					<p class="p103">*按照实际到账情况计算</p>
					<p class="brokerage color999 f12"></p>
				</div>
			</div>
			<div class="list list-flex pt2 pl1-5 pr1-5">
				<div >
					<p>提现进度</p>
				</div>
				<div class="color666">
					<p class="cffa300"><span class="cash_finish down_yellow"></span>提现申请</p>
					<p class="cffa300"><span class="cash_finish down_yellow up_yellow"></span>财务审核</p>
					<p class=""><span class="" id="cash_statu"></span></p>
				</div>
				<div class="color999 tr">
					<p class="apply_time"></p>
					<p class="auth_time"></p>
					<p class="pay_time"></p>
				</div>
			</div>
			<div class="list3 pl1-5 pt1 pr1-5">
				<p class="pt1 pb1">
				<span>转入账户：</span>
				<span class="zr-name color666 r" ></span>
				</p>
				<p class="pt1 pb1">
				<span>到账时间：</span>
				<span class="dz-name color666 r" ></span>
				</p>
				<p class="pt1 pb1">
				<span>创建时间：</span>
				<span class="cjb-time color666 r" ></span>
		
				</p>
				<p class="pt1 pb1">
				<span>订单号：</span>
				<span class="number color666 r" ></span>
				</p>
				<p class="pt1 pb1 none fail_status">
				<span>状态：</span>
				<span style="float: right;color: #999;">订单失败，款项自动返回账户余额</span>
				</p>
			</div>
			<div class="list4">
			<a >对此单有疑问，请拨打服务热线</a>
			</div>
		</div>
	<!-- 团队成长 type=9 -->
		<div class="none">
			<div class="account2 pt2">
				<p class="p100">团队成长(元)</p>
				<span class="time"></span>
				<div class="account2-1 pb2">
					<p class="p102 commission"></p>
					<p class="p103">*按照实际到账情况计算</p>
				</div>
			</div>
			<div class="fline ml1-33" style="padding: 0;"></div>
			<div class="list pb5 pt2 pl1-5 pr1-5">	
				<div class="list-flex">
					<p class="customerType color333">经纪人</p>
					<p class="agent_name color666"></p>	
				</div>	
				<div class="list-flex">
					<p class=" color333">邀请1个投资人</p>
					<p class="agent_time color666">
						<img src="/images/agent/finish_1.png" style="width: 1.2rem;height: 1.2rem;">
						<span>完成</span>
					</p>	
				</div>
				<div class="f11 color999">
					<div>*在平台首次成功代理品牌，奖励团队成长<em class="getmoney"></em>元</div>
					<div>*奖金发放至经纪人的上线经纪人，如有疑问请联系无界商圈客服</div>
				</div>
			</div>
		</div>
	<!-- 团队发展 type=8-->
		<div class="none">
			<div class="account2 pt2">
				<p class="p100">团队发展(元)</p>
				<span class="time"></span>
				<div class="account2-1 pb2">
					<p class="p102 commission"></p>
					<p class="p103">*按照实际到账情况计算</p>
				</div>
			</div>
			<div class="fline ml1-33" style="padding: 0;"></div>
			<div class="list pb5 pt2 pl1-5 pr1-5">	
				<div class="list-flex">
					<p class="customerType color333">经纪人</p>
					<p class="agent_name color666"></p>	
				</div>	
				<div class="list-flex">
					<p class="enroll color333">注册时间</p>
					<p class="enrollTime color666"></p>	
				</div>
				<div class="list-flex">
					<p class=" color333">代理1个品牌</p>
					<p class="agent_time color666">
						<img src="/images/agent/finish_1.png" style="width: 1.2rem;height: 1.2rem;">
						<span>完成</span>
					</p>	
				</div>
				<div class="f11 color999">
					<div>*注册成为无界商圈经纪人，完成实名认证并成功代理品牌，获得团队发展奖</div>
				</div>
			</div>
		</div>
	<!-- 团队育成奖 -->
		<div class="none">
			<div class="account2 pt2">
				<p class="p100">团队育成奖(<em class="level">等级</em>)(元)</p>
				<span class="time"></span>
				<div class="account2-1 pb2">
					<p class="p102 commission"></p>
					<p class="p103">*按照实际到账情况计算</p>
				</div>
			</div>
			<div class="list pb5 pt2 pl1-5 pr1-5">
				<div >
					<p class="f13">达到<em class="level"></em>，获得团队育成奖<em class="commission2"></em>元</p>
				</div>
				<div class="color666">
					<table class="color333 f11 teamtable">
						<tr class="tc">
							<td>级别</td>
							<td>晋升要求/考核要求</td>
							<td>条件说明</td>
						</tr>
						<tr>
							<td class="tc" style="vertical-align: middle;">1</td>
							<td>发展5个经纪人且团队完成1单（拉新或自主成单）</td>
							<td>如果团队成员晋升主管成功获得 2,000元育成奖励</td>
						</tr>
						<tr>
							<td class="tc" style="vertical-align: middle;">2</td>
							<td>团队经纪人总人数达到10人且有1个是主管级别、完成2单及以上</td>
							<td>如团队成员成功晋升经理级别获得 4,000元育成奖励</td>
						</tr>
						<tr>
							<td class="tc" style="vertical-align: middle;">3</td>
							<td>团队经纪人总人数达到15人且有2个是主管级别、完成2单及以上</td>
							<td>如团队成员成功晋升经理级别获得 6,000元育成奖励 </td>
						</tr>
					</table>
				</div>
				<p class="f11 color999">*奖金发放至团队主管，如有疑问请联系无界商圈客服</p>
			</div>
		</div>
	<!-- 发展客户 type=10 -->
		<div class="none">
			<div class="account2 pt2">
				<p class="p100">发展客户</p>
				<span class="time"></span>
				<div class="mt2-1 pb2">
					<p class="p102 commission"></p>
					<p class="p103">*按照实际到账情况计算</p>
				</div>
			</div>
			<div class="fline ml1-33" style="padding: 0;"></div>
			<div class="list pb5 pt2 pl1-5 pr1-5">	
				<div class="list-flex">
					<p class="color333">投资人</p>
					<p class="customer color666"></p>	
				</div>	
				<div class="list-flex">
					<p class="enroll color333">注册时间</p>
					<p class="enrollTime color666"></p>	
				</div>	
				<div class="f11 color999">
					*因邀请有效客户(通过回访及存货时长判定)，给予<em class="getmoney"></em>元奖励
				</div>
			</div>
		</div>
	<!-- 活动邀约奖月结 -->
		<div class="none">
			<div class="account2 pt2">
				<p class="p100">活动邀约奖(月结:10月)(元)</p>
				<span class="time">2017/10/22</span>
				<div class="mt2-1 pb2">
					<p class="p102 commission"></p>
					<p class="p103">*按照实际到账情况计算</p>
					<p class="p103">*仅展示补发奖金，基础奖励请前往单次活动查看</p>
				</div>
			</div>
			<div class="list pl1-5 pr1-5">
				<div class="list-flex">
					<p >本月累计</p>
					<div class="color666 tr">
						<p class="mb0 "></p>
						<p class="mt-08">(按实际签到情况为准)</p>
					</div>
				</div>
				<div class="list-flex">
					<p >已发放奖金</p>
					<div class="color666 tr">
						<p class="mb0 "></p>
						<p class="mt-08">(以基础档位结算：50元/人)</p>
					</div>
				</div>
				<div class="list-flex">
					<p>达到档位</p>
					<p class="color666 tr">410/人</p>
				</div>
				<div class="list-flex">
					<p >月结补发奖金</p>
					<div class="color666 tr">
						<p class="">元</p>
						<p class="mt-08">(月结补发奖金于次月10日到账)</p>
					</div>
				</div>
				<div class="color999 f11">*奖金发放至团队主管，如有疑问请联系无界商圈客服</div>
				<div class="color999 f11">*附，OVO邀约档位奖励表:</div>
				<div class="pb5">	
					<table class="rewardtable">
						<tr>
							<td>OVO邀约 (人)</td>
							<td>每邀约一个客户的奖励金额 (元)</td>
						</tr>
						<tr>
							<td>＜ 3</td>
							<td>50</td>
						</tr>
						<tr>
							<td>3-8</td>
							<td>65</td>
						</tr>
						<tr>
							<td>9-17</td>
							<td>80</td>
						</tr>
						<tr>
							<td>18-35</td>
							<td>95</td>
						</tr>
						<tr>
							<td>36-59</td>
							<td>110</td>
						</tr>
						<tr>
							<td>60-104</td>
							<td>125</td>
						</tr>
						<tr>
							<td>105-149</td>
							<td>140</td>
						</tr>
						<tr>
							<td>≥150</td>
							<td>150</td>
						</tr>

					</table>
				</div>
			</div>
		</div>
	<!-- 活动邀约单次活动 type=14-->
		<div class="none">
			<div class="account2 pt2">
				<p class="p100">活动邀约奖(单次活动)(元)</p>
				<span class="time"></span>
				<div class="mt2-1 pb2">
					<p class="p102 commission"></p>
					<p class="p103">*按照实际到账情况计算</p>
				</div>
			</div>
			<div class="list pl1-5 pr1-5">
				<div class="list-flex">
					<p>OVO活动</p>
					<p class="color666 tr activity_name"></p>		
				</div>
				<div class="list-flex">
					<p >活动时间</p>
					<p class="color666 tr activity_time"></p>				
				</div>
				<div class="list-flex">
					<p>活动场地</p>
					<div class="color666 tr ovo_name">
						<p class="">杭州 杭后OVO运营中心</p>
						<p class="mt-08"></p>
						<p class="mt-08"></p>
					</div>
				</div>
				<div class="list-flex">
					<p>邀请参会人数</p>
					<div class="color666 tr">
						<p class="activity_num"></p>
						<p class="mt-08">(按实际签到情况为准)</p>
					</div>
				</div>
				<div class="list-flex">
					<p>奖励金额</p>
					<div class="color666 tr">
						<p class="rewards"></p>
					</div>	
				</div>
				<p class="mt-08 r color666">(以基础档位结算为准：50元/人，月结补充档位差价)</p>
				<div class="clearfix"></div>
			</div>
		</div>
	<!-- 到票补贴 -->
		<div class="none">
			<div class="account2 pt2">
				<p class="p100">到票补贴(元)</p>
				<span class="time"></span>
				<div class="mt2-1 pb2">
					<p class="p102 commission"></p>
					<p class="p103">*按照实际到账情况计算</p>
				</div>
			</div>
			<div class="list pl1-5 pr1-5 pb5">
				<div class="list-flex">
					<p class="customerType color333">投资人</p>
					<p class="color666 investor"></p>	
				</div>	
				<div class="list-flex">
					<p class="color333">考察品牌</p>
					<p class="color666 brand_name"></p>	
				</div>
				<div class="list-flex">
					<p class="enroll color333">考察门店</p>
					<div class="color666 tr">
						<p class="color666 brand_store"></p>
						<p class="color666 brand_zone">所在地区</p>	
						<p class="color666 brand_address">详细地址</p>		
					</div>
				</div>
				<div class="list-flex">
					<p class="color333">考察订金</p>
					<p class="color666 deposit"></p>	
				</div>
				<div class="list-flex">
					<p class=" color333">交付情况</p>
					<p class="color666 situation"></p>	
				</div>
				<div class="list-flex">
					<p class=" color333">考察时间</p>
					<p class="color666 Inspection_time"></p>	
				</div>
				<div class="list-flex">
					<p class=" color333">品牌商务对接</p>
					<p class="color666 business"></p>	
				</div>

			</div>
		</div>
	<!-- 项目入驻 type=16-->
		<div class="none">
			<div class="account2 pt2">
				<p class="p100">项目入驻(元)</p>
				<span class="time"></span>
				<div class="mt2-1 pb2">
					<p class="p102 commission"></p>
					<p class="p103">*按照实际到账情况计算</p>
				</div>
			</div>
			<div class="fline ml1-5" style="padding:0rem;" ></div>
			<div class="list pl1-5 pr1-5 pb5 pt1-5">
				<div class="list-flex">
					<p class="customerType color333">经纪人</p>
					<p class="color666 investor"></p>	
				</div>	
				<div class="list-flex">
					<p class="color333">品牌项目</p>
					<p class="color666 brand_name"></p>	
				</div>
				<div class="list-flex">
					<p class=" color333">提交时间</p>
					<p class="color666 Inspection_time"></p>	
				</div>
			</div>
		</div>
	</section>
	
@stop
@section('endjs')
	<script type='text/javascript' src='{{URL::asset('/')}}/js/agent/dist/Chart.bundle.min.js'></script>
	<script>
      var $body = $('body');
      document.title = "具体明细";
      // hack在微信等webview中无法修改document.title的情况
      var $iframe = $('<iframe ></iframe>').on('load', function() {
      setTimeout(function() {
      $iframe.off('load').remove()
      }, 0)
      }).appendTo($body);
  </script> 
	<script type='text/javascript'>
		Zepto(function(){
			 var args = getQueryStringArgs();
          	 var id = args['id'] || 0,
          	 	 type = args['type'] ,
          	 	 log_id = args['log_id'];
          	 var divBox = $('#containerBox').children();
          
          	 if(type == 1){
          	 	divBox.eq(3).removeClass('none');
          	 }else if (type == 4){
          	 	divBox.eq(0).removeClass('none');
          	 }else if(type == 5){
          	 	divBox.eq(1).removeClass('none');
          	 }else if(type == 6){
          	 	divBox.eq(2).removeClass('none');
          	 }else if(type == 8){
          	 	divBox.eq(5).removeClass('none');
          	 }else if(type == 9){
          	 	divBox.eq(4).removeClass('none');
          	 }else if(type == 10){
          	 	divBox.eq(7).removeClass('none');
          	 }else if(type == 11 || type == 12 || type == 13){
          	 	divBox.eq(6).removeClass('none');
          	 }else if(type == 14){
          	 	divBox.eq(9).removeClass('none');
          	 }else if(type == 15){
          	 	divBox.eq(10).removeClass('none');
          	 }else if(type == 16){
          	 	divBox.eq(11).removeClass('none');
          	 }
          	function  getDetail(id,type,log_id){
          		var url = labUser.agent_path +'/user/commission-detail/_v010003';
          		ajaxRequest({'id':id,'type':type,'log_id':log_id},url,function(data){
          			if(data.status){
          				var datas = data.message;
          				if(type ==1){
          					$('.commission').text('-'+datas.commission);
          					$('.zr-name').text(datas.account);
          					$('.dz-name').text(datas.in_account_time);
          					$('.cjb-time').text(datas.created_time);
          					$('.number').text(datas.withdraw_no);
          					$('.apply_time').text(datas.apply_time);
          					$('.auth_time').text(datas.auth_time);
          					$('.pay_time').text(datas.pay_time);
          					$('.pay_time').text(datas.pay_time);
          					$('.brokerage').text('手续费用扣除'+datas.fee+'，应到帐'+(datas.commission-datas.fee).toFixed(2));
          					if(datas.status == 2){
          						$('#cash_statu').addClass('cash_finish up_yellow').parent('p').addClass('cffa300').append('打款');
          					}else if(datas.status == -1){
          						$('#cash_statu').addClass('cash_fail up_red').parent('p').addClass('cfd4d4d').append('失败');
          						$('.fail_status').removeClass('none');       						
          						$('.list3 p span:nth-child(2)').css('color','#999999');
          						$('.list3 p .dz-name').css('color','#fd4d4d');
          					}else{
          						$('#cash_statu').addClass('cash_wait up_gray').parent('p').addClass('color666').append('打款');
          					} 
          				}else if(type == 4 || type == 6){
							$('.time').text(datas.created_at);
          					$('.commission').text('+'+datas.commission);					
          					$('.tzr-name').text(datas.customer_name);
	          				$('.zmpp-name').text(datas.brand_title);
	          				$('.money').text('¥'+datas.amount);
	          				$('.pre_pay').text('¥'+datas.pre_pay);//首款
	          				$('.end_pay').text('¥'+datas.tail_pay);//尾款
	          				$('.discount').text(datas.discount_fee);//优惠抵扣
	          				$('.actual_pay').text(datas.online_pay);//线上首付实际支付
	          				$('.pay_way').text(datas.pay_way);//支付方式
	          				$('.start_pay_time').text(datas.online_pay_at);
	          				$('.actual_end_pay').text(datas.tail_pay);//尾款支付
	          				$('.end_pay_way').text(datas.bank_no);//尾款支付方式
	          				$('.end_pay_time').text(datas.tail_pay_time);//尾款支付时间
	          				$('.agent_name').text(datas.agent_name);//经纪人姓名
	          				
          				}else if(type ==5){
          					$('#team_sub').text(datas.quarter);
          					$('.time').text(datas.unfreeze_time);
          					$('.commission').text('+'+datas.commission);
          					$('#total_order').text(datas.total_orders);
          					$('#my_order').text(datas.my_orders);
          					$('#mydown_order').text(datas.my_subordinate_orders);
          					$('.agent_name').text(datas.agent_name);
          					var introHtml = '根据梯度说明，当前处在<span class="cffa300">梯度'+datas.level+'</span>。当前'+datas.letter_quarter+'，我的团队共获得佣金奖励<span class="cffa300">￥'+datas.total_commission+'</span>整。其中，我获得<span class="cffa300">￥'+datas.my_subordinate_commission+'</span>整,我的下属成员获得<span class="cffa300">￥'+datas.my_commission+'K</span> 整。';
          					$('#intro').html(introHtml);
          					var config = {
				                  type: 'doughnut',
				                  data: {
				                      datasets: [{
				                          data: [ 
				                              datas.my_subordinate_orders,
				                              datas.my_orders
				              
				                          ],
				                          backgroundColor: [
				                              'rgb(40,115,225)',
				                              'rgb(255,163,0)'    
				                          ]                        
				                      }]                              
				                  },
				                  options: {
				                      // responsive: true,
				                      cutoutPercentage:50,
				                      legend: {
				                          position: 'left',
				                      },
				                      animation: {
				                          animateScale: true,
				                          animateRotate: true
				                      }
				                  }
				              };
				              var ctx = document.getElementById("mychart").getContext("2d");
				              window.myDoughnut = new Chart(ctx, config);

          				}else if(type == 8){
          					$('.time').text(datas.created_time);
          					$('.commission').text('+'+datas.commission);
          					$('.getmoney').text(datas.commission);					
          					$('.agent_name').text(datas.realname);
	          				$('.enrollTime').text(datas.register_time);	
          				}else if(type == 9){
          					$('.time').text(datas.created_time);
          					$('.commission').text('+'+datas.commission);
          					$('.getmoney').text(datas.commission);						
          					$('.agent_name').text(datas.realname);
          				}else if(type == 10){
          					$('.time').text(datas.created_time);
          					$('.commission').text('+'+datas.commission);	
          					$('.getmoney').text(datas.commission);					
          					$('.customer').text(datas.realname);
	          				$('.enrollTime').text(datas.register_time);	
          				}else if(type == 11){
          					$('.time').text(datas.created_time);
          					$('.commission').text('+'+datas.commission);
          					$('.commission2').text(datas.commission);
          					$('.level').text('等级1');
          				}else if(type == 12){
          					$('.time').text(datas.created_time);
          					$('.commission').text('+'+datas.commission);
          					$('.commission2').text(datas.commission);
          					$('.level').text('等级2');
          				}else if(type == 13){
          					$('.time').text(datas.created_time);
          					$('.commission').text('+'+datas.commission);
          					$('.commission2').text(datas.commission);
          					$('.level').text('等级3');
          				}else if(type == 14){
          					$('.time').text(datas.created_time);
          					$('.commission').text('+'+datas.commission);
          					$('.activity_name').text(datas.activity_name);
          					$('.activity_time').text(datas.begin_time); 					
          					var str="";
          					for(var i=0;i<datas.zone_names.length;i++){
          						str += '<p>'+datas.zone_names[i]+' '+datas.maker_names[i]+'</p>';
          					}
          					$('.ovo_name').html(str);
          					$('.activity_num').text(datas.count+'人赴会');
          					$('.rewards').text(datas.commission);
          					
          				}else if(type == 15){
          					$('.time').text(datas.created_time);
          					$('.commission').text('+'+datas.commission);
          					$('.investor').text(data.message.realname);
          					$('.brand_name').text(data.message.brand_name);
          					$('.brand_store').text(data.message.store_name);
          					$('.brand_zone').text(data.message.zone_name);
          					$('.brand_address').text(data.message.address);
          					$('.deposit').text('￥'+data.message.money);//考查定金
          					$('.situation').text('已支付('+data.message.pay_time+')');//交付情况
          					$('.Inspection_time').text(data.message.inspect_time);//考察时间
          					$('.business').text(data.message.agent_name);//考察时间
          				}else if(type == 16){
          					$('.time').text(datas.created_time);
          					$('.commission').text('+'+datas.commission);
          					$('.investor').text(data.message.realname);
          					$('.brand_name').text(data.message.brand_name);
          					$('.Inspection_time').text(data.message.enter_time);//提交时间

          				}
          			}
          		})
          	};
          	getDetail(id,type,log_id);
		})
	</script>	
@stop