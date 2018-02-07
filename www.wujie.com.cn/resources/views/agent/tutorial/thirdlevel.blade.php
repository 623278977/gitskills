<!-- Created by hongky -->
@extends('layouts.default')
@section('css')
       <style>
      p{
        margin-bottom: 0;
      }
      .lh3-5{
        line-height: 3.5rem;
        height: 3.5rem;
      }
      .lh5-5{
        height: 5.5rem;
        line-height: 5.5rem;
      }
      .lh5{
        padding:1.5rem 1.5rem 1.5rem 0;
      }
      .flex_box{
        display: flex;
        align-items: center;
        justify-content: space-between;
      }
      .todetail{
        width:0.65rem;
        height: 1.2rem;
      }
      .c0{
        color:#000;
      }
    </style>
@stop
@section('main')
    <section class="containerBox bgcolor none" id="containerBox" >
      <div class="f12 color666 lh3-5 ui-border-t pl1-5 ui_type">
        问题类型
      </div>
      <div class="pl1-5 bgwhite ui_contain"></div>
      <div class="ques_detail pl1-5 bgwhite none">
          <div class="outques">
            <div class="ques_ans">
              <p class="f15 fline lh5 c0 ui_name"></p>
              <div class="f15 pt1-5 pr1-5 pb1-5">
                <p style="margin: 0 0 0" class="ui_content"></p>
              </div>
            </div>
          </div>
      </div>
    </section>
@stop
@section('endjs')
   <script type='text/javascript'>
        new FastClick(document.body);
        Zepto(function(){
          var urlPath = window.location.href,
              args = getQueryStringArgs(),
              id = args['id'],
              type = args['type'];
          var detail={
              init:function(){
                  var param = {};
                      param['id']=id;
                      param['type']=type;
                  var url=labUser.agent_path+'/tiro/lists/_v010001';
                  ajaxRequest(param, url, function (data) {
                  if (data.status) {
                                    detail.data(data.message);
                                    detail.go();
                                    $('title').text(data.message.title);
                                    $('.containerBox').removeClass('none');
                                     setPageTitle(data.message.title);
                                     
                                    }
                          })
                  },
              data:function(obj){
                if(obj.jump==1){
                            $.each(obj.lists,function(k,v){
                                var html='';
                                    html+='<div class="flex_box lh5-5 fline" data-id="'+v.id+'" data-type="'+v.type+'">\
                                              <p class="f15 pr1-5">'+v.question+'</p>\
                                              <p class="pr1-5"><img src="/images/agent/to.png" alt="" class="todetail"></p>\
                                           </div>'
                                    $('.ui_contain').append(html);
                })
                }else if(obj.jump==0){
                                     $('.ques_detail').removeClass('none');
                                     $('.ui_type').addClass('none');
                                     $('.ui_name').html(obj.lists.question.replace(/&nbsp;/g,''));
                                     $('.ui_content').html(obj.lists.answer.replace(/&nbsp;/g,''));
                }  
              },
              go:function(){
                  $(document).on('click','.flex_box',function(){
                        var id=$(this).data('id'),
                            type=$(this).data('type');
                  window.location.href = labUser.path+'webapp/agent/tutorial/fourthlevel?id='+id+'&type='+type;
                  })
              }
              }
            detail.init();
           
        })
    </script> 
@stop