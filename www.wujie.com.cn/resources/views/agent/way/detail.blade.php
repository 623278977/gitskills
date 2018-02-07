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
   </style>
@stop
@section('main')
     <section class='sec'>
        <div style="width:100%;height:2rem"></div>
        <p>-请通过银行转达的方式，将钱款打至三方托管账户。打完账户后，请及时联系您的经纪人，</p>
        <div style="width:100%;height:2rem"></div>
        <p>-告知钱款已交付，并等待财务进行审核并反馈信息。</p> 
        <div style="width:100%;height:2rem"></div>
        <p>-全款到账后，合同自动生效，余下的需要补钱的合同会有经纪人、商务代表与您取得联系。</p>
        <div style="width:100%;height:2rem"></div>
        <p>-银行：临安市农村信用合作联合社衣锦信用社</p>
        <div style="width:100%;height:2rem"></div>
        <p>-账户：201000093534729</p>
        <div style="width:100%;height:2rem"></div>
        <div style="width:100%;height:2rem"></div>
    </section>
@stop

@section('endjs')
    <script>
        $(document).ready(function(){
          $('title').text('了解尾款补齐操作办法')  
        })  
    </script>
@stop