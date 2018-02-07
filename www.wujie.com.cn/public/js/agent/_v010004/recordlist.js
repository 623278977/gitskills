//Byhongky
var player,
    args=getQueryStringArgs(),
	  id = args['id']||0,
    agent_id = args['agent_id'],
    brand_id=args['brand_id'],
    page=1,
    pagesize=3;
var Param ={
            "video_id": id,
            "page":page,
            "page_size": pagesize
            }
var Video = {
  brand:function(){
                        var param={};
                            param['page']=page;
                            param['page_size']=pagesize;
                            param['news_id']=id;
                        var url=labUser.agent_path+'/news/clock/_v010004';
                        ajaxRequest(param,url,function(data){
                if(data.status){
                        var html='';
                        $.each(data.message.data,function(k,v){
                            html+='<ul  class=" ui_listdetail ui-border-b">';
                            html+='<li><img  class="nick_pict"  src="'+(v.avatar?v.avatar:'/images/default/avator-m.png')+'"></li>';
                            html+='<li>\
                                        <p class="b f16 color333 margin7">'+v.realname+'</p>\
                                        <p class="color999 f12 margin7">'+v.zone_name+' <span class="fr">'+Video.unix(v.created_at)+'</span></p>\
                                       </li>\
                                   </ul>';
                              })
                            if(param.page==1){
                                $('.list_mumber').html(html); 
                               if(data.message.data.length == 0){
                                $('.list_mumber').addClass('none');
                                $('.getmore').addClass('none');
                                $('#nocommenttip2').removeClass('none');
                              }
                               if(data.message.data.length<3){
                                $('.getmore').text('没有更多了…').attr('disabled',true);
                               }else{
                                $('.getmore').text('点击加载更多').removeAttr('disabled');
                                }
                            }else{
                                $('.list_mumber').append(html);
                              if(data.message.data.length<3){
                                $('.getmore').text('没有更多了…').attr('disabled',true);
                                return false;
                             }   
                            }
                           $('.containerBox').removeClass('none');
                          }
                      })
                  
  },
  unix:function(unix){
                      var newDate = new Date();
                          newDate.setTime(unix * 1000);
                      var Y = newDate.getFullYear(),
                          M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1,
                          D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate(),
                          h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours(),
                          m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes(),
                          s = newDate.getSeconds() < 10 ? '0' + newDate.getSeconds() : newDate.getSeconds();
                      return Y + '年' + M + '月' + D + '日' + ' ' + h + ':' + m + ':' +s;
   }
  
};
 
 Video.brand(Param);
//加载更多的打卡用户
$('.getmore').on('click',function(){
         page++;  
         Video.brand(Param); 
 })
