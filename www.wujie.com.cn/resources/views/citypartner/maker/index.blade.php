@extends('citypartner.layouts.layout')

@section('styles')
    <link rel="stylesheet" href="/css/citypartner/share.css"/>
    <link rel="stylesheet" href="/css/citypartner/reset.css">
    <link rel="stylesheet" href="/css/citypartner/common.css">
    <link rel="stylesheet" href="/css/citypartner/w-pages.css">
    <link rel="stylesheet" href="/css/citypartner/w-ovo.css">
    <link rel="stylesheet" href="/css/citypartner/ovo_member.css">
    <link rel="stylesheet" href="/css/citypartner/account.css">
    <script type="text/javascript" src="/js/citypartner/jquery-1.8.3.min.js"></script>
@stop
@section('title')
<title>我的OVO中心</title>
@stop

@section('content')
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
                                @foreach($data as $k=>$v)
                                <div class="box-contain @if($v['status']=="通知会员") box-contain-mem @endif @if($v['status']=="已举办"||$v['status']=="已过期"||$v['status']=="已结束") box-contain-pass @endif">
                                    <div class="box-img">
                                        <a href="{{$v['detailurl']}}"><img src="{{$v['list_img']}}" alt=""></a>
                                    </div>
                                    <p>
                                        <a href="{{$v['detailurl']}}">{{cut_str($v['subject'],26)}}</a>
                                    </p>
                                    <span>{{$v['begin_time']}}</span>
                                    @if($v['price']==-1)
                                    <a class="ar"><em ></em><strong>免费</strong></a>
                                    @else
                                    <a class="ar"><em >￥</em> <strong>{{$v['price']}}</strong> <em>元</em></a>
                                    @endif

                                    <button activity_id="{{$v['id']}}" @if($v['status']=='我要合办'||$v['status']=='通知会员')onclick="tanchuang($(this));;" @endif class="btn btn-ovo-act mc">{{$v['status']}}</button>
                                </div>
                                    @endforeach
                                <div class="clearfix"></div>
                                    @if($totalPage>1)
                                        <div class="m-ovo-pages">
                                            <?php echo  loadPage($totalPage,$currentPage,$activitysCount);?>
                                        </div>
                                    @endif
                            </div>
                        </div>

                    </div>

                </div>


            </div>
        </div>
    </div>
     <script>
        $(function(){
            tabs($('.m-title ul.tit li '), $('.m-ovo-box'));
            tabs($('.m-ovo-box ul li'),$('.box-all'));
        })
        /***全局变量**/
        var obj={};
        var nickname_array=[];
        var username_array=[];
        var uid_array=[];
        /***全局变量**/
        function createNickname(){
             nickname_array=[];
             username_array=[];
             uid_array=[];
            $('#content .checkbox').each(function(){
                var nickname=$(this).parent().find('.nickname_label').attr('nickname');
                var username=$(this).parent().find('.tel_label').attr('username');
                var uid=$(this).parent().find('.tel_label').attr('uid');
                if(nickname_array.indexOf(nickname)==-1){
                    nickname_array.push(nickname);
                }
                if(username_array.indexOf(username)==-1){
                    username_array.push(username);
                }
                if(uid_array.indexOf(uid)==-1){
                    uid_array.push(uid);
                }
            });
            $('.huiyuanming').html(nickname_array.join(','));
        }
        $("#all_check").click(function(){
            if(this.checked){
                $(".uncheck").addClass("checkbox");
            }else{
                $(".uncheck").removeClass("checkbox");
            }
            createNickname();
        });
        $(document).on('click','#content span.uncheck',function(){
            $(this).toggleClass("checkbox");
            createNickname();
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
        $(".close_join").click(function(){
            $(".join").addClass("hide");
            $(".overlay").addClass("hide");
        });
        $(".close_notice").click(function(){
            $(".notice").addClass("hide");
            $(".overlay").addClass("hide");
        });

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
            myovo.setactivitycount(params);
            return false;
        });


        $('.sendMessage').click(function(){
            var message='尊敬的会员@{{username}}：您好，“'+$('.maker_address').html()+'”将于“'+$('.activity_begin_time').html()+'”举办“'+$('.activity_subject').html()+'”活动，欢迎您报名参加，详情请查看“'+$('.activity_short_url').attr('href')+'”';
            message+='活动地点：“'+$('.maker_address').html()+'”';
            message+='活动时间：“'+$('.activity_begin_time').html()+'”';
            var params={
                username:username_array,
                uid:uid_array,
                message:message,
                activity_id:obj.attr('activity_id')
            };
            if(!username_array.length||!uid_array){
            	$(".overlay").removeClass("hide")
            	$(".notice").removeClass("hide");
                $("#error p").html("请选择会员!");
            	$("#error").css("display","block");
            	setTimeout('$("#error").hide("slow")',2000);

                return false;
            }
            myovo.sendMessage(params);
        });
    </script>
    
    <script type="text/javascript" src="/js/citypartner/common.js"></script>
    <script src="/js/citypartner/index.js"></script>
    <script src="/js/citypartner/myovo.js"></script>
    <script src="/js/citypartner/gundongtiao.js"></script>
   
@stop
