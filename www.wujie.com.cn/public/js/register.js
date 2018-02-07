/**
 * Created by wangcx on 2016/8/10.
 */
Zepto(function () {

    //pc端获得当前日期
    var date=new Date();
    var formatDate = function (date) {
        var y = date.getFullYear();
        var m = date.getMonth() + 1;
        m = m < 10 ? '0' + m : m;
        var d = date.getDate();
        d = d < 10 ? ('0' + d) : d;
       // return y + '-' + m + '-' + d;
        $(".date").html(y + '-' + m + '-' + d);
    };
    formatDate(date);

    //验证手机号
    $("input[name=phonenumber]").blur(function(){
        var reg=/1[34578]\d{9}/ig;
        var num=$(this).val().trim();
        if(reg.test(num)&&num.length==11){

        }else{
            if(num==""){
                $(".alert p").html("手机号码不能为空");
            }else{
                $(".alert p").html("手机号码格式错误");
            };
          //  var left="-"+(parseFloat($('.alert').width())/60)+"rem";
         //   var left=($("body").width()-$(".alert").width())/60+"rem";
         //   $(".alert").css("display","block");
        //    setTimeout($(".alert").opacity("slow"),1000);
        }
    });
    //短信验证码计时器
    var tt;
    var wait = 60;

    function time(o) {
        if (wait == 0) {
            o.removeAttr("disabled");
            o.html("重新发送");
            o.css("font-size","1.6rem");
            wait = 60;
        } else {
            o.attr("disabled", true);
            o.css("font-size","1.2rem");
            o.html('重新发送(' + wait + 's)');
            wait--;
            tt = setTimeout(function () {
                    time(o)
                },
                1000)
        }
    };
//点击获取验证码
    $("#mes-code").click(function () {
        var getcode = $(this);
        time(getcode);
        $(this).css("background", "#c8c8c8");
    });
// 获取分享人信息
    
    function getsharename(code){
        var param={};
        param['code']=code;
        var url=labUser.api_path+'/activity/sharename';
        ajaxRequest(param,url,function(data){
            if(data.status){
                $("#share-name").html(data.message.name);
            };
        })
    };
    getsharename(code);

/*    var uid=labUser.uid;
    function sharer(uid){
        var param={};
        param["uid"]=uid;
        var url=labUser.api_path+'/login/inviteregister';
        ajaxRequest(param,url,function(data){
            if(data.status){
                $("#share-name").html(data.message.nickname);
            };
        })
    };
  sharer(uid);
 */ 
  //图形验证码
   function picidentify(){
        var param={};
       
       var url=labUser.api_path+'/identify/sendcaptcha';
        ajaxRequest(param,url,function(data){
            if(data.status){
                $("#pic-code").html('<img style="width:100%;height:100%;" src="'+data.message+'"/>');
                console.log(data.message.captcha_id);
            };
        })
   }
   picidentify();
});