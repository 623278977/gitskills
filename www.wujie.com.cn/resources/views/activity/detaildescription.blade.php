@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/j_pages.css" rel="stylesheet" type="text/css"/>
@stop
@section('main')
    <section id="actDetailContainer" class="none">
        <div class="intro_detail_plr ft_106 intro_detail_mt14" id="detailContent">

        </div>
    </section>
@stop

@section('endjs')
    <script type="text/javascript">
        Zepto(function () {
            var liveflag = (window.location.href).indexOf('live') > 0 ? true : false;
            var param = {
                "id":{{$id}},
                "uid":'0'
            };
            var getDescription = {
                actdesp: function (id, uid) {
                    var param = {};
                    param["id"] = id;
                    param["uid"] = uid;
                    var url = labUser.api_path + '/activity/detail';
                    ajaxRequest(param, url, function (data) {
                        if (data.status) {
                            //html调整
                            getActivityDetail(data.message);
                        }
                    });
                },
                livedesp:function(id,uid){
                    var param = {};
                    param["id"] = id;
                    param["uid"] = uid;
                    var url = labUser.api_path + '/live/detail';
                    ajaxRequest(param, url, function (data) {
                        if (data.status) {
                            //html调整
                            $('#detailContent').html(data.message.live.description);
                            $('#actDetailContainer').removeClass('none');
                        }
                    });
                }
            };
            if(liveflag){
                getDescription.livedesp(param.id, param.uid);
            }
            else{
                //activity & video
                getDescription.actdesp(param.id, param.uid);
            }
            function getActivityDetail(result) {
                $('#detailContent').html(result.self.description);
                $('#actDetailContainer').removeClass('none');
            }
        });
    </script>
@stop