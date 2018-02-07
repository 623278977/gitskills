$.validator.setDefaults({
    submitHandler: function(form) {
        form.submit();
    }
});

jQuery.validator.addMethod("isPhone", function(value,element) {
    var length = value.length;
    var mobile = /^(((13[0-9]{1})|(15[0-9]{1}))+\d{8})$/;
    var tel = /^\d{3,4}-?\d{7,9}$/;
    return this.optional(element) || (tel.test(value) || mobile.test(value));
}, "请输入正确格式的手机号码");


//注册表单验证validate
//var validator1 =$("#formRegister").validate( {
//    errorElement: "span",
//    rules:{
//        phone:{
//            required:true,
//            isPhone:true,
//            minlength:11,
//            maxlength:11
//        },
//        name:"required",
//        check:"required",
//        password:{
//            required:true,
//            //minlength:6,
//            //maxlength:16
//        },
//        confirmpassword:{
//            required:true,
//            equalTo: "#password"
//        },
//        invite:"required"
//    },
//    messages:{
//        phone:{
//            required:"请输入手机号",
//            isPhone:"请输入正确格式的手机号！",
//            minlength:"请输入正确格式的手机号！",
//            maxlength:"请输入正确格式的手机号！"
//        },
//        check:{
//            required:"请输入验证码",
//        },
//        invite:{
//            required:"请输入邀请码",
//        },
//        name:{
//            required:"请输入领导人姓名",
//        },
//        password: {
//            required: "请输入密码",
//            //minlength: "密码长度不能小于6位",
//            //maxlength:"密码长度不能超过16位"
//        },
//        confirmpassword: {
//            required: "请输入密码",
//            //minlength: "密码长度不能小于6位",
//            equalTo: "两次密码输入不一致"
//        },
//    },
//});
//$('.reg-close-resset').click(function(){
//    validator1.resetForm();
//})
//个人资料表单验证
//$("#formInfo").validate( {
//    errorElement: "span",
//    rules:{
//
//        name:"required",
//        sex:"required",
//        province:"required",
//        city:"required"
//    },
//    messages:{
//        name:"请输入真实姓名",
//        sex:"请选择性别",
//        province:"请选择地区",
//        city:"请选择地区"
//
//    },
//});
//登录表单验证
//$("#formLogin").validate({
//    errorElement:"span",
//    rules:{
//        username:"required",
//        password:"required"
//    },
//    messages:{
//        username:"请输入账号",
//        password:"请输入密码"
//    }
//});
//忘记密码表单验证
//$('#formForget').validate({
//    errorElement:"span",
//    rules:{
//        username:"required",
//        password:"required"
//    },
//    messages:{
//        username:"请输入账号",
//        password:"请输入密码"
//    }
//});

//重置密码表单验证
//$('#formModify').validate({
//    errorElement:"span",
//    rules:{
//        password:{
//            required:true,
//            minlength:6,
//            maxlength:16
//        },
//        confirmpassword:{
//            required:true,
//            equalTo: "#password"
//        },
//    },
//    messages:{
//        password: {
//            required: "请输入密码",
//            minlength: "密码长度不能小于6位",
//            maxlength:"密码长度不能超过16位"
//        },
//        confirmpassword: {
//            required: "请输入密码",
//            minlength: "密码长度不能小于6位",
//            equalTo: "两次密码输入不一致"
//        },
//    }
//});
