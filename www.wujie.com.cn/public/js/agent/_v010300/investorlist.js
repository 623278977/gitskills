//By Hongky
	 new FastClick(document.body);
   var args=getQueryStringArgs(),
       brand_id = args['brand_id'],
       contract_id=args['contract_id'],  
       agent_id= args['agent_id'],
       uid=args['uid'];
       $('.containerBox').data('id',uid);
   var Investor={
             init(brand_id,agent_id,uid){
                  var param={};
                      param['brand_id'] = brand_id;
                      param['agent_id'] = agent_id;
                      param['uid'] = uid;
             var url = labUser.agent_path + '/customer/customer-list/_v010300';
             ajaxRequest(param, url, function(data) {
                if (data.status){
                              Investor.data(data.message);
                              $('.containerBox').removeClass('none');
                           }
                  })
           },
           data(obj){
                    $('.ui-brandname').text(obj.brand_name);
                    //投资客户
                    if(obj.intention_customer!=''){
                       var h='';
                       for(var i=0;i<obj.intention_customer.length;i++){
                          h+='<div class="fline choose_kehu" data-id="'+obj.intention_customer[i].uid+'" num="ad">\
                                <div class="investor ">\
                                  <p class="mr1"><img src="'+obj.intention_customer[i].avatar+'" class="avatar"/></p>\
                                  <div class="investorMes">';
                          h+='<p class=""><span class="f15 color333 mr05" >'+obj.intention_customer[i].nickname+'</span>';
                      if(obj.intention_customer[i].gender==0){
                          h+='<img src="/images/agent/girl.png" class="grade" /></p>';
                      }else if(obj.intention_customer[i].gender==1){
                          h+='<img src="/images/agent/boy.png" class="grade" /></p>';
                      }else if(obj.intention_customer[i].gender==-1){
                          h+='<img src="/images/agent/boy.png" class="grade none" /></p>';
                      }    
                          h+='<p class=""><span class="f12 color666">'+obj.intention_customer[i].zone+'</span><span class=""></span></p>\
                                  </div>\
                                </div>';
                         if(obj.intention_customer[i].selected){
                          h+='<img src="/images/agent/rightyellow.png" class="choosen "data-id="'+obj.intention_customer[i].uid+'"/>';
                         }else{
                          h+='<img src="/images/agent/rightyellow.png" class="choosen none" data-id="'+obj.intention_customer[i].uid+'"/>';
                         }   
                          h+='</div>'; 
                      }
                      $('.A').append(h);
                    }else{
                      $('.A').addClass('none').prev().addClass('none');
                    }
                   //普通客户
                   if(obj.normal_customer!=''){
                       var h='';
                       for(var i=0;i<obj.normal_customer.length;i++){
                          h+='<div class="fline choose_kehu" data-id="'+obj.normal_customer[i].uid+'" num="ad">\
                                <div class="investor ">\
                                  <p class="mr1"><img src="'+obj.normal_customer[i].avatar+'" class="avatar"/></p>\
                                  <div class="investorMes">';
                          h+='<p class=""><span class="f15 color333 mr05" >'+obj.normal_customer[i].nickname+'</span>';
                      if(obj.normal_customer[i].gender==0){
                          h+='<img src="/images/agent/girl.png" class="grade" /></p>';
                      }else if(obj.normal_customer[i].gender==1){
                          h+='<img src="/images/agent/boy.png" class="grade" /></p>';
                      }else if(obj.normal_customer[i].gender==-1){
                          h+='<img src="/images/agent/boy.png" class="grade none" /></p>';
                      }    
                          h+='<p class=""><span class="f12 color666">'+obj.normal_customer[i].zone+'</span><span class=""></span></p>\
                                  </div>\
                                </div>';
                      if(obj.normal_customer[i].selected){
                         h+='<img src="/images/agent/rightyellow.png" class="choosen" data-id="'+obj.normal_customer[i].uid+'"/>'; 
                      }else{
                         h+='<img src="/images/agent/rightyellow.png" class="choosen none" data-id="'+obj.normal_customer[i].uid+'"/>';
                       }   
                         h+='</div>'; 
                      }
                      $('.B').append(h);
                    }else{
                      $('.B').addClass('none').prev().addClass('none');
                    }
                  if(obj.normal_customer==''&&obj.intention_customer==''){
                    $('.article').addClass('none');
                    $('.nocomment').removeClass('none');
                  }
           }
    }
     Investor.init(brand_id,agent_id,uid);
    $(document).on('click','.choose_kehu',function(){
    	$(this).find('.choosen').removeClass('none');
    	$(this).siblings().find('.choosen').addClass('none');
    	$(this).parent('.chooseclient').siblings().find('.choosen').addClass('none');	
      var uid=$(this).data('id');
          $('.containerBox').data('id',uid);
      
    }) 
    //选择发送客户
    function sendClient(uid) {
    if (isiOS) {
           var message = {
                method:'sendClient',
                params:uid
            }; 
        window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
    } else if (isAndroid) {
           javascript:myObject.sendClient(uid);
    }
}  
  //确定按钮
 function confirmClient(){
           var id=$('.containerBox').data('id');
               if(id){
                     sendClient(id)
               }else{
                     tips('请选择客户')
            }          
        
   }
function tips(e) {
         $('.tips').text(e).removeClass('none');
        setTimeout(function() {
            $('.tips').addClass('none');
        }, 1500);
    }; 