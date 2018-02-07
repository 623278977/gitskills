@extends('layouts.default')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/iscroll.css"/>
    <link href="{{URL::asset('/')}}/css/act_detail.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/brand.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/agent/_v010004/brandvod.css" rel="stylesheet" type="text/css"/>
    <!-- <link href="{{URL::asset('/')}}/css/agent/_v010004/section.css" rel="stylesheet" type="text/css"/> -->
@stop
@section('main')
    <section class="containerBox none">
        <!--视频分享-->
        <div class="share_video">
            <img src="{{URL::asset('/')}}/images/live.png" alt="">
        </div>
        <div id="video_box"></div>
        <article>
                <ul class="ui_have_brand">
                    <li class="clickbg">详情</li>
                    <li>章节</li>
                    <li class="ui-user">已打卡用户</li>
                </ul>
                <ul class="ui-progress-bar">
                    <li class="ui-padding1"><span class="ui-blue"></span></li>
                    <li class="ui-padding2"><span></span></li>
                    <li class="ui-padding3"><span></span></li>
                </ul>
        </article>    
        <section class="videodetail_box">
            <!-- 基本信息模块 -->
            <div class="mb10 basic_info  style"> 
                  <!-- 讲师介绍 -->
                     <div class="ui-unkown videotitle f15 color333">
                     哈哈哈哈哈哈哈哈哈啊哈哈
                    </div>
                    <div class=" pl1-33  pb05 mb1-5 brand ui-list-forprofessor ">
                        <p class="f15 color333  b mb0">讲师介绍</p>
                        <ul class="ui-professor-introduce">
                             <li><img class="ui-pict1" src="/images/default/avator-m.png"/></li>
                             <li>
                                <p class="f15 color333">迪丽热巴</p>
                                <p class="f13 color999">
                                 迪丽热巴（Dilraba），1992年6月3日出生于新疆乌鲁木齐，中国内地女演员，毕业于上海戏剧学院。2013年，主演个人首部电视剧《阿娜尔罕》。2014年参演古装玄幻剧《古剑奇谭》，2015年主演校园魔幻网络剧《逆光之恋》 ，同年凭借都市爱情剧《克拉恋人》获得2015年国剧盛典最受欢迎新人女演员。
                                </p>
                             </li>
                        </ul>
                    </div>
                   <!-- 课程介绍 -->
                    <div class=" pl1-33  pb05 clear" id="basicvideo_info">
                        <p class=" f15 color333  b">课程介绍</p>
                        <p class="f13 color999 ui-lesson-introduce">
                                 迪丽热巴（Dilraba），1992年6月3日出生于新疆乌鲁木齐，中国内地女演员，毕业于上海戏剧学院。2013年，主演个人首部电视剧《阿娜尔罕》。2014年参演古装玄幻剧《古剑奇谭》，2015年主演校园魔幻网络剧《逆光之恋》 ，同年凭借都市爱情剧《克拉恋人》获得2015年国剧盛典最受欢迎新人女演员。
                        </p>
                    </div>        
            </div>
            <!-- 章节部分 -->
            <div class="style none ui-lesson">
                      <div class="ui-lesson-list"></div>
                   <!--  <div class="ui-father-detail">
                       <div class="fline ui-div f15 color333 b">第一张的什么学习哈哈哈<img class="fr ui-pict30 rotate180" src="/images/up.png"/></div>
                    </div>
                    <ul class="ui-son-detail">
                      <li>
                        <img class="ui-pict31 fl" src="/images/agent/sectiontext.png"/>
                            <span class="f13 color333 ui-padding30">这位是我的的嘎嘎嘎嘎哈哈哈</span>
                        <button class="ui-blue">开始学习</button>
                      </li>
                      <li>
                        <img class="ui-pict31 fl" src="/images/agent/sectiontext.png"/>
                            <span class="f13 color333 ui-padding30">这位是我的的嘎嘎嘎嘎哈哈哈</span>
                        <button class="ui-grey" disabled="disabled">已学习</button>
                      </li>
                    </ul>
                    <div style="width:100%;height:1rem"></div>
                    <div class="ui-father-detail">
                       <div class="fline ui-div f15 color333 b">第一张的什么学习哈哈哈<img class="fr ui-pict30 rotate180" src="/images/up.png"/></div>
                    </div>
                    <ul class="ui-son-detail">
                      <li>
                        <img class="ui-pict31 fl" src="/images/agent/sectiontext.png"/>
                            <span class="f13 color333 ui-padding30">这位是我的的嘎嘎嘎嘎哈哈哈</span>
                        <button class="ui-blue">开始学习</button>
                      </li>
                      <li>
                        <img class="ui-pict31 fl" src="/images/agent/sectiontext.png"/>
                            <span class="f13 color333 ui-padding30">这位是我的的嘎嘎嘎嘎哈哈哈</span>
                        <button class="ui-grey" disabled="disabled">已学习</button>
                      </li>
                    </ul>
                    <div style="width:100%;height:1rem"></div> -->
                  <div class="tc none nocomment" id="nocommenttip3">
                    <img src="{{URL::asset('/')}}/images/nomessage_icon1.png" style="height: 16rem;width: 16rem;margin:0 auto;display: inline-block;">
                  </div>   
            </div>
            <!-- 打卡记录 -->
            <div class="listsforchart  style none">
                <div class="ui_lists">
                   <div class="ui_agentname ui-border-b">
                        <div class=""> 经纪人名单</div>
                   </div> 
                   <div class="list_mumber">
                       <ul  class="  ui_listdetail ui-border-b">
                           <li><img  class="nick_pict"  src="{{URL::asset('/')}}/images/default/avator-m.png"></li>
                           <li>
                               <p class="b f16 color333 margin7">哈哈哈哈</p>
                               <p class="color999 f12 margin7">上海徐家汇 <span class="fr">2017/18/20  18:00:00</span></p>
                           </li>
                       </ul>
                       <ul  class="  ui_listdetail ui-border-b">
                           <li><img  class="nick_pict"  src="{{URL::asset('/')}}/images/default/avator-m.png"></li>
                           <li>
                               <p class="b f16 color333 margin7">哈哈哈哈</p>
                               <p class="color999 f12 margin7">上海徐家汇 <span class="fr">2017/18/20  18:00:00</span></p>
                           </li>
                       </ul>
                   </div>
                </div>
                <button class="getmore">加载更多数据</button>

                <div class="tc none nocomment" id="nocommenttip2">
                    <img src="{{URL::asset('/')}}/images/nomessage_icon1.png" style="height: 16rem;width: 16rem;margin:0 auto;display: inline-block;">
               </div>     
            </div>      
         </section>
         <div class="tipsfor none">完成阅读学习，点击“完成学习”参与小测试</div>
         <div class="triangle none"></div>
         <div style="width:100%;height:7rem"></div>
         <button class="ui-fixed-button f15 b">完成学习，参与测试</button>
    </section>
    <section style="position: fixed;bottom: 0;background: #FFFFFF;height:17px" class="iphone_btn none"></section>
@stop
@section('endjs')
    <script>
       $(document).ready(function(){$('title').text('视频学习详情');})
       iphonexBotton('.ui-fixed-button')
    </script>
    <script src="https://qzonestyle.gtimg.cn/open/qcloud/video/h5/h5connect.js" charset="utf-8"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/dist/fontsize.min.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/agent/_v010004/brandvod.js"></script>
@stop