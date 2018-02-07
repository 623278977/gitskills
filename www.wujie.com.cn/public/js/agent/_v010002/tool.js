 // created byhongky
    new FastClick(document.body);
    var urlPath = window.location.href,
        args = getQueryStringArgs(),
        agent_id = args['agent_id'],
        customer_id= args['customer_id'],
        brand_id=args['brand_id'],
        one=0,
        two=0,
        three=0;
    function stampchange(unix){
                                var newDate = new Date();
                                    newDate.setTime(unix * 1000);
                                    var Y = newDate.getFullYear();
                                    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
                                    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
                                    var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
                                    var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
                                    var s = newDate.getSeconds() < 10 ? '0' + newDate.getSeconds() : newDate.getSeconds();
                                    return Y + '年' + M + '月' + D +'日'+ ' ' + h + ':' + m+ ':'+s;
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
    function unix_to_parttime(unix) {
                                    var newDate = new Date();
                                        newDate.setTime(unix * 1000);
                                        var Y = newDate.getFullYear();
                                        var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
                                        var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
                                        return Y + '年' + M + '月' + D + '日';
     }
    var track={
        activity:function(agent_id,customer_id,brand_id){
                    var param = {};
                        param["agent_id"] = agent_id;
                        param['customer_id']=customer_id;
                        param['brand_id']=brand_id;
                        var url=labUser.agent_path+'/customer/records-activity/_v010000';
                        ajaxRequest(param, url, function (data) {
                        if (data.status) {
                                            activity(data.message.activeList);
                                            one=data.message.total;
                                           $('#ui_track_').text(one);
                                          }//data.status
                                })// ajaxRequest
        },
        invest:function(agent_id,customer_id,brand_id){
                    var param = {};
                        param["agent_id"] = agent_id;
                        param['customer_id']=customer_id;
                        param['brand_id']=brand_id;
                        var url=labUser.agent_path+'/customer/records-inspect/_v010000';
                        ajaxRequest(param, url, function (data) {
                        if (data.status){
                                          invest(data.message.record_list);
                                          two=data.message.details;
                                          $('#ui_track_').text(one-1+two-1+2);
                                          }//data.status
                                })// ajaxRequest
        },
        bargain:function(agent_id,customer_id,brand_id){
                    var param = {};
                        param["agent_id"] = agent_id;
                        param['customer_id']=customer_id;
                        param['brand_id']=brand_id;
                        var url=labUser.agent_path+'/customer/records-contract/_v010000';
                        ajaxRequest(param, url, function (data) {
                        if (data.status){
                                         bargain(data.message.conreact);
                                         three=data.message.totals;
                                         $('#ui_track_').text((one-1)+(two-1)+(three-1)+3);
                                          }//data.status
                                })// ajaxRequest
        }
    };
    track.activity(agent_id,customer_id,brand_id);
    track.invest(agent_id,customer_id,brand_id);
    track.bargain(agent_id,customer_id,brand_id);
    function activity(obj){
        $.each(obj,function(k,v){
            if(v.status_info.status==0){
              var html='';
                  html+='<div class="ui_activity_wait ui-border-t">';
                  html+='<div class="ui_track_status clear no height4">\
                            <span class="color333 f12">活动邀请(确认中)</span>\
                            <span class="color666 f12 fr">'+stampchange(v.created_at)+'</span>\
                         </div>';
                  html+='<div class="ui_common">\
                            <ul class="ui_common_con">\
                                <li>\
                                    <img class="ui_img5"  src="'+v.activity_img+'">\
                                </li>\
                                <li>';
                 if(v.activity_title.length>19){
                  html+='<p style="text-align:left" class="f13 b">'+v.activity_title.substring(0,18)+'…'+'</p>';  
                 }else{
                  html+='<p style="text-align:left" class="f13 b">'+v.activity_title+'</p>';  
                  } 
                  html+=' <p style="text-align:left" class="margin07 f12">活动时间：'+stampchange(v.created_at)+'</p>\
                                    <p style="text-align:left" class="margin07 f12">活动地点：'+v.address+'</p>\
                                    <p style="text-align:left" class="margin07 f12">邀请状态：<span class="ffa300">待确认</span>\
                                        <span class="b" style="padding-left:1rem">'+v.status_info.remark+'</span>\
                                    </p>\
                                </li>\
                            </ul>\
                            <div style="width:100%;height:1rem"></div>\
                            <div class="ui_send_again">\
                                <button class="actInvitation" data-amount="'+v.amount+'" data-uid="'+v.uid+'" data-id="'+v.invite_id+'" data-title="'+v.activity_title+'" data-imgUrl="'+v.activity_img+'">再次发送</button>\
                            <button class="ui_upordown mt0 turnoff_act" data-id="'+v.invite_id+'">关闭</button>\
                            </div>\
                        </div>';
                   html+='</div>';
                $('.ui_activity').append(html);
            }else if(v.status_info.status==1){
                var html='';
                    html+='<div class="ui_activity_accept ui-border-t">';
                    html+='<div class="ui_track_status clear no height4">\
                                <span class="color333 f12">活动邀请(已接受)</span>\
                                <span class="color666 f12 fr">'+stampchange(v.created_at)+'</span>\
                           </div>';
                    html+='<div class="ui_common">\
                            <ul class="ui_common_con">\
                                <li>\
                                    <img class="ui_img5"  src="'+v.activity_img+'">\
                                </li>\
                                <li>';
                    if(v.activity_title.length>19){
                    html+='<p style="text-align:left" class="f13 b">'+v.activity_title.substring(0,18)+'…'+'</p>';  
                     }else{
                    html+='<p style="text-align:left" class="f13 b">'+v.activity_title+'</p>';  
                      } 
                    html+='<p style="text-align:left" class="margin07 f12">活动时间：'+stampchange(v.created_at)+'</p>\
                                    <p style="text-align:left" class="margin07 f12">活动地点：'+v.address+'</p>\
                                    <p style="text-align:left" class="margin07 f12">邀请状态：<span class="be74">已接受</span></p>\
                                    <p style="text-align:left" class="margin07 f12">确认时间：'+stampchange(v.confirm_time)+'</p>\
                                </li>\
                            </ul>\
                            <div style="width:100%;height:2rem"></div>\
                        </div>';
                    html+='</div>';
                     $('.ui_activity').append(html);
            }else if(v.status_info.status==-1){
                var html='';
                    html+='<div class="ui_activity_refuse ui-border-t">';
                    html+='<div class="ui_track_status clear no height4">\
                                <span class="color333 f12">活动邀请(已拒绝)</span>\
                                <span class="color666 f12 fr">'+stampchange(v.created_at)+'</span>\
                           </div>';
                    html+='<div class="ui_common">\
                            <ul class="ui_common_con">\
                                <li>\
                                    <img class="ui_img5"  src="'+v.activity_img+'">\
                                </li>\
                                <li>';
                    if(v.activity_title.length>19){
                    html+='<p style="text-align:left" class="f13 b">'+v.activity_title.substring(0,18)+'…'+'</p>';  
                     }else{
                    html+='<p style="text-align:left" class="f13 b">'+v.activity_title+'</p>';  
                      } 
                    html+='<p style="text-align:left" class="margin07 f12">活动时间：'+stampchange(v.created_at)+'</p>\
                                    <p style="text-align:left" class="margin07 f12">活动地点：'+v.address+'</p>\
                                    <p style="text-align:left" class="margin07 f12">邀请状态：<span class="fc6262">已拒绝</span></p>';
                    if(v.status_info.remark.length>16){
                      html+='<p style="text-align:left" class="margin07 f12">拒绝理由：<span class="color333 b f12">'+v.status_info.remark.substring(0,16)+'…'+'</span></p>';  
                    }else{
                      html+='<p style="text-align:left" class="margin07 f12">拒绝理由：<span class="color333 b f12">'+v.status_info.remark+'</span></p>';
  
                    }    
                    html+='<p style="text-align:left" class="margin07 f12">确认时间：'+stampchange(v.confirm_time)+'</p>\
                                </li>\
                            </ul>\
                            <div style="width:100%;height:4.5rem"></div>\
                        </div>';
                    html+='</div>';
                    $('.ui_activity').append(html);
            }else if(v.status_info.status==-2){
                var html='';
                    html+='<div class="ui_activity_refuse ui-border-t">';
                    html+='<div class="ui_track_status clear no height4">\
                                <span class="color333 f12">活动邀请(已拒绝)</span>\
                                <span class="color666 f12 fr">'+stampchange(v.created_at)+'</span>\
                           </div>';
                    html+='<div class="ui_common">\
                            <ul class="ui_common_con">\
                                <li>\
                                    <img class="ui_img5"  src="'+v.activity_img+'">\
                                </li>\
                                <li>';
                    if(v.activity_title.length>19){
                    html+='<p style="text-align:left" class="f13 b">'+v.activity_title.substring(0,18)+'…'+'</p>';  
                     }else{
                    html+='<p style="text-align:left" class="f13 b">'+v.activity_title+'</p>';  
                      } 
                    html+='<p style="text-align:left" class="margin07 f12">活动时间：'+stampchange(v.created_at)+'</p>\
                                    <p style="text-align:left" class="margin07 f12">活动地点：'+v.address+'</p>\
                                    <p style="text-align:left" class="margin07 f12">邀请状态：<span class="fc6262">已拒绝</span></p>';
                    html+='<p style="text-align:left" class="margin07 f12">拒绝理由：<span class="fc6262">已过期</span></p>';
                    html+='<p style="text-align:left" class="margin07 f12">确认时间：'+stampchange(v.confirm_time)+'</p>\
                                </li>\
                            </ul>\
                            <div style="width:100%;height:4.5rem"></div>\
                        </div>';
                    html+='</div>';
                    $('.ui_activity').append(html);    
            }
        })
    }
    function invest(obj){
        $.each(obj,function(k,v){
           if(v.status==0){
              var html='';
                  html+='<div class="ui_invest_wait ui-border-t">';
                  html+='<div class="ui_track_status clear no height4">\
                            <span class="color333 f12">考察邀请函(确认中)</span>\
                            <span class="color666 f12 fr">'+stampchange(v.created_at)+'</span>\
                        </div>';
                  html+='<div class="ui_common">\
                            <ul class="ui_common_con">\
                                <li>\
                                    <img class="ui_img5"  src=" '+v.brand_logo+'">\
                                </li>\
                                <li>\
                                    <p style="text-align:left" class="f13 b">'+v.brand_title+'</p>\
                                    <p style="text-align:left" class="margin07 f12">考察门店：'+v.inspect_store_name+'</p>\
                                    <p style="text-align:left" class="margin07 f12">所在地区：'+v.head_address+'</p>\
                                    <p style="text-align:left" class="margin07 f12">详细地址：'+(v.inspect_address.length>15?v.inspect_address.substr(0,15)+'…':v.inspect_address)+'</p>\
                                    <p style="text-align:left" class="margin07 f12">考察时间：'+unix_to_parttime(v.inspect_time)+'</p>\
                                    <p style="text-align:left" class="margin07 f12">定金金额：'+v.currency+'元整人民币</p>\
                                    <p style="text-align:left" class="margin07 f12">邀请状态：<span class="ffa300">待确认</span>\
                                        <span class="b" style="padding-left:1rem">'+v.undetermined_time+'</span>\
                                    </p>\
                                </li>\
                            </ul>\
                            <div style="width:100%;height:8.5rem"></div>\
                            <div class="ui_send_again" >\
                                <button class="investInvitation" data-amount="'+v.amount+'" data-uid="'+v.uid+'" data-id="'+v.inspect_id+'"  data-title="'+v.brand_title+'" data-imgUrl="'+v.brand_logo+'" data-date="'+v.inspect_time+'" data-store="'+v.inspect_store_name+'">再次发送</button>\
                                <button class="ui_upordown mt0 turnoff_invest" data-id="'+v.inspect_id+'">关闭</button>\
                            </div>\
                        </div>';
                  html+='</div>';
                  $('.ui_invest').append(html);
           }else if(v.status==1){
                 var html='';
                     html+='<div class="ui_invest_accept ui-border-t">';
                     html+='<div class="ui_track_status clear no height4">\
                                <span class="color333 f12">考察邀请函(已接受)</span>\
                                <span class="color666 f12 fr">'+stampchange(v.created_at)+'</span>\
                            </div>';
                     html+='<div class="ui_common">\
                                <ul class="ui_common_con">\
                                    <li>\
                                        <img class="ui_img5"  src=" '+v.brand_logo+'">\
                                    </li>\
                                    <li>\
                                        <p style="text-align:left" class="f13 b">'+v.brand_title+'</p>\
                                        <p style="text-align:left" class="margin07 f12">考察门店：'+v.inspect_store_name+'</p>\
                                        <p style="text-align:left" class="margin07 f12">所在地区：'+v.head_address+'</p>\
                                        <p style="text-align:left" class="margin07 f12">详细地址：'+(v.inspect_address.length>15?v.inspect_address.substr(0,15)+'…':v.inspect_address)+'</p>\
                                        <p style="text-align:left" class="margin07 f12">考察时间：'+unix_to_parttime(v.inspect_time)+'</p>\
                                        <p style="text-align:left" class="margin07 f12">定金金额：'+v.currency+'元整人民币</p>\
                                        <p style="text-align:left" class="margin07 f12">支付方式：'+v.pay_way+'</p>\
                                        <p style="text-align:left" class="margin07 f12">邀请状态：<span class="be74">已接受</span></p>\
                                        <p style="text-align:left" class="margin07 f12">确认时间：'+stampchange(v.confirm_time)+'</p>\
                                    </li>\
                                </ul>\
                                <div style="width:100%;height:11.8rem"></div>\
                            </div>';
                     html+='</div>';
                      $('.ui_invest').append(html);
           }else if(v.status==-1){
                var html='';
                    html+='<div class="ui_invest_refuse ui-border-t">';
                    html+='<div class="ui_track_status clear no height4">\
                                <span class="color333 f12">考察邀请函(已拒绝)</span>\
                                <span class="color666 f12 fr">'+stampchange(v.created_at)+'</span>\
                           </div>';
                    html+='<div class="ui_common" >\
                            <ul class="ui_common_con">\
                                <li>\
                                    <img class="ui_img5"  src="'+v.brand_logo+'">\
                                </li>\
                                <li>\
                                    <p style="text-align:left" class="f13 b">'+v.brand_title+'</p>\
                                    <p style="text-align:left" class="margin07 f12">考察门店：'+v.inspect_store_name+'</p>\
                                    <p style="text-align:left" class="margin07 f12">所在地区：'+v.head_address+'</p>\
                                    <p style="text-align:left" class="margin07 f12">详细地址：'+(v.inspect_address.length>15?v.inspect_address.substr(0,15)+'…':v.inspect_address)+'</p>\
                                    <p style="text-align:left" class="margin07 f12">考察时间：'+unix_to_parttime(v.inspect_time)+'</p>\
                                    <p style="text-align:left" class="margin07 f12">定金金额：'+v.currency+'元整人民币</p>\
                                    <p style="text-align:left" class="margin07 f12">邀请状态：<span class="fc6262">已拒绝</span></p>\
                                    <p style="text-align:left" class="margin07 f12">确认时间：'+stampchange(v.confirm_time)+'</p>\
                                </li>\
                            </ul>\
                            <div style="width:100%;height:9.8rem"></div>\
                         </div>';
                    html+='</div>';
                     $('.ui_invest').append(html);
           }else if(v.status==2||v.status==4){
                 var html='';
                     html+='<div class="ui_invest_accept ui-border-t">';
                     html+='<div class="ui_track_status clear no height4">\
                                <span class="color333 f12">考察邀请函(已接受)</span>\
                                <span class="color666 f12 fr">'+stampchange(v.created_at)+'</span>\
                            </div>';
                     html+='<div class="ui_common">\
                                <ul class="ui_common_con">\
                                    <li>\
                                        <img class="ui_img5"  src=" '+v.brand_logo+'">\
                                    </li>\
                                    <li>\
                                        <p style="text-align:left" class="f13 b">'+v.brand_title+'</p>\
                                        <p style="text-align:left" class="margin07 f12">考察门店：'+v.inspect_store_name+'</p>\
                                        <p style="text-align:left" class="margin07 f12">所在地区：'+v.head_address+'</p>\
                                        <p style="text-align:left" class="margin07 f12">详细地址：'+(v.inspect_address.length>15?v.inspect_address.substr(0,15)+'…':v.inspect_address)+'</p>\
                                        <p style="text-align:left" class="margin07 f12">考察时间：'+unix_to_parttime(v.inspect_time)+'</p>\
                                        <p style="text-align:left" class="margin07 f12">定金金额：'+v.currency+'元整人民币</p>\
                                        <p style="text-align:left" class="margin07 f12">支付方式：'+v.pay_way+'</p>\
                                        <p style="text-align:left" class="margin07 f12">邀请状态：<span class="be74">已接受</span></p>\
                                        <p style="text-align:left" class="margin07 f12">确认时间：'+stampchange(v.confirm_time)+'</p>\
                                    </li>\
                                </ul>\
                                <div style="width:100%;height:11.8rem"></div>\
                            </div>';
                     html+='</div>';
                      $('.ui_invest').append(html);
           }else if(v.status==3){
                 var html='';
                     html+='<div class="ui_invest_accept ui-border-t">';
                     html+='<div class="ui_track_status clear no height4">\
                                <span class="color333 f12">考察邀请函(已接受)</span>\
                                <span class="color666 f12 fr">'+stampchange(v.created_at)+'</span>\
                            </div>';
                     html+='<div class="ui_common">\
                                <ul class="ui_common_con">\
                                    <li>\
                                        <img class="ui_img5"  src=" '+v.brand_logo+'">\
                                    </li>\
                                    <li>\
                                        <p style="text-align:left" class="f13 b">'+v.brand_title+'</p>\
                                        <p style="text-align:left" class="margin07 f12">考察门店：'+v.inspect_store_name+'</p>\
                                        <p style="text-align:left" class="margin07 f12">所在地区：'+v.head_address+'</p>\
                                        <p style="text-align:left" class="margin07 f12">详细地址：'+(v.inspect_address.length>15?v.inspect_address.substr(0,15)+'…':v.inspect_address)+'</p>\
                                        <p style="text-align:left" class="margin07 f12">考察时间：'+unix_to_parttime(v.inspect_time)+'</p>\
                                        <p style="text-align:left" class="margin07 f12">定金金额：'+v.currency+'元整人民币</p>\
                                        <p style="text-align:left" class="margin07 f12">支付方式：'+v.pay_way+'</p>\
                                        <p style="text-align:left" class="margin07 f12">邀请状态：<span class="be74">已接受</span></p>\
                                        <p style="text-align:left" class="margin07 f12">确认时间：'+stampchange(v.confirm_time)+'</p>\
                                    </li>\
                                </ul>\
                                <div style="width:100%;height:11.8rem"></div>\
                            </div>';
                     html+='</div>';
                      $('.ui_invest').append(html);
           }
        })
    }
    function bargain(obj){
        $.each(obj,function(k,v){
            if(v.status==0){
                var html='';
                    html+='<div class="ui_contrack_wait ui-border-t">';
                    html+='<div class="ui_track_status clear no height4">\
                                <span class="color333 f12">付款协议(确认中)</span>\
                                <span class="color666 f12 fr">'+stampchange(v.created_at)+'</span>\
                            </div>';
                    html+='<div class="ui_common_contrack">\
                            <div class="f13 ui_contrack_top ui-border-b">\
                               '+v.realname+'签署['+(v.brand.length>12?v.brand.substr(0,12)+'…':v.brand)+']付款协议确认中\
                                <span class="ffa300 fr">等待中</span>\
                            </div>\
                            <div class="ui_contrack_middle ui-border-b">\
                                <p style="text-align:left" class="margin07 f12">付款协议<span class="fr">'+v.contract_title+'</span></p>\
                                <p style="text-align:left" class="margin07 f12">加盟品牌<span class="fr">'+v.brand+'</span></p>\
                                <div style="width:100%;height:0.5rem"></div>\
                            </div>\
                            <div class="ui_contrack_bottom">\
                                <p style="text-align:left" class="margin07 f12">加盟总费用<span class="fr">￥'+v.amount+'</span></p>\
                                <div style="width:100%;height:0.3rem;clear:both "></div>\
                                <p style="text-align:left" class="margin07 f12  way none"><span class="fr ff">了解尾款补齐操作方式</span></p>\
                                <p style="text-align:left" class="margin07 f12 clear">合同文本</p>\
                                <ul class="ui_contrack_detail" data-url="'+v.address+'">\
                                    <li>\
                                        <img class="ui_img6"  src="/images/020700/bargain2.png">\
                                    </li>\
                                    <li>\
                                        <p class="f14 b textleft color333 margin05">'+v.brand+'加盟电子合同</p>\
                                    </li>\
                                    <li>\
                                        <img class="ui_img7"  src="/images/020700/m9.png">\
                                    </li>\
                                </ul>\
                            </div>';
                    html+='<div class="color333">\
                             <div style="width:100%;height:1.8rem"></div>\
                                <p style="text-align:left" class="margin07 f12">缴纳状态：\
                                    <span><span class="ffa300">待确认</span><span class="b color333 padding">'+v.leftover+'</span></span><span class="fr ui_upordown turnoff_contract" data-id="'+v.id+'">关闭</span> \
                                    <span class="fr ui_send bargainInvitation " data-amount="'+v.amount+'" data-uid="'+v.uid+'" data-id="'+v.id+'" data-league_type="'+v.league_type+'"   data-title="'+v.contract_title+'">再次发送</span>\
                                </p>\
                            </div>';
                    html+='</div>';
                    html+='</div>';
                    $('.ui_contrack').append(html);
            }else if(v.status==6){
                   var html='';
                    html+='<div class="ui_contrack_wait ui-border-t">';
                    html+='<div class="ui_track_status clear no height4">\
                                <span class="color333 f12">付款协议(确认中)</span>\
                                <span class="color666 f12 fr">'+stampchange(v.created_at)+'</span>\
                            </div>';
                    html+='<div class="ui_common_contrack">\
                            <div class="f13 ui_contrack_top ui-border-b">\
                               '+v.realname+'签署['+(v.brand.length>12?v.brand.substr(0,12)+'…':v.brand)+']付款协议确认中\
                                <span class="ffa300 fr">等待中</span>\
                            </div>\
                            <div class="ui_contrack_middle ui-border-b">\
                                <p style="text-align:left" class="margin07 f12">付款协议<span class="fr">'+v.contract_title+'</span></p>\
                                <p style="text-align:left" class="margin07 f12">加盟品牌<span class="fr">'+v.brand+'</span></p>\
                                <div style="width:100%;height:0.5rem"></div>\
                            </div>\
                            <div class="ui_contrack_bottom">\
                                <p style="text-align:left" class="margin07 f12">加盟总费用<span class="fr">￥'+v.amount+'</span></p>\
                                <div style="width:100%;height:0.3rem;clear:both "></div>\
                                <p style="text-align:left" class="margin07 f12  way none"><span class="fr ff">了解尾款补齐操作方式</span></p>\
                                <p style="text-align:left" class="margin07 f12 clear">合同文本</p>\
                                <ul class="ui_contrack_detail" data-url="'+v.address+'">\
                                    <li>\
                                        <img class="ui_img6"  src="/images/020700/bargain2.png">\
                                    </li>\
                                    <li>\
                                        <p class="f14 b textleft color333 margin05">'+v.brand+'加盟电子合同</p>\
                                    </li>\
                                    <li>\
                                        <img class="ui_img7"  src="/images/020700/m9.png">\
                                    </li>\
                                </ul>\
                            </div>';
                    html+='<div class="color333">\
                             <div style="width:100%;height:1.8rem"></div>\
                                <p style="text-align:left" class="margin07 f12">缴纳状态：\
                                    <span><span class="ffa300">等待支付完成</span><span class="b color333 padding none">'+v.leftover+'</span></span><span class="fr ui_upordown turnoff_contract none" data-id="'+v.id+'">关闭</span> \
                                    <span class="fr ui_send bargainInvitation none" data-amount="'+v.amount+'" data-uid="'+v.uid+'" data-id="'+v.id+'" data-league_type="'+v.league_type+'"   data-title="'+v.contract_title+'">再次发送</span>\
                                </p>\
                            </div>';
                    html+='</div>';
                    html+='</div>';
                    $('.ui_contrack').append(html);
            }else if(v.status==1){
                    var html='';
                        html+='<div class="ui_contrack_accept ui-border-t">';
                        html+='<div class="ui_track_status clear no height4">\
                                    <span class="color333 f12">付款协议(已接受)</span>\
                                    <span class="color666 f12 fr">'+stampchange(v.created_at)+'</span>\
                                </div>'; 
                        html+='<div class="ui_common_contrack">';
                        html+='<div class="f13 ui_contrack_top ui-border-b">\
                                  '+v.realname+'确定签署['+(v.brand.length>12?v.brand.substr(0,12)+'…':v.brand)+']付款协议\
                                 <span class="be74 fr">交易成功</span>\
                               </div>';
                        html+='<div class="ui_contrack_middle">\
                                <p style="text-align:left" class="margin07 f12">付款协议<span class="fr">'+v.contract_title+'</span></p>\
                                <p style="text-align:left" class="margin07 f12">流水号<span class="fr">'+v.contract_no+'</span></p>\
                                <p style="text-align:left" class="margin07 f12">加盟品牌<span class="fr">'+v.brand+'</span></p>\
                                <div style="width:100%;height:0.5rem"></div>\
                               </div>';
                        html+='<div class="ui_contrack_bottom">\
                                <p style="text-align:left" class="margin07 f12">合同文本</p>\
                                     <ul class="ui_contrack_detail" data-url="'+v.address+'">\
                                        <li>\
                                        <img class="ui_img6"  src="/images/020700/bargain2.png">\
                                        </li>\
                                         <li>\
                                         <p class="f14 b textleft color333 margin05">'+v.brand+'加盟电子合同</p>\
                                         <p class="f11 textleft color666 none">合同编号:'+v.contract_no+'</p>\
                                        </li>\
                                        <li>\
                                        <img class="ui_img7"  src="/images/020700/m9.png">\
                                        </li>\
                                    </ul>\
                               </div>';
                        html+='</div></div>';
                    $('.ui_contrack').append(html);
            }else if(v.status==-1){
                    var html='';
                        html+='<div class="ui_contrack_refuse ui-border-t">';
                        html+='<div class="ui_track_status clear no height4">\
                                    <span class="color333 f12">付款协议(已拒绝)</span>\
                                    <span class="color666 f12 fr">'+stampchange(v.created_at)+'</span>\
                             </div>';
                        html+='<div class="ui_common_contrack">';
                        html+='<div class="f13 ui_contrack_top ui-border-b">\
                                 '+v.realname+'拒绝['+(v.brand.length>12?v.brand.substr(0,12)+'…':v.brand)+']付款协议\
                                 <span class="fc6262 fr">加盟失败</span>\
                               </div>';
                        html+='<div class="ui_contrack_middle ui-border-b">\
                                   <p style="text-align:left" class="margin07 f12">付款协议<span class="fr">'+v.contract_title+'</span></p>\
                                   <p style="text-align:left" class="margin07 f12">加盟品牌<span class="fr">'+v.brand+'</span></p>\
                                   <div style="width:100%;height:1.5rem"></div>\
                               </div>';
                        html+='<div class="ui_contrack_bottom">\
                                <p style="text-align:left" class="margin07 f12">加盟费用<span class="fr">￥'+v.amount+'</span></p>\
                                <p style="text-align:left" class="margin07 f12">合同文本</p>\
                                <ul class="ui_contrack_detail" data-url="'+v.address+'">\
                                   <li>\
                                   <img class="ui_img6"  src="/images/020700/bargain2.png">\
                                  </li>\
                                  <li>\
                                    <p class="f14 b textleft color333 margin05">'+v.brand+'加盟电子合同</p>\
                                 </li>\
                                 <li>\
                                  <img class="ui_img7"  src="/images/020700/m9.png">\
                                 </li>\
                               </ul>\
                            </div>';
                        html+='</div></div>';
                    $('.ui_contrack').append(html);
            }else if(v.status==2||v.status==4||v.status==5){
                    var html='';
                        html+='<div class="ui_contrack_accept ui-border-t">';
                        html+='<div class="ui_track_status clear no height4">\
                                    <span class="color333 f12">付款协议(已接受)</span>\
                                    <span class="color666 f12 fr">'+stampchange(v.created_at)+'</span>\
                                </div>'; 
                        html+='<div class="ui_common_contrack">';
                        html+='<div class="f13 ui_contrack_top ui-border-b">\
                                  '+v.realname+'确定签署['+(v.brand.length>12?v.brand.substr(0,12)+'…':v.brand)+']付款协议\
                                 <span class="be74 fr">交易成功</span>\
                               </div>';
                        html+='<div class="ui_contrack_middle">\
                                <p style="text-align:left" class="margin07 f12">付款协议<span class="fr">'+v.contract_title+'</span></p>\
                                <p style="text-align:left" class="margin07 f12">流水号<span class="fr">'+v.contract_no+'</span></p>\
                                <p style="text-align:left" class="margin07 f12">加盟品牌<span class="fr">'+v.brand+'</span></p>\
                                <div style="width:100%;height:0.5rem"></div>\
                               </div>';
                        html+='<div class="ui_contrack_bottom">\
                                <p style="text-align:left" class="margin07 f12">合同文本</p>\
                                     <ul class="ui_contrack_detail" data-url="'+v.address+'">\
                                        <li>\
                                        <img class="ui_img6"  src="/images/020700/bargain2.png">\
                                        </li>\
                                         <li>\
                                         <p class="f14 b textleft color333 margin05">'+v.brand+'加盟电子合同</p>\
                                         <p class="f11 textleft color666 none">合同编号:'+v.contract_no+'</p>\
                                        </li>\
                                        <li>\
                                        <img class="ui_img7"  src="/images/020700/m9.png">\
                                        </li>\
                                    </ul>\
                               </div>';
                        html+='</div></div>';
                    $('.ui_contrack').append(html);
            }
        })
    }

 //再次发送调用
   function sendRichMsg(type,uType,uid,id,title,imgUrl,date,store,amount,league_type){
        if (isAndroid) {
            javascript:myObject.sendRichMsg(type,uType,uid,id,title,imgUrl,date,store,amount,league_type);
        }else if(isiOS){
            var data={
                'type':type,
                'uType':uType,
                'uid':uid,
                'id':id,
                'title':title,
                'imgUrl':imgUrl,
                'date':date,
                'store':store,
                'amount':amount,
                'contract_joinType':league_type
                    };
            window.webkit.messageHandlers.sendRichMsg.postMessage(data);
        }
    }
//再次发送活动邀请函
 $(document).on('click','.actInvitation',function(){
    var uid=$(this).data('uid'),
        id=$(this).data('id'),
        title=$(this).data('title'),
        imgUrl=$(this).data('imgurl'),
        amount=$(this).data('amount').toString();    
  sendRichMsg(1,'C',uid,id,title,imgUrl,'','',amount,'');
 })
 //再次发送考察邀请函
 $(document).on('click','.investInvitation',function(){
    var uid=$(this).data('uid'),
        id=$(this).data('id'),
        title=$(this).data('title'),
        imgUrl=$(this).data('imgurl'),
        date=$(this).data('date'),
        store=$(this).data('store'),
        amount=$(this).data('amount').toString();
    sendRichMsg(2,'C',uid,id,title,imgUrl,date,store,amount,'');
 })
 //再次发送合同
  $(document).on('click','.bargainInvitation',function(){
    var uid=$(this).data('uid'),
        id=$(this).data('id'),
        title=$(this).data('title'),
        imgUrl=labUser.path +"/images/020700/bargain2.png",
        amount=$(this).data('amount').toString(),
        league_type=$(this).data('league_type');
    sendRichMsg(3,'C',uid,id,title,imgUrl,'','',amount,league_type);
    //点击进入合同详情页  
 })
$(document).on('click','.ui_contrack_detail',function(){
         var url=$(this).data('url');
         window.location.href = labUser.path +'js/agent/generic/web/viewer.html?file='+url;
})
$(document).on('click','.way',function(){
         window.location.href = labUser.path +'webapp/agent/way/detail';
    })
//关闭等待的活动邀请函;
$(document).on('click','.turnoff_act',function(){
             var id=$(this).data('id');
             shutUp(id);
    })
//关闭等待的考察邀请函;
$(document).on('click','.turnoff_invest',function(){
             var id=$(this).data('id');
             shutUp(id);
    })
//关闭等待的合同邀请函;
$(document).on('click','.turnoff_contract',function(){
    var id=$(this).data('id');
    shutUpcontract(id);
})
  //关闭活动、考察邀请函待确定状态邀请函方法
  function shutUp(invite_id){
            var param={};
                param['invite_id']=invite_id;
             var url=labUser.agent_path+'/user/back-out/_v010002';
             ajaxRequest(param, url, function (data) {
                   if(data.status){
                         if(data.message=='ok'){
                            tips('关闭成功');
                         }
                        
                   }else{
                          tips(data.message);
                   }
            })
  }

   //关闭合同邀请函等待状态邀请函方法
  function shutUpcontract(contract_id){
            var param={};
                param['contract_id']=contract_id;
             var url=labUser.agent_path+'/contract/revoke';
             ajaxRequest(param, url, function (data) {
                   if(data.status){
                         if(data.message=='撤回成功!'){
                            tips('关闭成功')
                         }
                        
                   }else{
                          tips(data.message);
                   }
            })
  }
  function tips(e) {
         $('.tips').text(e).removeClass('none');
        setTimeout(function() {
            $('.tips').addClass('none');
        }, 1500);

    }; 
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
// 跳转订单详情页
$(document).on('click','.ui-pay-contract',function(){
        var order_no=$(this).data('order_no');
            checkMyorder(order_no);
          })