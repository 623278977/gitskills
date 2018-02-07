// created byhongky
Zepto(function (){
    new FastClick(document.body);
  //解决安卓客户端数字键盘出来导致页面变形的问题
  if(isAndroid){
     $(document).ready(function () {
　　      $('body').height($('body')[0].clientHeight+44);
    });
  } 
    var urlPath = window.location.href,
        args = getQueryStringArgs(),
        agent_id = args['agent_id'];
    var tt;
    var wait = 60;
    var Detail = {
        detail: function (agent_id,type) {
        var param = {};
            param["agent_id"] = agent_id;
            param['type']=type;
            var url=labUser.agent_path+'/user/invite-slogan/_v010000';
            ajaxRequest(param, url, function (data) {
            if (data.status) {
                               if(data.message.avatar){
                                $('.ui_img').attr('src',data.message.avatar)
                               }else{
                                $('.ui_img').attr('src','/images/default/avator-m.png')
                               }
                               Detail.name(data.message);
                                $('#act_container').removeClass('none');
                              }//data.status
                    })// ajaxRequest
            },//detail方法
        name:function(obj){
                          if(obj.is_public_realname==1){
                            $('.nickname').html((obj.realname?obj.realname:obj.nickname)+' '+':');
                          }else if(obj.is_public_realname==0){
                            $('.nickname').html(obj.nickname+' '+':')
                          }
        },
        style:function(){
             var phone =$('input[name="phone"]').val();
             var ret = /^1(3|4|5|7|8)\d{9}$/;
             $('.submit').on('click',function(){
             if(ret.test($('input[name="phone"]').val())){
                    yanzheng();
              }else{
                Detail.tips('请输入正确手机号码')
              }
             })
            $('.cancel').on('click',function(){
              $('.bg-model').addClass('none')
            })    
                     
        },
        tips:function(e){
            $('.tips').text(e).removeClass('none');
            setTimeout(function() {
                $('.tips').addClass('none');
            }, 1500);
        },
        time:function(o){ 
             if(wait == 0){
                        o.removeAttr("disabled");
                        o.html("重新发送");
                        o.css({
                          "font-size":"12px",
                          "background":"#f2f2f2"
                        });
                        wait = 60;
                      }else {
                        o.attr("disabled", true);
                        o.css({
                          "font-size":"12px",
                          "background":"#f2f2f2"
                        });
                        o.html('重新发送(' + wait + 's)');
                        wait--;
                        tt = setTimeout(function () {
                               Detail.time(o)
                            },
                            1000)
            }
        },
        getcode:function(){
          $('.getcode').on('click',function(){
            var that=$('.getcode');
             Detail.time(that);
             var param={};
                 param['username']=$('input[name="phone"]').val();
                 param['type']='registerCode';
                 param['app_name']='wjsq';
                 param['nation_code']=86;
             var url=labUser.api_path+'/identify/sendcode/_v010000';
             ajaxRequest(param,url,function(data){
            if(data.status){
                   };
                    });
                  })
        },
        makesure:function(){
          $('.makesure').on('click',function(){
            if($('#code').val()){
                   submit();
            }else{
                 Detail.tips('请输入验证码')
            }
          })
        }
        }//activityDetail对象    
     Detail.detail(agent_id,'customer');  
     Detail.style();
     Detail.getcode();
     Detail.makesure();
     function submit(){
          var param={};
              param['username']=$('input[name="phone"]').val();
              param['code']=$('#code').val();
              param['agent_id']=agent_id;
              param['type']='registerCode';
          var url=labUser.agent_path+'/user/customer-register/_v010002';
          ajaxRequest(param,url,function(data){
            if(data.status){
                  Detail.tips('注册成功');
                  $('.bg-model').addClass('none');
                  $('input[name="wrirecode"]').val('');
                  $('input[name="phone"]').val('');
                  setTimeout(function(){window.location.href = labUser.path + '/webapp/wjload/detail';},2000) ;       
             }else{
                  Detail.tips(data.message)
            };
        });
    }
   function yanzheng(){
            var param={};
                param['phone']=$('input[name="phone"]').val();
                param['type']=2;
            var url=labUser.agent_path+'/user/isregister/_v010000';
              ajaxRequest(param,url,function(data){
            if(data.status){
              if(data.message=='ok'){
                $('.bg-model').removeClass('none');
              }else if(data.message=='has_register'){
                  Detail.tips('您已经注册过了');
                 setTimeout(function(){window.location.href = labUser.path + '/webapp/wjload/detail';},2000) 
              }
              }else if(data.message=='has_register'){
                  Detail.tips('您已经注册过了');
                 setTimeout(function(){window.location.href = labUser.path + '/webapp/wjload/detail';},2000) 
              }
        });
   };

  
});//zepto外层
  