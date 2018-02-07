Zepto(function (){
    new FastClick(document.body);
    var urlPath = window.location.href,
        args = getQueryStringArgs(),
        activity_id = args['id'],
        agent_id = args['uid'] || '0';
    var askDetail = {
        detail: function (agent_id,status) {
            var param = {};
            param["uid"] = agent_id;
            param['status']=status;
            // var url=labUser.agent_path+'/user/contract-detail/_v010000';
             var url = labUser.api_path + '/contract/contract-detail/_v020800';
            ajaxRequest(param, url, function (data) {
            if (data.status) {
              waitConfirmation(data.message.conreact)
            }//data.status
            })// ajaxRequest
            }//detail方法
        }//activityDetail对象 
     askDetail.detail(agent_id,1);
    function waitConfirmation(obj){
    if(obj.length>0){
      $.each(obj,function(k,v){
        var html='';
            html+='<div class="ui_top_time">\
                          <div style="width:100%;height:2rem"></div>\
                          <center><div class="ui_show_time width20">'+unix_to_pretime(v.created_at)+'</div></center>\
                   </div>';
            html+='<div class="ui_common_contrack  bgcolor add_ui1">';
            html+='<div class="ui_contrack_middle  ui_pR color999 padding00 zone" data-id="'+v.id+'" data-order_no="'+v.order_no+'">\
                    <p style="text-align:left" class="margin07 f12">付款协议<span class="fr color333">'+v.contract_title+'</span></p>\
                    <p style="text-align:left" class="margin07 f12">流水号<span class="fr color333">'+v.contract_no+'</span></p>\
                    <p style="text-align:left" class="margin07 f12">加盟品牌<span class="fr color333">'+v.brand+'</span></p>\
                    <p style="text-align:left" class="margin07 f12">经纪人<span class="fr color333">'+v.agent_name+'</span></p>\
                    <p style="text-align:left" class="margin07 f12">加盟费用<span class="fr color333">￥'+v.amount+'</span></p>\
                    <p style="text-align:left" class="margin07 f12">创建时间<span class="fr color333">'+unix_to_yearalldatetime(v.created_at)+'</span></p>\
                    <p style="text-align:left" class="margin07 f12">确定时间<span class="fr color333">'+unix_to_yearalldatetime(v.confirm_time)+'</p>\
                    <div style="width:100%;height:1rem"></div>\
                   </div>';
            html+='<ul class="ui_border_flex ui_pR color666 f12 none" data-id="'+v.id+'">\
                      <li style="width:40%"><div class="ui-border-b ui_row"></div></li>\
                      <li style="width:20%"><span>首付情况</span></li>\
                      <li style="width:40%"><div class="ui-border-b ui_row"></div></li>\
                    </ul>';
            html+='<div class=" clear ui_bg ui_pR color999 zone none" data-id="'+v.id+'" data-order_no="'+v.order_no+'">\
                    <p style="text-align:left" class="margin07 f12">首次支付<span class="fr color333">￥'+v.pre_pay+'</span></p>\
                    <p style="text-align:left" class="margin07 f12">定金抵扣<span class="fr color333">-￥'+v.invitation+'</span></p>\
                    <p style="text-align:left" class="margin07 f12">创业基金抵扣<span class="fr color333">-￥'+v.fund+'</span></p>\
                    <p style="text-align:left" class="margin07 f12">实际支付<span class="fr color333">￥'+v.first_amount+'</span></p>\
                    <p style="text-align:left" class="margin07 f12">支付状态<span class="fr color333">'+v.first_pay_status+'</span></p>\
                    <p style="text-align:left" class="margin07 f12">支付方式<span class="fr color333">'+v.pay_way+'</span></p>\
                  </div>';
            html+='<div style="width:100%;height:0.5rem;clear:both " class="none"></div>\
                    <ul class="ui_border_flex ui_pR color666 f12 zone none" data-id="'+v.id+'" data-order_no="'+v.order_no+'">\
                            <li style="width:40%"><div class="ui-border-b ui_row"></div></li>\
                            <li style="width:20%"><span>尾款情况</span></li>\
                            <li style="width:40%"><div class="ui-border-b ui_row"></div></li>\
                    </ul>';
           if(v.status==1){
            html+=' <div class="ui_bg ui_pR color999 none" >\
                      <p style="text-align:left" class="margin07 f12 zone" data-id="'+v.id+'">尾款补齐<span class="fr color333">￥'+v.tail_pay+'</span></p>\
                      <p style="text-align:left" class="margin07 f12 zone" data-id="'+v.id+'">尾款状态<span class="fr fc6262">'+v.tail_pay_status+'</span></p>\
                      <p style="text-align:left" class="margin7 f10 clear zone" data-id="'+v.id+'"><span class="fr">*请于'+pretime(v.tail_leftover)+'前支付相应款项，如有延误等情况，请尽早联系经纪人</span></p>\
                      <div style="width:100%;height:0.5rem" class="clear zone" data-id="'+v.id+'"></div>\
                      <p style="text-align:left" class="margin7 f11 clear zone" data-id="'+v.id+'"><span class="fr">支付方式为线下对公账号转账</span></p>\
                      <div style="width:100%;height:0.5rem" class="clear"></div>\
                      <p style="text-align:left" class="margin7 f11 clear way"><span class="fr ff">了解尾款补齐操作方法</span></p>\
                    </div>';
          }else if(v.status==2){
           html+='<div class="ui_bg ui_pR  color999 zone none" data-id="'+v.id+'">\
                                 <p style="text-align:left" class="margin07 f12">尾款补齐<span class="fr color333">￥'+v.tail_pay+'</span></p>\
                                 <p style="text-align:left" class="margin07 f12">尾款状态<span class="fr be74">'+v.tail_pay_status+'</span></p>\
                                 <p style="text-align:left" class="margin7 f12 clear">支付方式<span class="fr color333">银行卡转账</span></p>\
                                 <p style="text-align:left" class="margin7 f12 clear"><span class="fr color333">'+v.bank_no +'('+ v.bank_name+')'+'</span></p>\
                                 <div style="height:1rem;width:100%;clear:both"></div>\
                                 <p style="text-align:left" class="margin7 f12 clear">到账时间<span class="fr color333">'+change_unix(v.tail_pay_at)+'</span></p>\
                               </div>';  
                  }
            html+='<div class="ui_contrack_bottom ui_pR color999 padding00">\
                    <p style="text-align:left" class="margin07 f12">合同文本</p>\
                    <ul class="ui_contrack_detail ui_add_bg " data-url="'+v.address+'">\
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
                  </div>';
            html+='</div>';
        $('.ui_con').append(html);
      })
    }else{
        $('#nocommenttip3').removeClass('none')
    }
    }
    //改造时间戳
    function unix_to_yearalldatetime(unix){
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
    function unix_to_pretime(unix) {
                                      var newDate = new Date();
                                      newDate.setTime(unix * 1000);
                                      var Y = newDate.getFullYear();
                                      var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
                                      var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
                                      return Y + '年' + M + '月';
     }
    function pretime(unix) {
                                    var newDate = new Date();
                                    newDate.setTime(unix * 1000);
                                    var Y = newDate.getFullYear();
                                    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
                                    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
                                    return  M + '/'+D;
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
                                return Y + '/' + M + '/' + D +'/'+ ' ' + h + ':' + m+ ':'+s;
    }
    $(document).on('click','.ui_contrack_detail',function(){
         var url=$(this).data('url');
         window.location.href = labUser.path +'js/agent/generic/web/viewer.html?file='+url;
}) 
//跳转到合同邀请函详情页
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
});//zepto外层
