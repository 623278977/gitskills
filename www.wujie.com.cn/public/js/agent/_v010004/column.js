//ByHongky
var args=getQueryStringArgs(),
	  id = args['id'],
    agent_id = args['agent_id'],
    brand_id=args['brand_id'],
    page=1,
    pagesize=3;
var Param ={
            "video_id": id,
            "page":page,
            "page_size": pagesize
            }
var Column = {
init:function(id,agent_id){
                  		var param={};
                  			  param['id']=id;
                      var url=labUser.agent_path+'/column/detail/_v010004';
                  		ajaxRequest(param,url,function(data){
                  			if(data.status){  
                                 Column.data(data.message.column);
                                 Column.article(data.message.article);
                                 Column.lesson(data.message.lesson);
                                 $('#act_container').removeClass('none');
                                 } 
                  		});
	},
data:function(obj){
                         $('.ui_professor_pict').css('background','url('+obj.photo+') no-repeat center');
                         $('.ui_professor_text p').eq(0).html(obj.title);
                         $('.ui_professor_text p').eq(1).find('span').eq(0).html(obj.name).next().html(obj.appellation).next().html(obj.study_times+'人学习');
                         $('.ui_column_introduce p').eq(1).html(obj.summary);


  },
article:function(obj){
                if(obj!=''){
                    $.each(obj,function(k,v){
                           var html='';
                               html+='<div class="ui_con color999" data-id="'+v.id+'">\
                                          <div class="padding">\
                                                <ul class="ui_text_pict">';
                              if(v.logo){
                                html+= '<li><p class="color333 f14 b ui-nowrap-multi">'+v.title+'</p>\
                                                         <p class="f12 ui-nowrap-multi">'+v.summary+'</p>\
                                                     </li>\
                                                     <li>\
                                                      <div class="ui_protect_pict fr"><img class="ui_pict1" src="'+v.logo+'"/></div>\
                                                     </li>\
                                                </ul>';
                                }else{
                                      html+='<li style="width:100%">\
                                               <p class="color333 f14 b ui-nowrap-multi">'+v.title+'</p>\
                                               <p class="f12 ui-nowrap-multi">'+v.summary+'</p>\
                                           </li>\
                                      </ul>';
                                }              
                                html+= '<p class="clear ui-border-b ui_row"></p>\
                                                <ul class="ui_text_down clear f11">\
                                                      <li>\
                                                        <ul class="ui_flex">\
                                                            <li>\
                                                              <img class="ui_zan fl" src="/images/agent/grey.png"/><span class="ui_padding fl">'+v.zan+'</span>\
                                                            </li>\
                                                            <li>\
                                                              <img class="ui_zan ui_mess" src="/images/agent/ui.png"/><span class="ui_padding">'+v.comments+'</span>\
                                                            </li>\
                                                            <li>\
                                                              <img class="ui_seen ui_mess" src="/images/agent/seen.png"/><span class="ui_padding">'+v.view+'</span>\
                                                            </li>\
                                                        </ul>\
                                                      </li>';
                                                       if(v.author){
                                                               html+= '<li><span style="padding-left:0.7rem">作者:'+v.author+'</span></li>';
                                                              }else{
                                                               html+= '<li><span style="padding-left:0.7rem;display:none">作者:'+v.author+'</span></li>';
                                                              }       
                                                       html+= '</ul>\
                                                            <p class="clear margin"></p>\
                                                       </div>\
                                           <div class="fline style"></div>\
                                        </div>';
                              $('article').append(html);
                              })                             
                        }else{
                             $('article').addClass('none');
                        }  
  },
lesson:function(obj){
         if(obj!=''){
                      $.each(obj,function(k,v){
                            var html='';
                                html+='<li class="video-zone" data-id="'+v.id+'">\
                                          <img class="ui_images" src="'+v.image+'"/>\
                                          <p class="color333 f13 ui_margin1">'+(v.subject.length<11?v.subject:v.subject.substring(0,10)+'…')+'</p>\
                                          <p class="f11 color999 ui_margin2">浏览量<span style="padding-left:0.5rem">'+v.view+'</span></p>\
                                       </li>';
                                $('.ui_video').append(html);
                      })
         }else{
          $('footer').addClass('none');
         }
  }
};
 Column.init(id);
// $('.getmore').on('click',function(){
//          page++;  
//          Video.brand(Param); 
//  })
// 热文详情跳转
$(document).on('tap','.ui_con',function(){
  var id=$(this).data('id');
      window.location.href=labUser.path+'webapp/agent/hotmessage/detail/_v010004?id='+id+'&agent_id='+agent_id;  
})
//视频跳转
$(document).on('tap','.video-zone',function(){
  var id=$(this).data('id');
      window.location.href=labUser.path+'webapp/agent/videoclass/detail/_v010004?id='+id+'&agent_id='+agent_id;  
})
