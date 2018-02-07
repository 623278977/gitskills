@extends('layouts.default')
@section('css')
  
@stop

@section('main')
  <section class="containerbox">
         <iframe width="100%" ></iframe>
  </section>
@stop
@section('endjs')
  <script>
     Zepto(function(){
     	 //浏览器视口的高度
     	 var args = getQueryStringArgs();
     	 var file = args['file'] || '' 
         var windowHeight = 0;
            if (document.compatMode == "CSS1Compat") {
                windowHeight = document.documentElement.clientHeight;
            } else {
                windowHeight = document.body.clientHeight;
            }
        $('iframe').css('height',windowHeight).attr('src','/js/agent/generic/web/viewer.html?file='+file);
        console.log($('iframe'));
    })
  </script>
   
@stop


