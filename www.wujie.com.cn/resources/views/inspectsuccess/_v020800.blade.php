@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/_v020800/inspect_suss.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <!-- 打开APP -->
    <div class="app_install none" id="installapp" style="position: absolute;z-index: 99">
        <i class="l">打开无界商圈APP，体验更多精彩内容 >></i>
        <span class="r" id="openapp"><img src="{{URL::asset('/')}}/images/install_btn.png" alt=""></span>
        <div class="clearfix"></div>
    </div>
    <section class="succ-sec">
        <div class="head">
        	<span class="banner"></span>
        </div>
        <div class="cont  ml3 mr3 pt4">
        		<div class=" mb3 dis_bet pl3 pr3"><span class="f14 color333 medium ">考察品牌</span><span class="f14 c8a869e medium brand_title "></span></div>
        		<div class="mt3 mb3 dis_bet pl3 pr3"><span class="f14 color333 medium ">邀请人</span><span class="f14 c8a869e medium realname "></span></div>
        		<div class="lines mb4">
	        		<span class="line_round lines_l"></span>
	        		<span class="deshed"></span>
	        		<span class="line_round lines_r"></span>
        		</div>
        	<ul class="pl3 pr3">
        		<li class="mt3 mb3 dis_bet"><span class="f14 color333 medium ">考察门店</span><span class="f14 c8a869e medium store_name "></span></li>
        		<li class="dis_bet mb3"><span class="f14 color333 medium ">所在地区</span><span class="f14 c8a869e medium zone "></span></li>
        		<li class="dis_bet mb3"><span class="f14 color333 medium ">详细地址</span><span class=" f14 c8a869e medium type address w_72"></span></li>
        		<li class="mt3 mb1-5 dis_bet"><span class="f14 color333 medium ">考察时间</span><span class="f14 c8a869e medium inspect_time "></span></li>
        		<li class="dis_bet"><span class="deshed"></span></li>
        		<li class="dis_bet mt1-5 mb3"><span class="f14 color333 medium ">支付定金</span><span class="cff4d64 f14 medium amount "></span></li>
        		<li class="dis_bet mt1-5 mb3"><span class="f14 color333 medium ">支付方式</span><span class="f14 c8a869e medium pay_way "></span></li>
        		<li class="dis_bet mt1-5 mb3"><span class="f14 color333 medium ">支付时间</span><span class="f14 c8a869e medium pay_at "></span></li>
        		<li class="dis_bet mt1-5 mb3 pb4"><span class="f14 color333 medium ">订单号</span><span class="f14 c8a869e medium order_no "></span></li>
        	</ul>
        </div>
        <div class="apply_text mr3 ml3 mt1-5 mb2 ">
        	<p class="texta f12 medium color999 pl3 pr3">
        		A.订金支付成功后，可以在签约合同使用订金进行一部分加盟费用抵扣。同时，如果没有后续加盟意向，我们将为你退款订金。
        	</p>
        	<p class="textb f12 medium color999 pl3 pr3">
        		B.考察时间由经纪人与您事先协商而定，如临时有调整，请联系经纪人。需要调整考察时间或门店信息，可以通过经纪人进行协调安排。
        	</p>
        </div>
    </section>
    <!--浏览器打开提示-->
    <div class="safari none">
        <img src="{{URL::asset('/')}}/images/safari.png">
    </div>
    <div class="none" id="video_title_none"></div>
    <div class="none" id="video_descript_none"></div>
    <div class="none" id="endtime_none"></div>
    <div class="isFavorite"></div>
    
@stop
@section('endjs')
    <script>
        Zepto(function () {
        	new FastClick(document.body);
        	$('body').css('background','#f2f2f2');
				var args = getQueryStringArgs(),
				order_no = args['order_no'] || '0';
				
			function getdetail(order_no){
            	var param = {
                        "order_no":order_no,
                    };
                var url = labUser.api_path + '/user/inspect-pay-success/_v020800';
                ajaxRequest(param,url,function(data){
                	if(data.status){
                		if(data.message){
	                		 d = data.message;
	                		 var unix_ymd = unix_YMD(d.inspect_time)
	                		$('.brand_title').html(d.brand_title);
	                		$('.realname').html('经纪人：'+d.realname);
	                		$('.store_name').html(d.store_name);
	                		$('.zone').html(d.zone);
	                		$('.address').html(d.address);
	                		$('.inspect_time').html(unix_ymd);
	                		$('.amount').html('¥ '+d.amount);
	                		$('.pay_way').html(d.pay_way);
	                		$('.pay_at').html(d.pay_at);
	                		$('.order_no').html(d.order_no);
	                	}
                	}else {
                		$('.brand_title').html('/');
                		$('.realname').html('/');
                		$('.store_name').html('/');
                		$('.zone').html('/');
                		$('.address').html('/');
                		$('.inspect_time').html('/');
                		$('.amount').html('/');
                		$('.pay_way').html('/');
                		$('.pay_at').html('/');
                		$('.order_no').html('/');
                	}
                	
                });
            }
            getdetail(order_no); 
            function unix_YMD(unix) {
			    var newDate = new Date();
			    newDate.setTime(unix * 1000);
			    var Y = newDate.getFullYear();
			    var M = newDate.getMonth() + 1 < 10 ? '0' + (newDate.getMonth() + 1) : newDate.getMonth() + 1;
			    var D = newDate.getDate() < 10 ? ('0' + newDate.getDate() ) : newDate.getDate(); 
			    return Y + '年' + M + '月' + D + '日' ;
			};
        });

    </script>
    
@stop