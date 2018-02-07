@extends('layouts.default')
<!-- Created by wangcx -->
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/customerdetail.css" rel="stylesheet" type="text/css"/>
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
                <p class="mt05" >
                  <span class="f15 mr1 customerName b"></span>
                  <span class="mr05 f15 remarkName b"></span>
                  <span class="f12 mark customerLevel "></span>
                  <span class="r f12 mt05 " id='todetail' >详细资料<img src="/images/agent/todetail.png" alt="" class="todetail"></span>
                </p>
              </div>
              <div class="f12 customerTel"></div>
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
      <div class="tabs bgwhite tc">
        <div class="c2873ff">
          <p>跟进品牌</p>
          <p class="followNum"></p>
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
            <!-- <div class="brand fline">
                <img src="" alt="" class="l logo">
                <div class="mt05">
                  <div>
                      <span class="f15 mr1">品牌名称</span>
                      <span class="r f12 mt1 c2873ff">跟进情况</span>
                  </div>
                  <div class="f11 color999"><span>7月12日</span>派单成功</div>
                </div>
                <div class="clearfix"></div>
            </div>
              <ul class="followlists">
                <li>
                  <p>
                    <span class="imgbox"><img src="/images/agent/link.png" class="link_img"></span>与投资人形成代理关系
                  </p>
                  <p class="color999 f11">2017/02/02</p>
                </li>
                <li>
                  <p>
                    <span class="imgbox"><img src="/images/agent/right.png" class="right_img"></span>获得投资人电话及其他通讯方式
                  </p>
                  <p class="color999 f11">2017/02/02</p>
                </li>
                <li>
                  <p>
                    <span class="imgbox"><img src="/images/agent/error.png" class="error_img"></span>邀请用户参加OVO发布会
                  </p>
                  <p class="color999 f11">2017/02/02</p>
                </li>
                <li>
                  <p>
                    <span class="imgbox"><img src="/images/agent/error.png" class="error_img"></span>邀请用户总部或门店考察
                  </p>
                  <p class="color999 f11">2017/02/02</p>
                </li>
                <li>
                  <p>
                    <span class="imgbox"><img src="/images/agent/error.png" class="error_img"></span>签约付款协议，交付线上首付
                  </p>
                  <p class="color999 f11">2017/02/02</p>
                </li>
                <li>
                  <p>
                    <span class="imgbox"><img src="/images/agent/error.png" class="error_img"></span>签约付款协议，线下尾款补齐
                  </p>
                  <p class="color999 f11">2017/02/02</p>
                </li>
              </ul>
              <div style="height:1.2rem;background: #f2f2f2;"></div> -->
               <div class="bottom_button flex_btn">
                  <button class="white bg_blue   w10-5 creat_event">活动邀请</button>
                   <button class="white bg_blue  w10-5 creat_inspect">考察邀请</button>
                   <button class="white bg_blue   w10-5 creat_pact">付款协议</button>
              </div>
              
          </div>
       
        <!-- 跟单备注 -->
          <div class="remarks bgwhite none">
                <!-- <div class="remark">
                  <p class="f12 color666 w_92 ui-nowrap-multi">用户有意向进行实地考察，需要和品牌方方面进行场地确认啦，可以考虑7月30日进行场地考察，杭州以湖用户有意向进行实地考察，需要和品牌方方面进行场地确认啦，可以考虑7月30日进行场地考察，杭</p>
                  <p class="f12">相关品牌：洗茶</p>
                  <p class="f11">客户登记：重要客户<span class="color999 r">2017/02/02</span></p>
                  <img src="/images/agent/pull_down.png" alt="" class="showdetail">
                </div> -->
               <!-- <div class="bottom_button">
                  <button class="white bg_blue l w_100 addremark">添加备注</button>
              </div> -->
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
                <!-- <div class="relative mb1-5 fline">
                  <p class="f14">活动名称，最多两行</p>
                  <p class="f12 color666">活动时间：</p>
                  <p class="f12 color666">活动地点：</p>
                  <p class="f12 color666">邀请状态：<span class="cffa300">待确认</span><span class="b color333">（还剩7天四个小时）</span></p>
                  <button class="sendAgain">再次发送</button>
                </div>
                <div class="relative mb1-5 fline">
                  <p class="f14">活动名称，最多两行</p>
                  <p class="f12 color666">活动时间：</p>
                  <p class="f12 color666">邀请状态：<span class="cfd4d4d">已拒绝</span></p>
                   <p class="f12 color666">拒绝理由：<span>时间冲突安排</span></p>
                  <p class="f12 color666">确认时间：<span>2017年02月01日 18：00:00</span></p>
                </div>
                <div class="relative mb1-5 fline">
                  <p class="f14">活动名称，最多两行</p>
                  <p class="f12 color666">活动时间：</p>
                  <p class="f12 color666">邀请状态：<span class="c30be74">已接受</span></p>
                   <p class="f12 color666">活动地点：</p>
                   <p class="f12 color666">确认时间：</p>
                  
                </div> -->
              </div>
              <div class="bottom_button">
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
                <!-- <div class="relative mb1-5 fline">
                  <p class="f14">考察品牌:xicha </p>
                  <p class="f12 color666">考察时间：</p>
                  <p class="f12 color666">考察门店：</p>
                  <p class="f12 color666">所在地区：<span >杭州</span></p>
                  <p class="f12 color666">详细地址：<span >杭州市下城区</span></p>
                  <p class="f12 color666">定金金额：<span >300元人民币整</span></p>
                  <p class="f12 color666">邀请状态：<span class="cffa300">待确认</span><span class="color333 b">还剩多少天</span></p>
                  <button class="sendAgain">再次发送</button>
                </div>
                <div class="relative mb1-5 fline">
                  <p class="f14">考察品牌:xicha </p>
                  <p class="f12 color666">考察时间：</p>
                  <p class="f12 color666">考察门店：</p>
                  <p class="f12 color666">所在地区：<span >杭州</span></p>
                  <p class="f12 color666">详细地址：<span >杭州市下城区</span></p>
                  <p class="f12 color666">定金金额：<span >300元人民币整</span></p>
                  <p class="f12 color666">邀请状态：<span class="cfd4d4d">已拒绝</span></p>
                  <p class="f12 color666">拒绝理由：<span class="color333">不知道什么理由</span></p>
                  <p class="f12 color666">确认时间：<span >2017年</span></p>
                </div>
                <div class="relative mb1-5 fline">
                  <p class="f14">考察品牌:xicha </p>
                  <p class="f12 color666">考察时间：</p>
                  <p class="f12 color666">考察门店：</p>
                  <p class="f12 color666">所在地区：<span >杭州</span></p>
                  <p class="f12 color666">详细地址：<span >杭州市下城区</span></p>
                  <p class="f12 color666">定金金额：<span >300元人民币整</span></p>
                  <p class="f12 color666">支付方式：<span >支付包</span></p>
                  <p class="f12 color666">邀请状态：<span class="c30be74">已接受</span></p>
                  <p class="f12 color666">确认时间：<span >2017年</span></p>   
                </div> -->
              </div>
              <div class="bottom_button">
                <button class="white bg_blue l w_100 creat_inspect">创建考察邀请</button>
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
                  <!-- <div class="pl1-5">
                    <div class="fline lh45 flex_bet f13 mb1-5">
                      <p class="">汗水签订洗茶付款协议</p>
                      <p class="c30be74 pr1-5">支付成功</p>
                    </div>
                    <div class="flex_bet pr1-5 color666 f12">
                      <p>付款协议</p>
                      <p>合同名称</p>
                    </div> 
                    <div class="flex_bet pr1-5 color666 f12">
                      <p>合同号</p>
                      <p>编号</p>
                    </div> 
                    <div class="flex_bet pr1-5 color666 f12">
                      <p>加盟品牌</p>
                      <p>洗茶</p>
                    </div> 
                    <div class="flex_bet pr1-5 color666 f12">
                      <p>合同撰写</p>
                      <p class="tr">
                        <span class="bk">无界商代表</span>
                        <span class="bk">无界商代表</span>
                      </p>
                    </div>
                    <div class="fline  mr1-5 mb1-5 relative">
                      <span class="bgwhite color666 f12 pl1-5 pr1-5 pt1 pb1 centerspan">首付情况</span>
                    </div>
                    <div class="flex_bet pr1-5 color666 f12">
                      <p>首次支付</p>
                      <p>洗茶</p>
                    </div> 
                    <div class="flex_bet pr1-5 color666 f12">
                      <p>定金抵扣</p>
                      <p></p>
                    </div> 
                    <div class="flex_bet pr1-5 color666 f12">
                      <p>创业基金抵扣</p>
                      <p>洗茶</p>
                    </div> 
                    <div class="flex_bet pr1-5 color666 f12">
                      <p>实际支付</p>
                      <p>洗茶</p>
                    </div> 
                     <div class="flex_bet pr1-5 color666 f12">
                      <p>支付状态</p>
                      <p>洗茶</p>
                    </div> 
                     <div class="flex_bet pr1-5 color666 f12">
                      <p>支付方式</p>
                      <p class="tr"><span class="bk">支付宝</span><span class="bk">qq.com</span></p>
                      
                    </div> 
                     <div class="flex_bet pr1-5 color666 f12">
                      <p>实际支付</p>
                      <p>洗茶</p>
                    </div> 
                    <div class="flex_bet pr1-5 color666 f12">
                      <p>支付时间</p>
                      <p>洗茶</p>
                    </div> 
                    <div class="fline  mr1-5 mb1-5 relative">
                      <span class="bgwhite color666 f12 pl1-5 pr1-5 pt1 pb1 centerspan">尾款情况</span>
                    </div>
                    <div class="flex_bet pr1-5 color666 f12">
                      <p>尾款补齐</p>
                      <p>￥1000</p>
                    </div> 
                    <div class="flex_bet pr1-5 color666 f12">
                      <p>支付状态</p>
                      <p class=" tr">
                          <span class="cfd4d4d">味支付</span>
                          <span class="bk">* 请提醒投资人尽快支付尾款费用</span>
                          <span class="bk">支付方式为线下对公账号转账</span>
                          <a class="bk c2873ff">了解尾款补齐操作办法</a>
                      </p>
                    </div> 
                    <div class="pr1-5 mb1-5">
                      <p class="color666 f12">合同文本</p>
                      <div class="flex_bet align-c p1-5 bgf5 radius_03" >
                        <div>
                          <img src="/images/agent/pact.png" alt="" class="l" style='width:3.4rem;height:3.9rem;'>
                          <div class="l ml05">
                            <p class="f14 b  mb0">息差加盟电子合同</p>
                            <p class="f12 "> 合同编号</p>
                          </div>  
                        </div>
                        <img src="/images/agent/black_to.png" alt="" style='width: 0.7rem;height:1.3rem;'>
                      </div>
                    </div>
                    <div class="flex_bet pr1-5 color666 f12">
                      <p>确定时间</p>
                      <p>2017/0.20/1</p>
                    </div> 
                  </div> -->               
                  <!-- </div> -->
              </div>
              <div class="bottom_button">
                <button class="white bg_blue l w_100 creat_pact">创建付款协议</button>
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
@stop
@section('endjs')
    <script type="text/javascript">
      Zepto(function(){
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
              var url = labUser.agent_path +'/customer/detail-brands/_v010000';
              ajaxRequest({'agent_id':agent_id,'customer_id':customer_id},url,function(data){
                  if(data.status){
                    var customer = data.message.customer||'';
                    var list = data.message.brand_list || [];
                    var remarkname = customer.remark ? '('+customer.remark+')' :'';
                    $('.avatar').attr('src',customer.avatar);
                    $('.customerName').text(customer.nickname);
                    $('.remarkName').text(remarkname);
                    $('.customerLevel').text(customer.level);
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
                            invHtml += '<div class="bottom_button"><button class="white bg_blue l w_100 send_brand">发送品牌给客户</button></div>';
                            $('.brand_intro').html(invHtml);
                    }else{
                      if(list.length >0){
                        $.each(list,function(i,j){
                          var brandHtml = ''
                          brandHtml += ' <div class="brand fline" data-id="'+j.brand_id+'"><img src="'+j.logo+'" class="l logo"> <div class="mt05">';
                          brandHtml += ' <div><span class="f15 mr1">'+j.brand_title+'</span><span class="r f12 mt1 c2873ff" >跟进情况</span></div>';
                          brandHtml += '<div class="f11 color999"><span>'+unix_to_dateMD(j.success_time)+'</span>派单成功</div></div><div class="clearfix"></div>';
                          brandHtml += '</div><ul class="followlists followlists_'+i+'">';
                          brandHtml += '<li class="none"><p class="color999"><span class="imgbox"><img src="/images/agent/link.png" class="link_img "></span>与投资人形成代理关系</p></li>';
                          brandHtml += '<li class="none"><p class="color999"><span class="imgbox"><img src="/images/agent/right.png" class="right_img"></span>获得投资人电话及其他通讯方式</p></li>';
                          brandHtml += '<li><p class="color999"><span class="imgbox"><img src="/images/agent/error.png" class="error_img"></span>邀请用户参加OVO发布会</p></li>';
                          brandHtml += '<li><p class="color999"><span class="imgbox"><img src="/images/agent/error.png" class="error_img"></span>邀请用户总部或门店考察</p></li>';
                          brandHtml += '<li><p class="color999"><span class="imgbox"><img src="/images/agent/error.png" class="error_img"></span>签约付款协议，交付线上首付</p></li>';
                          brandHtml += '<li><p class="color999"><span class="imgbox"><img src="/images/agent/error.png" class="error_img"></span>签约付款协议，线下尾款补齐</p></li>';
                          brandHtml += ' </ul> <div style="height:1.2rem;background: #f2f2f2;"></div>';
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
        //跟单备注
          function getDocumentary(agent_id,customer_id){
            var url = labUser.agent_path +'/customer/detail-remarks/_v010000';
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
                    remarkHtml +='<div class="bottom_button"><button class="white bg_blue l w_100 addremark">添加备注</button></div>'
                    $('.remarks').html(remarkHtml);
                    // var dis = $('.remark .ui-nowrap-multi');
                    // $.each(dis,function(i,j){
                    //     var spanHeight= parseFloat($(j).css('height')),spanLH=parseFloat($(j).css('line-height'));
                    //         if(spanHeight > spanLH*2){
                    //             // dis.addClass('ui-nowrap-multi'); 
                    //         }else{
                    //             $(j).siblings('img').addClass('none');
                    //             // dis.addClass('pb1-5');
                    //         } ;  
                    // });
                    
                }
            })
          }
          getDocumentary(agent_id,customer_id);
      //活动邀请
          function getEvents(agent_id,customer_id,type){
            var url = labUser.agent_path +'/customer/activity-invite/_v010000';
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
            var url = labUser.agent_path +'/customer/inspect-invite/_v010000';
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
                                }else if(j.status == 1 || j.status == 2){
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
              var url = labUser.agent_path + '/customer/contracts/_v010000';
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
                              // pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>合同撰写</p> <p class="tr">';
                              // pactHtml += '<span class="bk">无界商圈法务代表</span><span class="bk">'+j.brand+'法务代表</span></p> </div>';
                              // pactHtml += '<div class="fline mb1" style="margin-top:-0.5rem;"></div>';
                              pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>加盟总费用</p> <p>￥'+j.amount+'</p></div>';
                              pactHtml += '<div class="pr1-5 mb1-5"><p class="color666 f12">合同文本</p><div class="toContract flex_bet align-c p1-5 bgf5 radius_03" data-id="'+j.id+'" data-url="'+j.address+'" >';
                              pactHtml += '<div><img src="/images/agent/pact.png" alt="" class="l" style="width:3.4rem;height:3.9rem;">';
                              pactHtml += '<div class="l ml05"><p class="f14 b w25 lh3-9 text-ellipsis mb0">'+j.brand+'加盟电子合同</p></div></div>';
                              pactHtml += '<img src="/images/agent/black_to.png" alt="" style="width: 0.7rem;height:1.3rem;""></div></div>';
                              pactHtml += '<div class="fline mb1" style="margin-top:-0.5rem;"></div>';
                              pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>拒绝理由</p><p>'+j.remark+'</p></div>';
                              pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>确定时间</p><p>'+unix_to_fulltime_hms(j.confirm_time)+'</p></div> </div><div class="bgf5 pt1-5"></div></div>';
                            }else if(j.status == 0){
                              pactHtml += '<div class="pact_wait"><div class="pl1-5"><div class="fline lh45 flex_bet f13 mb1-5">';
                              pactHtml += '<p class="no-wrap flex-1">等待'+j.nickname+'确定['+j.brand+']付款协议</p><p class="cffa300 pr1-5">等待中</p></div>';
                              pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>付款协议</p><p>'+j.contract_title+'</p></div>';
                              if(j.contract_no){
                                 pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>流水号</p><p>'+j.contract_no+'</p></div>';
                              }                         
                              pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>加盟品牌</p><p>'+j.brand+'</p></div>';
                              // pactHtml += '<div class="flex_bet  color666 f12 fline mb1"><p>合同撰写</p> <p class="tr">';
                              // pactHtml += '<span class="bk pr1-5">无界商圈法务代表</span><span class="bk pr1-5">'+j.brand+'法务代表</span></p></div>';
                              pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>加盟总费用</p><p>￥'+j.amount+'</p></div>';
                              pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>线上首付</p><p>￥'+j.pre_pay+'</p></div>';
                              pactHtml += ' <div class="flex_bet pr1-5 color666 f12"><p>线下尾款</p><p>￥'+j.tail_pay+'</p></div>';
                              pactHtml += '<div class="flex_bet  color666 f12  pr1-5 "><p>缴纳方式</p><p class="tr">';
                              pactHtml += '<span class="bk">线上首付一次结清</span><span class="bk">线下尾款银行转账</span>';
                              pactHtml += '<span class="bk c2873ff wkbq">了解尾款补齐操作方法</span></p></div>';
                              //改15388 bug
                              pactHtml += '<div class="pr1-5 mb1-5"><p class="color666 f12">合同文本</p><div class="toContract flex_bet align-c p1-5 bgf5 radius_03" data-id="'+j.id+'" data-url="'+j.address+'">';
                              pactHtml += ' <div><img src="/images/agent/pact.png" alt="" class="l" style="width:3.4rem;height:3.9rem;">';
                              pactHtml += '<div class="l ml05"><p class="f14 b text-ellipsis w25 lh3-9 mb0">'+j.brand+'加盟电子合同</p><p class="f12 none"> 合同编号:'+j.contract_no+'</p></div></div><img src="/images/agent/black_to.png" alt="" style="width: 0.7rem;height:1.3rem;"></div></div>';
                              pactHtml += '<div class="mt1 mb1 fline"></div>';
                              
                              pactHtml += '<div class="flex_bet pr1-5 color666 f12 align-c pt1"><p>缴纳状态:<span class="cffa300">待确认 </span>('+j.leftover+')</p><p><button class="pact_sendagain" data-id="'+j.id+'" data-title="'+j.contract_title+'" data-brand="'+j.brand+'">再次发送</button></p></div></div><div class="bgf5 pt1-5"></div></div>';
                            }else if(j.status == 1 || j.status == 2){
                              pactHtml += '<div class="pact_accept"><div class="pl1-5"><div class="fline lh45 flex_bet f13 mb1-5">';
                              pactHtml += '<p class="no-wrap flex-1">'+j.nickname+'签订['+j.brand+']付款协议</p><p class="c30be74 pr1-5">支付成功</p></div>';
                              pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>付款协议</p><p>'+j.contract_title+'</p></div>';
                              if(j.contract_no){
                                pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>流水号</p><p>'+j.contract_no+'</p></div>';
                              }                        
                              pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>加盟品牌</p><p>'+j.brand+'</p></div>';
                              // pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>合同撰写</p><p class="tr">';
                              // pactHtml += '<span class="bk">无界商圈法务代表</span><span class="bk">'+j.brand+'法务代表</span></p></div>';
                              pactHtml += '<div class="fline  mr1-5 mb1-5 relative"><span class="bgwhite color666 f12 pl1-5 pr1-5 pt1 pb1 centerspan">首付情况</span></div>';
                              pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>首次支付</p><p>￥'+j.pre_pay+'</p></div>';
                              pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>订金抵扣</p><p>-￥'+j.invitation+'</p></div>';
                              pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>创业基金抵扣</p><p>-￥'+j.fund+'</p></div>';
                              pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>实际支付</p> <p>￥'+j.first_amount +'</p></div>';
                              pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>支付状态</p><p>'+j.first_pay_status+'</p></div>';
                              pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>支付方式</p><p class="tr"><span class="bk">'+j.pay_way+'</span></p></div>';
                              pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>支付时间</p><p>'+unix_to_fulltime_hms(j.pay_at)+'</p></div>';
                              pactHtml += '<div class="fline  mr1-5 mb1-5 relative"><span class="bgwhite color666 f12 pl1-5 pr1-5 pt1 pb1 centerspan">尾款情况</span></div>';
                              pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>尾款补齐</p><p>￥'+j.tail_pay+'</p></div>';
                              pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>支付状态</p>';
                              if(j.status == 1){
                                pactHtml += '<p class="tr">';
                                 pactHtml += '<span class="cfd4d4d">'+j.tail_pay_status+'</span><span class="bk">* 请提醒投资人尽快支付尾款费用</span><span class="bk">支付方式为线下对公账号转账</span><a class="bk wkbq c2873ff">了解尾款补齐操作办法</a></p></div>';
                              }else{
                                pactHtml +='<p class="c30be74">'+j.tail_pay_status+'</p></div>';
                                pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>支付方式</p><p class="tr"><span class="bk">银行卡转账</span>';
                                pactHtml += '<span>'+j.bank_no+'</span> <span>('+j.bank_name+')</span></p></div>';
                                // pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>到帐时间</p><p>'+j.+'</p></div>'
                                // pactHtml += '<div class="flex_bet pr1-5 color666 f12"><p>财务确认人</p><p>'+j.auditor+'</p></div>'
                              }
                                                         
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
              window.location.href = labUser.path + 'webapp/agent/customer/data?agent_id='+agent_id+'&customer_id='+customer_id;
          });
  //发送品牌给客户
          $(document).on('click','.send_brand',function(){
              var nickname = $('.customerName').text();
              agentBrand(customer_id,nickname);
          })
    //添加备注
        $(document).on('click','.addremark',function(){
            window.location.href = labUser.path + 'webapp/agent/remark/detail?agent_id='+agent_id+'&customer_id='+customer_id+'&form_list=1';
        });
    //跟进情况
        $(document).on('click','.brand',function(){
            var id= $(this).attr('data-id');
            window.location.href = labUser.path + 'webapp/agent/tracklist/detail?agent_id='+agent_id+'&customer_id='+customer_id+'&brand_id='+id;
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
            // nickname = $('.customerName').text();
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
             brand_name = _this.attr('data-brand');
         // var nickname = $('.customerName').text();
             sendRichMsg('3','C', customer_id,contract_id, title, '','','');
      });

    //查看合同详情
      $(document).on('click','.toContract',function(){
        var URL = $(this).attr('data-url');
            window.location.href = labUser.path +'js/agent/generic/web/viewer.html?file='+URL;
            // window.location.href = labUser.path +'webapp/agent/contract/viewer?file='+URL;
      });

    //了解尾款补齐
     $(document).on('click','.wkbq',function(){
            window.location.href = labUser.path +'webapp/agent/way/detail';
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