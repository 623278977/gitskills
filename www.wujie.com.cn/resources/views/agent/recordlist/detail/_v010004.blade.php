@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/agent/_v010004/brandvod.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section class="containerBox none">
            <div class="listsforchart  style">
                <div class="ui_lists">
                   <div class="ui_agentname ui-border-b">
                        <div class=""> 经纪人名单</div>
                   </div> 
                   <div class="list_mumber">
                      <!--  <ul  class="  ui_listdetail ui-border-b">
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
                       </ul> -->
                   </div>
                </div>
                <button class="getmore">加载更多数据</button>

                <div class="tc none nocomment" id="nocommenttip2">
                    <img src="{{URL::asset('/')}}/images/nomessage_icon1.png" style="height: 16rem;width: 16rem;margin:0 auto;display: inline-block;">
               </div>     
          </div>      
    </section>
@stop
@section('endjs')
    <script>
       $(document).ready(function(){$('title').text('打卡列表');})
    </script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/dist/fontsize.min.js"></script>
    <script type="text/javascript" src="{{URL::asset('/')}}/js/agent/_v010004/recordlist.js"></script>
@stop