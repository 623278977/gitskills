@extends('layouts.default')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/iscroll.css"/>
    <link href="{{URL::asset('/')}}/css/act_detail.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/brand.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/v010000/vod.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
     <div class="brand-message brand-message2 fixed bgcolor none" id="brand-mes" style="top:0">
            <form action="">
                <p class="fline f14  name ">
                    <label for=""> 姓名：</label>
                    <input type="text" placeholder="请输入您的姓名" name="realname">
                </p>
                <p class="f14 name">
                    <label for=""> 手机号：</label>
                    <input type="text" placeholder="请输入您的手机号" name="phone">
                </p>
                <p class="name textarea">
                    <label for="" class="f14 color333">咨询：</label>
                    <input  class="f14 width80" placeholder="请输入您要咨询的事项" name="consult">
                </p>
                <a href="javascript:;" class="btn f14 send-mes" >提交</a>  
                <input type="reset" class="none b-reset" >   
            </form>
    </div>
    <section class="containerBox none" >
        <!-- 加盟意向弹框 -->
   
        <!--打开app-->
        <div class="install none" id="installapp">
            <p class="l">打开无界商圈APP，观看完整高清视频 >> </p>
            <!--蓝色图标-->
            <span class="r" id="openapp"><img src="{{URL::asset('/')}}/images/yaoqing.png" alt="">
                <!-- <img src="{{URL::asset('/')}}/images/020502/install_btn.png" alt="">橙色 -->
            </span>
            <div class="clearfix"></div>
        </div>
       
        <!--视频分享-->
        <div class="share_video">
            <img src="{{URL::asset('/')}}/images/live.png" alt="">
        </div>
        <div id="video_box"></div>
        <section class="videodetail_box" style="padding-bottom:4.5rem;">
            <div class="ui_basicmes ">基本信息</div>
            <ul class="ui_have_brand none">
                <li class="ui-border-r clickbg">基本信息</li>
                <li>已打卡用户</li>
            </ul>
            <div class="rem"></div>
          <!-- 基本信息 -->
            <div class="mb10 basic_info  style "> 
                <div>
                <!-- 品牌信息 -->
                    <div class=" pl1-33  pb05 mb1-5 brand">
                        <p class=" f16 color333 fline b mb0">品牌信息</p>
                         <div class="brand_info " id='brand_info'>
                
                        </div>
                    </div>
                <!-- 视频信息 -->
                    <div class="intro pl1-33  pb05 " id="basicvideo_info">
                        <p class=" f16 color333 fline b">视频信息</p>
                        <div class=" f12 color666 pr1-33">
                            <div class="l video_img "><img src="" alt="" id="basic_videoimg"></div>
                            <div class="video_intro" id="basic_videoinfo">
                                <!-- <p class="f16 mb02">标题</p>
                                <p class="f12 color999">录制时间：<span>2016-11-12</span></p> -->
                            </div>
                            <div class="clearfix"></div>
                            <p style="margin-top: 1rem; height:1px;background: #f3f4f8;transform: scale(1,0.5);"></p>
                            <div class="f12 color999">
                                <p class="mt2 mb05  color333 f14">详情</p>
                                <div class="disVideo pb3 ">
                                <div class="tc none nocomment" id="nocommenttip">
                                <img src="{{URL::asset('/')}}/images/nomessage_icon1.png" style="height: 16rem;width: 16rem;margin:0 auto;display: inline-block;">
                                </div>      
                                </div>
                            </div>
                        </div>
                    </div>
                </div>            
            </div>
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
                <div class="getmore">加载更多数据</div>
                <div class="tc none nocomment" id="nocommenttip2">
                    <img src="{{URL::asset('/')}}/images/nomessage_icon1.png" style="height: 16rem;width: 16rem;margin:0 auto;display: inline-block;">
               </div>     
            </div>      
         </section>
        <!--  <button class="fixedbottom">邀请客户报名</button> -->
         <!-- 视频相关信息用于分享出去 -->
         <input class="none"  id="video_detail" data_img="">
         <input type="hidden" id="share_img">
         <input type="hidden" id="share">
        <!-- 蒙层 -->
        <div class="fixed-bg none"></div>
        <div class="alert none">
            <p></p>
        </div>
       <!--  分享页打开 -->
        <ul class="ui_share none" >
            <li><img src="/images/downapp.png"></li>
            <li>发送加盟意向</li>
        </ul>
         <!--浏览器打开提示-->
        <div class="safari none">
            <img src="{{URL::asset('/')}}/images/safari.png">
        </div>
        <div class="isFavorite"></div>
    </section>
@stop
@section('endjs')
    <script src="https://qzonestyle.gtimg.cn/open/qcloud/video/h5/h5connect.js" charset="utf-8"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/dist/fontsize.min.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/agent/vod.js"></script>
    <script type='text/javascript'>
        function showShare() {
            var title = $('#video_detail').attr('title');//点播的标题
            var url = window.location.href;
            var img = $('#video_detail').attr('data_img');
            var header ='视频';
            var des=removeHTMLTag($('#video_detail').attr('data_des').replace(/&nbsp;/g,''));
            var content = delHtmlTag(cutString(des, 18));//点播的描述
            var type='video';
            shareOut(title, url, img, header, content,'','','',type)
        }   
        function delHtmlTag(str){
            return str.replace(/<{FNXX=]+>/g,"");//去掉所有的html标记
        }
    </script>

@stop