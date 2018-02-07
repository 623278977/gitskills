<!-- Created by wcx -->

@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/_v010004/exam.css" rel="stylesheet" type="text/css"/>
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
                   <!--  <li class="f15 tl white lh45">
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
            <p class="tc b f15">提醒</p>
            <p class="tc f13 b color666">完成本章节学习，提交答案正确:</p>
            <p class="tc f12 color666 right_ans"></p>
            <div class="tc"> <button class="know">知道了</button></div>
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
            var url = labUser.agent_path + '/academy/agent-study-topic-list/_v010004';
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
            var url= labUser.agent_path +'/academy/agent-study-check-out/_v010004';
            ajaxRequest(param,url,function(data){
                if(data.status){
                    $('.right_ans').text(text);
                   $('.fixed').removeClass('none');
                   $('.right').removeClass('none');

                }else{
                    $('.fixed').removeClass('none');
                    $('.error').removeClass('none');
                }
            })
        };

        $(document).on('click','.submit',function(){
            var ques_id = $(this).attr('data-num');  //问题id
            var answ_id = $('#ans_list li .choosen:not(.none)').attr('data-id');//答案的id
            var text = $('#ans_list li .choosen:not(.none)').parent('span').siblings('span').text();
            sendAnswer(ques_id,answ_id,agent_id,text);
        });

        //点击 模态框 确定按钮
        $(document).on('click','.again',function(){
            $('.fixed').addClass('none');
            $('.error').addClass('none');
             getQuestion(param);
        });

        $(document).on('click','.know',function(){
            $('.fixed').addClass('none');
            $('.right').addClass('none');
            backFrontPage();
        })

        function backFrontPage(id) {
             if (isAndroid) {
                  javascript:myObject.backFrontPage();
              }else if (isiOS) {
                var data={
                    'id':id
                };
                window.webkit.messageHandlers.backFrontPage.postMessage(data);
              }
        };

    })
  </script>
@stop