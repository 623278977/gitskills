/**
 * Created by jizx on 2016/5/18.
 * 活动门票
 */
Zepto(function () {
    var param = {
        "id":act_id
    };
    var actTicket = {
        getList: function (id) {
            var param = {};
            param["id"] = id;
            var url = 'http://wjsq3.local/api' + '/activity/tickets';
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
            $.each(result, function (index,item) {
                if(item.status=='1'){
                    if(item.num=='0'){
                        var typeNum=item.type;
                        //无票,显示已截止
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
                    else{
                        var typeNum=item.type;
                        setHtml+='<div class="code-box">';
                        setHtml+='<section class="section-box tc relative">';
                        setHtml+='<a href="xxxxxx?id='+item.id+'&actid='+item.activity_id+'">';
                        setHtml+='<div class="tc-box tc c">';
                        setHtml+='<div class="tc-border '+colorArray[typeNum]+'">';
                        setHtml+='<div class="left">';
                        setHtml+='<span class=" f16">'+typeArray[typeNum]+'</span>';
                        setHtml+='<p class="f12">'+item.intro+'</p>';
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