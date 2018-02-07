@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/_v010300/waitcustomer.css" rel="stylesheet" type="text/css"/>
    <link href="/css/animate.css" rel="stylesheet" type="text/css"/>
    <link href="/css/agent/demo.css" rel="stylesheet" type="text/css"/>
    <script src="/js/agent/one.js"></script>
@stop
@section('main')
       <div class="loader" style="text-align: center; margin-top: 16rem">
        <div class="loader-inner ball-pulse-rise">
          <div></div>
          <div></div>
          <div></div>
          <div></div>
          <div></div>
        </div>
      </div>    
    <section id="act_container" class="none">
    	<article class="animated zoomInLeft">
            <!--  <div class="ui-contain">
                   <div class="ui-title fline color666 f15">
                     <span class="fl">一点点奶茶·哈哈哈</span>
                     <span class="fr color999 f12">创建时间：2017-11-12</span>
                   </div>
                   <ul class="ui-three color666 f13">
                   	   <li class="fline">
                   	   	   <p class="ui-line-height">目标投资人</p>
                   	   	   <ul class="ui-pict-text">
                   	   	   	    <li class="pl10" style="width:23%">
                   	   	   	    	<img class="ui-length" src="/images/default/avator-m.png"/>
                   	   	   	    </li>
                   	   	   	    <li >
                   	   	   	    	<p class="text-align b f15 color666">哈哈哈</p>
                   	   	   	    	<p class="text-align">哈哈哈</p>
                   	   	   	    </li>
                   	   	   	    <li>
                   	   	   	    	<button class="ui-pay">提醒支付</button>
                   	   	   	    </li>
                   	   	   	    <li>
                   	   	   	    	<button class="ui-pay ui-add">与他聊聊</button>
                   	   	   	    </li>
                   	   	   </ul>
                   	   	   <div class="clear ui-style"></div>
                   	   </li>
                   	   <li class="fline">
                   	   	   <p class="ui-line-height">目标品牌</p>
                   	   	   <ul class="ui-pict-text">
                   	   	   	    <li class="pl10" style="width:23%">
                   	   	   	    	<img class="ui-length2" src="/images/default/avator-m.png"/>
                   	   	   	    </li>
                   	   	   	    <li class="width1">
                   	   	   	    	<p class="text-align2 b f14 color666">哈哈哈</p>
                   	   	   	    	<p class="text-align2 f12">哈哈哈</p>
                   	   	   	    	<p class="text-align2 f12 ">行业分类：<span class="color333">鲜果饮品</span></p>
                   	   	   	    </li>
                   	   	   </ul>
                   	   	   <div class="clear ui-style"></div>
                   	   </li>
                   	   <li class="fline">
                   	   	     <p class="ui-line-height">电子合同状态</p>
	                   	   	   <ul class="ui-pict-text">
	                   	   	   	    <li class="width2 color999 text-align4">
	                   	   	   	    	<p class="color333 f15 b k">等待对方确认</p>
	                   	   	   	    	<p class="k">等待对方确认耗时：<span class="color333 f12 b">1天5小时22分</span></p>
	                   	   	   	    	<p class="k">请尽快让投资人确定邀请函，避免邀请函过期处理。</p>
	                   	   	   	    </li>
	                   	   	   </ul>
                   	   	   <div class="clear ui-style"></div>
                   	   </li>
                   </ul>
                   	<ul class="ui-three color666 f13 none">
                   	   <li>
                   	   	     <p class="ui-line-height">加盟方案</p>
	                   	   	   <ul class="ui-pict-text">
	                   	   	   	    <li class="width2 color999 text-align4" style="padding-right: 1rem">
	                   	   	   	    	<p class="color333 f12 ">加盟方案A<span class="fr">一点点区域代理方案</span></p>
	                   	   	   	    	<p class="color333 f12 ">加盟类型<span class="fr">区域代理</span></p>
	                   	   	   	    	<p class="color333 f12 ">总费用<span class="fr fd">￥19000</span></p>
	                   	   	   	    	<div class="ui-grey  f11">
                                        <ul class="ui-pay-detail">
                                             <li class="color666">费用明细</li>
                                             <li >
                                               <p class="color999">加盟费：￥20000</p>
                                               <p class="color999">加盟费：￥20000</p>
                                               <p class="color999">加盟费：￥20000</p>
                                             </li>
                                        </ul>
	                   	   	   	    	     <p class="color666 clear">最高提成<span class="fr  f8">可提成佣金部分 33%</span></p>
	                   	   	   	    	      <div style="width:100%;height:1.5rem;clear: both;"></div>	
	                   	   	   	    	     <p class="color666">合同/文件<span class="fr  fe">《品牌加盟付款协议》</span></p>
	                   	   	   	    	     <p class="color666 "><span class="fr  fe">《品牌加盟合同》</span></p>
	                   	   	   	    	       <div style="width:100%;height:1.5rem;clear: both;"></div>
	                   	   	   	    	     <p class="color999 ">* 如款项存在修改幅度，请联系商务对其进行修改。</p>
	                   	   	   	    	     <p class="color999 "> * 加盟合同将安排线下签约，实际成交按款项交齐为准。</p>
	                   	   	   	    	     <p class="color999 "> * 佣金结算以可提成佣金部分乘以提成比例进行计算。</p>	
	                   	   	   	    	     <p class="color999 "> *  对加盟方案存在疑问，请联系商圈客服人员。</p>	
	                   	   	   	    	     <p class="color999 "> *  无界商圈保持最终解释权。</p>			
	                   	   	   	    	</div>
	                   	   	   	    </li>
	                   	   	   </ul>
                   	   	   <div class="clear">
                   	  </li>
                   </ul>
                    <div class="ui-strch f15  clear ">展开<span style="padding-left: 1rem"><img class="down" src="/images/agent/1/down.png"><span></div>
             </div> -->
        </article>
         <div class="tc none nocomment" id="nocommenttip3">
                <img src="{{URL::asset('/')}}/images/nomessage_icon1.png" style="height: 16rem;width: 16rem;margin:0 auto;display: inline-block;">
         </div>  
        <div style="width:100%;height:5rem;"></div>
    </section>
@stop
@section('endjs')
 <script src="{{URL::asset('/')}}/js/agent/_v010300/wait.js"></script>
 <script>
 	$(document).ready(function(){
 		$('title').text('等待客户完成的');
 	});
 	$(document).on('tap','.ui-strch',function(){
 		if($(this).find('img').hasClass('a180')){
 			$(this).html('展开<span style="padding-left: 1rem"><img class="down" src="/images/agent/1/down.png"><span>');
 			$(this).prev().addClass('none');
 		}else{
 			$(this).html('收起<span style="padding-left: 1rem"><img class="down a180" src="/images/agent/1/down.png"><span>');
 			$(this).prev().removeClass('none');
 		}
 	});

    $(document).on('click','.gochat',function(){
      var uid=$(this).attr('data_id');
      var nickname=$(this).attr('data_nickname');
      goChat('c', uid, nickname);
      
    });
    function goChat(uType, uid, nickname) {
    if (isAndroid) {
      javascript: myObject.goChat(uType, uid, nickname);
    }
    else if (isiOS) {
      var message = {
          method : 'goChat',
          params : {
            'uType':uType,
            'id':uid,
            'name':nickname
          }
      }; 
      
      window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
    }
  };
 </script>  
@stop