@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/myteam.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/agent/mycharges.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section class="containerBox none bgcolor" id="containerBox" style="min-height: 100%">
      <div class="myteamnav">
            <span class="agentBlue " id="downAgent" >团队成员</span>
            <span id="" >团队业绩</span>
      </div>
      <div class="downline">
        <div class="f12 pl1-5 pt1 pb1 color666">成功发展团队<span id="linernum"> </span>人</div>
        <div class="pl1-5 pb5 bgwhite">
           <ul class="downliners" id="downliners">
             <!-- <li class="fline">
               <div class="linersName ">
                 <img src="/images/agent/boy.png" alt="" class="l mr1 ">
                 <div class="l">
                   <p><span class="f16 mr1 v_align_m">King</span><span class="getV mr05"><img src="/images/agent/v.png" alt="">已认证</span><span  class="f12">初级经纪人</span></p>
                   <p class="gender"><img src="/images/agent/girl.png" alt=""></p>
                 </div>
               </div>
               <div class="r lh2-2 pr1-5 tr">
                 <p class="f12 color666">山海 浦东</p>
                 <p class="f11 color999">2017/01/01 加入</p>
               </div>
             </li> -->
           </ul>
        </div>
        <div>
           <button class="developliners" onclick='devDownline()'>发展团队</button>
        </div>
      </div>
      <div class="teamClan none mt1-2">
        <!-- 本季度成单量 -->
          <div class="bgwhite">
            <div class="lh45 b f16 fline introTitle">
              <span>本季度成单量</span>
            </div>
            <div class="pl1-5 ">
              <div class="fline pr1-5">
                  <div class="pt2-5 pb2-5 f12 lh2 l w60">
                    <p >团队总成绩 <span id="quarter_orders" class="f15 b"></span></p>
                    <p><span class="yellowcircle"></span><span class="ownname"></span> <span id="qua_ownorders" class="f15 b"></span></p>
                    <p><span class="bluecircle"></span>团队成员 <span id="qua_downorders" class="f15 b"></span></p>
                  </div>
                  <div class="r tr w40 pr1-5">
                    <div class="chart-container r" style="position: relative; height:11rem; width:11rem">
                        <canvas id="currentchart" ></canvas>
                    </div>
                  </div>
                  <div class="clearfix"> </div>
              </div>
              <div class="pt1 pb1 tc">
                <button class="viewDetail" id="quarterDetail">查看具体明细</button>
              </div>
            </div> 
            <div class="quaShow none">
              <ul class="color666 f13 pl1-5 pr1-5 none" id='re_quarter'>  
              </ul>
              <div class="pt1 pb1 f12 tc quaMore" >
                加载更多
              </div>
            </div>
            
            
          </div>
          
          <!-- 总成单明细 -->
          <div class="bgwhite">
            <div class="lh45 b f16 fline introTitle mt1-2">
              <span>总成单明细</span>
            </div>
            <div class="pl1-5 ">
              <div class="fline pr1-5">
                  <div class="pt2-5 pb2-5 f12 lh2 l w60">
                    <p >团队总成绩 <span id="total_orders" class="f15 b"></span></p>
                    <p><span class="yellowcircle"></span><span class="ownname"></span> <span id="total_ownorders" class="f15 b"></span></p>
                    <p><span class="bluecircle"></span>团队成员 <span id="total_downorders" class="f15 b"></span></p>
                  </div>
                  <div class="r tr w40 pr1-5">
                    <div class="chart-container r" style="position: relative; height:11rem; width:11rem">
                        <canvas id="allchart" ></canvas>
                    </div>
                  </div>
                  <div class="clearfix"> </div>
              </div>
              <div class="pt1 pb1 tc">
                <button class="viewDetail" id="totalDetail">查看具体明细</button>
              </div>
            </div>
            <div class="totalShow none">
              <ul class="color666 f13 pl1-5 pr1-5 none" id='re_total'>
              </ul>
              <div class="pt1 pb1 f12 tc totalMore">
                加载更多
              </div>
            </div> 
            
          </div>
      </div>
      <!-- <a href="javascript:void(0)" >这里测试合同文本，不用管</a> -->
    </section>
@stop
@section('endjs')
    <script type='text/javascript' src='{{URL::asset('/')}}/js/agent/dist/Chart.bundle.min.js'></script>
    <script>
       Zepto(function(){
            new FastClick(document.body);
            // tab切换
            $(document).on('click','.myteamnav span' , function(){
                var i = $(this).index();
                $(this).addClass('agentBlue').siblings('span').removeClass('agentBlue');
               if(i == 0){
                    $('.downline').removeClass('none');
                    $('.teamClan').addClass('none');
               }else if(i == 1){
                    $('.downline').addClass('none');
                    $('.teamClan').removeClass('none');
                    drawCanvas(ownname,qua_own_per,qua_down_per,total_own_per,total_down_per);
               }
            });

            var args = getQueryStringArgs();
            var agent_id = args['agent_id'] || 0;
            var index = args['index'] || 0;
            var pageSize = 2;
            var totalPage = 1;
            var quarterPage = 1;
            var qua_own_per,total_own_per,qua_down_per,total_down_per,ownname;
            
           
        // 下线经纪人
            function getDownliner(id){
              var url =labUser.agent_path + '/user/subordinate/_v010005';
                  ajaxRequest({'agent_id':id},url,function(data){
                      if(data.status){
                          var linerHtml = '';
                          $('#linernum').text(' '+data.message.count+' ');
                          $('#downAgent').attr('data-id',data.message.superior);
                          if(data.message.list.length > 0){
                              $.each(data.message.list,function(i,j){
                                  linerHtml +='<li class="fline" data-id="'+j.id+'"><div class="linersName"><img src="'+j.avatar+'" alt="" class="l mr1 ">';                  
                                  if(j.realname){
                                    linerHtml += '<div class="l"><p><span class="f15 mr1 v_align_s">'+cutString(j.realname,5)+'</span>';
                                    linerHtml += '<span class="getV mr05"><img src="/images/agent/v.png" alt="">已认证</span>';
                                  }else{
                                    linerHtml += '<div class="l"><p><span class="f15 mr1 v_align_s">'+cutString(j.nickname,5)+'</span>';
                                  }
                                  linerHtml += '<span  class="f11">'+j.level+'</span></p>';                            
                                  if(j.gender == 0){
                                      linerHtml += '<p class="gender"><img src="/images/agent/girl.png" alt=""></p></div>';
                                  }else if(j.gender == 1){
                                      linerHtml += '<p class="gender"><img src="/images/agent/boy.png" alt=""></p></div>';
                                  }else{
                                    linerHtml += '</div>';
                                  } 
                                  linerHtml +='<div class="clearfix"></div><div class="color666 f11 tl mt05">发展团队：'+j.downlines+'人 &nbsp;&nbsp;团队总人数：'+j.teams+'人</div></div>'      
                                  linerHtml += '<div class="lh2-2 pr1-5 tr "><p class="f12 color666 l2-2"> '+j.city+'</p>';
                                  linerHtml += '<p class="f11 color999 l2-2">'+unix_to_yeardate(j.created_at)+'加入</p></div></li>'
                              });
                              $('#downliners').html(linerHtml);  
                              
                          }else{
                              // $('#downliners').html('暂无信息').css({'backgroundColor':'#f2f2f2','text-align':'center'});   
                              $('.downline').addClass('tc').html('<img  src="/images/agent/noteamer.png" style="width:18.5rem;height:14.9rem;margin:auto;margin-top:30%;"><div><button class="developliners" onclick="devDownline()">发展团队</button></div>'); 
                          } ;
                           mySuperior();
                          $('.containerBox').removeClass('none');
                      }
                  })
            };

      //跳转经纪人详情
          $(document).on('click','#downliners li',function(){
            var customer_id = $(this).attr('data-id');
            window.location.href = labUser.path + 'webapp/agent/details/detail?customer_id='+customer_id+'&agent_id='+agent_id;
          })

          // 团队业绩
            function getTeamClan(id){
              var url = labUser.agent_path + '/user/team-sales/_v010005';
                  ajaxRequest({'agent_id':id},url,function(data){
                      if(data.status){
                          $('#quarter_orders').text(data.message.quarter_orders);
                          $('#qua_ownorders').text(data.message.my_quarter_orders);
                          $('#qua_downorders').text(data.message.subordinate_quarter_orders);
                          $('.ownname').text(data.message.nickname);
                          $('#total_orders').text(data.message.total_orders);
                          $('#total_ownorders').text(data.message.my_orders);
                          $('#total_downorders').text(data.message.subordinate_orders);
                        // 计算百分比
                        if(data.message.quarter_orders != '0'){
                          qua_own_per = parseInt(data.message.my_quarter_orders/data.message.quarter_orders*100);
                          qua_down_per = 100- qua_own_per;
                        }else{
                          qua_own_per = 0;
                          qua_down_per = 0;
                        };
                        console.log(data.message.total_orders);
                        if(data.message.total_orders != '0'){     
                          total_own_per = parseInt(data.message.my_orders/data.message.total_orders*100);
                          total_down_per = 100 -total_own_per;
                        }else{
                          total_own_per = 0;
                          total_down_per = 0;
                        }                 
                        ownname= data.message.nickname;
                        drawCanvas(ownname,qua_own_per,qua_down_per,total_own_per,total_down_per);

                      }
                  })
            };
          // 团队业绩详情
            function getorderDetail(id,type,page,pageSize){
                var url = labUser.agent_path + '/user/sales-detail/_v010005';
                var param = {};
                    param['agent_id'] = id;
                    param['type'] = type;
                    param['page'] = page;
                    param['page_size'] =pageSize;
                    ajaxRequest(param,url,function(data){
                        if(data.status){
                            var clanHtml = '';
                            if(data.message.length>0){
                                $.each(data.message,function(i,j){
                                    clanHtml += '<li class=" fline clanDetail"><p>投资人：'+j.nickname+'</p>';
                                    clanHtml += '<p>品牌：'+j.brand_name+'</p><p>加盟套餐：'+j.pagcakge_fee+'人民币</p><p>加盟时间：'+unix_to_yeardate(j.created_at)+'</p><p>对接经纪人：'+j.agent+'</p></li>';
                                });
                            };
                          if(type == 'quarter'){
                              $('#re_quarter').append(clanHtml);
                              // $('.quaShow').removeClass('none');
                              if(data.message.length <2){
                                  $('.quaMore').text('没有更多了...');
                              } 

                          }else if(type == 'all'){
                              $('#re_total').append(clanHtml);
                              // $('.totalShow').removeClass('none');
                              if(data.message.length <2){
                                 $('.totalMore').text('没有更多了...');
                              }
                          };
                        }else{
                          if(type == 'quarter'){
                            $('#quarterDetail').parent('div').addClass('none').siblings('div').removeClass('fline');
                          }else if(type == 'all'){
                            $('#totalDetail').parent('div').addClass('none').siblings('div').removeClass('fline');
                          }
                        }
                    });
            };

            getDownliner(agent_id);
            getTeamClan(agent_id);
            getorderDetail(agent_id,'quarter',quarterPage,pageSize)
            getorderDetail(agent_id,'all',totalPage,pageSize)
      // 查看详情
            $('#quarterDetail').click(function(){
              if($(this).text() == '查看具体明细'){
                   // getorderDetail(agent_id,'quarter',page,pageSize);
                   $('.totalShow').addClass('none');
                   $('#totalDetail').text('查看具体明细');
                   $('#re_quarter').removeClass('none');
                   $('.quaShow').removeClass('none');
                   $(this).text('收起');   
              }else if($(this).text() == '收起'){
                  quarterPage = 1;
                  $('.quaMore').text('加载更多');
                  $(this).text('查看具体明细');
                  $('.quaShow').addClass('none');
                  $('#re_quarter').children().slice(2).remove();
              }; 
            });

            $('#totalDetail').click(function(){
               if($(this).text() == '查看具体明细'){
                   // getorderDetail(agent_id,'all',page,pageSize);
                   $('.quaShow').addClass('none');
                   $('#quarterDetail').text('查看具体明细');
                   $('#re_total').removeClass('none');
                   $('.totalShow').removeClass('none');
                   $(this).text('收起');
                   
              }else if($(this).text() == '收起'){
                  totalPage = 1
                  $('.totalMore').text('加载更多');
                  $(this).text('查看具体明细');
                  $('.totalShow').addClass('none');
                  $('#re_total').children().slice(2).remove();
                   
              }; 
            })
            $('.quaMore').click(function(){
                if($(this).text() == '没有更多了...'){
                    return;
                }else{
                  quarterPage ++;
                  getorderDetail(agent_id,'quarter',quarterPage,pageSize);
                }      
            })
            $('.totalMore').click(function(){
                if($(this).text() == '没有更多了...'){
                    return;
                }else{
                  totalPage ++;
                  getorderDetail(agent_id,'all',totalPage,pageSize);
                }
                
            })

      
            function drawCanvas(ownname,q_own,q_down,t_own,t_down) {
                var cur_config = {
                  type: 'doughnut',
                  data: {
                      datasets: [{
                          data: [
                              q_own,
                              q_down
                          ],
                          backgroundColor: [
                              'rgb(255,163,0)', 
                              'rgb(40,115,225)',   
                          ]                        
                      }],
                      labels: [
                          ownname,
                          "团队成员"
                      ]
                      
                  },
                  options: {
                      responsive: true,
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
              var all_config = {
                  type: 'doughnut',
                  data: {
                      datasets: [{
                          data: [
                              t_own,
                              t_down
                          ],
                          backgroundColor: [
                              'rgb(255,163,0)', 
                              'rgb(40,115,225)'   
                          ]
                          
                      }],
                      labels: [
                          ownname,
                          "团队成员"
                      ]    
                  },
                  options: {
                      responsive: true,
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
             
                var currentctx = document.getElementById("currentchart").getContext("2d");
                var allctx = document.getElementById('allchart').getContext('2d');
                if(q_own == 0 && q_down == 0){
                  cur_config.data.datasets[0].data = ['1'];
                  cur_config.data.datasets[0].backgroundColor = ['rgb(170,170,170)'];
                  cur_config.data.labels = ['无成单'];
                }
                if(t_own == 0 && t_down == 0){
                  all_config.data.datasets[0].data = ['1'];
                  all_config.data.datasets[0].backgroundColor = ['rgb(170,170,170)'];
                  all_config.data.labels = ['无成单'];
                }
                window.myDoughnut_1 = new Chart(currentctx, cur_config);  
                window.myDoughnut_2 = new Chart(allctx,all_config)
      
            };
           
            if(index == 2){
                $('.myteamnav span').eq(1).click();
            }else if(index == 1){
              $('.myteamnav span').eq(0).click();
            };
            

        //我的上级
          function mySuperior(){
            var id=$('#downAgent').attr('data-id');
            if (isAndroid) {
                  javascript:myObject.mySuperior(id);
              } else if (isiOS) {
                  var data = {
                      'id':id
                  }
                  window.webkit.messageHandlers.mySuperior.postMessage(data);
              }
          };
         
         
       });
    </script>  
   <script type='text/javascript' >
    
        //发展下线经纪人
        //developliners
        function devDownline(){
          if (isAndroid) {
                javascript:myObject.devDownline();
            } else if (isiOS) {
                var data = {};
                window.webkit.messageHandlers.devDownline.postMessage(data);
            }
        };
    </script> 
@stop