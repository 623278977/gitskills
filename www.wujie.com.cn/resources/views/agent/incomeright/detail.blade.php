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
        table{
            width:100%;
            text-align:center;
        }
        table tr{
            width:25%;
            height:2rem;
            line-height: 2rem;
        }
        table td{
             border:1px solid #666;
        }
   </style>
@stop
@section('main')
    <section class='sec'>
        <h3 class="f16">了解邀请收益</h3>
        <div style="width:100%;height:2rem"></div>
        <p>固定金额，1000元/位。每位邀请投资人首次成单后获得1000元现金奖励,上不封顶。</p>
        <div style="width:100%;height:2rem"></div>
        <h3 class="f16">了解经纪人下线收益</h3>
        <div style="width:100%;height:2rem"></div>
        <p>结算的时间维度为季度，每一个标准季度为一个结算周期。</p>
        <div style="width:100%;height:2rem"></div>
        <p>结算方式为分档累进，及团队提成。</p>
        <div style="width:100%;height:2rem"></div>
        <p>举例</p>
        <div style="width:100%;height:2rem"></div>
        <p>分档累进的档位 - </p>
        <div style="width:100%;height:2rem"></div>
        <table>
            <tr><td>档位</td><td>完成单数</td><td>提成单</td><td>说明</td></tr>
            <tr><td>1</td><td>3</td><td>1000</td><td>X≤3</td></tr>
            <tr><td>2</td><td>6</td><td>2000</td><td>3＜X ≤6</td></tr>
            <tr><td>3</td><td>9</td><td>3000</td><td>6＜X ≤9</td></tr>
        </table>
        <div style="width:100%;height:2rem"></div>
        <p>团队说明 - </p>
        <div style="width:100%;height:2rem"></div>
        <table>
            <tr><td>成员</td><td>完成单数</td><td>关系说明</td><td>说明</td></tr>
            <tr><td>我</td><td>1</td><td></td><td>我本人完成单数1</td></tr>
            <tr><td>A</td><td>5</td><td>下线经纪人</td><td>A及A下线经纪人总共完成单数为5</td></tr>
            <tr><td>B</td><td>3</td><td>下线经纪人</td><td>B及B下线经纪人总共完成单数为2</td></tr>
        </table>
        <div style="width:100%;height:2rem"></div>   
        <p>由此可以看出，我的团队整体完成单数为9（1+5+3）单，达到3档，</p>
        <div style="width:100%;height:2rem"></div> 
        <p>实际总共提成达到27000元（9*3000）。这27000是提供我，及A和B</p>
        <div style="width:100%;height:2rem"></div> 
        <p>及他们下线进行分成的。</p>
        <div style="width:100%;height:2rem"></div> 
        <p>由表中得知，A完成了5单，达到了2档，实际获得的提成达到10000元。</p>
        <div style="width:100%;height:2rem"></div> 
        <p>由表中得知，B完成了3单，达到了1档，实际获得的提成达到3000元。</p>
        <div style="width:100%;height:2rem"></div> 
        <p>如果按照档位，我完成了1单，应该只获得1000元，但由于团队的壮大</p>
        <div style="width:100%;height:2rem"></div> 
        <p>及分档累进的计算，我实际获得的金额是14000元，远远高于1000元。</p>
        <div style="width:100%;height:2rem"></div> 
        <p>实际我得到团队佣金13000元，成单佣金1000元。</p>
        <div style="width:100%;height:2rem"></div> 
        <p>所以通过举例说明，得出以下两点：</p>
        <div style="width:100%;height:2rem"></div> 
        <p>1、分佣按照季度结算，并且有分档累进的表格可以查询，得出当前的佣金系数及佣金金额。</p>
        <div style="width:100%;height:2rem"></div> 
        <p>2、您的团队越大，所完成的单数越多，并达到新的档位，能为你带来更多意想不到的收益。</p>
        <div style="width:100%;height:2rem"></div> 
    </section>
@stop

@section('endjs')
    
@stop