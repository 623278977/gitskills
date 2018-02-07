
@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/accountDetail.css" rel="stylesheet" type="text/css"/>
    <style>
        .check_commisson{
            background: #2873ff;
            color: #fff;
            font-size: 1.5rem;
            border-radius: 0.3rem;
            padding:0.8rem 2rem;
            border:none;

        }

    </style>
@stop

@section('main')
    <section class="bgwhite p1-5 ">
       <div class="commission">
            <p>
            <span class="f15">成单提成（元）</span>
            <span class="f11 color999 r" id="usually_ques">常见问题</span>
            </p>
            <p class="tc-money cfd4d4d f33"></p>
            <p class="sjdz f13 color999">*按照实际到账情况计算</p>
       </div>
        <div class="commission-2 f15">
                    <p>
                    <span class="color999">成单品牌</span>
                    <span class="brand_name r"></span>
                    </p>
                    <p>
                    <span class="color999">投资人</span>
                    <span class="username r"></span>
                    <span class="nickname r"></span>
                    </p>
                    <p>
                    <span class="color999 ">成单时间</span>
                    <span class="time r"></span>
                    </p>
        </div>
        <div class="mt2 tc">
            <button class="f15 check_commisson">查看我的佣金</button>
        </div>
    </section>
@stop

@section('endjs')
    <script>  
        Zepto(function(){
             var args = getQueryStringArgs();
             var agent_id = args['agent_id'] || '0',
                 id = args['id'] || '0';//合同id
             function getDetail(id,agent_id){
                var url = labUser.agent_path +'/brand/commission/_v010000';
                ajaxRequest({'contract_id':id},url,function(data){
                    if(data.status){
                       $('.tc-money').text(data.message.commission);
                       $('.brand_name').text(data.message.title);
                       $('.username').text('('+data.message.username+')');
                       $('.nickname').text(data.message.realname);
                       $('.time').text(unix_to_yeardate(data.message.success_time));
                    }
                });
             };    
            getDetail(id);

        //查看我的佣金
            $('.check_commisson').click(function(){
                window.location.href = labUser.path +'webapp/agent/mycharge/detail?agent_id='+agent_id;
            });
        //常见问题
            $('#usually_ques').click(function(){
                window.location.href = labUser.path + 'webapp/agent/tutorial/firstlevel?id=1&type=1';
            })
        })
            
    </script>
@stop