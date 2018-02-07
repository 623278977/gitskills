Zepto(function () {
var pageNow = 1,
    pageSize =5;
var args = getQueryStringArgs();
var uid = args['uid'] || 0;
var  activity_id = args['id'];
var Param = {
            "id": activity_id,
            "uid": uid,
            "type": 1,
            "page": pageNow,
            "pageSize": pageSize
        };        
        //获取活动列表
        function actlist(param){ 
            // var param = {};
            // param['id'] =param.id;
            // param['page']=param.page;
            // param['pageSize']=param.pageSize;
            // param['type']=param.type;
             var urlPath = window.location.href;
            var url = labUser.api_path + '/activity/list/_v020700';
            ajaxRequest(param, url, function(data){
                if (data.status){   
                    var news=data.message;
                    // var dataCount=data.message.dataCount;
                    var line='';
                    $.each(news,function(i,item){
                        line+='<div class="actpiccontent container-list relative " style="height:239px;cursor:pointer;" data-id="'+item.id+'">';
                        line+='<div class="pict_con absolute position40"><img src="' + item.list_img + ' " alt=""/></div>';
                    if(item.subject.length>30){
                         line+='<span id="act_name"  class="absolute position2 p24 left358">'+ item.subject.substring(0,30)+'…'+'</span>';   
                     }else{
                          line+='<span id="act_name"  class="absolute position2 p24 left358">'+ item.subject+'</span>';   
                     };       
                    line+='<span class="act_time_pict absolute position3 left358"></span><p id="_time" class="absolute p14 left390">'+unix_to_fulltime(item.begin_time_origin)+'~'+'</p><p id="_time_end" class="absolute p14">'+unix_to_fulltime(item.end_time)+'</p>';
                    line+=' <span class="act_city_pict absolute position4 left358"></span><p id="cityshost" class="absolute">' + item.host_cities + '</p>';
                    if(item.live_support==1){
                        line+='<span class=" absolute position5 left390">*支持OVO直播服务</span>';
                    }else{
                       line+='<span class=" absolute position5 left390 none">*支持OVO直播服务</span>';
                    }
                    line+='</div>'; 
                           });//循环的地方  
                    if(param.page==1){
                        $(".activitylist").html(line);
                        if(news.length == 0){
                            $('.jaizai h5').addClass('none');
                            $('.jaizai img').addClass('none');
                            $('.activitylist').append('<p style="color:#ea5520;width:100%;height:60px;line-height:60px;text-algin:center;font-size:14px">暂时没有活动哟</P>') 
                        }
                        if(news.length<5){
                            $('.jaizai h5').addClass('none');
                            $('.jaizai img').addClass('none');
                        }else{
                            $('.jaizai h5').removeClass('none').text('点击加载更多').removeAttr('disabled');
                        } 
                    }else{ 
                        $(".activitylist").append(line);
                        if(news.length<5){
                            $('.jaizai h5').text('没有更多了...').attr('disabled','true').css({'height':'60px','line-height':'60px'});
                            $('.jaizai img').addClass('none');
                            return;
                        }   
                    }
                    // if(news.length == 0){
                    //     $('.jaizai h5').addClass('none');
                    //     $('.jaizai img').addClass('none');
                    //     $('.activitylist').append('<p style="color:#ea5520;width:100%;height:60px;line-height:60px;text-algin:center;font-size:14px">暂时没有活动哟</P>') 
                    // }
                    // if(news.length<5){
                    //     $('.jaizai h5').addClass('none');
                    //     $('.jaizai img').addClass('none');
                    // }else{
                    //     $('.jaizai h5').removeClass('none').text('点击加载更多').removeAttr('disabled');
                    // } 
                }
                
            })
           
        };
        /*首次加载*/
         // var counte = 0; /*计数器*/
        actlist(Param);

        /*监听加载更多*/
        $(document).on('click', '.jaizai', function(){
            if($('.jaizai h5').attr('disabled')){
                return;
            }
           Param.page++;  
           actlist(Param);
        });
    })