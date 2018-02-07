//By Hongky
Zepto(function(){
	 new FastClick(document.body);
    var args=getQueryStringArgs();
        uid=args['uid'],
        order_no= args['order_no'];
   var Fudai={
             init:function(uid,order_no){
                    var params={};
                        params['uid']=uid;
                        params['order_no']=order_no;
                    var url=labUser.api_path + '/user/myorderinfo/_v020902';
                    ajaxRequest(params, url, function(data) {
                        if (data.status){
                                    Fudai.data(data.message.orderInfo);
                                    $('.containerBox').removeClass('none');
                                   }
                          })
              },
              data:function(obj){
                        $('#brandtitle').html('成功加盟'+' '+obj.brand_title+' '+'品牌');
                        $('.a1').html(obj.contract_name);
                        $('.a2').html(obj.contract_no);
                        $('.a3').html(obj.league_type);
                        $('.a4').html(obj.brand_title);
                        $('.a5').html(obj.agent_nickname);
                        $('.a6').html(obj.brand_title+'法务代表');
                        $('.a7').html(obj.amount);
                        $('.b1').html('￥'+obj.initial_packet);
                        $('.b2').html('￥'+obj.packet_sum);
                        $('.b3').html('￥'+obj.invite_packet);
                        $('.b4').html('￥'+obj.intent_packet);
                        $('.c1').html(obj.total_pay);
                        $('.c2').html(obj.residue);
                        $('.c3').data('url',obj.contract_addr);
                        $('.c4').html(obj.brand_title+'加盟电子合同');

                        

              }
          
        }
     Fudai.init(uid,order_no);
    $(document).on('click','.ui_contrack_detail',function(){
         var url=$(this).data('url');
         window.location.href = labUser.path +'js/agent/generic/web/viewer.html?file='+url;
    })
});