@extends('citypartner.layouts.layout')
@section('title')
    <title>账户管理-个人资料</title>
@stop
 @section('styles')
     <link rel="stylesheet" type="text/css" href="/css/citypartner/share.css"/>
     <link rel="stylesheet" type="text/css" href="/css/citypartner/account.css"/>
 @stop
@section('content')
    <div class="container">
        <div class="font">
            <h2>
                账号管理
            </h2>
            <a href="/citypartner/account/password?uid={{ $partner->uid }}" >修改密码</a>
        </div>
        <div class="intro">
            <div class="person" >
                <div class="left">个人资料</div>
                <div class="right">
                    <p><label for="">头像</label>
                        <img src="{{ isset($partner->avatar) ? getImage( $partner->avatar,'avatar','') :'/images/citypartner/m-head.png' }}" alt="头像"/>
                    </p>
                    <p><label for="">姓名</label><span>{{ isset($partner->realname) ? $partner->realname :"" }} </span></p>
                    <p><label for="">地址</label><span>{{isset($partner->zone) ? ($partner->zone->upid == 0 ? $partner->zone->name:$partner->zone->pzone->name):"" }}&nbsp;&nbsp;{{ isset($partner->zone) ? $partner->zone->name :"" }}</span></p>
                    <p><label for="">手机号</label><span>{{ isset($partner->username) ? $partner->username:'' }}</span></p>
                    <p><label for="">我的邀请码</label><span>{{ isset($partner->invite_token) ? $partner->invite_token:'' }}</span></p>
                    <p><label for="">领导人姓名</label><span> {{ isset($partner->pPartner) ? $partner->pPartner->realname:'' }}</span></p>
                    <p><label for="">邮箱</label><span>{{ isset($partner->email) ? $partner->email: "" }}</span></p>
                </div>
            </div>
            <div class="blank">
                <div class="left">银行账户</div>
                <div class="right">
                    <p><label for="">银行卡账户</label><span>{{$partner->bank_account}}</span></p>
                    <p><label for="">银行</label><span>{{$partner->bank}}</span></p>
                    <p><label for="">开户行</label><span>{{$partner->deposit_bank}}</span></p>
                    <p><label for="">持卡人姓名</label><span>{{$partner->cardholder_name}}</span></p>
                    <p><label for="">持卡人身份证</label><span>{{$partner->idcard}}</span></p>
                </div>
            </div>
            <a href="/citypartner/account/edit?uid={{ $partner->uid }}" type="button">编辑</a>
        </div>
    </div>
@stop
