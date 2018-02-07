define(['Zepto', 'common', 'tool'], function(Zepto, common, tool) {
	return {
		show: function() {
			new FastClick(document.body);
			var args = getQueryStringArgs(),
				contract_id = args['contract_id'];
			var param = {};
			    param['contract_id'] = contract_id;
			var url = labUser.api_path + '/contract/detail/_v020800';
			ajaxRequest(param, url, function(data) {
				if (data.status) {
					$.each(data.message, function(k, v) {
						// $('.ui_top').html('成功加盟' +(v.brand.length>5?v.brand.substring(0,5)+'…':v.brand)+'品牌，合约签署完成');
						$('.ui_top').html(' ');
					var html = '';
						html += '<div class="ui_infor1 f14 color333">\
									           <p>付款协议<span class="fr a869e">' + v.contract_title + '</span></p>\
									           <p>流水号<span class="fr a869e">' + v.contract_no + '</span></p>\
									           <p>加盟品牌<span class="fr a869e">' + v.brand + '</span></p>\
								          </div>';
						html += '<div style="width:100%;height:1rem"></div>\
									         <ul class="ui_circle">\
									           <li><div class="ui_left_circle"></div></li>\
									           <li>\
									              <ul class="ui_border_flex ui_pR a869e f12">\
									                      <li style="width:40%"><div class="ui-border-b ui_row"></div></li>\
									                      <li style="width:20%"><span>首付情况</span></li>\
									                      <li style="width:40%"><div class="ui-border-b ui_row"></div></li>\
									              </ul>\
									           </li>\
									           <li><div class="ui_right_circle fr"></div></li>\
									         </ul>';
						html += '<div class="ui_infor2 f14 color333 paddingb01">\
									           <p>首次支付<span class="fr a869e">￥' + v.pre_pay + '</span></p>\
									           <p>定金抵扣<span class="fr a869e">-￥' + v.invitation + '</span></p>\
									           <p>创业基金抵扣<span class="fr a869e">-￥' + v.fund + '</span></p>\
									           <p>实际支付<span class="fr a869e">￥' + v.first_amount + '</span></p>\
									           <p>支付状态<span class="fr a869e">' + v.first_pay_status + '</span></p>\
									           <p>支付方式<span class="fr a869e">' + v.pay_way + '</span></p>\
									           <p><span class="fr a869e">' + v.buyer_id + '</span></p>\
           									   <div style="width:100%;height:1rem;clear:both"></div>\
									           <p>支付时间<span class="fr a869e">' + tool.unix2(v.pay_at) + '</span></p>\
								           </div>';
						html += '<ul class="ui_border_flex ui_pR color666 f12 padding01">\
									                      <li style="width:40%"><div class="ui-border-b ui_row"></div></li>\
									                      <li style="width:20%"><span>尾款情况</span></li>\
									                      <li style="width:40%"><div class="ui-border-b ui_row"></div></li>\
									         </ul>';
						html += '<div class="ui_infor2 f14 color333 paddingb01">\
									           <p>尾款补齐<span class="fr a869e">￥' + (v.tail_pay) + '</span></p>\
									           <p>支付状态<span class="fr ff4d64">' + v.tail_pay_status + '</span></p>\
								           </div>';
						html += '<div class="ui_infor3 f12 a869e ">';
						if(v.status==2){
						html +=	'<p><span class="fr a869e none">*请于' + unix3(v.tail_leftover) + '前支付相关款项</span></p>';	
						}else if(v.status==1){
						html +=	'<p><span class="fr a869e ">*请于' + unix3(v.tail_leftover) + '前支付相关款项</span></p>';	
						}
						html += '<div style="height:0.5rem;width:100%;clear:both"></div>\
								           <p><span class="fr">如有延误等情况，请尽早联系经纪人</span></p>\
								           <div style="height:0.5rem;width:100%;clear:both"></div>\
								           <p><span class="fr">支付方式为线下转账打款，请了解对公账号等信息</span></p>\
								           <div style="height:0.5rem;width:100%;clear:both"></div>\
								           <p class="way"><span class="fr f14 ff2873 ">了解尾款补齐操作办法</span></p>\
								         </div>';
						html += '<div class="ui_infor2 f14 color333 paddingb01">\
									           <p>对公账号<span class="fr a869e">' + v.company_bank_no + '</span></p>\
									           <p>所属银行<span class="fr a869e">' + v.company_bank_name + '</span></p>\
									           <p>单位名称<span class="fr a869e">' + v.company_name + '</span></p>\
									           <p><span class="fr f12 a869e">*对公账号转账前，请先联系经纪人确认线下对公账户</span></p>\
									           <div style="height:0.5rem;width:100%;clear:both"></div>\
									           <p><span class="fr f12 a869e">*转账后，3~4天确认账户到账，届时会有专人通知您</span></p>\
									        </div>';
						html += '<div class="ui_contrack_bottom ui_pR color333 padding00">\
								                <p style="text-align:left" class="margin07 f12">合同文本</p>\
								                <ul class="ui_contrack_detail ui_add_bg " data-url="'+v.address+'">\
								                  <li>\
								                    <img class="ui_img6"  src="/images/020700/bargain2.png">\
								                  </li>\
								                  <li>\
								                    <p class="f14 b textleft color333 margin05">' +(v.brand.length>6?v.brand.substring(0,6)+'…':v.brand)+'加盟电子合同</p>\
								                    <p class="f11 textleft color333 none">合同编号:' + v.contract_no + '</p>\
								                  </li>\
								                  <li>\
								                    <img class="ui_img7"  src="/images/020700/y.png">\
								                  </li>\
								                </ul>\
								          </div>';
						$('.ui_con').append(html).removeClass('none');
						$('#ui_bottom').removeClass('none');
						$('.ui_top').removeClass('none').addClass('a-bounceinT');
					})
				}
			})
		}
	}
})