
@extends('layouts.default')
@section('css')
     <!--<link href="{{URL::asset('/')}}/css/agent/_v010300/createsuccess.css" rel="stylesheet" type="text/css"/>--> 
    <style type="text/css">
    	p {
    		margin-bottom: 0;
    	}
    	.tongyong {
			background-color: #ffffff;
			box-shadow: 0rem 0.15rem 0.5rem 0rem rgba(0, 0, 0, 0.1);
			border-radius: 0.3rem;
			margin: 0 auto;
    	}
    	.ty_detail {
    		display: flex;
    		justify-content: space-between;
    		align-items: center;
    		width: 33rem;
			/*height: 6.55rem;*/
			background-color: #fafafa;
			border-radius: 0.3rem;
			border: solid 1px #e5e5e5;
			padding-left: 1.25rem;
			padding-right: 1.85rem;
			margin: 0 auto;
    	}
    	.cf2403c {
    		color: #f2403c;
    	}
    	.ty_detail>p:nth-child(3) {
    		text-align: center;
    	}
    	.send {
    		width: 29.85rem;
			height: 3.5rem;
			background-color: #2873ff;
			border-radius: 0.2rem;
			text-align: center;
			line-height: 3.5rem;
			margin-left: auto;
			margin-right: auto;
    	}
    	.ty_detail>div{
    		display: flex;
    		justify-content: flex-start;
    		align-items: center;
    	}
    	.brand_logo img {
    		width: 5rem;
    		height: 5rem;
    		border-radius: 0.2rem;
    	}
    	.ty_text,.brand_text,.useGeneral_title,.call_we {
    		text-align: center;
    	}
    	.useGeneral_title img {
    		width: 1.55rem;
			height: 0.6rem;
    	}
    	.useGeneral_title span {
    		font-size: 1.7rem;
    		color: #f13335;
    	}
    	.use_general,.acquire_hb {
    		background-color: #ffffff;
			box-shadow: 0rem 0.15rem 0.5rem 0rem rgba(0, 0, 0, 0.1);
			border-radius: 0.3rem;
    	}
    	.useGeneral_img {
    		display: flex;
    		align-items: flex-start;
    	}
    	.useGeneral_img img{
    		width: 9rem;
			height: 11.5rem;
			float: left;
    	}
    	.useGeneral_img  p span {
    		line-height: 2rem;
    	}
    	.acquire_method p span:nth-child(1) {
    		background-color: #f13335;
			border-radius: 2rem;
			padding: 0.2rem 0.75rem;
			display: inline-block;
    	}
    	.call_we a{
    		font-size: 1.3rem;
    		color: #2873ff;
    	}
    	.brand_logo_p {
    		display: flex;
    		flex-direction: column;
    		justify-content: flex-start;
    	}
    	.ty_name {
    		width: 13rem;
    		overflow: hidden;
			text-overflow:ellipsis;
			white-space: nowrap;
    	}
    	.line_round {
			width: 1.3rem;
			height: 1.3rem;
			border-radius: 1.3rem;
			background-color: #EFEFF4;
		}
		.deshed {
			width: 100%;
			border: 1px dashed #EFEFF4 !important;
			height: 0.16rem;
		}
		.lines {
			display: flex;
			justify-content: space-between;
			align-items: center;
		}
		.lines_l {
			margin-left: -0.6rem;
			margin-right: 2.4rem;
		}
		.lines_r {
			margin-right: -0.6rem;
			margin-left: 2.4rem;
		}
		.yifa {
			width: 33rem;
			/*height: 6.55rem;*/
			background-color: #fafafa;
			border-radius: 0.3rem;
			border: solid 1px #e5e5e5;
			padding-left: 1.25rem;
			padding-right: 1.85rem;
			margin: 0 auto;
			display: flex;
			justify-content: space-between;
			align-items: center;
		}
		.reject {
			width: 4.8rem;
			height: 4.8rem;
		}
		.guoqi {
			opacity: 0.5;
		}
		.border_t_r {
			border-top-left-radius:0.3rem;
			border-top-right-radius: 0.3rem;
			border-bottom-left-radius: 0;
			border-bottom-right-radius: 0;
		}
		.border_b_r {
			border-top-left-radius: 0;
			border-top-right-radius: 0;
			border-bottom-left-radius: 0.3rem;
			border-bottom-right-radius: 0.3rem;
		}
    </style>
@stop

@section('main')
    <section class="containerBox pt2 pl1 pr1 none">
       <div class="tongyong pt3 pb2-5">
       		<div class="ty_detail pt1 pb1">
       			<div class="">
       				<p class="brand_logo mr1"><img src=""/></p>
	       			<div class="brand_logo_p">
	       				<span class="ty_name f14 color333"></span>
	       				<span class="f11 color999 red_support_type"></span>
	       				<p><span class="f11 color999">有效期至 </span><span class="ty_time f11 color999"></span></p>
	       			</div>
       			</div>
       			<p class="">
       				<span class="f11 cf2403c">￥</span><span class="ty_money cf2403c f22">233</span><br /><span class="f11 color999">满<span class="f11 color999 min_consume"></span>减</span><span class="f11 color999 ty_money"></span>
       			</p>
       		</div>
       		<div class="send_yet">
       			<div class="yifa f11 color666 ">
	       			<div class="">
	       				<p class="yifa_customer mt1"><span class="">已发送投资人：</span><span class="user_name"></span>（<span class="user_tel"></span>）</p>
		       			<p class="ticket_use mb05"><span class="ticket_state">奖券状态：已使用-</span><span class="state_time"></span><span class="">；加盟品牌 - </span><span class="state_brand"></span></p>
		       			<p class="ticket_dated mb1-2">奖券状态：<span class=""></span></p>
	       			</div>
	       			<img src="/images/agent/guoqi.png" class="reject none"/>
	       			
	       		</div>
       		</div>
       		
       		<p class="send f15 white mt2">发送给我的投资人</p>
       </div>
       <!--通用红包-->
       <p class="ty_text mt1-5 none"><span class="f12 color999">通用红包在支付加盟费用首付款的时候可以进行现金抵扣</span><br /><span class="f12 color999">通用红包不支持转账、提现，仅用于支付抵扣</span></p>
       <!--品牌红包-->
       <p class="brand_text mt1-5 none"><span class="f12 color999">品牌红包在支付加盟费用首付款的时候可以进行现金抵扣</span><br /><span class="f12 color999">品牌红包不支持转账、提现，仅用于支付抵扣</span></p>
       
       <!--通用红包-->
       <div class="tongyong_hb mt3 none">
       		<!--如何使用-->
	       <div class="use_general pt1-5 pl1 pr1 pb2-5">
	       		<p class="useGeneral_title">
	       			<img src="/images/agent/usehbl.png"/>&nbsp;&nbsp;
	       			<span class="b">如何使用通用红包</span>&nbsp;&nbsp;
	       			<img src="/images/agent/usehbr.png"/>
	       		</p>
	       		<div class="useGeneral_img mt2 mb1-5">
	       			<img src="/images/agent/tongyonghb.png" alt="" class="mr1"/>
	       			<p class="">
	       				<span class="f12 color666">在线上进行考察订金、加盟首付款支付的时候，可以使用通用红包进行抵扣。</span><br />
		       			<span class="f12 color666">通用红包不限品牌。</span><br />
		       			<span class="f12 color666">部分通用红包支持考察订金的抵扣。</span><br />
		       			<span class="f12 color666">仅支持线上抵扣，不支持线下签约付款使用。</span><br />
	       			</p>
	       		</div>
	       		<p class="f12 color666" style="line-height: 2rem;">请在红包有效期内进行使用，超过期限，则无法正常使用。</p>
	       		<p class="f12 color666" style="line-height: 2rem;">请确认红包是否支持叠加使用。</p>
	       		<p class="f12 color666" style="line-height: 2rem;">红包使用如有问题，请联系无界商圈客服人员或您的经纪人。</p>
	       </div>
	       <!--如何获得-->
	       <div class="acquire_hb mt2 pt1-5 pl1 pr1 pb2-5">
	       		<p class="useGeneral_title">
	       			<img src="/images/agent/usehbl.png"/>&nbsp;&nbsp;
	       			<span class="b">如何获得通用红包</span>&nbsp;&nbsp;
	       			<img src="/images/agent/usehbr.png"/>
	       		</p>
	       		<div class="useGeneral_img mt2 mb1 acquire_method">
	       			<img src="/images/agent/fangfa1.png" alt="" class="mr1"/>
	       			<p class="">
	       				<span class="f12 white mb1-5">方法一</span><br />
		       			<span class="f12 color666">关注无界商圈，我们定期会组织线上活动，为你发放通用红包。</span><br />
		       			<span class="f12 color666">数量有限，先到先得。</span><br />
	       			</p>
	       		</div>
	       		<div class="useGeneral_img mt2 mb1 acquire_method">
	       			<img src="/images/agent/fangfa2.png" alt="" class="mr1"/>
	       			<p class="">
	       				<span class="f12 white mb1-5">方法二</span><br />
		       			<span class="f12 color666">我们会定期发放红包，当你打开无界商圈应用，会惊喜的发现，有红包——降临了！</span><br />
		       			<span class="f12 color666">对！赶紧领取红包，加盟品牌优惠更多！</span><br />
	       			</p>
	       		</div>
	       		<div class="useGeneral_img mt2 mb1 acquire_method">
	       			<img src="/images/agent/fangfa3.png" alt="" class="mr1"/>
	       			<p class="">
	       				<span class="f12 white mb1-5">方法三</span><br />
		       			<span class="f12 color666">获得经纪人的邀请码，输入手机号获得相应的通用红包！</span>
	       			</p>
	       		</div>
	       		<div class="useGeneral_img mt2 mb1 acquire_method">
	       			<img src="/images/agent/fangfa4.png" alt="" class="mr1"/>
	       			<p class="">
	       				<span class="f12 white mb1-5">方法四</span><br />
		       			<span class="f12 color666">经纪人获得无界商圈给予的“福袋”，可以将福袋中的红包分享至投资人。</span><br />
		       			<span class="f12 color666">快，赶紧联系你的经纪人，让他看看福袋里是否已经装满了惊喜！</span><br />
		       			<span class="f12 color666">* 如经纪人福袋无红包，属于正常情况。</span><br />
		       			<span class="f12 color666">红包为随机派发，存在暂无红包情况。</span>
	       			</p>
	       		</div>
	       		<p class="f12 color666" style="line-height: 2rem;">赶紧获得无界商圈通用红包！</p>
	       		<p class="f12 color666" style="line-height: 2rem;">让你的创业加盟，更轻松！</p>
	       </div>
       </div>
       
       
       <!--品牌红包-->
       
       <div class="brand_hb mt3 none">
       		<!--如何使用-->
	       <div class="use_general pt1-5 pl1 pr1 pb2-5">
	       		<p class="useGeneral_title">
	       			<img src="/images/agent/pinpaihb.png"/>&nbsp;&nbsp;
	       			<span class="b">如何使用品牌红包</span>&nbsp;&nbsp;
	       			<img src="/images/agent/usehbr.png"/>
	       		</p>
	       		<div class="useGeneral_img mt2 mb1-5">
	       			<img src="/images/act_banner.png" alt="" class="mr1"/>
	       			<p class="">
	       				<span class="f12 color666">接受经纪人发送的”加盟合同“，同意”支付协议“。</span><br />
		       			<span class="f12 color666">在线上进行加盟首付款支付的时候，可以使用品牌红包进行抵扣。</span><br />
		       			<span class="f12 color666">品牌红包仅可以抵扣该品牌的加盟首付费用。其他品牌无法抵扣。</span><br />
		       			<span class="f12 color666">仅支持线上抵扣，不支持线下签约付款使用。</span><br />
	       			</p>
	       		</div>
	       		<p class="f12 color666" style="line-height: 2rem;">请在红包有效期内进行使用，超过期限，则无法正常使用。</p>
	       		<p class="f12 color666" style="line-height: 2rem;">请确认红包是否支持叠加使用。</p>
	       		<p class="f12 color666" style="line-height: 2rem;">红包使用如有问题，请联系无界商圈客服人员或您的经纪人。</p>
	       </div>
	       <!--如何获得-->
	       <div class="acquire_hb mt2 pt1-5 pl1 pr1 pb2-5">
	       		<p class="useGeneral_title">
	       			<img src="/images/agent/fangfa1.png"/>&nbsp;&nbsp;
	       			<span class="b">如何获得品牌红包</span>&nbsp;&nbsp;
	       			<img src="/images/agent/usehbr.png"/>
	       		</p>
	       		<div class="useGeneral_img mt2 mb1 acquire_method">
	       			<img src="/images/act_banner.png" alt="" class="mr1"/>
	       			<p class="">
	       				<span class="f12 white mb1-5">方法一</span><br />
		       			<span class="f12 color666">关注无界商圈，我们定期会组织线上活动，为你发放品牌红包。</span><br />
		       			<span class="f12 color666">数量有限，先到先得。</span><br />
	       			</p>
	       		</div>
	       		<div class="useGeneral_img mt2 mb1 acquire_method">
	       			<img src="/images/agent/fangfa1.png" alt="" class="mr1"/>
	       			<p class="">
	       				<span class="f12 white mb1-5">方法二</span><br />
		       			<span class="f12 color666">我们会定期发放红包，当你打开无界商圈应用，会惊喜的发现，有红包——降临了！</span><br />
		       			<span class="f12 color666">对！赶紧领取红包，加盟品牌优惠更多！</span><br />
	       			</p>
	       		</div>
	       		<div class="useGeneral_img mt2 mb1 acquire_method">
	       			<img src="/images/agent/brandff3.png" alt="" class="mr1"/>
	       			<p class="">
	       				<span class="f12 white mb1-5">方法三</span><br />
		       			<span class="f12 color666">分享红包，让红包滚动起来！</span>
		       			<span class="f12 color666">通过“红包”按钮分享，集齐邀请筹码，获得品牌红包！</span>
		       			<span class="f12 color666">更有机会获得大红包！</span>
	       			</p>
	       		</div>
	       		<div class="useGeneral_img mt2 mb1 acquire_method">
	       			<img src="/images/agent/fangfa4.png" alt="" class="mr1"/>
	       			<p class="">
	       				<span class="f12 white mb1-5">方法四</span><br />
		       			<span class="f12 color666">经纪人获得无界商圈给予的“福袋”，可以将福袋中的红包分享至投资人。</span><br />
		       			<span class="f12 color666">快，赶紧联系你的经纪人，让他看看福袋里是否已经装满了惊喜！</span><br />
		       			<span class="f12 color666">* 如经纪人福袋无红包，属于正常情况。</span><br />
		       			<span class="f12 color666">红包为随机派发，存在暂无红包情况。</span>
	       			</p>
	       		</div>
	       		<p class="f12 color666" style="line-height: 2rem;">赶紧获得无界商圈品牌红包！</p>
	       		<p class="f12 color666" style="line-height: 2rem;">让你的创业加盟，更轻松！</p>
	       </div>
       </div>
       <p class="call_we mt10 mb4"><a href="tel:400-011-0061">联系我们</a></p>
    </section>
    <section >
    	<div class="common_pops none"></div>
    </section>
@stop

@section('endjs')
    <!--<script src="{{URL::asset('/')}}/js/agent/_v010300/createsuccess.js"></script>-->
    <script type="text/javascript">
//  	$(document).ready(function(){$('title').text('无界商圈红包')});
    	new FastClick(document.body);
    	var args=getQueryStringArgs(),
            agent_id = args['agent_id'] || '0',  //经纪人id   
            agent_get_red_id = args['agent_get_red_id'] || 0,  //经纪人对应的红包id
            card_id = args['card_id'],
            urlPath = window.location.href,
		    shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
    	function getDetail(agent_id,agent_get_red_id){
    		var params = {};
	    		params['type'] = 'agent';
	    		params['agent_get_red_id'] = agent_get_red_id;
	    		params['agent_id'] = agent_id;
    		var url = labUser.agent_path + '/lucky-bag/look-red-details/_v010300';                                                                                                                                                                                                                                                                                          
    		ajaxRequest(params,url,function(data){
    			if (data.status) {
    				if(data.message){
    					
    					//红包类型 type为1时  是通用红包   2时为品牌红包
    					if(data.message.type==1){       
    						$('title').text('我的福袋-通用红包');  
    						$('.brand_logo').addClass('none');
    						$('.ty_name').text(data.message.red_name);
    						$('.ty_text').removeClass('none');
    						$('.tongyong_hb').removeClass('none');
    						$('.brand_hb').addClass('none');
    					}else if(data.message.type==2) {
    						$('title').text('我的福袋-品牌红包');
    						$('.brand_logo').removeClass('none');
    						$('.brand_logo img').attr('src',data.message.brand_logo);
    						$('.ty_name').text(data.message.red_name);
    						$('.brand_hb').removeClass('none');
    						$('.tongyong_hb').addClass('none');
    						$('.ty_text').addClass('none');
    						$('.brand_text').removeClass('none');
    					};
    					//红包发送情况
    					if(data.message.send_status==1){
    						$('.send').addClass('none');
    						$('.yifa').removeClass('none');
    						$('.user_name').text(data.message.use_person.user_name);
    						$('.user_tel').text(data.message.use_person.user_tel);
    						$('.state_brand').text(data.message.brand_name);
    						if (data.message.use_person.red_status == '-1') {
    							$('.ticket_use').addClass('none');
    							$('.ticket_dated span').text('已过期');
    							$('.tongyong').addClass('guoqi');
    							$('.reject').removeClass('none');
    							$('.cf2403c').css('color','#999999');
    							$('.ty_detail').addClass('border_t_r');
    							$('.yifa').addClass('border_b_r');
    						}else if(data.message.use_person.red_status == 0){
    							$('.ticket_use').addClass('none');
    							$('.ticket_dated span').text('未使用');
    						}else {
    							$('.ticket_use').removeClass('none');
    							$('.ticket_dated').addClass('none');
	    						$('.state_time').text(data.message.use_person.used_at);
    						}
    					}else {
    						$('.send').removeClass('none');
    						$('.send_yet').addClass('none');
    					};
    					$('.red_support_type').text(data.message.red_support_type);
    					$('.ty_time').text(data.message.red_expire_at);
    					$('.ty_money').text(data.message.red_limit);
    					$('.min_consume').text(data.message.min_consume);
    					$('.containerBox').removeClass('none');
    				}
    			}
    		})
    	}
    	getDetail(agent_id,agent_get_red_id);
//福袋详情选择投资人
function fudaiCustomer(agent_get_red_id,card_id){
	  if (isAndroid) {
          javascript:myObject.fudaiCustomer(agent_get_red_id,card_id);
      } else if (isiOS) {
          var message = {
          method : 'fudaiCustomer',
          params : {
            'agent_get_red_id':agent_get_red_id,
            'card_id':card_id
          }
      }; 
          window.webkit.messageHandlers.TYiOSWebEventHandle.postMessage(message);
      }
}
$(document).on('click','.send',function(){
	fudaiCustomer(agent_get_red_id,card_id);
	console.log(234);
});

   	
    </script>
@stop