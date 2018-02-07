
@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/brandDetail.css" rel="stylesheet" type="text/css"/>

@stop

@section('main')
    <section id="brand_detail" class="bgcolor pt3 pl1 pr1">
        <div class="bgwhite learn pt3 pl1 pr1">
            <div class="tiptitle tc">
                品牌代理申请中...
            </div>
            <div>
                <div class="steps tc pb1-5 pt1-5">
                    <div class="stepblue">
                        <p class="lh-1-2 ">发送<br>申请</p>
                        <p class="step">1</p>
                    </div>
                    <div class="blueline"></div>
                    <div>
                        <p class="lh-1-2 color999">参与<br>培训</p>
                        <p class="step">2</p>
                    </div>
                    <div class="grayline"></div>
                    <div>
                        <p class="lh-1-2 color999">等待<br>测试</p>
                        <p class="step">3</p>
                    </div>
                    <div class="grayline"></div>
                     <div>
                        <p class="lh-1-2 color999">获得<br>代理权</p>
                        <p class="step">4</p>
                    </div>
                </div>
                <div class="color666 f12 pl05 pr05">
                    <p  class="mb0">成功提交了品牌代理申请，无界商圈已为您开启该品牌学习板块。</p>
                    <p class="mb0">返回品牌详情，进入学习模块。线上自学视频、文档，线下OVO模式课程辅导。</p>
                    <p class="mb0">最终通过综合测试，获得品牌代理。</p>
                </div>
                <div class="pt1-5 pb1-5 tc">
                    <button class="learnbutton">
                        进入学习模块
                    </button>
                </div>
            </div>
        </div>
    </section>

@stop

@section('endjs')
    <script>  
        Zepto(function(){
             var args = getQueryStringArgs();
             var agent_id = args['agent_id'] || '0',
                 brand_id = args['id'] || '0';
            $('.learnbutton').click(function(){
                window.location.href = labUser.path +'webapp/agent/brand/detail?id='+brand_id+'&agent_id='+agent_id+'&fromlearn=1';
            })
        })
            
    </script>
@stop