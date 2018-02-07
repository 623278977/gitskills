// created byhongky
Zepto(function (){
    new FastClick(document.body);
    var urlPath = window.location.href,
        args = getQueryStringArgs(),
        inspect_id = args['inspect_id'];
    var tagFlag = urlPath.indexOf('is_out') > 0 ? true : false;
    $(document).ready(function(){
      $('body').css('background','#f2f2f2');
    })
    var Extend = {
        detail: function (inspect_id) {
            var param = {};
            param["inspect_id"] = inspect_id;
            var url=labUser.agent_path+'/message/show-inspect-invitation/_v010000';
            ajaxRequest(param, url, function (data) {
            if (data.status) {
                                Extend.data(data.message);
                                Extend.style(data.message);
                                $('#act_container').removeClass('none');
            }else{
                         $('.ui_container').addClass('none');
                         $('.ui_onmessage').removeClass('none');
                         $('#act_container').removeClass('none');
            }//data.status
                    })// ajaxRequest
            },//detail方法 
        data:function(obj){
             $('#customer_name').html(' '+obj.customer_realname+' ');
             if(obj.customer_gender=='未知'){
              $('#sex').addClass('none');
             }else if(obj.customer_gender=='女'){
              $('#sex').html(obj.customer_gender+'士');
             }else if(obj.customer_gender=='男'){
              $('#sex').html('先生');
             }
             $('.ui_onetime').html('&nbsp'+unix_to_gagag(obj.inspect_time)+'&nbsp');
             $('.ui_brand_infor li').eq(1).find('img').attr('src',obj.img);
             $('.ui_brand_infor li').eq(2).html(obj.title);
             $('.storename').html('考察门店：'+obj.store_name);
             $('.belongname').html('所在地区：'+obj.head_address);
             $('.detailname').html('地址:'+obj.totals_address);
             $('.time_name').html(unix_to_parttime(obj.inspect_time));
             $('.curreny_name').html('￥'+obj.default_money);
             if(obj.title.length>8){
               $('#brand').html('&nbsp'+'【'+obj.title+'】'+'&nbsp'+'进行实地考察。').parent().addClass('ui_left'); 
               $('.showit').addClass('none'); 
               $('#ui_need').addClass('none');
             }else{
              $('#brand').html('&nbsp'+'【'+obj.title+'】'+'&nbsp');
             }
             if(obj.is_public_realname==1){
               $('#agent_name').html(' '+'('+(obj.agent_realname?obj.agent_realname:obj.agent_nickname)+')'+' ');
               $('.hahaname').html('('+(obj.agent_realname?obj.agent_realname:obj.agent_nickname)+')');
               $('.ggname').html('跟单经纪人:'+(obj.agent_realname?obj.agent_realname:obj.agent_nickname));
             }else if(obj.is_public_realname==0){
               $('#agent_name').html(' '+'('+obj.agent_nickname+')'+' ');
               $('.hahaname').html('('+obj.agent_nickname+')');
               $('.ggname').html('跟单经纪人:'+obj.agent_nickname);
             } 
             $('.in_time').html('邀请时间：'+change_unix(obj.invite_time));
             $('.ui_fixed li').eq(0).data('brand_id',obj.brand_id).data('store_id',obj.store_id).data('agent_id',obj.agent_id);
             $('.ui_fixed li').eq(1).data('brand_id',obj.brand_id).data('store_id',obj.store_id).data('agent_id',obj.agent_id);
        },
        style:function(obj){
            if(obj.status==0){
              $('.ui_top_fixed').addClass('none');
            }else if(obj.status==1||obj.status==4){
              $('.ui_fixed').addClass('none');
              $('.ui_top').css('margin','4.5rem  auto');
              if(obj.is_public_realname==1){
                $('.ui_top_fixed').html('接受了来自'+(obj.agent_realname?obj.agent_realname:obj.agent_nickname)+'的品牌考察邀请');
              }else if(obj.is_public_realname==0){
                 $('.ui_top_fixed').html('接受了来自'+obj.agent_nickname+'的品牌考察邀请');
               }
              $('.comform_time').html('确认时间：'+change_unix(obj.confirm_time));
              $('.ui_fixeded').removeClass('none');
              $('.ui_fixeded').html('<a class="ff5a00">查看我的订单</a>').data('order_no',obj.order_no);
            }else if(obj.status==-1){
              $('.ui_fixed').addClass('none');
              $('.ui_top').css('margin','4.5rem  auto');
              if(obj.is_public_realname==1){
                 $('.ui_top_fixed').html('拒绝了来自'+(obj.agent_realname?obj.agent_realname:obj.agent_nickname)+'的品牌考察邀请').addClass('refuse');
              }else if(obj.is_public_realname==0){
                  $('.ui_top_fixed').html('拒绝了来自'+obj.agent_nickname+'的品牌考察邀请').addClass('refuse');
              }
              $('.comform_time').html('拒绝时间：'+change_time(obj.confirm_time)).parent().removeClass('none');
              $('.refusebg').html('拒绝理由：'+obj.reason).removeClass('none');
            }else if(obj.status==2||obj.status==3){
              $('.ui_fixed').addClass('none');
              $('.ui_top').css('margin','4.5rem  auto');
              if(obj.is_public_realname==1){
                $('.ui_top_fixed').html('接受了来自'+(obj.agent_realname?obj.agent_realname:obj.agent_nickname)+'的品牌考察邀请');
              }else if(obj.is_public_realname==0){
                 $('.ui_top_fixed').html('接受了来自'+obj.agent_nickname+'的品牌考察邀请');
               }
              $('.comform_time').html('确认时间：'+change_unix(obj.confirm_time));
              $('.ui_fixeded').removeClass('none');
              $('.ui_fixeded').html('<a class="ff5a00">查看我的订单</a>').data('order_no',obj.order_no);
            }else if(obj.status==-3){
              $('.ui_container').addClass('none');
              $('.ui_onmessage').removeClass('none');
            }
             $('.ui_fixed li').eq(1).on('click',function(){
              var brand_id=$(this).data('brand_id'),
                  agent_id=$(this).data('agent_id'),
                  store_id=$(this).data('store_id');
                  acceptInvestigate(inspect_id,agent_id,brand_id,store_id);
             })
             $('.ui_fixed li').eq(0).on('click',function(){
              var brand_id=$(this).data('brand_id'),
                  agent_id=$(this).data('agent_id'),
                  store_id=$(this).data('store_id');
                  rejectInvestigate(inspect_id,agent_id,brand_id,store_id);
             })
            if(tagFlag){
              $('.ui_fixeded').addClass('none');
            }else{
              $(document).on('click', '.ui_fixeded', function(){
                var order_no = $(this).data('order_no');
                checkMyorder(order_no, 1);
              })
            }
        }       
        }//activityDetail对象    
        Extend.detail(inspect_id);
        //点击进入详情
   $(document).on('click','.zone',function(){
      window.location.href =labUser.path+'webapp/agent/profit/detail'; 
   })   
    
});//zepto外层
    function stampchange(unix){
                                var newDate = new Date();
                                newDate.setTime(unix * 1000);
                                var Y = newDate.getFullYear();
                                var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
                                var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
                                var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
                                var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
                                var s = newDate.getSeconds() < 10 ? '0' + newDate.getSeconds() : newDate.getSeconds();
                                return  M + '月' + D +'日'+ ' ' + h + ':' + m;
      }
     function change_unix(unix){
                                var newDate = new Date();
                                newDate.setTime(unix * 1000);
                                var Y = newDate.getFullYear();
                                var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
                                var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
                                var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
                                var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
                                var s = newDate.getSeconds() < 10 ? '0' + newDate.getSeconds() : newDate.getSeconds();
                                return Y + '/' + M + '/' + D + ' ' + h + ':' + m+ ':'+s;
    }
    function unix_to_parttime(unix){
                                    var newDate = new Date();
                                    newDate.setTime(unix * 1000);
                                    var Y = newDate.getFullYear();
                                    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
                                    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
                                    return Y + '/' + M + '/' + D ;
    }
    function change_time(unix){
                                var newDate = new Date();
                                newDate.setTime(unix * 1000);
                                var Y = newDate.getFullYear();
                                var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
                                var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
                                var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
                                var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
                                var s = newDate.getSeconds() < 10 ? '0' + newDate.getSeconds() : newDate.getSeconds();
                                return Y + '年'+  M + '月' + D +'日'+ ' ' + h + ':' + m;
    }
    function unix_to_gagag(unix) {
                                  var newDate = new Date();
                                  newDate.setTime(unix * 1000);
                                  var Y = newDate.getFullYear();
                                  var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
                                  var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
                                  return  M + '/' + D ;
   }
   //接受考察邀请函
   function acceptInvestigate(inspect_id,agent_id,brand_id,store_id){
              if (isAndroid) {
                  javascript:myObject.acceptInvestigate(inspect_id);
              }else if(isiOS){
                  var data={
                      'inspect_id':inspect_id,
                      'agent_id':agent_id,
                      'brand_id':brand_id,
                      'store_id':store_id
                          };
                  window.webkit.messageHandlers.acceptInvestigate.postMessage(data);
              }
        }
  //拒绝考察邀请函
  function rejectInvestigate(inspect_id,agent_id,brand_id,store_id){
              if (isAndroid) {
                  javascript:myObject.rejectInvestigate(inspect_id);
              }else if(isiOS){
                  var data={
                      'inspect_id':inspect_id,
                      'agent_id':agent_id,
                      'brand_id':brand_id,
                      'store_id':store_id
                          };
                  window.webkit.messageHandlers.rejectInvestigate.postMessage(data);
              }
        }
  //查看我的订单
  function checkMyorder(order_no,type){
              if (isAndroid) {
                  javascript:myObject.checkMyorder(order_no);
              }else if(isiOS){
                  var data={
                      'order_no':order_no,
                      'type':type
                          };
                  window.webkit.messageHandlers.checkMyorder.postMessage(data);
              }
       }    