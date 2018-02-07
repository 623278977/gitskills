$('body').addClass('bgcolor');
new FastClick(document.body);
// FastClick.attach(document.body)
 var args = getQueryStringArgs();
    var uid = args['uid'] || 0,
        id = args['id'];
    var urlPath = window.location.href;
    var shareUrl = urlPath.indexOf('is_share') > 0 ? '&is_share=1' : '';

Zepto(function() {  
    var brandDetail = {
        //品牌的详情信息获取
        detail: function(id, uid) {
            var param = {};
            param['id'] = id;
            param['uid'] = uid;
            if(shareFlag){
                param['type'] = 'html5'
            }else{
                param['type'] = "app";
            }
            param['is_send_cloud_info'] = '0';//表示是否给经纪人发消息
            var code = $('#brand_name').attr('data-code');
            var url = labUser.api_path + '/brand/detail/_v020900';
            ajaxRequest(param, url, function(data) {
                if (data.status) {               
                    getMore(data.message);
                    getJoin(data.message);
                  $('#brand_detail').removeClass('none');
                }
            })
        }       
    };
     // 图文详情 项目介绍 图集
    function getMore(result) {
        // console.log(result);
        document.title = result.brand.name;
        $('.pic_text').html(result.brand.detail);
           //公司详情
        if ($('#brand_company').text()!=='') {
            $('.brand-company-h').removeClass('none');
        }
    }
    //加盟简介，优势，条件
    function getJoin(result) {
        var brand = result.brand;
        if (brand.league ==''||brand.league==undefined) {
            $('#brand_j_1').parent().addClass('none');
        }
        if (brand.advantage ==''||brand.advantage==undefined) {
            $('#brand_j_2').parent().addClass('none');
        }
        if (brand.prerequisite==''||brand.prerequisite==undefined) {
            $('#brand_j_3').parent().addClass('none');
        }
        $('#brand_j_1').html(brand.league);
        $('#brand_j_2').html(brand.advantage);
        $('#brand_j_3').html(brand.prerequisite);

    }
    brandDetail.detail(id,uid)
    
}); 