<!-- Created by wcx -->

@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/_v010004/exam.css" rel="stylesheet" type="text/css"/>
    <style type="text/css">
        .right{
            width:29.5rem;
            margin-left:-14.75rem;
        }
    </style>
@stop

@section('main')
    <section id="" class="exam_bg none" >
        <div class="p1-5 frame">    
            <div class="white f20 question mb05" id="que_title">
                
            </div>
            <p class="light-blue f11 mb0">选择以下的选项，点击“提交”提交答案。</p>
            <p class="light-blue f11">通过后，将系统判断已完成该条目的学习任务。</p>
            <div class="answers">
                <ul id="ans_list">
                    <!-- <li class="f15 tl white lh45">
                        <span class="choose"><img src="/images/agent/choosen.png" alt="选中1" title="答案1" class="choosen none"></span>
                        <span>A 我不知道hi啊哦</span>
                    </li> -->
                </ul>
            </div>
        </div>
        <div class="tc mt2-5 pb5">
            <button class="submit">提交</button>
        </div>
        <div class="fixed none"></div>
        <div class="right none">
            <!-- <p class="tc b f14 fline" style="padding-bottom: 1rem;">通过测试</p>
            <p class="tc f14 mt2-5">恭喜你已通过该问题的测试</p>
            <div class="tc"><button class="backto mr1-5">知道了</button><button class="learn_next">学习下一章</button></div> -->
           <!--  <p class="tc b f14 fline" style="padding-bottom: 1rem;">学习完成</p>
            <p class="tc f14 mt2-5">恭喜你已学完全部内容，真棒！</p>
            <div class="tc"><button class="know">知道了</button></div> -->
        </div>
        <div class="error none">
            <p class="tc b f15">提醒</p>
            <p class="tc f13 color666">很遗憾，回答错误</p>
            <div class="tc"><button class="again">再来一次</button></div>
        </div>
    </section>
@stop

@section('endjs')
    <script type="text/javascript">
      var $body = $('body');
      document.title = "章节测试";
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
        $(document).on('click','.answers li',function(){
            $(this).find('.choosen').toggleClass('none');
            $(this).siblings().find('.choosen').addClass('none');
        });
        var args = getQueryStringArgs();
        var  id = args['id'] || 0,      //品牌id
             type = args['type'] ,      //学习的类型
             type_id = args['type_id'], //学习的咨询或视频id
             agent_id = args['agent_id']; //经纪人id
    //获取问题
        function getQuestion(param){
            var url = labUser.agent_path + '/academy/agent-study-topic-list/_v010005';
            ajaxRequest(param,url,function(data){
                if(data.status){
                    $('#que_title').text(data.message.stem);
                    if(data.message.lists.length >0){
                        $('.submit').attr('data-num',data.message.lists[0].quiz_id);
                    }
                    
                    var ansHtml = '';
                    $.each(data.message.lists,function(i,j){
                        ansHtml += '<li class="f15 tl white pt1 pb1" >';
                        ansHtml += '<span class="choose l"><img src="/images/agent/choosen.png" alt="选中" class="choosen none" data-id="'+j.option_num+'" ></span>'
                        ansHtml += '<span class="width85 inline-block">'+j.content+'</span></li>'
                    })
                    $('#ans_list').html(ansHtml);
                    $('.exam_bg').removeClass('none');
                }
            })
        };
        var param ={'brand_id':id,'study_type':type,'post_id':type_id};
        getQuestion(param);
       //提交答案
        function sendAnswer(ques_id,answ_id,agent_id,text){
            var param ={};
                param['answer_id'] =answ_id;
                param['issue_id'] =ques_id;
                param['agent_id'] =agent_id;
            var url= labUser.agent_path +'/academy/agent-study-check-out/_v010005';
            ajaxRequest(param,url,function(data){
                if(data.status){
                   $('.fixed').removeClass('none');
                   if(data.message.is_complete == 0){
                        $('.right').html('<p class="tc b f14 fline" style="padding-bottom: 1rem;">通过测试</p>\
                        <p class="tc f14 mt2-5">恭喜你已通过该问题的测试</p>\
                        <div class="tc"><button class="backto mr1-5">知道了</button><button class="learn_next" data-id="'+data.message.content_id+'" data-type="'+data.message.content_type +'" data-num="'+data.message.content_num+'">学习下一章</button></div>');
                   }else{
                        $('.right').html('<p class="tc b f14 fline" style="padding-bottom: 1rem;">学习完成</p>\
                                         <p class="pl2 pr2 f14 mt2-5 mb0">恭喜你已学完全部内容，真棒！</p>\
                                         <p class="f14 pl2 pr2">请保持联络方式畅通，我们稍后会对您进行电话回访测试。</p>\
                                            <div class="tc"><button class="know">知道了</button></div>');
                   }
                   $('.right').removeClass('none');
                   if(type == '1'){
                      followUpState(agent_id,id,'video',type_id)
                   }else if(type == '2'){
                      followUpState(agent_id,id,'news',type_id)
                   }
                   
                }else{
                    $('.error').html('<p class="tc b f15">提醒</p><p class="tc f13 color666">很遗憾，回答错误</p><div class="tc"><button class="again">再来一次</button></div>');
                    $('.fixed').removeClass('none');
                    $('.error').removeClass('none');
                }
            })
        };
    //代理状态跟进（学习成果后，跟进学习状态）
        function followUpState(agent_id,brand_id,type,post_id){
          var param = {};
              param['agent_id'] = agent_id;
              param['brand_id'] = brand_id;
              param['type'] = type;
              param['post_id'] = post_id;
          var url = labUser.agent_path + '/brand/apply-status/_v010100';
          ajaxRequest(param,url,function(data){

          })
        } 
        $(document).on('click','.submit',function(){
            var ques_id = $(this).attr('data-num');  //问题id
            var answ_id = $('#ans_list li .choosen:not(.none)').attr('data-id');//答案的id
            var text = $('#ans_list li .choosen:not(.none)').parent('span').siblings('span').text();
            console.log($('#ans_list li .choosen:not(.none)'));
            if($('#ans_list li .choosen:not(.none)').length == 0){
              $('.error').html('<p class="tc b f15">提醒</p><p class="tc f13 color666">请选择一个答案</p><div class="tc"><button class="tochoose">去选择</button></div>');
              $('.fixed').removeClass('none');
              $('.error').removeClass('none');
            }else{
              sendAnswer(ques_id,answ_id,agent_id,text);
            }
            
        });

        //点击 模态框 确定按钮
        $(document).on('click','.again',function(){
            $('.fixed').addClass('none');
            $('.error').addClass('none');
             getQuestion(param);
        });

         $(document).on('click','.tochoose',function(){
            $('.fixed').addClass('none');
            $('.error').addClass('none');
        });

        $(document).on('click','.know,.backto',function(){
            $('.fixed').addClass('none');
            $('.right').addClass('none');
            backFrontPage();
            // window.location.href = labUser.path + 'webapp/agent/brand/detail/_v010005?id='+id+'&agent_id='+agent_id;
        })
    //下一章学习
        $(document).on('click','.learn_next',function(){
            var type = $(this).attr('data-type');
            var type_id = $(this).attr('data-id');
            var num = $(this).attr('data-num');
            var nexturl = '';
            if(type == 'article'){
                nexturl = labUser.path +'webapp/agent/headline/headlinestudy/_v010005?id='+type_id+'&brand_id='+id+'&agent_id='+agent_id+'&section_id='+num;
            }else if(type == 'video'){
                 nexturl = labUser.path +'webapp/agent/brandvod/detail/_v010005?id='+type_id+'&brand_id='+id+'&agent_id='+agent_id;
            }
             if(isAndroid){
                backFrontPage();
                window.location.href = nexturl;
             }else if(isiOS){
                backFrontPage(nexturl);
             }
            
        })

        function backFrontPage(nexturl) {
             if (isAndroid) {
                  javascript:myObject.backFrontPage();
              }else if (isiOS) {
                var message = {
                    method : 'backFrontPage',
                    params :{
                      'nexturl':nexturl
                    }
                }; 
                window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
              }
        };

    })
  </script>
@stop