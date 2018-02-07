// Byhongky
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
                          if(data.message.is_answer==1){
                            Test.tips('您今天已经答过题了，明天再来吧');
                          }else{
                             window.location.href=labUser.path+'webapp/agent/dati/test/_v010300?agent_id='+agent_id;
                          }
                         
                        }
                  })
           },
           tips(e) {
                   $('.tips').text(e).removeClass('none');
                    setTimeout(function() {
                        $('.tips').addClass('none');
                    }, 1500);      
           },
           agentlist(){
                  var param={};
                  var url = labUser.agent_path + '/temporary/return-get-answer-red-use-datas/_v010300';
             ajaxRequest(param, url, function(data) {
                if (data.status){
                       Test.data(data.message);  
                       // $('.container').removeClass('none')         
                             }
                  })
           },
           data(obj){
                     var swiper = new Swiper('.swiper-container', {
                        pagination: '.swiper-pagination',
                        paginationClickable: true,
                        direction: 'vertical',
                        autoplay: 2000,
                        autoplayDisableOnInteraction: false,
                        loop:false,
                        observer:true,
                        bserveParents:true   
                        });
                     var h='';
                     for(var i=0;i<obj.length;i++){
                        h+=' <div class="swiper-slide f12 fff">'+obj[i].agent_name+'获得了'+obj[i].red_amount+obj[i].type+'</div>';
                     }
                     $('.swiper-wrapper').html(h);
           }
        };
        Test.agentlist();
    $('.startdati').on('click', function(){
          Test.init(agent_id);
    })