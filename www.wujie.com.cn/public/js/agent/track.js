// created byhongky
Zepto(function (){
    new FastClick(document.body);
    var urlPath = window.location.href,
        args = getQueryStringArgs(),
        agent_id = args['agent_id'],
        customer_id= args['customer_id'],
        brand_id=args['brand_id'];
        $(document).ready(function(){
          $('body').css('background','#f2f2f2')
        })
    var Detail = {
        detail: function (agent_id,customer_id,brand_id) {
            var param = {};
                param["agent_id"] = agent_id;
                param['customer_id']=customer_id;
                param['brand_id']=brand_id;
            var url=labUser.agent_path+'/customer/records-all/_v010000';
            ajaxRequest(param, url, function (data) {
            if (data.status) {
                                Detail.message(data.message.customer);
                                Detail.brand(data.message.brand);
                                Detail.follow(data.message.follow);
                                Detail.related(data.message);
                                Detail.remark(data.message.remark_list);
                                Detail.tasklist(data.message.follow.task);
                                Detail.table();
                                Detail.go();
                                Detail.add(); 
                                Detail.transform();
                                $('#act_container').removeClass('none');
                              }//data.status
                    })// ajaxRequest
            },//detail方法
        message:function(obj){
            if(obj.avatar){
              $('#nickpict').attr('src',obj.avatar);
              $('#boss').attr('src',obj.avatar);
            }else{
              $('#nickpict').attr('src','/images/default/avator-m.png');
              $('#boss').attr('src','/images/default/avator-m.png');
            }
            if(obj.nickname.length>8){
              $('.c_nickname').text(obj.nickname.substring(0,7)+'…');
              $('#bossnickname').text(obj.nickname.substring(0,7)+'…');
              $('.ui_track_top').data('id',obj.nickname);
            }else{
              $('.c_nickname').text(obj.nickname);
              $('#bossnickname').text(obj.nickname);
              $('.ui_track_top').data('id',obj.nickname);
            }
            if(obj.gender==0){
              $('#sex').attr('src','/images/020700/person.png')
            }else if(obj.gender==1){
              $('#sex').attr('src','/images/agent/boy.png')
            }else if(obj.gender==-1){
               $('#sex').addClass('none');
            }
            if(obj.username){
              $('#com_infor li').eq(2).find('a').eq(0).attr('href','sms:'+obj.username);
              $('#com_infor li').eq(2).find('a').eq(1).attr('href','tel:'+obj.username);  
              }else{
              $('#com_infor li').eq(2).find('a').eq(0).on('click',function(){
                Detail.tips('该客户未公开手机号码，暂时不能发送短信')
              });
              $('#com_infor li').eq(2).find('a').eq(1).on('click',function(){
                Detail.tips('该客户未公开手机号码，暂时不能拨打电话')
              });
              }
               $('#com_infor li').eq(2).find('a').eq(2).data('nickname',obj.nickname);    
            },//message方法结尾
        brand:function(obj){
            if(obj.title.length>3){
                $('.ui_brandname').text(obj.title.substring(0,3)+'…');
                $('#ui_brand').text(obj.title.substring(0,2));
                $('#boss_wanted').text(obj.title.substring(0,3)+'…'+'意向投资人');
                $('#agentself_wanted').text(obj.title.substring(0,3)+'…'+'代理经纪人');
            }else{
                $('.ui_brandname').text(obj.title);
                $('#ui_brand').text(obj.title);
                $('#boss_wanted').text(obj.title+'意向投资人');
                $('#agentself_wanted').text(obj.title+'代理经纪人');
            };
            if(obj.slogan){
              $('.slogan').html(obj.slogan).addClass('fr');
            }else{
              $('.slogan').addClass('none');
            }     
            },//brand方法结尾
        follow:function(obj){
            if(obj.status==0){
            }else if(obj.status==1){
             $('.ui_left_infor li').eq(2).find('span').addClass('ui_progressred').removeClass('ui_progressgrey');
             $('.ui_left_infor li').eq(3).find('img').attr('src','/images/020700/success.png');
            }
            $('.trackday').html('已经跟进'+obj.days+'天');
            $('#tracktime').text(unix_to_parttime(obj.start_time));
        },//follow方法结尾
        table:function(){
            $('.ui_tab li').on('click',function(){
              var index=$(this).index();
              var span='<span></span>';
              $(this).addClass('ownbg').siblings().removeClass('ownbg');
              $('.ui_tabs li').eq(index).append(span).siblings().html('');
              $('.container>section').eq(index).removeClass('none').siblings().addClass('none');
            })
            $('.ui_tab li').eq(1).on('click',function(){
              $('.ui_bind').addClass('none');
              })
            $('.ui_tab li').eq(2).on('click',function(){
              $('.ui_bind').addClass('none');
              })
            $('.ui_tab li').eq(3).on('click',function(){
              $('.ui_bind').addClass('none');
              })
            $('.ui_tab li').eq(0).on('click',function(){
              $('.ui_bind').removeClass('none');
              })
            $('#motai').on('click',function(){
              $('.bg-model').removeClass('none');
            })
            $(document).on('click','.ui_task,.bg-model',function(){
              $('.bg-model').addClass('none')
            })
        },
        add:function(){
            var ui_all=$('ui_all');
            var ui_activity=$('#ui_activity').clone(true).removeClass('none  pb'),
                ui_invest=$('#ui_invest').clone(true).removeClass('none  pb'),
                ui_contrack=$('#ui_contrack').clone(true).removeClass('none pb');
                $('.ui_all').append(ui_activity);
                $('.ui_all').append(ui_invest);
                $('.ui_all').append(ui_contrack);
        },
        related:function(obj){
            if(obj.agent_avatar){
              $('#agentself').attr('src',obj.agent_avatar);
            }else{
              $('#agentself').attr('src','/images/default/avator-m.png');

            }
            $('#ui_bind_time_').html(stampchange(obj.relation_time))
            // $('#ui_track_').text(obj.records);
        },
        remark:function(obj){
          var swiper = new Swiper('.swiper-container', {
              pagination: '.swiper-pagination',
              paginationType: 'custom',
              nextButton: '.swiper-button-next',
              prevButton: '.swiper-button-prev',
              observer:true,//修改swiper自己或子元素时，自动初始化swiper
              bserveParents:true         
        });
        if(obj.length>0){
          if(obj.length<3){
            $('.common_').addClass('none')
          }
        $.each(obj, function (k, v) {
            var html = '';
                html+='<div class="swiper-slide">';
                html+='<ul class="ui_remark_detail">\
                        <li>\
                          <div class="ui_text f11 color666 ui-nowrap-multi">'+v.content+'</div>\
                        </li>\
                        <li class="transformone transform" >\
                          <img  class="ui_img10"  src="/images/down.png">\
                        </li>\
                      </ul>\
                      <div style="width:100%;height:0.5rem;clear:both"></div>\
                      <p class=" f12 color333 margin05 clear">相关品牌：'+v.brand+'</p>';
                if(v.level==1){
                  html+='<p class=" f12 color333">客户等级：普通客户<span class="fr color666">'+stampchange(v.time)+'</span></p>'; 
                }else if(v.level==2){
                  html+='<p class=" f12 color333">客户等级：主要客户<span class="fr color666">'+stampchange(v.time)+'</span></p>'; 
                }else if(v.level==3){
                   html+='<p class=" f12 color333">客户等级：关键客户<span class="fr color666">'+stampchange(v.time)+'</span></p>'; 
                }else if(v.level==-1){
                   html+='<p class=" f12 color333">客户等级：遗失客户<span class="fr color666">'+stampchange(v.time)+'</span></p>'; 
                }else if(!v.level){
                    html+='<p class=" f12 color333">客户等级：普通客户<span class="fr color666">'+stampchange(v.time)+'</span></p>'; 
                }
               
                html+='</div>';
            $('.swiper-wrapper').append(html); 
        }); 
            $('.ui_noremark').addClass('none');
            $('.ui_remarkdata').removeClass('none');
        }else{
            $('.ui_noremark').removeClass('none');
            $('.ui_remarkdata').addClass('none');
        }   
        },
        transform:function(){
          $('.transform').on('click',function(){
            if($(this).hasClass('transformone')){
              $(this).addClass('transformtwo').removeClass('transformone');
              $(this).siblings().find('.ui_text').removeClass('ui-nowrap-multi');
            }else{
              $(this).addClass('transformone').removeClass('transformtwo');
              $(this).siblings().find('.ui_text').addClass('ui-nowrap-multi');
            }
          })
        },
        go:function(){
          $(document).on('click','.button,.ui_once',function(){
           window.location.href = labUser.path + "webapp/agent/remark/detail?customer_id=" + customer_id + "&agent_id=" + agent_id+"&brand_id=" + brand_id;
          })
        },
        tasklist:function(obj){
         $.each(obj,function(k,v){
              if(v.type==1){
                 $('.ui_task_detail p').eq(3).addClass('color333');
                 $('.p5').attr('src','/images/020700/w21.png');
                 $('.time1').removeClass('none').html(Detail.stamptime(v.time));
              }else if(v.type==2){
                 $('.ui_task_detail p').eq(4).addClass('color333');
                 $('.p2').attr('src','/images/020700/w21.png');
                 $('.time2').removeClass('none').html(Detail.stamptime(v.time));
              }else if(v.type==3){
                 $('.ui_task_detail p').eq(5).addClass('color333');
                 $('.p3').attr('src','/images/020700/w21.png');
                 $('.time3').removeClass('none').html(Detail.stamptime(v.time));
              }else if(v.type==4){
                 $('.ui_task_detail p').eq(6).addClass('color333');
                 $('.p4').attr('src','/images/020700/w21.png');
                 $('.time4').removeClass('none').html(Detail.stamptime(v.time));
              }

         })
        },
        stamptime:function(unix){
                            var newDate = new Date();
                            newDate.setTime(unix * 1000);
                            var Y = newDate.getFullYear();
                            var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
                            var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
                            return Y + '/' + M + '/' + D;
        },
        tips:function(e) {
            $('.tips').text(e).removeClass('none');
          setTimeout(function() {
            $('.tips').addClass('none');
          }, 1500);
        }
        }//activityDetail对象    
     Detail.detail(agent_id,customer_id,brand_id);
     //进入聊天室调用；
  function goChat(uType, uid, nickname) {
    if (isAndroid) {
      javascript: myObject.goChat(uType, uid, nickname);
    }
    else if (isiOS) {
      var data = {
        'uType': uType,
        'id': uid,
        'name':nickname
      };
      window.webkit.messageHandlers.goChat.postMessage(data);
    }
  }
  $('#com_infor li').eq(2).find('a').eq(2).on('click', function() {
    var nickname=$(this).data('nickname');
    goChat('c', customer_id,nickname);
  });
});//zepto外层

// refresh()
