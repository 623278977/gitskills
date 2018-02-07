<!-- Created by wangcx -->

@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/dist/swiper.min.css" rel="stylesheet" type="text/css"/> 
    <link href="{{URL::asset('/')}}/css/agent/_v010004/videoclass.css" rel="stylesheet" type="text/css"/>

@stop
@section('main')
  <section class="containerBox bgcolor " id="containerBox">
    <div class="pb8">
      <div  id="videobox">
         
      </div>
      <div class="videotitle pt1-5 pb1-5 f15 pl1-5 pr1-5 mb1-33 bgwhite" style="margin-top: 21rem;">
          
      </div>
      <div class="bgwhite pl1-5 pr1-5 mb1-33 ui-profressor">
        <div class="lh45 b f15 ">讲师介绍</div>
        <div>
          <img src="" class="teacher_head l">
          <div class="l teacher_intro">
            <p class="f15 mb05" id="lecturer_name"></p>
            <p class="color999 f13" id="lecturer_summary">
        
            </p>
          </div>
          <div class="clearfix"> </div>
        </div>
      </div>
      <div class="bgwhite p1-5 mb1-33">
        <div class="lh45 b f15 ">课程介绍</div>
        <div class="color999 f13" id="introduce">
         
        </div>
      </div>

      <div class="bgwhite pl1-5 pr1-5 pb1-5 mb1-33">
          <div class="lh45 b f15 ">也许还想看这个</div>
          <ul  id='agent_videos' class="videos ">
             <!--  <li class="A_videos_intro l">
                  <div class="A_videos_img mb05">
                      <img src="{{URL::asset('/')}}/images/livetips.png" class="">
                      <img src="/images/agent/learned.png" class="learned_logo">
                  </div>
                  <div class="" >
                      <p class="f13 mb05 color333 nowrap">1第十三届杭州商业特许经营连锁加盟展览会</p>
                      <p class="f11 color999 nowrap mb1">浏览量 3.0万</p>
                  </div>
              </li> 
             
              <div class="clearfix"> </div> -->
          </ul>
      </div>

      <div class="bgwhite pl1-5 pr1-5 pb1-5">
        <div class="lh45 b f15 ">热门评论</div>
        <div>
          <ul id="comments">
            <!-- <li class="fline tl mb1-5">
              <div class="l mr1"><img src="" class="com_header"></div>
              <div class="l width85">
                <p class="lh3-3 color666 f13 ">张先生</p>
                <p class="f15 ">评论的内容</p>
                <div class="color999 mb1">
                  <span class="l">10月10日</span>
                  <span class="r"><img src="../images/agent/grey.png" class="zan_img mr05"><em>1</em></span>
                  <div class="clearfix"></div>
                </div>
              </div>
              <div class="clearfix"></div>
            </li>       -->  
          </ul>
        </div>
      </div>
      <div class="tc pt1-5 pb1-5 f13 color999 loadcomment none" data-do="1">点击加载更多</div>
    </div>
    <div class="bgwhite fix_bottom" data-type = "parent">
        <input value=" 写评论..." class="comment tl " type="button" style="background: transparent;color:#ccc;">
        <button class="comment_button">评论</button>
    </div>
    <div class="copy_suc tc none">
      <img src="/images/agent/success.png" style="">
      <p class="white f12 ">已复制</p>
    </div>
  </section>
@stop
@section('endjs')
    <script src="https://qzonestyle.gtimg.cn/open/qcloud/video/h5/h5connect.js" charset="utf-8"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/dist/swiper-3.4.2.min.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/agent/_v010005/reply.js"></script>
    <script>
      var $body = $('body');
      document.title = "视频详情";
      // hack在微信等webview中无法修改document.title的情况
      var $iframe = $('<iframe ></iframe>').on('load', function() {
      setTimeout(function() {
      $iframe.off('load').remove()
      }, 0)
      }).appendTo($body);
  </script> 
    <script type="text/javascript">
      new FastClick(document.body);
      Zepto(function(){
        var args = getQueryStringArgs();
        var agent_id = args['agent_id'] || 0,
            id = args['id'];
        var shareFlag = location.href.indexOf('is_share') >-1 ? true :false;
        var page = 1,pageSize =10;
        if(shareFlag){
          $('.fix_bottom').addClass('none');
        }
        function getVideodetail(id,uid){
             var param = {};
                 param.id = id;
                 param.uid = uid;
             var url=labUser.agent_path +'/lessons/detail/_v010005';
             ajaxRequest(param,url,function(data){
                if(data.status){
                  var vodHtml = '';
                  if(data.message){
                    var detail = data.message.detail;
                    var vodHtml ='';
                    getVod(detail.url,0);
                    $('.videotitle').text(detail.subject).attr('data-image',detail.image);  
                    $('.teacher_head').attr('src',detail.lecturer_avatar);         
                    $('#lecturer_name').text(detail.lecturer_name);           
                    $('#lecturer_summary').text(detail.lecturer_summary);    
                    $('#introduce').html(detail.introduce);
                    $.each(data.message.recommend,function(i,j){
                        vodHtml +='<li class="A_videos_intro l" data-id="'+j.id+'"><div class="A_videos_img mb05"><img src="'+j.image+'" class="" style="height:100%;"></div><div class="" ><p class="f13 mb05 color333 nowrap">'+j.subject+'</p><p class="f11 color999 nowrap mb1">浏览量 '+Format(j.view)+'</p></div></li>';
                    }) 
                    vodHtml += '<div class="clearfix"></div>';
                    $('#agent_videos').html(vodHtml).attr('data-sum',detail.share_summary);  
                    if(!data.message.detail.lecturer_name){
                       $('.ui-profressor').addClass('none');
                    }


                  }
                }
             })
          };

        getVideodetail(id,agent_id);


        $(document).on('click','.A_videos_intro',function(){
          var id= $(this).attr('data-id');
          window.location.href= labUser.path +'webapp/agent/videoclass/detail/_v010005?id='+id+'&agent_id='+agent_id;
        })

        //获取热门评论
          function getComment(id,uid,page,pageSize,type){
             var param = {};
                 param.id = id;
                 param.uid = uid;
                 param.type ='Lesson';
                 param.page =page;
                 param.pageSize =pageSize;
             var url=labUser.agent_path +'/comment/comment-list/_v010005';
             ajaxRequest(param,url,function(data){
                if(data.status){
                  var comHtml='';
                  if(data.message.data.length >0){
                    $.each(data.message.data,function(i,j){
                      comHtml +='<li class="fline tl mb1-5"><div class="l mr1"><img src="'+j.avatar+'" class="com_header"></div><div class="l width85"><p class="lh3-3 color666 f13 comment_name">'+j.c_nickname+'</p><p class="f15 relative inline-block comment_content"><span class="comment_text">'+j.content+'</span>';
                      if(j.c_uid == uid){
                        comHtml += '<span class="comment_tip none" style="width:6.5rem;"><em class="copy" style="width:100%;">复制</em></span></p>'
                      }else{
                        comHtml += '<span class="comment_tip none"><em class="reply" data-id="'+j.id+'" data-type="video">回复</em><em class="copy">复制</em></span></p>';
                      }                      
                      if(j.pId){
                        comHtml += '<div class="bgcolor f13 p1 mb1"><p><span class="c2873ff">'+j.p_nickname+'</span>：</p><p class="color666 break-word ui-nowrap-multi" >'+j.pContent+'</p></div>';
                      }
                      comHtml += '<div class="color999 mb1"><span class="l">'+timeForm(j.created_at_time)+'</span><span class="r ui-zan-zone" data-id="'+j.id+'">';
                      if(j.is_zhan == '0'){
                            comHtml +='<img src="/images/agent/grey.png" class="zan_img mr05">';
                          }else{
                            comHtml +='<img src="/images/agent/ui_pict.png" class="zan_img mr05">';
                          };
                      comHtml +='<em class="zan_num">'+j.likes+'</em></span><div class="clearfix"></div></div></div><div class="clearfix"></div></li>'; 
                    })
                    if(type == 'init'){
                      $('#comments').html(comHtml);
                    }else{
                      $('#comments').append(comHtml);
                    }
                    
                    $('.loadcomment').removeClass('none');
                    if(data.message.data.length < pageSize){
                      $('.loadcomment').text('已加载全部评论').attr('data-do',"0").removeClass('none');
                    }
                  }
                  if(data.message.all_count == 0){
                      $('#comments').html('<div class="tc nocomment" id="nocommenttip2">\
                          <img src="{{URL::asset('/')}}/images/nomessage_icon1.png" style="height: 16rem;width: 16rem;margin:0 auto;display: inline-block;margin-bottom: 4rem">\
                      </div> ');
                    $('.loadcomment').addClass('none');
                  };
                }
             })
          };
          getComment(id,agent_id,page,pageSize,'init');
          /**实例化点播**/
          function getVod(video_url, stop_time) {
              player = new qcVideo.Player(
                  //页面放置播放位置的元素 ID
                  "videobox",
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

          $(document).on('click','.fix_bottom',function(){
                uploadpic(id,'Lesson',true)       
          })

          $(document).on('click','.loadcomment',function(){
            var statu = $(this).attr('data-do');
            if(statu == "1"){
              page++;
              getComment(id,agent_id,page,pageSize,'more')
            }
              
          })

          //评论点赞或取消；
           $(document).on('click','.ui-zan-zone',function(){
                var canzan = 1;
                var id=$(this).data('id');
                var zan_num=$(this).find('.zan_num').text();
                  if(canzan == 1){                  
                      if($(this).find('img').attr('src')=='/images/agent/grey.png'){
                        $(this).find('img').attr('src','/images/agent/ui_pict.png');
                        $(this).find('.zan_num').text(zan_num-1+2);
                        // canzan =0;        
                        zanToggle(id,agent_id,'1');
                      }else{
                        // canzan =0;
                        $(this).find('img').attr('src','/images/agent/grey.png');
                        $(this).find('.zan_num').text(zan_num-1);
                        zanToggle(id,agent_id,'0');
                      };
                  }
                    
           })

           //点赞或取消
           function  zanToggle(id,agent_id,type){
                var param={};
                    param['id']=id;
                    param['uid']=agent_id;
                    param['type'] = type;
                var url=labUser.agent_path+'/comment/assign-user-comment-add-zan/_v010005';
                ajaxRequest(param, url, function (data) {
                    if(data.status){
                        canzan = 1;
                    }
                })
           }

          function timeForm(unix) {
              var newDate = new Date();
              newDate.setTime(unix * 1000);
              var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
              var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
              var h = newDate.getHours() < 10 ? ('0' + newDate.getHours()) : newDate.getHours();
              var m = newDate.getMinutes() < 10 ? '0' + newDate.getMinutes() : newDate.getMinutes();
              return M + '月' + D + '日 ' ;
          }

          //格式化浏览量
        function Format(str){
            return (str > 10000)?(parseFloat(str/10000).toFixed(1)+'万'):str;
        }

        commonReply(id);

      

      });//Zepto end
    
       function Refresh(){
          location.reload();
       }  

       function showShare() {
            var args = getQueryStringArgs(),
                id = args['id'] || '0';      
            var title = $('.videotitle').text();
            var pageUrl =window.location.href;
            var img = $('.videotitle').attr('data-image') || labUser.path +'/images/agent-share-logo.png';
            var header = '视频';
            var content = cutString(removeHTMLTag($('#introduce').text()), 18);
            var sharecontent = $('#agent_videos').attr('data-sum') || content;
            shareOut(title, pageUrl, img, header, sharecontent,'','','','video','','','share','video',id);//分享
        };
              
    </script>  
@stop