@extends('layouts.default')
@section('css')
     <link href="{{URL::asset('/')}}/css/agent/_v010300/list.css" rel="stylesheet" type="text/css"/> 
     <link href="/css/animate.css" rel="stylesheet" type="text/css"/>
     <script src="/js/vue.min.js"></script>    
@stop
@section('main')
    <section class="containerBox none">
    <div class="tips none"></div>
    <article class="animated zoomInLeft">
       <p class="f11 color999 mt1-2 ml1-5"><span class="">您的上级</span></p>
       <div class="chooseclient mt1-2 bgwhite A myleader" >
	       	<!-- <div class="fline choose_kehu" data="1" num="ad">
	       		<div class="investor ">
	       			<p class="mr1"><img src="/images/act_banner.png" class="avatar"/></p>
	       			<div class="investorMes">
	       				<p class=""><span class="f15 color333 mr05" >姚凯</span><img src="/images/agent/girl.png" class="grade" /></p>
	       				<p class=""><span class="f12 color666">上海&nbsp;浦东</span><span class=""></span></p>
	       			</div>
	       		</div>
	       		<img src="/images/agent/rightyellow.png" class="choosen none"/>
	       	</div>
       		
       		<div class="fline choose_kehu" data="2" num="ad">
	       		<div class="investor ">
	       			<p class="mr1"><img src="/images/act_banner.png" class="avatar"/></p>
	       			<div class="investorMes">
	       				<p class=""><span class="f15 color333 mr05" >姚凯</span><img src="/images/agent/girl.png" class="grade" /></p>
	       				<p class=""><span class="f12 color666">上海&nbsp;浦东</span><span class=""></span></p>
	       			</div>
	       		</div>
	       		<img src="/images/agent/rightyellow.png" class="choosen none"/>
	       	</div> -->
       </div>
       <!--其他客户-->
       <p class="f11 color999 mt1-2 ml1-5 "><span class="">您的团队</span></p>   
       <div class="chooseclient mt1-2 bgwhite B myteam">
	       <!-- 	<div class="fline choose_kehu" data="3" num="ad">
	       		<div class="investor ">
	       			<p class="mr1"><img src="/images/act_banner.png" class="avatar"/></p>
	       			<div class="investorMes">
	       				<p class=""><span class="f15 color333 mr05" >姚凯</span><img src="/images/agent/girl.png" class="grade" /></p>
	       				<p class=""><span class="f12 color666">上海&nbsp;浦东</span><span class=""></span></p>
	       			</div>
	       		</div>
	       		<img src="/images/agent/rightyellow.png" class="choosen none"/>
	       	</div>
       		
       		<div class="fline choose_kehu"  data="4" num="ad">
	       		<div class="investor ">
	       			<p class="mr1"><img src="/images/act_banner.png" class="avatar"/></p>
	       			<div class="investorMes">
	       				<p class=""><span class="f15 color333 mr05">姚凯</span><img src="/images/agent/girl.png" class="grade" /></p>
	       				<p class=""><span class="f12 color666">上海&nbsp;浦东</span><span class=""></span></p>
	       			</div>
	       		</div>
	       		<img src="/images/agent/rightyellow.png" class="choosen none"/>
	       	</div> -->
       </div>
       <p class="f11 color999 mt1-2 ml1-5"><span class="">其他经纪人</span></p>   
       <div class="chooseclient mt1-2 bgwhite B otheragent">
	       	<!-- <div class="fline choose_kehu" data="3" num="ad">
	       		<div class="investor ">
	       			<p class="mr1"><img src="/images/act_banner.png" class="avatar"/></p>
	       			<div class="investorMes">
	       				<p class=""><span class="f15 color333 mr05" >姚凯</span><img src="/images/agent/girl.png" class="grade" /></p>
	       				<p class=""><span class="f12 color666">上海&nbsp;浦东</span><span class=""></span></p>
	       			</div>
	       		</div>
	       		<img src="/images/agent/rightyellow.png" class="choosen none"/>
	       	</div>
       		
       		<div class="fline choose_kehu"  data="4" num="ad">
	       		<div class="investor ">
	       			<p class="mr1"><img src="/images/act_banner.png" class="avatar"/></p>
	       			<div class="investorMes">
	       				<p class=""><span class="f15 color333 mr05">姚凯</span><img src="/images/agent/girl.png" class="grade" /></p>
	       				<p class=""><span class="f12 color666">上海&nbsp;浦东</span><span class=""></span></p>
	       			</div>
	       		</div>
	       		<img src="/images/agent/rightyellow.png" class="choosen none"/>
	       	</div> -->
       </div>
     </article>
       <div class="tc none nocomment" id="nocommenttip3">
                <img src="{{URL::asset('/')}}/images/nomessage_icon1.png" style="height: 16rem;width: 16rem;margin:0 auto;display: inline-block;">
       </div>  
    </section>
    <section style="height: 10rem;"></section>
    <div class ="bg-model none">
        <div class ='ui_content'>
        	<div class="ui-tips-title fline b f14 color333 none send">发送成功<img  class="fr ui-size30"  src="/images/020700/w20.png"></div>
        	<div class="ui-tips-title fline b f14 color333 none get">赐福请求已发送<img  class="fr ui-size30"  src="/images/020700/w20.png"></div>
			<p class="color333 f12 centerto none senda">在一起，过福年，好福气，要分享~</p>
			<p class="color333 f12 centerto none geta">邀请投资人获取福卡更快哦!>></p>
			<p class="p5"><center><button class="gochat">知道了</button></center></p>			
        </div>
    </div>
@stop

@section('endjs')
    <script src="/js/agent/_v010300/list.js"></script>
    <script type="text/javascript">
    	$(document).ready(function(){$('title').text('选择发送客户')});
    </script>
   <!--  <script>
    	 var vm=new Vue({
          data:{
              yourleader:[],
              myteam:[],
              otheragent:[]
           },
          methods:{
              init(agent_id){
                        var params={};
                            params['agent_id']=agent_id;
                            params['type']='1,2,3';
                        var url=labUser.agent_path + '/user/agent-relation/_v010300';
                        ajaxRequest(params, url, function(data) {
                        if (data.status){
                                    // this.yourleader=               
                                   }
                          })
                    }
                },
            mounted(){
                    var args=getQueryStringArgs();
                    var agent_id=args['agent_id'];
                    this.init(agent_id);
                }
    }).$mount('.containerBox')
  
    </script> -->
@stop