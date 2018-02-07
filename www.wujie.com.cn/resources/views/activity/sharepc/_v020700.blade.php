@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/_v020700pc/chosen.min.css" rel="stylesheet" type="text/css"/> 
    <link rel="stylesheet" href="{{URL::asset('/')}}/css/swiper.min.css">
    <link href="{{URL::asset('/')}}/css/_v020700pc/actdetail.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/_v020700pc/pc.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/_v020700pc/act_related.css" rel="stylesheet" type="text/css"/>
     
@stop
@section('main')
    <section id="act_container" class="none">
        <!-- 头部 -->
       <header>
           <div style="width:1172px;height:72px;margin: 0 auto">
           <img class="logo" style="cursor:pointer;" src="{{URL::asset('/')}}/images/020700pc/p2.png" alt=""/>
           <div id="download" style="cursor:pointer;" class="width fr margin-l">下载APP</div>
           <div id="moreact" style="cursor:pointer;" class="width fr margin-m">更多活动</div>
           </div>
       </header>
       <!-- 点击下载APP出现下载图片 -->  
       <div id="doan_pict" style="cursor:pointer;" class="none">
          <span><img src="{{URL::asset('/')}}/images/020700pc/p40.png"></span>
          <img  src="{{URL::asset('/')}}/images/020700pc/p12.png">
          <h6>扫一扫，下载无界商圈</h6>
          <h6>优活动，更精彩</h6>
       </div>
      <!--  点击出现更多活动 -->
      <div id="activitylist" class="activitylist none " style="margin-top:72px;"></div>
      <div class="jaizai" style="padding-bottom:10px">
      <h5 style="color:#ea5520;margin-top: 0;margin-bottom: 0">点击加载更多</h5>
           <img style="width:20px;height:20px" src="{{URL::asset('/')}}/images/020700pc/icon.png" alt=""/>
      </div>
     <!--  报名弹窗 -->
     <div id="signname" class="signname none">

      <!--  二维码 -->
      <!--  报名 -->
          <div id="textsign" class="textsign relative firststep a-rotateinLT  ">
            <div class="swith_icon">
               <img src="{{URL::asset('/')}}/images/020700pc/p94.png" alt="">
            </div>
            <span></span>
              <div id="pre_defalut" class="">
                  <input class="shuru position50" name="nickname" type="text" placeholder="姓名：" maxlength="10" /><br/>
                  <div class="choice_zone">
                    <select id="country" class="dept_select" style="border:0;float:left;margin-top: 10px"></select>
                    <select id="province" class="dept_select" style="border:0;float:left;margin-top: 10px" ></select>
                    <select id="city" class="dept_select" style="border:0;float:left;margin-top: 10px"></select>
                  </div>
                  <input class="shuru position30 " style="padding-bottom:12px " name="phonenumber" type="text" placeholder="手机号："   value="" /><br/>
                  <div id="jiaoyan" class="none" style="text-align: center ">
                    <input name="pict_code" class="position51 position60"  style="width:180px" type="text " placeholder="校验码：" maxlength="5" />
                    <samp id="piccode" class="position53"></samp>
                  </div>
                  <input class="position51" name="codeS" type="text " placeholder="请输入验证码"  /><button id="mescode" class="position52">获取短信验证码</button><br/>
                  <div id="submitnext" class="choicemeeting" style="cursor:pointer;">下一步，选择会场</div>
              </div>
              <div id="next_defalut" class="none">
                   <div id="style1"></div>
                   <div id="p60" class=""> <img src="{{URL::asset('/')}}/images/020700pc/p60.png" alt=""></div>
                   <div id="erweima"></div>
                   <div id="p61" class=""> <img src="{{URL::asset('/')}}/images/020700pc/p70.png" alt=""></div>
              </div>
          </div>
      <!--  选择会场 -->
          <div id="textsign" class="textsign meettextsign secondstep a-rotateinLT  relative none">
                <span></span>
                <div class="meet_pict"></div>
                <div class="meet_place">
                  <div class="meet_live_text"><samp class="meet_choice_btn" ></samp><label style="font-size:12px;">现场参与</label><label style="color:#eb653c">推荐</label></div> 
                  <!-- <div class="text_meet_live">
                       <div class="upper_">  
                           <samp  style="padding-left:44px" class="defulat_choice_btn"></samp>
                           <label style="float:left;font-size:12px;">宁波</label>
                           <label style="color:#999;float:right;margin-right:20px">宁波巴啦啦区OVO运营中心</label>
                       </div>
                       <div class="downper none">
                          <label class="_address_">联系地址：</label><label style="color:#999;">郑州巴啦啦运营中心</label>
                          <label class="_phone_">联系电话：</label><label style="color:#999;">13345332678</label>
                      </div>
                  </div> -->                                   
                </div>
                <div class="see_live_text none"><samp class="defulat_choice_btn"></samp><label>观看直播</label></div>
                <div class="sub_live_text" style="cursor:pointer;">提交</div>
          </div>
         <!--  报名成功 -->
           <div id="textsign" class="textsign meettextsign height a-rotateinLT  laststep relative none" style="overflow: auto;">
                <span></span>
                <div style="width:100%;height:45px"></div>
                <div class="bao_pict"> <img src="{{URL::asset('/')}}/images/020700pc/success.png" alt=""></div>
                <div style="width:100%;height:13px"></div>
                <div class="con_buks">
                  <div class="title_time">
                       <p id="attend_title" style="font-weight:bold;margin: 0 0 0.5rem">全球敏捷运动峰会</p>
                       <p id="attend_time"  style="font-weight:bold;">2017-45-65 周五 9：00</p>
                   </div>
                   <div style="width:100%;height:26px"></div>
                   <div class="title_content">
                      <p id="attend_people"><samp style="font-weight:bold;">参会人姓名</samp><label style="font-weight: normal;">123456</label></p>
                      <p id="attend_phone"><samp  style="font-weight:bold;" >手机号</samp><label style="font-weight: normal;">hahahahh</label></p>
                      <p id="attend_way"><samp  style="font-weight:bold;">参会方式</samp><label style="font-weight: normal;">hahhaah</label></p>
                      <p id="attend_address"><label style="font-weight: normal;">hahhaah</label></p>
                   </div>
                </div>
                <div class="explain_buks"></div>
               
          </div>
     </div>
      <!-- banner部分-->
        <section id="bannercontent" style="width:100%;height:340px;background: #f2f2f2">
            <section style="width:1172px;margin:0 auto">
                 <div class="actpiccontent relative ">
             <!--  活动图片 -->
                  <div class="swiper-container actpicture absolute position1">
                      <div class="swiper-wrapper"></div>
                      <div class="swiper-pagination swiper-pagination-fraction"></div>
                  </div>
                  <span id="act_name"  class="absolute position2 p24 act_sub"></span>
                 <!--  时间地点席位数据 -->
                <span class="act_time_pict absolute position3"></span><p id="act_time" class="absolute p14">12/14 10:00</p>
                <p id="act_time_end" class="absolute p14">12/14 10:00</p>
                <span class="act_city_pict absolute position4"></span><p id="citys" class="absolute">北京、上海、杭州、温州</p>
                <span id="isLive" class=" absolute position5">*支持OVO直播服务</span>
                <span class="act_city_per absolute position6"></span><p id="limit_attent_per" class="absolute">限额500人</p>
                <span class=" absolute position7">*席位有限，请尽快报名</span>
                <!-- 报名按钮 -->
                <button id="woyaobaoming" class="absolute position8">我要报名</button>
                <button id="collect"  class="absolute position9">收藏192</button>
                <button id="fenxiang" class="absolute position10"></button>
                <!-- 浏览人数统计 -->
                <div id="pernumber" class="pernumber absolute"><span id="seen" class="color333">22次</span></div>
                 <div id="fen_pict_" class="fen_pict_ none absolute ">
                     <!--  <img  src="{{URL::asset('/')}}/images/020700pc/p12.png"> -->
                     <!--  <img  src="{{URL::asset('/')}}/images/020700pc/fenxiang.png"> -->
                 </div>
            </div>
            </section>
        </section>
        <section id="act_intro" class="mt0">
            <!-- 活动详情介绍 -->
            <div class="acttext" style="font-size:24px">活动详情</div>
            <div id="actbrand" class="relative">
           <!--  活动详情展示 -->
            <section class="bgwhite act_desp tline  absolute position20" id="actdescription"></section>
            <!-- 品牌展示 -->
            <div id="brand" class="absolute">
                <section id="pinpai" style="background: #f2f2f2;" class="brandcontain ">
                    <div class="brandtext f16 fline ">相关品牌</div>
                    <div id="no_data_" class="no_data_  none" style="width:10rem;height:10rem;margin: 4rem auto!important ">
                        <img src="{{URL::asset('/')}}/images/020502/no_message.png" style='width:10rem;height:10rem' alt="">
                    </div>
                </section> 
                <div id="add_" style="width: 96%;height:1060px;border-top: 1px solid #f2f2f2;margin:45px auto "><!-- 外层 -->
                  <div class="f16   location" >活动地点</div>
                  <section style='background-color: #f2f2f2;padding-left:1.33rem;' class="none " id="address" ></section>
                  <div class=" weixin ">
                       <img class="width10" src="{{URL::asset('/')}}/images/020700pc/p10.png" alt="">
                       <div class="width11">
                      <!--  <img class=" width11" src="{{URL::asset('/')}}/images/020700pc/p12.png" alt=""> -->
                       </div>
                  </div> 
                  <div class="f16   location" style="line-height:60px">最近参与</div>
                  <section class="enrollment none fline">
                    <div id="list"></div>      
                  <!--   <div class="more none">点击加载更多</div> -->
                    <div class="no_data  none"  style="width:10rem;height:10rem;margin: 4rem auto!important ">
                        <img src="{{URL::asset('/')}}/images/020502/no_message.png" style='width:10rem;height:10rem' alt="">
                    </div>
                  </section>
                  <div id="shengyuminge">仅剩230个名额请抓紧报名</div>
                </div><!-- 外层 -->
            </div> 
            </div> 
        
                
            </section>
        </section>      
         <!--  评论留言 -->
          <footer  style="margin-top:1450px;background:#f2f2f2; ">
            <article style="width:1170px;margin:0 auto">
             <div class="commentback" id="commentback" style="height:300px">
                <div style="height:20px"></div>
                <div  id="commentnumber"><b id="cmber" data-id=""></b></div>
                <div  id="commentnumber">您有任何想说的，想问的问题，在这里留言</div>
                <div class="textareacon">
                    <textarea class="f12" name="comment" id="comtextarea" cols="30" rows="10" style="resize: none;" placeholder="请输入想说……"></textarea>
                    
                </div>
            </div>
            <div style="width:100%;height:auto;text-align: right;margin-top: -20px;"><button style="padding: 1rem;border: medium none;border-radius: 0.5rem;
                  background-color: #ea5520; padding: 0.3rem 1.5rem; color: #fff; width: 155px;" class=" subcomment f16" id="subcomments">留言</button></div>
            <div id="liuyan_"  style="width:100%;height:20px;background:#f2f2f2"></div>
            <!--留言评论-->
            <section id="comment" class="mt1 pl1-33 bgwhite " style='padding:0 0 0 1.333rem;background: #f2f2f2;margin-top:20px; margin-bottom: 20px'>
                <div id="thelist" class="bgfont">
                    <ul class="pr1-33" id="allComment" style="margin-top:0;">

                    </ul>
                </div>
                <div id="pullUp" data-pagenow="1" style="display:none;">
                    <span class="pullUpLabel" style="display: none;"></span>
                </div>
            </section>
            <button id="getMore" class="getMore f12 c8a none">点击加载更多</button>
        </article>
      </footer>
      <div id="bottomblock" class="bottomblock">
          <div class="wisqpict">
              <img class=" width33" src="{{URL::asset('/')}}/images/020700pc/p12.png" alt="">
          </div>
          <div id="guanbi" style="width:50px;height:50px;position: absolute;bottom: 140px;right:20px; ">
          <center>
            <img style="width:31px;height:31px" src="{{URL::asset('/')}}/images/020700pc/p80.png" alt="">
          </center>
          </div>
      </div>
       <div class="tips" id="tip" style="right:305.5px;">
          <div class="sharebrand">
              <i style=""></i>
              <p>微信分享</p>
              <div class="tipload" >
                <div class="fem_xiang_pict"></div>
                <p style="padding: 0 0 0 0;margin: 0 0 0 0;">分享此活动到</p>
                <p style="padding: 0 0 0 0;margin: 0 0 0 0;">微信朋友圈</p>
              </div>
          </div>
          <div class="cuservice">
          </div>
          <div class="backtop" id="backtop" style="visibility:hidden;">
              <i></i>
              <p>回顶部</p>
          </div>
      </div>
    </section>
@stop
@section('endjs')
    <script src="{{URL::asset('/')}}/js/swiper.min.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/_v020700pc/pc.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/_v020700pc/address.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/_v020700pc/actlist.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/_v020700pc/jquery.min.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/_v020700pc/chosen.jquery.min.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/_v020700pc/area_chs.js"></script>

    <script type='text/javascript'>
    if($('#allComment>li').length==0){
      $('footer').css('margin-bottom','200px')
    }
    $(document).on('click','.container-list',function(){
    var args = getQueryStringArgs(),
        uid = args['uid'] || '0';
     var id=$(this).data('id');
    window.location.href = labUser.path + "webapp/activity/sharepc/_v020700?id="+id+"&uid="+ uid;

});  
  var isChrome = navigator.userAgent.toLowerCase().match(/chrome/) != null; if (isChrome) { 
     var Width=$(window).width();
   if(Width<1300){
    $('#doan_pict').css('right','10px')
   } 
   }
  // var Width=$(window).width();
  // alert(Width);
  // if(Width<1480){
  //   $('#doan_pict').css('right','0')
  // }
  // $('.laststep').change(function(){  
  // if($('.laststep').is(':visible')){
  //   $('#signname').css('overflow','auto')
  //   $('.laststep').css('overflow','auto')
  // }else{
  //   $('#signname').css('overflow','hidden')
  //   $('.laststep').css('overflow','auto')
  // } 
  // })
  $(document).on('click','#guanbi',function(){
       if($('#bottomblock').is(':visible')){
        $('#bottomblock').remove();
        $('footer').css('margin-bottom','0');
       } ;
      })

    //百度统计浏览量
     var _hmt = _hmt || [];
    (function() {
      var hm = document.createElement("script");
      hm.src = "https://hm.baidu.com/hm.js?8ccbe24f04075d8872631d15b0ec83fb";
      var s = document.getElementsByTagName("script")[0]; 
      s.parentNode.insertBefore(hm, s);
    })();
        //评论按钮颜色变化
            $('#comtextarea').on('keyup',function(){
                $('#subcomments').css('backgroundColor','#ea5520');
                if($('#comtextarea').val()==''){
                     $('#subcomments').css('backgroundColor','#999');
                }
            });
            $('#moreact').on('click',function(){
            $(this).css('color','#ea5520').siblings().css('color','#333');
            $('#activitylist').removeClass('none').addClass('a-fadeinT');
            $('#doan_pict').hide();
             if($('#activitylist').is(':hidden')){
              $('#bannercontent').css('display','block');
              $('#act_intro').css('display','block');
              $('footer').css('display','block');
              $('.bottomblock').css('display','block');
              $('#tip').css('display','block');
            }else{
              $('#bannercontent').css('display','none');
              $('#act_intro').css('display','none');
              $('footer').css('display','none');
              $('.bottomblock').css('display','none');
              $('#tip').css('display','none');
            }
            })
            
            $('#download').on('click',function(){
            $(this).css('color','#ea5520').siblings().css('color','#333');
            $('#activitylist').addClass('none');
            $('#doan_pict').show();
            if($('#activitylist').is(':hidden')){
              $('#bannercontent').css('display','block');
              $('#act_intro').css('display','block');
              $('footer').css('display','block');
              $('.bottomblock').css('display','block');
              $('#tip').css('display','block');
            }else{
              $('#bannercontent').css('display','none');
              $('#act_intro').css('display','none');
              $('footer').css('display','none');
              $('.bottomblock').css('display','none');
              $('#tip').css('display','none');
            }
            })
            //
            $(document).on('click','.bottomblock',function(){
              $('#activitylist').addClass('none');
               $('#moreact').css('color','#333');
               $('#download').css('color','#333');
               $('#doan_pict').hide();
            if($('#activitylist').is(':hidden')){
              $('#act_intro').css('display','block');
              $('footer').css('display','block');
              $('.bottomblock').css('display','block');
              $('#tip').css('display','block');
            }else{
              $('#act_intro').css('display','none');
              $('footer').css('display','none');
               $('.bottomblock').css('display','none');
              $('#tip').css('display','none');
            }
            })
            //刷新页面
            function reload() {
            location.reload();
             }
             // 点击我要报名
            $('#textsign span').on('click',function(){
                  reload();
                $('#signname').addClass('none');
                $('html').css('overflow','auto');
            });
        var args = getQueryStringArgs();
        var uid = args['uid'] || 0,
        id = args['id'];
        var urlPath = window.location.href;
        var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
        var storenum=0;
        // 发送手机验证码
       $('#mescode').on('click',function(){
          var phonenum=$("input[name='phonenumber']").val();
          if($('#jiaoyan').hasClass('none')){
              if(storenum>=3&&phonenum){
                 $('#jiaoyan').removeClass('none');
              }else{
                 getyanCode(); 
                 storenum++;
              }
          }else {  
              if($('input[name="pict_code"]').val() == $('#piccode').text()){
                  getyanCode(); 
              }else{
                alert('图形验证码不正确！')
              }  
          }
        });

      //图形验证码监听事件
      function checkPictCode(){
        var code=$('input[name="pict_code"]').val();
        var bijiao=$('#piccode').text();
        if(code!=bijiao){
          alert('图形验证码输入有误')
        }else{
           getyanCode();
        }
      }
      
      //监听图文验证码的输入；
     $('input[name="pict_code"]').keyup(function(){
      var piccode = $('#piccode').text();
      console.log(piccode);
       if($('input[name="pict_code"]').val() == piccode){
         checkPictCode(); 
       }else{
          return;
       }
     });
     $('input[name="pict_code"]').change(function(){
        var piccode = $('#piccode').text();
        if($('input[name="pict_code"]').val() == piccode){
            console.log(piccode);
       }else{
          alert('图形验证码不正确！')
       }
     });

     //获取短信验证码
      function getyanCode(){
         var param={};
         param["type"]='standard';
         param['username']=$('input[name="phonenumber"]').val();
         param['nickname']=$('input[name="nickname"]').val();
         var url=labUser.api_path+'/identify/sendcode';
        if($('input[name="phonenumber"]').val()){
          if($('input[name="phonenumber"]').val().length==10){
            // $('#jiaoyan').removeClass('none');
            ajaxRequest(param,url,function(data){
               if(data.status){
                 var getcode=$("#mescode");
                 time(getcode);                    
                }else{
                   alert(data.message);
                    return false;
                    };
                 });
               return;
               }else if($('input[name="phonenumber"]').val().length==11){
               var reg=/^\d{10,11}$/;
               if(reg.test($('input[name="phonenumber"]').val())){
               // $('#jiaoyan').removeClass('none');
               ajaxRequest(param,url,function(data){
               if(data.status) {
                 var getcode=$("#mescode");
                 time(getcode);                    
                }else{
                   alert(data.message);
                    return false;
                    };
                 });
               } else {alert('手机号码格式不对')} 
           }else if($('input[name="phonenumber"]').val().length!=10||$('input[name="phonenumber"]').val().length!=11){
            alert('号码格式不对')
           }
            }else{
          alert('手机号不能为空')
        }
      };
        //60秒短信计时；
        var tt;
        var wait = 60;
        var name=$('input[name="nickname"]').val();
        var phonenum=$("input[name='phonenumber']").val();
        var code=$('input[name="jiaoyan"]').val();
        var codeS=$('input[name="codeS"]').val();
        // var reg=/1[34578]\d{9}/;
        function time(o) {
        if (wait == 0) {
            o.removeAttr("disabled");
            o.html("重新发送");
            o.css({
              "font-size":"12px",
              "background":"#ea5520"
            });
            wait = 60;
               } else {
            o.attr("disabled", true);
            o.css({
              "font-size":"12px",
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
        // 点击获取验证码
      //点击刷新获取校验码
       function picidentifyCode(){
        var param={};
       var url=labUser.api_path+'/identify/captchaid';
        ajaxRequest(param,url,function(data){
            if(data.status){
               $("#piccode").attr("data-id",data.message.captcha_id);
             //   console.log(data.message.captcha_id);
                // console.log($("#piccode").attr("data-id"));
                 // picidentify($("#piccode").attr("data-id"));3
                 var code_id = $("#piccode").attr('data-id');
                 picidentify(code_id);
            };
        });

   };
  picidentifyCode();
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

    $('#piccode').click(function(){
       // picidentify($("#piccode").attr("data-id"));
      picidentifyCode();     
   });
   
   //提交进入下一场；
    $('#submitnext').on('click',function(){
      // var code=$('input[name="pict_code"]').val();
      // var bijiao=$('#piccode').text();
      var codeS=$('input[name="codeS"]').val();
      var Name=$('input[name="nickname"]').val();
      var phonenum=$('input[name="phonenumber"]').val();
      if(codeS&&Name){
         checkMesCode(codeS,phonenum,'standard');
         $('header').data('tel_id',phonenum); 
       
      } else {
          alert('信息请填写完整')
      }

    });
    //校验验证码函数；
     function checkMesCode(code,username,type){
        var param={};
        param["code"]=code;
        param["username"]=username;
        param["type"]=type;
        var url=labUser.api_path+'/identify/checkidentify';
        ajaxRequest(param,url,function(data){
            if(data.status){
                    $('input[name="realname"]').val('');
                    $('input[name="pict_code"]').val('');
                    $('input[name="codeS"]').val('');
                    $('input[name="phonenumber"]').val('');
                    $('.firststep').hide();
                    $('.secondstep').show();
              }else{
                  alert(data.message);            
              };
            });
           };
 
// })//最外层 
     function changestyle(){
        //切换报名顺序；
        $('.swith_icon').on('click',function(){
          $(this).find('img').attr('src','{{URL::asset('/')}}/images/020700pc/p94.png');
          if($('#pre_defalut').hasClass('none')){
             $('#pre_defalut').removeClass('none');
             $('#next_defalut').addClass('none').addClass('a-rotateinLT');
          }else{
              $('#pre_defalut').addClass('none').addClass('a-rotateinLT');
              $(this).find('img').attr('src','{{URL::asset('/')}}/images/020700pc/icon_bao.png');
              $('#next_defalut').removeClass('none');
          }
        })
      //分享按钮悬浮出现；
      $('#fenxiang').click(function(){
        if($('#fen_pict_').hasClass('none')){
          $('#fen_pict_').removeClass('none')
        }else{
          $('#fen_pict_').addClass('none')
        }
       })
      //切换按钮
      $('#doan_pict span').on('click',function(){
        $('#doan_pict').hide();
        $('#download').css('color','#333')
      })
      $('.sharebrand').hover(function(){
        $('.tipload').show()
      },function(){
        $('.tipload').hide()
      })
     
     }
     changestyle(); 
     //底部评论点赞或取消点赞
       function commentzan(id,uid,type){
          var param={};
              param['id']=id;
              param['uid']=uid;
              param['type']=type;
           var url=labUser.api_path+'/comment/zhan';
           ajaxRequest(param,url,function(data){
            if(data.status){
              if(param["type"] == 1){
                $('#sayzan').removeClass('dian_zanpict').addClass('dian_zan_yi_pict');
                alert(data.message)
              }else{
                  $('#sayzan').removeClass('dian_zan_yi_pict').addClass('dian_zanpict');
                  alert('取消点赞')
              }
            }
           })
       }
      //点赞事件
      $(document).on('click','#sayzan',function(){
         var type;
         var id=$(this).data('id');
         var args = getQueryStringArgs();
         var uid = args['uid'] || '0';
          if($(this).hasClass('dian_zanpict')){
            alert('请至APP上操作')
            // $(this).addClass('dian_zan_yi_pict').removeClass('dian_zanpict');
            //  var orign=$(this).prev().text();
            //  $(this).prev().text(orign-1+2);;
            // type=1;
          }else{
             // $(this).addClass('dian_zanpict').removeClass('dian_zan_yi_pict');
              alert('请至APP上操作')
             // var orign=$(this).prev().text();
             // $(this).prev().text(orign-1);
             // type=0;
          }
         commentzan(id,uid,type) 
      })
      $(document).on('click','.logo',function(){
       window.location.href="https://www.wujie.com.cn/index.html";
      })
        //提示框定位
    function positionTip(window_width,distance) {
        if(window_width>1332){
            $('#tip').css('right',distance+'px');
        }
        else{
            $('#tip').css('right','0px');
        }
        $(window).resize(function () {
            var windowWidth =  $(window).width();
            if(windowWidth < 1332){
                $('#tip').css('right','0px');
            }
            else{
                $('#tip').css('right',(windowWidth-1172)/2-60-20+'px');
            }
        });
        $(window).scroll(function () {
            var documentHeight = $(document).height(),
                    windowHeight = $(window).height(),
                    scrollBottom = $(window).scrollTop() + windowHeight;
            if (documentHeight <= scrollBottom) {
                $("#backtop").css('visibility','visible');
            }
            else{
                $("#backtop").css('visibility','hidden');
            }
        });
    }

    var window_width=$(window).width(),
        current_ID = 'ovofbh',
        aid = '',//省ID
        dis=(window_width-1172)/2-60-20;
    positionTip(window_width,dis);
//当点击跳转链接后，回到页面顶部位置
    $("#backtop").click(function () {
        //$('body,html').animate({scrollTop:0},1000);
        if ($('html').scrollTop()) {
            $('html').scrollTop(0);
            return false;
        }
        $('body').scrollTop(0);
        return false;
    });


    </script>
<script type="text/javascript">
var areaObj = [];
function initLocation(e){
  var a = 0;
  for (var m in e) {
    areaObj[a] = e[m];
    var b = 0;
    for (var n in e[m]) {
      areaObj[a][b++] = e[m][n];
    }
    a++;
  }
}
</script>

<script type="text/javascript" src="{{URL::asset('/')}}/js/_v020700pc/location_chs.js"></script>
<script type="text/javascript">
  var country = '';
  for (var a=0;a<=_areaList.length-1;a++) {
    var objContry = _areaList[a];
    country += '<option value="'+objContry+'" a="'+(a+1)+'">'+objContry+'</option>';
  }
  $("#country").html(country).chosen().change(function(){
    var a = $("#country").find("option[value='"+$("#country").val()+"']").attr("a");
    var _province = areaObj[a];
    var province = '';
    for (var b in _province) {
      var objProvince = _province[b];
      if (objProvince.n) {
        province += '<option value="'+objProvince.n+'" b="'+b+'">'+objProvince.n+'</option>';
      }
    }
    if (!province) {
      province = '<option value="0" b="0">------</option>';
    }
    $("#province").html(province).chosen().change(function(){
      var b = $("#province").find("option[value='"+$("#province").val()+"']").attr("b");
      var _city = areaObj[a][b];
      var city = '';
      for (var c in _city) {
        var objCity = _city[c];
        if (objCity.n) {
          city += '<option value="'+objCity.n+'">'+objCity.n+'</option>';
        }
      }
      if (!city) {
        var city = '<option value="0">------</option>';
      }
      $("#city").html(city).chosen().change();
      $(".dept_select").trigger("chosen:updated");
    });
    $("#province").change();
    $(".dept_select").trigger("chosen:updated");
  });
  $("#country").change();
  // $("button").click(function(){
  //   alert($("#country").val()+$("#province").val()+$("#city").val());
  // });
   function showdistrict(){
          $('#country').show();
          $('#province').show();
          $('#city').show()
    }
    showdistrict();

</script>

@stop