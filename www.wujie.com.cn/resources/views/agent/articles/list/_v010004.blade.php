<!-- Created by wangcx -->

@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/dist/swiper.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/agent/_v010004/list.css" rel="stylesheet" type="text/css"/>
    <!-- <link href="{{URL::asset('/')}}/css/agent/_v010004/articles.css" rel="stylesheet" type="text/css"/> -->
    <style type="text/css">
        .flex{
          display: flex;
        }
        .flex-around{
          justify-content: space-around;
        }
        .lh45{
          height: 4.5rem;
          line-height:4.5rem;
        }
        .c2873ff{
          color:#2873ff;
        }
        .choosen{
          color:#2873ff;
          border-bottom: 2px solid #2873ff;
        }
        .banner{
          padding-bottom:1.2rem;
          background: #f2f2f2;
        }
        .banner .slide_img{
          width: 100%;
          height: 12rem;
        }
        .tf.fline::after{
            top:-1px;
            bottom: 0;
        }

    </style>
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
        <div class="swiper-wrapper">
          <!-- <div class="swiper-slide">
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
          <!--  <div class="ui_con color999">
                  <div class="padding">
                        <ul class="ui_text_pict">
                             <li>
                                 <p class="color333 f14 b ui-nowrap-multi">骤然的爱情，穿不过永不睡哈哈哈哈哈</p>
                                 <p class="f12 ui-nowrap-multi">
                                    狮子（Lion）被假设等同于行走动物（Walk），老鹰（Eagle）被假设等同于飞行动物（Fly）。
                                    这看起来很成功，因为子类能严格向上转型，但他有隐患。
                                    当有一种天马（Pegasus）介入到里面的时候，我们才发现狮子其实只是“会行走的动物”， 
                                 </p>
                             </li>
                             <li>
                              <div class="ui_protect_pict fr"><img class="ui_pict1" src="/images/agent/ui2.png"/></div>
                             </li>
                        </ul>
                        <p class="clear ui-border-b ui_row"></p>
                        <ul class="ui_text_down clear f11">
                              <li>
                                <ul class="ui_flex">
                                    <li>
                                      <img class="ui_zan fl" src="/images/agent/grey.png"/><span class="ui_padding fl">6000</span>
                                    </li>
                                    <li>
                                      <img class="ui_zan ui_mess" src="/images/agent/ui.png"/><span class="ui_padding">6000</span>
                                    </li>
                                    <li>
                                      <img class="ui_seen ui_mess" src="/images/agent/seen.png"/><span class="ui_padding">6000</span>
                                    </li>
                                </ul>
                              </li>
                              <li>作者：无界商圈</li>
                        </ul>
                        <p class="clear margin"></p>
                    </div>
                  <div class="fline style"></div>
           </div> -->
           
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
      document.title = "商圈热文";
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
        $('nav.flex>div').click(function(){
            $(this).addClass('choosen');
            $(this).siblings('div').removeClass('choosen');
        });
        var args = getQueryStringArgs();
        var agent_id = args['agent_id'] || 0;
            
        var page = 1;
        var pageSize =10;
        var type='agent_article';

         //获取分类
        function getType(){
          var url=labUser.agent_path +'/article/types/_v010004';
          ajaxRequest({},url,function(data){
            if(data.status){
              var typeHtml ='';
              if(data.message.length >0){
                $.each(data.message,function(i,j){
                  if(i == 0){
                    $('.getmore').attr('data-id',j.id);
                    articleList(page,pageSize,j.id);
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
        function articleList(page,pageSize,type,click_type){
             var param = {};
                 param.page = page;
                 param.pageSize = pageSize;
                 param.type = type;
             var url=labUser.agent_path +'/article/list/_v010004';
             ajaxRequest(param,url,function(data){
                if(data.status){
                   var html='';
                  if(data.message.data){
                    $.each(data.message.data,function(k,v){
                          
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
                                                        html +='<li><span style="padding-left:0.7rem">作者：'+v.author+'</span></li>';
                                                      }
                                                      
                                                html +='</ul>\
                                                <p class="clear margin"></p>\
                                            </div>\
                                          <div class="fline style"></div>\
                                 </div>';
                        
                    });
                    if(data.message.data.length < pageSize){
                      $('.getmore').addClass('none');
                      $('.nomore').removeClass('none');
                    }else{
                      $('.getmore').removeClass('none');
                      $('.nomore').addClass('none');
                    }          
                  }
                  if(click_type == 'new'){
                    $('.commend').html(html); 
                  }else{
                     $('.commend').append(html);
                  }
                  
                }
             })
          };

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
                  };
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
        //加载更多
         $(document).on('click','.getmore',function(){
              page++;
              var type_id = $(this).attr('data-id');
              articleList(page,pageSize,type_id,'old');
        })

         // banner图片链接
        $(document).on('click','.swiper-slide',function(){
          var url = $(this).attr('data-url');
          window.location.href = url;
        })

         $(document).on('click','nav.flex>div',function(){
            $(this).addClass('choosen');
            $(this).siblings('div').removeClass('choosen');
            var type_id = $(this).attr('data-id');
            $('.getmore').attr('data-id',type_id);
            page =1;
            articleList(page,pageSize,type_id,'new');
        });

         $(document).on('click','.ui_con',function(){
            var id=$(this).attr('data-id');
            window.location.href = labUser.path +'webapp/agent/hotmessage/detail/_v010004?id='+id+'&agent_id='+agent_id;
         })
    
      });
       
    </script>  
@stop