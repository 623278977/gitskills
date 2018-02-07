Zepto(function (){
    new FastClick(document.body);
    var page=1;
    var page_size=100;
    var accept=0;
    var refuse=0;
    var again=0;
    var urlPath = window.location.href,
        args = getQueryStringArgs(),
        activity_id = args['id'],
        agent_id = args['agent_id'];
    var askDetail = {
        detail: function (agent_id,status) {
            var param = {};
                param["agent_id"] = agent_id;
                param['status']=status;
            var url=labUser.agent_path+'/user/contract-detail/_v010000';
            ajaxRequest(param, url, function (data) {
            if (data.status) {
              waitConfirmation(data.message)
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
                          <center><div class="ui_show_time">'+unix_to_pretime(v.created_at)+'</div></center>\
                   </div>';
            html+='<div class="ui_common_contrack  bgcolor add_ui1">';
            html+='<div class="f13 ui_contrack_top fline ui_pR">等待'+' '+(v.realname?v.realname:v.nickname)+' '+'确定'+' '+'['+(v.brand.length>7?v.brand.substr(0,7)+'…':v.brand)+']'+' '+'付款协议<span class="ffa300 fr">等待中</span></div>';
            html+='<div class="ui_contrack_middle fline ui_pR color666">\
                    <p style="text-align:left" class="margin07 f12">付款协议<span  class="fr">'+v.contract_title+'</span></p>\
                    <p style="text-align:left" class="margin07 f12 none">流水号<span class="fr">'+v.contract_no+'</span></p>\
                    <p style="text-align:left" class="margin07 f12">加盟品牌<span class="fr">'+v.brand+'</span></p>\
                    <div style="width:100%;height:0.5rem"></div>\
                  </div>';
            html+='<div class="ui_contrack_middle fline ui_pR color666">\
                    <p style="text-align:left" class="margin07 f12">加盟总费用<span class="fr">￥'+v.amount+'</span></p>';
            if(v.pre_pay){
              html+=' <p style="text-align:left" class="margin07 f12">线上首付<span class="fr">￥'+v.pre_pay+'</span></p>';
            }else{
              html+=' <p style="text-align:left" class="margin07 f12">线上首付<span class="fr">尚未支付</span></p>';
            }
            html+='<p style="text-align:left" class="margin07 f12">线下尾款<span class="fr">￥'+v.tail_pay+'</span></p>\
                    <p style="text-align:left" class="margin07 f12">缴纳方式<span class="fr">线上首付一次性结清</span></p>\
                    <p style="text-align:left" class="margin07  f12"><span class="fr">线下尾款银行转账</span></p>\
                    <div style="width:100%;height:1rem" class="clear"></div>\
                    <p style="text-align:left" class="margin07  f12 clear  way"><span class="fr ff">了解尾款补齐操作方法</span></p>\
                    <div style="width:100%;height:0.3rem" class="clear"></div>\
                  </div>';
            html+='<div class="ui_contrack_bottom fline ui_pR color666">\
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
                            <img class="ui_img7"  src="/images/rightjt.png">\
                          </li>\
                        </ul>\
                        <div style="width:100%;height:1.5rem"></div>\
                  </div>';
            html+='<div class="ui_pR color666">\
                      <div style="width:100%;height:1.8rem"></div>\
                      <p style="text-align:left" class="margin07 f12">邀请状态：\
                      <span><span class="ffa300">待确认</span><span class=" color333 padding">'+'(&nbsp'+v.leftover +'&nbsp)'+'</span></span> \
<span class="fr ui_send" data-id="'+v.id+'" data-amount="'+v.amount+'" data-uid="'+v.uid+'" data-title="'+v.contract_title+'" data-realname="'+v.realname+'" data-nickname="'+v.nickname+'">\
                        再次发送</span>\
                      </p>\
                    </div>\
                  </div>';
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
                                          return Y + '-' + M + '-' + D + ' ' + h + ':' + m+ ':'+s;
     }
    function unix_to_pretime(unix) {
                                        var newDate = new Date();
                                        newDate.setTime(unix * 1000);
                                        var Y = newDate.getFullYear();
                                        var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
                                        var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
                                        return Y + '年' + M + '月' + D + '日';
     }
   
    // 再次发送合同 
 function sendRichMsg(type,uType,uid,id,title,imgUrl,date,store,nickname,amount){
        if (isAndroid) {
            javascript:myObject.sendRichMsg(type,uType,uid,id,title,imgUrl,date,store,amount);
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
                'nickname':nickname,
                'amount':amount
                    };
            window.webkit.messageHandlers.sendRichMsg.postMessage(data);
        }
    }
    // 再次发送事件
    $(document).on('click','.ui_send',function(){
        var uid=$(this).data('uid'),
            id=$(this).data('id'),
            title=$(this).data('title'),
            imgUrl=labUser.path +"/images/020700/bargain2.png",
            realname=$(this).data('realname'),
            nickname=$(this).data('nickname'),
            amount=$(this).data('amount').toString();
            if(realname){
             sendRichMsg(3,'C',uid,id,title,imgUrl,'','',realname,amount)
            }else{
             sendRichMsg(3,'C',uid,id,title,imgUrl,'','',nickname,amount)  
            }
       
    })
    //点击查看合同文本
    $(document).on('click','.ui_contrack_detail',function(){
         var url=$(this).data('url');
         window.location.href = labUser.path +'js/agent/generic/web/viewer.html?file='+url;
    })
    $(document).on('click','.way',function(){
         window.location.href = labUser.path +'webapp/agent/way/detail';
    })
    $(document).ready(function(){
        $('body').css('background','#f2f2f2');
    })
});//zepto外层
