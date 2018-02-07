@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/j_pages.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section id="container" class="none">
        <div class="intro_detail_title intro_detail_ml13">
            <img src="{{URL::asset('/')}}/images/intro_detail.png" alt="logo" style="width: 5rem;height: 5rem;" id="logoPic">
            <span id="ovoName"></span>
        </div>
        <div class="intro_detail_content intro_detail_plr intro_detail_mt22" id="descriptionDiv">

        </div>
    </section>
@stop

@section('endjs')
    <script typr="text/javascript">
        Zepto(function () {
            var param = {
                "maker_id":{{$id}},
                "uid": labUser.uid
            };
            var ovoDescript={
                getDetail:function(param){
                    var url = labUser.api_path + '/maker/switchmaker';
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
        //分享
        function showShare() {
            var title = $('#ovoName').text();
            var url = window.location.href;
            var img = $('#logoPic').attr('src');
            var header = '';
            var content = cutString($('#descriptionDiv').text(), 18);
            shareOut(title, url, img, header, content);
        }
    </script>
@stop