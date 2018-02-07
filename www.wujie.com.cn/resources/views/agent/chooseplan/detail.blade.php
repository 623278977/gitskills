
@extends('layouts.default')
@section('css')
     <link href="{{URL::asset('/')}}/css/agent/_v010300/chooseplan.css" rel="stylesheet" type="text/css"/> 
    
@stop

@section('main')
    <section class="containerBox">
       <!--<div class="top dis_bet bgwhite">
       		<div class="mu">
       			<img src="/images/agent/mub_blue.png" class="mb05"/>
       			<span class="f13 c2873ff">选择目标品牌</span>
       		</div>
       		<span class="ifline w4 mt2-5 c2873ff"></span>
       		<div class="fangan">
       			<img src="/images/agent/fangan_blue.png" class="mb05"/>
       			<span class="f13 c2873ff">选择加盟方案</span>
       		</div>
       		<span class="fline w4 mt2-5"></span>
       		<div class="">
       			<img src="/images/agent/send_grey.png" class="mb05"/>
       			<span class="f13 color_ccc">发送至投资人</span>
       		</div>
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
	       						<span class="f12 color666 l_h12">行业分类：</span><span class="f12 color333 l_h12">鲜果饮品</span>
	       					</div>
	       				</div>

	       			<p class="textEnd">
	       				<span class="f11 color999">支持：</span><span class="support f11 color999">单店加盟、区域代理</span><br />
						<span class="f11 color999">该品牌有 <em class="brand_num f11 c2873ff">3</em> 个加盟方案</span>
	       			</p>
	       		</div>
       		</div>
       </div>-->
       <!--选择加盟方案-->
       <!--<div class="choosePlan mt1-2 bgwhite">
       		<p class="pt1-5 pb1-5 fline"><span class="f15 color333">选择加盟方案</span></p>
       		<div class="plan fline">
       			<div class="packageType">
       				<span class="chooseNo choose_img mr1"></span>
       				<div class="planText mb1-5">
       					<p class="lh2-3"><span class="f12 color333">加盟方案A</span><span class="f12 color333">一点点区域代理方案</span></p>
       					<p class="lh2-3"><span class="f12 color333">加盟类型</span><span class="f12 color333">区域代理</span></p>
       					<p class="lh2-3"><span class="f12 color333">总费用</span><span class="f12 cfd4d4d">¥ 180,000</span></p>
       				</div>

       			</div>
       			<div class="unfold">
       				<span class="c2873ff f12">展开查看详细</span>
       			</div>
       			<div class="planDetail  bgf2f2 ml2-5 none">
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
       <!---->
       <!--<div class="mt1-5 foot">
       		<p class="f11 color999">没有合适的加盟方案？</p>
       		<p class="f11 color999 mb1">不要着急，联系商务客服代表，为你快速解决！</p>
       		<p class="f11 c2873ff"><a href="tel:">电话商务代表 ></a></p>
       </div>-->
    </section>
    <section style="height: 10rem;"></section>
    <section class="enjoy" style="background-color: #f2f2f2;">
    	<div class="common_pops none"></div>
    </section>
@stop

@section('endjs')
    <script src="{{URL::asset('/')}}/js/agent/_v010300/chooseplan.js"></script>
    <script type="text/javascript">
    	var args=getQueryStringArgs(),
    		uid = args['uid'] || 0;
console.log(uid)
    	$(document).ready(function(){$('title').text('创建品牌加盟函')});
    	//点击下一步 
	    function joinText(agent_id,uid){
	    	var contractId = $('.choose_img').attr('contract_id');
	    	var brandId = $('.choose_img').attr('brand_id');
	    	if(contractId==undefined){
	    		tips('请选择加盟方案');
	    	}else {
	    		window.location.href = labUser.path + '/webapp/agent/chooseinvestor/detail?agent_id='+agent_id+'&contract_id='+contractId+'&brand_id='+brandId+'&uid='+uid;
	    	}
	  		console.log(contractId,brandId);
	  		
	    };
	    //点击确定
		function contractTrue(){
			var contractId = $('.choose_img').attr('contract_id');
	    	var brandId = $('.choose_img').attr('brand_id');
	    	if(contractId==undefined){
	    		tips('请选择加盟方案');
	    	}else {
	    		window.location.href = labUser.path + '/webapp/agent/createsuccess/detail?agent_id='+agent_id+'&contract_id='+contractId+'&brand_id='+brandId+'&uid='+uid;
	    	}
		};
    </script>
@stop