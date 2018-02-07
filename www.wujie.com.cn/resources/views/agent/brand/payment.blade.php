
@extends('layouts.default')
@section('css')
    <!-- <link href="{{URL::asset('/')}}/css/agent/accountDetail.css" rel="stylesheet" type="text/css"/> -->
    <style>
        .agent_blue{
            color: #2873ff;
        }
        .flexbox{
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .f33{
            font-size: 3.3rem;
        }
        .f_center{
            padding:0.5rem 1rem;
            background: #fff;
            position: absolute;
            top:50%;left:50%;
            z-index: 2;
            transform: translate(-50%,-45%);
            -webkit-transform: translate(-50%,-45%);
            -moz-transform: translate(-50%,-45%);
            -o-transform: translate(-50%,-45%);
        }
        .remind{
            width: 100%;
            height: 5.5rem;
            background: #fff;
            border:none;
            border-top:1px solid #ddd;
            position: fixed;
            bottom: 0;left: 0;
            font-size: 1.5rem;
            color:#2873ff;
            line-height: 5.5rem;
            text-align: center;
        }
        .c30be74{
            color:#30be74;
        }

    </style>
@stop

@section('main')
    <section class="bgwhite p1-5 ">
        <div class="head ">
            <p class="flexbox">
                <span class="bigmoney f15">加盟总费用</span>
                <span class="money f33"></span>
            </p>
        </div>
        <p class="flexbox fline tc relative">
           <span class="f13 color666 f_center">首付情况</span>
        </p>
    <div class="color666">
    <!-- 首付情况 -->
        <p class="flexbox">
            <span class="f15">首付支付：</span>
            <span class="zfzf-money f13" ></span>
        </p>
        <p class="flexbox">
            <span class="f15">订金抵扣：</span>
            <span class="djdk-money f13" ></span>
        </p>
        <p class="flexbox">
        <span class="f15">创业基金抵扣：</span>
            <span class="cyjj-money f13" ></span>
        </p>
        <p class="flexbox">
            <span class="f15">实际支付：</span>
            <span class="zjtf-money f13" ></span>
        </p>
        <p class="flexbox">
            <span class="f15">支付状态</span>
            <span class="zfzt-money f13" ></span>
        </p>
        <p class="flexbox">
            <span class="f15">支付方式：</span>
            <span class="zffs-money f13" ></span>
        </p>
        <p class="tr">
            <span class="zfzh f13" ></span>
        </p>
        <p class="flexbox">
            <span class="f15">支付时间</span>
            <span class="zffs-time f13" ></span>
        </p>
   
        <!--尾款情况-->
        <p class="flexbox fline tc relative">
            <span class="f13 color666 f_center">尾款情况</span>
        </p>
        <p class="flexbox f13">
            <span class="">尾款补齐：</span>
            <span class="wkbq" ></span>
        </p>
        <p class="flexbox f13">
            <span class="">支付状态：</span>
            <span class="wk-zfzt cfd4d4d"></span>
        </p>
        <div class="wk-fail none">
            <p class="tr f13">
                <span >*请提醒投资人尽快支付尾款费用</span>
            </p>
            <p class="tr f13">支付方式为线下对公账账号转账</p>
            <p class="tr f13">
                <a href="/webapp/agent/way/detail" class="agent_blue">了解尾款补齐操作办法</a>
            </p> 
        </div>
        <div class="wk-suc none">
            <p class="flexbox f13">
                <span>支付方式</span>
                <span class="wk-zffs">银行卡转账</span>
            </p>
            <p class="tr f13 yhzh"></p>
            <p class="flexbox f13">
                <span>到帐时间</span>
                <span class="wk-dzsj"></span>
            </p>
           <!--  <p class="flexbox f13">
                <span>财务确认人</span>
                <span class="cwqrr"></span>
            </p> -->
        </div>
    </div>
    <div class="wk-fail none">
        <a class="remind">提醒用户完成尾款支付</a>
    </div>
     <div class="common_pops none">    
     </div>
    </section>
@stop

@section('endjs')
    <script>  
        Zepto(function(){
             var args = getQueryStringArgs();
             var contract_id = args['id'] || '0';
             var agent_id = args['agent_id'] || '0';
             var customer_id = args['customer_id'] || '0';
                 
             function getDetail(id){
                var url = labUser.agent_path +'/contract/detail/_v010000';
                ajaxRequest({'contract_id':id},url,function(data){
                    if(data.status){
                        var datas= data.message.conreact[0];
                       $('.money').text(datas.amount);//合同总金额
                       $('.zfzf-money').text('￥'+ datas.pre_pay);//首付支付
                       $('.djdk-money').text('-￥'+ datas.invitation);//订金抵扣
                       $('.cyjj-money').text('-￥'+ datas.fund);//创业基金抵扣
                       $('.zjtf-money').text('￥'+ datas.first_amount);//首付实际支付
                       $('.zfzt-money').text(datas.first_pay_status);//首付支付状态
                       $('.zffs-money').text(datas.pay_way);//支付方式
                       $('.zfzh').text(datas.buyer_id);//支付账号
                       $('.zffs-time').text(unix_to_fulltime_hms(datas.pay_at));//支付时间
                       $('.wkbq').text('￥'+datas.tail_pay);//尾款补齐
                       $('.wk-zfzt').text(datas.tail_pay_status);//尾款支付状态
                       if(datas.status ==1){
                            $('.wk-fail').removeClass('none');
                       }else {
                            $('.wk-suc').removeClass('none');
                            $('.wk-zfzt').removeClass('cfd4d4d').addClass('c30be74');
                            $('.yhzh').text(datas.bank_no + ' ('+datas.bank_name+')');
                            $('.wk-dzsj').text(unix_to_fulltime_hms(datas.tail_pay_at));//暂未返回
                            // $('.cwqrr').text(datas.auditor);
                       };

                    }
                });
             };    
            getDetail(contract_id);
        //获取客户电话
            function getTelnum(id,customer_id){
                var url = labUser.agent_path +'/customer/detail-infos/_v010000';
                ajaxRequest({'agent_id':id,'customer_id':customer_id},url,function(data){
                    if(data.status){
                        if(data.message.has_tel == 0){  
                            $('.remind').attr('href','javascript:alertShow("对方号码未公开");');
                        }else{
                             $('.remind').attr('href','tel:'+data.message.relation_tel);
                        };
                        
                    }
                })
            };
            getTelnum(agent_id,customer_id);

            // 提示框
              function alertShow(content){
                  $(".common_pops").text(content);
                  $(".common_pops").css("display","block");
                  setTimeout(function(){$(".common_pops").css("display","none")},2000);
             };
        })
            
    </script>
@stop