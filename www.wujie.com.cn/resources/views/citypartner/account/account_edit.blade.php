@extends('citypartner.layouts.layout')
@section('title')
    <title>账户管理-个人资料编辑</title>
@stop
        @section('styles')
            <link rel="stylesheet" type="text/css" href="/css/citypartner/share.css"/>
            <link rel="stylesheet" type="text/css" href="/css/citypartner/basic.css"/>
            <link rel="stylesheet" type="text/css" href="/css/citypartner/account.css"/>
            <link rel="stylesheet" href="/css/citypartner/jquery.Jcrop.min.css" type="text/css" />
        @stop
@section('content')
<div class="modal" id="modal" >
    <div class="overlay"></div>
    <div class="change">
        <h3>修改头像</h3>
        <a id="head_close" href="javascript:void(0)" >×</a>
        <div class="photo">
            <div class="wrap" id="jcropdiv">
                <div class="wl">
                    <div class="jc-demo-box" data="0">
                        <div id="target" class="jcrop_w">
                            <img id='avatar' name="avatar" width="170px" height="170px" src="{{getImage($partner->avatar,'avatar','')}}"/>
                        </div>
                    </div>
                </div>
                <div class="wr" id="preview-pane">
                    <h4>头像预览：</h4>
                    <div class="preview-container">
                        <div class="pre-1 big">
                            <img  name="avatar" class="jcrop-preview jcrop_preview_s"  src="{{ getImage($partner->avatar,'avatar','') }}"   alt="头像预览-大"/>
                        </div>
                        <div class="pre-2 small">
                            <img  name="avatar"  class="jcrop-preview jcrop_preview_s" src="{{ getImage($partner->avatar,'avatar','') }}"   alt="头像预览—小"/>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <p>拖拽或者缩放图中的虚线方格可调整头像，注意右侧小头像浏览效果</p>
        <p>图像大小小于5MB，建议上传正方形图片，支持JPG,JPEG,PNG,GIF格式</p>
        <form action="" style="display: none" id="tupian_load">
            <input type="text" id="x1" name="cut_x"/>X1
            <input type="text" id="y1" name="cut_y"/>Y1
            <input type="text" id="x2" name="x2"/>X2
            <input type="text" id="y2" name="y2"/>Y2
            <input type="text" id="w" name="cut_width"/>W
            <input type="text" id="h" name="cut_height"/>H
            <input type="hidden" value="" name="path" id="cut-path">
        </form>
        <form id="ajax-file" action="{{url('citypartner/upload/index') }}" enctype="multipart/form-data" method="POST">
            <div class="upload">
                <div style="position: relative;">
                    <a href="javascript:void(0);" id="uploadAvatarBtn" style="color: white">上传头像</a>
                    <input type="file" id="uploadAvatar" style="position: absolute;left: 0px;top: 0px;width: 100%;height: 100%;display: inline-block;background-color: transparent;opacity: 0;filter:alpha(opacity=0);cursor: pointer;" name="myfile" size="10" onchange="fileUpload('uploadAvatar','avatar','170:170')"/>
                </div>
            </div>
            <a href="#" type="button" id="btnAvatar">确定</a>
        </form>
    </div>
</div>
    <div class="container">
            <div class="font">
                <h2>
                    账号管理
                </h2>
                <a href="/citypartner/account/password?uid={{ $partner->uid }}" >修改密码</a>
            </div>
            <form class="intro" action="edit" id="editForm">
                <!--<div class="close"><a href="">×</a></div>-->
                <div class="person">
                    <div class="left">个人资料</div>
                    <div class="right">
                        <p><label for="">头像</label>
                            <input type="hidden" name="uid" value="{{$partner->uid}}">
                            <input type="hidden" name="avatar" value="{{$partner->avatar ? $partner->avatar: '' }}" id="user-avatar"/>
                            <img id="what" src="{{getImage($partner->avatar,'avatar','')}}" alt="头像"/>
                            <a href="javascript:void(0)" id="change_head">修改头像</a>
                        </p>
                        <p><label for="">姓名</label><input type="text" name="realname" value="{{ $partner->realname }}"></p>
                        <p>
                            <label for="">地址</label>
                            <select  id="city">
                                <option value="">省份</option>
                                @foreach($zoneTree as $zone)
                                    <option value="{{$zone['id']}}" @if(isset($family[1]) && $family[1]['id'] == $zone['id']) selected="selected" @endif>{{$zone['name']}}</option>
                                @endforeach
                            </select>
                            <select name="zone_id" id="zone">
                                <option value="">城市</option>
                                @if(isset($family[1]) && $family[1]['id'] )
                                    @foreach(App\Models\Zone\Entity::where(['upid'=>$family[1]['id'],'status'=>'1'])->where('upid','>','0')->get(['name','id']) as $zone)
                                        <option value="{{$zone->id}}" @if($zone->id ==$family[0]['id']) selected @endif>{{$zone->name}}</option>
                                    @endforeach
                                @endif
                            </select>
                        </p>
                        <p><label for="">手机号</label><span>{{ $partner->username }}</span> </p>
                        <p><label for="">我的邀请码</label><span>{{ $partner->invite_token }}</span> </p>
                        <p><label for="">领导人姓名</label><span>{{ isset($partner->pPartner) ? $partner->pPartner->realname:'' }}</span> </p>
                        <p><label for="">邮箱</label><input type="text" name="email" value="{{ $partner->email }}"> </p>
                    </div>
                </div>
                <div class="blank">
                    <div class="left">银行账户</div>
                    <div class="right">
                        <p><label for="">银行卡账户</label><input type="text" name="bank_account" value="{{$partner->bank_account}}"></p>
                        <p><label for="">银行</label><input type="text" name="bank" value="{{$partner->bank}}"></p>
                        <p><label for="">开户行</label><input type="text" name="deposit_bank" value="{{$partner->deposit_bank}}"></p>
                        <p><label for="">持卡人姓名</label><input type="text" name="cardholder_name" value="{{$partner->cardholder_name}}"></p>
                        <p><label for="">持卡人身份证</label><input type="text" name="idcard" value="{{$partner->idcard}}"></p>
                    </div>
                </div>
                <div class="edit">
                	<input type="hidden" name="formType" value="editAccount">
                	<a href="/citypartner/account/list?uid={{ $partner->uid }}" type="button">取消编辑</a>
                	<a href="javascript:void(0)" type="button" id="btnEdit">保存修改</a>
                </div>
            </form>
        <div class="prompt psd_4 hide" id="error">
            <p id="info">新密码中包含非法字符</p>
            <p>请重新修改！</p>
        </div>
        </div>
    @stop
@section('scripts')
    <script type="text/javascript" src="/js/citypartner/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" src="/js/citypartner/basic.js"></script>
    <script type="text/javascript" src="/js/citypartner/jquery.jcrop.js"></script>
    <script type="text/javascript" src="/js/citypartner/change_head.js"></script>
    <script type="text/javascript" src="/js/citypartner/common.js"></script>
    <script type="text/javascript" src="/js/citypartner/ajaxfileupload.js"></script>
    <script>var uploadUrl = "{{ url('citypartner/upload/index') }}"</script>
    <script>
        $("#city").change(function() {
            $("#zone").empty();
            $.post("{{url('citypartner/zone/children')}}", {
                id: $(this).val(),
                _token: $('input[name="_token"]').val()
            }, function(data) {
                if (data.status) {
                    var html = '<option value="">城市</option>';
                    $.each(data.message, function(i, n) {
                        html += "<option value='"+n.id+"'>"+n.name+"</option>";
                    });
                    $("#zone").html(html);
                }
            }, 'json');
        });
        $("#btnEdit").click(function(){
            ajaxRequest($('#editForm').serializeArray(),$('#editForm').attr('action'),function(data){
                if(data.status){
                    location.href = data.forwardUrl;
                    return ;
                }
                $('#info').html(data.message);
                $('#error').css('display','block');
                setTimeout('$("#error").hide("slow")',2000);
                $('#img_captcha').trigger('click');
            });
        });
        $(".head_close").click(function(){
            $("#modal").hide();
            $("body").css({overflow:"visible",padding:0})
        });
                  
        $("#btnAvatar").click(function(){
            var data={};
            $('#tupian_load input').each(function(k,v){
                data[v.name]=v.value;
            });
            if(!data.path){
                $("#modal").hide();
                $("body").css({overflow:"visible",padding:0});
                return alert('未选择图片无法剪切');
            }
            ajaxRequest(data,'/citypartner/upload/cut',function(json){
                if(json.status){
    //                var path = $("#avatar").attr('src');
                    $("#what").attr('src','/'+json.message+'?'+Math.random());
                    $('#user-avatar').val(json.message);
                    $("#modal").hide();
            $("body").css({overflow:"visible",padding:0});
                }else{
                    alert(json.message);
                }
            });
        });
//        $(function(){
//            $(".jcrop_w>img").css({top:'0px','left':'0px'});
//        })
    </script>

@stop
