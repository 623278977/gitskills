@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/_v010005/vipzone.css" rel="stylesheet" type="text/css"/>
    <style>
           .ui-text-detail img{
               width:100%;
           }
    </style>
@stop
@section('main')
    <section id="act_container" class="none">
              <div class="install none" id="installapp">
                      <p class="l">打开无界商圈AgentAPP，体验更多精彩内容 >> </p>
                      <!--蓝色图标-->
                      <span class="r" id="openapp" style="width:8.66rem"><img class="r" src="{{URL::asset('/')}}/images/opennow.png" alt="">
                          <!-- <img src="{{URL::asset('/')}}/images/020502/install_btn.png" alt="">橙色 -->
                      </span>
                      <div class="clearfix"></div>
              </div>
              <div class="tips none"></div>
              <!-- 顶部导师照片 -->
              <div class="ui_professor_pict">
                     <div class="ui_professor_text white">
                           <p class="f15 b ">+ 这是女神迪丽热巴</p>
                           <p class="f12"><span>小虾</span><span class="fr">284学习</span></p>
                     </div>
              </div>
              <article class="ui-text-detail color666  f15">
                      迪丽热巴（Dilraba），1992年6月3日出生于新疆乌鲁木齐，中国内地女演员，毕业于上海戏剧学院。[1] 
                      2013年，主演个人首部电视剧《阿娜尔罕》。2014年参演古装玄幻剧《古剑奇谭》，2015年主演校园魔幻网络剧《逆光之恋》[2]  ，同年凭借都市爱情剧《克拉恋人》获得2015年国剧盛典最受欢迎新人女演员[3-4]  。2016年8月，主演都市爱情剧《麻辣变形计》[5]  。
                      2017年1月，主演的都市爱情喜剧《漂亮的李慧珍》；同月，主演古装仙侠剧《三生三世十里桃花》，并凭借该剧提名上海电视节“白玉兰奖”最佳女配角；2月，参加浙江卫视综艺节目《奔跑吧》担任常驻主持[6-7]  ；4月，主演喜剧电影《傲娇与偏见》，并凭借该电影获得2016中英电影节最佳新人奖[8]  ；8月14日，主演的古装剧《秦时丽人明月心》在浙江卫视周播剧场播出。
              </article>
              <section id="brandcon">
                    <!-- <ul class="ui-brand-zone">
                           <li><img class="ui-brand-pict" src="/images/nomessage_icon1.png"/></li>
                           <li>
                               <p class="b f14 color333 ui-nowrap-multi1 margin-ui">哈哈哈哈哈啊哈</p>
                               <p class="margin-ui f11 color999 none">哈哈哈哈</p>
                               <p class="margin-ui f12 color666">行业分类<span class="color333" style="padding-left: 0.5rem">哈哈哈哈</span></p>
                               <p class="margin-ui f12 color666">启动资金<span  style="padding-left: 0.5rem;color:#ff4d64">5~10(万)</span></p>
                               <p><span class="border-8a-radius ui-border-radius-8a circle">hahahha</span></p>
                           </li>
                           <li>
                               <p style="margin: 3rem 0 0"></p>
                               <p class="f18" style="margin:0 0 0"><span  style="color:#ff4d64">￥5~10(万)</span></p>
                               <p class="f10 color999">成单提成最高金额</p>
                           </li>
                    </ul>
                    <div class="clear fline" style="width:100%;height:0.0001rem"></div> -->
              </section>
              <section id="videocon">
                      <!-- <ul class="ui-video-zone">
                             <li><img class="ui-video-pict" src="/images/nomessage_icon1.png"/></li>
                             <li>
                                 <p class="b f14 color333 ui-nowrap-multi1">哈哈哈哈哈啊哈</p>
                                 <p></p>
                                 <p class="margin-ui2 f11 color999 ">录制时间：<span>5月11号 15:00</span></p>
                             </li>
                      </ul>
                      <div class="fline" style="background: #fff"></div> -->
              </section>
              
               <!-- 点赞和转发 -->
              <ul class="ui-zan-zhaun">
                      <li>
                          <center>
                                  <button class="ui-forzan">
                                    <p></p>
                                    <img class="ui-pict6 dian-zan" src="/images/agent/ui_pict.png"/>
                                    <p class="color2873ff f13 ui-margin5">1900</p>
                                  </button>
                          </center>
                        <p class="f13 color999 ui-margain2">点赞鼓励</p>
                      </li>
                      <li>
                        <center><button><center><img class="ui-pict7" src="/images/agent/zhuan.png"/></center></button></center>
                        <p class="f13 color999 ui-margain2">分享好友</p>
                      </li>
              </ul>
              <footer class="footer">
                      <div class="ui-commnet-title f13 color333 b">热门评论</div>
                      <div class="ui_list">
                               <!--  <ul class="ui-commnet-list clear">
                                      <li><img class="ui-pict10" src="/images/default/avator-m.png"/></li>
                                      <li class="fline">
                                        <p></p>
                                        <p class="color666 f13">迪丽热巴</p>
                                        <p class="color333 f15">哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈</p>
                                        <p class="color999 f11">10月12日<span class="fr"><img class="ui-pict20" src="/images/agent/weizan.png"/>
                                          <span style="padding: 0.5rem">111</span></span>
                                        </p>
                                      </li>
                                </ul>
                                <ul class="ui-commnet-list clear">
                                      <li><img class="ui-pict10" src="/images/default/avator-m.png"/></li>
                                      <li class="fline">
                                        <p></p>
                                        <p class="color666 f13">迪丽热巴</p>
                                        <p class="color333 f15">哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈</p>
                                        <p class="color999 f11">10月12日<span class="fr"><img class="ui-pict20" src="/images/agent/weizan.png"/>
                                          <span style="padding: 0.5rem">111</span></span>
                                        </p>
                                      </li>
                                </ul>
                                <ul class="ui-commnet-list clear">
                                      <li><img class="ui-pict10" src="/images/default/avator-m.png"/></li>
                                      <li class="fline">
                                        <p></p>
                                        <p class="color666 f13">迪丽热巴</p>
                                        <p class="color333 f15">哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈哈</p>
                                        <p class="color999 f11">10月12日<span class="fr"><img class="ui-pict20" src="/images/agent/weizan.png"/>
                                          <span style="padding: 0.5rem">111</span></span>
                                        </p>
                                      </li>
                                </ul>  -->
                      </div> 
                      <div class="tc none  nocomment" id="nocommenttip2">\
                          <img src="{{URL::asset('/')}}/images/nomessage_icon1.png" style="height: 16rem;width: 16rem;margin:0 auto;display: inline-block;margin-bottom: 4rem">\
                      </div>  
                      <button class="ui-get-more f12 color999 ">加载更多</button> 
                      <button class="formore f12 color999 none">已经加载全部</button> 
              </footer>
              <div style="width:100%;height:10rem"></div> 
              <div class="ui-fixed-botton">
                <input placeholder="写评论…" readonly="readonly"><button class="fr">评论</button>
              </div>
              <div class="copy_suc tc none">
                <img src="/images/agent/success.png" style="">
                <p class="white f12 ">已复制</p>
              </div>
             <!--  分享用 -->
              <input type="hidden" id="share">
                 <!--浏览器打开提示-->
              <div class="safari none">
                  <img src="{{URL::asset('/')}}/images/safari.png">
              </div>
              <div class="isFavorite"></div> 
    </section>
@stop

@section('endjs')
<script type="text/javascript" src="{{URL::asset('/')}}/js/agent/_v010005/reply.js"></script>
<script type="text/javascript" src="/js/agent/_v010005/vipzone.js"></script>
<script>
        $(document).ready(function(){
          $('title').text('专区详情');
        })
          function showShare() {
            var type='messageDetail',
                title ='无界商圈经纪人端，代理品牌，赚取分佣，好礼赢不停!',
                img = labUser.path+'images/agent-share-logo.png',
                name=$('#share').data('name'),
                header = '专区详情',
                content = name+'分享了一篇好文，赶紧点击查看详情吧！',
                id=id,
                url = window.location.href;
          shareOut(title, url, img, header, content,'','',id,type,'','','','','');

        };
        function reload(){
              location.reload();
        }
       function Refresh(){
                         reload()
       }  
      
</script>
@stop