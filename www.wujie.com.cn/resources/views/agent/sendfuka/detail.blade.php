@extends('layouts.default')
<!--zhangxm-->
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/_v010400/sendfuka.css" rel="stylesheet" type="text/css"/>
    
@stop
<!--zhangxm-->
@section('main')
 	<section >
    <!--安装app-->
        <div class="app_install fixed none" id="installapp">
            <i class="l">打开无界商圈AgentAPP，体验更多精彩内容 >></i>
            <span class="r" id="openapp" style="width:8.66rem"><img class="r" src="{{URL::asset('/')}}/images/opennow.png" alt=""></span>
            <div class="clearfix"></div>
        </div>
    <!--浏览器打开提示-->
        <div class="safari none">
            <img src="{{URL::asset('/')}}/images/safari.png">
        </div>
        <button class="loadapp f16 none" id="loadapp">
            <img src="{{URL::asset('/')}}/images/agent/dock-logo.png" alt="">下载APP
        </button>
    <!-- 蒙层 -->
        <div class="fixed-bg none"></div>
        <div class="alert none">
            <p></p>
        </div>
    </section>
	<section id='container' class="pt4-5 pb10-5 pl3-5 pr3-5 none">
		<!--赠送-->
		<div class="send none">
			<div class="acquirefu ">
				
			 	<p class="">
					<img src="/images/agent/roundwuliang.png" class="fuzi_img wu "/>
					<img src="/images/agent/roundjieliang.png" class="fuzi_img jie none"/>
					<img src="/images/agent/roundshangliang.pngg" class="fuzi_img shang none"/>
					<img src="/images/agent/roundquanliang.png" class="fuzi_img quan none"/>
					<img src="/images/agent/roundfuliang.png" class="fuzi_img fu none"/>
				</p>
				<div class="fu_wenzi">
					<img src="/images/agent/lingxing.png" class="lingxing"/>
					<p class="">
						<span class="wu fuzi_text  ml05 mr05">迎春接福，喜气洋洋</span>
						<span class="jie fuzi_text  ml05 mr05">财源滚滚，阖家幸福</span>
						<span class="shang fuzi_text  ml05 mr05">意气风发，好事连连</span>
						<span class="quan fuzi_text  ml05 mr05">万事如意，恭喜发财</span>
						<span class="fu fuzi_text  ml05 mr05">吉祥富贵，连年有余</span>
					</p>
					<img src="/images/agent/lingxing.png" class="lingxing"/>
				</div>
			 	<p class="fuka_num f14 white"><span class="">您有</span><span class="card_name"></span><span class="">福卡<span class="can_use_num"></span>张，确定赐予好友吗？</span></p>
			 	<p class="acquirefu_btn">
			 		<button class="shareFuka f15 c4f3b0b b">再想想</button>
			 		<button class="sendFriend f15 cff422f b">赠送给他</button>
			 	</p>
			</div>
		</div>
		<!--没有福卡-->
		 <div class="no_fuka none">
		 	<div class="noacquirefu">
		 		<p class="">
					<img src="/images/agent/roundwuhui.png" class="fuzi_img wu "/>
					<img src="/images/agent/roundjiehui.png" class="fuzi_img jie none"/>
					<img src="/images/agent/roundshanghui.png" class="fuzi_img shang none"/>
					<img src="/images/agent/roundquanhui.png" class="fuzi_img quan none"/>
					<img src="/images/agent/roundfuhui.png" class="fuzi_img fu none"/>
				</p>
		 		<p class="noacquirefu_text mt1 mb6"><span class=" f14 color666">您福气值不够，赶快去获取福卡吧</span></p>
			 	<p class="noacquirefu_btn">
			 		<button class="look_act f15 b">查看五福临门活动</button>
			 	</p>
			</div>
		 </div>
		 <!--已赠-->
		 <!--<div class="sendYet ">
			<div class="acquirefu ">
			 	<div class="send_yes">
			 		<p class="fuka_num f14 white"><span class="">您已赠送</span><span class="">1</span><span class="">张福卡给他</span></p>
			 		<button class="sendFuka f15  b">已赠送福卡</button>
			 	</div>
			</div>
		</div>-->
	</section>
    <section class="enjoy" style="background-color: #f2f2f2;">
    	<div class="common_pops none"></div>
    </section>
@stop
@section('endjs')
	<script type="text/javascript" src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
	<script>
		new FastClick(document.body);
		var args = getQueryStringArgs(),
            card_id = args['card_id'] || '0',   //福卡id
            agent_id = args['agent_id'] || '0',    //登录的经纪人id 
            get_agent_id = args['get_agent_id'] || '0',   //接受福卡经纪人的id        
			urlPath = window.location.href;
		var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
		$(document).ready(function(){
			$('title').text('五福临门');
		});
		function getDetail(agent_id,card_id){
			var params={};
				params['agent_id']=agent_id;
				params['card_id']=card_id;
			var url = labUser.agent_path+'/agent-redpacket/f-card-log/_v010400';
			ajaxRequest(params,url,function(data){
				if(data.status){
					$('#container').removeClass('none');
					if(data.message.can_use_num==0){
						$('.send').addClass('none');
						$('.no_fuka').removeClass('none');
						//对应福卡展示
						if(data.message.card_name == '无'){
							$('.wu').removeClass('none').siblings().addClass('none');
						}else if (data.message.card_name == '界') {
							$('.jie').removeClass('none').siblings().addClass('none');
						}else if (data.message.card_name == '商') {
							$('.shang').removeClass('none').siblings().addClass('none');
						}else if (data.message.card_name == '圈') {
							$('.quan').removeClass('none').siblings().addClass('none');
						}else if (data.message.card_name == '福') {
							$('.fu').removeClass('none').siblings().addClass('none');
						}
					}else {
						$('.no_fuka').addClass('none');
						$('.send').removeClass('none');
						$('.card_name').text(data.message.card_name);
						$('.can_use_num').text(data.message.can_use_num);
						//对应福卡展示
						if(data.message.card_name == '无'){
							$('.wu').removeClass('none').siblings().addClass('none');
						}else if (data.message.card_name == '界') {
							$('.jie').removeClass('none').siblings().addClass('none');
						}else if (data.message.card_name == '商') {
							$('.shang').removeClass('none').siblings().addClass('none');
						}else if (data.message.card_name == '圈') {
							$('.quan').removeClass('none').siblings().addClass('none');
						}else if (data.message.card_name == '福') {
							$('.fu').removeClass('none').siblings().addClass('none');
						}
					};
				}else {
					tips(data.message);
				}
				
			});
		};
		getDetail(agent_id,card_id);
		
		
		
		
		//赠送福卡	
		function sendFuka(agent_id,get_agent_id,card_id){
			var params = {};
				params['give_agent_id'] = agent_id;
				params['get_agent_id'] = get_agent_id;
				params['card_id'] = card_id;
			var url = labUser.agent_path + '/agent-redpacket/give-f-redpacket/_v010400';
			ajaxRequest(params,url,function(data){
				
			})	
		};
		//点击赠送福卡
		$(document).on('click','.sendFriend',function(){
			sendFuka(agent_id,get_agent_id,card_id);
		});
		//再想想
		
		
		
		//查看五福临门活动
		$(document).on('click','.look_act',function(){
			window.location.href = labUser.path + '/webapp/agent/activity/newyear/_v010400?agent_id='+agent_id;
		});
        //分享
        function showShare() {
        	var args=getQueryStringArgs(),
        		agent_id = args['agent_id']; 
            var type='news';
            var title = $('title').text();
            var img =  $('#container').attr('logo');
            if(img==''){
            	img=labUser.path+'images/agent/dock-logo.png';
            }
            var header = '五福临门';
            var summary = cutString($('#content').attr('summary'), 18);
            var content = '在一起，过福年，好福气，要分享~';
            if(content==''){
            	content = summary;
            }
            var id = agent_id;
            var url = window.location.href;
            if(summary==''){
            	shareOut(title, url, img, header, content,'','',id,type,'','','','','');
            }else {
            	shareOut(title, url, img, header, summary,'','',id,type,'','','','','');
            };
        };
        function reload(){
            location.reload();
        }
        function Refresh(){
            reload();
            $('body').scrollTop($('body')[0].scrollHeight);
       }
//二次分享
		function weixinShare(obj,is_share){
			if(is_share&&is_weixin()){
                /**微信内置浏览器**/
                $(document).on('tap', '#loadapp,#openapp', function () {
                    var _height = $(document).height();
                    $('.safari').css('height', _height);
                    $('.safari').removeClass('none');
                });
                //点击隐藏蒙层
                $(document).on('tap', '.safari', function () {
                    $(this).addClass('none');
                });
				var wxurl = labUser.api_path + '/weixin/js-config';
                        ajaxRequest({url: location.href}, wxurl, function (data) {
                            if (data.status) {
                                wx.config({
                                    debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
                                    appId: data.message.appId, // 必填，公众号的唯一标识
                                    timestamp: data.message.timestamp, // 必填，生成签名的时间戳
                                    nonceStr: data.message.nonceStr, // 必填，生成签名的随机串
                                    signature: data.message.signature, // 必填，签名，见附录1
                                    jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
                                });
                                wx.ready(function () {
                                    wx.onMenuShareTimeline({    //分享到朋友圈
                                        title: '我在【无界商圈Agent】发现好多不错的项目，快来看看吧！', // 分享标题
                                        link:location.href, // 分享链接
                                        imgUrl: obj.logo, // 分享图标
                                        success: function () {
                                            // 用户确认分享后执行的回调函数
                                            
                                        },
                                        cancel: function () {
                                            // 用户取消分享后执行的回调函数
                                        }
                                    });
                                    wx.onMenuShareAppMessage({  //分享给朋友
                                        title:'我在【无界商圈Agent】发现好多不错的项目，快来看看吧！',
                                        desc: '[ 注册无界商圈Agent，好礼享不停 ]',
                                        link: location.href,
                                        imgUrl: obj.logo, // 分享图标
                                        trigger: function (res) {
                                            // 不要尝试在trigger中使用ajax异步请求修改本次分享的内容，因为客户端分享操作是一个同步操作，这时候使用ajax的回包会还没有返回
//                                          console.log('用户点击发送给朋友');
                                        },
                                        success: function (res) {
                                            console.log('已分享');
                                            
                                        },
                                        cancel: function (res) {
//                                          console.log('已取消');
                                        },
                                        fail: function (res) {
//                                          console.log(JSON.stringify(res));
                                        }
                                    });
                                });
                            }
                        });
			}
		};
//打开本地--Android
function openAndroid(){
    var strPath = window.location.pathname;
    var strParam = window.location.search.replace(/is_out=1/g, '');
    var appurl = strPath + strParam;
    window.location.href = 'openwjsq://welcome' + appurl;
}
function oppenIos(){
    var strPath = window.location.pathname.substring(1);
    var strParam = window.location.search;
    var appurl = strPath + strParam;
    var share = '&is_out';
    var appurl2 = appurl.substring(0, appurl.indexOf(share));
    window.location.href = 'openwjsq://' + appurl2;
};
    </script>
@stop