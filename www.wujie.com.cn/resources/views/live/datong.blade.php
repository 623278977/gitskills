@extends('layouts.default')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{URL::asset('/')}}/css/datong.css"/>
@stop
@section('main')
    <section class="containerBox" id="containerBox" style='width:100%;height:100%;'>
        <!-- <div class="logo">
            <div class="l wujie">
                <img src="{{URL::asset('/')}}/images/datong/wujie_logo.png"  alt="">
                <p>无界商圈</p>
            </div>
            <div class="l datong"><img src="{{URL::asset('/')}}/images/datong/xiaoxijilogo.png"  alt=""></div>
        </div> -->
        <div class="head">
            <img src="{{URL::asset('/')}}/images/datong/title1.png"  alt="">
        </div>
        <p class="join">加入我们&nbsp;&nbsp;&nbsp;小投入&nbsp;大回报</p>
        <div class="l users">
            <p><span class="MF" id="usercount"><em></em></span> 在线</p>
            <div class="user_detail">
                <ul id="user_list">
                    <!-- <li><img src="{{URL::asset('/')}}/images/datong/datong_logo.png" alt=""><p>name1</p></li> -->
                </ul>
            </div>
        </div>
        <div class="l messages">
            <p><span class="MF" id='mescount'><em></em></span> 条信息</p>
            <div class="mes_detail ">
                <ul id='mes_list'>
                    <!-- <li>
                       <div class="mes_bg">
                            <img src="{{URL::asset('/')}}/images/datong/datong_logo.png" alt="" class="l">
                            <div class="l intro">
                                <p class="nickname">张二狗</p>
                                <p class='content'><img src=''>呵呵，说什么呢</p>
                            </div>
                        </div> 
                    </li> -->
                  
                </ul>
            </div>
            <div class="words none">
                <a class="close l">×</a>
                <img src="" alt="" class="l" >
                <div class="words_detail l">
                    <p id='words_name'></p>
                    <p id="words_con"></p>
                    <p id="words_img"></p>
                </div>
            </div>
        </div>
        <div class="l orders">
             <p><span class="MF " id="ordercount"><em></em></span> 个实时订单</p>
             <div class="orders_detail">
                <ul id='orderlist'>
                    <!-- <li>
                        <p><span class="brand_name">品牌名称</span></p>
                        <label class="w_45"><span class="customer">杭州/张***</span></label>
                        <label class="w_45"><span class="tel">18000000000</span></label>
                    </li> -->
                </ul>
             </div>
             <div class="erweima">  
                <img src="{{URL::asset('/')}}/images/wjerwei.png" alt="" >
                <p>下载无界商圈APP</p>
                <p>实时参与互动</p>
             </div>
        </div>
        <div class="foot">
            <span>两大“网红”品牌 邀你连线财富 见证实力</span>
        </div>
        <div class="newOrder none">
             <div class='center'>
                  <a class="close_alert l" id="close_alert">×</a>
                  <img src="{{URL::asset('/')}}/images/datong/order_check.png" alt="" class="order_check">
                  <p><span class="new">有新的订单啦！</span></p>  
                  <div class="ord_detail">
                     <span class="detail_brand">品牌名称</span>
                     <p class="xinxi"><span class="detail_add"></span><span class="detail_tel"></span></p>
                  </div>
             </div>   
        </div>
        <input type="hidden" id='dataStore' >
        <audio id="myaudio1" src="{{URL::asset('/')}}/images/maleo.mp3" hidden="true" controls="controls"></audio>
    </section>
@stop
@section('endjs')
     <script type="text/javascript" src="{{URL::asset('/')}}/js/jquery-1.8.3.min.js"></script>
     <script  type="text/javascript">
     $(function(){
        var dm_run_once = true;
        var dd_run_once = true;
        var yh_run_once=true;
        var user_run_once=true;
        var dm_array = [];
        var ord_array=[];
        var user_array=[];
        var param = {
                "id": "<?php echo $id;?>",
                "uid": "<?php echo $uid;?>",
                "section": 0,
                "commentType": 'Live',
                "type": 'Live',
                "use": 'big_screen',
                "urlPath": window.location.href,
                "page": 1,
                "page_size": 15,
                "update": "new",
                "fecthSize": 15,
                "real_order_max_id": 0,
                "sham_order_max_id": 0,
                "fromId": 0,
                "case": 'mix',
                'with_anonymous':0,
                'log_id':0,
                'fetch_size':60
            };
        var method={
        //在线人员列表
            getUserlist:function(param){
                var params={};
                    params['live_id']=param.id;
                    params['with_anonymous']=param.with_anonymous;
                    params['log_id']=param.log_id;
                    params['fetch_size']=param.fetch_size;
                var url=labUser.api_path + '/live/viewers/_v020400';
                ajaxRequest(params,url,function(data){
                    if(data.status){
                         if (data.message.count> 999) {
                              $('#usercount').html(999 + '<em>+</em>');
                            } else {
                               $('#usercount').html(data.message.count);
                            }
                        if(data.message.users.length>0){
                            $.each(data.message.users,function(i,item){
                                user_array.push(item);
                            })
                        }
                        $('#dataStore').attr("data-max_log_id", data.message.max_log_id);
                    }
                   if(user_run_once){
                        getUseronce( user_array);
                        user_run_once=false;
                   }
                    userInter=setInterval(function(){getUsers(user_array,userInter)},2000);
                })
            },
        // 订单列表
            getOrderList:function(param){
                var params = {};
                    params['live_id'] = param.id;
                    params['sham_order_max_id'] = param.sham_order_max_id;
                    params['real_order_max_id'] = param.real_order_max_id;
                    params['type'] = param.type;
                    var url = labUser.api_path + '/live/order-list';
                    ajaxRequest(params, url, function (data) {
                        if(data.status){
                            var returnObj = data.message;
                            $('#dataStore').attr("data-realid", returnObj.real_order_max_id);
                            $('#dataStore').attr("data-shamid", returnObj.sham_order_max_id);
                            if (returnObj.all_count > 99) {
                                $('#ordercount').html(99 + '<em>+</em>');
                            } else {
                                $('#ordercount').text(returnObj.all_count);
                            }
                            if (returnObj.orders_dynamic.length > 0) {
                                //下单动态
                                var orderdtHtml = '';
                                $.each(returnObj.orders_dynamic, function (index, item) {
                                    orderdtHtml += '<li><p><span class="brand_name">'+item.title+'</span></p>';
                                    orderdtHtml += '<label class="w_45"><span class="customer">' + item.zone_name + '/' + item.realname + '</span></label>';
                                    orderdtHtml += '<label class="w_45"><span class="tel">' + item.mobile + '</span></label></li>';
                                    ord_array.push(item);
                                });
                                $('#orderlist').append(orderdtHtml);
                              
                            }

                            if (dd_run_once) {
                                orderAnimate();
                                getNeworder(ord_array);
                                dd_run_once = false;

                            }
                        }
                    })
            },
        //评论列表
            getFreshList:function(param){
                var params = {};
                    params['type'] = param.type;
                    params['use'] = param.use;
                    params['uid'] = param.uid;
                    params['id'] = param.id;
                    params['fromId'] = param.fromId;
                    params['update'] = param.update;
                    params['fecthSize'] = param.fecthSize;
                var url = labUser.api_path + '/comment/fresh-list';
                ajaxRequest(params, url, function (data) {
                    if(data.status){
                         var resObj = data.message;
                        if (resObj.data.length > 0) {
                            $.each(resObj.data, function (index, item) {
                                // var content = cutString(item.content, 20);
                                var dm_item = {"info": item.c_nickname ,"content" : item.content, "img": item.avatar,'pic':item.images,'id':item.id};
                                dm_array.push(dm_item);
                            });
                        }
                        $('#dataStore').attr("data-fromid", resObj.max_id);
                        if (data.message.all_count> 999) {
                              $('#mescount').html(999 + '<em>+</em>');
                            } else {
                               $('#mescount').html(data.message.all_count);
                            }
                        
                        if (dm_run_once) {
                            fresh(dm_array);
                            dm_run_once = false;

                        }
                    }
                });
            }

        };
        // 加载用户列表
            //初次执行
            function getUseronce(arr){
                if(arr.length>12){
                    var userHtml='';
                    for(var i=0;i<12;i++){
                        userHtml+='<li><img src="'+arr[i].avatar+'"><p>'+cutString(arr[i].nickname, 3)+'</p></li>'
                    };
                    $('#user_list').html(userHtml);
                    arr.splice(0,12);
                }else if(arr.length<=12&&arr.length>0){
                    var userHtml='';
                    for(var i=0;i<arr.length;i++){
                        userHtml+='<li><img src="'+arr[i].avatar+'"><p>'+cutString(arr[i].nickname, 3)+'</p></li>'
                    };
                    $('#user_list').html(userHtml);
                     arr.splice(0);
                }
            };
            //定时执行
            function getUsers(arr,userInter){
                if(arr.length>12){
                    var userHtml='';
                    for(var i=0;i<12;i++){
                        userHtml+='<li><img src="'+arr[i].avatar+'"><p>'+cutString(arr[i].nickname, 3)+'</p></li>'
                    };
                    $('#user_list').html(userHtml);                    
                    arr.splice(0,12);
                }else if(arr.length<=12&&arr.length>0){
                    var userHtml='';
                    for(var i=0;i<arr.length;i++){
                        userHtml+='<li><img src="'+arr[i].avatar+'"><p>'+cutString(arr[i].nickname, 3)+'</p></li>'
                    };                                      
                    if($('#user_list>li').length>=12){
                            $('#user_list').prepend(userHtml);
                    }else{
                            $('#user_list').append(userHtml);
                     }                    
                    if($('#user_list>li').length>12){
                        $('#user_list>li').slice(12).remove();
                    };                  
                    arr.splice(0);
                    clearInterval(userInter);
                }
            };

        // 评论详情页
        function getMesdetail(){
            $(document).on('click','.mes_bg',function(){ 
                var id=$(this).attr('data-id');
                // method.getFreshdetail(id);
                var imgSrc=$(this).children('img').attr('src'),
                    nick=$(this).find('.nickname').text(),
                    content=$(this).find('.content').text(),
                    imgDis=$(this).find('.content').children('img');
                    $('.words>img').attr('src',imgSrc);
                    $('#words_name').text(nick);
                    $('#words_con').text(content);                 
                    $('#words_img').html(imgDis.clone());
                    $('.mes_detail').animate({'height':0},500,function(){
                        $('.mes_detail').addClass('none');
                        $('.words').removeClass('none').animate({'width':'100%','height':'85%'},500);
                    }); 

            });
            $(document).on('click','.words',function(){
                $('.words').animate({'width':'100%','height':0},500,function(){ 
                    $('.words').addClass('none');
                    $('.mes_detail').removeClass('none').animate({'height':'85%'},500);
                })
            }) ;

        };
        getMesdetail();    
         //下单动态
            function orderAnimate() {
                var orLenEle = $('#orderlist li');
                if (orLenEle.length > 3) {
                    looper_dingdan = setInterval(function () {
                        if ($('#orderlist li').length > 3) {
                            var firstTag=$('#orderlist').find('li:first');
                            var height=firstTag.height();
                            firstTag.animate({'height':0,'padding-top':'0','opacity':0},500,function(){
                                $('#orderlist li').first().remove();
                            });
                        }
                    }, 2000);
                }

            }
        // 新订单弹窗
            function getNeworder(arr){
                var i;
                if(!sessionStorage.getItem('ordernum')&&sessionStorage.getItem('ordernum')!=0){
                    i=0;
                }else{
                    i=parseInt(sessionStorage.getItem('ordernum'));
                };
               console.log(i);
                if(arr.length>0){
                     newOrder=setInterval(function(){                    
                        if(i<arr.length){
                            $('.detail_brand').html(arr[i].title);
                            $('.detail_add').html(arr[i].zone_name+'/'+arr[i].realname);
                            $('.detail_tel').html(arr[i].mobile);
                            $(".newOrder").css('display',"block");          
                            playMaleo();
                            setTimeout(function(){$(".newOrder").hide();},2000);
                            i++;
                        };
                        sessionStorage.setItem('ordernum',i);

                    },3000)
                }
            }; 
        //手动关闭订单弹窗
            $('.newOrder').on('click',function(){
                $('.newOrder').css('display','none');
            })
        //留言列表刷新

            function fresh(dm_array){
                if(dm_array.length>=4){
                    var mesHtml='';
                    for(var i=0;i<4;i++){
                        var imgHtml='';
                        if(dm_array[i].pic.length>0){
                            $.each(dm_array[i].pic,function(index,item){
                                imgHtml+='<img src="'+item+'">';
                            });
                        }
                        mesHtml+='<li><div class="mes_bg" data-id="'+dm_array[i].id+'"><img class="l" src="'+dm_array[i].img+'">';     
                        mesHtml+='<div class="l intro"><p class="nickname">'+dm_array[i].info+'</p>';
                        mesHtml+='<p class="content">'+imgHtml+dm_array[i].content+'</p></div><img id="go" src="{{URL::asset('/')}}/images/datong/click.png"></div></li>';
                    };
                    $('#mes_list').html(mesHtml);
                    $('#mes_list>li').addClass('future').removeClass('future');
                    dm_array.splice(0,4);
                    
                }else{
                    var mesHtml='';
                    for(var i=0;i<dm_array.length;i++){
                        var imgHtml='';
                        if(dm_array[i].pic.length>0){
                            $.each(dm_array[i].pic,function(index,item){
                            imgHtml+='<img src="'+item+'">';
                            })
                        }
                        mesHtml+='<li><div class="mes_bg" data-id="'+dm_array[i].id+'"><img class="l" src="'+dm_array[i].img+'">';     
                        mesHtml+='<div class="l intro"><p class="nickname">'+dm_array[i].info+'</p>';
                        mesHtml+='<p class="content">'+imgHtml+dm_array[i].content+'</p></div><img id="go" src="{{URL::asset('/')}}/images/datong/click.png"></div></li>'     
                    };
                    $('#mes_list').html(mesHtml);
                    $('#mes_list>li').addClass('future').removeClass('future');
                     dm_array.splice(0);
                };
               
                var Height=$('#mes_list>li').height();      
                     freshMes=setInterval(function(){
                     var behind='';
                      if(dm_array.length>4){
                         for(var i=0;i<4;i++){
                            var imgHtml='';
                                if(dm_array[i].pic.length>0){
                                $.each(dm_array[i].pic,function(index,item){
                                imgHtml+='<img src="'+item+'">';
                                })
                            };
                                behind+='<li><div class="mes_bg" data-id="'+dm_array[i].id+'"><img class="l" src="'+dm_array[i].img+'">';
                                behind+='<div class="l intro"><p class="nickname">'+dm_array[i].info+'</p>';
                                behind+='<p class="content">'+imgHtml+dm_array[i].content+'</p></div><img id="go" src="{{URL::asset('/')}}/images/datong/click.png"></div></li>'     
                         };
                        $('#mes_list').append(behind); 
                        $('#mes_list>li').addClass('future');
                        $('#mes_list').animate({'marginTop':-4*Height+'px'},500,function(){  
                             $('#mes_list>li').slice(0,4).remove();
                             $(this).css('marginTop',0);
                             $('#mes_list>li').removeClass('future');
                            dm_array.splice(0,4);  
                        })
                        }else if(dm_array.length>0&&dm_array.length<=4){  
                             console.log(dm_array.length);
                             var newLength=dm_array.length;
                            for(var i=0;i<newLength;i++){                              
                                var imgHtml='';
                                if(dm_array[i].pic.length>0){
                                    $.each(dm_array[i].pic,function(index,item){
                                    imgHtml+='<img src="'+item+'">';
                                    })
                                 };
                                behind+='<li><div class="mes_bg" data-id="'+dm_array[i].id+'"><img class="l" src="'+dm_array[i].img+'">'; 
                                behind+='<div class="l intro"><p class="nickname">'+dm_array[i].info+'</p>';
                                behind+='<p class="content">'+imgHtml+dm_array[i].content+'</p></div><img id="go" src="{{URL::asset('/')}}/images/datong/click.png"></div></li>'     
                            };
                            $('#mes_list').append(behind); 
                            $('#mes_list>li').slice(4).addClass('future');
                            $('#mes_list').animate({'marginTop':-(dm_array.length*Height)+'px'},500,function(){  
                                 $('#mes_list>li').slice(0,newLength).remove();
                                 console.log(dm_array.length);
                                  $('#mes_list>li').removeClass('future');
                                 $(this).css('marginTop',0);
                                dm_array.splice(0,newLength); 
                            });                           
                        }
                },3000);

            };


        //获取订单数据
            method.getOrderList({
                'id': param.id,
                'sham_order_max_id': $('#dataStore').attr("data-shamid"),
                'real_order_max_id': $('#dataStore').attr("data-realid"),
                'type': param.case
            });
        //获取评论数据
            method.getFreshList(param);
            method.getUserlist({
                'id': param.id,
                'with_anonymous':0,
                'log_id':0,
                'fetch_size':60
            });
        // 定时刷新
            setInterval(function () {
                //获取订单数据
                method.getOrderList({
                    'id': param.id,
                    'sham_order_max_id': $('#dataStore').attr("data-shamid"),
                    'real_order_max_id': $('#dataStore').attr("data-realid"),
                    'type': param.case
                });
                //获取评论数据
                param.fromId = $('#dataStore').attr("data-fromid");
                // param.log_id=$('#dataStore').attr("data-max_log_id");
                method.getFreshList(param);
                method.getUserlist({
                'id': param.id,
                'with_anonymous':0,
                'log_id':$('#dataStore').attr("data-max_log_id"),
                'fetch_size':60
                });
            
            }, 13000);
        // //切页面
        //     document.addEventListener("visibilitychange", function() {
        //         if(document.visibilityState=='visible'){
        //             fresh();
        //             barrager();
        //         }
        //         else if(document.visibilityState=='hidden'){
        //             clearAllDm();
        //             clearInterval(freshMes);
        //             clearInterval(looper_danmu);
        //         }
        //     });
        //maleo
            function playMaleo(){
                var myAuto_win = document.getElementById('myaudio1');
                myAuto_win.play();
            }
     });
     </script>
@stop