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
        <p>无界商圈经纪人等级目前设置：铜牌经纪人、银牌经纪人、金牌经纪人。</p>
        <div style="width:100%;height:2rem"></div>
        <p>我们通过用户的成单量设置经纪人等级，随着等级的提高，所获得的经纪人权益也将不断丰富。</p> 
        <div style="width:100%;height:2rem"></div>   
        <h3 class="f16">铜牌经纪人</h3>
        <div style="width:100%;height:2rem"></div>
        <p>- 享受无界商圈经纪人佣金分成规则；</p>
        <div style="width:100%;height:2rem"></div>
        <p>- 自由发展下线，获取下线佣金分成；</p>
        <div style="width:100%;height:2rem"></div>
        <p>- 享受邀请分成，邀请的投资人在无界商圈加盟成功获得邀请分成。</p>
        <div style="width:100%;height:2rem"></div>
        <h3 class="f16">银牌经纪人</h3>
        <div style="width:100%;height:2rem"></div>
        <p>- 派单优先级优于普通经纪人；</p>
        <div style="width:100%;height:2rem"></div>
        <p>- 享受无界商圈优质品牌独家推送，优先享受代理权；</p>
        <div style="width:100%;height:2rem"></div>
        <p>- 免费经纪人业务培训，线下OVO运营中心免票进入。</p>
        <div style="width:100%;height:2rem"></div>   
        <h3 class="f16">金牌经纪人</h3>
        <div style="width:100%;height:2rem"></div> 
        <p>- 享受最优投资人派单规则；</p>
        <div style="width:100%;height:2rem"></div> 
        <p>- 享受无界商圈金牌经纪人佣金分成，额外加成享不停；</p>
        <div style="width:100%;height:2rem"></div> 
        <p>- 免费经纪人业务培训，优质导师助你更进一步；</p>
        <div style="width:100%;height:2rem"></div> 
        <p>- 无界商圈经纪人联盟活动vip专享服务。</p>
        <div style="width:100%;height:2rem"></div> 
    </section>
@stop

@section('endjs')
    <script>
        $(document).ready(function(){
          $('title').text('经纪人等级权益')  
        })  
    </script>
@stop