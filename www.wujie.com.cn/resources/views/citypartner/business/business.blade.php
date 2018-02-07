@extends('citypartner.layouts.layout')
@section('title')
    <title>我的业务</title>
@stop
@section('styles')
    <link rel="stylesheet" href="/css/citypartner/share.css" type="text/css"/>
    <link rel="stylesheet" href="/css/citypartner/business.css" type="text/css"/>
@stop
@section('content')
<div class="no_msg hidden">
           <img src="/images/citypartner/img/no_msg.png"/>
           <p>目前没有任何业务哦~</p>
   </div>
   <div class="container">
      <div class="font">
      	<!--	<a href="/citypartner/profit/rule?uid={{$uid}}">收益规则说明</a>-->
           <h2>
               我的业务
           </h2>
           
       </div>
       <div class="detail">
           <ul id="process">
               <li class="actived" ><a href="javascript:void(0);" id="all">全部（{{ $num['total'] }} )</a></li>
               <li><a href="javascript:void(0)" id="review">待审核（{{ $num['review'] }}）</a></li>
               <li><a href="javascript:void(0)" id="pay">收款中（{{ $num['pay'] }}）</a></li>
               <li><a href="javascript:void(0)" id="finish">已完成（{{ $num['finish'] }}）</a></li>
               <li><a href="javascript:void(0)" id="return">已退回（{{ $num['back'] }}）</a></li>
           </ul>
           <div class="all" >
               <table >
                   <thead>
                   <tr>
                       <th>业务名称</th>
                       <th>合同金额（万元）</th>
                       <th>业务时间</th>
                   </tr>
                   </thead>
                   <tbody>
                   @foreach($business as $b)
                       <tr>
                           <td>
                           	 <div title="">
                           		<span>	@if($b->type == 1) <i>【服务类】</i> @elseif($b->type == 2) <s>【设备类】</s> @elseif($b->type == 3) 【资源对接类】 @elseif($b->type == 4)<b>【投资类】</b>@endif</span>
                                <a href="{{ url('citypartner/business/detail?id='.$b->id) }}">{{ $b->contract_name }}</a>  
                             </div> 
                           </td>
                           <td><span>{{ sprintf("%.2f",$b->amount/10000) }}</span></td>
                           <td>{{ date('Y-m-d',$b->created_at->getTimestamp()) }} <p>{{ date('H:i',$b->created_at->getTimestamp()) }}</p></td>
                       </tr>
                   @endforeach
                   </tbody>
               </table>
               <p>@if( $business->total() > $business->perPage())
                       @if($business->currentPage!=1)
                           <a href="{{ $business->url(1)}}">&lt;&lt;首页</a>
                           <a href="{{ $business->url($business->currentPage() >1 ? $business->currentPage()-1:1 )}}">&lt;上一页</a>
                       @endif
                       {{$business->currentPage()==1 ? 1:($business->currentPage()-1)*$business->perPage()+1}}-{{$business->currentPage()*($business->perPage())}}条，共<span>{{ $business->total() }}条</span>
                   @if($business->currentPage!=$business->lastPage())
                       <a href="{{$business->nextPageUrl()}}">下一页&gt;</a><a href="{{ $business->url($business->lastPage()) }}">尾页&gt;&gt;</a>
                   @endif
                   @else
                       {{$business->currentPage()==1 ? 1:($business->currentPage()-1)*$business->perPage()+1}}-{{$business->currentPage()*($business->perPage())}}条，共<span>{{ $business->total() }}条</span>
                   @endif</p>
           </div>
           <div class="hidden review" >
               <table >
                   <thead>
                   <tr>
                       <th>业务名称</th>
                       <th>合同金额（万元）</th>
                       <th>业务时间</th>
                   </tr>
                   </thead>
                   <tbody>
                   @foreach($businessReview as $b)
                       @if($b->status == 1)
                       <tr>
                           <td>
                            <div title="">
                          		<span>	@if($b->type == 1) <i>【服务类】</i> @elseif($b->type == 2) <s>【设备类】</s> @elseif($b->type == 3) 【资源对接类】 @elseif($b->type == 4)
                          		<b>【投资类】</b>@endif</span>
                           		<a href="{{ url('citypartner/business/detail?id='.$b->id) }}">{{ $b->contract_name }}</a> 
                           </div> 
                           </td>
                           <td><span>{{ sprintf("%.2f",floor($b->amount/100)/100) }}</span></td>
                           <td>{{ date('Y-m-d',$b->created_at->getTimestamp()) }} <p>{{ date('H:i',$b->created_at->getTimestamp()) }}</p></td>
                       </tr>
                       @endif
                   @endforeach
                   </tbody>
               </table>
               <p>@if( $businessReview->total() > $businessReview->perPage())
                       @if($businessReview->currentPage()!=1)
                           <a href="{{ $businessReview->url(1 )}}">&lt;&lt;首页</a>
                           <a href="{{ $businessReview->url($businessReview->currentPage() >1 ? $businessReview->currentPage()-1:1 )}}">&lt;上一页</a>
                       @endif
                       {{$businessReview->currentPage()==1 ? 1:($businessReview->currentPage()-1)*$businessReview->perPage()+1}}-{{$businessReview->currentPage()*($businessReview->perPage())}}条，共<span>{{ $businessReview->total() }}条</span>
                   @if($businessReview->currentPage()!=$businessReview->lastPage())
                       <a href="{{$businessReview->nextPageUrl()}}">下一页&gt;</a><a href="{{ $businessReview->url($businessReview->lastPage()) }}">尾页&gt;&gt;</a>
                   @endif
                   @else
                       {{$businessReview->currentPage()==1 ? 1:($businessReview->currentPage()-1)*$businessReview->perPage()+1}}-{{$businessReview->currentPage()*($businessReview->perPage())}}条，共<span>{{ $businessReview->total() }}条</span>
                   @endif</p>
           </div>
           <div class="hidden pay" >
               <table >
                   <thead>
                   <tr>
                       <th>业务名称</th>
                       <th>合同金额（万元）</th>
                       <th>业务时间</th>
                   </tr>
                   </thead>
                   <tbody>
                   @foreach($businessPay as $b)
                       @if($b->status == 2)
                           <tr>
                               <td>
	                                <div title="">
	                               		<span>	@if($b->type == 1) <i>【服务类】</i> @elseif($b->type == 2) <s>【设备类】</s> @elseif($b->type == 3) 【资源对接类】 @elseif($b->type == 4)
                          					<b>【投资类】</b>@endif</span>
	                               		<a href="{{ url('citypartner/business/detail?id='.$b->id) }}">{{ $b->contract_name }}</a> 
	                               	</div> 
                               </td>
                               <td><span>{{ sprintf("%.2f",$b->amount/10000) }}</span></td>
                               <td>{{ date('Y-m-d',$b->created_at->getTimestamp()) }} <p>{{ date('H:i',$b->created_at->getTimestamp()) }}</p></td>
                           </tr>
                       @endif
                   @endforeach
                   </tbody>
               </table>
               <p>@if( $businessPay->total() > $businessPay->perPage())
                       @if($businessPay->currentPage()!=1)
                           <a href="{{ $businessPay->url(1)}}">&lt;&lt;首页</a>
                           <a href="{{ $businessPay->url($businessPay->currentPage() >1 ? $businessPay->currentPage()-1:1 )}}">&lt;上一页</a>
                       @endif
                       {{$businessPay->currentPage()==1 ? 1:($businessPay->currentPage()-1)*$businessPay->perPage()+1}}-{{$businessPay->currentPage()*($businessPay->perPage())}}条，共<span>{{ $businessPay->total() }}条</span>
                   @if($businessPay->currentPge()!=$businessPay->lastPage())
                       <a href="{{$businessPay->nextPageUrl()}}">下一页&gt;</a><a href="{{ $businessPay->url($businessPay->lastPage()) }}">尾页&gt;&gt;</a>
                       @endif
                   @else {{$businessPay->currentPage()==1 ? 1:($businessPay->currentPage()-1)*$businessPay->perPage()+1}}-{{$businessPay->currentPage()*($businessPay->perPage())}}条，共<span>{{ $businessPay->total() }}条</span>
                   @endif</p>
           </div>
           <div class="hidden finish" >
               <table >
                   <thead>
                   <tr>
                       <th>业务名称</th>
                       <th>合同金额（万元）</th>
                       <th>业务时间</th>
                   </tr>
                   </thead>
                   <tbody>
                   @foreach($businessFinish as $b)
                       @if($b->status == 3)
                           <tr>
                               <td><div title="">
                               			<span>	@if($b->type == 1) <i>【服务类】</i> @elseif($b->type == 2) <s>【设备类】</s> @elseif($b->type == 3) 【资源对接类】 @elseif($b->type == 4)
                               			<b>【投资类】</b>@endif
                          				</span>
										<a href="{{ url('citypartner/business/detail?id='.$b->id) }}">{{ $b->contract_name }}</a> 
									</div> 
								</td>
                               <td><span>{{ sprintf("%.2f",$b->amount/10000) }}</span></td>
                               <td>{{ date('Y-m-d',$b->created_at->getTimestamp()) }} <p>{{ date('H:i',$b->created_at->getTimestamp()) }}</p></td>
                           </tr>
                       @endif
                   @endforeach
                   </tbody>
               </table>
               <p>@if( $businessFinish->total() > $businessFinish->perPage())
                       @if($businessFinish->currentPage()!=1)
                           <a href="{{ $businessFinish->url(1)}}">&lt;&lt;首页</a>
                           <a href="{{ $businessFinish->url($businessFinish->currentPage() >1 ? $businessFinish->currentPage()-1:1 )}}">&lt;上一页</a>
                       @endif
                       {{$businessFinish->currentPage()==1 ? 1:($businessFinish->currentPage()-1)*$businessFinish->perPage()+1}}-{{$businessFinish->currentPage()*($businessFinish->perPage())}}条，共<span>{{ $businessFinish->total() }}条</span>
                   @if($businessFinish->currentPage() != $businessFinish->lastPage())
                       <a href="{{$businessFinish->nextPageUrl()}}">下一页&gt;</a><a href="{{ $businessFinish->url($businessFinish->lastPage()) }}">尾页&gt;&gt;</a>
                       @endif
                   @else {{$businessFinish->currentPage()==1 ? 1:($businessFinish->currentPage()-1)*$businessFinish->perPage()+1}}-{{$businessFinish->currentPage()*($businessFinish->perPage())}}条，共<span>{{ $businessFinish->total() }}条</span>
                   @endif</p>
           </div>
           <div class="hidden return" >
               <table >
                   <thead>
                   <tr>
                       <th>业务名称</th>
                       <th>合同金额（万元）</th>
                       <th>业务时间</th>
                   </tr>
                   </thead>
                   <tbody>
                   @foreach($businessReturn as $b)
                       @if($b->status == -1)
                           <tr>
                               <td><div title=""><span>	@if($b->type == 1) <i>【服务类】</i> @elseif($b->type == 2) <s>【设备类】</s> @elseif($b->type == 3) 【资源对接类】
                               		 @elseif($b->type == 4)<b>【投资类】</b>@endif</span><a href="{{ url('citypartner/business/detail?id='.$b->id) }}">{{ $b->contract_name }}</a> 
                                	</div> 
                                </td>
                               <td><span>{{ sprintf("%.2f",$b->amount/10000) }}</span></td>
                               <td>{{ date('Y-m-d',$b->created_at->getTimestamp()) }} <p>{{ date('H:i',$b->created_at->getTimestamp()) }}</p></td>
                           </tr>
                       @endif
                   @endforeach
                   </tbody>
               </table>
               <p>@if( $businessReturn->total() > $businessReturn->perPage())
                   @if($businessReturn->currentPage()!=1)
                       <a href="{{ $businessReturn->url(1)}}">&lt;&lt;首页</a>
                       <a href="{{ $businessReturn->url($businessReturn->currentPage() >1 ? $businessReturn->currentPage()-1:1 )}}">&lt;上一页</a>
                       @endif
                       {{$businessReturn->currentPage()==1 ? 1:($businessReturn->currentPage()-1)*$businessReturn->perPage()+1}}-{{$businessReturn->currentPage()*($businessReturn->perPage())}}条，共<span>{{ $businessReturn->total() }}条</span>
                   @if($businessReturn->currentPage()!=$businessReturn->lastPage())
                       <a href="{{$businessReturn->nextPageUrl()}}">下一页&gt;</a><a href="{{ $businessReturn->url($businessReturn->lastPage()) }}">尾页&gt;&gt;</a>
                   @endif
                   @else {{$businessReturn->currentPage()==1 ? 1:($businessReturn->currentPage()-1)*$businessReturn->perPage()+1}}-{{$businessReturn->currentPage()*($businessReturn->perPage())}}条，共<span>{{ $businessReturn->total() }}条</span>
                   @endif</p>
           </div>
       </div>
   </div>
@stop
@section('scripts')
    <script type="text/javascript" src="/js/citypartner/jquery-1.6.2.min.js"></script>
    <script>
     $(function(){
            function getvl(name) {
                var reg = new RegExp("(^|\\?|&)"+ name +"=([^&]*)(\\s|&|$)", "i");
                if (reg.test(location.href)) return unescape(RegExp.$2.replace(/\+/g, " "));
                return "";
            };
            if({{ $num['total'] }}==0){
                $(".no_msg").removeClass("hidden");
                $(".detail").addClass("hidden");
                $("body").css("backgroundColor","#fff")
            }
              else{
                $("body").css("backgroundColor","#f0f1f3");
            }
            
                var status = getvl('status');
                if(status==1){
                    $(".detail>div").addClass('hidden');
                    $(".detail .review").removeClass('hidden');
                   	$("#all").parent("li").removeClass('actived');
                  	$("#review").parent("li").addClass('actived');
                }
                else if(status==2){
                    $(".detail>div").addClass('hidden');
                    $(".detail .pay").removeClass('hidden');
                   $("#all").parent("li").removeClass('actived');
                    $("#pay").parent("li").addClass('actived');
                }
                else if(status==3){
                    $(".detail>div").addClass('hidden');
                    $(".detail .finish").removeClass('hidden');
                   $("#all").parent("li").removeClass('actived');
                    $("#finish").parent("li").addClass('actived');
                }
                else if(status==-1){
                    $(".detail>div").addClass('hidden');
                    $(".detail .return").removeClass('hidden');
                    $("#all").parent("li").removeClass('actived');
                   $("#return").parent("li").addClass('actived');
                }
           $("#process li>a").click(function(){
                $(this).parent("li").addClass("actived").siblings().removeClass("actived");
                var ID=$(this).attr('id');
                $("."+ID).removeClass("hidden").siblings("div").addClass("hidden");
            });

        })
    </script>
@stop