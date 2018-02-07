     //byHongky 
      new FastClick(document.body);
            var urlPath = window.location.href,
                args = getQueryStringArgs(),
                type= args['id'],
                agent_id=args['agent_id'],
                is_num=args['is_num'],
                page=1,
                pagesize=3;
            var Param ={
                      "type": type,
                      "page":page,
                      "size": pagesize
                      }
            $(document).ready(function(){
              $('title').text('知识树-'+is_num);
            })
            function List(){};
                     List.prototype.init=function(){
                                  var param={};
                                      param['type']=type;
                                      param['page']=page;
                                      param['size']=pagesize;
                                  var url=labUser.agent_path+'/knowledge/list/_v010004';
                                  ajaxRequest(param, url, function (data) {
                                          if (data.status) {
                                                 if(data.message.total==0){
                                                              $('.ui-list').addClass('none');
                                                              $('.getmore').addClass('none');
                                                              $('#nocommenttip2').removeClass('none');
                                                    }else{
                                                 $.each(data.message.data,function(k,v){
                                                      var  html='';
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
                                                        if(param.page==1){
                                                              $('.ui-list').append(html); 
                                                             if(data.message.data.length == 0){
                                                              $('.ui-list').addClass('none');
                                                              $('.getmore').addClass('none');
                                                              $('#nocommenttip2').removeClass('none');
                                                            }
                                                             if(data.message.total<=3){
                                                              $('.getmore').text('没有更多了…').attr('disabled',true);
                                                             }else{
                                                              $('.getmore').text('点击加载更多').removeAttr('disabled');
                                                              }
                                                          }else{
                                                              $('.ui-list').append(html);
                                                            if(data.message.data.length<3){
                                                              $('.getmore').text('没有更多了…').attr('disabled',true);
                                                           }   
                                                          }           
                                                     })
                                                 } 
                                                 $('#act_container').removeClass('none');
                                            }
                                          });

                                     }
            var instance=new List();
                instance.init(Param);
            $(document).on('click','.ui_con',function(){
              var id=$(this).data('id');
              onAgentEvent('hot_article','',{'type':'hot_article','id':id,'userId':agent_id,'position':'3'});
              window.location.href=labUser.path+'webapp/agent/hotmessage/detail/_v010004?id='+id+'&agent_id='+agent_id;
            })
                //加载更多的打卡用户
            $('.getmore').on('click',function(){
                     page++;  
                    instance.init(Param);
             })