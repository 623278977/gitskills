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
     askDetail.detail(agent_id,-1);
    function waitConfirmation(obj){
    if(obj.length>0){
      $.each(obj,function(k,v){
        var html='';
            html+='<div class="ui_top_time">\
                          <div style="width:100%;height:2rem"></div>\
                          <center><div class="ui_show_time width20">'+unix_to_pretime(v.created_at)+'</div></center>\
                   </div>';
            html+='<div class="ui_common_contrack  bgcolor add_ui1 ">';
            html+='<div class="ui_contrack_middle fline ui_pR color999 padding00" data-id="'+v.id+'">\
                    <p style="text-align:left" class="margin07 f12">付款协议<span class="fr color333">'+v.contract_title+'</span></p>\
                    <p style="text-align:left" class="margin07 f12">加盟品牌<span class="fr color333">'+v.brand+'</span></p>\
                    <p style="text-align:left" class="margin07 f12">经纪人<span class="fr color333">'+v.agent_name+'</span></p>\
                    <p style="text-align:left" class="margin07 f12">合同撰写<span class="fr color333">无界商圈法务人员</span></p>\
                    <p style="text-align:left" class=" f12"><span class="fr color333">'+v.brand+'法务人员</span></p>\
                    <div style="width:100%;height:2.5rem"></div>\
                  </div>';
            html+='<div class="ui_contrack_bottom fline ui_pR color999">';
            html+='<div class="zone" data-id="'+v.id+'">\
                    <p style="text-align:left" class="margin07 f12">加盟费用<span class="fr color333">￥'+v.amount+'</span></p>\
                    <p style="text-align:left" class="margin07 f12">合同文本</p>';
            html+='</div>\
                    <ul class="ui_contrack_detail ui_add_bg" data-url="'+v.address+'">\
                      <li>\
                        <img class="ui_img6"  src="/images/020700/bargain2.png">\
                      </li>\
                      <li>\
                        <p class="f14 b textleft color333 margin05">'+v.brand+'加盟电子合同</p>\
                      </li>\
                      <li>\
                        <img class="ui_img7"  src="/images/020700/y.png">\
                      </li>\
                    </ul>\
                    <div style="width:100%;height:1.5rem"></div>\
                  </div>';
            html+='<div class="ui_pR color999 zone" data-id="'+v.id+'">\
                    <div style="width:100%;height:1.5rem"></div>';
            html+='<ul class="ui_refus f12">\
                         <li>拒绝理由</li>\
                         <li>\
                            <p style="margin-right:0" class="color333">'+v.remark+'</p>\
                         </li>\
                     </ul>';
            html+='<p style="text-align:left" class="margin0 f12">确定时间<span class="fr color333">'+unix_to_yearalldatetime(v.confirm_time)+'</span></p>\
                  </div>';
            html+='</div>';

        $('.ui_con').append(html);
      })
    }else{
        $('#nocommenttip3').removeClass('none')
    }
    }
    //改造时间戳
  function unix_to_yearalldatetime(unix) {
                                          var newDate = new Date();
                                              newDate.setTime(unix * 1000);
                                          var Y = newDate.getFullYear();
                                          var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
                                          var D = newDate.getDate() < 10 ? ('0' + newDate.getDate()) : newDate.getDate();
                                          var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
                                          var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
                                          var s = newDate.getSeconds() < 10 ? '0' + newDate.getSeconds() : newDate.getSeconds();
                                     return Y + '/' + M + '/' + D + ' ' + h + ':' + m + ':' + s;
  }
  function unix_to_pretime(unix) {
                                          var newDate = new Date();
                                              newDate.setTime(unix * 1000);
                                          var Y = newDate.getFullYear();
                                          var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
                                          var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
                                     return Y + '年' + M + '月' ;
     } 
  //跳转到合同详情页文本 
  $(document).on('click','.ui_contrack_detail',function(){
         var url=$(this).data('url');
         window.location.href = labUser.path +'js/agent/generic/web/viewer.html?file='+url;
  })
  //跳转到合同邀请函详情页
  $(document).on('click','.ui_contrack_middle,.zone',function(){
      var contract_id=$(this).data('id');
       window.location.href =labUser.path+'/webapp/client/pactdetails/_v020800?contract_id='+contract_id+'&uid='+agent_id; 
  })   
});//zepto外层
