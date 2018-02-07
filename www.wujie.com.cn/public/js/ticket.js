/*
票券
 */
var Ticket = $.extend({},{
    //现场票
    sceneTicketDetial:function(id,uid,activity_id){
        var url = labUser.api_path+'/userticket/detail';
        //var url = 'http://www.wjsq3.com:8080/www.wjsq3.com/public/api/userticket/detail';
        var param={};
        param["id"]=id;
        param["uid"]=uid;
        param["activity_id"]=activity_id;
        ajaxRequest(param,url,function(data){
            if(data.status){
                var datas = data.message;
                $("#title-x").html(datas.subject);
                $("#noticket-title").html(datas.subject);
                $(".code-img").attr("src",datas.qrcode);
                $(".ticket-num span").html(datas.ticket_no);
                $(".need-money i").html("¥"+datas.price);
                if(datas.is_check){
                    
                }else{
                    $(".ticket-img").attr('src',labUser.path+'/images/ticket.jpg');
                }
                $("#user-id").attr("src",labUser.avatar)
                $(".person").html('<span class="icon w33"><img src="'+labUser.avatar+'" alt="'+labUser.username+'" /></span>'+labUser.username);
                $("#times").html(datas.begin_time);
                $("#maker-ks").html(datas.maker_subject+'<br><i>'+datas.address+'</i>');
                $("#ticket-type").html(datas.type);
                $("#act-detail").attr("href",datas.activity_url);

                $(".my_detial").show();
            }
        });

    },

    //直播票
    zbTicketDetial:function(id,uid,activity_id){
        var url = labUser.api_path+'/live/livedata';
        //var url = 'http://www.wjsq3.com:8080/www.wjsq3.com/public/api/userticket/detail';
        var param={};
        param["id"]=id;
        param["uid"]=uid;
        param["activity_id"]=activity_id;
        ajaxRequest(param,url,function(data){
            if(data.status){
                var datas = data.message;
                $("#title-x").html(datas.subject);
                $(".code-img").attr("src",datas.qrcode);
                $(".ticket-num span").html(datas.ticket_no);
                $("#user-id").attr("src",labUser.avatar)
                $(".person").html('<span class="icon w33"><img src="'+labUser.avatar+'" alt="'+labUser.username+'" /></span>'+labUser.username);
                $("#ticket-type").html(datas.type);
                $("#act-detail").attr("href",datas.activity_url);
                $(".my_detial").show();
            }
        });
    },

    zbTicketDetial_n:function(id,uid,activity_id){
        var url = labUser.api_path+'/live/livedata';
        //var url = 'http://www.wjsq3.com:8080/www.wjsq3.com/public/api/live/livedata';
        var param={};
        param["id"]=id;
        param["uid"]=uid;
        param["activity_id"]=activity_id;
        ajaxRequest(param,url,function(data){
            if(data.status){
                var datas = data.message;
                $("#title-x").html(datas.subject);
                $("#zb-detail").attr("href",datas.url);
                $("#zb-detail .green").html(datas.begin_time);
                $(".my_detial").show();
            }
        });
    },

    //支付
    notTicket:function(ticket_no){
        var url = labUser.api_path+'/order/deadline';
        var param={};
        param["ticket_no"]=123456;
        ajaxRequest(param,url,function(data){
            if(data.status){
                var datas = data.message;
                function timet(){
                    var t = datas.order_lefttime; 
                    var m = Math.floor(t/60%60);
                    var s = Math.floor(t%60);
                    datas.order_lefttime--;
                    var m1 = String(m).substring(0,1);
                    var m2 = String(m).substring(1,2);
                    var s1 = String(s).substring(0,1);
                    var s2 = String(s).substring(1,2);
                    if(m>=10){
                        $("#time-low li").eq(0).html(m1);
                        $("#time-low li").eq(1).html(m2);
                    }else{
                        $("#time-low li").eq(0).html(0);
                        $("#time-low li").eq(1).html(m);
                    }
                    if(s>=10){
                        $("#time-low li").eq(3).html(s1);
                        $("#time-low li").eq(4).html(s2);
                    }else{
                        $("#time-low li").eq(3).html(0);
                        $("#time-low li").eq(4).html(s);
                    }
                    if(t <= 0){
                        $("#time-low li").eq(0).html("0");
                        $("#time-low li").eq(1).html("0");
                        $("#time-low li").eq(3).html("0");
                        $("#time-low li").eq(4).html("0");
                        clearInterval(ches);
                        $(".ph-title.orange").removeClass("none");
                        $(".need-money,.time-dowm").addClass("active");
                        $(".subThis").html('<button class="btn green-bg">删除该门票</button>');
                    }else{
                        $(".btn.blue-bg").click(function(){
                            clearInterval(ches);
                        })
                    }
                }
                timet();
                var ches = setInterval(function(){
                    timet();
                },1000);
                $(".my_detial").show();
            }
        });
        
    },

    //删除门票
    deleted:function(id,uid){
        var url = labUser.api_path+'/userticket/delete';
        //var url = 'http://www.wjsq3.com:8080/www.wjsq3.com/public/api/userticket/delete';
        var param={};
        param["id"]=id;
        param["uid"]=uid;
        ajaxRequest(param,url,function(data){
            if(data.status){

            }
        });
    }

})