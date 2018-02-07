@extends('layouts.default')
@section('css')
 <link href="{{URL::asset('/')}}/css/animate.css" rel="stylesheet" type="text/css"/> 
     <style>
     /*阻止浏览器默认行为css*/
     *{  
        -webkit-touch-callout:none;  
        -webkit-user-select:none;  
        -khtml-user-select:none;  
        -moz-user-select:none;  
        -ms-user-select:none;  
        user-select:none; 
        -webkit-tap-highlight-color: rgba(0,0,0,0) 
      }  
     </style>
@stop
@section('main')
  <section>
        <div style="width:100%;height:10rem"></div>
        <input type="button" value="长按出现或者点击出现" class="delete f16" style="width:100%;height:6rem;background:pink"/>  
        <div class="none alert f16 animated" style="width:100%;height:5rem;background: green;line-height: 5rem"><center>这是长按出现</center></div>
        <div class="none red f16 animated" style="width:100%;height:5rem;background:#999;line-height: 5rem"><center>这是点击出现</center></div>
        <input  autofocus="autofocus" type="number" style="width:100%;height:5rem;border-radius:0.5rem" placeholder="请输入号码"/>
  </section>
@stop
@section('endjs')
<script type="text/javascript">
    $.fn.longPress = function(fn) {  
        var timeout = undefined;  
        var $this = this;  
        for(var i = 0;i<$this.length;i++){  
            $this[i].addEventListener('touchstart', function(event) {  
              timeout = setTimeout(function(e){
                  $('.alert').removeClass('none').addClass('zoomInLeft').removeClass('zoomOutRight'); 
                }, 2000);
                }, false);  
            $this[i].addEventListener('touchend', function(event) { 
                 event.preventDefault(); 
                clearTimeout(timeout);
                }, false);  
        }  
    }       
    $('.delete').longPress(function(){  

     }); 
    // $('.deletefont').on('touchend',function(){ 
    // })  
    $('.delete').on('tap',function(){
        $('.red').removeClass('none').addClass('zoomInLeft').removeClass('zoomOutRight');
        // setTimeout(function(){$('.red').addClass('zoomInLeft')},2000);
    })
    $('.red,.alert').on('tap',function(){
        if($(this).hasClass('zoomInLeft')){
         $(this).addClass('zoomOutRight').removeClass('zoomInLeft');
         setTimeout(function(){$(this).addClass('none')},1000)
        }else{
         $(this).addClass('zoomInLeft').removeClass('zoomOutRight')   
        }
    })
    setTimeout(function(){
      $('input').focus()
    },2000)
</script>
@stop