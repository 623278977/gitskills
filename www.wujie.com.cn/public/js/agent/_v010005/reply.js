//1.0.5新增
		//点击评论内容出现弹窗
        
  function commonReply(id){
    //点击 弹窗其他部分，弹窗消失
      $(document).on('click',function(e){ 
        　　var e = e || window.event; //浏览器兼容性 
        　　var elem = e.target || e.srcElement; 
        　　while (elem) { //循环判断至跟节点，防止点击的是div子元素 
        　　　　if (elem.class && elem.class=='comment_tip') { 
        　　　　return; 
        　　} 
        　　elem = elem.parentNode; 
        　　} 
            $('.comment_content').css('background','transparent');
        　　$('.comment_tip').addClass('none'); //点击的不是div或其子元素 
        }); 
      $(document).on('click','.comment_content',function(){
          if(!shareFlag){
              var comment_tip = $(this).children('.comment_tip');
              if($(this).parents('li').siblings('li').find('.comment_tip').length >0){
                $(this).parents('li').siblings('li').find('.comment_tip').addClass('none');
                $(this).parents('li').siblings('li').find('.comment_content').css('background','transparent');
              }else if($(this).parents('ul').siblings('ul').find('.comment_tip').length >0){
                $(this).parents('ul').siblings('ul').find('.comment_tip').addClass('none');
                $(this).parents('ul').siblings('ul').find('.comment_content').css('background','transparent');
              }
              
              comment_tip.removeClass('none');
              $(this).css('background','#ddd');
              var left = comment_tip.offset().left;
              var right = comment_tip.offset().right;
               if(left < 2){
                  comment_tip.css('left','0');
                  $(this).children('.comment_tip').addClass('change_position');
               };
          }
            
         })
    //回复功能
        $(document).on('click','.reply',function(){
          var type = $(this).attr('data-type');
          var upid = $(this).attr('data-id');
          var comment_name = $(this).parents('li').find('.comment_name').text();
          console.log(comment_name);
            if(type == 'video'){
              uploadpic(id,'Lesson',true,upid,comment_name);
            }else if(type == 'mes'){
              uploadpic(id,'messageDetail',true,upid,comment_name);
            }else if(type == 'newmes'){
              uploadpic(id,'new_agent_detail',true,upid,comment_name);
            }else if(type=='wechat'){
              uploadpic(id,'wechat',true,upid,comment_name);
            }
            $('.comment_tip').addClass('none'); 
         })
      //复制文本
      $(document).on('click','.comment_tip .copy' ,function(){
              var text = $(this).parents('li').find('.comment_text').text();
              copyToCb(text);
              $('.comment_tip').addClass('none'); 
              $('.copy_suc').removeClass('none');
              setTimeout(function(){ $('.copy_suc').addClass('none')},2000);   
        }); 
     }
//1.0.5版本公用 安卓替换新方法
//upid 回复评论的id
//name 回复评论人的昵称
        function uploadpic(id, type, istext,upid,name) {
            if (isAndroid) {
                javascript:myObject.uploadpic(id, type, istext,upid,name);
            } else if (isiOS) {
                var data = {
                    "id": id,
                    "type": type,
                    "istext": istext,
                    "upid":upid,
                    'name':name
                }
                window.webkit.messageHandlers.uploadpic.postMessage(data);
            }
        }

         