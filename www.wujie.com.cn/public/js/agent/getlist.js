Zepto(function(){
        new FastClick(document.body);
        var args = getQueryStringArgs();
        var agent_id = args['agent_id'] || 0;     
        var page = 1;
        var size =10;
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
                     $('.containerBox').attr('data-id',j.id);
                     articleList(page,size,j.id);
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
        function articleList(page,size,type){
             var param = {};
                 param.page = page;
                 param.size = size;
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
                    if(data.message.data.length < size){
                      $('.getmore').addClass('none');
                      $('.nomore').removeClass('none');
                    }else{
                      $('.getmore').removeClass('none');
                      $('.nomore').addClass('none');
                    }          
                  }
                  $('.commend').append(html);
                  
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
        //  $(document).on('click','.getmore',function(){
        //       page++;
        //       var type_id = $(this).attr('data-id');
        //       articleList(page,size,type_id);
        // })
      
	     $(window).scroll(function() {
	     	var timers=null;
	        if (($(window).height() + $(window).scrollTop() + 0) >= $(document).height()) {
	            clearTimeout(timers);//timers 在外部初次定义为null
	            timers = setTimeout(function() {
	                page++;
	                var type_id = $('.containerBox').attr('data-id');
	                articleList(page,size,type_id);
	            }, 300);
	        }
	    });
         // banner图片链接
        $(document).on('click','.swiper-slide',function(){
          var url = $(this).attr('data-url');
          window.location.href = url;
        })

         $(document).on('click','nav.flex>div',function(){
            $(this).addClass('choosen');
            $(this).siblings('div').removeClass('choosen');
            $('.commend').empty();
            $('.getmore').addClass('none');
            $('.nomore').addClass('none');
            var type_id = $(this).attr('data-id');
            // $('.containerBox').data('id',type_id);
            $('.containerBox').attr('data-id',type_id);
            page =1;
            articleList(page,size,type_id);
        });

         $(document).on('click','.ui_con',function(){
            var id=$(this).attr('data-id');
            onAgentEvent('hot_article','',{'type':'hot_article','id':id,'userId':agent_id,'position':'2'});
            window.location.href = labUser.path +'webapp/agent/hotmessage/detail/_v010004?id='+id+'&agent_id='+agent_id;
         })
    
});
       
  