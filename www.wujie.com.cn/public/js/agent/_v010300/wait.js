//By Hongky
Zepto(function(){
	 new FastClick(document.body);
   var args=getQueryStringArgs(),
       agent_id= args['agent_id'],
       uid=args['uid'];
   var Customer={
             init:function(agent_id){
                  var param={};
                      param['agent_id'] = agent_id;
             var url = labUser.agent_path + '/customer/wait-confirm/_v010300';
             ajaxRequest(param, url, function(data) {
                if (data.status){
                             Customer.data(data.message);
                             $('#act_container').removeClass('none');
                              $('.loader').addClass('none');
                              $('.schmenu').addClass('none')
                           }else{
                              $('.loader').addClass('none');
                              $('.schmenu').addClass('none')
                           }
                  })
           },
           data:function(obj){
                    if(obj!=''){
                          var html='';
                          for(var i=0;i<obj.length;i++){
                              html+='<div class="ui-contain">\
                                       <div class="ui-title fline color666 f15">\
                                         <span class="fl">'+obj[i].brand_info.name+'</span>\
                                         <span class="fr color999 f12">创建时间：'+obj[i].created_at+'</span>\
                                       </div>\
                                       <ul class="ui-three color666 f13">\
                                           <li class="fline">\
                                               <p class="ui-line-height">目标投资人</p>\
                                               <ul class="ui-pict-text">\
                                                    <li class="pl10" style="width:23%">\
                                                      <img class="ui-length" src="'+obj[i].customer_info.avatar+'"/>\
                                                    </li>\
                                                    <li >\
                                                      <p class="text-align b f15 color666">'+(obj[i].customer_info.nickname.length<6?obj[i].customer_info.nickname:obj[i].customer_info.nickname.substr(0,6)+'…')+'</p>\
                                                      <p class="text-align">'+obj[i].customer_info.zone+'</p>\
                                                    </li>\
                                                    <li>\
                                                      <button class="ui-pay ui-add gochat" data_id="'+obj[i].customer_info.uid+'" data_nickname="'+obj[i].customer_info.nickname+'">与他聊聊</button>\
                                                    </li>\
                                               </ul>\
                                               <div class="clear ui-style"></div>\
                                           </li>\
                                           <li class="fline">\
                                               <p class="ui-line-height">目标品牌</p>\
                                               <ul class="ui-pict-text">\
                                                    <li class="pl10" style="width:23%">\
                                                      <img class="ui-length2" src="'+obj[i].brand_info.logo+'"/>\
                                                    </li>\
                                                    <li class="width1">\
                                                      <p class="text-align2 b f14 color666">'+(obj[i].brand_info.name.length<10?obj[i].brand_info.name:obj[i].brand_info.name.substr(0,10)+'…')+'</p>\
                                                      <p class="text-align2 f10">'+obj[i].brand_info.slogan+'</p>\
                                                      <p class="text-align2 f12 ">行业分类：<span class="color333">'+obj[i].brand_info.category+'</span></p>\
                                                    </li>\
                                               </ul>\
                                               <div class="clear ui-style"></div>\
                                           </li>\
                                           <li class="fline">\
                                                 <p class="ui-line-height">电子合同状态</p>\
                                                 <ul class="ui-pict-text">\
                                                      <li class="width2 color999 text-align4">\
                                                        <p class="color333 f15 b k">等待对方确认</p>\
                                                        <p class="k">等待对方确认耗时：<span class="color333 f12 b">'+obj[i].wait_time+'</span></p>\
                                                        <p class="k">请尽快让投资人确定邀请函，避免邀请函过期处理。</p>\
                                                      </li>\
                                                 </ul>\
                                               <div class="clear ui-style"></div>\
                                           </li>\
                                       </ul>\
                                        <ul class="ui-three color666 f13  none">\
                                           <li>\
                                                 <p class="ui-line-height">加盟方案</p>\
                                                 <ul class="ui-pict-text">\
                                                      <li class="width2 color999 text-align4" style="padding-right: 1rem">\
                                                        <p class="color333 f12 ">加盟方案A<span class="fr">'+obj[i].contract_info.name+'</span></p>\
                                                        <p class="color333 f12 ">加盟类型<span class="fr">'+obj[i].contract_info.league_type+'</span></p>\
                                                        <p class="color333 f12 ">总费用<span class="fr fd">￥'+obj[i].contract_info.total_cost+'</span></p>\
                                                        <div class="ui-grey  f11">\
                                                             <ul class="ui-pay-detail">\
                                                                 <li class="color666">费用明细</li>\
                                                                 <li >';
                                                                  var str='';
                                                                      for(var t=0;t<obj[i].contract_info.cost_details.length;t++){
                                                                        str+='<p class="color999">'+obj[i].contract_info.cost_details[t].cost_type+'：￥'+obj[i].contract_info.cost_details[t].cost
                                                                      }
                                                                html+=str;
                                                                html+='</li>\
                                                             </ul>\
                                                             <p class="color666 clear">最高提成<span class="fr  f8">可提成佣金部分 '+obj[i].contract_info.max_commission+'</span></p>\
                                                              <div style="width:100%;height:0.5rem;clear: both;"></div> \
                                                             <p class="color666 A" data-address="'+obj[i].contract_info.address+'">合同/文件<span class="fr  fe">《品牌加盟付款协议》</span></p>\
                                                             <p class="color666 none"><span class="fr  fe">《品牌加盟合同》</span></p>\
                                                               <div style="width:100%;height:0.5rem;clear: both;"></div>\
                                                             <p class="color999 ">* 如款项存在修改幅度，请联系商务对其进行修改。</p>\
                                                             <p class="color999 "> * 加盟合同将安排线下签约，实际成交按款项交齐为准。</p>\
                                                             <p class="color999 "> * 佣金结算以可提成佣金部分乘以提成比例进行计算。</p>  \
                                                             <p class="color999 "> *  对加盟方案存在疑问，请联系商圈客服人员。</p>  \
                                                             <p class="color999 "> *  无界商圈保持最终解释权。</p>      \
                                                        </div>\
                                                      </li>\
                                                 </ul>\
                                               <div class="clear">\
                                          </li>\
                                       </ul>\
                                        <div class="ui-strch f15  clear ">展开<span style="padding-left: 1rem"><img class="down" src="/images/agent/1/down.png"><span></div>\
                                 </div>';   
                          }
                        $('article').append(html);

                    }else{
                        $('article').addClass('none');
                        $('#nocommenttip3').removeClass('none');
                    }
                    
           }
        }
     Customer.init(agent_id);
     $(document).on('click','.A',function(){
         var url=$(this).data('address');
         window.location.href = labUser.path +'js/agent/generic/web/viewer.html?file='+url;
     })
});