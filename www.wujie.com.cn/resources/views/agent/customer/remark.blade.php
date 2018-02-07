@extends('layouts.default')
<!--zhangx-->
@section('css')
	<!--<link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/agent/bootstrap.min.css"/>-->
	<link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/agent/remark.css"/>
@stop
<!--zhangxm-->
@section('main')
	<section id='container' class="medium">
		<p class="p125-1 f12 color666">备注名</p>
		<p class=""><input class="f15 bcg-f remark-btn input color333 medium pt1 pb1" value=""></p>
		<p class="p125-1 f12 color666 aaa">客户等级</p>
		<div class="dropdown bgwhite">
		    <div class="re-div ">
		    	<span class="f15 choose-grade medium color333 ml05 medium"></span>
        		<span class="re-caret"><img class="ui_img5" src="{{URL::asset('/')}}/images/020700/r1.png"></span>
		    </div>
		    <ul class="re-ul border-ra dropdown-menu" role="menu" aria-labelledby="dropdownMenu1">
		    	<li class="fline"></li>
		        <!--<li role="presentation" class=" remark-li fline">
		            <span role="menuitem" tabindex="-1" class="grade ">普通用户</span>
		        </li>-->
		        <!--<li role="presentation" class="divider" ></li>-->
		        <!--<li role="presentation" class=" remark-li fline">
		            <span role="menuitem" tabindex="-1" class="grade">重点客户</span>
		        </li>-->
		        <!--<li role="presentation" class="divider"></li>-->
		        <!--<li role="presentation" class=" remark-li fline">
		            <span role="menuitem" tabindex="-1" class="grade">关键客户</span>
		        </li>-->
		    </ul>
		     
		</div>
	</section>
    <section class="enjoy " style='padding-bottom:4rem'>
    </section>
    <section style="border-top:1px solid transparent"></section>
@stop
@section('endjs')
    <script src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
    <!--<script src="{{URL::asset('/')}}/js/agent/src/bootstrap.js" type="text/javascript" charset="utf-8"></script>-->
    <script>
    		new FastClick(document.body);
			var args = getQueryStringArgs(),
			id = args['customer_id'] || '0',
			uid = args['agent_id'] || '0',
			urlPath = window.location.href;
			function getdetail(id,uid){
				var param = {};
				param['customer_id'] = id;
				param['agent_id'] = uid;
				param['tags']='edits';
				var url = labUser.agent_path + '/customer/edit-name/_v010000';
				ajaxRequest(param,url,function(data){
					if(data.message){
//						data.message.level.shift();    
						var levels = data.message.level;
						$('.input').val(data.message.remark);
						$('.choose-grade').html(data.message.current_level);
						$('.choose-grade').attr('current_levels',data.message.current_levels);
						var conHtml = '';
						if(levels.length>0){
							conHtml += '<li class="fline"></li>';
							$.each(levels, function(i,v) {
								conHtml += '<li  role="presentation" class="remark-li fline"><span role="menuitem" tabindex="-1" class="grade f15 color333 medium" data_num="'+i+'">'+v+'</span></li>';
							});
							
						}
						$('.re-ul').html(conHtml);
					}
				})
			}
			getdetail(id, uid);

			$('.re-ul').hide();
   			$(".re-div").click(function(e){
		        $(".re-ul").show();
		        e.stopPropagation();
			});
			$(document).on('click','.remark-li',function(){
    			$('.choose-grade').html($(this).children('span').html());
    			var data_nums = $(this).children('span').attr('data_num');
    			$('.choose-grade').attr('current_levels',data_nums);
    			
    			$('.re-ul').hide();
   			});
			$(document.body).click(function(){
			     $(".re-ul").hide();
   			});
    		new FastClick(document.body);
			var args = getQueryStringArgs(),
			id = args['customer_id'] || '0',
			uid = args['agent_id'] || '0';
			//传值	
    	function dosave(id,uid){
    		var params = {};
    		var remark = $('.input').val();
    		var customel_level = $('.choose-grade').html();
    		params['level'] = $('.choose-grade').attr('current_levels');
    		params['remark'] = remark;
    		params['customel_level'] = customel_level;
    		params['customer_id'] = id;
			params['agent_id'] = uid;
			params['tags'] = 'submit_edits';
			var url = labUser.agent_path + '/customer/edit-name/_v010000';
			ajaxRequest(params,url,function(data){
				if(data.status){
					alert(data.message);
					dosave_success(uid);
				}
			});
    	};
    	$(document).on('click','.aaa',function(){
    		dosave(id,uid);
    	});
    	//点击保存备注信息
		function dosaves(){
			dosave(id,uid);
		};
		function dosave_success(uid) {
			if(isAndroid) {
				javascript: myObject.dosave_success(uid);
			}
			else if(isiOS) {
				var data = {
					"uid": uid
				}
				window.webkit.messageHandlers.dosave_success.postMessage(data);
			}
		};
    </script>
@stop