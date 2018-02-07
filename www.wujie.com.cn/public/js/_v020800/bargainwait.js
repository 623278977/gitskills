 new FastClick(document.body);
    var page=1;
    var page_size=100;
    var urlPath = window.location.href,
        args = getQueryStringArgs(),
        agent_id = args['uid'];
    var askDetail = {
        detail: function (agent_id,status) {
            var param = {};
            param["uid"] = agent_id;
            param['status']=status;
            //var url=labUser.agent_path+'/user/contract-detail/_v010000';
            var url = labUser.api_path + '/contract/contract-detail/_v020800';
            ajaxRequest(param, url, function (data) {
            if (data.status) {
              waitConfirmation(data.message.conreact)
            }//data.status
            })// ajaxRequest
            }//detail方法
        }//activityDetail对象 
     askDetail.detail(agent_id,0);
    function waitConfirmation(obj){
    if(obj.length>0){
      $.each(obj,function(k,v){
        var html='';
            html+='<div class="ui_top_time">\
                          <div style="width:100%;height:2rem"></div>\
                          <center><div class="ui_show_time width10">'+unix_to_pretime(v.created_at)+'</div></center>\
                   </div>';
            html+='<div class="ui_common_contrack  bgcolor add_ui1">';
            html+='<div class="ui_contrack_middle fline ui_pR color999 padding00 zone" data-id="'+v.id+'" data-order_no="'+v.order_no+'">\
                      <p style="text-align:left" class="margin07 f12 ">付款协议<span class="fr color333">'+v.contract_title+'</span></p>\
                      <p style="text-align:left" class="margin07 f12 none">流水号<span class="fr color333">'+v.contract_no+'</span></p>\
                      <p style="text-align:left" class="margin07 f12 ">加盟品牌<span class="fr color333">'+v.brand+'</span></p>\
                      <p style="text-align:left" class="margin07 f12 ">经纪人<span class="fr color333">'+v.agent_name+'</span></p>\
                      <div style="width:100%;height:0.3rem"></div>\
                    </div>';
            html+='<div class="ui_contrack_middle  ui_pR color999 ">\
                    <p style="text-align:left" class="margin07 f12 zone" data-order_no="'+v.order_no+'" data-id="'+v.id+'">加盟总费用<span class="fr color333">￥'+v.amount+'</span></p>\
                    <div style="width:100%;height:0.3rem" class="clear"></div>\
                  </div>';
            html+='<div class="ui_contrack_bottom fline ui_pR color999">\
                          <p style="text-align:left" class="margin07 f12">合同文本</p>\
                          <ul class="ui_contrack_detail ui_add_bg" data-url="'+v.address+'">\
                            <li>\
                              <img class="ui_img6"  src="/images/020700/bargain2.png">\
                            </li>\
                            <li>\
                              <p class="f14 b textleft color333 margin05">'+v.brand+'加盟电子合同</p>\
                              <p class="f11 textleft color333 none">合同编号：'+v.contract_no+'</p>\
                            </li>\
                            <li>\
                              <img class="ui_img7"  src="/images/020700/y.png">\
                            </li>\
                          </ul>\
                          <div style="width:100%;height:1rem;clear:both"></div>\
                   </div>';
            html+='<div style="width:100%;height:2.3rem;"></div>\
                    <div class="ui_pR color666">';
            if(v.status==0){
               html+='      <ul class="accept_refuse">\
                              <li><a class="ui_border ui_refuse f13 color666 " data-id="'+v.id+'">拒绝</a></li>\
                              <li><a class="f13 ui_accept clear" data-id="'+v.id+'"><center>签约加盟合同</center></a></li>\
                          </ul>';
            }else if(v.status==6){
               html+=' <center><button class="ui-pay-contract f13" data-order_no="'+v.order_no+'">支付费用</button></center>';       
            }       
               html+='</div></div>';
               $('.ui_con').append(html);
      })
    }else{
        $('#nocommenttip3').removeClass('none')
    }
    }
    //提示方法
    function tips(e) {
         $('.tips').text(e).removeClass('none');
        setTimeout(function() {
            $('.tips').addClass('none');
        }, 1500);

    }; 
    function unix_to_pretime(unix) {
    var newDate = new Date();
    newDate.setTime(unix * 1000);
    var Y = newDate.getFullYear();
    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
    return Y + '年' + M + '月';
     }
    //接受合同签约
   function acceptContract(id){
    if (isAndroid) {
        javascript:myObject.acceptContract(id);
    }else if(isiOS){
        var data={
            'id':id
                };
        window.webkit.messageHandlers.acceptContract.postMessage(data);
    }
   }
   //拒绝合同
  function rejectContract(id){
    if (isAndroid) {
        javascript:myObject.rejectContract(id);
    }else if(isiOS){
        var data={
            'id':id
                };
        window.webkit.messageHandlers.rejectContract.postMessage(data);
    }
   }
  $(document).on('click','.ui_refuse',function(){
       var id=$(this).data('id');
       rejectContract(id)
  })
  $(document).on('click','.ui_accept',function(){
       var id=$(this).data('id');
       acceptContract(id)
  })
  $(document).on('click','.ui_contrack_detail',function(){
         var url=$(this).data('url');
         window.location.href = labUser.path +'js/agent/generic/web/viewer.html?file='+url;
}) 
 $(document).on('click','.zone',function(){
       // var contract_id=$(this).data('id');
       var order_no=$(this).data('order_no');
       checkMyorder(order_no);
       // window.location.href =labUser.path+'/webapp/client/pactdetails/_v020800?contract_id='+contract_id+'&uid='+agent_id; 
  }) 
  $(document).on('click','.way',function(){
         window.location.href = labUser.path +'webapp/agent/way/detail';
  })    
  function checkMyorder(orderNo){
        if (isAndroid) {
          javascript: myObject.checkMyorder(orderNo);
        }
        else if (isiOS) {
             var message = {
                    method:'checkMyorder',
                    params:{
                      'order_no':orderNo
                    }
                }; 
          window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
        }
      };
