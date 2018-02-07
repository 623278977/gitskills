Zepto(function () {
		var arg=getQueryStringArgs(),
			id=arg['id'],
			uid=arg['uid']||'0',
			maker_id=arg['uid']||'0',
			sign=(window.location.href).indexOf('is_sign') > 0 ? true : false;
      function getLiveticket(){
        var arg=getQueryStringArgs(),
            id=arg['id'],
            uid=arg['uid']||0;
        var param={};
            param['id']=id;
            param['uid']=uid;
        var url=labUser.api_path+'/activity/tickets/_v020400';    
        ajaxRequest(param,url,function(data){
         if(data.status){
          var div=data.message;
           $.each(div,function(i,item){
              var type=item.type;
              var live_id=item.id;
            if(type==2){
              $('header').data('live_id',live_id);
              var score_price=item.score_price;
              $('header').data('score_price',score_price);
            }else if(type==1){
              // $('header').data('ticket_id',live_id);
            }
           })  
         }
        })
      }
    getLiveticket();
		function getAddress(id){
			var param={};
				param['id']=id;
			var url=labUser.api_path+'/activity/detail/_v020700';
				ajaxRequest(param,url,function(data){
					if(data.status){
						// getdizhi(data.message);
						var ticket_id=data.message.ticket_id;
						var min_ticket_score=data.message.min_ticket_score;
						$('header').data('ticket_id',ticket_id);
						$('header').data('score_num',min_ticket_score);
						var html='',
							timestamp = Math.round(new Date().getTime() / 1000),
							end_time=data.message.end_time,
							title='活动地点('+data.message.activity_location_arr.length+')';
						setPageTitle(title)	;
						$.each(data.message.activity_location_arr,function(index,item){
							html+='<div style="cursor:pointer;" class="address" data_id='+item.id+'><span class="ovo_name">'+'('+item.zone+')'+'</span><span class="ovo_name">'+item.subject+'</span>';
							html+='<p class="c9 detail_address">'+item.address+'</p>';
							html+='<p class="c9" style="height:1rem"></p>';
							// html+='<p class="none des">'+removeHTMLTag(item.description)+'</p>';
              html+='<span class="jian_tou_" style="width:11px;height:21px;float:right;display:block;"></span>';
              html+='</div>';
              $(document).on('click','.address',function(){
               
                var args = getQueryStringArgs();
                var  activity_id = args['id']||'0';
                var data_id=$(this).attr('data_id');
                var url= labUser.path+'/webapp/activity/bmap?id='+activity_id+'&maker_id='+data_id;
                url=url.replace('https://','http://');
                window.location.href=url;
             });
						});
							// html+='<div class="footer"><span class="f63">*挑选合适的会场，赶紧报名活动吧</span><button type="button" class="signup" style="background-color:#ff5a00">立即报名</button></div>';
						$('#address').html(html);
						$.each(data.message.activity_location_arr, function(index, item){
                        var str = '';
                        str+='<div class="text_meet_live" data-maker_id="'+item.id+'">';
                        str+='<div class="upper_">';
                        str+='<samp  style="padding-left:55px" class="choice_btn defulat_choice_btn"></samp>';
                        str+='<label class="attend"  style="float:left;font-size:12px;" >'+item.zone+'</label>';
                        str+=' <label style="color:#999;float:right;margin-right:20px">'+item.subject+'</label>';
                        str+=' </div>';
                        str+='<div class="downper none">';
                        if(item.address.length>20){
                         str+='<h5><label class="_address_"><samp style="font-size:12px;color:#666">联系地址:</samp><samp style="color:#999;padding-left:10px; font-size:12px">'+item.address.substring(0,20)+'……'+'</label></h5>';  
                        }else{
                         str+='<h5><label class="_address_"><samp style="font-size:12px;color:#666">联系地址:</samp><samp style="color:#999;padding-left:10px;font-size:12px">'+item.address+'</samp></label></h5>'; 
                        }
                        
                        str+='<h5><label class="_phone_"><samp style="font-size:12px;color:#666">联系电话:</samp><samp style="color:#999;padding-left:10px;">'+item.tel+'</samp></label></h5>';
                        str+=' </div>'
                        str+=' </div>';//最外层
                          $('.meet_place').append(str);
                           
                       });
            $('.meet_place').children('.text_meet_live:first').find('samp:first').removeClass('defulat_choice_btn').addClass('meet_choice_btn');
            $('.meet_place').children('.text_meet_live:first').find('.downper').removeClass('none');
            $('header').data('maker_id',$('.text_meet_live:first').data('maker_id'));
						// var zone_id=data.message.activity_location_arr.zone_id;
						// $('#attend').attr('zone_id',zone_id);
						if(sign){
							$('.footer').addClass('none');
						};
						if (timestamp > end_time) {
				       	  $('.signup').css('background-color', '#ccc').text('报名结束').attr('disabled','true');
				        }else{
				        	$(document).on('tap', '.signup', function(){
				                var ovoid=maker_id;
				                var act_id = id;
				                var act_name = data.message.subject;
				                ActivityApply(act_id, ovoid, act_name, 'activity');
				       		});
				        }				        
				        $('#address').removeClass('none');
				        // 跳转地图
						$(document).on('tap','.address',function(){
							var address=$(this).find('.detail_address').text(),
								ovo_name=$(this).find('.ovo_name').text(),
								des=removeHTMLTag($(this).find('.des').text());
								des = cutString(des, 40);
							locationAddress(address, ovo_name, des);
						});						
					}else{
						alert(data.message);
					}
				})
		};
		
		getAddress(id);
		var urlPath=window.location.href,
				arg=getQueryStringArgs(),
				activity_id=arg['id'];
			var is_share = urlPath.indexOf('is_share') > 0 ? true : false;
			function getUsers(activity_id,offset,size){
				var param={};
					param['activity_id']=activity_id;

				var url=labUser.api_path+'/activity/signuserlist/_v020400';
				ajaxRequest(param,url,function(data){
					if(data.status){
						var title='报名人数('+data.message.length+')';						
						if(!is_share){
							setPageTitle(title);
						};
						var list = data.message;
						if(list==''){
							$('#list').addClass('none');
							$('.no_data').removeClass('none');
						}else{
							$('#list').removeClass('none');
						};
			            var sum = data.message.length;			    
			            var Html = '';
			            if(sum - offset < size ){
			                size = sum - offset;			              
			              }           
			            for(var j,i=offset; i<(size+offset); i++){
			            	if(list[i].price==0){
			            		j='免费';
			            	}else{
			            		j=list[i].price+'元';
			            	}
			                Html+='<p class="enroll fline"><img src='+list[i].avatar+'><span class="name" style="font-size:14px;">'+list[i].name.substring(0,1)+'**'+'</span><span class="ticket r">'+list[i].sign_time+'</span></p>';	
            			};
        				$('#list').html(Html);
    
            			if ( (offset + size) >= sum){
			                $(".more").hide();
			            }else{
			                $(".more").show();
			            };
						$('.enrollment').removeClass('none');
					}else{
						alert(data.message);
					}
				});	
			};
		// 加载更多
		    var counter = 0; /*计数器*/
		    var pageStart = 0; /*offset*/
		    var pageSize = 4; /*size*/
		    
		    /*首次加载*/
		    getUsers(activity_id,pageStart, pageSize);
		    
		    /*监听加载更多*/
		    // $(document).on('tap', '.more', function(){
		    //     counter++;
		    //     pageStart = counter * pageSize;		        
		    //     getUsers(activity_id,pageStart, pageSize);
		    // });
      //是否显示观看直播
           function getTicket(id){
              var param={};
                  param.id = id;
             var url=labUser.api_path+'/activity/enroll-infos/_v020700';
              ajaxRequest(param,url,function(data){
                  if(data.status){
                    var ticketArr = data.message.ticket;
                    for(var i= 0; i<ticketArr.length;i++){
                      if(ticketArr[i].type == 2 && ticketArr.score_price == 0){
                          $('.see_live_text').attr('ticket-id',ticketArr[i].id);
                          $('.see_live_text').removeClass('none');
                      }
                    }
                  };
              });

         };
         getTicket(activity_id); 
         //报名选择会场中事件
         //选择直播票
         $(document).on('click','.see_live_text',function(){
          var ticID = $(this).attr('ticket-id');
            $('header').attr('ticket_id',ticID);
            $(this).children('samp').removeClass('defulat_choice_btn').addClass('meet_choice_btn');
            $('.meet_live_text').find('samp').removeClass('meet_choice_btn').addClass('defulat_choice_btn');
            $('.text_meet_live').children('.upper_').children('samp').removeClass('meet_choice_btn').addClass('defulat_choice_btn');
            $('.text_meet_live').children('.downper').hide();
         });
         //选择现场票
         $(document).on('click','.meet_live_text',function(){
            $('.see_live_text samp').removeClass('meet_choice_btn').addClass('defulat_choice_btn');
            if($(this).children('samp').hasClass('meet_choice_btn')){
                return;
            }else{
              // $(this).children('samp').removeClass('defulat_choice_btn').addClass('meet_choice_btn');
              // $(this).next().children('.upper_').children('samp').removeClass('defulat_choice_btn').addClass('meet_choice_btn');
              // $(this).next().children('.downper').removeClass('none');
              $('.text_meet_live:first').click();
            }      
         });
         //各现场现场票
         $(document).on('click','.text_meet_live',function(){
          $('.meet_live_text samp').removeClass('defulat_choice_btn').addClass('meet_choice_btn');
          $('.see_live_text samp').removeClass('meet_choice_btn').addClass('defulat_choice_btn');
          var maker_id = $(this).data('maker_id');
          var samp = $(this).find('samp')[0];//为选择的元素
          if($(samp).hasClass('meet_choice_btn')){
            // $(samp).removeClass('meet_choice_btn').addClass('defulat_choice_btn');
            // $(this).children('.downper').hide();
            // $('.meet_live_text samp').addClass('defulat_choice_btn').removeClass('meet_choice_btn');
              return;
            }else{
              $('header').data('maker_id',maker_id);
              $(samp).removeClass('defulat_choice_btn').addClass('meet_choice_btn');
               $(this).children('.downper').show();
               $(this).siblings('.text_meet_live').find('.downper').hide();
               $(this).siblings('.text_meet_live').children('.upper_').children('samp').removeClass('meet_choice_btn').addClass('defulat_choice_btn');
               $('.see_live_text samp').removeClass('meet_choice_btn').addClass('defulat_choice_btn');
            };

            });
         	//提交会场
          $(document).on('click','.sub_live_text',function(){
              // var is_meet = $('.meet_live_text samp').hasClass('meet_choice_btn') ? true : false,
              //     is_live = $('.see_live_text samp').hasClass('meet_choice_btn') ? true : false;
              var ticket_id=$('header').data('ticket_id');
              var score_num=$('header').data('score_num');
              var maker_id=$('header').data('maker_id');
              var tel_id=$('header').data('tel_id');
              var phonenum = $("input[name='phonenumber']").val();
              var param={};
                  param['uid']=uid || 0;
                  param['activity_id']=activity_id;
                  param['maker_id']=$('.see_live_text samp').hasClass('meet_choice_btn') ? 0 : maker_id;
                  param['score_num']= score_num;
                  param['ticket_id']=ticket_id;
                  param['path']='html5';
                  param['tel']=tel_id;
                  param['name'] = $('input[name="nickname"]').val();
               var url=labUser.api_path+'/activity/apply-and-pay/_v020700';
               ajaxRequest(param,url,function(data){
                  if(data.status){
                      var order_no=data.message.order_no;
                      $('.secondstep').hide();
                      $('.laststep').show();
                      $('#signname').css('overflow','auto');
                      databack(order_no);
                      getUsers(activity_id,0,5)
                  }else{
                    alert(data.message);
                  }
               })  
          })
         
       //样式完毕
      //提交会场
     function unix_to_year_weekdatetime(unix) {
        var newDate = new Date();
        newDate.setTime(unix * 1000);
        var Y = newDate.getFullYear();
        var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
        var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
        var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
        var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
        var week=newDate.getDay();
               if(week==0){
                  week="周日"
                }
                if(week==1){
                  week="周一"
                }
                if(week==2){
                  week="周二"
                }
                if(week==3){
                  week="周三"
                }
                if(week==4){
                  week="周四"
                }
                if(week==5){
                  week="周五"
                }
                if(week==6){
                  week="周六"
                };
        return Y + '-' + M + '-' + D + ' '+week+' ' +h + ':' + m;
    };
    //获取时间戳
function Cymd_to_unix(datetime) {
    var tmp_datetime = datetime.replace(/:/g, '-');
    tmp_datetime = tmp_datetime.replace(/年/g, '-');
    tmp_datetime = tmp_datetime.replace(/月/g, '-');
    tmp_datetime = tmp_datetime.replace(/日/g, '-');
    var arr = tmp_datetime.split("-");   
    var now = new Date(Date.UTC(arr[0], arr[1] - 1, arr[2], arr[3] - 8, arr[4]));
    return parseInt(now.getTime() / 1000);
}
    function databack (order_no){
      var nickname = $('input[name="nickname"]').val();
      var param={};
      param['order_no']=order_no;
      var url=labUser.api_path+'/activity/check-and-apply/_v020700';
      ajaxRequest(param,url,function(data){
            if(data.status){
              var begin_time=Cymd_to_unix(data.message.begin_time);
                  begin_time = unix_to_year_weekdatetime(begin_time); 
               $('#attend_title').html(data.message.subject);
               $('#attend_time').html(begin_time);
               $('#attend_people label').html(data.message.name);
               $('#attend_phone  label').html(data.message.tel);
               var ticket_type=data.message.ticket_type;
               var zone = $('.meet_choice_btn').parent('.upper_').children('label').eq(1).html();
               var strings='<samp style="font-weight:bold;">地点</samp>';
               if(ticket_type==1){
                $('#attend_way  label').html('现场票');
                $('#attend_address').append(strings);
                $('#attend_address label').html(zone);
               }else{
                 $('#attend_way  label').html('直播票');
                 $('#attend_address label').html('');
               } 
              }
            });
     };
     
	});//Zepto最外层
	 function setPageTitle(title) {
            if (isAndroid) {
                javascript:myObject.setPageTitle(title);
            } 
            else if (isiOS) {
                var data = {
                   "title":title
                }
                window.webkit.messageHandlers.setPageTitle.postMessage(data);
            }
        };
  
