@extends('layouts.default')
<!-- Created by wangcx -->
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/customerdetail.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/agent/mybrand.css" rel="stylesheet" type="text/css"/>
    <style>
       .flex_bet p:first-child{
          flex-shrink: 0;
          margin-right:1rem;
       }
    </style>
@stop
@section('main')
    <section class="containerBox none bgcolor" id="containerBox">
      <!-- 头部 -->
        <div class='header '>
            <img src="" alt="" class="l avatar">
            <div class="white ml6-5">
              <div >
                <p class="mt05 mb05" >
                  <span class="f15 mr1 customerName b"></span>
                  <span class="mr05 f15 remarkName b"></span>
                  <span class="f12 mark customerLevel "></span>
                  <span class="r f12 mt05 " id='todetail' >详细资料<img src="/images/agent/todetail.png" alt="" class="todetail"></span>
                </p>
              </div>
              <div class="f12 "><span id="fromtype" class="mr05"></span><span class="customerTel"></span></div>
            </div>
            <div class="clearfix"></div>
        </div>
      <!-- 功能按键 -->
      <div class="fuc white">
        <button><a id="callphone"><img src="/images/agent/call.png"  style="vertical-align: text-bottom;">电话</a></button>
        <button><a id="sendmes"><img src="/images/agent/mes.png" class="v_baseline">短信</a></button>
        <button id='chatBtn'><img src="/images/agent/chat.png" class="v_sub" style="vertical-align: text-top;">聊天窗</button>
      </div>
      <!-- tab栏 -->
      <div class="tabs invite_tabs bgwhite tc">
        <div class="c2873ff">
          <p>跟进品牌</p>
          <p class="followNum"></p>
        </div>
        <div >
          <p>意向记录</p>
          <p class="recordNum"></p>
        </div>
        <div class="">
          <p>跟单备注</p>
          <p class="singular"></p>
        </div>
        <div class="tab">
          <p>活动邀请</p>
          <p class="eventNum"></p>
        </div>
        <div class="tab">
          <p>考察邀请</p>
          <p class="inspectionNum"></p>
        </div>
        <div class="tab">
          <p>付款协议</p>
          <p class="pactNum"></p>
        </div>
      </div>
      <div class="tabBox pb8">
        <!-- 跟进品牌 -->
          <div class="bgwhite brand_intro ">
            <!--  -->
              <div class="bottom_button flex_btn ">
                  <button class="white bg_blue   w10-5 creat_event">活动邀请</button>
                   <button class="white bg_blue  w10-5 creat_inspect">考察邀请</button>
                   <button class="white bg_blue   w10-5 creat_pact">付款协议</button>
              </div>     
          </div>
        <!-- 意向记录 -->
          <div class="intent_log none">
           <!--  <div class="pb1 bgwhite mb1-5 pl1-5">
              <div class="lh45 f15 fline">
                10月26日
              </div>
              <div class="borderblue-left mt1-5" >
                <div class="color999 bline f12 relative">
                  <p class="mb0 pb1-5">咨询品牌,并安排经纪人跟进</p>
                  <p class="point_p "><span class="point"></span></p>
                </div>
                <div class="pb1 pt1 pr1-5 bline flex_between">
                  <div class="flex_start">
                    <img src="" class="c_brandImg mr05"  >
                    <div>
                      <p class="f14 mb05">品牌名称</p>
                      <p class="color666 f12 mb05">特色餐饮</p>
                    </div>
                  </div>            
                  <img src="/images/agent/to.png" class="brandTo mt1-5" >
                </div>
                <div class="pb1 pt1 pr1-5 bline flex_between">
                  <div class="flex_start">
                    <img src="" class="c_brandImg mr05"  >
                    <div>
                      <p class="f14 mb05">品牌名称</p>
                      <p class="color666 f12 mb05">特色餐饮</p>
                    </div>
                  </div>            
                  <img src="/images/agent/to.png" class="brandTo mt1-5" >
                </div>

                <div class="color999 bline f12 mt1-5 pb1-5">收藏品牌 <p class="point_p"><span class="point"></span></p></div>
                <div class="pb1 pt1 pr1-5 bline flex_between">
                  <div class="flex_start">
                    <img src="" class="c_brandImg mr05"  >
                    <div>
                      <p class="f14 mb05">品牌名称</p>
                      <p class="color666 f12 mb05">特色餐饮</p>
                    </div>
                  </div>            
                  <img src="/images/agent/to.png" class="brandTo mt1-5" >
                </div>

                <div class="color999 bline pb1-5 mt1-5 f12">报名活动 <p class="point_p"><span class="point"></span></p></div> 
                <div class="pt1 pr1-5 flex_between">
                  <div class="flex_start">
                    <img src="" class="l activityImg mr05" >
                    <div >
                      <p class="f14 mb05">活动名称，最多两行名称</p>
                      <p class="color666 f12 mb05">开始时间：5月12是 11:30</p>
                    </div>
                  </div>               
                  <img src="/images/agent/to.png" class="brandTo" >
                </div>

              </div>
            </div> -->
            <div class="bottom_button flex_btn ">
                  <button class="white bg_blue   w10-5 creat_event">活动邀请</button>
                   <button class="white bg_blue  w10-5 creat_inspect">考察邀请</button>
                   <button class="white bg_blue   w10-5 creat_pact">付款协议</button>
            </div>
          </div>
        <!-- 跟单备注 -->
          <div class="remarks bgwhite none">
               
          </div>
        <!-- 活动邀请 -->
          <div class="activity bgwhite none">
              <nav class="flex_nav fline lh45 p0">
                <div class="blue">全部</div>
                <div>已确认</div>
                <div>待确认</div>
                <div>取消</div>
              </nav>
              <div class="act_status mt1  pl1-5 ">
                
              </div>
              <div class="bottom_button ">
                <button class="white bg_blue l w_100 creat_event">创建活动邀请</button>
              </div>
          </div>
        <!-- 考察邀请 -->
          <div class="inspect bgwhite none">
              <nav class="flex_nav fline lh45 p0">
                <div class="blue">全部</div>
                <div>已确认</div>
                <div>待确认</div>
                <div>取消</div>
              </nav>
              <div class="review_status pl1-5 ">
                
              </div>
              <div class="bottom_button ">
                <button class="white bg_blue l w_100 creat_inspect ">创建考察邀请</button>
              </div>
          </div>
        <!-- 付款协议 -->
          <div class="pact bgwhite none">
              <nav class="flex_nav fline lh45 p0">
                <div class="blue">全部</div>
                <div>已确认</div>
                <div>待确认</div>
                <div>取消</div>
              </nav>
              <div class="pact_status ">
                  
              </div>
              <div class="bottom_button ">
                <button class="white bg_blue l w_100 creat_pact ">创建付款协议</button>
              </div>
          </div>
       </div>
      </div>
  <!-- 编辑备注弹窗 -->
    <div class="fixbg none">
      
    </div>
  
     <div class="editpop p1-5 none a-fadeinB">
        <textarea  rows='6' class="note_remark" style="width:100%;border:none;padding:1rem;"></textarea>
        <div class="mb2 mt2 tc">
          <button class="sub_note">提交</button>
        </div>
     </div>
  <!-- 确认删除弹窗 -->
    <div class="delpop bgwhite none">
      <p class="f15 tc">确定删除该条备注？</p>
      <div>
        <button id="sure_del">确定</button><button id="cancel_del" style="border-right: none;border-bottom-right-radius: 0.6rem">取消</button>
      </div>   
    </div>
     <div class="common_pops none">    
     </div>
    </section>
    <section style="position: fixed;bottom: 0;background: #FFFFFF;height:17px" class="iphone_btn none"></section>
@stop
@section('endjs')
    <script type="text/javascript" src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
    <script type="text/javascript">
      Zepto(function(){
          iphonexBotton('.bottom_button');
         new FastClick(document.body);
         var args = getQueryStringArgs();
         var agent_id = args['agent_id'] || 0,
             customer_id = args['customer_id'] || 0;
        // tabs切换
            $('.tabs div').click(function(){
              var _this = $(this),tabbox = $('.tabBox');
              var _index = _this.index();
             _this.addClass('c2873ff').removeClass('tab');
             _this.siblings('div').removeClass('c2873ff').addClass('tab');
             tabbox.children('div').eq(_index).removeClass('none');
             tabbox.children('div').eq(_index).siblings('div').addClass('none');
            });
        
        // 跟进品牌
          function followBrand(agent_id,customer_id){
              var url = labUser.agent_path +'/customer/detail-brands/_v010300';
              ajaxRequest({'agent_id':agent_id,'customer_id':customer_id},url,function(data){
                  if(data.status){
                    var customer = data.message.customer||'';
                    var list = data.message.brand_list || [];
                    var remarkname = customer.remark ? '('+customer.remark+')' :'';
                    $('.avatar').attr('src',customer.avatar);
                    $('.customerName').text(customer.nickname);
                    $('.remarkName').text(remarkname);
                    $('.customerLevel').text(customer.level);
                    if(remarkname == ''){
                      $('.customerName').removeClass('mr1');
                    }
                    if(customer.is_invite == '1'){
                      $('#fromtype').text('邀请投资人');
                      
                    }else if(customer.is_invite == '2'){
                       $('#fromtype').text('派单投资人');
                      $('.tabs').removeClass('invite_tabs');
                      $('.recordNum').parent('div').remove();
                      $('.intent_log').remove();
                    }
                   
                    if(isAndroid){
                      $('.customerLevel').css('paddingTop','0.2rem');
                    }
                    $('.customerTel').text(customer.username);
                    $('.customerTel').attr('data-public',customer.is_public_tel);
                    if(customer.is_public_tel == 1){
                      $('#callphone').attr({'href':'tel:'+customer.username,'data-tel':customer.username}).css('color','#fff');
                      $('#sendmes').attr('href','sms:'+customer.username).css('color','#fff');
                    }else{
                       $('#callphone').attr('onclick','alertShow("该客户未公开手机号码，暂时不能拨打电话")');
                       $('#sendmes').attr('onclick','alertShow("该客户未公开手机号码，暂时不能发送短信")');
                    }
                    $('.followNum').text(list.length);
                                       
                    if(customer.is_invite == 1 && list.length == 0){
                        var invHtml = '<div  class="tc color666 is_invite pb3 pt3 f12"><p>您为邀请客户，如需形成跟进关系</p>';
                            invHtml += '<p>请发送您的代理品牌至用户</p><p>用户如感兴趣，点击意向按钮，自动形成促单关系</p></div>';
                            invHtml += '<div class="bottom_button"><button class="white bg_blue l w_100 send_brand ">发送品牌给客户</button></div>';
                            $('.brand_intro').html(invHtml);
                            iphonexBotton('.bottom_button');
                    }else{
                      if(list.length >0){
                        $.each(list,function(i,j){
                          var brandHtml = ''
                          brandHtml += ' <div class="brand fline" data-id="'+j.brand_id+'"><img src="'+j.logo+'" class="l logo"> <div class="mt05">';
                          brandHtml += ' <div><span class="f15 mr1">'+j.brand_title+'</span><span class="r f12 mt1 c2873ff" >跟进情况</span></div>';
                          brandHtml += '<div class="f11 color999"><span>'+unix_to_dateMD(j.success_time)+'</span>派单成功</div></div><div class="clearfix"></div>';
                          brandHtml += '</div><ul class="followlists showBrandLog followlists_'+i+'">';
                          brandHtml += '<li class="none"><p class="color999"><span class="imgbox"><img src="/images/agent/link.png" class="link_img "></span>与投资人形成代理关系</p></li>';
                          brandHtml += '<li class="none"><p class="color999"><span class="imgbox"><img src="/images/agent/right.png" class="right_img"></span>获得投资人电话及其他通讯方式</p></li>';
                          brandHtml += '<li><p class="color999"><span class="imgbox"><img src="/images/agent/error.png" class="error_img"></span>邀请用户参加OVO发布会</p></li>';
                          brandHtml += '<li><p class="color999"><span class="imgbox"><img src="/images/agent/error.png" class="error_img"></span>邀请用户总部或门店考察</p></li>';
                          brandHtml += '<li><p class="color999"><span class="imgbox"><img src="/images/agent/error.png" class="error_img"></span>签约付款协议，交付线上首付</p></li>';
                          brandHtml += '<li><p class="color999"><span class="imgbox"><img src="/images/agent/error.png" class="error_img"></span>签约付款协议，线下尾款补齐</p></li>';
                          brandHtml += ' </ul><div class="tc up mt-05"><p class="up_down "><img src="/images/agent/down.png" alt=""></p></div> <div style="height:1.2rem;background: #f2f2f2;"></div>';
                          $('.brand_intro').append(brandHtml);
                          var ul_obj = $('.followlists_'+i);
                          var eventArr = [];
                          if(j.event && j.event.length >0){
                               eventArr = unique(j.event);
                          }
                          console.log(eventArr);
                                                                                           
                          if(eventArr.length >0){
                              $.each(eventArr,function(index,item){
                                if(item.schedule == 1){
                                    ul_obj.children('li').eq(0).removeClass('none');                              
                                }else if(item.schedule == 2){
                                    ul_obj.children('li').eq(1).removeClass('none');
                                }else{
                                    ul_obj.children('li').eq(item.schedule-1).find('img').attr({'src':'/images/agent/right.png'}).addClass('right_img').removeClass('error_img');                                 
                                };
                                ul_obj.children('li').eq(item.schedule-1).children('p').removeClass('color999');
                                ul_obj.children('li').eq(item.schedule-1).append('<p class="color999 f11">'+unix_to_yeardate2(item.event_time)+'</p>');
                              }); 
                              
                          };
                         
                        });

                      }else{
                         $('.brand_intro').append('<div class="f12 color999 tc pt10">暂无跟进品牌</div>').removeClass('bgwhite');
                      }
                    }
                    $('#containerBox').removeClass('none');
                  };
              })
          };
          followBrand(agent_id,customer_id);  
        //意向加盟        
          function getIntentDetail(){
            var url = labUser.agent_path + '/customer/customer-intention-record/_v010300';
            ajaxRequest({'agent_id':agent_id,'customer_id':customer_id},url,function(data){
              if(data.status){
                  $('.recordNum').text(data.message.count || '0');
                  if(data.message.data_list.length >0){
                    var intentHtml = '';
                    
                    $.each(data.message.data_list,function(index,item){   
                      var paiHtml='',favHtml = '',actHtml='',subHtml = '', insHtml = '',conHtml='';
                      intentHtml += '<div class="pb1 bgwhite mb1-5 pl1-5"><div class="lh45 f15 fline  ml-1-5" style="padding-left:1.5rem;">'+getMD(item.created_at)+'</div>';
                      intentHtml += '<div class="borderblue-left ">';
                      for(var i = 0 ; i< item.data_list.length; i++){
                          
                          if(item.data_list[i].type == 'pai_brand'){
                            paiHtml += '<div class="pb1 pt1 pr1-5 bline flex_between toB" data-statu="'+item.data_list[i].brand_status+'" data-id="'+item.data_list[i].pai_brand_id+'"><div class="flex_start"><div><img src="'+item.data_list[i].pai_brand_logo+'" class="c_brandImg mr05"></div><div><p class="f14 mb05">'+cutString(item.data_list[i].pai_brand_name,15)+'</p><p class="color666 f12 mb05">'+item.data_list[i].pai_brand_cate+'</p></div></div><img src="/images/agent/to.png" class="brandTo "></div>';
                          };
                          if(item.data_list[i].type == 'favorite_brand'){
                            favHtml += '<div class="pb1 pt1 pr1-5 bline flex_between toB" data-statu="'+item.data_list[i].brand_status+'" data-id="'+item.data_list[i].favorite_brand_id+'"><div class="flex_start"><div><img src="'+item.data_list[i].favorite_brand_img+'" class="c_brandImg mr05"></div><div><p class="f14 mb05">'+cutString(item.data_list[i].favorite_brand_name,15)+'</p><p class="color666 f12 mb05">'+item.data_list[i].favorite_brand_cate+'</p></div></div><img src="/images/agent/to.png" class="brandTo " ></div>';
                          };
                          if(item.data_list[i].type == 'activity'){
                            actHtml += '<div class="pt1 pr1-5 pb1 bline flex_between toA" data-id="'+item.data_list[i].activity_id+'"><div class="flex_start"><div><img src="'+item.data_list[i].activity_img+'" class="l activityImg mr05"></div><div ><p class="f14 mb05">'+cutString(item.data_list[i].activity_name,12)+'</p><p class="color666 f12 mb05">活动时间：'+unix_to_mdhm(item.data_list[i].activity_time)+'</p></div></div><img src="/images/agent/to.png" class="brandTo" ></div>';
                          }
                          if(item.data_list[i].type == 'subscription'){
                            subHtml +='<div class="pt1 pb1 bline pr1-5 flex_between toL" data-id="'+item.data_list[i].live_id+'"><div class="flex_start"><div><img src="'+item.data_list[i].live_img+'" class="l activityImg mr05"></div><div ><p class="f14 mb05">'+cutString(item.data_list[i].live_title,12)+'</p><p class="color666 f12 mb05">开始时间：'+unix_to_mdhm(item.data_list[i].live_time)+'</p></div></div><img src="/images/agent/to.png" class="brandTo" ></div>';
                          }
                          if(item.data_list[i].type == 'inspect_invite'){
                            insHtml += '<div class="bline pb1"><div class="toB pt1 pr1-5  flex_between" data-statu="'+item.data_list[i].brand_status+'" data-id="'+item.data_list[i].brand_id+'"><div class="flex_start"><div><img src="'+item.data_list[i].brand_logo+'" class="c_brandImg mr05"></div><div><p class="f14 mb05">'+cutString(item.data_list[i].brand_name,15)+'</p><p class="color666 f12 mb05">'+item.data_list[i].brand_cate+'</p></div></div><img src="/images/agent/to.png" class="brandTo mt1-5" ></div><p class="color999 f12 mb0">考察时间安排：'+unix_YMD(item.data_list[i].inspect_time)+'</p></div>';
                          };
                          if(item.data_list[i].type == 'contract'){
                            conHtml +='<div class="bline pb1"><div class="toB pt1 pr1-5  flex_between" data-statu="'+item.data_list[i].brand_status+'" data-id="'+item.data_list[i].brand_id+'"><div class="flex_start"><div><img src="'+item.data_list[i].brand_logo+'" class="c_brandImg mr05"></div><div><p class="f14 mb05">'+cutString(item.data_list[i].brand_name,15)+'</p><p class="color666 f12 mb05">'+item.data_list[i].brand_cate+'</p></div></div><img src="/images/agent/to.png" class="brandTo mt1-5" ></div><p class="color999 f12 mb0">邀请经纪人：'+item.data_list[i].invite_person+'</p></div>';
                          };

                      }

                      if(paiHtml){
                          intentHtml += '<div class="color999 bline f12 mt1-5 pb1-5">咨询品牌,并安排经纪人跟进<p class="point_p"><span class="point"></span></p></div>'+paiHtml;
                      };
                      if(favHtml){
                          intentHtml += '<div class="color999 bline f12 mt1-5 pb1-5">收藏品牌 <p class="point_p"><span class="point"></span></p></div>'+favHtml;
                      };
                      if(actHtml){
                          intentHtml += '<div class="color999 bline pb1-5 mt1-5 f12">报名活动 <p class="point_p"><span class="point"></span></p></div>'+actHtml;
                      };
                      if(subHtml){
                          intentHtml += '<div class="color999 bline pb1-5 mt1-5 f12">订阅直播 <p class="point_p"><span class="point"></span></p></div>'+subHtml;
                      };
                      if(insHtml){
                          intentHtml += '<div class="color999 bline pb1-5 mt1-5 f12">接受品牌考察邀请 <p class="point_p"><span class="point"></span></p></div>'+insHtml;
                      };
                      if(conHtml){
                          intentHtml += '<div class="color999 bline pb1-5 mt1-5 f12">成功加盟品牌 <p class="point_p"><span class="point"></span></p></div>'+conHtml;
                      };

                      intentHtml +='</div></div>';

                    })// foreach end;
                    $('.intent_log').append(intentHtml);
                    var domArr = $('.borderblue-left');
                    for(var i=0;i<domArr.length;i++){
                      $(domArr[i]).find('.bline').last().removeClass('pb1 bline');

                    }
                    
                  }else{
                    $('.intent_log').append('<div class="tc f12  bgcolor color999 pt10">暂无备注信息，请添加</div>');
                  }

              }
            })
          };
          getIntentDetail(agent_id,customer_id);
        //跟单备注
          function getDocumentary(agent_id,customer_id){
            var url = labUser.agent_path +'/customer/detail-remarks/_v010300';
            ajaxRequest({'agent_id':agent_id,'customer_id':customer_id},url,function(data){
                if(data.status){
                    $('.singular').text(data.message.totals);
                    var list = data.message.list || [];
                    var remarkHtml ='';
                    if(list.length >0){
                        $.each(list,function(i,j){
                          $.each(j.remark_list,function(index,item){
                            if(item.content !=''){
                              remarkHtml += '<div class="remark fline relative" ><p class="f12 color666 w_92 ui-nowrap-multi">'+entitiestoUtf16(item.content)+'</p>';
                              remarkHtml += '<p class="f12">相关品牌：'+item.brand_title+'</p><p class="f12">客户等级：'+item.level_describe+'<span class="color999 r">'+unix_to_fulltime(item.created_at)+'</span></p>';
                              remarkHtml += '<img src="/images/agent/pull_down.png" alt="" class="showdetail">';
                              remarkHtml += '<div class="editbg none"><div class="pl1 pr1 tc editTip radius_03"><p class="fline ptb05 editdata" data-id="'+item.id+'" data-level="'+item.level+'">编辑</p><p class="delnote">删除</p></div></div></div>'; 
                            };
                                 
                          })
                      });

                    }else{
                      remarkHtml += '<div class="tc f12  bgcolor color999 pt10">暂无备注信息，请添加</div>'
                    };
                    remarkHtml +='<div class="bottom_button "><button class="white bg_blue l w_100 addremark ">添加备注</button></div>'
                    $('.remarks').html(remarkHtml);
                    iphonexBotton('.bottom_button');                 
                }
            })
          }
          getDocumentary(agent_id,customer_id);
      //活动邀请
          function getEvents(agent_id,customer_id,type){
            var url = labUser.agent_path +'/customer/activity-invite/_v010300';
            ajaxRequest({'agent_id':agent_id,'customer_id':customer_id,'type':type},url,function(data){
                if(data.status){
                    $('.eventNum').text(data.message.totals);
                    var list = data.message.activity_list || [];                 
                    var actHtml = '';
                    if(list.length >0){
                      $.each(list,function(i,j){
                        if(j.list){
                          $.each(j.list,function(index,item){
                            if(item.statusInfo.status == -1 || item.statusInfo.status == -2){
                              actHtml += '<div class="relative mb1-5 mt1 fline act_cancel"><p class="f14">'+item.title+'</p>';
                              actHtml += ' <p class="f12 color666">活动时间：'+unix_to_fulltime(item.begin_time)+'</p>';
                              actHtml += '<p class="f12 color666">邀请状态：<span class="cfd4d4d">已拒绝</span></p>';
                              if(item.statusInfo.status == -2){
                                actHtml += '<p class="f12 color666">拒绝理由：<span class="color333 b">邀请函已过期</span></p>';
                              }else{
                                actHtml += '<p class="f12 color666">拒绝理由：<span class="color333 b">'+item.statusInfo.remark+'</span></p>';
                              };                   
                              actHtml += '<p class="f12 color666">确认时间：'+unix_to_fulltime_s(item.confirm_time)+'</p></div>';
                            }else if(item.statusInfo.status == 0){
                              actHtml += '<div class="relative mb1-5 mt1 fline act_wait"> <p class="f14">'+item.title+'</p>';
                              actHtml += '<p class="f12 color666">活动时间：'+unix_to_fulltime(item.begin_time)+'</p>';
                              actHtml += '<p class="f12 color666">活动地点：'+item.cities+'</p>';
                              actHtml += '<p class="f12 color666">邀请状态：<span class="cffa300">待确认</span><span class="b color333">('+item.statusInfo.remark+')</span></p>';
                              actHtml += '<button class="act_sendAgain sendAgain" data-id="'+item.invite_id+'" data-title="'+item.title+'" data-src="'+item.list_img+'">再次发送</button></div>';
                            }else if(item.statusInfo.status == 1){
                              actHtml += '<div class="relative mb1-5 mt1 fline act_accept"><p class="f14">'+item.title+'</p>';
                              actHtml += '<p class="f12 color666">活动时间：'+unix_to_fulltime(item.begin_time)+'</p>';
                              actHtml += '<p class="f12 color666">邀请状态：<span class="c30be74">已接受</span></p>';
                              actHtml += '<p class="f12 color666">活动地点：'+item.cities+'</p>';
                              actHtml += '<p class="f12 color666">确认时间：'+unix_to_fulltime_s(item.confirm_time)+'</p></div>';
                            };
                          })
                        }
                      });
                      actHtml += '<div class="f12 color999 bgcolor tc pt10 none act_nodata pr1-5 l w100">暂无数据</div>';
                    }else{
                      actHtml += '<div class="f12 color999 bgcolor tc pt10 pr1-5 l w100">暂无数据</div>';
                    };    
                    $('.act_status').append(actHtml);
                };
            })
          };
          getEvents(agent_id,customer_id,2)

    //考察邀请
          function getInspects(agent_id,customer_id){
            var url = labUser.agent_path +'/customer/inspect-invite/_v010300';
            ajaxRequest({'agent_id':agent_id,'customer_id':customer_id},url,function(data){
                if(data.status){
                    $('.inspectionNum').text(data.message.totals);
                    var list = data.message.gather_inspect_list || [];
                    var inspectHtml = '';
                    if(list.length >0){
                        $.each(list,function(index,item){
                            if(item.inspect_list){
                              $.each(item.inspect_list,function(i,j){
                                if(j.status == -1 || j.status == -2){
                                    inspectHtml += '<div class="relative mb1-5 mt1 fline inspect_cancel"><p class="f14">考察品牌:'+j.brand_title+'</p>';
                                    inspectHtml += '<p class="f12 color666">考察时间：'+unix_YMD(j.time)+'</p>';
                                    inspectHtml += '<p class="f12 color666">考察门店：<span class="color333 b">'+j.store_name+'</span></p>';
                                    inspectHtml += '<p class="f12 color666">所在地区：'+j.head_address+'</p>';
                                    inspectHtml += '<p class="f12 color666">详细地址：'+j.inspect_address+'</p>';
                                    inspectHtml += '<p class="f12 color666">定金金额：'+j.currency+'元人民币整</p>';
                                    inspectHtml += '<p class="f12 color666">邀请状态：<span class="cfd4d4d">已拒绝</span></p>';
                                    if(j.status == -2){
                                      inspectHtml += '<p class="f12 color666">拒绝理由：<span class="color333 b">邀请函已过期</span></p>';
                                    }else{
                                      inspectHtml += '<p class="f12 color666">拒绝理由：<span class="color333 b">'+j.remark+'</span></p>';
                                    }
                                    inspectHtml += '<p class="f12 color666">确认时间：'+unix_to_fulltime_s(j.confirm_time)+'</p></div>';
                                }else if(j.status == 0){
                                    inspectHtml += '<div class="relative mb1-5 mt1 fline inspect_wait"><p class="f14">考察品牌:'+j.brand_title+'</p>';
                                    inspectHtml += '<p class="f12 color666">考察时间：'+unix_YMD(j.time)+'</p>';
                                    inspectHtml += '<p class="f12 color666">考察门店：'+j.store_name+'</p>';
                                    inspectHtml += '<p class="f12 color666">所在地区：'+j.head_address+'</p>';
                                    inspectHtml += '<p class="f12 color666">详细地址：'+j.inspect_address+'</p>';
                                    inspectHtml += '<p class="f12 color666">定金金额：'+j.currency+'元人民币整</p>';
                                    inspectHtml += '<p class="f12 color666">邀请状态：<span class="cffa300">待确认</span> <span class="color333 b">('+j.status_summary+')</span></p>';
                                    inspectHtml += '<button class="inspect_sendAgain sendAgain" data-id="'+j.id+'" data-store="'+j.store_name+'" data-brand="'+j.brand_title+'" data-src="'+j.image+'" data-date="'+j.time+'">再次发送</button></div>';
                                }else if(j.status == 1 || j.status == 2 || j.status == 3 || j.status == 4){
                                    inspectHtml += '<div class="relative mb1-5 mt1 fline inspect_accept"><p class="f14">考察品牌:'+j.brand_title+'</p>';
                                    inspectHtml += '<p class="f12 color666">考察时间：'+unix_YMD(j.time)+'</p>';
                                    inspectHtml += '<p class="f12 color666">考察门店：'+j.store_name+'</p>';
                                    inspectHtml += '<p class="f12 color666">所在地区：'+j.head_address+'</p>';
                                    inspectHtml += '<p class="f12 color666">详细地址：'+j.inspect_address+'</p>';
                                    inspectHtml += '<p class="f12 color666">定金金额：'+j.currency+'元人民币整</p>';
                                    inspectHtml += '<p class="f12 color666">支付方式：'+j.pay_way+'</p>';
                                    inspectHtml += ' <p class="f12 color666">邀请状态：<span class="c30be74">已接受</span></p>';
                                    inspectHtml += '<p class="f12 color666">确认时间：'+unix_to_fulltime_s(j.confirm_time)+'</p> </div>';
                                };
                              });
                            }
                        });
                        inspectHtml += '<div class="f12 color999 bgcolor tc pt10 none inspect_nodata pr1-5 l w100">暂无数据</div>';
                    }else{
                        inspectHtml +='<div class="f12 color999 bgcolor tc pt10  inspect_nodata pr1-5 l w100">暂无数据</div>'
                    }            
                    $('.review_status').html(inspectHtml);
                }
            });
          };
          getInspects(agent_id,customer_id);

    //合同加盟
          function getPactdetail(agent_id,customer_id){
              var url = labUser.agent_path + '/customer/contracts/_v010300';
              ajaxRequest({'agent_id':agent_id,'customer_id':customer_id},url,function(data){
                if(data.status){
                    $('.pactNum').text(data.message.totals);
                    var list = data.message.conreact || [];
                    var pactHtml = '';
                    if(list.length >0){
                      $.each(list,function(i,j){                    
                            if(j.status == -1 || j.status == -2){
                              pactHtml += '<div class="pact_cancel"><div class="pl1-5 "><div class="fline lh45 flex_bet f13 mb1-5">';
                              pactHtml += '<p class="no-wrap flex-1">'+j.nickname+'拒绝['+j.brand+']付款协议</p><p class="cfd4d4d pr1-5">加盟失败</p></div>';
                              pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>付款协议</p><p>'+j.contract_title+'</p></div>';
                              if(j.contract_no){
                                pactHtml += ' <div class="flex_bet pr1-5 color666 f12"><p>流水号</p><p>'+j.contract_no+'</p></div>';
                              };                           
                              pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>加盟品牌</p><p>'+j.brand+'</p></div>';
                              pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>加盟总费用</p> <p>￥'+j.amount+'</p></div>';
                              pactHtml += '<div class="pr1-5 mb1-5"><p class="color666 f12">合同文本</p><div class="toContract flex_bet align-c p1-5 bgf5 radius_03" data-id="'+j.id+'" data-url="'+j.address+'" >';
                              pactHtml += '<div><img src="/images/agent/pact.png" alt="" class="l" style="width:3.4rem;height:3.9rem;">';
                              pactHtml += '<div class="l ml05"><p class="f14 b w25 lh3-9 text-ellipsis mb0">'+j.brand+'加盟电子合同</p></div></div>';
                              pactHtml += '<img src="/images/agent/black_to.png" alt="" style="width: 0.7rem;height:1.3rem;""></div></div>';
                              pactHtml += '<div class="fline mb1" style="margin-top:-0.5rem;"></div>';
                              pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>拒绝理由</p><p>'+j.remark+'</p></div>';
                              pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>确定时间</p><p>'+unix_to_fulltime_hms(j.confirm_time)+'</p></div> </div><div class="bgf5 pt1-5"></div></div>';
                            }else if(j.status == 0 || j.status == 6){
                              pactHtml += '<div class="pact_wait"><div class="pl1-5"><div class="fline lh45 flex_bet f13 mb1-5">';
                              pactHtml += '<p class="no-wrap flex-1">等待'+j.nickname+'确定['+j.brand+']付款协议</p><p class="cffa300 pr1-5">等待中</p></div>';
                              pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>付款协议</p><p>'+j.contract_title+'</p></div>';
                              if(j.contract_no){
                                 pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>流水号</p><p>'+j.contract_no+'</p></div>';
                              }                         
                              pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>加盟品牌</p><p>'+j.brand+'</p></div>';
                              pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>加盟总费用</p><p>￥'+j.amount+'</p></div>'
                              //改15388 bug
                              pactHtml += '<div class="pr1-5 mb1-5"><p class="color666 f12">合同文本</p><div class="toContract flex_bet align-c p1-5 bgf5 radius_03" data-id="'+j.id+'" data-url="'+j.address+'">';
                              pactHtml += ' <div><img src="/images/agent/pact.png" alt="" class="l" style="width:3.4rem;height:3.9rem;">';
                              pactHtml += '<div class="l ml05"><p class="f14 b text-ellipsis w25 lh3-9 mb0">'+j.brand+'加盟电子合同</p><p class="f12 none"> 合同编号:'+j.contract_no+'</p></div></div><img src="/images/agent/black_to.png" alt="" style="width: 0.7rem;height:1.3rem;"></div></div>';
                              pactHtml += '<div class="mt1 mb1 fline"></div>';
                              if(j.status == 0){
                                pactHtml += '<div class="flex_bet pr1-5 color666 f12 align-c"><p>缴纳状态:<span class="cffa300">待确认 </span>('+j.leftover+')</p><p><button class="pact_sendagain" data-id="'+j.id+'" data-title="'+j.contract_title+'" data-brand="'+j.brand+'" data-amount="'+j.amount+'" data-type="'+j.type+'">再次发送</button></p></div></div><div class="bgf5 pt1-5"></div></div>';
                              }else{
                                pactHtml += '<div class="flex_bet pr1-5 color666 f12 align-c"><p>缴纳状态:<span class="cffa300"> 等待支付完成</p></div></div><div class="bgf5 pt1-5"></div></div>';
                              }
                              
                            }else if(j.status == 1 || j.status == 2 || j.status == 4 || j.status == 5){
                              pactHtml += '<div class="pact_accept"><div class="pl1-5"><div class="fline lh45 flex_bet f13 mb1-5">';
                              pactHtml += '<p class="no-wrap flex-1">'+j.nickname+'签订['+j.brand+']付款协议</p><p class="c30be74 pr1-5">支付成功</p></div>';
                              pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>付款协议</p><p>'+j.contract_title+'</p></div>';
                              if(j.contract_no){
                                pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>流水号</p><p>'+j.contract_no+'</p></div>';
                              }
                              pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>加盟品牌</p><p>'+j.brand+'</p></div>';
                              pactHtml += '<div class="pr1-5 mb1-5"><p class="color666 f12">合同文本</p><div class="toContract flex_bet align-c p1-5 bgf5 radius_03" data-id="'+j.id+'" data-url="'+j.address+'">';
                              pactHtml += ' <div><img src="/images/agent/pact.png" alt="" class="l" style="width:3.4rem;height:3.9rem;">';
                              pactHtml += '<div class="l ml05"><p class="f14 b text-ellipsis w25 lh3-9 mb0">'+j.brand+'加盟电子合同</p><p class="f12 none"> 合同编号:'+j.contract_no+'</p></div></div><img src="/images/agent/black_to.png" alt="" style="width: 0.7rem;height:1.3rem;"></div></div>';
                              pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>确定时间</p><p>'+unix_to_fulltime_hms(j.confirm_time)+'</p></div></div><div class="bgf5 pt1-5"></div></div>';
                            };
                          });
                          pactHtml += '<div class="f12 color999 bgcolor tc pt10 none pact_nodata ">暂无数据</div>'
                    }else{
                        pactHtml += '<div class="f12 color999 bgcolor tc pt10  pact_nodata ">暂无数据</div>'
                    }     
                    $('.pact_status').html(pactHtml);
                  }
              });
          };
          getPactdetail(agent_id,customer_id);


      //各项筛选
          function handleDom(type,sel){
            var selDom = $('.'+type).children('nav').children('div');
            $(selDom).click(function(){
                 var _index = $(this).index();
                      $(this).addClass('blue').siblings('div').removeClass('blue');
                      if(_index == 0){
                          $('.'+sel+'_accept').removeClass('none');
                          $('.'+sel+'_wait').removeClass('none');
                          $('.'+sel+'_cancel').removeClass('none');
                          if($('.'+sel+'_accept').length>0 || $('.'+sel+'_wait').length >0 || $('.'+sel+'_cancel').length >0){
                              $('.'+sel+'_nodata').addClass('none');
                          };
                      }else if(_index == 1){
                        if($('.'+sel+'_accept').length >0){
                            $('.'+sel+'_accept').removeClass('none');
                            $('.'+sel+'_nodata').addClass('none');
                        }else{
                            $('.'+sel+'_nodata').removeClass('none');
                        }
                          // $('.'+sel+'_accept').removeClass('none');
                          $('.'+sel+'_wait').addClass('none');
                          $('.'+sel+'_cancel').addClass('none');
                      }else if(_index == 2){
                        if($('.'+sel+'_wait').length >0){
                            $('.'+sel+'_wait').removeClass('none');
                            $('.'+sel+'_nodata').addClass('none');
                        }else{
                            $('.'+sel+'_nodata').removeClass('none');
                        }
                          $('.'+sel+'_accept').addClass('none');
                          // $('.'+sel+'_wait').removeClass('none');
                          $('.'+sel+'_cancel').addClass('none');
                      }else if(_index == 3){
                        if($('.'+sel+'_cancel').length >0){
                            $('.'+sel+'_cancel').removeClass('none');
                            $('.'+sel+'_nodata').addClass('none');
                        }else{
                            $('.'+sel+'_nodata').removeClass('none');
                        }
                          $('.'+sel+'_accept').addClass('none');
                          $('.'+sel+'_wait').addClass('none');
                          // $('.'+sel+'_cancel').removeClass('none');
                      };
            });
          };

          handleDom('activity','act');
          handleDom('inspect','inspect');
          handleDom('pact','pact');

  //详细资料
          $('#todetail').click(function(){
              window.location.href = labUser.path + 'webapp/agent/customer/data/_v010002?agent_id='+agent_id+'&customer_id='+customer_id;
          });
  //发送品牌给客户
          $(document).on('click','.send_brand',function(){
              var nickname = $('.customerName').text();
              agentBrand(customer_id,nickname);
          })
    //添加备注
        $(document).on('click','.addremark',function(){
            window.location.href = labUser.path + 'webapp/agent/remark/detail/_v010002?agent_id='+agent_id+'&customer_id='+customer_id+'&form_list=1';
        });
    //跟进情况
        $(document).on('click','.brand',function(){
            var id= $(this).attr('data-id');
            window.location.href = labUser.path + 'webapp/agent/tracklist/detail/_v010002?agent_id='+agent_id+'&customer_id='+customer_id+'&brand_id='+id;
        });

    //跟进品牌详情展开与收起
        $(document).on('click','.up',function(){
            $(this).toggleClass('down');
            $(this).siblings('.up').removeClass('down')
            $(this).prev('.followlists').toggleClass('showBrandLog');
            $(this).prev('.followlists').siblings('.followlists').addClass('showBrandLog');
        });
    //编辑跟单备注
        $(document).on('click','.editdata',function(){
            $('.fixbg').removeClass('none');
            $('.editpop').removeClass('none').addClass('a-fadeinB');
            var re_text = $(this).parents('.remark').children('.ui-nowrap-multi').text();
            var id = $(this).attr('data-id');
            var level_id = $(this).attr('data-level');
            $('.sub_note').attr('data-id',id);
            $('.sub_note').attr('data-level',level_id);
            $('.note_remark').val(re_text);
        });
        $(document).on('click','.fixbg',function(){
          $(this).addClass('none');
          $('.editpop').addClass('none');
          $('.delpop').addClass('none');
          $('.note_remark').val('');
          $('.sub_note').attr('data-id','');
          $('.sub_note').attr('data-level','');
        });
        // 提交备注
        $(document).on('click','.sub_note',function(){
          var param ={};                         
              param['id'] =$(this).attr('data-id');     
              param['level_id'] = $(this).attr('data-level');
              param['remark'] = utf16toEntities($('.note_remark').val());

          var url = labUser.agent_path + '/customer/edit-remark/_v010000';
            ajaxRequest(param,url,function(data){
              if(data.status){
                 $('.fixbg').addClass('none');
                 $('.editpop').addClass('none');
                 $('.sub_note').attr('data-id','');
                 $('.sub_note').attr('data-level','');
                 alertShow('编辑成功');
                 getDocumentary(agent_id,customer_id)
              }else{
                alertShow('编辑失败');
              }
            })
        });
        // 删除备注
        $(document).on('click','.delnote',function(e){
            var id= $(this).siblings('.editdata').attr('data-id');
            e.stopPropagation();
            $('.fixbg').removeClass('none');
            $('.delpop ').removeClass('none');
            $('#sure_del').attr('data-id',id);
        });
        $(document).on('click','#sure_del',function(){
            var id= $(this).attr('data-id');
            var url = labUser.agent_path + '/customer/delete-remark/_v010000';
            ajaxRequest({'id':id},url,function(data){
                if(data.status){
                    $('.fixbg').addClass('none');
                    $('.delpop').addClass('none');
                    alertShow('删除成功');
                    getDocumentary(agent_id,customer_id);
                }
            })
        });
        $(document).on('click','#cancel_del',function(){
            $('.fixbg').addClass('none');
            $('.delpop').addClass('none');
        });    
           

    //备注箭头
        $(document).on('click','.showdetail',function(){
            $(this).siblings('.editbg').toggleClass('none');
        });
        $(document).on('click','.editbg',function(){
          $(this).toggleClass('none');
        });

    //聊天窗
        $(document).on('click','#chatBtn',function(){
          var nickname = $('.customerName').text();
            goChat('C',customer_id,nickname);
        });
    //点击创建活动邀请
        $(document).on('click','.creat_event',function(){
            var avatar =$('.avatar').attr('src'),
                realname = $('.customerName').text();
                username = $('.customerTel').text();
            createInvitation('1',customer_id,avatar,realname,username);
        });
    //点击创建考察邀请
        $(document).on('click','.creat_inspect',function(){   
            var avatar =$('.avatar').attr('src'),
                realname = $('.customerName').text();
                username = $('.customerTel').text();  
            createInvitation('2',customer_id,avatar,realname,username);
        });
    //创建付款协议
        $(document).on('click','.creat_pact',function(){
          var  realname = $('.customerName').text();
            createContract(customer_id,realname);
        });

         //活动邀请 再次发送
      $(document).on('click','.act_sendAgain',function(){
        var _this = $(this);
        var ActivityID = _this.attr('data-id'),
            title = _this.attr('data-title'),
            imageURL = _this.attr('data-src');
            sendRichMsg('1','C',customer_id,ActivityID,title,imageURL,'','');
      });

     

      //考察邀请 再次发送
      $(document).on('click','.inspect_sendAgain',function(){
        var _this = $(this);
        var ins_id = _this.attr('data-id'),
            store_name= _this.attr('data-store'),
            brand_name = _this.attr('data-brand'),
            date = _this.attr('data-date')
            img = _this.attr('data-src');            
            sendRichMsg('2','C', customer_id,ins_id,brand_name,img,date,store_name);
      });

      

    //付款协议再次发送
      $(document).on('click','.pact_sendagain',function(){
         var _this =$(this);
         var contract_id = _this.attr('data-id'),
             title = _this.attr('data-title'),
             brand_name = _this.attr('data-brand'),
             amount = _this.attr('data-amount'),
             contract_joinType = _this.attr('data-type') == '2'? '品牌加盟' : '渠道加盟';
             sendRichMsg('3','C', customer_id,contract_id, title, '','','',amount,contract_joinType);
      });

    //查看合同详情
      $(document).on('click','.toContract',function(){
        var URL = $(this).attr('data-url');
            window.location.href = labUser.path +'js/agent/generic/web/viewer.html?file='+URL;
      });

    //了解尾款补齐
     $(document).on('click','.wkbq',function(){
            window.location.href = labUser.path +'webapp/agent/way/detail';
      });

     //跳转至品牌详情
     $(document).on('click','.toB',function(){
        var id = $(this).attr('data-id');
        var status = $(this).attr('data-statu');
        if(status == '正常'){
          window.location.href = labUser.path +'webapp/agent/brand/detail/_v010002?id='+id+'&agent_id='+agent_id;
        }else{
          alertShow("该品牌已下架");
        }
            
      });

     // 跳转活动详情
     $(document).on('click','.toA',function(){
        var id = $(this).attr('data-id');
          window.location.href = labUser.path +'webapp/agent/activity/detail?id='+id+'&agent_id='+agent_id;
      });
     // 跳转至直播详情
     $(document).on('click','.toL',function(){
        var id = $(this).attr('data-id');
            window.location.href = labUser.path +'webapp/agent/live/detail?id='+id+'&agent_id='+agent_id;
      });


  });
      function goChat(type,uid,name){
        if (isAndroid) {
            javascript:myObject.goChat(type,uid,name);
        } else if (isiOS) {
            var data = {
                "uid": uid,
                'type':type,
                'name':name
            }
            window.webkit.messageHandlers.goChat.postMessage(data);
        }
      }

      function createInvitation(type,uid,avatar,realname,username){
        if (isAndroid) {
            javascript:myObject.createInvitation(type,uid,avatar,realname,username);
        } else if (isiOS) {
            var data = {
                "uid": uid,
                'type':type,
                'avatar':avatar,
                'realname':realname,
                'username':username
            };
            window.webkit.messageHandlers.createInvitation.postMessage(data);
        }
      };
      
      function createContract(uid,realname){
        if (isAndroid) {
            javascript:myObject.createContract(uid,realname);
        } else if (isiOS) {
            var data = {
                "uid": uid,
                'realname':realname
            }
            window.webkit.messageHandlers.createContract.postMessage(data);
        }
      };

      function agentBrand(id,name){
        if (isAndroid) {
            javascript:myObject.agentBrand(id,name);
        } else if (isiOS) {
            var data = {
              'id':id,
              'name':name
            };
            window.webkit.messageHandlers.agentBrand.postMessage(data);
        }
      };

      function unix_to_dateMD(unix) {
          var newDate = new Date();
          newDate.setTime(unix * 1000);
          var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
          var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
          return M + '月' + D + '日';
      }
      function getMD(str){
          var arr = str.split('-');
          return arr[1]+'月' + arr[2] + '日';
      }
      function unix_to_fulltime_hms(unix) {
            var newDate = new Date();
            newDate.setTime(unix * 1000);
            var Y = newDate.getFullYear();
            var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
            var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
            var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
            var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
            var s = newDate.getSeconds() < 10 ? '0' + newDate.getSeconds() : newDate.getSeconds();
            return Y + '/' + M + '/' + D  + ' ' + h + ':' + m + ':' +s;
      };

      // 提示框
      function alertShow(content){
              $(".common_pops").text(content);
              $(".common_pops").css("display","block");
              setTimeout(function(){$(".common_pops").css("display","none")},2000);
      };
      //数组去重
      function unique(array){ 
            var r = []; 
            for(var i = 0, l = array.length; i < l; i++) { 
             for(var j = i + 1; j < l; j++) 
                if (array[i].schedule === array[j].schedule) j = ++i;            
                    r.push(array[i])    
            } 
             return r; 
      }  

      //16进制转为8进制
      function utf16toEntities(str) {
            var patt=/[\ud800-\udbff][\udc00-\udfff]/g;
            // 检测utf16字符正则
            str = str.replace(patt, function(char){
                var H, L, code;
                if (char.length===2) {
                    H = char.charCodeAt(0);
                    // 取出高位
                    L = char.charCodeAt(1);
                    // 取出低位
                    code = (H - 0xD800) * 0x400 + 0x10000 + L - 0xDC00;
                    // 转换算法
                    return "&#" + code + ";";
                } else {
                    return char;
                }
            });
            return str;
        }

      //8进制转为16进制  //表情转码
      function entitiestoUtf16(str){
            // 检测出形如&#12345;形式的字符串
            var strObj=utf16toEntities(str);
            var patt = /&#\d+;/g;
            var H,L,code;
            var arr = strObj.match(patt)||[];
            for (var i=0;i<arr.length;i++){
                code = arr[i];
                code=code.replace('&#','').replace(';','');
                // 高位
                H = Math.floor((code-0x10000) / 0x400)+0xD800;
                // 低位
                L = (code - 0x10000) % 0x400 + 0xDC00;
                code = "&#"+code+";";
                var s = String.fromCharCode(H,L);
                strObj.replace(code,s);
            }
            return strObj;
        }  

    </script>  
@stop