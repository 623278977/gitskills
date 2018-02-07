@extends('citypartner.layouts.layout')
@section('title')
       <title>账号管理-密码修改</title>
@stop
@section('styles')
        <link rel="stylesheet" href="/css/citypartner/share.css" type="text/css"/>
        <link rel="stylesheet" href="/css/citypartner/account.css" type="text/css"/>
        <style>
            ::-webkit-input-placeholder { /* WebKit browsers */
                color:    #C8C8C8;
            }
            :-moz-placeholder { /* Mozilla Firefox 4 to 18 */
                color:    #C8C8C8;
            }
            ::-moz-placeholder { /* Mozilla Firefox 19+ */
                color:    #C8C8C8;
            }
            :-ms-input-placeholder { /* Internet Explorer 10+ */
                color:    #C8C8C8;
            }
            .intro .person .right>p>input{
                border: 1px solid #eee;
            }
            .intro .person .right>span{
                position: absolute;
                color: #23a4f8;font-size: 14px;
                top:200px;left:151px;
                display: none;
            }
        </style>
@stop
@section('content')
  <div class="container">
      <div class="font">
          <h2 id="test">
              账号管理
          </h2>
          <a href="/citypartner/account/list?uid={{ $partner->uid }}" >个人资料</a>
      </div>
      <form class="intro " action="password" id="updateForm" >
          <div class="person">
              <div class="left">修改密码</div>
              <div class="right" style="padding-top:45px;position: relative">
                  <p>
                      <label for="">旧密码</label>
                      <input type="password" name="oldPassword" placeholder="请输入您原来的密码">
                  </p>
                  <p>
                      <label for="">新密码</label>
                      <input type="password" name="password" placeholder="请设置新密码，6-16位数字或字母">
                  </p>
                  <p>
                      <label for="">确认密码</label>
                      <input type="password" name="confirmation_password" placeholder="请确认密码"  >
                  </p>
                  <span id="span_error">两次密码不一致!</span>
                  <p>
                      <label for="">验证码</label>
                      <input type="text" name="captcha" placeholder="请输入验证码" style="width:150px;">
                      <img id="img_captcha" style="width:100px; height:25px; margin-left:20px; border-radius: 0em; cursor:pointer;"  src="{{ url('citypartner/account/newcpt') }}" onclick="this.src='{{ url('citypartner/account/newcpt') }}?r='+Math.random();" alt="">
                  </p>
              </div>
          </div>
          <input type="hidden" name="formType" value="chPwd">
          <input type="hidden" name="uid" value="{{ $partner->uid }}">
          <a href="javascript:void(0)" type="button" style="margin-top:150px; margin-bottom:70px;" id="updatePwd">确定</a>
      </form>
      <div class="prompt psd_1 hide" id="success">
          <p >密码修改成功！</p>
          <p>请牢记密码！</p>
      </div>
      <div class="prompt psd_4 hide" id="error">
          <p id="info">新密码中包含非法字符</p>
          <p>请重新修改！</p>
      </div>

  </div>
@stop
@section('scripts')
          <script type="text/javascript" src="/js/citypartner/jquery-1.9.1.min.js"></script>
          <script type="text/javascript" src="/js/citypartner/common.js"></script>
            <script>
                  $('#updatePwd').click(function(){
                      var password = $("input[name='password']").val();
                      var confirmation_password = $("input[name='confirmation_password']").val();
                      if(password !== confirmation_password){
                            $("#span_error").css('display','block');
                          return false;
                      }
                    ajaxRequestPwd($('#updateForm').serializeArray(),$('#updateForm').attr('action'),function(data){
                        if(data.status){
//                            $('#info').html(data.message);
                            $('#success').css('display','block');
                            setTimeout(function(){window.location.href=data.forwardUrl;}, 2000);
                        }else {
                            $('#info').html(data.message);
                            $('#error').css('display','block');
                            setTimeout('$("#error").hide("slow")',2000);
                            $('#img_captcha').trigger('click');
                        }
                    });
                  });
            </script>
 @stop