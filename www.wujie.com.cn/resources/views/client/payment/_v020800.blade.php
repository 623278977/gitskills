<!-- Created by wangcx -->
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
        .dgzh{
            border-radius: 0.5rem;
            border:1px dashed #666;
            padding: 1.33rem;
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
                <span >*请于<em class="zcrq"></em>前支付相应款项，如有延误等情况，请尽早联系经纪人</span>
            </p>
            <p class="tr f13">支付方式为线下对公账账号转账</p>
            <p class="tr f13">
                <a href="/webapp/agent/way/detail" class="agent_blue">了解尾款补齐操作办法</a>
            </p>
            <div class="dgzh">
                <p class="flexbox f12">
                    <span class="">对公账号：</span>
                    <span id="bank_no"></span>
                </p>
                <p class="flexbox f12">
                    <span class="">所属银行：</span>
                    <span id="bank_name"></span>
                </p>
                <p class="flexbox f12">
                    <span class="">单位名称：</span>
                    <span id="company_name">杭州天涯若比邻网络信息服务有限公司</span>
                </p>
            </div> 
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
   <!--  <div class="wk-fail none">
        <a class="remind">提醒用户完成尾款支付</a>
    </div> -->
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
                            $('.zcrq').text(unix_to_date(datas.tail_leftover));
                            $('#bank_no').text(datas.company_bank_no);
                            $('#bank_name').text(datas.company_bank_name);
                            $('#company_name').text(datas.company_name);
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

            function unix_to_date(unix) {
                    var newDate = new Date();
                    newDate.setTime(unix * 1000);
                    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
                    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate();
                    return M + '月' + D + '日';
                }
       
        })
            
    </script>
@stop