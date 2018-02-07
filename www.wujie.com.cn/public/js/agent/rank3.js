//created by hky
Zepto(function (){
    new FastClick(document.body);
    var urlPath = window.location.href,
        args = getQueryStringArgs(),
        activity_id = args['id'],
        agent_id = args['agent_id'] || '0',
        shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
    if(shareFlag){
        $('#installapp').removeClass('none')
    }
    //创建构造函数
    function Rankdetail(){};
    //取数据
         Rankdetail.prototype.detail=function (agent_id) {
                   var param = {};
                       param["agent_id"] = agent_id;
                   var url=labUser.agent_path+'/user/level/010000';
                    ajaxRequest(param, url, function (data) {
                    if (data.status) {
                       data.message.my_orders=43;
                       progress(data.message.my_orders);
                       init.rule(data.message.summary);
                       init.data(data.message);
                       init.go();
                                   }
                    })
            }
      //初始化数据
        Rankdetail.prototype.data=function(obj){
                       $('#agent_title').html(obj.level);
                       $('#agent_rank').html(obj.next_level);
                       $('.ui_need_data').html('还需完成'+obj.next_level_need_orders+'单');
                       $('#data_detail').html(obj.my_orders+'单');
                   var total=obj.next_level_need_orders+obj.my_orders,
                       stillneed=obj.next_level_need_orders/total;
                       if(obj.my_orders<50){
                        init.recordIntro(Math.round(stillneed*100));
                        $('.ui_need_data').removeClass('none');
                        $('#current').text('距离下一个等级：');
                       }else{
                        init.recordIntro(0);
                        $('.ui_need_data').addClass('none');
                        $('#current').text('当前等级：');
                       }
                       if(!obj.avatar){
                        $('.ui_image img').attr('src',"/images/default/avator-m.png");
                       }else{
                        $('.ui_image img').attr('src',obj.avatar); 
                       }
            }
        //跳转
        Rankdetail.prototype.go=function(){
                      $(document).on('click','.ui_href',function(){
                        window.location.href=labUser.path +"webapp/agent/rankright/detail";
                      })
            }
        //取数据
        Rankdetail.prototype.rule=function(obj){
                       $.each(obj,function(k,v){
                        var html='';
                            html+='<p>-'+v+'</p>';
                        $('.ui_rights_text').append(html);
                       })
            }
        //显示统计图
       Rankdetail.prototype.recordIntro=function(num){
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
                                cutoutPercentage:90,
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
    var init=new Rankdetail();
        init.detail(agent_id);
        

});//zepto外层
// progress(49)