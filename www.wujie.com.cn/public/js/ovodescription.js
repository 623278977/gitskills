/**
 * Created by jizx on 2016/5/17.
 * OVO中心介绍
 */
Zepto(function () {
    var param = {
        "maker_id": ovoID,
        "uid": 32
        //labUser.uid
    };
    var ovoDescript={
        getDetail:function(param){
            var url = 'http://wjsq3.local/api' + '/maker/detail';
            ajaxRequest(param, url, function (data) {
                if (data.status) {
                    ovoDescript.setContent(data.message);
                    $('#container').removeClass('none');
                }
                else {

                }
            });
        },
        setContent: function (content) {
            $('#logoPic').attr('src',content.logo);
            $('#ovoName').text(content.subject);
            $('#descriptionDiv').empty().append(content.description);
        }
    };
    ovoDescript.getDetail(param);
});