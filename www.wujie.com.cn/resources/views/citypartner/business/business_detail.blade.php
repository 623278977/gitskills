@extends('citypartner.layouts.layout')
@section('title')
    <title>业务详情</title>
@stop
@section('styles')
    <link rel="stylesheet" href="/css/citypartner/share.css" type="text/css"/>
    <link rel="stylesheet" href="/css/citypartner/business_detail.css" type="text/css"/>
@stop
@section('content')
    <div class="container">
        <div class="font">
            <h2>
                业务详情<span>基本信息</span>
            </h2>
            <a href="/citypartner/business/list?id={{$business->partner_uid}}">返回业务列表</a>
        </div>
        <div class="main">
            <h3><span>业务名称：</span>{{ $business->contract_name }}</h3>
            <ul>
                <li>
                    <p><span>客户名称：</span>{{ $business->customer }}</p>
                    <p><span>客户负责人：</span>{{ $business->contact_person }}</p>
                    <p><span>联系电话：</span>{{ $business->phone }}</p>
                    <p><span>业绩系数：</span>{{ sprintf("%.2f",$business->ratio)}}</p>
                    <p><span>审核流程：</span>
                 <!--   @if($business->status == -1) 已退回 @elseif($business->status == 1) 待审核 @elseif($business->status == 2) 收款中 @elseif($business->status == 3)已完成 @endif -->
                    </p>
                </li>
                <li>
                    <p><span>合同编号：</span><b>{{ $business->contract_id }}</b></p>
                    <p><span>业务类型：</span>@if($business->type == 1) 服务类 @elseif($business->type == 2) 设备类 @elseif($business->type == 3) 资源对接类 @elseif($business->type == 4) 投资类 @endif</p>
                    <p><span>合同金额：</span><s>￥{{ sprintf("%.2f",floor($business->amount/100)/100)}}</s>万元</p>
                </li>
                <li>
                    <p><span>当前状态：</span></p>
                    <img src="@if($business->status == -1) /images/citypartner/img/return.png @elseif($business->status == 1) /images/citypartner/img/review.png @elseif($business->status == 2) /images/citypartner/img/payment.png @elseif($business->status == 3) /images/citypartner/img/finish.png @endif" alt="待审核"/>
                </li>
            </ul>
            <table>
                <thead>
                <tr>
                    <th>动态</th>
                    <th>操作人</th>
                    <th>操作时间</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>业务等待审核</td>
                    <td style="color: #23a4f8; font-size:18px;">{{ isset($business->businessAudit) ? $business->businessAudit->admin->nickname : '' }}</td>
                    <td>{{ date('Y-m-d',$business->created_at->timestamp) }}<p>{{ date('H:i',$business->created_at->timestamp) }}</p></td>
                </tr>
                </tr>
                @foreach($audits as $audit)
                    <tr>
                        <td>@if($audit->status == -1) 财务审核未通过 @elseif($audit->status == 1) 财务审核已通过 @elseif($audit->status == 2) 收款中 @elseif($audit->status == 3)已完成 @endif</td>
                        <td style="color: #23a4f8;font-size:18px;">{{ $audit->admin->nickname  }}</td>
                        <td>{{ date('Y-m-d',$audit->created_at->timestamp) }}<p>{{ date('H:i',$audit->created_at->timestamp) }}</p></td>
                    </tr>
                @endforeach
                @foreach($payments as $k=>$payment)
                    <tr>
                        <td>财务收款 <span style="color:#ff6633; background-image:url('');">人民币{{ "   ".sprintf("%.2f",$payment->amount/10000)."万   " }}@if($k == $paynum-1 && $payed ==$business->amount)(收款完成)@endif</span></td>
                        <td style="color: #23a4f8 ;font-size:18px;">{{ isset($business->businessAudit) ? $business->businessAudit->admin->nickname : '' }}</td>
                        <td>{{ date('Y-m-d',$payment->created_at->timestamp) }}<p>{{ date('H:i',$payment->created_at->timestamp) }}</p></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div>
                <h4><span>客户介绍：</span></h4>
                <p>{{ $business->customer_intro }}</p>
                <h4><span>业务描述：</span></h4>
                <p>{{ $business->description }}</p>
                <h4><span>其他备注：</span></h4>
                <p>{{ $business->remark }}</p>
                <h4><span>附件：</span></h4>
                @foreach($attachments as $attachment)
                    <p class="down" style="color:#23a4f8; font-size:16px; font-weight:bold;">
                   	 <i style="width:85%;overflow:hidden;display:inline-block;white-space:nowrap;text-indent:0px;font-style:normal;">
                   	 {{ $attachment->name }}（{{ human_filesize($attachment->size )}}）
                    	 <b style="font-weight:normal;color:#e8e8e8;padding-left:20px;"> -------------------------------------------------------------------------------------------------------------------------------------------- </b></i> 
                  
                    	 
                        <a href="{{ $attachment->path }}" ><button type="button">下载查看</button></a>
                    </p>
                @endforeach
            </div>
        </div>
    </div>
@stop
@section('scripts')
    <script type="text/javascript" src="/js/citypartner/jquery-1.6.2.min.js"></script>
    <script type="text/javascript" src="/js/citypartner/common.js"></script>
    <script type="text/javascript" src="/js/citypartner/ajaxfileupload.js"></script>
    <script>var uploadUrl = "{{url('citypartner/upload/index')}}";</script>
@stop
