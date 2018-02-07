
@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/brandDetail.css" rel="stylesheet" type="text/css"/>
    <style>
        .keywords span{
            padding:0 0.3rem;
        }
    </style>
@stop

@section('main')
    <section id="brand_detail" class="bgcolor pb8">
        <div class="bgwhite p1-5 mb1-5">
            <div class="l brandlogo mr1">
                <img src="" alt="" class="brandlogo" id="brandlogo">
            </div>
            <div class="l width50">
                <p class="brandname f14 mb0"></p>
                <p class="slogan f11 color999 mb0"></p>
                <p class="f12 color666 mb0">行业分类 <span class="industry color333"></span></p>
                <p class="f12 color666 mb0">启动资金 <span class="agent-red invest"></span></p>
                <p class="f10 color666 mb0 keywords mt05"></p>
            </div>
            <div class="r tr mt3">
                <p class="color999 f12 mb0">已代理人数</p>
                <p class="cffa300 f18 agent_num"></p>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="bgwhite pl1-5 pb1-5 mb1-5">
            <p class="lh45 fline f15">提成说明</p>
            <div class="commission pr1-5">
                
            </div>
        </div>
        <div class="bgwhite pl1-5 pb1-5 mb1-5">
            <p class="lh45 fline f15">代理条件</p>
            <div class="condition pr1-5">
                
            </div>    
        </div>
        <div class="bgwhite p1-5 f12 choose">
            <span class="checkbox c2873ff no_check" ></span>
            <span class="color999 expand_hot">本人已阅读并同意签署</span><span><a href="/webapp/agent/agreement/detail" class="color333">《无界商圈经纪人代理协议》</a></span>
        </div>
        <div class="">
            <button class="applyAgent">同意代理协议并提交申请</button>
        </div>
        <div class="common_pops none"></div>
    </section>
    <section style="position: fixed;bottom: 0;background: #FFFFFF;height:17px" class="iphone_btn none"></section>

@stop

@section('endjs')
    <script>  
        Zepto(function(){
            iphonexBotton('.applyAgent');
             new FastClick(document.body);
             var args = getQueryStringArgs();
             var agent_id = args['agent_id'] || '0',
                 brand_id = args['id'] || '0';
             function getDetail(id,agent_id){
                var url = labUser.agent_path +'/brand/apply-detail/_v010000';
                ajaxRequest({'brand_id':id,'agent_id':agent_id},url,function(data){
                    if(data.status){
                        $('#brandlogo').attr('src',data.message.logo);
                        $('.brandname').text(data.message.title);
                        $('.slogan').text(data.message.slogan);
                        $('.industry').text(data.message.category_name);
                        $('.invest').text(data.message.investment_arrange);
                        $('.agent_num').text(data.message.agent_num+'人');
                        $('.commission').html(data.message.commission_des);
                        $('.condition').html(data.message.condition);
                        var str= '',key = data.message.keywords;
                        if(key.length > 0){
                            for(var i=0;i<key.length;i++){
                                str += '<span>'+key[i]+'</span>';
                            }
                            $('.keywords').html(str);
                        }
                    }
                });
             };    
            getDetail(brand_id,agent_id);

            //申请代理
            function applyAgent(agent_id,id){
                var url = labUser.agent_path +'/brand/apply/_v010000';
                ajaxRequest({'agent_id':agent_id,'brand_id':id},url,function(data){
                    if(data.status){
                        window.location.href = labUser.path + 'webapp/agent/brand/applysuccess?id='+brand_id+'&agent_id='+agent_id;
                        finish();
                    }else{
                        alertShow(data.message);
                    }
                })
            }
            //同意协议
            $('.checkbox').click(function(){
                $(this).toggleClass('no_check');
            });
            $('.expand_hot').click(function(){
                $('.checkbox').toggleClass('no_check');
            })

            $('.applyAgent').click(function(){
                if($('.checkbox').hasClass('no_check')){
                    alertShow('请阅读并勾选无界商圈经纪人代理协议');
                    return;
                }else{
                    applyAgent(agent_id,brand_id);
                }
            });

            function finish(){
                if (isAndroid) {
                    javascript:myObject.finish();
                } else if (isiOS) {
                    var data = {};
                    window.webkit.messageHandlers.finish.postMessage(data);
                }
              };
        })
            
    </script>
@stop