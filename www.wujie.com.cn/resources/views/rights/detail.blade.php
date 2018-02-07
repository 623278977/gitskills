@extends('layouts.default')
@section('css')
    <link href="{{URL::asset('/')}}/css/page_detail.css?v=1.0.0" rel="stylesheet" type="text/css"/>
   <!--  <link href="{{URL::asset('/')}}/css/government_detail.css?v=1.0.0" rel="stylesheet" type="text/css"/> -->

@stop
@section('main')
    <section id="act_container" class="">
        <section class="" id="rights-fix">
            <div class="massive bd0 pl1 relative" >
                <!-- <img src="" alt="">
                <div class="l">
                    <div class="act_name">活动名称活动名名称活动名称</div>
                    <div class="clearfix"></div>
                    <p class="dark_gray f12">视频简介视频简介</p>
                    <i class="icon icon_hd"></i> 活动:12场 <i class="icon icon_zb"></i> 直播：12场 <i class="icon icon_lb"></i> 录播：12场
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div> -->
            </div>
            <div class="massive-grey"></div>

        </section>
        <div class="rights-bottom"></div>
        <div id="zb-rights">
          
        </div>
            

    </section>
@stop

@section('endjs')
   <script>
   Zepto(function () {
        var urlPath = window.location.href,
            uid = labUser.uid;            
        var param = {
            "vip_id":{{$vip_id}},
            "uid": "<?php echo isset($user->uid) && $user->uid > 0 ? $user->uid : $uid;?>"

        };
        var rightsDetail = {
            detail: function (vip_id,uid) {
                var param = {};
                param["vip_id"] = vip_id;
                param["uid"] = uid;
                param["attach"] =1;
                param["agreement"]=1;
                var url = labUser.api_path + '/vip/detail';
                // var url = '/api/vip/detail';
                ajaxRequest(param, url, function (data) {
                    if (data.status) {
                        //html调整
                        getRightsDetail(data.message);
                        // console.log(data.message);
                    }
                });
            }
        };
        rightsDetail.detail(param.vip_id,param.uid);
        function getRightsDetail(result) {
            setPageTitle(result.name+"权益说明");
            var str='';
            str+=[
            '<img src="'+result.poster+'" alt="">',
            '<div class="l">',
                '<div class="act_name">'+result.name+'</div>',
                '<div class="clearfix"></div>',
                '<p class="dark_gray f12">'+result.subtitle+'</p>',
                '<div class="other-zb">',
                '<i class="icon icon_hd"></i> 活动:'+result.activity_count+'场&nbsp&nbsp&nbsp&nbsp;<i class="icon icon_zb"></i> 直播:'+result.live_count+'场&nbsp&nbsp&nbsp&nbsp;<i class="icon icon_lb"></i> 录播:'+result.video_count+'场',
                '</div>',
                '<div class="clearfix"></div>',
            '</div>',
           '<div class="clearfix"></div>'
            ].join('');
            $('.massive').append(str);
            $('#zb-rights').html(result.agreement);

           
        }
 
        // setPageTitle(String phoneTitle);
   })
   </script>
   <script>
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

   </script>
@stop