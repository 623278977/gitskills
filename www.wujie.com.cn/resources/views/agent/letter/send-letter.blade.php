@extends('layouts.default')
@section('css')
	<link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/agent/send-letter.css"/>
	<link href="{{URL::asset('/')}}/css/intlTelInput.css" rel="stylesheet" type="text/css"/>
@stop
<!--zhangxm-->
@section('main')
	<section id='container' class="medium">
		<!--<div class="head">
			<div class="head-cont">
				<img src="/images/666.jpg" class="mr1"/>
				<div class="text">
					<span class="cffa300 f13 bold b">皮皮凯：</span><span class="f13 bold b color333">“一起来用无界商圈经纪人办，代理品牌，邀请投资人、管理加盟客户、轻松签大单，优惠享不停！”</span>
				</div>
			</div>
		</div>
		<div class="foot mb7">
			<input type="text" class="mobile f15 color999 medium" placeholder="请输入手机号"/><br />
			<span id="moileMsg"></span><br />
			<span class="submit f15">接受邀请，注册无界商圈经纪人</span>
		</div>-->
	</section>
	<section>
		<div class="common_pops none"></div>
       <!--弹窗-->
         <div class ="bg-model none">
      　　   <div class ='ui_content'>
                <div class="ui_task ui-border-b relative">
                  <span class="f15">请输入手机验证码</span>
                </div>
                <div class="ui_task_detail f12 color666 padding">
                    <div class="ui_iphone border999">
                      <input id="code" type="text" class="input f12 fl" name="wrirecode" maxlength="5" placeholder="请输入验证码">
                      <button class="f12 fr getcode">获取验证码</button>
                    </div>
                    <div style="width:100%;height:3.3rem"></div>
                    <div class="ui_iphone padding-right">
                      <button class="f12 fl border999 with cancel">取消</button>
                      <button class="f12 fr blue with makesure">确定</button>
                    </div>
                </div>
             </div>
          </div>
        <!-- 注册成功弹窗 -->
        <div class="fixbg none">
            <div class="suc_tips tc pt2 pl1-5 pr1-5">
              <p class="f15 fline b" style="padding-bottom: 1rem;">恭喜你注册成功</p>
              <p class="f15 tl pt1">初始密码为您的注册手机号，请及时登录并修改密码!</p>
              <button class="be_sure mt1 mb2">确定</button>
            </div>
        </div>
	</section>
	<section class="enjoy" style='padding-bottom:7rem'>
    </section>
@stop
@section('endjs')
    <script src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
    <script src="{{URL::asset('/')}}/js/intlTelInput.js"></script>
    <script src="{{URL::asset('/')}}/js/agent/send-letter.js"></script>
@stop