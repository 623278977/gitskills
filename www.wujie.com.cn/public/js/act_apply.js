/**
 * Created by wangkq on 2016/10/08.
 * 报名购买门票（主要针对面免费票）
 */
Zepto(function () {
    var param = {
        "id":act_id
    };
    var actTicket = {
        getList: function (id) {
            var param = {};
            param["id"] = id;
            var url = labUser.api_path + '/activity/tickets';
            // var url = 'http://wjsq3.local/api' + '/activity/tickets';
            ajaxRequest(param, url, function (data) {
                if (data.status) {
                    //html调整
                    var html = actTicket.setResult(data.message);
                    $('#ticketList').html(html);
                }
            })
        },
        setResult: function (result) {
            var setHtml='';
            var typeArray=['免费票','现场票','直播票','vip票'];
            var colorArray=['bd-green','bd-orange','bd-orange','bd-blue'];
            var str1='', str2='';
            str1=[
                '<div class="ticket-live">',
                '<span class="wujie_icon"></span>直播票</div>',
                ].join('');
            str2= ['<div class="ticket-live">',
                '<span class="address_icon2"></span>',
                '现场票',
                '</div>'].join('');
            $.each(result, function (index,item) {
                if(item.status=='1'){
                    if (item.type==2) {
                        items(item);
                        setHtml=str1+setHtml;
                    }else{
                        setHtml += str2;
                        items(item);
                    }
                    if(item.num=='0'){
                        var typeNum=item.type;
                        //无票,显示已售完
                        setHtml+='<div class="code-box">';
                        setHtml+='<section class="section-box tc relative">';
                        setHtml+='<div class="tc-box tc c">';
                        setHtml+='<div class="tc-border bd-gray">';
                        setHtml+='<div class="left">';
                        setHtml+='<span class=" f16">'+typeArray[typeNum]+'</span>';
                        setHtml+='<p class="f12">'+item.intro+'</p>';
                        setHtml+='</div>';
                        setHtml+='<div class="right f14"><div class="buy"><em class="f18">已售完</em></div></div>';
                        setHtml+='</div></div></section></div>';
                    }else if(item.num==1){
                         var typeNum=item.type;
                        //活动过期,显示已截止
                        setHtml+='<div class="code-box">';
                        setHtml+='<section class="section-box tc relative">';
                        setHtml+='<div class="tc-box tc c">';
                        setHtml+='<div class="tc-border bd-gray">';
                        setHtml+='<div class="left">';
                        setHtml+='<span class=" f16">'+typeArray[typeNum]+'</span>';
                        setHtml+='<p class="f12">'+item.intro+'</p>';
                        setHtml+='</div>';
                        setHtml+='<div class="right f14"><div class="buy"><em class="f18">已截止</em></div></div>';
                        setHtml+='</div></div></section></div>';
                    }
                    // else{
                    // }                   
                }
                function items(item) {
                    var typeNum=item.type;

                    setHtml+='<div class="code-box">';
                    setHtml+='<section class="section-box tc relative">';
                    if (item.price==0) {
                        setHtml+='<a href="'+labUser.path+'webapp/activity/freecheck?id='+item.activity_id+'&ticket_id='+item.id+'">';
                        setHtml+='<div class="tc-box tc c">';
                        setHtml+='<div class="tc-border '+colorArray[0]+'">';
                        setHtml+='<div class="left">';
                        setHtml+='<span class=" f16">'+typeArray[0]+'</span>';                    
                        setHtml+='<p class="f12">'+item.remark+'</p>';
                        setHtml+='</div>';
                        setHtml+='<div class="right f14"><div class="buy"><em class="f18">免费</em><p>购票</p></div></div>';
                        setHtml+='</div></div></a></section></div>';
                    }else{
                        setHtml+='<a onclick='+"alert('直播票及其他产生费用的现场票请登录无界商圈应用端进行购买')"+'>';
                        setHtml+='<div class="tc-box tc-box2 tc c" >';
                        setHtml+='<div class="tc-border '+colorArray[typeNum]+'">';
                        setHtml+='<div class="left">';
                        setHtml+='<span class=" f16" id="sss">'+typeArray[typeNum]+'</span>';
                        if (item.remark==null) {
                             setHtml+='<p class="f12"></p>';
                        }else{
                            setHtml+='<p class="f12">'+item.remark+'</p>';
                        }
                        setHtml+='</div>';
                        setHtml+='<div class="right f14"><div class="buy">¥<em class="f18">'+item.price+'</em><p>购票</p></div></div>';
                        setHtml+='</div></div></a></section></div>';
                    }     
                }
            });
            return setHtml;
        }

    }
    /*页面加载时调用*/
    actTicket.getList(param.id);  
   

});
   
