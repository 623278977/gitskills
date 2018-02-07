@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/_v010100/knowledgetree.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/agent/_v010004/list.css" rel="stylesheet" type="text/css"/>
    <style>
    	.tree_header {
    		width: 100%;
    		height: 12rem;
    	}
    	.tree_header img {
    		width: 100%;
    		height: 100%;
    	}
    </style>
@stop

@section('main')
    
    <section id="container" >
        <!-- 头部 -->
        <div class="list">
        	<!--<div class="tree_header mb1-2">
	            <img src="/images/agent/head_bcg.png" alt="" class="">
	        </div>
	        <div class="tree_nav bgwhite">
	            <div class="tree_item">
	              <img src="/images/agent/selection.png" alt="">
	              <p>餐厅选址</p>
	            </div>
	
	            <div class="tree_item">
	              <img src="/images/agent/design.png" alt="">
	              <p>装修设计</p>
	            </div>
	
	            <div class="tree_item">
	              <img src="/images/agent/Finance.png" alt="">
	              <p>财务管理</p>
	            </div>
	
	            <div class="tree_item">
	              <img src="/images/agent/hr.png" alt="">
	              <p>人力资源</p>
	            </div>
	
	            <div class="tree_item">
	              <img src="/images/agent/plan.png" alt="">
	              <p>营销策划</p>
	            </div>
	
	            <div class="tree_item">
	              <img src="/images/agent/skill.png" alt="">
	              <p>管理技能</p>
	            </div>
	
	            <div class="tree_item">
	              <img src="/images/agent/guide.png" alt="">
	              <p>创业指南</p>
	            </div>
	        </div>-->
        </div>
        <div class="commend">
          <!--  <div class="ui_con color999">
                  <div class="padding">
                        <ul class="ui_text_pict">
                             <li>
                                 <p class="color333 f14 b ui-nowrap-multi">骤然的爱情，穿不过永不睡哈哈哈哈哈</p>
                                 <p class="f12 ui-nowrap-multi">
                                    狮子（Lion）被假设等同于行走动物（Walk），老鹰（Eagle）被假设等同于飞行动物（Fly）。
                                    这看起来很成功，因为子类能严格向上转型，但他有隐患。
                                    当有一种天马（Pegasus）介入到里面的时候，我们才发现狮子其实只是“会行走的动物”， 
                                 </p>
                             </li>
                             <li>
                              <div class="ui_protect_pict fr"><img class="ui_pict1" src="/images/agent/ui2.png"/></div>
                             </li>
                        </ul>
                        <p class="clear ui-border-b ui_row"></p>
                        <ul class="ui_text_down clear f11">
                              <li>
                                <ul class="ui_flex">
                                    <li>
                                      <img class="ui_zan fl" src="/images/agent/grey.png"/><span class="ui_padding fl">6000</span>
                                    </li>
                                    <li>
                                      <img class="ui_zan ui_mess" src="/images/agent/ui.png"/><span class="ui_padding">6000</span>
                                    </li>
                                    <li>
                                      <img class="ui_seen ui_mess" src="/images/agent/seen.png"/><span class="ui_padding">6000</span>
                                    </li>
                                </ul>
                              </li>
                              <li>作者：无界商圈</li>
                        </ul>
                        <p class="clear margin"></p>
                    </div>
                  <div class="fline style"></div>
           </div> -->
           
      </div>
    </section>
@stop

@section('endjs')
<script src="{{URL::asset('/')}}/js/agent/_v010100/knowledgetree.js"></script>
<script type="text/javascript">
	$(document).ready(function(){ $('title').text('知识树'); });
</script>
@stop