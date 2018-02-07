@extends('layouts.default')
@section('css')
@stop
@section('main')
    <section class="pl1-33 pr1-33">
        <div class="f16 pt2 pb2 tc b">在线合同</div>
        <!-- <ul class="venturelist f14 mt1 pl1-5">
            <li>创业基金是无界商圈为有意向成为品牌加盟商的用户而提供的福利红包，此基金可直接在加盟某品牌时抵扣部分加盟费用；</li>
            <li>一个品牌的创业基金仅限用作加盟此品牌时抵扣，且不可拆分使用；</li>
        </ul> -->
        <div class="f14 pl1-33 pr1-33" id="treaty">
        	
        </div>
    </section>
@stop
@section('endjs')
<script>
	var args = getQueryStringArgs();
    var uid = args['uid'] || 0,
        id = args['id'];
    var urlPath = window.location.href;
    var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
    Zepto(function() {
    	function treaty(id, uid, type) {
            var param = {};
            param['id'] = id;
            param['uid'] = uid;
            param['type'] = type;
            var url = labUser.api_path + '/brand/detail/_v020500';
            ajaxRequest(param, url, function(data) {
                if (data.status) {
                	console.log('q');
                   $('#treaty').html(data.message.brand.treaty);
                }
            })
        };
        treaty(id,uid,'app');
    });
    // title
        function setPageTitle(title) {
            if (isAndroid) {
                javascript:myObject.setPageTitle(title);
            } 
            else if (isiOS) {
                var data = {
                   "title":title
                }
                window.webkit.messageHandlers.setPageTitle.postMessage(data);
            }
        }
        setPageTitle('线上合同');
   
</script>
@stop