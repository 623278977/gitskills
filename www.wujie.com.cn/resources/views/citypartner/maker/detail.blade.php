@extends('citypartner.layouts.layout')

@section('styles')
    <link rel="stylesheet" href="/css/citypartner/share.css"/>
    <link rel="stylesheet" href="/css/citypartner/reset.css">
    <link rel="stylesheet" href="/css/citypartner/common.css">
    <link rel="stylesheet" href="/css/citypartner/w-pages.css">
    <link rel="stylesheet" href="/css/citypartner/w-ovo.css">
    <link rel="stylesheet" href="/css/citypartner/ovo_member.css">
@stop
@section('title')
<title>我的OVO中心</title>
@stop

@section('content')
<div class="g-ovo">
    <div class="container myovo">
    	<div class="prompt hide" id="error" > 
    		<p> </p>	
    	</div>
    	<div class="prompt hide" id="success" > 
    		<p> 合办申请成功!</p>	
    	</div>					
        @include('citypartner.maker.navigation')
                    <div>
                        <div class="box-all">
                            <div class="ovo-boxs mc">
                                <div class="ovo-boxs-img">
                                    <img src="{{$activity['list_img']}}" alt="">
                                </div>
                                <h3>{{$activity['subject']}}</h3>
                                <div class="title">
                                    <span>发布者</span>{{$activity['publisher']}}
                                </div>
                                <div class="title">
                                    <span>活动时间</span>{{$activity['begin_time']}}
                                </div>
                                @if($flag!=1)
                                <div class="title">
                                    <span>门票总数</span>{{$activity['activity_count']}}张
                                </div>
                                @endif
                                <div class="title">
                                    @if($activity['price']==-1)
                                        <span>票券票价</span><strong>免费</strong>
                                    @else
                                        <span>票券票价</span>  ￥ <strong>{{$activity['price']}}</strong> 元
                                    @endif
                                </div>
                                <div class="clearfix"></div>
                                <h3>活动详情</h3>
                                <p class="mt20">{!!$activity['description']!!}</p>
                            </div>
                            @if($flag==1)
                            <div class="ovo-boxs mt20 mc" id="never">
                                <h3 class="tc mt100">您未联合创办本场活动</h3>
                                @if($status=='立即合办')
                                <button class="btn btn-ovo-act mc" onclick="tanchuang($(this));" activity_id="{{$activity['id']}}">立即合办</button>
                                @else
                                <button class="btn btn-ovo-act mc"<?php if($status=='通知会员'){?> onclick="tanchuang($(this));" activity_id="{{$activity['id']}}"<?php }?>>{{$status}}</button>
                                @endif
                            </div>
                            @elseif($flag==2||$flag==3)
                                <div class="ovo-boxs-h ">
                                    报名总数：{{$myOvoApplyCount}}人
                                </div>
                                <table class="tc mc">
                                    <thead>
                                        <tr>
                                           <th>姓名</th>
                                            <th>手机号</th>
                                            <th>报名时间</th>
                                            <th>签到情况</th>
                                            <th>签到时间</th> 
                                        </tr>
                                    </thead>
                                    <tbody id="tbody">
                                    </tbody>
                                </table>
                                <div class="ovo-boxs-h2 ">
                                    <div class="m-ovo-pages pagecontrol">
                                        
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="overlay hide"></div>
    <div class="join hide">
        <a href="javascript:void(0)" class="close_join">×</a>
        <h1>我要合办活动</h1>
        <form action="">
            <p><label for="">设置活动人数：</label>
                <input type="text" class="input_num"onkeyup='this.value=this.value.replace(/\D/gi,"")'/>人
            </p>
            <p><span>选择合办活动后，该场活动会公开给您的会员，</span></p>
            <p><span>您的会员将可以看到本场活动，并选择您的网点报名参加活动</span></p>
            <p><button type="button" class="setactivitycount close_join">确定</button></p>
        </form>
    </div>
    <div class="notice">
        <a href="javascript:void(0)" class="close_notice">×</a>
        <h1>选择会员</h1>
        <form action="" >
            <p>
                <span class="uncheck" >
                   <input type="checkbox" name="total" class="login-checkbox" id="all_check" />
                </span>
                全选
            </p>
            <div class="sel" id="mainBox">
                <div  id="content">
                </div>
            </div>
            <p>短信内容如下：</p>
            <div class="msg">
                <p class="firstLine"></p>
                <p class="secondLine"></p>
                <p class="thirdLine"></p>
            </div>
            <button type="button" class="sendMessage close_notice">立即推送</button>
        </form>
    </div>
<script type="text/javascript" src="{{URL::asset('js/')}}/citypartner/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="/js/citypartner/common.js"></script>
<script src="/js/citypartner/index.js"></script>
<script src="/js/citypartner/myovo.js"></script>
@stop
@section('scripts')
    <script>
        $(function(){
            tabs($('.m-title ul.tit li '), $('.m-ovo-box'));
            tabs($('.m-ovo-box ul li'),$('.box-all'));
           if($("#never>button").html()=="已结束"){
                $("#never>button").removeClass("btn-ovo-act").addClass("btn-ovo-over");
           }
           if($("#never>button").html()=="已过期"){
                 $("#never>button").removeClass("btn-ovo-act").addClass("btn-ovo-over");
           }
        });
        var params={
            page:1,
            id:'{{$activity['id']}}',
            maker_id:'{{$maker_id}}',
            flag:'{{$flag}}'
        };
        @if($flag==2&&$myOvoApplyCount)
            myovo.getApplyusers(params);
        @endif

        $(document).on("click",".ajaxPage",function(){
            params.page=$(this).attr('page');
            myovo.getApplyusers(params);
            return false;
        });
        function tanchuang($obj){
            obj=$obj;
            $obj.addClass('active_activity_id');
            $(".overlay").removeClass("hide");
            $target={};
            if(obj.html()=='我要合办'){
                $target=$('.join');
                $target.removeClass("hide");
                $target.css("visibility","visible");
            }else if(obj.html()=='通知会员'){
                $target=$('.notice');
                var params={
                    activity_id:$obj.attr('activity_id')
                };
                myovo.showpanel(params);
            }

        };
        $('.setactivitycount').click(function(){
            if(!$('.input_num').val()){
            $("#error p").html("人数不能为空!")
            $("#error").css("display","block");
            setTimeout('$("#error").hide("slow")',2000);
                return false;
            }
            var params={
                num:$('.input_num').val(),
                activity_id:$('.active_activity_id').attr('activity_id')
            };
            var url=url_prex+'maker/joint';
            ajaxRequest(params,url,function(data){
                $("#success p").html("合办申请成功!")
                $("#success").css("display","block");
                setTimeout('$("#error").hide("slow")',2000);
                location.reload(true);
            });
            return false;
        });
        $(".close_join").click(function(){
            $(".join").addClass("hide");
            $(".overlay").addClass("hide");
        });
    </script>
@stop