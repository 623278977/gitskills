// Byhongky
Zepto(function(){
	 new FastClick(document.body);
   var args=getQueryStringArgs(),
       agent_id= args['agent_id'];

   var Test={
             init(agent_id){
                  var param={};
                      param['agent_id'] = agent_id;
                  var url = labUser.agent_path + '/temporary/answer-give-reds/_v010300';
             ajaxRequest(param, url, function(data) {
                if (data.status){
                         Test.data(data.message);
                          $('article').removeClass('none')
                        }
                  })
           },
           data(obj){
                     $('article').find('p').eq(0).html('品牌问答 ——'+obj.brand_name);
                     $('article').find('p').eq(1).html(obj.stem.dec+' '+obj.stem.stem);
                     var str='';
                     for(var i=0;i<obj.lists.length;i++){
                          str+='<div class="xuanti b f18 color666 mb2" data-id="'+obj.lists[i].options_num_id+'" data-titleid="'+obj.lists[i].temporary_activity_quiz_id+'">\
                                '+obj.lists[i].content+'\
                               </div>';    
                     }
                    $('.ui-test').html(str);
           },
           // 提交答题方法
           submit(agent_id,id,titleid){
                  var param={};
                      param['agent_id'] = agent_id;
                      param['options_num_id']=id;
                      param['temporary_activity_quiz_id']=titleid;
                  var url = labUser.agent_path + '/temporary/handle-agent-answer/_v010300';
                  ajaxRequest(param, url, function(data) {
                  if (data.status){
                            if(data.message.is_answer){
                            Test.successdata(data.message)
                             $('.bg-model').removeClass('none');
                             $('.right').removeClass('none');
                            }else{
                              Test.tips('回答正确，进入下一题');
                              Test.data(data.message); 
                            }    
                          }else{
                             $('.bg-model').removeClass('none');
                             $('.error').removeClass('none');
                            
                          }
                  })
           },
           //选题点击事件
           tips(e) {
                   $('.tips').text(e).removeClass('none');
                    setTimeout(function() {
                        $('.tips').addClass('none');
                    }, 1000);      
           },
           successdata(obj){
                      $('.name').html((obj.red_name.length<13?obj.red_name:obj.red_name.substr(0,13)+'…'));
                      $('.type').html(obj.red_support_type);
                      $('.time').html(unix_to_yeardate1(obj.red_expire_at));
                      $('.meony').html('￥'+obj.red_limit);
                      $('.meony2').html('满'+obj.min_consume+'减'+obj.red_limit);
           }
        }
     Test.init(agent_id);
    $(document).on('click','.xuanti',function(){
            var id=$(this).data('id'),
                titleid=$(this).data('titleid'),
                time=$('.game_time').text();
                if(time>0){
                    Test.submit(agent_id,id,titleid);
                    }else{
                    Test.tips('您已超时，不能答题');
                 }
      })
    //点击蒙层消失；
    $(document).on('click','.ui-size1',function(){
      $('.bg-model').addClass('none');
       window.location.href=labUser.path+'webapp/agent/dati/index/_v010300?agent_id='+agent_id;
    })
     $(document).on('click','.datitorromow',function(){
      window.location.href=labUser.path+'webapp/agent/dati/index/_v010300?agent_id='+agent_id;
    })
     function unix_to_yeardate1(unix) {
            var newDate = new Date();
                newDate.setTime(unix * 1000);
                var Y = newDate.getFullYear();
                var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
                var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
                return Y + '.' + M + '.' + D;
     }
    $(document).on('click','.lookfudai',function(){
        lookForward()
    })
  function lookForward() {
      if (isiOS) {
             var message = {
                  method:'lookForward'
              }; 
          window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
      } else if (isAndroid) {
          javascript:myObject.lookForward();
      }
    }
});
