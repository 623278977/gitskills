//Byhongky
var player,
    args=getQueryStringArgs(),
	  id = args['id']||0,
    agent_id = args['agent_id'],
    brand_id=args['brand_id'],
    page=1,
    pagesize=3;
var showtag = (window.location.href).indexOf('showtag') > 0 ? true : false;
var Param ={
            "video_id": id,
            "page":page,
            "page_size": pagesize
            }
var Video = {
	vodDetail:function(id,agent_id){
                  		var param={};
                  			  param['id']=id;
                          param['agent_id']=agent_id;
                      var url=labUser.agent_path+'/video/study-video-detail/_v010004';
                  		ajaxRequest(param,url,function(data){
                  			if(data.status){  
                                 Video.changestyle();
                                 getVod(data.message.video_url, 0);  
                                 Video.data(data.message); 
                                 $('.containerBox').removeClass('none');
                                 } 
                  		});
	},
  data:function(obj){
                      $('.videotitle').html(obj.title);
                      $('.ui-lesson-introduce').html(obj.video_description);
                      if(obj.lecturers_id!=0){
                        $('.ui-professor-introduce li').eq(0).find('img').attr('src',(obj.lecturers.avatar?obj.lecturers.avatar:'/images/default/avator-m.png'));
                        $('.ui-professor-introduce li').eq(1).find('p').eq(0).text(obj.lecturers.name);
                        $('.ui-professor-introduce li').eq(1).find('p').eq(1).text(obj.lecturers.summary);
                      }else{
                        $('.ui-list-forprofessor').addClass('none');
                      }               
                     if(obj.is_complete==1){
                        $('.ui-fixed-button').text('已完成学习').css('background','#ccc');
                         Video.static();
                     }else if(obj.is_complete==0){
                         Video.delay(); 
                         Video.brand(Param);
                     }
  },
  brand:function(){
                        var param={};
                            param['page']=page;
                            param['page_size']=pagesize;
                            param['video_id']=id;
                        var url=labUser.agent_path+'/video/clock/_v010000';
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
                          }
                      })
                  
  },
  unix:function(unix){
                      var newDate = new Date();
                          newDate.setTime(unix * 1000);
                          Y = newDate.getFullYear(),
                          M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1,
                          D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate(),
                          h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours(),
                          m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes(),
                          s = newDate.getSeconds() < 10 ? '0' + newDate.getSeconds() : newDate.getSeconds();
                      return Y + '年' + M + '月' + D + '日' + ' ' + h + ':' + m + ':' +s;
   },
  static:function(){
                      var param={};
                          param['agent_id']=agent_id;
                          param['brand_id']=brand_id;
                          param['type']='video';
                          param['post_id']=id;
                       var url=labUser.agent_path+'/brand/apply-status/_v010000';
                       ajaxRequest(param,url,function(data){})
   },
   changestyle:function(){
                  if(showtag){
                     $('.ui_have_brand li').eq(1).addClass('clickbg').siblings().removeClass('clickbg');
                     $('.ui-progress-bar li').eq(1).find('span').addClass('ui-blue').parent().siblings().find('span').removeClass('ui-blue');
                     $('.style').eq(1).removeClass('none').siblings('.style').addClass('none');
                  }
                    $('.ui_have_brand li').on('click',function(){
                       $(this).addClass('clickbg').siblings().removeClass('clickbg');
                       var index=$(this).index();
                       $('.style').eq(index).removeClass('none').siblings('.style').addClass('none');
                       $('.ui-progress-bar li').eq(index).find('span').addClass('ui-blue').parent().siblings().find('span').removeClass('ui-blue');
                    })
                    $('.share_video').addClass('none');            
                    $(".containerBox").removeClass('none');
                    $(document).on('click','.ui-pict30',function(){
                        var rotate=$(this).hasClass('rotate180')?true:false;
                        if(rotate){
                            $(this).addClass('rotate0').removeClass('rotate180').parent().parent().next().addClass('none');
                          }else{
                            $(this).addClass('rotate180').removeClass('rotate0').parent().parent().next().removeClass('none');  
                          }
                      })
                    //点击进入视频学习
                    $(document).on('click','.ui-blue,.ui-grey',function(){
                      var id=$(this).data('id');
                      var number=$(this).data('number');
                      var Video=$(this).prev().prev('img').attr('src')==('/images/agent/video.png')?true:false;
                          if(Video){
                           window.location.href=labUser.path+'webapp/agent/brandvod/detail/_v010004?&id='+id+'&agent_id='+agent_id+'&brand_id='+brand_id+'&showtag=1';
                           }else{
                           window.location.href=labUser.path+'/webapp/agent/headline/headlinestudy/_v010004?id='+id+'&agent_id='+ agent_id+'&section_id='+number+'&brand_id='+ brand_id;
                           }
                    }) 
                    //点击进入测试
                    $(document).on('click','.ui-fixed-button',function(){
                        if($(this).text()=='完成学习，参与测试'){
                           window.location.href=labUser.path+'webapp/agent/exam/detail/_v010004?id='+ brand_id+'&type=1&type_id='+id+'&agent_id='+agent_id;
                        }
                    })

   },
   delay:function(){
                    $('.tipsfor,.triangle').removeClass('none');
                     setTimeout(function(){
                        $('.tipsfor,.triangle').addClass('none')
                      },8000)
   },
  charter:function(){
                      var param={};
                          param['agent_id']=agent_id;
                          param['brand_id']=brand_id;
                          param['video_id']=id;
                      var url=labUser.agent_path+'/brand/chapter-list/_v010004';
                      ajaxRequest(param,url,function(data){
                           Video.charterlist(data.message); 
                       })
   },
   charterlist:function(obj){
              $.each(obj.chapter,function(k,v){
                var html='';
                    html+='<div class="ui-father-detail">\
                             <div class="fline ui-div f15 color333 b">'+v.chapter_num+' '+v.name+'<img class="fr ui-pict30 rotate180" src="/images/up.png"/></div>\
                           </div>';
                    html+='<ul class="ui-son-detail">';
                var str='';
                    for(i=0;i<v.content.length;i++){
                          str+='<li>';
                      if(v.content[i].type=='article'){
                          str+='<img class="ui-pict31 fl" src="/images/agent/sectiontext.png"/>';
                       }else if(v.content[i].type=='video'){
                          str+='<img class="ui-pict32 fl" src="/images/agent/video.png"/>';
                       }
                       if(v.content[i].is_curr==1){
                          str+='<span class="f13 color333 ui-padding30" style="color:#2873ff">'+v.content[i].cotent_num+' '+(v.content[i].title.length>17?v.content[i].title.substring(0,16)+'…':v.content[i].title)+'</span>';
                       }else{
                          str+='<span class="f13 color333 ui-padding30">'+v.content[i].cotent_num+' '+(v.content[i].title.length>17?v.content[i].title.substring(0,16)+'…':v.content[i].title)+'</span>';
                       }
                      if(v.content[i].is_complete==0){
                           if(v.content[i].is_curr==1){
                          str+='<button class="ui-blue none" data-id="'+v.content[i].id+'" data-number="'+v.content[i].cotent_num+'">开始学习</button>';
                        }else{
                           str+='<button class="ui-blue" data-id="'+v.content[i].id+'" data-number="'+v.content[i].cotent_num+'">开始学习</button>';
                        }
                       }else if(v.content[i].is_complete==1){
                          str+='<button class="ui-grey"  data-id="'+v.content[i].id+'"  data-number="'+v.content[i].cotent_num+'">已学习</button>';
                       }      
                          str+='</li>';
                    };
                    html+=str;
                    html+='</ul>';
                    html+='<div style="width:100%;height:1rem"></div>';
                    $('.ui-lesson-list').append(html);     
              })
   }
};
 Video.vodDetail(id,agent_id);
 Video.charter();

/**实例化点播**/
function getVod(video_url, stop_time) {
    player = new qcVideo.Player(
        //页面放置播放位置的元素 ID
        "video_box",
        {
            "file_id": video_url, //视频 ID (必选参数)
            "app_id": "1251768344", //应用 ID (必选参数)，同一个账户下的视频，该参数是相同的
            "auto_play": "0", //是否自动播放 默认值0 (0: 不自动，1: 自动播放)
            "width": 414, //播放器宽度，单位像素
            "height": 232, //播放器高度，单位像素
            "stop_time": stop_time,
            "disable_full_screen": 0
        },
        {
            //播放状态
            // status 可为 {ready: "播放器已准备就绪",seeking:"搜索",
            // suspended:"暂停", playing:"播放中" , playEnd:"播放结束" , stop:"试看结束触发"
            'playStatus': function (status) {
                if (status == 'playing') { //播放中
                    console.log('playing');
                }
                if (status == "playEnd") { //播放结束
                    console.log('end');
                }
                if (status == "stop") { //试看结束
                   
                }
            },
        });
}
//加载更多的打卡用户
$('.getmore').on('click',function(){
         page++;  
         Video.brand(Param); 
 })
//本地缓存;
if(!window.localStorage){
            alert("浏览器不支持支持localstorage");
          }else{
            var storage=window.localStorage;
                storage["n"]=1;
          }
$(document).on('click','.ui-user',function(){
    Video.brand(Param); 
})
