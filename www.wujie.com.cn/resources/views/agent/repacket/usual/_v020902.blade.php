@extends('layouts.default')
@section('css')
    <link href="/css/_v020902/reward.css" rel="stylesheet" type="text/css"/>
    <script src="/js/vue.min.js"></script>
    <script src="/js/_v020902/tool.js"></script>
@stop
@section('main')
    <section class="containerBox" id="containerBox">
             <header></header>
             <article class="mt20" id="scroll">
             	 <ul class="ui-red-bao">
             	 	<li class="mt2" :data-id="message.id">
             	 		<p class="b f32 f04 margin4" v-html="message.amount"></p>
             	 		<p class="f12 f04 margin2">奖励红包</p>
             	 		<p class="opcity">hah</p>
             	 	</li>
             	 	<li>
             	 		<p></p>
             	 		<p class="b f16 color333 margin5">新用户邀请红包</p>
             	 		<p class="f12 color999  margin3">不限使用期限；全场通用红包</p>
             	 		<p class="f12 color999 margin3">可用于考察订金抵扣、品牌加盟费用抵扣</p>
             	 	</li>
             	 </ul>
             	 <div class="clear">
             	 <div class="clear ui-bar"></div>
                 <p class="color666 f12 ui-bety hasnoagent">
                    <span class="slogn">联系您的经纪人，商量喜茶的品牌加盟吧！</span>
                    <button class="ui-use fr f14 b margin6 gochatagent">联系经纪人</button></p>	
             	 </div> 
             </article>
             <span class="ui-span"></span>
             <section class="ui-how-use color666 f12 none">
             	      <div class="ui-bg"></div>
             	      <p class="opcity" style="margin:0 0 0">hah</p>
             	      <ul class="ui-flex-text">
             	      	       <li><img class="ui-pict-size" src="/images/redbg.png"/></li>
             	      	       <li>
             	      	       	   <p>接受经纪人发送的“加盟合同”，同意“支付协议”。</p>
             	      	       	   <p>在线上进进行加盟首付款支付的时候，可以使用品牌红包进行抵扣。</p>
             	      	       	   <p>品牌红包仅可以抵扣该品牌的加盟首付费用。其他品牌无法抵扣。</p>
             	      	       	   <p>仅支持线上抵扣,不支持线下签约付款使用。</p>
             	      	       </li>
             	      </ul>
             	      <p class="clear">请在红包有效期内进行使用，超过期限，则无法正常使用。</p>
             	      <p>请确认红包是否支持叠加使用。</p>
             	      <p style="margin:0 0 0">红包使用如有问题，请联系无界商圈客服人员或您的经纪人。</p>
             </section>
             <div class="ui-has-used none">
             	  <div class="fline ui-title f15 b color333">使用记录</div>
             	  <div class="fline ui-middle f14 color999">
             	  	   <p>加盟合同<span class="fr color333">呵呵呵</span></p>
             	  	   <p>合同号<span class="fr color333">呵呵呵</span></p>
             	  	   <p>加盟品牌<span class="fr color333">呵呵呵</span></p>
             	  	   <p>加盟总费用<span class="fr color333">￥10000,000</span></p>
             	  </div>
             	   <div class="fline ui-footer f14 color999">
             	  	   <p>考察定金折扣<span class="fr color333">-￥10000,000</span></p>
             	  	   <p>通用红包<span class="fr color333">-￥10000,000</span></p>
             	  	   <p>品牌红包<span class="fr color333">-￥10000,000</span></p>
             	  	   <p>奖励红包<span class="fr  fd4d4d">￥10000,000</span></p>
             	  </div>
                   <div class="fline ui-footer-p f14 color999">
                       <p>优惠金额<span class="fr color333">-￥10000,000</span></p>
                       <p>实际应支付<span class="fr color333">-￥10000,000</span></p>
                   </div>
             </div>
             <footer class="f14 f28"><a href="tel:4000110061" class="f14 f28" style="display: block;width: 100%;height:100%">联系我们</a></footer>
    </section>
@stop
@section('endjs')
   
   <script>
    window.onload=function(){
       new Vue({
             el:'#containerBox',
             data:{
                    message:{
                      id:'',
                      name:''
                    }
                },
              methods:{
                 init(id){
                    var _this=this;
                    var params={};
                        params['id']=id;
                        params['_token'] = labUser.token;  
                    var url=labUser.api_path + '/user/package-detail/_v020902';
                    axios({
                          method:'POST' ,
                          url: url,
                          data: params
                    }).then(function (response) {
                             this.message=response.data.message;
                    }.bind(this)).catch(function (error) {
                             console.log(error);
                    });
                    var params={};
                        params['id']=id;
                    var url=labUser.api_path + '/user/package-detail/_v020902';
                    ajaxRequest(params, url, function(data) {
                    if (data.status){
                          this.message=data.message;
                          this.id=data.message.id;
                          alert(typeof(this.message));
                          $('.containerBox').removeClass('none');      
                          }
                    })
                 }
              },
              mounted(){
                var args=getQueryStringArgs();
                   id=args['id'];
                   this.init(id);
              },
         
       })
       }
   </script>
@stop