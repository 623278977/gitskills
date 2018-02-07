define(['Zepto','common','tool'],function(Zepto,common,tool){
	return {
		init: function() {
			new FastClick(document.body);
			var args = getQueryStringArgs(),
				contract_id = args['contract_id'];
			var param = {};
			param['contract_id'] = contract_id;
			var url = labUser.api_path + '/contract/detail/_v020800';
			ajaxRequest(param, url, function(data) {
				if (data.status) {
					$('.ui_top').html(' ');
					$.each(data.message, function(k, v) {
						var html = '';
							html += '<div class="ui_infor1 f14 color333">\
									           <p>付款协议<span class="fr a869e">' + v.contract_title + '</span></p>';
							html += '<p>加盟品牌<span class="fr a869e">' + v.brand + '</span></p>\
									           <p>合同撰写<span class="fr a869e">无界商圈法务代表</span></p>\
									           <p><span class="fr a869e">' + v.brand + '法务代表</span></p>\
									       </div>';
							html += '<div style="width:100%;height:1.9rem"></div>\
									         <ul class="ui_circle">\
									           <li><div class="ui_left_circle"></div></li>\
									           <li><div class="ui_dotted"></div></li>\
									           <li><div class="ui_right_circle fr"></div></li>\
									         </ul>';
							html += '<div class="ui_infor1 f14 color333 padding">';
							html += '<ul class="ui_refus">\
								             <li>拒绝理由</li>\
								             <li><p class="a869e fr">' + v.remark + '</p></li>\
								           </ul>\
								           <div style="width:100%;height:0.7rem;clear:both"></div>';
							html += '<p>确认时间<span class="fr a869e">' + tool.unix1(v.confirm_time) + '</span></p>\
									       </div>';
						$('.ui_con').append(html).addClass('ui_border');
						$('.ui_top').removeClass('none').addClass('a-rotateinRT');

					})
				}
			})
		}
	}
})