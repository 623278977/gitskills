
@extends('layouts.default')
@section('css')
     <link href="{{URL::asset('/')}}/css/agent/_v010300/createsuccess.css" rel="stylesheet" type="text/css"/> 
    
@stop

@section('main')
    <section class="containerBox">
       
       <!--<div class="create_success bgwhite mb1-2 pb2">
       		<img src="/images/agent/createSuccess.png" class="mt2 mb05"/>
       		<p class="f15 ">成功发送品牌加盟至投资人</p>
       		<p class="f13 color999 mt05">等待对方确认并支付首付款项</p>
       </div>-->
       
       <!--目标品牌-->
       <!--<div class="choose mt1-2 bgwhite">
       		<p class="pt1-5 pb1-5 fline"><span class="f15 color333">目标品牌</span>
       		<div class="brand_list">
       			<div class="chooseBrand fline">
	       				<div class="brand">
	       					<p class="brand_logo mr1"><img src="/images/act_banner.png"/></p>
	       					<div class="">
	       						<p class="f14 color333 brand_name">喜茶® HEEKCAA</p>
	       						<p class="f11 color999 mb1-2 brand_text">INSPIRATION OF TEA</p>
	       						<p style="width: 12rem;"><span class="f12 color666 l_h12">行业分类：</span><span class="f12 color333 l_h12">鲜果饮品</span></p>
	       					</div>
	       				</div>
	       			
	       			<p class="textEnd">
	       				<span class="f11 color999">支持：</span><span class="support f11 color999">单店加盟、区域代理</span><br />
						<span class="f11 color999">该品牌有 <em class="brand_num f11 c2873ff">3</em> 个加盟方案</span>	       			
	       			</p>
	       		</div>
       		</div>
       </div>-->
       
       <!--目标投资人-->
       <!--<div class="chooseclient mt1-2 bgwhite">
       		<p class="pt1-5 pb1-5 fline"><span class="f15 color333">目标投资人</span></p>
	       	<div class="investor">
	       		<div class="">
	       			<p class="mr1"><img src="/images/act_banner.png" class="avatar"/></p>
	       			<div class="investorMes">
	       				<p class=""><span class="f15 color333 mr05">姚凯</span><img src="/images/agent/girl.png" class="grade" /></p>
	       				<p class=""><span class="f12 color666">上海&nbsp;浦东</span></p>
	       			</div>
	       		</div>
	       		<span class="chat scale-1 f14 c2873ff">与他聊聊</span>
	       	</div>
       </div>-->
       <!--选择加盟方案-->
       <!--<div class="choosePlan mt1-2 bgwhite">
       		<p class="pt1-5 pb1-5 fline"><span class="f15 color333">选择加盟方案</span>
       		<div class="plan">
       			<div class="packageType mb1-5">
       					<p class="lh2-3"><span class="f12 color666">加盟方案A</span><span class="f12 color666">一点点区域代理方案</span></p>
       					<p class="lh2-3"><span class="f12 color666">加盟类型</span><span class="f12 color666">区域代理</span></p>
       					<p class="lh2-3"><span class="f12 color666">总费用</span><span class="f12 cfd4d4d">¥ 180,000</span></p>
       				
       			</div>
       			<div class="planDetail  bgf2f2 ">
       					<div class="costDetail">
       						<p class="f11 color666">费用明细</p>
       						<p class="">
       							<span class="f11 color999">加盟费：¥ 20,000</span>
       							<span class="f11 color999">保证金：¥ 30,000</span>
       							<span class="f11 color999">设备费用：¥ 200,000</span>
       							<span class="f11 color999">首批货款：¥  30,000</span>
       							<span class="f11 color999">其他费用：¥ 0</span>
       						</p>
       					</div>
       					<p class="dis_bet mt1-5 mb2 ml">
       						<span class="f11 color666">最高提成</span><span class="f11 cffa300">可提成佣金部分 33%</span>
       					</p>
       					<div class="dis_bet mb2">
       						<span class="f11 color666">合同/文件</span>
       						<p class="textEnd">
       							<span class="f11 c2873ff">《 品牌加盟付款协议 》</span><br />
       							<span class="f11 c2873ff">《品牌加盟合同》</span>
       						</p>
       					</div>
       					<p class="f10 color999 lh1-5">* 如款项存在修改幅度，请联系商务对其进行修改。</p>
       					<p class="f10 color999 lh1-5">* 加盟合同将安排线下签约，实际成交按款项交齐为准。</p>
       					<p class="f10 color999 lh1-5">* 佣金结算以可提成佣金部分乘以提成比例进行计算。</p>
       					<p class="f10 color999 lh1-5">*  对加盟方案存在疑问，请联系商圈客服人员。</p>
       					<p class="f10 color999 lh1-5">*  无界商圈保持最终解释权。</p>
       				</div>
       		</div>
       </div>-->
       <!--底部-生成加盟函按钮-->
       <!--<div class="setup pt05 pb05 fixed-bottom-iphoneX f15 color-f">
       		与投资人聊聊
       </div>-->
    </section>
    <section style="height: 10rem;">
    	<div class="common_pops none"></div>
    </section>
    <section style="position: fixed;bottom: 0;background: #FFFFFF;height: 17px;" class="iphone_btn none"></section>
@stop

@section('endjs')
    <script src="{{URL::asset('/')}}/js/agent/_v010300/createsuccess.js"></script>
    <script type="text/javascript">
    	$(document).ready(function(){$('title').text('创建成功')});
    </script>
@stop