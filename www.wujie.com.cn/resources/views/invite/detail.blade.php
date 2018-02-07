@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/invite.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/intlTelInput.css" rel="stylesheet" type="text/css"/>
    <style>
      .intl-tel-input .flag-dropdown {
          left: 3rem;
      }
      .intl-tel-input.inside input[type="text"], .intl-tel-input.inside input[type="tel"] {
          padding-left: 7.5rem;
      }
    </style>
@stop
@section('beforejs')
  <script>
    if(!is_weixin()&&!isiOS&&!isAndroid){
        var code="{{$code}}";
        window.location.href= labUser.path +'/webapp/invite/detailpc?code='+code;
    };
  </script>
   <script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan id='cnzz_stat_icon_1261374248'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s95.cnzz.com/z_stat.php%3Fid%3D1261374248' type='text/javascript'%3E%3C/script%3E"));</script>
@stop
@section('main')
    <section id="container" class="">
     <div class="header">
        <div class="found">
            <p ><span id="share-name">Xavier</span>喊你上「无界商圈」</p>
            <p >“在这里，发现商机，不信你看看~”</p>
        </div>
    </div>

    <div class="invite">
        <form action="" id="register" method="post" >
            <!-- <p> <input type="text"  name="zonenumber" placeholder="区号" id="zone" /></p> -->
            <p> <input type="text"  name="phonenumber" id="zone" value="+86 " placeholder="手机号" /></p>
            <p> <input type="text" name="piccode"  placeholder="输入图形验证码"/>
                <button type="button" data-id="" class="ident_code" id="piccode"></button>
            </p>
           <p><input type="text" name="mescode" placeholder="短信验证码"/>
               <button type="button" class="ident_code" id="mescode" >获取验证码</button>
           </p>
            <p> <input type="text" name="nickname" placeholder="请设置您的昵称"/></p>
            <p><input type="password" name="psd" placeholder="设置您的密码"/></p>
            <button type="button" class="btn-foot">立即注册</button>
        </form>
        
    </div>
    <div class="intro">
        <p class="wjsq">
            <span>无界商圈</span>
        </p>
        <p class="intro_del">创业讲座、大咖分享、产品发布等高端活动可参与可观看，<br>
            打破本地限制，可跨域互动交流！
        </p>
        <img src="{{URL::asset('/')}}/images/iPhone6.png" alt="背景图片"/>
    </div>
    <div class="cover hide">
    </div>
    <div class="prompt hide">
        <h3>提示</h3>
        <p>您已是无界商圈用户。</p>
        <div>
            <a href="javascript:void(0)" class="know">知道了</a><a href="javascript:void(0)" class="open">打开应用</a>
        </div>
    </div>
    <div class="alert hide">
        <p></p>
    </div>
    <div class="module hide">
        <img src="{{URL::asset('/')}}/images/safari.png" alt="点击右上角，浏览器中打开">
    </div>
    </section>
@stop

@section('endjs')
<script src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
<script src="{{URL::asset('/')}}/js/intlTelInput.js"></script>
<script>
    var $body = $('body');
    document.title = "邀请好友注册「无界商圈」";
    // hack在微信等webview中无法修改document.title的情况
    var $iframe = $('<iframe ></iframe>').on('load', function() {
    setTimeout(function() {
    $iframe.off('load').remove()
    }, 0)
    }).appendTo($body)
</script>
<script >
    Zepto(function () {
    new FastClick(document.body);
    //国际区号
    $('#zone').intlTelInput();
  
    //pc端获得当前日期
    var date=new Date();
    var formatDate = function (date) {
        var y = date.getFullYear();
        var m = date.getMonth() + 1;
        m = m < 10 ? '0' + m : m;
        var d = date.getDate();
        d = d < 10 ? ('0' + d) : d;
       // return y + '-' + m + '-' + d;
        $(".date").html(y + '-' + m + '-' + d);
    };
    formatDate(date);
    
 

      // 获取分享人信息
    var code="{{$code}}";
    function getsharename(code){
        var param={};
        param['code']=code;
        var url=labUser.api_path+'/activity/sharename';
        ajaxRequest(param,url,function(data){
            if(data.status){
                $("#share-name").html(data.message.name);
            };
        })
    };
    getsharename(code);
   // 获取图形验证码id

   function picidentifyCode(){
        var param={};
       var url=labUser.api_path+'/identify/captchaid';
        ajaxRequest(param,url,function(data){
            if(data.status){
               $("#piccode").attr("data-id",data.message.captcha_id);
             //   console.log(data.message.captcha_id);
                console.log($("#piccode").attr("data-id"));
                 picidentify($("#piccode").attr("data-id"));
            };
        });

   };
   picidentifyCode();

  //图形验证码
   function picidentify(id){
        var param={};
         param['id'] =id;
       var url=labUser.api_path+'/identify/sendcaptcha';

        ajaxRequest(param,url,function(data){
            if(data.status){
                $("#piccode").html(data.message.captcha);
       //         console.log(data.message.captcha);
            };
        })
   };

   //picidentify();
   $('#piccode').click(function(){
        picidentifyCode();
        
   });

    //短信验证码计时器
    var tt;
    var wait = 60;

    function time(o) {
        if (wait == 0) {
            o.removeAttr("disabled");
            o.html("重新发送");
            o.css({
              "font-size":"1.6rem",
              "background":"#8ec5e9"
            });
            wait = 60;
        } else {
            o.attr("disabled", true);
            o.css({
              "font-size":"1.2rem",
              "background":"#c8c8c8"
            });
            o.html('重新发送(' + wait + 's)');
            wait--;
            tt = setTimeout(function () {
                    time(o)
                },
                1000)
        }
    };
    //发送手机验证码
    function getmescode(username,type,code){
        var param={};
        param["username"]=username;
        param["type"]=type;
        param["nation_code"]=code;
        var url=labUser.api_path+'/identify/sendcode';
        ajaxRequest(param,url,function(data){
         
               if(data.status) {
                 var getcode=$("#mescode");
                 time(getcode);                    
                }else{
                  $(".alert p").html(data.message);
                    errorshow();
                    return false;
               };
        });
    };

    //  验证手机验证码
    function checkmescode(code,username,type){
        var param={};
        param["code"]=code;
        param["username"]=username;
        param["type"]=type;
        var url=labUser.api_path+'/identify/checkidentify';
        ajaxRequest(param,url,function(data){
            if(data.status){
                  var codeId=$("#piccode").attr("data-id");
                  var  phonenum=$("input[name='phonenumber']").val().split(' ')[1],
                        piccode=$("input[name='piccode']").val(),
                        mescode=$("input[name='mescode']").val(),
                        nick=$("input[name='nickname']").val(),
                        psd=$("input[name='psd']").val(),
                        code="{{$code}}";
                       getuserDetail(phonenum,psd,nick,code,codeId,piccode);
              }else{
                 $(".btn-foot").removeAttr('disabled');
                 $(".alert p").html(data.message);
                  errorshow();
              };
            });
    };

    $("#mescode").click(function(){
        var  phonenum=$("input[name='phonenumber']").val().split(' ')[1];    //获取输入的手机号
             piccode=$("input[name='piccode']").val(),
             nation_code =$("input[name='phonenumber']").val().split(' ')[0];
        if(nation_code == ''){
             $(".alert p").html("请输入手机区号");
            errorshow();
            return false;
        }
        if(phonenum==""){
            $(".alert p").html("请输入手机号");
            errorshow();
            return false;
        };
        if(phonenum!=""){
            if(!reg.test(phonenum)){
                $(".alert p").html("请输入正确的手机号");
                errorshow();
                return false;
            };
        };
        if(piccode==""){
            $(".alert p").html("请输入图形验证码");
             errorshow();  
            return false;
       };
       if(piccode!=""){
            if (piccode!=($("#piccode").html())){
                $(".alert p").html("图形验证码不正确");
                errorshow();  
                return false;
            };
       };
        getmescode(phonenum,'register',nation_code);

    });

// 验证手机号已存在
  function checkuser(username){
    var param={};
    param["username"]=username;
    var url=labUser.api_path+'/login/checkuser';
    ajaxRequest(param,url,function(data){
       var phonenum=$("input[name='phonenumber']").val().split(' ')[1],
           mescode=$("input[name='mescode']").val();
        if(data.status){
            if(data.message==1){
                $(".cover").removeClass("hide");
                $(".prompt").removeClass("hide");
                return false;
            }else if(data.message==0){
              console.log(mescode);
            };
        };
    });
  };
  $(".know").click(function(){
     $(".cover").addClass("hide");
     $(".prompt").addClass("hide");
  });

  //手机号失去焦点
    $("input[name=phonenumber]").blur(function(){
        var phonenum=$("input[name='phonenumber']").val().split(' ')[1];
        checkuser(phonenum);
    });

//  提交注册成功信息，获取用户uid;
   function getuserDetail(username,password,nickname,code,captcha_id,captcha_value){
          var param={};
          param['username']=username;
          param["password"]=password;
          param["nickname"]=nickname;
          param["code"]=code;
          param["captcha_id"]=captcha_id;
          param["captcha_value"]=captcha_value;
          var url=labUser.api_path+'/login/inviteregister';
          
          ajaxRequest(param,url,function(data){
              if(data.status){
                var uid=data.message.uid;
                console.log(data.message.uid);
                window.location.href= labUser.path +'/webapp/join/detail?uid='+uid;
              }else{
                $(".btn-foot").removeAttr('disabled');
                $(".alert p").html(data.message); 
                errorshow();  
                return false;
              }
          });
       }; 
    //错误提示
    function errorshow(){
        $(".alert").css("display","block");
        setTimeout(function(){$(".alert").css("display","none")},2000);
   };   
    //表单验证
    var reg=/^\d{10,11}$/;  //验证手机号,不能加g，加后出现判断错误
    var psdreg=/^\w{6,16}$/;

   //点击立即注册按钮
   $(document).on('tap','.btn-foot',function(){
     // _czc.push(﻿["_trackEvent","二维码",'下载','安卓下载',1]);
    var phonenum=$("input[name='phonenumber']").val().split(' ')[1],
        piccode=$("input[name='piccode']").val(),
        mescode=$("input[name='mescode']").val(),
        nick=$("input[name='nickname']").val(),
        psd=$("input[name='psd']").val(),
        nation_code =$("input[name='phonenumber']").val().split(' ')[0];

       if(nation_code==""){
            $(".alert p").html("请选择国际区号"); 
            errorshow();  
            return false;
       };
       if(phonenum==""){
            $(".alert p").html("请输入手机号"); 
            errorshow();  
            return false;
       };
       if(phonenum!=""){
            if(!reg.test(phonenum)){        
                $(".alert p").html("请输入正确的手机号"); 
                errorshow();  
                return false;
            }
       };
       if(piccode==""){
            $(".alert p").html("请输入图形验证码");
             errorshow();  
            return false;
       };
       if(piccode!=""){
            if (piccode!=($("#piccode").html())){
                $(".alert p").html("图形验证码不正确");
                errorshow();  
                return false;
            };
       };
       if(mescode==""){
            $(".alert p").html("请输入短信验证码");
             errorshow();  
            return false;
       };
       
       if(nick==""){
            $(".alert p").html("请输入昵称");
             errorshow();  
            return false;
       };
       if(psd==""){
            $(".alert p").html("请输入6-16位密码");
             errorshow();  
            return false;
       }; 
       if(psd!=""){
         if (!psdreg.test(psd)){
             $(".alert p").html("请输入6-16位密码");
             errorshow();  
            return false;
         }
       };
     
      $(".btn-foot").attr('disabled','true');
      checkmescode(mescode,phonenum,'register');
      
    });
   $(".open").click(function(){
         if(is_weixin()){
                    $(".module").removeClass("hide");
                    $(".module").click(function(){
                     $(this).addClass("hide");
              });
            };
            if (isiOS) {
                  window.location.href = 'openwjsq://' ;
              }
            if (isAndroid) {
                  window.location.href="openwjsq://welcome";
               };
   });
});
       
</script>

@stop