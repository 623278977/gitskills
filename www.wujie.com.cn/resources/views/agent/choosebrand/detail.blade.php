
@extends('layouts.default')
@section('css')
     <link href="{{URL::asset('/')}}/css/agent/_v010300/choosebrand.css" rel="stylesheet" type="text/css"/> 
    
@stop

@section('main')
    <section class="containerBox">
       <!--<div class="top dis_bet bgwhite">
       		<div class="mu">
       			<img src="/images/agent/mub_blue.png" class="mb05"/>
       			<span class="f13 c2873ff">选择目标品牌</span>
       		</div>
       		<span class="fline w4 mt2-5"></span>
       		<div class="fangan">
       			<img src="/images/agent/fangan_grey.png" class="mb05"/>
       			<span class="f13 color_ccc">选择加盟方案</span>
       		</div>
       		<span class="fline w4 mt2-5"></span>
       		<div class="">
       			<img src="/images/agent/send_grey.png" class="mb05"/>
       			<span class="f13 color_ccc">发送至投资人</span>
       		</div>	
       </div>-->
       <!--选择品牌-->
       <!--<div class="choose mt1-2 bgwhite">
       		<p class="pt1-5 pb1-5 fline"><span class="f15 color333">选择品牌</span><span class="f12 color999">目前您代理 <em class="brand_num f12 color999"></em> 个品牌</span></p>
       		<div class="brand_list">
       			
	       		<div class="chooseBrand fline">
	       			<div class="">
	       				<span class="chooseNo mr1"></span>
	       				<div class="brand">
	       					<p class="brand_logo mr1"><img src="/images/act_banner.png"/></p>
	       					<div class="">
	       						<p class="f14 color333 brand_name">喜茶® HEEKCAA</p>
	       						<p class="f11 color999 mb1-2 brand_text">INSPIRATION OF TEA</p>
	       						<span class="f12 color666">行业分类：</span><span class="f12 color333">鲜果饮品</span>
	       					</div>
	       				</div>
	       			</div>
	       			<p class="f11 color999"><span class="">支持：</span><span class="support">单店加盟</span></p>
	       		</div>
       		</div>
       </div>-->
       <!---->
       <!--<div class="mt1-5 foot">
       		<p class="f11 color999">没有代理投资人想要加盟的品牌？</p>
       		<p class="f11 color999 mb1">赶紧申请代理，好机会不要错过！</p>
       		<p class="f11 c2873ff">点击前往品牌列表 ></p>
       </div>-->
    </section>
    <section class="enjoy" style="background-color: #f2f2f2;">
    	<div class="common_pops none"></div>
    </section>
@stop

@section('endjs')
    <script src="{{URL::asset('/')}}/js/agent/_v010300/choosebrand.js"></script>
    <script type="text/javascript">
    	$(document).ready(function(){$('title').text('创建品牌加盟函')});
    	//点击下一步时调用    
	    function joinText(agent_id,uid){
			var brandId = $('.choose_img').attr('brand_id');
	  		console.log(brandId);
	  		if(brandId==undefined){
	  			tips('请选择品牌');
	  		}else {
	  			window.location.href = labUser.path + '/webapp/agent/chooseplan/detail?agent_id='+agent_id+'&brand_id='+brandId+'&uid='+uid;
	  		}
	  		
	    };
		
    </script>
@stop