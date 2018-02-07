@extends('layouts.default')
@section('css')
    <link href="/css/_v020902/order.css" rel="stylesheet" type="text/css"/>
    <script src="/js/vue.min.js"></script>
@stop
@section('main')
    <section class="containerBox none" id="containerBox">
            <header>
                <div class="ui-brand-name f16 color333 b">
                    <img class="ui-size1" src="/images/020902/n.png"/>
                    <span id="brandtitle">成功加盟 喜茶 品牌</span>
                </div>
                <div class="ui-icon mt4">
                    <img class="ui-size2" src="/images/020902/r.png"/> 
                </div>
                <p class=" color666 ui-size3 mb05 f10">* 订单生成后，请尽快线下打款或联系经纪人POS机刷卡支付，结清加盟款项。</p>
                <p class=" color666 ui-size3 mt05 f10 mb05">* 款项结清后，请在订单页点击 
                    <span style="color:#fd4d4d">“ 完成支付 ” </span>。
                    待财务人员确认款项到齐，会发放相应<br/><span style="padding-left: 0.7rem">返现奖励。</span>
                </p>
                <p class=" color666 ui-size3 mb05 f10 mt05">* 返现奖励为生成订单中标识的红包优惠，请添加您的支付宝或银行卡，确认后将2个
                    <br/><span style="padding-left: 0.7rem">工作日内打款</span>
                </p>
                <p class=" color999 ui-size3 mt05 f10 mt2">如有其它疑问，请联系您的经纪人或无界商圈客服人员，取得相应联系。</p>
            </header>
            <div class="ui-join color999 f14 A">
                <div class="ui-title fline f15 b color333">加盟信息</div> 
                <div class="ui-text">
                    <p>合同/协议 <span class="fr color333 a1">品牌加盟</span></p> 
                    <p>合同号 <span class="fr color333 a2">品牌加盟</span></p> 
                    <p>加盟方式<span class="fr fd4d4d a3">品牌加盟</span></p> 
                    <p>加盟品牌 <span class="fr color333 a4">品牌加盟</span></p> 
                    <p>经纪人 <span class="fr color333 a5">品牌加盟</span></p> 
                    <p>合同撰写 <span class="fr color333">无界商圈法务代表</span><br/><span style="padding-top: 1rem" class="fr color333 a6">哈哈哈哈</span></p> 
                    <p class="clear mt4">总费用 <span class="fr color333 a7">￥12，0000</span></p> 
                </div> 
            </div>
            <!-- 费用状况 -->
            <div class="ui-join color333 f14 B">
                <div class="ui-title fline f15 b color333">红包返现</div> 
                <div class="ui-text">
                    <p><img class="ui-money" src="/images/020902/o.png"><span class="pl05">初创红包</span> <span class="fr fd4d4d b1">-￥1,200</span></p> 
                    <p>
                        <img class="ui-money" src="/images/020902/c.png">
                        <span class="pl05">红包优惠</span>
                        <!-- <img class="ui-jt fr" src="/images/rightjt.png"> -->
                        <span class="fr fd4d4d b2">-￥1,200</span>
                    </p> 
                    <p>
                        <img class="ui-money" src="/images/020902/p.png">
                        <span class="pl05">考察定金</span>
                        <!-- <img class="ui-jt fr" src="/images/rightjt.png"> -->
                        <span class="fr fd4d4d b3">-￥1,200</span> 
                    </p> 
                    <p>
                        <img class="ui-money" src="/images/020902/q.png">
                        <span class="pl05">意向加盟金</span>
                        <!-- <img class="ui-jt fr" src="/images/rightjt.png"> -->
                        <span class="fr color333 b4">未支付过意向加盟金</span>
                    </p> 
                </div> 
                <div class="ui-title  f14  color999 toright  transform10">
                    <p>通过您获得的品牌专享红包、全场通用红包等叠加<br/>
                        红包返现只有将品牌加盟金额全款交齐<br/>
                    才会以现金形式返现到您的支付宝或银行卡</br>
                    </p>
                </div> 
            </div>
            <!-- 支付情况 -->
             <div class="ui-join color999 f14 A mb2">
                <div class="ui-title fline f15 b color333">支付情况</div> 
                <div class="ui-text ">
                    <p>已累计付款 <span id="" class="fr color333 c1">￥120000</span></p> 
                    <p>剩余款项 <span class="fr fd4d4d c2">￥130000</span></p>
                    <ul class="ui_contrack_detail c3">
                            <li>
                                <img class="ui_img6"  src="{{URL::asset('/')}}/images/020700/bargain2.png">
                            </li>
                            <li>
                                <p class="f14 b textleft color333 margin05 c4">喜茶加盟电子合同</p>
                                <p class="f11 textleft color666 up none">合同编号：</p>
                            </li>
                            <li>
                                <img class="ui_img7"  src="{{URL::asset('/')}}/images/020902/f.png">
                            </li>
                    </ul> 
                </div> 
            </div>
            <div class="clear" style="height:6rem"></div>
    </section>
@stop
@section('endjs')
   <script src="/js/_v020902/order.js"></script>
   <script>
   	    $(document).ready(function(){
   	    	$('title').text('订单生成')
   	    })
   </script>
  <!--  <script>
           new FastClick(document.body);
           var vm=new Vue({
                data:{
                     contract:{},
                },
                methods:{
                    init(uid,order_no){
                        var params={};
                            params['uid']=uid;
                            params['order_no']=order_no;
                        var url=labUser.api_path + '/user/myorderinfo/_v020902';
                        ajaxRequest(params, url, function(data) {
                        if (data.status){
                                                   
                                   }
                          })
                    }
                },
                mounted(){
                    var args=getQueryStringArgs();
                    var uid=args['uid'];
                    var order_no= args['order_no'];
                    this.init(uid,order_no);
                }
           }).$mount('#containerBox')
   </script> -->
@stop