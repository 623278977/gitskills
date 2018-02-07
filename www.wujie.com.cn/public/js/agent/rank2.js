Zepto(function (){
    new FastClick(document.body);
    var urlPath = window.location.href,
        args = getQueryStringArgs(),
        activity_id = args['id'],
        agent_id = args['agent_id'] || '0';
    var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
    if(shareFlag){
        $('#installapp').removeClass('none')
    };
    function recordIntro(num){
          var config = {
                  type: 'doughnut',
                  data: {
                      datasets: [{
                          data: [ 
                              num,
                              100-num
                          ],
                          backgroundColor: [
                              'rgb(242,242,242)',
                              'rgb(255,163,0)'    
                          ]                        
                      }]                              
                  },
                  options: {
                      // responsive: true,
                      cutoutPercentage:86,
                      legend: {
                          position: 'left',
                      },
                      animation: {
                          animateScale: true,
                          animateRotate: true
                      }
                  }
              };
              var ctx = document.getElementById("mychart").getContext("2d");
              window.myDoughnut = new Chart(ctx, config);
            }  
    var activityDetail = {
        detail: function (agent_id) {
            var param = {};
            param["agent_id"] = agent_id;
            // var url = labUser.api_path + '/activity/detail/_v020700';
            var url=labUser.agent_path+'/user/level/010000';
            ajaxRequest(param, url, function (data) {
            if (data.status) {
               data.message.my_orders=40;
               progress(data.message.my_orders);
               $('#agent_title').html(data.message.level);
               $('#agent_rank').html(data.message.next_level);
               $('.ui_need_data').html('还需完成'+data.message.next_level_need_orders+'单');
               activityDetail.rule(data.message.summary);
               $('#data_detail').html(data.message.my_orders+'单');
               var total=data.message.next_level_need_orders+data.message.my_orders;
               var stillneed=data.message.next_level_need_orders/total;
               // 完成量的展示与否
               if(data.message.my_orders<50){
                recordIntro(Math.round(stillneed*100));
                $('.ui_need_data').removeClass('none');
                $('#current').text('距离下一个等级：');
               }else{
                recordIntro(0);
                $('.ui_need_data').addClass('none');
                $('#current').text('当前等级：');
               } 
               //头像的展示与否；
               if(data.message.avatar==''||data.message.avatar==undefined||data.message.avatar.length==0){
                  $('.ui_image img').attr('src',"/images/default/avator-m.png");
               }else{
                  $('.ui_image img').attr('src',data.message.avatar); 
                   };
               $('#act_container').removeClass('none');
            }//data.status
            })// ajaxRequest
            },//detail方法
            go:function(){
              $(document).on('click','.ui_href',function(){
                window.location.href=labUser.path +"webapp/agent/rankright/detail";
              })
            },
            rule:function(obj){
                 $.each(obj,function(k,v){
                  var html='';
                      html+='<p>-'+v+'</p>';
                  $('.ui_rights_text').append(html);
                 })
            }
        }//activityDetail对象 
    activityDetail.go();
    activityDetail.detail(agent_id);
});//zepto外层
// progress(49)