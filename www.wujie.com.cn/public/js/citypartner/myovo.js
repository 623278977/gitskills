$('.btn-ovo-create').click(function(){
    window.location.href=$(this).attr('href');
});
var myovo;
/**我的ovo中心**/
myovo = $.extend({},{

    /**发布活动**/
    createActivity:function(params){
        var url=url_prex+'maker/storeactivity';
        ajaxRequest(params,url, function (data) {
            $("#success p").html("活动创建成功!")
            $("#success").css("display","block");
            setTimeout('$("#error").hide("slow")',2000);
            if(data.status){
                location.href='/citypartner/maker/index?type=3';
            }
        });
    },
    /**设置活动人数**/
    setactivitycount:function(params){
        var url=url_prex+'maker/joint';
        ajaxRequest(params,url,function(data){
            $("#success p").html("合办申请成功!")
            $("#success").css("display","block");
            setTimeout('$("#error").hide("slow")',2000);
            obj.html('通知会员');
            obj.parent('.box-contain').addClass('box-contain-mem');
            $(".join").addClass("hide");
            $(".overlay").addClass("hide");
        });
    },
    /**通知会员 页面数据显示**/
    showpanel:function(params){
        var url=url_prex+'maker/showpanel';
        ajaxRequest(params,url,function(data){
            if(data.status){
                var members=data.message.members;
                var activity=data.message.activity;
                var maker=data.message.maker;
                var membersHtml='';

                if(members.length){
                    $.each(members,function(i,member){
                        membersHtml+='<p><span class="uncheck">';
                        membersHtml+=' <input type="checkbox" class="tel_label" name="tel" username="'+member.username+'" uid="'+member.uid+'"/>';
                        membersHtml+='</span>';
                        membersHtml+='<label class="nickname_label" for="" nickname="'+member.nickname+'">'+member.nickname+'<span>'+member.dealtel+'</span></label>';
                        membersHtml+='</p>';
                    });
                }
                $('#content').html(membersHtml);

                $('.firstLine').html('【无界商圈】尊敬的会员'+'<span class="huiyuanming"></span>'+'：您好，“<span class="maker_address">'+maker.address+'</span>”将于“<span class="activity_begin_time">'+activity.begin_time+'</span>”举办“<span class="activity_subject">'+activity.subject+'</span>”活动，欢迎您报名参加，详情请查看“<a  class="activity_short_url" href="'+activity.short_url+'">url</a>”');
                $('.secondLine').html('活动地点：“'+maker.address+'”');
                $('.thirdLine').html('活动时间：“'+activity.begin_time+'”');
                $target.removeClass("hide");
                $target.css("visibility","visible");
            }
        });
    },
    /**通知会员 发送短信**/
    sendMessage:function(params){
        var url=url_prex+'maker/sendmessage';
        ajaxRequest(params,url,function(data){
   //         showMessage(data.message);
        });
    },
    /**获取本ovo里面报名活动的人**/
    getApplyusers:function(params){
        var url=url_prex+'maker/ajaxgetapplyusers';
        ajaxRequest(params,url,function(data){
            if(data.status){
                var html='';
                if(data.message.applyusers.length){
                    $.each(data.message.applyusers,function(i,obj){
                        html+='<tr>';
                        html+='<td>'+obj.nickname+'</td>';
                        html+='<td>'+obj.username+'</td>';
                        html+='<td>'+obj.apply_time+'</td>';
                        html+='<td>'+(obj.is_check>0?'是':'否')+'</td>';
                        html+='<td>'+obj.sign_time+'</td>';
                        html+='</tr>';
                    });
                }
                $('#tbody').html(html);
                $('.pagecontrol').html(data.message.pageHtml).find('a').each(function(){
                    var self=$(this),page=self.attr('href')?self.attr('href').match(/page=(\d+)/):0;
                    if(page){
                        page=page[1];
                    }else{
                        page=1;
                    }
                    self.attr('href','javascript:void(0)');
                    self.click(function(){
                        params.page=page;
                        myovo.getApplyusers(params);
                    });
                });
            }else{
                showMessage(data.message);
            }
        });
    },
    /****参与的活动列表***/
    getapplylist:function(params){
        var url=url_prex+'maker/ajaxgetusertickets';
        ajaxRequest(params,url,function(data){
            if(data.status){
                var users=data.message.users;
                var pageHtml=data.message.pageHtml;
                var usersHtml='';
                if(users.length){
                    $.each(users,function(i,user){
                        usersHtml+='<tr>';
                        usersHtml+='<td>'+user.created_at+'</td>' ;
                        usersHtml+='<td>报名-'+user.subject+''+(user.price!='免费'?'收费':'')+'活动</td>';
                        usersHtml+='<td>'+user.price+'</td>';
                        usersHtml+='</tr>';
                    });
                }
                $('#tbody').html(usersHtml);
                $('#pageControl').html(pageHtml);
            }else{
                showMessage(data.message);
            }
        });
    }
});