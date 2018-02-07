@extends('layouts.default')
@section('css')
   <style>
        p,h2,h3,h4,h5,h6{
            margin:0;
            padding:0;
        }
        h1,h2,h3,h4,h5{
            font-weight: bold;
             font-family:"Hiragino Sans GB","Microsoft Yahei UI","Microsoft Yahei","微软雅黑","Segoe UI",Tahoma,"宋体b8b体",SimSun,sans-serif;
        }
        .sec{
            font-family:"Hiragino Sans GB","Microsoft Yahei UI","Microsoft Yahei","微软雅黑","Segoe UI",Tahoma,"宋体b8b体",SimSun,sans-serif;
            color:#333;
            padding:1rem ;
            font-size: 1.2rem;
            background-color: #fff;
        }
        .pl{
            padding-left:2rem;
        }
        body{
            background: #fff;
        }
        img{
            width:100%;
            height:100%;
        }
   </style>
@stop
@section('main')
    <section class='sec'>
       <img src="/images/020700/page.png">
    </section>
@stop
@section('endjs')
<script type="text/javascript">
  $(document).ready(function(){
    $('title').text('项目投资意向书')  
        })  
</script>    
@stop