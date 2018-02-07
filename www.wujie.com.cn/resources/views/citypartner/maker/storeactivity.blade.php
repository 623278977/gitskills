@extends('citypartner.layouts.layout')

@section('styles')
<link rel="stylesheet" href="/css/citypartner/share.css"/>
<link rel="stylesheet" href="/css/citypartner/basic1.css"/>
<link rel="stylesheet" href="/css/citypartner/create.css"/>
<link rel="stylesheet" href="/css/jquery.Jcrop.min.css" />
<link rel="stylesheet" href="/js/kindeditor/themes/default/default.css"/>
@stop

@section('title')
<title>创建本地活动</title>
@stop

@section('content')
<!--预览海报弹窗-->
<div class="overlay hide"></div>
<div class="modal hide">
    <h2> 上传图片</h2>
    <a href="javascript:void(0)" class="close" onclick="hide()">×</a>
    <div class="jc-demo-box" data="0">
        <div id="target" class="jcrop_w" >
            <img id="preview" src="/images/citypartner/img/haibao_up.png"/>
        </div>
    </div>
    <!--坐标-->
    <form action="" style="display:none;" id="tupian_load">
        <input type="text" id="x1" name="cut_x"/>X1
        <input type="text" id="y1" name="cut_y"/>Y1
        <input type="text" id="x2" name="x2"/>X2
        <input type="text" id="y2" name="y2"/>Y2
        <input type="text" id="w" name="cut_width"/>W
        <input type="text" id="h" name="cut_height"/>H
        <input type="hidden" value="" name="path" id="cut-path">
    </form>
    <div class="choose">
        <div style="position: relative;float: left;">
            <a href="#" onclick="$('#re_doc').click();" class="re_upload" >重新上传</a>
            <input type="file" id="upload-file" name="myfile" onchange="fileUpload('upload-file');" style="position: absolute;left: 0px;top: 0px;width: 100%;height: 100%;display: inline-block;background-color: transparent;opacity: 0;filter:alpha(opacity=0);cursor: pointer;"/>
        </div>
        <div>
            <a href="javascript:void(0)" class="up_pic" id="btnAvatar">上传图片</a>
            <a href="javascript:void(0)" onclick="hide()">取消</a>
        </div>
    </div>
</div>
<div class="container">
    <div class="font">
        <h2>
            创建本地活动
        </h2>
    </div>
    <form class="main">
        <div>
            <label for=""><span style="color: red;margin-right: 3px;">*</span>活动海报</label>
            <div class="right">
                <div class="picture">
                    <img src="" width="340px" height="190px" name="photo" alt="" id="activity-image" style="background-color:#f0f1f3;"/>
                    <input type="hidden" value="" name="list_img"/>
                    <p>支持JPG/PNG/JPGE 大小建议340*190或比例340:190</p>
                </div>
                <div class="upload">
                    <p><a href="javascript:;" class="unpre">没有准备活动海报？</a></p>
                    <p style="position: relative;">
                        <button type="button" onclick="show();">浏览文件</button>
                        <input type="file" id="upload-file-first" name="myfile" onchange="fileUpload('upload-file-first',true);" style="position: absolute;left: 0px;top: 0px;width: 100%;height: 100%;display: inline-block;background-color: transparent;opacity: 0;filter:alpha(opacity=0);cursor: pointer;"/>
                    </p>
                </div>
            </div>
        </div>
        <div>
            <p><label for=""><span style="color: red;margin-right: 3px;">*</span>活动名称</label><input type="text" name="subject"/></p> 
            <p><label for=""><span style="color: red;margin-right: 3px;">*</span>开始时间</label><input type="text" name="begin_time" id="d4311" onFocus="WdatePicker({lang: 'zh-cn', dateFmt: 'yyyy-MM-dd HH:mm', maxDate: '#F{$dp.$D(\'d4312\')}', minDate: '%y-%M-{%d+1}'})"/></p>
            <p><label for=""><span style="color: red;margin-right: 3px;">*</span>结束时间</label><input type="text" name="end_time" id="d4312"   onFocus="WdatePicker({lang: 'zh-cn', dateFmt: 'yyyy-MM-dd HH:mm', minDate: '#F{$dp.$D(\'d4311\')}'})"/></p>
            <p><label for=""><span style="color: red;margin-right: 3px;">*</span>活动详情</label><textarea name="description"   id="editor_id"></textarea></p>
            <p><label for=""><span style="color: red;margin-right: 3px;">*</span>人数说明</label><input type="text" name="num" id="person"/><input id="add_person" type="button" onclick="addPer()"><span>人</span><input id="down_person" type="button" onclick="downPer()"></p>
            <p><label for="" style="padding-left:9px;">门票费用</label><input type="text" name="price" id="ticket"/><input id="add_ticket" type="button" onclick="addTic()"><span>元</span><input id="down_ticket" type="button" onclick="downTic()"></p>
            <p><label for="" style="padding-left:9px;">门票说明</label><textarea name="intro" id="" cols="30" rows="10"></textarea></p>
            <p><label for="" style="padding-left:9px;">其他备注</label><textarea name="remark" id="" cols="30" rows="10"></textarea></p>
            <input type="hidden" name="partner_uid" value="{{\Illuminate\Support\Facades\Auth::id()}}"/>
        </div>
        <a href="javascript:void(0);" class="createActivity">保存修改</a>
        <button type="button" onclick="location.href = '/citypartner/maker/index'" style="cursor: pointer;">取消</button>
    </form>
     <div class="prompt  hide" id="error">
          <p>*号处不能为空!</p>
 	</div>
 	 <div class="prompt  hide" id="success">
          <p> 活动创建成功！</p>
 	</div>
</div>

<div class="w-upimg hide">
    <h2>上传图片 <a class="close"  style="float:right;font-size:20px;" href="#">×</a></h2>
    <div class="box">
        <div class="box-img">
            <input type="checkbox" name="poster" >
            <img src="/images/citypartner/poster/wn.png" alt=""/>
        </div>
        <div class="box-img">
            <input type="checkbox" name="" >
            <img src="/images/citypartner/poster/wn1.png" alt="">
        </div>
        <div class="box-img">
            <input type="checkbox" name="" >
            <img src="/images/citypartner/poster/cj.png" alt="">
        </div>
        <div class="box-img">
            <input type="checkbox" name="" >
            <img src="/images/citypartner/poster/cy1.png" alt="">
        </div>
        <div class="box-img">
            <input type="checkbox" name="" >
            <img src="/images/citypartner/poster/hd1.png" alt="">
        </div>
        <div class="box-img">
            <input type="checkbox" name="" >
            <img src="/images/citypartner/poster/hd2.png" alt="">
        </div>
        <div class="box-img">
            <input type="checkbox" name="" >
            <img src="/images/citypartner/poster/jypx1.png" alt="">
        </div>
        <div class="box-img">
            <input type="checkbox" name="" >
            <img src="/images/citypartner/poster/jypx2.png" alt="">
        </div>
        <div class="box-img">
            <input type="checkbox" name="" >
            <img src="/images/citypartner/poster/kj1.png" alt="">
        </div>
        <div class="box-img">
            <input type="checkbox" name="" >
            <img src="/images/citypartner/poster/kj2.png" alt="">
        </div>
        <div class="box-img">
            <input type="checkbox" name="" >
            <img src="/images/citypartner/poster/kj3.png" alt="">
        </div>
        <div class="box-img">
            <input type="checkbox" name="" >
            <img src="/images/citypartner/poster/sw1.png" alt="">
        </div>
        <div class="box-img">
            <input type="checkbox" name="" >
            <img src="/images/citypartner/poster/sw2.png" alt="">
        </div>
        <div class="box-img">
            <input type="checkbox" name="" >
            <img src="/images/citypartner/poster/sw3.png" alt="">
        </div>
        <div class="box-img">
            <input type="checkbox" name="" >
            <img src="/images/citypartner/poster/yl.png" alt="">
        </div>
        <div class="box-img">
            <input type="checkbox" name="" >
            <img src="/images/citypartner/poster/yl1.png" alt="">
        </div>
    </div>
    <div class="buttons">
        <button id="up_load">上传图片</button>
        <a href="#" class="close" style="margin-top: 5px;font-size:14px;">取消</a>
    </div>
</div>
@stop


@section('scripts')
<script type="text/javascript" src="{{URL::asset('js/')}}/citypartner/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="/js/citypartner/common.js"></script>
<script type="text/javascript" src="{{URL::asset('js/')}}/citypartner/jquery.jcrop.js"></script>
<script type="text/javascript" src="{{URL::asset('js/')}}/citypartner/basic1.js"></script>
<script type="text/javascript" src="{{URL::asset('js/')}}/citypartner/ajaxfileupload.js"></script>
<script type="text/javascript" src="{{URL::asset('js/')}}/citypartner/myovo.js"></script>
<script type="text/javascript" src="{{URL::asset('js/')}}/citypartner/WdatePicker.js"></script>
<script charset="utf-8" src="{{URL::asset('js/')}}/kindeditor/kindeditor-all.js"></script>
<script charset="utf-8" src="{{URL::asset('js/')}}/kindeditor/lang/zh-CN.js"></script>
<script>
    $(function(){
        $('#target').Jcrop({
            aspectRatio: 340/190,
            allowSelect: false,
            minSize: [340,190],
            setSelect: [0,0,340,190]
        });
    });

            KindEditor.ready(function (K) {
                window.editor = K.create('#editor_id', {
                    afterBlur: function () {
                        this.sync();
                    }
                })
            });

            $(function () {
                $(".my-text").focus(function () {
                    $(this).css("border", "1px solid #b2dffd");
                }).blur(function () {
                    $(this).css('border', '1px solid #eee')
                });
            });
            function show() {
                $('#preview').css({'top': '0px', 'left': '0px', 'width': 'auto', 'height': 'auto'});
                $(".overlay").removeClass("hide");
                $(".modal").removeClass("hide");
            }
            function hide() {
                $(".overlay").addClass("hide");
                $(".modal").addClass("hide");
            }
            ;
            $(function () {
                $("#person").keyup(function () {
                    if (isNaN($(this).val()) || parseInt($(this).val()) < 1) {
                        $(this).val("1");
                        return;
                    }
                });
                $("#ticket").keyup(function () {
                    if (isNaN($(this).val()) || parseInt($(this).val()) < 1) {
                        $(this).val("0");
                        return;
                    }
                });
                //上传海报把复选框变成单选
                $('.w-upimg').find('input[type=checkbox]').click(function () {
                    if ($(this).is(':checked')) {
                        $('.w-upimg').find('input[type=checkbox]').attr('checked', false);
                        $(this).attr('checked', true);
                    } else {
                        $('.w-upimg').find('input[type=checkbox]').attr('checked', false);
                        $(this).attr('checked', false);
                    }
                });
                $('a.unpre').click(function () {
                    $('.overlay').removeClass('hide');
                    $('.w-upimg').removeClass('hide');
                })
                $('a.close').click(function () {
                    $('.w-upimg').addClass('hide');
                    $('.overlay').addClass('hide');
                })
            });

            /*人数加一*/
            function addPer() {
                var num_add = parseInt($("#person").val()) + 1;
                if ($("#person").val() == "") {
                    num_add = 1;
                }
                $("#person").val(num_add);
            }
            ;
            /*人数减一*/
            function downPer() {
                var num_dec = parseInt($("#person").val()) - 1;
                if ($("#person").val() == "") {
                    num_dec = 1
                }
                if (num_dec < 1) {
                    //人数必须大于或等于1
    //                alert("人数不能小于1");
                } else {
                    $("#person").val(num_dec);
                }
            }
            ;
            /*票数加一*/
            function addTic() {
                var num_add = parseInt($("#ticket").val()) + 1;
                if ($("#ticket").val() == "") {
                    num_add = 1;
                }
                $("#ticket").val(num_add);
            }
            ;
            /*票数减一*/
            function downTic() {
                var num_dec = parseInt($("#ticket").val()) - 1;
                if ($("#ticket").val() == "") {
                    num_dec = 0
                }
                if (num_dec < 1) {
                    //数量必须大于或等于1
//                    alert("票数不能小于1");
                    $("#ticket").val(0);
                } else {
                    $("#ticket").val(num_dec);
                }
            }
            ;
            var params = {};
            $('.createActivity').click(function () {
//                params.list_img = $('.picture img').attr('src').replace(/(\?.*)?/gi,'');
                params.list_img = $('input[name="list_img"]').val();
                params.subject = $('input[name="subject"]').val();
                params.begin_time = $('input[name="begin_time"]').val();
                params.end_time = $('input[name="end_time"]').val();
                params.description = $('textarea[name="description"]').val();
                params.num = $('input[name="num"]').val();
                params.price = $('input[name="price"]').val();
                params.intro = $('textarea[name="intro"]').val();
                params.remark = $('textarea[name="remark"]').val();
                params.partner_uid = $('input[name="partner_uid"]').val();
                if (!params.list_img) {
                	$("#error p").html("请上传活动海报!")
                	$("#error").css("display","block");
                	setTimeout('$("#error").hide("slow")',2000);
                    return false;
                }
                if (!params.subject) {
                	$("#error p").html("请填写活动名称 !")
                	$("#error").css("display","block");
                	setTimeout('$("#error").hide("slow")',2000);
                    return false;
                }
                if (!params.begin_time || !params.end_time) {
                	$("#error p").html("请选择开始和结束时间!")
                	$("#error").css("display","block");
                	setTimeout('$("#error").hide("slow")',2000);
                    return false;
                }
                if (!params.description) {
                	$("#error p").html("请填写活动详情 !")
                	$("#error").css("display","block");
                	setTimeout('$("#error").hide("slow")',2000);
                    return false;
                }
                if (!params.num) {
                	$("#error p").html("请设置活动人数!")
                	$("#error").css("display","block");
                	setTimeout('$("#error").hide("slow")',2000);
                    return false;
                }
                myovo.createActivity(params);
            });
            fileUpload = function (fileId, hidden) {
//            var data={};
//            $('#tupian_load input').each(function(k,v){
//                data[v.name]=v.value;
//            });
                $.ajaxFileUpload({
                    data:{width:700,height:380},
                    url: '/citypartner/upload/index',
                    secureuri: false, // 是否启用安全提交
                    dataType: 'json', // 数据类型
                    fileElementId: fileId, // 表示文件域ID
                    // 提交成功后处理函数 html为返回值，status为执行的状态
                    success: function (json, status) {
                        if (json.status) {
                            show();
                            $('#preview').attr('src', json.message.url);
                            $('#cut-path').val(json.message.path);
                            if (hidden) {
                                $('#' + fileId).css('display', 'none');
                            }
                        } else {
                            alert(json.message);
                        }
                    },
                    // 提交失败处理函数
                    error: function (html, status, e) {
                        console.log(html);
                    }
                })
            };
            $("#btnAvatar").click(function () {
                var data = {};
                $('#tupian_load input').each(function (k, v) {
                    data[v.name] = v.value;
                });
                ajaxRequest(data, '/citypartner/upload/cut', function (json) {
                    if (json.status) {
                        $("#activity-image").attr('src', '/' + json.message+'?'+Math.random());
                        $('input[name="list_img"]').val(json.message);
                        hide();
                    } else {
                        alert(json.message);
                    }
                });
            });
            $("#up_load").click(function () {
                var img_src = $(":checked").next().attr("src");//340*190
                ajaxRequest({cut_x:0,cut_y:0,cut_width:340,cut_height:190,path:img_src}, '/citypartner/upload/cut', function (json) {
                    if (json.status) {
                        $(".picture>img").attr("src", '/' + json.message+'?'+Math.random());
                        $('input[name="list_img"]').val(json.message);
                        $(".w-upimg").addClass("hide");
                        $('.overlay').addClass('hide');
                    } else {
                        alert(json.message);
                    }
                });
            });
</script>
@stop
