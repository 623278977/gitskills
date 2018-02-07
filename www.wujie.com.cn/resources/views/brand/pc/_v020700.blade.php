@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/_v020700/brandpc.css" rel="stylesheet" type="text/css"/>
@stop
@section('beforejs')
  <script>
     var args = getQueryStringArgs(),
        uid = args['uid'] || 0,
        id = args['id'],
        urlPath = window.location.href;
    var origin_mark = args['share_mark'] ;//分销参数，分享页用
    var origin_code = args['code'] || 0;
    var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
    var shareUrl = urlPath.indexOf('is_share') > 0 ? '&is_share=1' : '';
   if(isiOS||isAndroid){
        window.location.href = labUser.path + 'webapp/brand/detail/_v020700?id='+id+'&uid='+uid+'&share_mark='+origin_mark+'&code='+origin_code+shareUrl;
   }
  </script>
@stop
@section('main')
    <section class="bgcolor container">
      <!-- 页头 -->
       <nav class="brandnav bgwhite">
            <div class="w1172">
                <div class="l nav-left">
                    <img src="/images/dock-logo2.png"  class="l dock">
                   <div class="l solgan">
                       <img src="/images/wujietext.png" alt="" class="shangquan_logo">
                       <p class="fs14 color666">百万创业者首选的招商加盟服务平台</p>
                   </div>
               </div>
               <div class="r nav-right">
                   <!-- <span class="tc">更多活动</span> -->
                   <span >下载APP</span>
                   <em><img src="/images/020700pc/p12.png" alt=""></em>
               </div>
               <div class="clearfix"></div>
            </div>
           
       </nav>
       <div class="main">
       <!-- 品牌信息 -->
           <div class="header w1172">
               <div class=" l">
                   <img src="/images/share_img.png" alt="" class="l brand-img" width=152 height=152>
                   <div class="brand-info l">
                       <p class="b brand-title">品牌的名称</p>
                       <p><label for="">标语</label><span id="slogan">为你创造生活之美</span></p>
                       <p><label for="">分类</label><span id="cates">生活服务</span></p>
                       <p><label for="">启动资金</label><span id="invest">20~50万元</span></p>
                       <p><label for="">主营产品</label><span id="products">阿萨德发的</span></p>
                       <p><label for="">店铺数量</label><span >中国大陆地区 <em class="cea5200" id="storenum">2317</em> 家门店</span></p>
                   </div>
               </div>
               <div class="r tr">
                   <div class="brand-erwei">
                       <div style="display: inline-block;">
                           <p class="fs24">品牌二维码</p>
                           <p class="fs14">通过无界商圈移动端扫描</p>
                           <p class="fs14">获取更多品牌加盟优惠和资讯</p>
                       </div>
                        <span class="small-erwei branderwei" ></span>
                   </div>
                   <div class="datas">
                       <dl>
                           <dt><img src="/images/020700pc/shares.png" alt=""></dt>
                           <dd id='sharenum'></dd>
                       </dl>
                       <dl style="border-left:1px solid #999;border-right: 1px solid #999;">
                           <dt><img src="/images/020700pc/favs.png" alt=""></dt>
                           <dd id="favnum"></dd>
                       </dl>
                       <dl>
                           <dt><img src="/images/020700pc/views.png" alt=""></dt>
                           <dd id="viewnum"></dd>
                       </dl>
                   </div>
               </div>
           </div>
        <!-- 项目介绍等 -->
            <div class="bgwhite" >
              <div class="tab">
                   <span class="pruintro active">项目介绍</span>
                   <span class="comintro">公司介绍</span>
                   <span class="location">现场实景</span>
                   <span class="policy">加盟政策</span>
                   <span class="question">项目问答</span>
                   <!-- <span class="words">项目留言</span> -->
               </div>
            </div>
      <!-- 在线问答与电话资讯 -->
            <div class="w1172 getmes"> 
              <div class="online l">
                <p class="que_title" >在线问答</p>
                <form action="" class="online_form" >
                  <div  class="w50 l ">
                    <p class="w88 bgwhite lh45 br6  pl25">
                      <label for="">姓名：</label>
                      <input type="text" id="realname">
                    </p>
                  </div>     
                  <div class="w50 l ">
                   <p class="w88 bgwhite lh45 br6 pl25">
                     <label for="">手机号：</label>
                     <input type="text" name='tel' id="telnum">
                  </p>  
                  </div>
                  <div class="w50 l ">
                    <p class="w88 bgwhite message pl25 pt10">
                      <label for="">留言：</label>
                      <textarea name="" id="consult" cols="30"  placeholder="请填写留言或选择快捷留言" style='height:100px;width:80%;'></textarea>
                    </p> 
                    <p class="re_visit w88 pl25" type="all">
                      <label for="">回访：</label>
                      <span data-type='all'><img src="/images/020700pc/checked.png" alt="">随时</span>
                      <span data-type='working'><img src="/images/020700pc/un_check.png" alt="">上班时间</span>
                      <span data-type='off_working'><img src="/images/020700pc/un_check.png" alt="">下班时间</span>
                    </p> 
                  </div>
                  <div class="w50 l ">
                    <p class="bgwhite br6 pl25 lh45 w50 checkcode">
                      <label for="">校验码：</label><input type="text" name='captcha'> 
                    </p>
                    <img src="/identify/piccaptcha" alt="" class="yanzhengma" id="yanzhengma" onclick="this.src='/identify/piccaptcha/'+Math.random()">
                    <div class="w88 mt30">
                      <p class="color999 fs14 tc mb5">最近已有<span id="visiters">20</span>人提交了项目问答</p>
                      <button class="submit" type='button'>提&nbsp;&nbsp;问</button>
                    </div>
                  </div>
                  <input type="reset" class="none reset">
                </form>
              </div>
              <div class="mobile l">
                <p class="que_title">电话咨询</p>
                <p class="lh45"><span class="fs24 cea5200 b pl25">400 011 0061</span><span class="fs14">  (总部)</span></p>
                <p class="bgwhite lh45 free_call br6">
                  <input type="text" placeholder="请输入您的手机号码" id="phonenum">
                  <button type="button" id="join">
                      <p class="fs18">点击免费通话</p>
                      <p class="fs10">已有<span id="joiners"></span>人申请加入</p>
                  </button>
                </p>
                <div class="cue pl25 color999 fs14">
                  <p>温馨提示：</p>
                  <p>请确保您的手机接听免费。</p>
                  <p>项目方会自动回复您输入的电话，省时省力掌握商机！</p>
                </div>
              </div>
               <div class="tanchuang none">
                  发表成功
                </div>
            </div>
      <!-- 活动模块 -->
           <div class="bgwhite" style="padding-top:36px;padding-bottom: 36px;" id="rel_act">
               <!-- <div class="w1172 activity">
                  <div class="horn">
                    活动
                  </div>
                  <div class="act_img l">
                      <img src="" alt="">
                  </div>
                  <div class="act_intro l">
                    <p class="fs24 b mb30">科技驱动新零售商业变革 - 2017年阿里巴巴智慧供应链开放日</p>
                    <p class="mb15"><em><img src="/images/020700pc/clock.png" alt=""></em><span class="time">2017年5月26日 9:00 ~ 2017年5月26日 16:00</span></p>
                    <p class="mb15"><em><img src="/images/020700pc/local.png" alt=""></em><span class="local">杭州、深圳、北京、广州、浦江</span></p>
                    <p class="mb15"><em><img src="/images/020700pc/persons.png" alt=""></em><span class="persons">限额 500 人</span></p>
                  </div>
                  <div class="sign_num">
                      <span class="fs14">已有多少xx人报名</span>
                      <button class="sign">我要报名</button>
                  </div>
                    <img src="" alt="" class="act_erwei">
               </div> -->
           </div>
        <!-- 各项目展示 -->
          <div class="items bgwhite none" id="items">
             <div class="w1172 ">
                <div class="l logo_title">
                  <img src="" id="brand_logo">
                  <span class="fs14 brand-title" ></span>
                </div>
                <div class="r brand_item">
                    <span class="brand-title"></span><span class="brand_pro cea5200">/项目介绍</span>
                    <button class="toask none" >我要提问</button>
                </div>
                <div class="clearfix"></div>
             </div>
          </div>
          <div class="">
              <div class="w1172 items_intro">
                 <div class="pruintro_detail ">
                    
                 </div>
                 <div class="comintro_detail none">
                   
                 </div>
                 <div class="location_detail  none">

                 </div>
                 <div class="policy_detail none">
                   
                 </div>
                 <div class="question_detail none" style="background: #f2f2f2;">
                    <ul id="questions">
                      <li class="que_ans">
                        <img src="" alt="" class="l">
                        <div class="r que_ans_detail">
                          <p class="fs18 mt30 mb30"><span class="ques">请问开一家奶茶店需要多少钱？总部会吃问开一家奶茶店需要多少钱？总部会吃问开一家奶茶店需要多少钱？总部会吃问开一家奶茶店需要多少钱？总部会吃吗</span> <span class='r fs14'> 2017-02-01 15：00 ：00</span></p>
                          <div class="ans_pro">
                            <span class="cea5200 l bName">细查:</span>
                            <span class="l ans_detail">难舍难分，为何。痛彻心底的来不及，之分看盒子你 和星难舍难分，为何。痛彻心底的来不及，之分看盒子你 和星星难舍难分，为何。痛彻心底的来不及，之分看盒子你 和星星难舍难分，为何。痛彻心底的来不及，之分看盒子你 和星星难舍难分，为何。痛彻心底的来不及，之分看盒子你 和星星星</span>
                          </div>
                        </div>
                      </li>
                     
                    </ul>
                 </div>
                <!--  <div class="words_detail none">
                   <ul>
                     <li class="messages">
                       <img src="" alt="" class="l">
                        <div class="r que_ans_detail">
                          <p class="fs18 mt30 mb30"><span class="username">用户名称</span> <span class='r fs14'> 2017-02-01 15：00 ：00</span></p>
                          <div class="fs14 user_mes">
                           难舍难分，为何。痛彻心底的来不及，之分看盒子你 和星难舍难分，为何。痛彻心底的来不及，之分看盒子你 和星星难舍难分，为何。痛彻心底的来不及，之分看盒子你 和星星难舍难分，为何。痛彻心底的来不及，之分看盒子你 和星星难舍难分，为何。痛彻心底的来不及，之分看盒子你 和星星星
                          </div>
                        </div>
                     </li>
                     <li></li>
                   </ul>
                 </div> -->
              </div>
          </div>
  
       </div>
       <div class="footer ">
          <div class="w1172">
            <div class="w50 l">
              <p class="cea5200 fs24">无界商圈为你提供沉浸式招商新体验！</p>
              <p class="wujie"><img src="/images/020700pc/wujie.png" alt=""></p>
            </div>
            <div class="w50 l">
              <div class="l">
                <img src="/images/020700pc/p12.png" alt="" class="loadapp">
              </div>
              <div class="l app_intro">
                <p>扫一扫，下载无界商圈</p>
                <p>优活动，更精彩</p>
                <p class="fs18 mt30">同时，你也可以前往各大应用商场搜索 “<span class="cea5200">无界商圈</span>”</p>
              </div>
              <div class="clearfix"></div>
              <img src="/images/020700pc/close.png" alt="" class="close">
            </div>
          </div>
       </div>
  
       <div class="tishikuang br6 none">
          请完善相关信息
       </div>
       <div class="tips" id="tip" style="right: 285.5px;">
          <div class="sharebrand">
              <i style=""></i>
              <p>微信分享</p>
              <div class="tipload">
                <img src="" alt="" id='branderwei' class="branderwei">
                <p>分享此品牌到</p>
                <p>微信朋友圈</p>
              </div>
          </div>
          <div class="cuservice">
          </div>
          <div class="backtop" id="backtop" style="visibility: hidden;">
              <i></i>
              <p>回顶部</p>
          </div>
      </div>
      <div class="fixed none">
        <div class="tc erwei-kuang">
          <img src="" alt="" class="branderwei" id="act-erwei">
          <p class="fs14 cea5200">手机查看了解更多详情</p>
          <span class="erwei-close">×</span>
        </div>
      </div>
      <div class="">
        
      </div>
    </section>
@stop

@section('endjs')
	<script src="{{URL::asset('/')}}/js/_v020700pc/brandpc.js"></script>
@stop