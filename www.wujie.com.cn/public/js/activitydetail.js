/**
 * Created by jizx on 2016/5/11.
 * function:活动详情
 */
Zepto(function () {
    var urlPath=window.location.href;
    var shareStr='is_share';
    var param = {
        "id": id,
        //"<?php echo $id;?>
        "uid": '32'
        ////labUser.uid
    };
    //分享页
    if(urlPath.indexOf(shareStr) > 0){
        $('#noShareBtn').addClass('none');
        $('#shareBtn').removeClass('none');
        //价格-->门票列表入口
        $(document).on('click','.wjb', function () {
            //提示app操作
        });
        $(document).on('click','.signup', function () {
            //提示app操作
        });
        //推荐活动
        $(document).on('click','.recommend-act', function () {
            var act_id=$(this).data('actid');
            window.location.href='http://wjsq3.local/webapp/activity/detail?is_share=1&id='+act_id;
        });
    }
    else{
        //价格-->门票列表入口
        $(document).on('click','.wjb', function () {
            var act_id = $('#act_name').data('act_id');
            window.location.href='http://wjsq3.local/webapp/ticket/act-ticket?id='+act_id;
        });
        //推荐活动
        $(document).on('click','.recommend-act', function () {
            var act_id=$(this).data('actid');
            window.location.href='http://wjsq3.local/webapp/activity/detail?id='+act_id;
        });
        //收藏、群聊、立即报名
        $(document).on('click','.collect', function () {

        });
        $(document).on('click','.chat', function () {

        });
        $(document).on('click','.signup', function () {
            var act_id = $('#act_name').data('act_id');
            window.location.href='http://wjsq3.local/webapp/ticket/act-ticket?id='+act_id;
        });
    }

    var activityDetail = {
        detail: function (id,uid) {
            var param = {};
            param["id"] = id;
            param["uid"] = uid;
            var url = 'http://wjsq3.local/api' + '/activity/detail';
            ajaxRequest(param, url, function (data) {
                if (data.status) {
                    //html调整
                    getActivityDetail(data.message);
                }
            })
        }
    }
    /*页面加载时调用*/
    activityDetail.detail(param.id,param.uid);
    /**展开收起**/
    $(".act_intro .up").click(function () {
        $(".act_address").css("height", "16.86rem");
        $(".up").hide();
        $(".down").show();
    });
    $(".act_intro .down").click(function () {
        $(".act_address").css("height", "auto");
        $(".up").show();
        $(".down").hide();
    });
    /*调整html*/
    function getActivityDetail(result){
        $('#bannerPic').attr('src',result.self.detail_img);//海报图
        $('#act_picsrc').attr('src',result.self.list_img);//小图
        $('#act_name').html(result.self.subject);//名称
        $('#act_name').data('act_id',result.self.id);//活动id
        $('#seenNum').html(result.self.view);//浏览量
        $('#storeNum').html(result.self.likes);//收藏

        /**时间判断**/
        var begin_time = unix_to_datetime(result.self.begin_time);//开始时间
        var end_time = unix_to_datetime(result.self.end_time);  //结束时间
        var begin_time_day = begin_time.substring(0,4);
        var end_time_day = end_time.substring(0,4);
        if( begin_time_day == end_time_day){
            end_time = end_time.slice(5);
        }
        $('#act_time').html(begin_time+' - '+end_time);
        /*价格判断*/
        var priceArray = (result.self.price).split('@');
        //定义sort的比较函数
        priceArray = priceArray.sort(function(a,b){
            return a-b;
        });
        if(priceArray[priceArray.length-1] == 0){
            $('#wjbNum').html('免费');//免费
        }
        else if(priceArray[priceArray.length-1] != 0 && priceArray[0] == priceArray[priceArray.length-1]){
            $('#wjbNum').html(priceArray[priceArray.length-1]+'元');//一档非免费
        }
        else if(priceArray[0]!= priceArray[priceArray.length-1]){
            $('#wjbNum').html(priceArray[0]+'~'+priceArray[priceArray.length-1]+'元');//多档
        }
        var cityArray=(result.self.city).split('@');
        var idsArray=(result.self.ids=='')?[]:(result.self.ids).split('@');
        var addressArray=(result.self.address).split('@');
        var nameArray=(result.self.name).split('@');
        var typeArray=(result.self.type).split('@');
        var htmlOVOaddress='';
        if(idsArray.length>0){
            $.each(nameArray, function (index,item) {
                htmlOVOaddress+='<dd class="address_list">';
                htmlOVOaddress+='<span class="address_icon"></span>';
                htmlOVOaddress+=' <div class="infor" data-address_id="'+idsArray[index]+'">';
                htmlOVOaddress+='<p>'+item+'</p>';
                htmlOVOaddress+='<p>'+addressArray[index]+'</p><span class="sj_icon"></span>';
                htmlOVOaddress+='</div>';
                htmlOVOaddress+='</dd>';
            });
        }

        $('#address_flag').empty();
        $('#address_flag').html(htmlOVOaddress);
        //发布者
        var publisherHtml='';
        publisherHtml+='<span class="img"><img src="'+result.self.avatar+'"/></span> <i class="author_name">'+result.self.nickname+'</i>发布<span class="sj_icon"></span>';
        $('#publisher').html(publisherHtml);
        $('#publisher').attr("value",result.self.pub_id);
        //视频详情描述
        $('#video_description').html(result.self.description);
        //推荐活动
        var recAct='';
        $.each(result.rec, function (index,item) {
            recAct+='<li data-actid="'+item.id+'" class="recommend-act"><img src="'+item.list_img+'" alt="actpic"><p>'+item.subject+'</p></li>';
        });
        $('#recommend_act').empty();
        $('#recommend_act').append(recAct);
        //更多详情 value=id,事件在commonjs里
        $('#actMoreDetail').attr('value',result.self.id);
        //更多活动
        $(document).on('click','#act_list', function () {
            //跳转到活动列表
        });
        //结束报名,当前时间戳大于结束时间戳
        var timestamp=Math.round(new Date().getTime()/1000);
        if(timestamp > result.self.end_time){
            $('.signup').attr('disabled','disabled').css('background-color','#ccc').text('活动已结束，不能报名');
        }
        else{
            $('.signup').removeAttr('disabled');
        }

        $('#act_container').removeClass('none');
    }

})