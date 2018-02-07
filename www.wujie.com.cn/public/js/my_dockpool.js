/**
 * Created by jizx on 2016/5/13.
 * 页面：对接申请池
 */
Zepto(function () {
    var pageNow = 1,
        pageSize = 10,
        uid = getUid,
        swipeUp = false;//上划标志;
    var param = {
        "uid": uid,
        "page": pageNow,
        "page_size": pageSize
    };
    var dockPool = {
        getList: function (param) {
            var url = labUser.path + '/userapply/index';
            ajaxRequest(param, url, function (data) {
                if (data.status) {
                    //获取某一页对接池列表html
                    var returnHtml = getDockList(data.message);
                    $('#listContainer').append(returnHtml);
                    $('#dockpoolSection').removeClass('none');
                }
                else {
                    if (swipeUp) {
                        pageNow--;
                    }
                }
                swipeUp = false;
            });
        },
        sendRemind: function (param, obj) {
            var url = labUser.path + '/userapply/remindreply';
            ajaxRequest(param, url, function (data) {
                if (data.status) {
                    $(obj).removeClass('green_bt').addClass('gray_bt').attr('disabled', 'disabled').text('已提醒');
                }
                else {
                    console.log('myDock-sendRemind-failed'+ new Date());
                }
            });
        }
    }
    /*页面加载时调用*/
    dockPool.getList(param);
    function getDockList(result) {
        var html = '';
        $.each(result, function (index, item) {
            html += '<div class="item_list">';
            html += '<div class="intro_detail_plr">';
            html += '<table class="dock_table">';
            html += '<tr><td class="width-20"><span>需求类型：</span></td><td>' + item.type + '</td></tr>';//需求类型
            html += '<tr><td><span>招商信息：</span></td><td>' + item.investinfo + '</td></tr>';
            html += '<tr><td colspan="2"><div class="border-b"></div></td></tr>';//分割线
            html += '<tr><td><span>申请人：</span></td><td>' + item.realname + '</td></tr>';
            html += '<tr><td><span>OVO中心：</span></td><td>' + item.subject + '</td></tr>';
            html += '<tr><td><span>公司名称：</span></td><td>' + item.company_name + '</td></tr>';
            html += '<tr><td><span>具体描述：</span></td><td>' + item.content + '</td></tr>';
            html += '<tr><td><span>提交时间：</span></td><td>' + item.created_at + '</td></tr>';
            html += '<tr><td colspan="2"><div class="border-b"></div></td></tr>';//分割线
            html += '<tr><td><img src="' + item.logopath + '" alt="logo"></td><td rowspan="2">' + item.remark + '</td></tr>';//头像，答复
            html += '<tr><td class="blue">' + item.nickname + '</td></tr>';//名字
            if (item.can_send == '1' && item.status == '1') {
                //发送提醒
                html += '<tr><td colspan="2" class="btpd"><button class="fr green_bt width-12" data-itemid="' + item.id + '">' + item.remind + '</button></td></tr>';
            }
            else if (item.can_send == '0' && item.status == '3') {
                //已经处理给出回复
                html += '<tr><td class="btpd"></td><td class="tr">' + item.updated_at + '</td></tr>';
            }
            else if (item.can_send == '0' && item.status == '2') {
                //已经提醒，处理中
                html += '<tr><td colspan="2" class="btpd"><button class="fr gray_bt width-12" data-itemid="' + item.id + '" disabled>' + item.remind + '</button></td></tr>';
            }
            html += '</table>';
            html += '</div>';
            html += '</div>';
        });
        return html;
    }

    //发送提醒
    $(document).on('click', 'button', function () {
        var itemid = $(this).data('itemid');
        var param = {
            "uid": uid,
            "id": itemid
        };
        dockPool.sendRemind(param, $(this));
    });
    //上划加载更多
    $(document).on('swipeUp', '#listContainer', function () {
        swipeUp = true;
        pageNow++;
        var param = {
            "uid": uid,
            "page": pageNow,
            "page_size": pageSize
        };
        dockPool.getList(param);
    });

});