@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/_v010100/particulars.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section id="act_container" class=""> 
                  <div class="install none" id="installapp">
                      <p class="l">打开无界商圈AgentAPP，体验更多精彩内容 >> </p>
                      <!--蓝色图标-->
                      <span class="r" id="openapp" style="width:8.66rem"><img class="r" src="{{URL::asset('/')}}/images/opennow.png" alt="">
                          <!-- <img src="{{URL::asset('/')}}/images/020502/install_btn.png" alt="">橙色 -->
                      </span>
                      <div class="clearfix"></div>
                 </div>
                 <div class="tips none"></div>
              <header class="ui-container" id="content">
               <!--  标题 -->
                    <p class="ui-titletext ui-nowrap-multi b">
                    	
                    </p>
                    <p class="ui-public-time color f11 color999 ui-margin"></p>
                    <article class="ui-text-detail color666  f15">
                    	 
                    </article>
              </header>
             <!--  分享用 -->
              <input type="hidden" id="share">
                 <!--浏览器打开提示-->
              <div class="safari none">
                  <img src="{{URL::asset('/')}}/images/safari.png">
              </div>
              <div class="isFavorite"></div>
              <button class="loadapp f16 none" id="loadapp">
		            <img src="{{URL::asset('/')}}/images/agent/dock-logo.png" alt="">下载APP
		      </button>
    </section>
@stop
@section('endjs')
<script type="text/javascript" src="/js/agent/_v010100/particulars.js"></script>
<script>
  $(document).ready(function(){$('title').text('文章详情')});
        function showShare() {
            var type='talkeverydayDetail',
                title = $('#share').data('title'),
                img =  $('#share').data('img'),
                header = '一分钟话术',
                summary = cutString($('#content').attr('summary'), 18),
                content = cutString(removeHTMLTag($('.ui-text-detail').text()), 18),
                id=id,
                url = window.location.href;
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

</script>
@stop













