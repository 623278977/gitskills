$(function () {


    //点击注册弹出遮罩层
    var reg = $('.register');
    reg.click(function () {
        bg();
        $(' .form-reg').removeClass('hide');
    });
    //登录窗口点击立即注册
    $('a#m-reg').click(function () {
        $('#formContainer').addClass('hide');
        $(' .form-reg').removeClass('hide');
        bg();
    });
    //注册窗口点击登录
    $('a#m-reg-log').click(function () {
        $('.form-reg').addClass('hide');
        $('#formContainer').removeClass('hide');

    })
    //点击首页立即登录弹出
    $('#btn-login').click(function () {
        bg();
        $('#formContainer').removeClass('hide').removeClass('flipped');
    });
    //首页忘记密码弹窗
    $('#m-forget').click(function () {
        bg();
        //$('#formContainer').removeClass('hide').addClass('flipped');
        $('#formContainer').removeClass('hide').removeClass('flipped');
        $('#login-suden').click();

    });
    //登录表单复选框checkbox
    $('#remNameSpan').click(function () {
        $(this).toggleClass('checkbox');
        if ($('#remName').val() == 0) {
            $('#remName').val(1);
        } else {
            $('#remName').val(0);
        }
    })
    $('#remPassSpan').click(function () {
        $(this).toggleClass('checkbox');
        if ($('#remPass').val() == 0) {
            $('#remPass').val(1);
        } else {
            $('#remPass').val(0);
        }
    })
    //onblur边框变色
    $('.m-form input').focus(function () {
        $(this).css('border', '1px solid #bcbfc2')
    }).blur(function () {
        $(this).css('border', '1px solid #28313b')
    });
    //关闭弹窗
    $('.close').click(function () {
        $('.m-forms,.bg').addClass('hide');
    });
    $('.close2').click(function () {
        location.href = "/citypartner/public/index";
        //$("#form-forget input[name='phone']").val('');
        //$("#form-forget input[name='code']").val('');
        //$('#form-forget span[name="code_error"]').html('');
        //$('#form-forget span[name="phone_error"]').html('');
        //$('#formContainer,.bg').addClass('hide');
    });
    //谷歌浏览器去除黄底
    $('.m-form input').attr('autocomplete', 'off');
    //回车键登录
    //$("#formLogin").bind('keyup',function(event) {
    //    if(event.keyCode==13){
    //        alert('dd');
    //        $('#formLogin').submit();
    //    }
    //});
    $("#formLogin input").keydown(function (e) {
        var e = e || event,
            keycode = e.which || e.keyCode;
        if (keycode == 13) {
            $("#dologin").trigger("click");
        }
    });


    //获取验证码
    var tt;
    var wait=60;
    function time(o) {
        if (wait == 0) {
            o.removeAttribute("disabled");
            o.value="重新发送";
            wait = 60;
        } else {
            o.setAttribute("disabled", true);
            o.value="重新发送(" + wait + ")";
            wait--;
            tt=setTimeout(function() {
                    time(o)
                },
                1000)
        }
    };

    var tt2;
    var wait2=60;
    function time2(o) {
        if (wait2 == 0) {
            o.removeAttribute("disabled");
            o.value="重新发送";
            wait2 = 60;
        } else {
            o.setAttribute("disabled", true);
            o.value="重新发送(" + wait2 + ")";
            wait2--;
            tt2=setTimeout(function() {
                    time2(o)
                },
                1000)
        }
    };


    $('.m-check3').click(function () {
        var $phone = $("#regphone");
        //console.log($phone);
        if ($phone.val().length != 11) {
            $('#inputCheck').text('请输入正确的手机号');
            setTimeout(function(){
                $('#inputCheck').text('');
            },3000);
            return false;
        }
        var that = this;
        var param = {};
        param['username'] = $("#formRegister input[name='phone']").val();
        if (!param['username']) {
            $('#inputCheck').text('手机号码不能为空');
            $(this).css({'background': '#2e3a45', 'border': '0'});
            return false;
        }
        param['type'] = "citypartner_register";
        $.ajax({
            type: 'post',
            url: '/citypartner/public/sendcode',
            data: param,
            cache: false,
            dataType: 'json',
            success: function (data) {
                if (data.status == true) {
                    clearTimeout(tt);
                    time(that);
                    $('#formRegister span[name="phone_error"]').text('');
                    $('#formRegister span[name="code_error"]').text('验证码已发送到您的手机，请勿泄露！');
                } else if (data.status == false && data.message[0] == 'phone_error') {
                    $('#formRegister span[name="phone_error"]').text(data.message[1]);
                } else {
                    $('#formRegister span[name="code_error"]').text('验证码发送失败！');
                }
                setTimeout(function () {
                    $('#formRegister span[name="phone_error"]').text('');
                    $('#formRegister span[name="code_error"]').text('');
                }, 5000);
                $(this).css({'background': '#2e3a45', 'border': '0'});
            }
        });
    });
    //关闭弹窗验证码清0
    $('.reg-close-resset').click(function(){
        clearTimeout(tt);
        $('.m-check3').val('发送验证码').removeAttr('disabled');
        wait=60;
    });

    $('#login-now').click(function(){
        //bg();
        $('.form-modify').addClass('hide');
        //window.location.reload();

        $('#formContainer').removeClass('hide').removeClass('flipped');
    });


    //鼠标经过邀请码悬浮窗
    $("a.a-tip-invite").hover(function () {
        $("#tip-invite").css("display", "block");
    }, function () {
        $("#tip-invite").css('display', 'none');
    });
    //服务条款
    $('a.afuwu').click(function(){
        $('.fuwu').removeClass('hide');
    });
    $('a.queren').click(function(){
        $('.fuwu').addClass('hide');
    })

    //个人资料上传头像照片
    $('.uphead').click(function () {
        $('.m-head').addClass('hide');
        $('.m-head-pic').removeClass('hide');
    });
    //省市联动插件
    //new PCAS("province","city","area","","","");


    //关于我们选项卡颜色获取


    function tabs() {
        var tabColor = ['#034f7c', '#075e92', '#0b6ba5', '#1674ad', '#1a82c1', '#2390d2', '#279adf', '#2aa2ea', '#2ba7f1', '#45baff'];
        var tabLi = $('.aboutUs ul li ');
        var tabDiv = $('.aboutUs .m-context  .box');
        for (var k = 0; k < tabLi.length; k++) {
            (function (a) {
                $($('.aboutUs ul li ')[a]).css('backgroundColor', tabColor[a]);
            }(k));
        }
        ;
        tabLi.click(function () {
            $(this).addClass('cur').siblings().removeClass('cur');
            var index = tabLi.index(this);
            tabDiv.eq(index).removeClass('hide').siblings().addClass('hide');
        });
    };
    tabs();


    /**
     * 第一步注册
     */
    $("#firstBtn").bind('click', function () {
        var param = {};
        param['phone'] = $("#formRegister input[name='phone']").val();
        param['code'] = $("#formRegister input[name='code']").val();
        param['leadername'] = $("#formRegister input[name='leadername']").val();
        param['invite'] = $("#formRegister input[name='invite']").val();
        param['password'] = $("#formRegister input[name='password']").val();
        param['confirmpassword'] = $("#formRegister input[name='confirmpassword']").val();
        param['act'] = $("#formRegister input[name='act']").val();
        $.ajax({
            type: 'post',
            url: '/citypartner/public/register',
            data: param,
            cache: false,
            dataType: 'json',
            success: function (data) {
                if (data.status == true) {
                    $("div[name='reg1']").addClass('hide');
                    $("div[name='reg2']").removeClass('hide');
                    $("div[name='reg3']").addClass('hide');
                    $("#formInfo").append("<input type='hidden' name='partner_id' value='" + data.message + "'>");
                } else {
                    $("#formRegister span[name='" + data.message[0] + "']").text(data.message[1]);
                    setTimeout(function () {
                        $("#formRegister span[name='" + data.message[0] + "']").text('');
                    }, 5000);
                }
            },
            error: function (data) {
                var errorinfo = '';
                var return_data = data.responseJSON ? data.responseJSON : eval('(' + data.responseText + ')');
                $.each(return_data, function (i, j) {
                    if (errorinfo == '') {
                        errorinfo = j[0];
                        $("#formRegister span[name='" + i + "_error']").text(errorinfo);
                        setTimeout(function () {
                            $("#formRegister span[name='" + i + "_error']").text('');
                        }, 5000);
                    }
                });
            }
        });
    });

    /**
     * 第二步注册
     */
    $("#secondBtn").bind('click', function () {
        var param = {};
        param['name'] = $("#formInfo input[name='name']").val();
        param['sex'] = $("#formInfo select[name='sex']").val();
        param['province'] = $("#formInfo select[name='province']").val();
        param['city'] = $("#formInfo select[name='city']").val();
        param['act'] = $("#formInfo input[name='action']").val();
        param['partner_id'] = $("#formInfo input[name='partner_id']").val();
        param['avatar'] = $("#formInfo input[name='avatar']").val();
        $.ajax({
            type: 'post',
            url: '/citypartner/public/register2',
            data: param,
            cache: false,
            dataType: 'json',
            success: function (data) {
                if (data.status == true) {
                    $("div[name='reg1']").addClass('hide');
                    $("div[name='reg2']").addClass('hide');
                    $("div[name='reg3']").removeClass('hide');
                    //3秒后跳转
                    setTimeout(function () {
                        location.href = "/citypartner/public/index?login=now";
                    }, 3000);
                } else {
                    $("#formInfo span[name='" + data.message[0] + "']").text(data.message[1]);
                    setTimeout(function () {
                        $("#formInfo span[name='" + data.message[0] + "']").text('');
                    }, 5000);
                }
            },
            error: function (data) {
                var errorinfo = '';
                var return_data = data.responseJSON ? data.responseJSON : eval('(' + data.responseText + ')');
                $.each(return_data, function (i, j) {
                    if (errorinfo == '') {
                        errorinfo = j[0];
                        $("#formInfo span[name='" + i + "_error']").text(errorinfo);
                        setTimeout(function () {
                            $("#formInfo span[name='" + i + "_error']").text('');
                        }, 5000);
                    }
                })
            }
        });
    });

    /**
     * 注册完成
     */
    $("#registerOk").bind('click', function () {
        location.href = "/citypartner/public/index?login=now";
    });

    /**
     * 加入我们
     */
    $("#join button[name='join']").bind('click', function () {
        var name = $("#join input[name='name']").val();
        var phone = $("#join input[name='phone']").val();
        var email = $("#join input[name='email']").val();
        $("div[name='join']").html('');
        if (name == "" || phone == "" || email == "" || $("#join textarea[name='message']").val() == ""){
            joinUs('<p class="tc mt50">抱歉！无法提交 <br>请您填写相关信息</p>',2000);
            return;
        }
        if (!checkPhone(phone)) {
            joinUs('<p class="tc mt50">联系电话格式有误!</p>',2000);
            return;
        }
        if (!checkEmail(email)) {
            joinUs('<p class="tc mt50">电子邮箱格式有误!</p>',2000);
            return;
        }
        joinUs('<p class="tc mt50">提交成功</p><p class="tc">感谢您的支持！</p>',3000);
        setTimeout(function () {
            $("#join input[name='name']").val('');
            $("#join input[name='phone']").val('');
            $("#join input[name='email']").val('');
            $("#join textarea[name='message']").val('');
        }, 3000);
    });

    //加入我们提示文字
    function joinUs(html,time){
        $("div[name='join']").html(html);
        $("div[name='join']").removeClass('hide');
        setTimeout(function () {
            $("div[name='join']").addClass('hide');
        }, time);
    }
    //了解我们
    $('.m-about-box ul li ').hover(function function_name() {
        $(this).siblings().css({'backgroundColor':'rgba(0,0,0,0.3)','filter' :'alpha(opacity=30)'});
    },function () {
        $(this).siblings().css({'backgroundColor':'rgba(0,0,0,0)','filter' :'alpha(opacity=0)'});
    });

    /**
     * 找回密码
     */
    $("#forgetPwd").bind('click', function () {
        var param = {};
        param['phone'] = $("#form-forget input[name='phone']").val();
        param['code'] = $("#form-forget input[name='code']").val();
        param['act'] = $("#form-forget input[name='act']").val();
        $.ajax({
            type: 'post',
            url: '/citypartner/public/forgetpwd',
            data: param,
            cache: false,
            dataType: 'json',
            success: function (data) {
                if (data.status == true) {
                    $("#formContainer").addClass('hide');
                    $("#resetdiv").removeClass('hide');
                    $("#resetOkdiv").addClass('hide');
                    $("#formModify").append("<input type='hidden' name='phone' value='" + param['phone'] + "'>");
                } else {
                    $("#form-forget span[name='" + data.message[0] + "']").text(data.message[1]);
                    setTimeout(function () {
                        $("#form-forget span[name='" + data.message[0] + "']").text('');
                    }, 5000);
                }
            },
            error: function (data) {
                var errorinfo = '';
                var return_data = data.responseJSON ? data.responseJSON : eval('(' + data.responseText + ')');
                $.each(return_data, function (i, j) {
                    if (errorinfo == '') {
                        errorinfo = j[0];
                        $("#form-forget span[name='" + i + "_error']").text(errorinfo);
                        setTimeout(function () {
                            $("#form-forget span[name='" + i + "_error']").text('');
                        }, 5000);
                    }
                })
            }
        });
    });

    /**
     * 重置密码
     */
    $("#reset").bind('click', function () {
        var param = {};
        param['phone'] = $("#formModify input[name='phone']").val();
        param['password'] = $("#formModify input[name='password']").val();
        param['confirmpassword'] = $("#formModify input[name='confirmpassword']").val();
        param['act'] = $("#formModify input[name='act']").val();
        $.ajax({
            type: 'post',
            url: '/citypartner/public/reset',
            data: param,
            cache: false,
            dataType: 'json',
            success: function (data) {
                if (data.status == true) {
                    $('.form-modify').css({'display': 'none'});
                    bg();
                    $("#resetOkdiv").removeClass('hide');
                    //$('#formContainer').removeClass('hide').removeClass('flipped');
                    //$("#reset_pwd_error").text();
                    //3秒后跳转
                    setTimeout(function () {
                        location.href = "/citypartner/public/index?login=now";
                    }, 3000);
                } else {
                    $("#resetdiv span[name='password_error']").text(data.message[1]);
                    setTimeout(function () {
                        $("#resetdiv span[name='password_error']").text('');
                    }, 5000);
                }
            },
            error: function (data) {
                var errorinfo = '';
                var return_data = data.responseJSON ? data.responseJSON : eval('(' + data.responseText + ')');
                $.each(return_data, function (i, j) {
                    if (errorinfo == '') {
                        errorinfo = j[0];
                        $("#resetdiv span[name='password_error']").text(errorinfo);
                        setTimeout(function () {
                            $("#resetdiv span[name='password_error']").text('');
                        }, 5000);
                    }
                })
            }
        });
    });

    /**
     * 找回密码发送验证码
     */
    $('#getcode').click(function () {
        var $phone = $("#forgetphone");
        //console.log($phone);
        if ($phone.val().length != 11) {
            $('span[name="phone_error"]').text('请输入账号');
            setTimeout(function(){
                $('span[name="phone_error"]').text('');
            },3000);
            return false;
        }
        var that = this;
        var param = {};
        param['username'] = $("#form-forget input[name='phone']").val();
        param['type'] = "forget_partner_pwd";
        $.ajax({
            type: 'post',
            url: '/citypartner/public/sendcode',
            data: param,
            cache: false,
            dataType: 'json',
            success: function (data) {
                if (data.status == true) {
                    clearTimeout(tt);
                    time2(that);
                    $('#form-forget span[name="phone_error"]').text('');
                    $('#form-forget span[name="code_error"]').text('验证码已发送到您的手机，请勿泄露！');
                } else if (data.status == false && data.message[0] == 'phone_error') {
                    $('#form-forget span[name="phone_error"]').text(data.message[1]);
                } else {
                    $('#form-forget span[name="code_error"]').text('验证码发送失败！');
                }
                setTimeout(function () {
                    $('#form-forget span[name="phone_error"]').text('');
                    $('#form-forget span[name="code_error"]').text('');
                }, 5000);
                $(this).css({'background': '#2e3a45', 'border': '0'});
            }
        });
    })

    /**
     * 登陆
     */
    $('#dologin').click(function () {
        var param = {};
        param['phone'] = $("#formLogin input[name='phone']").val();
        param['password'] = $("#formLogin input[name='password']").val();
        param['remName'] = $("#formLogin input[name='remName']").val();
        param['remPass'] = $("#formLogin input[name='remPass']").val();
        param['act'] = $("#formLogin input[name='act']").val();
        $.ajax({
            type: 'post',
            url: '/citypartner/public/login',
            data: param,
            cache: false,
            dataType: 'json',
            success: function (data) {
                if (data.status == true) {
                    location.href = "/citypartner/public/index";
                } else {
                    $("#formLogin span[name='" + data.message[0] + "']").text(data.message[1]);
                    setTimeout(function () {
                        $("#formLogin span[name='" + data.message[0] + "']").text('');
                    }, 5000);
                }
            },
            error: function (data) {
                var errorinfo = '';
                var return_data = data.responseJSON ? data.responseJSON : eval('(' + data.responseText + ')');
                $.each(return_data, function (i, j) {
                    if (errorinfo == '') {
                        errorinfo = j[0];
                        $("#formLogin span[name='" + i + "_error']").text(errorinfo);
                        setTimeout(function () {
                            $("#formLogin span[name='" + i + "_error']").text('');
                        }, 5000);
                    }
                })
            }
        });
    })

    $("#m-login-btns button[name='myteam']").click(function () {
        location.href = "/citypartner/myteam/index";
    });
    $("#m-login-btns button[name='mybusiness']").click(function () {
        location.href = "/citypartner/business/list";
    });
    $("#m-login-btns button[name='myprofit']").click(function () {
        location.href = "/citypartner/profit/list";
    });
    $("#m-login-btns button[name='mymaker']").click(function () {
        location.href = "/citypartner/maker/index";
    });

    /**
     * 裁剪图片
     */
    $("#AvatarOk").click(function () {
        var cut_x = $("#cutAvatar input[name='x1']").val();
        var cut_y = $("#cutAvatar input[name='y1']").val();
        var cut_width = $("#cutAvatar input[name='w']").val();
        var cut_height = $("#cutAvatar input[name='h']").val();
        var saveurl = $("#cutAvatar input[name='path']").val();
        if(!saveurl){
            $("#upload_avatar").addClass('hide');
            $("#user_avatar").removeClass('hide');
            return alert('未选择图片无法剪切');
        }
        ajaxRequest({
//            'cut_type': 'user',
            'path': saveurl,
            'cut_x': cut_x,
            'cut_y': cut_y,
            'cut_width': cut_width,
            'cut_height': cut_height
        }, '/citypartner/upload/cut', function (data) {
            if (data.status) {
//                $("image[name='avatar']").attr("src", data.message);
                $("#user-avatar").attr("src", '/'+data.message+'?'+Math.random());
                $('#user-avatar-input').val(data.message);
                $("#upload_avatar").addClass('hide');
                $("#user_avatar").removeClass('hide');
            } else {
                //showMessage('error','截图失败');
            }
        });
    });
    if ((window.location.href).indexOf('login') > 0) {
        $('#btn-login').click();
    } else if ((window.location.href).indexOf('register') > 0) {
        reg.click();
    };
    $('.login-close').click(function(){
        if((window.location.href).indexOf('?')>0){
            window.location.href = (window.location.href).split('?')[0];
        }
    });
});

//显示遮罩层
function bg() {
    $('.bg').removeClass('hide');
}
//选项卡
function tabs(lis, boxs) {
    lis.click(function () {
        $(this).addClass('cur').siblings().removeClass('cur');
        var index = lis.index(this);
        boxs.eq(index).removeClass('hide').siblings().addClass('hide');
    })
};
//回车键登录
function keyLogin() {
    if (event.keyCode == 13)  //回车键的键值为13
        document.getElementById("dologin").click(); //调用登录按钮的登录事件
};

//手机验证
function checkPhone(phone){
    var pattern = /^1[345678]\d{9}$/;
    return pattern.test(phone);
};

//邮箱验证
function checkEmail(email){
    var pattern = /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
    return pattern.test(email);
};



//部分兼容IE
var isIE=!!window.ActiveXObject;
var isIE6=isIE&&!window.XMLHttpRequest;
var isIE8=isIE&&!!document.documentMode;
var isIE7=isIE&&!isIE6&&!isIE8;
if (isIE){
    //$('#login-now').click(function(){
    //    $('.form-modify').addClass('hide');
    //    bg();
    //    $('#formContainer').removeClass('hide').removeClass('flipped');
    //});
    if (isIE8){
        $('.form-login').css({
            'width':600,
            'height':480,
            'margin-left':-300
        });
        $('#formLogin').css({
            'width':390
        });
        $('#m-forget').click(function () {
            bg();
            $('#formContainer').removeClass('hide').removeClass('flipped');
        });
    }
}