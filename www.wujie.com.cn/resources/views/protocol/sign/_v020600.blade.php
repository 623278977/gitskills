@extends('layouts.default')
@section('css')
@stop
@section('main')
    <section class="pl1-33 pr1-33">
        <div class="f16 pt2 pb2 tc b">签到奖励体系（首次，单次，连续X日签到、）</div>
        
        <div class="f14 mt1">
            
            <p>在“我的”呈现： </p>
            <p> 常规签到：</p>
            <p>功能：登录点击签到按钮 </p>
            <p>展现形式：已登录点击签到，奖励 +5 积分 </p>
            <p>签到规则： </p>
            <p>
                首次签到+10 积分 仅每个账号一次
            </p>
            <p>以后每增一天+5 积分 </p>
            <p>连续签到 7 天，额外奖励+20 积分 </p>
            <p>连续签到 15 天，额外奖励+50 积分 </p>
            <p>连续签到 30 天，额外奖励+100 积分 </p>
            <p>签到中断后，需重新签到</p>
            <p>连续签到31天及以后天数获得的积分都是“100积分”，上限是“100”</p>
        </div>
       
    </section>
@stop
@section('endjs')
@stop