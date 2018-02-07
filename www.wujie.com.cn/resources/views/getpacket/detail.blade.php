@extends('layouts.default')
<!-- Created by wangcx -->
@section('css')
  <link href="/css/animate.css" rel="stylesheet" type="text/css"/>
  <style>
    .bg_pic {
      background:url('/images/packet_bg01.png') no-repeat  left 0 top 0/100% 15.4rem,url('/images/packet_bg02.png') no-repeat  left 0 top 15.2rem/100% 15.3rem,url('/images/packet_bg03.png') no-repeat  left 0 top 30.3rem/100% 16rem,url('/images/packet_bg04.png') no-repeat  left 0 top 46.1rem/100% 26.8rem;
      min-height: 100%;
      padding-top: 30%;
    }
    .packet{
      width:29.6rem;
      background: #fb594e;
      border-radius:0.5rem;
      margin:auto; 
      overflow: hidden;
      padding-bottom: 7rem;
      position: relative;
    }
    .heal {
      width: 29.6rem;
      height: 24rem;
      border-radius: 50%;
      background: #de4337;
      position: absolute;
      top:-12rem;
      left: 0;
    }
    .form {
      padding-top: 20rem;
    }
    .wj_logo {
        position: absolute;
        left: 50%;
        width: 8.6rem;
        height: 8.6rem;
        bottom: -4.3rem;
        margin-left: -4.3rem;
        /*z-index: 3;*/
      }
      .getmes{
        width: 90%;
        height: 4.4rem;
        border-radius: 2.2rem;
        background: #fce7d2;
        margin: 1.5rem auto;
        overflow: hidden;
        position: relative;
      }
      .phone{
        height: 100%;
        width: 100%;
        border: none;
        background: #fce7d2;
        font-size: 1.4rem;
        padding-left: 2rem;
        padding-right: 2rem;
        color:#333;
      }
      .mesCode{
        height: 100%;
        padding-left: 2rem;
        background: #fce7d2;
        border:none;
        width: 50%;
        color:#333;
      }
      .getCode{
        height: 2.5rem;
        border: none;
        background: #f4c85d;
        color: #fff;
        border-radius: 1.25rem;
        margin-left: 1rem;
        float: right;
        position: absolute;
        top: 0.9rem;
        right: 1rem;
      }
      .tips{
        padding:1rem 2rem;
        background-color: rgba(0,0,0,0.9);
        position: fixed;
        top: 45%;
        left: 50%;
        transform: translateX(-50%);
        text-align: center;
        z-index: 9999999;
        font-size: 1.6rem;
        color:#fff;
        border-radius: 0.4rem;
        -ms-transform:translateX(-50%); /* IE 9 */
        -moz-transform:translateX(-50%);    /* Firefox */
        -webkit-transform:translateX(-50%); /* Safari 和 Chrome */
        -o-transform:translateX(-50%);  /* Opera */
    }
    .getPacket{
      background: #f4c85d;
      color: #cf3a40;
      font-size: 1.8rem;
      border-radius: 0.3rem;
      border: none;
      margin-top: 3rem,auto;
      padding: 1rem 4rem;
    }
    ::-webkit-input-placeholder { /* WebKit browsers */
              color:#b88e80;
         }
        :-moz-placeholder { /* Mozilla Firefox 4 to 18 */
         　　color:#b88e80;
        }
        ::-moz-placeholder { /* Mozilla Firefox 19+ */
         　color:#b88e80;
        }
        :-ms-input-placeholder { /* Internet Explorer 10+ */
         　color:#b88e80;
        }
  }
  </style>
@stop
@section('main')
  <section class="containerBox bgcolor " style="height: 100%;" id="containerBox">
      <div class="bg_pic">
        <div class="packet animated swing">
          <div class="heal">
            <img src="/images/wujie_logo.png" alt="" class="wj_logo">
          </div>
          <div class="form">
            <form action="">
              <p class="f14 getmes">
                <input type="text" placeholder="输入手机号" class="phone f14">
              </p>
              <p class="f14 getmes">
                <input type="text" placeholder="输入短信验证码" class="mesCode f14">
                <input type="button" value="获取验证码" class="getCode f14" id="getCode">
              </p>
              <input type="reset" id="reset" style="display: none;">
            </form>
            
          </div>
        </div>
        <div class="pt3 tc">
          <button class="getPacket">
            领取红包
          </button>
        </div>
      </div>
      <div class="tips none"></div>
  </section>
@stop
@section('endjs')
    <script>
      var $body = $('body');
      document.title = "无界商圈红包";
      // hack在微信等webview中无法修改document.title的情况
      var $iframe = $('<iframe ></iframe>').on('load', function() {
      setTimeout(function() {
      $iframe.off('load').remove()
      }, 0)
      }).appendTo($body);
  </script> 
    <script type="text/javascript">
      new FastClick(document.body);
      Zepto(function(){
        new FastClick(document.body);
        var reg=/^\d{10,11}$/;
        var args = getQueryStringArgs();
        var brand_id = args['id'] || 0;
        
         //发送验证码
        function sendCode(username,type,nation_code,app_name){
            var param = {};
                param.username = username;
                param.type = type;
                param.nation_code = nation_code;
                param.app_name = app_name;
            var url = labUser.api_path + '/identify/sendcode/_v020900';
            ajaxRequest(param,url,function(data){
                if(data.status){
                    console.log('发送验证码成功');
                }else{
                     tips(data.message)
                }
            })

        }

        //获取验证码
      $('#getCode').on('click',function(){
            var username = $('.phone').val();
            var app_name = 'wjsq';
            if(username == ''){
                tips('手机号码不能为空');
                return;
            }else if(!reg.test(username)){
                tips('手机格式不正确');
                return;
            }else{
                time($('#getCode'))
                sendCode(username,'standard','86',app_name);
            }
        })

        //注册领红包
        //
        function signIn(username,brand_id,code){
            var param = {};
                param.username = username;
                param.brand_id = brand_id;
                param.code =code;
            var url = labUser.api_path + '/redpacket/receive-share/_v020902'
            ajaxRequest(param,url,function(data){
                if(data.status){
                    var packet_ids  = data.message.red_packets;
                    var uid = data.message.uid;
                    window.location.href = labUser.path+'/webapp/packet/detail?id='+packet_ids+'&uid='+uid;
                }else{
                  tips(data.message);
                }
            })
        }

        $('.getPacket').click(function(){
            var username = $('.phone').val();
            var mesCode = $('.mesCode').val();
            if(username == ''){
                tips('手机号码不能为空');
                return;
            }else if(!reg.test(username)){
                tips('手机格式不正确');
                return;
            }else{
              signIn(username,brand_id,mesCode);
            }
        })

        //验证码倒计时
        var wait = 60;
        function time(o){
            if (wait == 0) {
                o.removeAttr("disabled");
                o.val("重新发送");
                o.css({
                  "font-size":"15px",
                  "color":'#fff',
                  "background":"#f4c85d"
                });
                wait = 60;
            }else {
                o.attr("disabled", true);
                o.css({
                  "font-size":"15px",
                  "color":'#fff',
                  "background":"#f4d7ba"
                });
                o.val('重新发送 ' + wait + 's');
                wait--;
                tt = setTimeout(function () {
                       time(o)
                    },
                    1000)
            }
        };

         //提示框
         function tips(e) {
            $('.tips').text(e).removeClass('none');
            setTimeout(function() {
              $('.tips').addClass('none ');
            }, 1500);
        }
      })
    </script>  
@stop