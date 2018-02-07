@extends('layouts.default')
@section('css')
   <style>
        p,h2,h3,h4,h5,h6{
            margin:0;
            padding:0;
        }
        h1,h2,h3,h4,h5{
            font-weight: bold;
             font-family:"Hiragino Sans GB","Microsoft Yahei UI","Microsoft Yahei","微软雅黑","Segoe UI",Tahoma,"宋体b8b体",SimSun,sans-serif;
        }
        .sec{
            font-family:"Hiragino Sans GB","Microsoft Yahei UI","Microsoft Yahei","微软雅黑","Segoe UI",Tahoma,"宋体b8b体",SimSun,sans-serif;
            color:#333;
            padding:1rem ;
            font-size: 1.2rem;
            background-color: #fff;
        }
        .pl{
            padding-left:2rem;
        }
        ul>li{
            text-align: left;
        }
   </style>
@stop
@section('main')
    <section class='sec'>
        <h2 style='font-size: 2rem;margin-bottom: 2rem;'>退款政策</h2>
        <p style='text-indent: 2em;text-indent: 2em;'>如果你于正式投资之前决定放弃投资，可申请退款。退款须遵守杭州天涯若比邻网络信息服务有限公司（下称“天涯若比邻”）和品牌方退款政策。对于符合退款条件的意向投资方，请在缴纳预付金（如定金、订金等）起15天内提起申请退款。
        </p>
        <h5 style='margin-top:1rem'> 退款须知 </h5>
        <p>
            1、退款仅限你实际支付款项，且不包含平台积分、红包等折扣款及依据相关协议非退款项；
         </p>    
        <p> 2、退款条件应符合天涯若比邻和品牌方相关退款政策规定；
        </p>
        <p> 3、你应规定时间内提起退款申请，并提交《退款协议书》（格式见附件）；
        </p>
        <p>4、你应退还天涯若比邻或品牌方开具的相关发票或收据。</p>
        <h5 style='margin-top:1rem'>退还发票/收据</h5>
        <p>如果你索取并收到了普通发票或收据，则须在退款前退回。若你的发票或收据上包含不可退款的款项，请在退回发票或收据并完成退款后致电客服。</p>
        <h5 style='margin-top:1rem'>退款</h5>
         <p> 天涯若比邻收到退款申请后，就会根据相关协议在15个工作日内处理你的退款申请。</p>
         <p> 如果你订购时的付款方式是银行转账或现金付款，请用电子邮件发送以下信息至 <a  style='text-decoration:underline;font-weight:bold;color:#333;'> tyrbl@tyrbl.com  </a> 。
         </p>
           <ul>
               <li>•    账户持有人姓名:</li>
               <li>•    银行账号:</li>
               <li>•    银行全称:</li>
               <li>•    银行（分）支行名称:</li>
               <li>•    银行（分）支行所在市/省:</li>
               <li>•    账户持有人电子邮件:</li>
           </ul>
            <div style='font-weight: bold;'>
                 <p >如有疑问，请致电：  <a style='text-decoration:underline;'>400-011-0061</a>，客服人员将介入协调解答；客服电话接听时间10：00-11:30,13:00-17:00，其他时间来电，问题若无法解决，请见谅！
                 </p>
                <p>本条款应视为《服务条款》及相关协议的重要补充约定。</p>
                 <p style='text-align: right;margin-top:1rem'> 杭州天涯若比邻网络信息服务有限公司</p>
                <p style='text-align: right;padding-right:3rem;'>2016年11月21日</p>
            </div>


           <h5 style='margin-top:15rem'>附件：</h5>  
           <h2 style='font-size: 2rem;text-align:center;margin:2rem 0;'>退款协议书</h2>
            <p style="text-indent:2em;">因个人原因，本人撤回并放弃对 <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>（品牌方或被投资方，下称“品牌方”） <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>           项目（下称“项目”）的意向投资。特申请贵司退回本人预付款项，合计<u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;元</u> 
            <u>（大写金额：     ）</u> 。
            </p> 
            <p style="text-indent:2em;">本人收款账户为：</p>
            <p style="text-indent:2em;">开户行： </p>                            
            <p style="text-indent:2em;">开户账号： </p>                           
            <p style="text-indent:2em;">开户银行名称：</p>                         
            <p style="text-indent:2em;">联系电话：</p>
            </p>
            <p>以后出现任何问题与贵司没有任何关系。</p>
            <p >注：保证金 <u>&nbsp;&nbsp;&nbsp;&nbsp;</u> 元，方式为汇款（  ）现金（  ），本人于本协议签订之日归还贵司收据，若以后有保证金收据出现，声明无效，特此证明！</p>
            <p style="text-indent:2em;">此致！</p>
            
            <p style='margin-left:60%'>申请人（签章）：</p>
            <p style='margin-left:60%'>时间：</p>
            <p style="height:5rem;"></p>                                                                    
    </section>
@stop

@section('endjs')
    
@stop