/**
 * Created by jizx on 2016/5/13.
 */
Zepto(function () {
    var param = {
        "id": id,
        "uid": 32
    };
    var activityDetail = {
        detail: function (id, uid) {
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
    };
    /*页面加载时调用*/
    activityDetail.detail(param.id, param.uid);
    function getActivityDetail(result) {
        $('#detailContent').html(result.self.description);
        //结束报名,当前时间戳大于结束时间戳
        //var timestamp = Math.round(new Date().getTime() / 1000);
        //if (timestamp > result.self.end_time) {
        //    $('.signup').attr('disabled', 'disabled').css('background-color', '#ccc').text('报名结束');
        //}
        //else {
        //    $('.signup').removeAttr('disabled');
        //}
        $('#actDetailContainer').removeClass('none');
    }
});