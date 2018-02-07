@extends('layouts.default')
@section('css')
	<link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/agent/inviteCode.css"/>
@stop
<!--zhangxm-->
@section('main')
	<section id='container' class="" style="background:#f2f2f2;">
		<div class="banner">
			<img src="/images/agent/inviteCode2.png" class="inveteImg"/>
		</div>
		<div class="code_content">
			<p class="" style="display: flex;justify-content: center;align-items: center;">
				<img src="/images/agent/redCode2.png" class="redCode"/>
				<span class="f15 color333">你的专属邀请码</span>
			</p>
			<p class="tel f18 color333 b"></p>
			<p class="f13 color333">or</p>
			<p class="code f18 color333 b"></p>
		</div>
		<div class="explain">
			<p class="f12 color333 l_h">
				<span class="lines flines"></span>
				<span class="f12 color333 ml1 mr1 b">使用规则说明</span>
				<span class="lines flines"></span>
			</p>
			<p class="f12 color333">①. 下载无界商圈经纪人端，注册用户，输入邀请码，可以选择输入手机号或专属6位邀请码。</p>
			<p class="f12 color333">②. 完成以上输入操作，自动形成绑定关系，即成为你的团队成员和邀请投资人。</p>
		</div>
	</section>
	
@stop
@section('endjs')
    <script type="text/javascript">
    	Zepto(function(){
    		$(document).ready(function(){
	    		$('title').text('我的邀请码');  
	        });
    		new FastClick(document.body);
    		$('body').css('background','#f2f2f2');
    		var args = getQueryStringArgs(),
    			agent_id = args['agent_id'] || '0',
    			urlPath = window.location.href;
    		var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
    		function getdetail(agent_id){
    			var params = {};
    			params['table'] = 'agent';
    			params['where_field'] = 'id';
    			params['where_value'] = agent_id;
    			params['fields'] = 'username,my_invite';
    			var url = labUser.agent_path + '/user/table-value/_v010001';
    			ajaxRequest(params,url,function(data){
    				if(data.status){
    					if(data.message){
    						$('.tel').text(data.message.username);
    						$('.code').text(data.message.my_invite);
    					}
    				}
    			})
    		};
    		getdetail(agent_id);
    	})
    </script>
@stop