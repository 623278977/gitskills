@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/_v010004/knowledgetree.css" rel="stylesheet" type="text/css"/>
    <style>
    	.tree_header {
    		width: 100%;
    		height: 12rem;
    	}
    </style>
@stop

@section('main')
    
    <section id="container" >
        <!-- 头部 -->
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
    </section>
@stop

@section('endjs')
<script src="{{URL::asset('/')}}/js/agent/_v010004/knowledgetree.js"></script>
<script type="text/javascript">
	$(document).ready(function(){ $('title').text('知识树'); });
</script>
@stop