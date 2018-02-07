
@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/dist/swiper.min.css" rel="stylesheet" type="text/css"/>
    <link href="{{URL::asset('/')}}/css/_v020900/brandpro.css" rel="stylesheet" type="text/css"/>
<style>

    #swiper-container1{
        overflow: auto;
    }
    #swiper-container2{
        width:100%;
        overflow: hidden;
        position: relative;
    }
    .swiper-pagination{
        color:#fff;
        text-align: right;
        padding-right:1.33rem;
    }
    .swiper-slide{
        overflow: auto;
        -webkit-overflow-scrolling: touch;
    }
</style>
@stop
@section('beforejs')
   <script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? " https://" : " http://");document.write(unescape("%3Cspan class='hide' id='cnzz_stat_icon_1261401820'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s95.cnzz.com/z_stat.php%3Fid%3D1261401820' type='text/javascript'%3E%3C/script%3E"));
   var args = getQueryStringArgs(),
        uid = args['uid'] || 0,
        id = args['id'],
        urlPath = window.location.href;
    var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
    var shareUrl = urlPath.indexOf('is_share') > 0 ? '&is_share=1' : '';
   if(!(isiOS||isAndroid)){
        window.location.href = labUser.path + 'webapp/brand/pc/_v020700?id='+id+'&uid='+uid+shareUrl;
   }
   </script>
@stop
@section('main')
    <section id="brand_detail" class="bgcolor none">
                    <div class="brand_detail ">
                            <!-- 图文详情 -->
                            <div class=" mb1-33 white-bg pl1-33">
                                <p class="mb1-33  f16 b fline ">图文详情</p>
                                <div class="pic_text pr1-33 color666 pb1-33 f12"></div>
                            </div>
                            <!-- 项目介绍 -->
                            <div class=" mb1 white-bg pl1-33">
                                <p class="mb1-33  f16 b fline">加盟简介</p>
                                <div class="join_intro pr1-33 color666 pb1-33 f12" id='brand_j_1'> </div>
                            </div>
                            <div class=" mb1 white-bg pl1-33">
                                <p class="mb1-33  f16 b fline">加盟优势</p>
                                <div class="join_adv pr1-33 color666 pb1-33 f12" id='brand_j_2'> </div>                    
                                
                            </div>
                            <div class="mb1-33 white-bg pl1-33">
                                <p class="mb1-33  f16 b fline">加盟条件</p>
                                <div class="join_term pr1-33 color666 pb1-33  f12" id='brand_j_3'></div>
                                 
                            </div>
                    </div>
       
    </section>
   
@stop

@section('endjs')
	<script src="{{URL::asset('/')}}/js/_v020900/brandpro.js"></script> 
@stop