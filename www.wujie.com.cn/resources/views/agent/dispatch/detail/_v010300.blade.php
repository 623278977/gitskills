@extends('layouts.default')
<!-- Created by wangcx -->
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/dispatch.css" rel="stylesheet" type="text/css"/>
    <style>
       .flex_bet p:first-child{
          flex-shrink: 0;
          margin-right:1rem;
       }
    </style>
@stop
@section('main')
  <section class="containerBox bgcolor " style="min-height: 100%;" id="containerBox">
      <!-- 头部 -->
        <div class='pl1-5 pr1-5 bgwhite pt1 pb1 flex-start mb1'>
            <img src="" alt="" class="l avatar mr1">
            <div class="l">
              <div class="flex-center">             
                  <span class="f15 mr1 b realname"></span>         
                  <img src="" class="gender">             
              </div>
              <div class="f12 color666 userMes">
                
              </div>
            </div>        
        </div> 
        <div class="bgwhite pl1-5 pr1-5 pt1 mb1"> 
            <div class="pb05">
                <p class="flex-center"><span class="mr05 ver"><img src="/images/agent/honour.png" class="honour"></span><span class="f13 color666 brandName"></span><span class="f13 ml1 color999">今天/意向品牌</span></p>
                <p><span class="red-point mr1 ver-middle"></span><span class="yellow commission"></span><span class="f11 ml1 color999">最高提成比例</span></p>
                <p class="flex-center"><span class="yellow-point mr1 ver-middle"></span><span class="color666 f13 innerType"></span><span class="f11 ml1 color999">加盟类型</span></p>
                <p class="flex-center"><span class="green-point mr1 ver"></span><span class="color666 f13 difficulty"></span><span class="f11 ml1 color999">成单难度系数</span></p>
            </div>
           <!--  <div class="pt1 pb1">
                <span class="red-point mr1 ver-super"></span><span class="cffa300 f15">接此单，多赚500元</span><span class="color999 f11 ml1">额外加成</span>
            </div> -->
        </div>
        <div class="bgwhite pl1-5 pr1-5 pt1 pb1">
          <p class="flex-center"><span class="orange-point mr1"></span><span class="f11 color666">其他</span></p>
          <div class="ml1-5"> 
            <p class="f11 color999 mb0">OVO活动参与记录</p>
            <p class="f13 color666"><span class="signs"></span>场活动累计参与记录</p>
          </div>
          <div class="ml1-5">
            <p class="f11 color999 mb0">考察记录</p>
            <p class="f13 color666"><span class="invitations"></span>次品牌实体店铺考察记录</p>
          </div>
          
        </div> 
      <!-- 底部按钮 -->
        <div class="bottom-btn">
          <button class="uninterest">拒绝</button>
          <button class="tobeAgent">接入咨询</button>
        </div>
        <input id="savedata" type="hidden"></input>
  </section>
@stop
@section('endjs')
    <script>
      var $body = $('body');
      document.title = "任务详情";
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
        var args = getQueryStringArgs();
        var agent_id = args['agent_id'] || 0,
            id = args['id'];
        var url = labUser.agent_path+'/consult/detail/_v010300'
          ajaxRequest({"agent_id":agent_id,"id":id},url,function(data){
              if(data.status){
                var mes = data.message;
                var str='';
                $('#savedata').attr({'data-uid':mes.uid,"data-brand":mes.brand_id,"data-zone":mes.zone_name});
                // $('#savedata').attr('data-uid',mes.uid);
                $('.avatar').attr('src',mes.avatar);
                $('.realname').text(mes.realname);
                if(mes.gender == '0'){
                  $('.gender').attr('src','/images/agent/girl.png')
                }else if(mes.gender == '1'){
                  $('.gender').attr('src','/images/agent/boy.png')
                }else{
                  $('.gender').remove();
                };
                if(mes.birth){
                  str +='<span>'+mes.birth+'</span>';
                };
                if(mes.zone_name){
                  if(mes.birth){
                    str +=' · <span>'+mes.zone_name+'</span>';
                  }else{
                    str +='<span>'+mes.zone_name+'</span>';
                  }
                  
                }
                if(mes.be_industry){
                  if(mes.birth || mes.zone_name){
                      str +=' · <span>'+mes.be_industry+'</span>';
                  }else{
                    str +='<span>'+mes.be_industry+'</span>';
                  }
                  
                }
                
                $('.userMes').html(str);
                $('.brandName').text(mes.brand_name +(mes.slogan ? (' - '+mes.slogan) : ''));
                $('.commission').text(mes.commission);
                $('.innerType').text(mes.brand_agency_way);
                $('.difficulty').text(mes.difficulty);
                $('.signs').text(mes.signs);
                $('.invitations').text(mes.invitations);
    
              }
          })
          // 拒绝
          $(document).on('click','.uninterest',function(){
            var uid= $('#savedata').attr('data-uid'),
                brand_id =$('#savedata').attr('data-brand');
            refuseConsult(id,uid,brand_id);
          });

          // 接受
          $(document).on('click','.tobeAgent',function(){
            var uid= $('#savedata').attr('data-uid'),
                brand_id =$('#savedata').attr('data-brand'),
                realname = $('.realname').text(),
                zone = $('#savedata').attr('data-zone'),
                brand_name = $('.brandName').text();
            acceptConsult(id,uid,brand_id,realname,zone,brand_name);
          });
      })

     
       // 提示框
          function alertShow(content){
              $(".common_pops").text(content);
              $(".common_pops").css("display","block");
              setTimeout(function(){$(".common_pops").css("display","none")},2000);
         };
        // 拒绝按钮
         function refuseConsult(id,uid,brand_id){
            if (isAndroid) {
              javascript:myObject.refuseConsult(id,uid,brand_id);
            } 
            else if (isiOS) {
                var data = {
                   "id":id,
                   "uid":uid,
                   "brand_id":brand_id
                }
                window.webkit.messageHandlers.refuseConsult.postMessage(data);
            }
        }

      // 接入按钮
        function acceptConsult(id,uid,brand_id,realname,zone,brand_name){
          if (isAndroid) {
              javascript:myObject.acceptConsult(id,uid,brand_id,realname,zone,brand_name);
            } 
            else if (isiOS) {
                var data = {
                   "id":id,
                   "uid":uid,
                   "brand_id":brand_id,
                   "realname":realname,
                   "zone":zone,
                   "brand_name":brand_name
                }
                window.webkit.messageHandlers.acceptConsult.postMessage(data);
            }
        }
    
    </script>  
@stop