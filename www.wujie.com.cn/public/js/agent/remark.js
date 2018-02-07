// created byhongky
    new FastClick(document.body);
    var urlPath = window.location.href,
        args = getQueryStringArgs(),
        agent_id = args['agent_id'],
        brand_id = args['brand_id'] || '',
        customer_id= args['customer_id'];
    var form_list =window.location.href.indexOf('form_list') > 0 ? true:false;
        if(form_list){
          $('.brand_sel').removeClass('none');
        }
    var Detail = {
        detail: function (agent_id,customer_id) {
            var param = {};
            param["agent_id"] = agent_id;
            param['customer_id']=customer_id;
            param['id']=brand_id ;
            param['tags']='default';
            var url=labUser.agent_path+'/customer/add-remark/_v010000';
            ajaxRequest(param, url, function (data) {
            if (data.status) {
                                Detail.init(data.message);
                                Detail.loop(data.message);
                                Detail.style();
                                $('#act_container').removeClass('none');
                              }//data.status
                    })// ajaxRequest
            },//detail方法
        init:function(obj){
            if(obj.customer_level){
             $('#ui_level').html(obj.customer_level).addClass('color333'); 
             $('#ui_level').data('key',obj.customer_level_id);
            }else{
             $('#ui_level').html('请选择客户等级') 
            }   
            if(obj.current_level){
               var html='';
                   html='<option>'+obj.current_level+'</option>';
                   $('#container').append(html);
            }
        },
       loop:function(obj){
            $.each(obj.customer_level_list,function(k,v){
              var html='';
                  html='<li class="ui-border-b" data-key="'+k+'">'+v+'</li>';
                  $('.ui_level_con').append(html);
            });
            var bra_html = '';
            if(obj.brand_list && obj.brand_list.length>0){
              $.each(obj.brand_list,function(x,y){
                  bra_html+='<li class="ui-border-b" data-key="'+y.id+'">'+y.brand_name+'</li>'
              });
              $('.brand_ul').html(bra_html);
            };
            
       },
       style:function(){
        if($('textarea').val()){
            $('textarea').addClass('color333');
          };
        // 选择品牌
          $('.brand_level').click(function(){
            $(this).addClass('ui-border-b');
            $('.brand_ul').removeClass('none').removeClass('a-fadeoutT');
          });

          $('.brand_ul li').on('click',function(){
            $('.brand_level').removeClass('ui-border-b');
            $('.brand_ul').addClass('a-fadeoutT');
            setTimeout(function(){$(".brand_ul").addClass('none')}, 500);
            var html=$(this).text();
            var key=$(this).data('key');
            $('#brand_level').html(html).addClass('color333');
            $('#brand_level').data('key',key);
         })
      //选择客户等级
          $('.level').on('click',function(){
            $(this).addClass('ui-border-b');
            $('.ui_level_con').removeClass('none').removeClass('a-fadeoutT');
          })

          $('.ui_level_con li').on('click',function(){
            $('.level').removeClass('ui-border-b');
            $('.ui_level_con').addClass('a-fadeoutT');
            setTimeout(function(){$(".ui_level_con").addClass('none')}, 500);
            var html=$(this).text();
            var key=$(this).data('key');
            $('#ui_level').html(html).addClass('color333');
            $('#ui_level').data('key',key);
         })
        }
        }//activityDetail对象    
     Detail.detail(agent_id,customer_id);  
     function tips(e) {
            $('.common_pops').text(e).removeClass('none');
          setTimeout(function() {
            $('.common_pops').addClass('none');
          }, 1500);

    }; 
    function dosaves(){
            var param = {};
                param["agent_id"]=agent_id;
                param['customer_id']=customer_id;
              if(form_list){
                param['id']=$('#brand_level').data('key');
              }else{
                param['id']=brand_id;
              };  
                param['level_id']=$('#ui_level').data('key');
                param['remark']=$('textarea').val();
                param['tags']='submits';
            var url=labUser.agent_path+'/customer/add-remark/_v010000';  
                if(!($('textarea').val())){
                   tips('备注信息不能为空');
                }else if(!(param['id'])){
                  tips('请选择品牌');
                }else{
                  ajaxRequest(param, url, function (data) {
                      if (data.status) {
                          dosave_success();
                      }else{
                        tips('添加失败');
                      }
                  });
                };
          };

    function dosave_success(){
      if (isAndroid) {
          javascript:myObject.dosave_success();
      } else if (isiOS) {  
          var data={};
          window.webkit.messageHandlers.dosave_success.postMessage(data);
      }
    };


    