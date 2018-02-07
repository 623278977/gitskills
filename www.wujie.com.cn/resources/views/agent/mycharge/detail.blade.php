@extends('layouts.default')
<!-- Created by Wangcx -->
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/mycharges.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section class="containerBox none bgcolor" id="containerBox" >
        <div class="mycharge pl1-5 pr1-5 pb1">
            <p class="f12 b pt0-8" >可提现余额（元）</p>
            <p class="f24 b" id="currency"></p>
            <p class="f10 fline cue">*按实际到账情况计算</p>
            <p class=" pt0-8 cash">
              <span class="f10">
                  <em >本季结算中（元）</em>
                  <em id="frozen_currency"></em>
              </span>
              <span class="f10">
                  <em >累计提现（元）</em>
                  <em id="total_currency"></em>
              </span>
            </p>
            <a href='/webapp/agent/tutorial/firstlevel?id=0&type=1' class="ques">常见问题</a>
        </div>
        <div class="bgwhite mt1-2">
              <div class="lh45 b f16 fline introTitle">
                <span>季度业绩说明</span>
                <div class="changeQuarter r">
                    <p class="selected pl1"></p>
                    <ul class="quarters pl1 pr1 none">
                    </ul>
                </div>  
              </div>
              <div class="pl1-5 pr1-5 has_charges">
                <div class="fline ">
                    <div class="pt2-5 pb2-5 f12 lh2 l w60">
                      <p >当前业绩总和（单）<span id="total_order"></span></p>
                      <p><span class="yellowcircle"></span>我的 <span id="my_order"></span></p>
                      <p><span class="bluecircle"></span>下线成员 <span id="mydown_order"></span></p>
                    </div>
                    <div class="r tr w40">
                      <div class="chart-container r" style="position: relative; height:11rem; width:11rem">
                          <canvas id="mychart" ></canvas>
                      </div>
                    </div>
                    <div class="clearfix"> </div>
                </div>
                <div class="pt1-5 pb1-5 color666 f13 lh2">
                  <p id="intro"></p>
                </div>
              </div>
              <div class="no_charges">
                
              </div> 
        </div>
        <div class="bgwhite mt1-2 pb8">
          <div class="lh45 b f16 fline introTitle">账户明细（元）</div>
          <ul class="countdetail pl1-5 ">
            <!-- <li class="fline">
              <div class="tl">
              <p class="f16">成单提成</p>
              <p class="f12 color999">2017/10/012 10:00:00</p>
              </div>
              <div class="tr flex-right mt05">
                <div>
                  <p class="f12">+2000</p>
                  <p class="f10 ffa300">处理中</p>
                </div>
                <div class="imgTo">
                  <img src="/images/agent/to.png" alt="" >
                </div>
              </div> 
            </li> -->
            
          </ul>
          <div class="tc color666 pt2 pb2 moredetail f12">加载更多</div>
        </div>
        <div>
          <button class="withdraw">提现</button>
        </div>
    </section>
@stop
@section('endjs')
    <script type='text/javascript' src='{{URL::asset('/')}}/js/agent/dist/Chart.bundle.min.js'></script>
    <script>
      var $body = $('body');
      document.title = "我的佣金";
      // hack在微信等webview中无法修改document.title的情况
      var $iframe = $('<iframe ></iframe>').on('load', function() {
      setTimeout(function() {
      $iframe.off('load').remove()
      }, 0)
      }).appendTo($body);
  </script> 
    <script type='text/javascript'>
        new FastClick(document.body);
        $('.withdraw').click(function(){
          withdraw();
        });
        $('body').addClass('bgcolor');
        function withdraw(){
          if (isAndroid) {
                javascript:myObject.withdraw();
            } else if (isiOS) {
                var data = {};
                window.webkit.messageHandlers.withdraw.postMessage(data);
            }
        };
       Zepto(function(){
          var args = getQueryStringArgs();
          var agent_id = args['agent_id'] || 0,
              page = 1,
              page_size = 5;
            
            function myCharge(agent_id,page,page_size,quater){
                var url = labUser.agent_path + '/user/my-commission/_v010002';
                var param = {};
                  param['agent_id'] = agent_id;
                  param['page'] = page;
                  param['page_size'] =page_size;
                  param['quarter_chioces'] = quater;
                ajaxRequest(param,url,function(data){
                  if(data.status){
                      var obj = data.message;
                      $('#currency').text(obj.currency);
                      if(obj.currency <= 0){
                        $('.withdraw').css('backgroundColor','#aaa').attr('disabled','true');
                      }
                      $('#frozen_currency').text(obj.frozen_currency);
                      $('#total_currency').text(obj.total_currency);
                      
                      var qua = obj.quarter_chioces[0];
                        $('.selected').text(qua);
                      var quaHtml = '';
                      if(obj.quarter_chioces.length >0){
                          $.each(obj.quarter_chioces,function(i,j){
                            quaHtml += '<li>'+j+'</li>';
                          })
                      };                   
                      $('.quarters').append(quaHtml);
                      var moreHtml = detailDom(obj);                 
                      $('.countdetail').html(moreHtml);
                      if(obj.detail.length < page_size){
                        $('.moredetail').addClass('none');
                      };
                      $('.containerBox').removeClass('none');
                      recordIntro(obj,qua);
                  }else{
                    $('#containerBox').html('<div class="pt3 f14 color999 tc">'+data.message+'</div>').removeClass('none');
                    // $('#mychart').remove();
                  }
                })
            };

          //加载更多
          function getMore(agent_id,page,page_size,quarter){
              var url = labUser.agent_path + '/user/my-commission/_v010002';
              var param = {};
                  param['agent_id'] = agent_id;
                  param['page'] = page;
                  param['page_size'] =page_size;
                  param['quarter_chioces'] = quarter;
              ajaxRequest(param,url,function(data){
                  if(data.status){
                     var moreHtml = detailDom(data.message);               
                     $('.countdetail').append(moreHtml);
                     if(data.message.detail.length<page_size){
                        $('.moredetail').text('没有更多了...');
                     }
                  }
              })
          };

        //账户明细的DOM
         function detailDom(obj){
              var moreHtml = '';
              if(obj.detail.length > 0){
                 $.each(obj.detail,function(i,j){
                  if(j.title.remark == '处理中'){
                      moreHtml += '<li class="fline" data-id="'+j.id+'" data-type="'+j.type+'" data-log="'+j.log_id+'"><div class="tl"> <p class="f16">'+j.title.title+'</p>';
                      moreHtml += ' <p class="f12 color999">'+j.created_at.replace(/-/g,'/')+'</p></div> <div class="tr flex-right mt05 pr1-5">';
                    moreHtml += ' <div><p class="f12">'+j.num+'</p><p class="f10 ffa300">'+j.title.remark+'</p></div>';
                  }else{
                      moreHtml += '<li class="fline" data-id="'+j.id+'" data-type="'+j.type+'" data-log="'+j.log_id+'"><div class="tl"> <p class="f16">'+j.title.title+j.title.remark+'</p>';
                      moreHtml += ' <p class="f12 color999">'+j.created_at.replace(/-/g,'/')+'</p></div> <div class="tr flex-right mt05 pr1-5">';
                      moreHtml += ' <div><p class="f12">'+j.num+'</p></div>';
                  }
                    moreHtml += '<div class="imgTo"><img src="/images/agent/to.png" alt="" ></div></div></li>';
                });
              };
              return moreHtml;
                // $('.countdetail').append(moreHtml);
         };

      //业绩说明
       function recordIntro(obj,qua){
          $('#total_order').text(obj.total_orders);
          $('#my_order').text(obj.my_orders);
          $('#mydown_order').text(obj.my_subordinate_orders);
          var config = {
                  type: 'doughnut',
                  data: {
                      datasets: [{
                          data: [ 
                              obj.my_subordinate_orders,
                              obj.my_orders
                          ],
                          backgroundColor: [
                              'rgb(40,115,225)',
                              'rgb(255,163,0)'    
                          ]                        
                      }],
                      labels: [
                          "下线成员",
                          "我的"
                      ]                             
                  },
                  options: {
                      // responsive: true,
                      cutoutPercentage:50,
                      legend: {
                          display:false
                      },
                      animation: {
                          animateScale: true,
                          animateRotate: true
                      }
                  }
              };
              var ctx = document.getElementById("mychart").getContext("2d");
              window.myDoughnut = new Chart(ctx, config);
              var introHtml = '';
                  introHtml += '根据梯度说明，当前处在梯度<span class="ffa300">'+obj.level+'</span>，<span class="ffa300">'+qua+'</span>，我的团队共获得佣金奖励<span class="ffa300">￥'+obj.total_commission+'</span> 整。其中，我获得<span class="ffa300">￥'+obj.my_commission+'</span> 整，我的下属成员获得<span class="ffa300">￥'+obj.my_subordinate_commission+'</span> 整。';  
              $('#intro').html(introHtml);
       }

           //初始加载
           myCharge(agent_id,page,page_size);

           $(document).on('click','.moredetail',function(){
              page++;
              var quarter = $('.selected').text();
              getMore(agent_id,page,page_size,quarter);
           });

           $(document).on('click','.quarters li',function(e){
              var oldele = $('.selected').text();
              // var e = e || window.event
              window.event? window.event.cancelBubble = true : e.stopPropagation();
              $('.selected').text($(this).text());
              $('.quarters').toggleClass('none');
              var newele = $('.selected').text();
              if(oldele != newele){
                  page =1;
                  var params = {};                
                  params['agent_id'] = agent_id;
                  params['page'] =page;
                  params['page_size'] =page_size;
                  params['quarter_chioces'] =newele;
                  var url = labUser.agent_path +'/user/my-commission/_v010002';
                  ajaxRequest(params,url,function(data){
                    if(data.status){
                      $('.has_charges').removeClass('none');
                      $('.no_charges').addClass('none');
                      var moreHtml = detailDom(data.message);
                          recordIntro(data.message,newele);
                      $('.countdetail').html(moreHtml);
                    }else{
                      $('.has_charges').addClass('none');
                       $('.no_charges').html('<div class="pt3 f14 color999 tc pb3">'+data.message+'</div>').removeClass('none');
                    }             
                  })   
              }
  
           });

           $(document).on('click','.selected',function(){
              $('.quarters').toggleClass('none');
           });

           $(document).on('click','.countdetail li',function(){
              var id= $(this).attr('data-id');
              var type= $(this).attr('data-type');
              var log_id = $(this).attr('data-log');
              window.location.href = labUser.path + 'webapp/agent/mycharge/datas?id='+id+'&type='+type+'&log_id='+log_id;
           });
     
       });
       
    </script> 
@stop