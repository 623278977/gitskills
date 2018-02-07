//By Hongky
Zepto(function(){
	 new FastClick(document.body);
   var args=getQueryStringArgs(),
       agent_id=args['agent_id'];
   var Fudai={
             init(agent_id){
                  var param={};
                      param['agent_id'] = agent_id;
             var url = labUser.agent_path + '/agent-redpacket/new-year-redpacket/_v010300';
             ajaxRequest(param, url, function(data) {
                if (data.status){
                                 Fudai.data(data.message);
                                $('#containerBox').removeClass('none');
                           }else{
                                $('.nocomment').removeClass('none')
                           }
                  })
           },
           data(obj){
                     $('footer').html('<a  href="tel:4000110061" style="color:#2873ff;display:block;height:100%">联系我们</a>');
                    if(obj.type==1){
                       $('.hello').html('<center><img class="ui-size7" src="/images/fu.png"></center>');
                       $('.hello2').text(obj.cont);
                       $('.hello3').text(obj.count);
                    }else if((obj.type==2)){
                       $('.hello3').text(obj.count);
                       $('.hello2').html('赏金已自动划入“您的钱包”，<span class="go" style="color:#2873ff">点击查看</span>');
                       $('.hello').html('<center><span class="b" style="color:#ff422f;font-size:6.7rem;">'+obj.cont+'</span><span class="f15" style="color:#ff422f;">元</span></center>');
                    }
           }
          
        }
     Fudai.init(agent_id);
     $(document).on('click','.go',function(){
         window.location.href= labUser.path+'/webapp/agent/mycharge/detail/_v010300?agent_id='+agent_id;
     })
});