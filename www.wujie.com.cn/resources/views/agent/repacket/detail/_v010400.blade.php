@extends('layouts.default')
@section('css')
    <link href="/css/_v020902/quan.css" rel="stylesheet" type="text/css"/>
    <script src="/js/vue.min.js"></script>
    <script src="/js/_v020902/tool.js"></script>
@stop
@section('main')
    <section class="containerBox none animated zoomIn" id="containerBox" v-show="message!=''">
             <header></header>
             <article class="mt20 ">
             	 <ul class="ui-red-bao">
             	 	<li class="mt2">
             	 		<p class="b f32 f04 margin4">￥1000</p>
                        <p v-show="message.status==0" class="f12 f04 margin2 color666">待激活</p>
             	 		<p v-show="message.status==1" class="f12 f04 margin2">可提现</p>
             	 		<p class="opcity">hah</p>
             	 	</li>
             	 	<li>
             	 		<p></p>
             	 		<p class="b f16 color333 margin5">新用户邀请红包</p>
             	 		<p class="f12 color999  margin3" v-html="message.in_time+'已入账'"></p>
                        <p v-show="message.status==0" class="f12 color999  margin3">待邀请投资人加盟成单激活红包</p>
             	 		<p v-show="message.status==1" class="f12 color999  margin3" v-html="message.active_time+'已激活'"></p>
             	 	</li>
             	 </ul>
             	 <div class="clear">
             	 <div class="clear ui-bar" v-show="message.status==0"></div>
                 <p class="color666 f12 ui-bety" v-show="message.status==0">快去发现你中意的品牌吧！<button class="ui-use fr f14 b margin6 gochat" :data-nickname="message.invite_agent">进入聊天窗</button></p>	
             	 </div> 
             </article>
             <span class="ui-span"></span>
             <section class="ui-how-use color666 f12 "  v-show="message.status==0">
             	      <div class="ui-bg"></div>
                      <p class="opcity" style="margin:0 0 0">hah</p>
             	      <p class="clear">
                        <img class="tranform001" src="/images/020902/u.png"/>
                        <span style="padding-left: 0.2rem">所邀请的投资人在平台成功加盟品牌这时候,您获得的千元邀请奖券</span>
                        <span style="padding-left: 1rem">会直接兑现，变现成现金！</span></p>
             	      <p>
                         <img class="tranform001" src="/images/020902/u.png"/>
                         <span  style="padding-left: 0.2rem">您可以关注投资人在平台上的动态，也可以推荐优质品牌给投资人</span>
                         <span  style="padding-left: 1rem">促使他们加盟品牌。</span>
                     </p>
             	      <p style="margin:0 0 0">
                        <img class="tranform001" src="/images/020902/u.png"/>
                        <span>商机掌握在您手中，不要让他溜走！快和您的邀请投资人联系吧！</span>
                      </p>
                      <p class="f10" style="text-align:center;margin:4rem 0 0">有疑问联系我们的客服人员，最终解释归无界商圈所有。</p>
             </section>
             <div class="ui-has-used" v-show="message.status==1">
             	  <div class="fline ui-title f15 b color333">详情说明</div>
             	  <div class="fline ui-footer f14 color999">
             	  	   <p>投资人<span class="fr color333" v-html="message.customer_name"></span></p>
             	  	   <p>加盟品牌<span class="fr color333" v-html="message.brand_name"></span></p>
             	  	   <p>加盟金额<span class="fr color333" v-html="'￥'+message.amount"></span></p>
             	  	   <p>促单经纪人<span class="fr color333" v-html="message.follow_agent"></span></p>
             	  	   <p>邀请经纪人<span class="fr color333" v-html="message.invite_agent"></span></p>
             	  	   <p>成交时间<span class="fr color333" v-html="message.active_time"></span></p>
             	  </div>
             </div>
             <footer class="f14 f28 clear"><a href="tel:4000110061" class="f14 f28" style="display: block;width: 100%;height:100%">联系我们</a></footer>
    </section>
@stop
@section('endjs')
   <script>
   	    $(document).ready(function(){
   	    	$('title').text('奖券')
   	    })
   </script>
   <script> 
          var vm= new Vue({
                 el:'#containerBox',
                 data:{
                    message:{
                          id:'',
                          name:'',
                          status:'',
                          in_time:''
                        }
                    },
                  methods:{
                     init(agent_id,id){
                        var params={};
                            params['id']=id;
                            params['agent_id']=agent_id;
                            params['_token'] = labUser.token;  
                        var url=labUser.agent_path  + '/user/package-detail/_010100';
                        axios({
                              method:'POST',
                              url: url,
                              data: params
                        }).then(function (response) {
                                this.message=response.data.message;
                                $('#containerBox').removeClass('none');
                                console.log(this.message);
                        }.bind(this)).catch(function (error) {
                                 console.log(error);
                        });
                     }
                  },
                  mounted(){
                    var args=getQueryStringArgs();
                        agent_id=args['agent_id'],
                        id=args['id'];
                        this.init(id,agent_id);
                        $('.gochat').data('id',agent_id); 
                       
                  },
             
           });
   </script>
   <script>
          $(document).on('click','.gochat',function(){
              var uid=$(this).attr('data-id'),
                  nickname=$(this).attr('data-nickname');
                  goChat('c', uid, nickname);
          
        });
       
    function goChat(uType, uid, nickname) {
    if (isAndroid) {
      javascript: myObject.goChat(uType, uid, nickname);
    }
    else if (isiOS) {
         var message = {
                method:'goChat',
                params:{
                    'uType': uType,
                    'id': uid,
                    'name':nickname
                }
            }; 
      window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
    }
  };
   </script>
@stop