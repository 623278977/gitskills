
@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/brand.css" rel="stylesheet" type="text/css"/>
        <link href="{{URL::asset('/')}}/css/act_detail.css?v=1.0.0" rel="stylesheet" type="text/css"/>

    
@stop
@section('main')
 <!--安装app-->
        <div class="install-app none" id="installapp">
            <img src="{{URL::asset('/')}}/images/dock-logo.png" alt="">
            <div class="fl pl1">
                <span>无界商圈</span><br>
                <span>用无界商圈找无限商机</span>
            </div>
            <a href="javascript:;" class="install-close f24">×</a>
            <a href="javascript:;" class="install-open" id="openapp">立即开启</a>
        </div>
    <section class="bgcolor pt1-5" id="brand_ques">
       
        <div class="commentback none" id="commentback">
            <div style="width: 100%;height:100%;position:absolute;bottom:15rem;left:0;" id="tapdiv"></div>
            <div class="textareacon">
                <textarea class="f12" name="comment" id="comtextarea" cols="30" rows="10" maxlength="150" style="resize: none;"
                          placeholder="请输入5-150字的项目问题，请尽量描述"></textarea>
                <button class="fr subcomment f16" id="brand_ask" >提交</button>
            </div>
        </div>
         <!--浏览器打开提示-->
        <div class="safari none">
            <img src="{{URL::asset('/')}}/images/safari.png">
        </div>
    </section>
@stop

@section('endjs')
   <!-- <script src="{{URL::asset('/')}}/js/brand.js"></script> -->
   <script>
   Zepto(function () {
         //判断版本来调整顶部的悬浮条
        if (window.location.href.indexOf('_v020502')!=-1) {
            $('.install-app').addClass('install-app2');
            $('#installapp img').attr('src','{{URL::asset('/')}}/images/dock-logo2.png')
        }
        $('body').addClass('bgcolor');
        new FastClick(document.body);
        var args = getQueryStringArgs();
        var uid = args['uid'];
        var id = args['id'];
        var urlPath = window.location.href;
        var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
        if(shareFlag){
            $('.install-app').removeClass('none');
             if (is_weixin()) {
                /**微信内置浏览器**/
                $(document).on('tap', '#brand_loadAPP,#openapp', function() {
                    var _height = $(document).height();
                    $('.safari').css('height', _height);
                    $('.safari').removeClass('none');
                });
                // 点击隐藏蒙层
                $(document).on('tap', '.safari', function() {
                    $(this).addClass('none');
                });
        }else {
                if (isiOS) {
                    //打开本地a
                    $(document).on('tap', '#openapp', function() {
                        var strPath = window.location.pathname.substring(1);
                        var strParam = window.location.search;
                        var appurl = strPath + strParam;
                        var share = '&is_share';
                        var appurl2 = appurl.replace(/is_share=1/g, '');
                        window.location.href = 'openwjsq://' + appurl2;
                    });
                    /**下载app**/
                    $(document).on('tap', '#brand_loadAPP', function() {
                        window.location.href = 'https://itunes.apple.com/app/id981501194';
                    });
                } else if (isAndroid) {
                    $(document).on('tap', '#brand_loadAPP', function() {
                        window.location.href = 'http://a.app.qq.com/o/simple.jsp?pkgname=com.tyrbl.wujiesq';
                    });
                    $(document).on('tap', '#openapp', function() {
                        var strPath = window.location.pathname;
                        var strParam = window.location.search.replace(/is_share=1/g, '');
                        var appurl = strPath + strParam;
                        window.location.href = 'openwjsq://welcome' + appurl;
                    });
                }
            };
        }

        $('#tapdiv').on('click',function () {
            $('#commentback').addClass('none');
        });
        $('#comtextarea').on('focus', function () {
            setTimeout(function () {
                var c = window.document.body.scrollHeight;
                window.scroll(0, c);
            }, 500);
            return false;
        });
       
        var inputtext = document.getElementById('comtextarea');
        var submitbtn = document.getElementById('brand_ask');
        inputtext.oninput = function () {
            var text = this.value;
            if(text.length>4){
                submitbtn.style.backgroundColor = '#ff5a00';
            }
            else{
                submitbtn.style.backgroundColor = '#999';
            }
        }
        /**评论按钮绑定input选中**/
        $("#brand-ques").bind("click", function () {
            
            $('#commentback').removeClass('none');
            $('.textareacon textarea').focus();
            $('#tapdiv').one('click', function () {
                $('#comtextarea').val('');
                $('#commentback').addClass('none');
                $('#brand_ask').css('backgroundColor','#999');
            });
        });
        // 点击提问提交
        $('#brand_ask').on('click',function () {
            var content = $('#comtextarea').val();
            if (content.length<5||content.length>150) {
                alert('请输入5-150字的项目问题');
                // return false;
                $('#comtextarea').focus();
            }else{
                quesDetail.ask(id,uid,content);
            }
        });

        var quesDetail = {
            detail:function (id) {
                var param= {};
                param['brand_id'] = id;
                var url = labUser.api_path + '/brand/question/_v020500';
                ajaxRequest(param,url,function (data) {
                    if (data.status) {
                        getQuesDetail(data.message);
                    }
                })
            },
             ask:function (id,uid,content) {
                var param= {};
                param['id'] = id;
                param['uid'] = uid;
                param['content'] = content;
                var url = labUser.api_path + '/brand/ask';
                // var url = '/api/brand/ask';
                ajaxRequest(param,url,function (data) {
                    if (data.status) {
                        alert('你的提问已发送至无界商圈后台，稍后会有客服跟您联系');
                        $('#comtextarea').val('');
                        $('#commentback').addClass('none');
                        $('#subcomments').css('backgroundColor','#999');
                        onEvent('brand_detail_ask_question_submit');
                        // sencondShare('intent');
                        return false;

                        
                    }
                })
            },
        };
        function getQuesDetail(result) {
            $.each(result,function (i,item) {
                var str='';
                str+=['<div class="brand-info white-bg mb1-5  ">',
                        '<div class="brand-pl  relative">',
                            '<div class="info-head fline ">',
                                '<span class="tleft f16 "> <em class="brand-ask f12 fl " >问</em><span class="f16w color333 width90 fl pt1-2 pb1-2">'+item.quiz+'</span></span>',
                                '<div class="clearfix"></div>',
                            '</div>',      
                            '<div class="clearfix"></div>',
                            '<div class="info-head  ">',
                                '<span class="tleft f16 "> <span class="brand-answer f12 fl">答</span><span class="f14 color333 width90 fl pt1-2 pb1-2 color8a">'+item.answer+'</span></span>',
                                '<div class="clearfix"></div>',
                            '</div>',       
                        '</div>',
                    '</div>'].join('');
                $('#brand_ques').append(str);
            })
        }
        quesDetail.detail(id);    
   });
    function quesBrand() {
        $('#commentback').removeClass('none');
    }
    function onEvent(str) {
        if (isAndroid) {
            javascript:jsUmsAgent.onEvent(str);
        } else if (isiOS) {
            var data = {
                'brand':str
            };
            window.webkit.messageHandlers.onEvent.postMessage(data);
        }
    }
        
   </script>
@stop