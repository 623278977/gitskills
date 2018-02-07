<!-- Created by wangcx -->

@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/dist/swiper.min.css" rel="stylesheet" type="text/css"/> 
    <link href="{{URL::asset('/')}}/css/agent/_v010004/videoclass.css" rel="stylesheet" type="text/css"/>

@stop
@section('main')
  <section class="containerBox bgcolor " style="min-height: 100%;" id="containerBox">
    <div class="bgwhite">
      <nav class="flex-around flex lh45 f13">
       <!--  <div class="choosen">推荐</div>
        <div>正能量</div>
        <div>金句</div>
        <div>分类</div>
        <div>自定义</div> -->
      </nav>
      <div class="banner swiper-container" id="swiper-container">
        <div class="swiper-wrapper" id="banners">
         <!--  <div class="swiper-slide">
            <img src="/images/agent/ins-success.png" class="slide_img">
          </div> -->
          <!--  <div class="swiper-slide">
            <img src="/images/agent/ins-success.png">
          </div> -->
         <!-- <div class="swiper-slide">
            <img src="/images/agent/ins-success.png">
          </div> -->
        </div>
        <div class="swiper-pagination"></div>
      </div>
      <div class="commend">
          <ul  id='agent_videos' class="videos p1-5" style="overflow: hidden;">
              <!-- <li class="A_videos_intro l">
                  <div class="A_videos_img mb05">
                      <img src="{{URL::asset('/')}}/images/livetips.png" class="">
                      <img src="/images/agent/learned.png" class="learned_logo">
                  </div>
                  <div class="" >
                      <p class="f13 mb05 color333 nowrap">1第十三届杭州商业特许经营连锁加盟展览会</p>
                      <p class="f11 color999 nowrap mb1">浏览量 3.0万</p>
                  </div>
              </li>  -->
          </ul>
      </div>
     <div class="tc lh45 color999 f12 tf fline getmore none">点击加载更多...</div>
     <div class="tc lh45 color999 f12 tf fline none nomore" style="background: #f2f2f2;">已全部加载...</div>
    </div>
      
        
  </section>
@stop
@section('endjs')
    <script type="text/javascript" src="{{URL::asset('/')}}/js/dist/swiper-3.4.2.min.js"></script>
    <script>
      var $body = $('body');
      document.title = "视频课堂";
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
        var agent_id = args['agent_id'] || 0;
        var page = 1,
            pageSize =10,
            type = 'agent_lesson';
        //banner图
        function getBanner(type){
          var url = labUser.agent_path +'/ad/list/_v010004';
          ajaxRequest({type:type},url,function(data){
              if(data.status){
                  var banner ='';
                  if(data.message.length >0){
                    $.each(data.message,function(i,j){
                      banner += '<div class="swiper-slide" data-url="'+j.link_url+'"><img src="'+j.image+'" class="slide_img"></div>';
                    })
                    $('.swiper-wrapper').html(banner);                   
                  } ;
                var mySwiper = new Swiper('#swiper-container',{//子swiper 
                        // resistanceRatio: 50, //抵抗率,值越小抵抗越大越难将slide拖离边缘，0时完全无法拖离。
                        // slidesPerView : 'auto',
                        // loopedSlides :1,
                        pagination : '.swiper-pagination', 
                        paginationType : 'bullets',
                        onInit: function(swiper){
                                if($('.swiper-wrapper .swiper-slide').length == 1){
                                  $('.swiper-pagination').addClass('none');
                                }
                            }
                       
                    })  
     
              }
          })
        }

        getBanner(type);
        // banner图片链接
        $(document).on('click','.swiper-slide',function(){
          var url = $(this).attr('data-url');
          window.location.href = url;
        })
        
        //获取分类
        function getType(){
          var url=labUser.agent_path +'/lessons/types/_v010004';
          ajaxRequest({},url,function(data){
            if(data.status){
              var typeHtml ='';
              if(data.message.length >0){
                $.each(data.message,function(i,j){
                  if(i == 0){
                    videoList(page,pageSize,j.id,'new');
                    $('.getmore').attr('data-id',j.id);
                    typeHtml += '<div class="choosen" data-id="'+j.id+'">'+j.contents+'</div>'
                  }else{
                    typeHtml += '<div data-id="'+j.id+'">'+j.contents+'</div>'
                  }                 
                });

                $('nav.flex-around').html(typeHtml);
              }    
            }
          })
        }

        getType();
        function videoList(page,pageSize,type,click_type){
             var param = {};
                 param.page = page;
                 param.pageSize = pageSize;
                 param.type = type;
             var url=labUser.agent_path +'/lessons/list/_v010004';
             ajaxRequest(param,url,function(data){
                if(data.status){
                  var vodHtml = '';
                  if(data.message){
                    $.each(data.message,function(i,j){
                      vodHtml +='<li class="A_videos_intro l" data-id="'+j.id+'"><div class="A_videos_img mb05"><img src="'+j.image+'" style="height:100%">';
                      vodHtml +='</div><div class="" ><p class="f13 mb05 color333 nowrap">'+j.subject+'</p>';
                      vodHtml +=' <p class="f11 color999 nowrap mb1">浏览量 '+Format(j.view)+'</p></div></li> '
                    });
    
                    if(click_type == 'new'){
                      $('#agent_videos').html(vodHtml);    
                    }else {
                      $('#agent_videos').append(vodHtml);   
                    }       
                  };
                  if(data.message.length < pageSize){
                    $('.getmore').addClass('none');
                    $('.nomore').removeClass('none');
                  }else{
                    $('.getmore').removeClass('none');
                    $('.nomore').addClass('none');
                  }
                }
             })
          };

          $(document).on('click','.getmore',function(){
              page++;
              var type_id = $(this).attr('data-id');
              videoList(page,pageSize,type_id,'old');
          })

        $(document).on('click','nav.flex>div',function(){
            $(this).addClass('choosen');
            $(this).siblings('div').removeClass('choosen');
            var type_id = $(this).attr('data-id');
            page =1;    
            $('.getmore').attr('data-id',type_id);
            videoList(page,pageSize,type_id,'new');
        })
      //格式化浏览量
        function Format(str){
            return (str > 10000)?(parseFloat(str/10000).toFixed(1)+'万'):str;
        }

        $(document).on('click','.A_videos_intro',function(){
          var id= $(this).attr('data-id');
          window.location.href = labUser.path +'/webapp/agent/videoclass/detail/_v010004?id='+id+'&agent_id='+agent_id;
        });

      });
        
    </script>  
@stop