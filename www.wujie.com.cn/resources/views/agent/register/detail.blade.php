@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/v010000/register.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section id="act_container" class="none">
          <div class="tips none"></div>
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
                      <button class="f12 fl border999 with cancel mb2">取消</button>
                      <button class="f12 fr blue with makesure mb2">确定</button>
                    </div>
                </div>
             </div>
          </div>
      <div style="height:2.2rem"></div>
      <div class="ui_allcon">
        <div class="ui_topinfor">
          <div class="ui_inner_detail">
            <ul class="ui_router">
              <li>
                <img class="ui_img" src="{{URL::asset('/')}}/images/default/avator-m.png">
              </li>
              <li>
                <p class="f13 color333 textleft"><span class="ffa300 nickname">哈哈哈：</span>“一起来用无界商圈，提供OVO场 </p>
                <p class="f13 color333 textleft">景化招商服务，品牌加盟更有专业经纪人</p>
                <p class="f13 color333 textleft">服务，轻松easy一步搞定！” </p>
              </li>
            </ul>
          </div>
        </div>
        <div class="ui_register_tel">
          <input class="phonenumber" name="phone" type="tel" placeholder="请输入手机号" maxlength="11" >
        </div>
        <div class="ui_router_con relative">
          <img class="ui_img_5 absolute" src="{{URL::asset('/')}}/images/020700/w5.png">
           <img class="ui_img_6 absolute" src="{{URL::asset('/')}}/images/020700/w7.png">
        </div>
       <center><button class="submit">接受邀请，注册无界商圈</button></center>
      </div>
    </section>
@stop
@section('endjs')
    <script type="text/javascript" src="{{URL::asset('/')}}/js/agent/register.js"></script>
@stop