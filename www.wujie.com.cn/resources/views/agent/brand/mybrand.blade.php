@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/mybrand.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section class="containerBox bgcolor" id="containerBox" >
       <div class="agentnav">
            <span class="agentBlue " id="alAgent" >已代理(0)</span>
            <span id="isAgent" >申请中(0)</span>
       </div>
       <div class="already">
           <!-- <div class="plr1-1 bgwhite pt1-5 mb1-33"> -->
                <!-- <div class="brandInfo  fline">
                    <div class="l brandImg ml0-4">
                        <img src="" alt="" >
                    </div>
                   <div class="l mr1-2">
                       <p class="f14">品牌标题</p>
                       <p class="color999">solgan </p>
                       <p class="color666 f12">行业分类：<span class="color333">鲜果饮品</span></p>
                       <p class="color666 f12">启动资金：<span class="fd4d4d">5-10（万）</span></p>
                   </div>
                   <div class="imgTo r">
                       <img src="{{URL::asset('/')}}/images/agent/to.png" alt="" class="to">
                   </div>
                   <div class="r mt1-5">
                       <p class="f18 fd4d4d tr">￥8K</p>
                       <p class="f10 tr">成单最高提成</p>
                   </div>
                   <div class="clearfix"></div>
               </div>
               <div class="brandAgents" id="brandAgent"> -->
               <!-- 成单量 -->
                 <!--   <div class="fline">
                       <p class="mt1-5">
                           <span class="ml0-4 sucIcon">成</span>该品牌的成单量
                       </p>
                       <p class="deals color666 f12">
                         <span class="pl3-2 ">我的成单量 <em>2</em></span>
                         <span >我的下线经纪人成单量<em>3</em></span>
                       </p>
                   </div> -->
                <!-- 客户 -->
                   <!-- <div class="fline">
                       <p class=" pt1-5 mycustomer f14 b">我的客户×<span>细查</span></p>
                       <p class="docCustomer color666 f12">
                            <span class="pl3-2 ">累计跟单客户<em>2</em></span>
                            <span>当前跟单客户<em style="margin-right: 7rem;">2</em></span>
                        </p>
                   </div> -->
                <!-- 事件点 -->
                  <!--  <div>
                       <p class="pt1-5 eventPoint f14 b">事件点</p>
                       <ul class="events pl3-2 pr0-4 mb1-5">
                           <li>
                             <span class="color666"><em class="point"></em>获得品牌代理</span>
                             <span class="color999">2016/07/26</span>
                           </li>
                           <li>
                             <span class="color666"><em class="point"><i class="verLine"></i></em>获得第一笔派单</span>
                             <span class="color999">2016/07/26</span>
                           </li>
                           <li>
                             <span><em class="point"><i class="verLine"></i></em>获得第一笔提成</span>
                             <span>2016/07/26</span>
                           </li>
                       </ul>
                   </div> -->
                   
              <!--  </div>
               <div class="tc up">
                    <p class="up_down"><img src="/images/agent/up.png" alt=""></p>
               </div>
           </div> -->
        
       </div>
       <div class="applying none">
            <!-- <div class="plr1-1 bgwhite pt1-5 mb1-33">
               <div class="brandInfo  fline">
                        <div class="l brandImg ml0-4">
                            <img src="" alt="" >
                        </div>
                       <div class="l mr1-2">
                           <p class="f14">品牌标题</p>
                           <p class="color999">solgan </p>
                           <p class="color666 f12">行业分类：<span class="color333">鲜果饮品</span></p>
                           <p class="color666 f12">启动资金：<span class="fd4d4d">5-10（万）</span></p>
                       </div>
                       <div class="imgTo r">
                           <img src="{{URL::asset('/')}}/images/agent/to.png" alt="" class="to">
                       </div>
                       <div class="r mt3">
                           <p class="f12 fd4d4d tr ffa300">5</p>
                       </div>
                       <div class="clearfix"></div>
                </div>
                
            </div > -->
       </div>
       
       
      
    </section>
@stop
@section('endjs')
    <script>
       Zepto(function(){
            new FastClick(document.body);
        // 详情展示
            $(document).on('click','.up',function(){
                var brandAgents = $(this).siblings('.brandAgents');
                brandAgents.toggleClass('showBrand');
                $(this).siblings('.brandInfo').toggleClass('fline');
                $(this).toggleClass('down');
                var this_bg = $(this).parent('div').siblings('.bgwhite');
                this_bg.children('.brandAgents').addClass('showBrand');
                this_bg.children('.brandInfo').removeClass('fline');
                this_bg.children('.up').removeClass('down');
            });
        // tab切换
            $(document).on('click','.agentnav span' , function(){
                var i = $(this).index();
                $(this).addClass('agentBlue').siblings('span').removeClass('agentBlue');
               if(i == 0){
                    $('.already').removeClass('none');
                    $('.applying').addClass('none');
               }else if(i == 1){
                    $('.already').addClass('none');
                    $('.applying').removeClass('none');
               }
            });

         var args = getQueryStringArgs();
         var agent_id = args['agent_id'] || 0;
         var _index = args['index'] || 0;
         if(_index == 2){
            $('.agentnav span')[1].click();
         }
         var al_url =labUser.agent_path + '/user/agent-brands/_v010000';
         var is_url = labUser.agent_path + '/user/apply-brands/_v010000';
             ajaxRequest({'agent_id':agent_id},al_url,function(data){
                if(data.status){
                    var brands = data.message.brands || [];
                    $('#alAgent').text('已代理 ('+data.message.agent_brands+')');
                    // $('#isAgent').text('申请中 ('+data.message.agent_brands+')');
                    agentBrandDetail(brands);
                }
             });
            ajaxRequest({'agent_id':agent_id},is_url,function(data){
              if(data.status){
                  var a_brand = data.message.brands || [];
                  $('#isAgent').text('申请中 ('+data.message.apply_brands+')');
                  applyBrandDetail(a_brand);
              }
            });
        // 已代理
            function agentBrandDetail(obj){
                var brandHtml = '';
                if(obj.length >0){
                     $.each(obj,function(i,j){
                      brandHtml += '<div class="plr1-1 bgwhite pt1-5 mb1-33"><div class="brandInfo" data-id='+j.id+'> <div class="l brandImg mr1 ml0-4">';
                      brandHtml += '<img src="'+j.logo+'" alt="" ></div><div class="l mr1-2 width48"><p class="f14 b">'+j.title+'</p>';
                      brandHtml += '<p class="color999">'+j.slogan+'</p><p class="color666 f12">行业分类 <span class="color333 b">'+j.category_name+'</span></p>';
                      brandHtml += '<p class="color666 f12">启动资金 <span class="fd4d4d">'+j.investment_min+'-'+j.investment_max+' (万)</span></p></div>';
                      brandHtml += '<div class="imgTo r"><img src="{{URL::asset('/')}}/images/agent/to.png" alt="" class="to"></div>';
                      brandHtml += '<div class="r mt1-5" style="width:15%;"><p class="f18 fd4d4d tr">'+j.max_percent+'</p><p class="f10 tr">佣金最高提成比例</p></div>';
                      brandHtml += '<div class="clearfix"></div></div>';
                      brandHtml += '<div class="brandAgents showBrand"><div class="fline"><p class="mt1-5"><span class="ml0-4 sucIcon">成</span><span class="f14 b ml1">该品牌成单量</span></p>';
                      brandHtml += '<p class="deals color666 f12"><span class="pl3-2 ">我的成单量 <em>'+j.my_own_orders+'</em></span><span >我的下线经纪人成单量<em>'+j.my_subordinate_orders+'</em></span></p></div>';
                      brandHtml += '<div class="fline"><p class=" pt1-5 mycustomer f14 b">我的客户×<span>'+j.title+'</span></p>';
                      brandHtml += '<p class="docCustomer color666 f12"><span class="pl3-2 ">累计跟单客户<em>'+j.total_customers+'</em></span><span>当前跟单客户<em style="margin-right: 7rem;">'+j.now_customers+'</em></span></p></div>';
                      // brandHtml += '<div><p class="pt1-5 eventPoint f14 b">事件点</p><ul class="events pl3-2 pr0-4 mb1-5">';
                  if(j.events.length > 0){
                      brandHtml += '<div><p class="pt1-5 eventPoint f14 b">事件点</p><ul class="events pl3-2 pr0-4 mb1-5">';
                      var eventHtml = '';
                      // var eventType = '';
                      $.each(j.events,function(index,item){
                        var summary = item.summary;
                          if(item.type == 2){
                            if(item.zone_name){
                              summary = item.summary+' : '+item.name + ' ('+item.zone_name+')';
                            }else{
                              summary = item.summary + ':' +item.name;
                            }
                          }
                          if(index == 0){
                            eventHtml += '<li><span class="color666"><em class="point"></em>'+summary+'</span><span class="color999">'+unix_to_yeardate2(item.time)+'</span></li>';
                          }else{
                            eventHtml += '<li><span class="color666"><em class="point"><i class="verLine"></i></em>'+summary+'</span><span class="color999">'+unix_to_yeardate2(item.time)+'</span></li>';
                          }
                          
                      });
                      brandHtml += eventHtml+'</ul></div></div>';
                  }else{
                      brandHtml += '</div>';
                  } 
                      brandHtml += '<div class="tc up"><p class="up_down"><img src="/images/agent/down.png" alt=""></p></div> </div>';    
                  });
                }else{
                  brandHtml += '<div class="tc"><img src="/images/agent/defind_brand.png" style="width: 16.4rem;margin-top: 8rem;"></div>';
                }
               
                $('.already').html(brandHtml);
             };
          // 申请中
             function applyBrandDetail(obj){
              if(obj.length >0){
                  var applyHtml = '<div class="plr1-1 bgwhite pt1-5 mb1-33">';
                  $.each(obj,function(i,j){
                      applyHtml += '<div class="brandInfo  fline" data-id="'+j.id+'"><div class="l brandImg mr1 ml0-4">';
                      applyHtml += '<img src="'+j.logo+'" alt="" ></div><div class="l mr1-2 width60"><p class="f14">'+j.title+'</p>';
                      applyHtml += '<p class="color999">'+j.slogan+'</p><p class="color666 f12">行业分类：<span class="color333">'+j.category_name+'</span></p>';
                      applyHtml += '<p class="color666 f12">启动资金：<span class="fd4d4d">'+j.investment_min+'-'+j.investment_max+' (万)</span></p></div>';
                      applyHtml += '<div class="imgTo r"><img src="{{URL::asset('/')}}/images/agent/to.png" alt="" class="to"></div>';
                      applyHtml += '<div class="r mt3"><p class="f12 fd4d4d tr ffa300">'+j.unread_num+'</p></div><div class="clearfix"></div></div>';
                  });
                  applyHtml += '</div>';
              }else{
                applyHtml = '<div class="tc"><img src="/images/agent/defind_brand.png" style="width: 16.4rem;margin-top: 8rem;"></div>'
              };              
              $('.applying').html(applyHtml);
             };

             $(document).on('click','.brandInfo',function(){
                var id = $(this).attr('data-id');
                var position_id ;
                if($('.already').hasClass('none')){
                  position_id = '10';
                }else{
                   position_id = '9';
                }
                onAgentEvent('brand_detail','',{'type':'brand','id':id,'userId':agent_id,'position':position_id});
                window.location.href = labUser.path + 'webapp/agent/brand/detail?id='+id+'&agent_id='+agent_id;
             })

       });
    </script>
    
    
@stop