<!--zhangxm-->
@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/mer-remind.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section class="containerBox pl1-5 mt1" id="containerBox" >
    	<!--OVO活动邀请函（接受、拒绝）-->
      <!--<div class="act ">
      	<p class="top mt2-25">
      		<img src="/images/agent/time_03.png" class="mr1 "/><span class="time c2873ff b bold">今天</span>
      	</p>
      	<div class="bord-l ml08">
      		<div class="act-cont bgwhite mb1-2">
      			<div class="act-1 fline f13">
      				<img src="/images/awardpic.png" class="avatar mr1"/><span class="b bold">周杰伦</span><span class="cfd4d4d ">接受了</span><span class="color666">你的</span><span class="c2873ff">lol活动邀请</span>
      			</div>
      			<div class="act-2 fline">
      				<div class="act-2l">
      					<img src="/images/default.jpg" class="act-2limg mr1"/>
      					<p class="act-2lp over-text">
      						<span class="mb1 over-text f13 b bold act-2lspan color333">新零售时代下的营销秘诀</span><br />
      						<span class="over-text f11 act-2lspan color999">开始时间：3月3日 23:23</span><br />
      						<span class="over-text f11 color999">活动地点：地球、月球、皮球</span>
      					</p>
      				</div>
      				<img src="/images/jump.png" class="act-jump"/> 
      			</div>
      			<span class="cfd4d4d f12 pt1">拒绝理由：你太坑了, 带不动</span>
      		</div>
      	</div>
      </div>-->
      <!--考察邀请函（接受、拒绝）-->
      <!--<div class="inst">
      	<p class="top">
      		<img src="/images/agent/time_03.png" class="mr1 "/><span class="time c2873ff b bold">12/24</span>
      	</p>
      	<div class="bord-l ml08 pl2 pr1-5 pt05">
      		<div class="act-cont bgwhite mb1-2">
      			<div class="act-1 fline f13">
      				<img src="/images/share_image.png" class="avatar mr1"/>
      				<span class="b bold">张学友</span>&nbsp;<span class="cfd4d4d">拒绝了</span><span class="">你的</span><span class="c2873ff">排位考察邀请</span>
      			</div>
      			<div class="act-2 fline">
      				<div class="inst-2l pr1">
      					<p class="inst-2lp text-end mb05"><span class="f12 color333">考察场地</span><span class="f12 color666">总部：杭州市北部<br />地址：杭州市西部大沙漠</span>
      					<p class="inst-2lp mb05"><span class="f12 color333">考察时间</span><span class="f12 color666">2222/22/22</span></p>
      					<p class="inst-2lp mb05"><span class="f12 color333">订金金额</span><span class="f12 color666">¥3,333</span></p>
      					<p class="inst-2lp mb05"><span class="f12 color333">支付方式</span><span class="f12 color666">支付宝(大象花呗)</span></p>
      					<p class="inst-2lp mb05"><span class="f12 color333">支付时间</span><span class="f12 color666">2222/22/33 12:12:12</span></p>
      				</div>
      				<img src="/images/jump.png" class="inst-jump"/>
      			</div>
      			<span class="cfd4d4d f12 pt1">拒绝理由：你太坑了, 带不动</span>
      		</div>
      	</div>
      </div>-->
      <!--电子合同（签订、拒绝）-->
      <!--<div class="pact">
      	<p class="top">
      		<img src="/images/agent/time_03.png" class="mr1 "/><span class="time c2873ff b bold">33/332</span>
      	</p>
      	<div class="bord-l ml08 pl2 pr1-5 pt05">
      		<div class="act-cont bgwhite mb1-2">
      			<div class="act-1 fline f13">
      				<img src="/images/flowerpic.png" class="avatar mr1"/>
      				<span class="b bold">张学友</span>&nbsp;<span class="cfd4d4d">签订</span>&nbsp;<span class="c2873ff">3亿合同</span>
      			</div>
      			<div class="act-2 ">
      				<div class="inst-2l ">
      					<p class="inst-2lp mb05"><span class="f12 color333">加盟合同</span><span class="f12 color666">合同名称</span>
      					<p class="inst-2lp mb05"><span class="f12 color333">合同号</span><span class="f12 color666">12343241</span></p>
      					<p class="inst-2lp mb05"><span class="f12 color333">加盟品牌</span><span class="f12 color666">喜茶</span></p>
      					<p class="inst-2lp mb05 text-end"><span class="f12 color333">合同撰写</span><span class="f12 color666">无界商圈法务代表<br />喜茶法务代表</span></p>
      				</div>
      				
      			</div>-->
      			<!--首付-->
      			<!--<div class="">
      				<div class="inst-2l ">
      					<p class="down-pay mb05"><span class="fline wid"></span><span class="f12 color666">首付情况</span><span class="fline wid"></span></p>
      					<p class="inst-2lp mb05"><span class="f12 color333">首次支付</span><span class="f12 color666">¥ 23,333</span>
      					<p class="inst-2lp mb05"><span class="f12 color333">定金抵扣</span><span class="f12 color666">-¥ 23,333</span></p>
      					<p class="inst-2lp mb05"><span class="f12 color333">创业基金抵扣</span><span class="f12 color666">-¥ 0</span></p>
      					<p class="inst-2lp mb05"><span class="f12 color333">实际支付</span><span class="f12 color666">¥ 14,222</span></p>
      					<p class="inst-2lp mb05"><span class="f12 color333">支付状态</span><span class="f12 color666">已支付</span>
      					<div class="inst-2lp mb05 text-end">
      						<span class="f12 color333">支付方式</span>
      						<p class=" mb05 text-end">
      							<span class="f12 color666">支付宝</span><br />
      							<span class="f12 color333">251175150@qq.com</span>
      						</p>
      					</div>
      					<p class="inst-2lp mb05"><span class="f12 color333">支付时间</span><span class="f12 color666">1111/22/22 22:22:22</span>
      				</div>
      			</div>-->
      			<!--尾款-->
      			<!--<div class="act-2">
      				<div class="inst-2l ">
      					<p class="down-pay mb05"><span class="fline wid"></span><span class="f12 color666">尾款情况</span><span class="fline wid"></span></p>
      					<p class="inst-2lp mb05"><span class="f12 color333">尾款补齐</span><span class="f12 color666">¥ 23,333</span></p>
      					<div class="inst-2lp mb05 text-end">
      						<span class="f12 color333">支付状态</span>
      						<p class="f12 medium">
      							<span class="cfd4d4d mb05">未支付</span><br />
      							<span class=" mb05 color666">* 请提醒投资人尽快支付尾款费用</span><br />
      							<span class=" mb05 color666">支付方式为线下对公账号转账</span><br />
      							<span class="c2873ff mb05">了解尾款补齐操作办法</span><br />
      						</p>
      					</div>-->
      					<!--已结清-->
      					<!--<div class=" mb05 text-end">
      						<p class="inst-2lp mb05"><span class="f12 color333">支付状态</span><span class="f12 c59c78a">已结清</span></p> 
      						<div class="inst-2lp mb05 text-end">
      							<span class="f12 color333">支付方式</span>
      							<p class=" mb05 text-end">
	      							<span class="f12 color666">支付宝</span><br />
	      							<span class="f12 color666">251175150@qq.com</span>
      							</p>
      						</div>
      						<p class="inst-2lp mb05"><span class="f12 color333">到账时间</span><span class="f12 color666">3222/22/22 22:22:22</span></p>
      						<p class="inst-2lp mb05"><span class="f12 color333">财务确认人</span><span class="f12 color666">皮皮凯</span></p>
      					</div>
      					
      					<p class="inst-2lp mb05"><span class="f12 color333">合同文本</span></p>
      					<div class="pct-2 mb1">
      						<div class="act-2l pact-text">
      							<img src="/images/agent/my_contract.png" class="pact-img mr1"/>
      							<p class="pact-2lp over-text">
      								<span class="over-text f14 b bold act-2lspan color333">喜茶加盟合同</span><br />	
      								<span class="over-text f11 act-2lspan color333">合同编号：3月3日 23:23</span><br />
      							</p>
      						</div>
      						<img src="/images/jump.png" class="pct-jump"/>
      					</div>
      					<div class="fline"></div>
      					<p class="inst-2lp pt1 pb1"><span class="f12 color333">确定时间</span><span class="f12 color666">1111/22/22 22:22:22</span></p>
      				</div>
      			</div>
      			
      		</div>
      	</div>
      </div>-->
      <!--电子合同（拒绝）-->
      <!--<div class="pact">
      	<p class="top">
      		<img src="/images/agent/time_03.png" class="mr1 "/><span class="time c2873ff b bold">今天</span>
      	</p>
      	<div class=" bord-l ml08 pl2 pr1-5 pt05">
      		<div class="act-cont bgwhite mb1-2">
      			<div class="act-1 fline f13">
      				<img src="/images/zb-logo.png" class="avatar mr1"/>
      				<span class="b bold">张学友</span>&nbsp;<span class="cfd4d4d">拒绝了</span>&nbsp;<span class="c2873ff">3亿合同</span>
      			</div>
      			<div class="act-2 fline medium">
      				<div class="inst-2l ">
      					<p class="inst-2lp mb05"><span class="f12 color333">加盟合同</span><span class="f12 color666">合同名称</span>
      					<p class="inst-2lp mb05"><span class="f12 color333">合同号</span><span class="f12 color666">12343241</span></p>
      					<p class="inst-2lp mb05"><span class="f12 color333">加盟品牌</span><span class="f12 color666">喜茶</span></p>
      					<p class="inst-2lp mb05 text-end"><span class="f12 color333">合同撰写</span><span class="f12 color666">无界商圈法务代表<br />喜茶法 务代表</span></p>
      				</div>
      			</div>
      			<p class="inst-2lp mb05 mt1"><span class="f12 color333">加盟总费用</span><span class="f12 color666">¥ 23,333</span></p>
      			<p class="inst-2lp mb05"><span class="f12 color333">合同文本</span></p>
      			<div class="pct-2 mb1">
      				<div class="act-2l pact-text">
      					<img src="/images/agent/my_contract.png" class="pact-img mr1"/>
      					<p class="pact-2lp over-text">
      						<span class="over-text f14 b bold act-2lspan color333">喜茶加盟合同</span><br />	
      						<span class="over-text f11 act-2lspan color333">合同编号：3月3日 23:23</span><br />
      					</p>
      				</div>
      				<img src="/images/jump.png" class="pct-jump" pactId=""/>
      			</div>
      			<div class="fline"></div>
      			<p class="inst-2lp pt1 pb1"><span class="f12 color333">拒绝理由</span><span class="f12 color666">想不出理由了</span></p>
      		</div>
      	</div>
      </div>-->
      
    </section>
    <section>
    	<div class="default none">
      		<img src="/images/agent/new_mer_remind.png"/>
        </div>
    </section>
@stop
@section('endjs')
    <script src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
    <script src="{{URL::asset('/')}}/js/agent/mer-remind.js" ></script>	
@stop