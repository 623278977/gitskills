Zepto(function() {
	new FastClick(document.body);
	$('body').css('background', '#f2f2f2');
	var args = getQueryStringArgs(),
		agent_id = args['agent_id'] || 0,
		urlPath = window.location.href;
	var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
	//获取详情
	function getdetail() {
		var param = {};

		var url = labUser.agent_path + '/knowledge/types/_v010006';
		ajaxRequest(param, url, function(data) {
			var conHtml = '',
				html = '';
			if(data.status) {
				if(data.message) {
					if(data.message.banner != '') {
						if(data.message.banner.image != '') {
							conHtml += '<div class="tree_header mb1-2" link_url="' + data.message.banner.link_url + '">';
							conHtml += '<img src="' + data.message.banner.image + '" alt="" class="">';
							conHtml += '</div>';
						}
					}
					conHtml += '<div class="tree_nav bgwhite">';
					$.each(data.message.list, function(i, v) {
						conHtml += '<div class="tree_item" id="' + v.id + '" is_num="' + v.contents + '">';
						conHtml += '<img src="' + v.icon + '" alt="">';
						conHtml += '<p class="f11 color999">' + v.contents + '</p>';
						conHtml += '</div>';
					});
					conHtml += '</div>';
					if(data.message.recommend.length > 0) {
						$.each(data.message.recommend, function(k, v) {

							html += '<div class="ui_con color999" data-id="' + v.id + '">\
                                          <div class="padding">\
                                                <ul class="ui_text_pict">';
							if(v.logo) {
								html += '<li><p class="color333 f14 b ui-nowrap-multi mb1">' + v.title + '</p>\
                                                         <p class="f12 ui-nowrap-multi" style="line-height: 2rem;">' + v.summary + '</p>\
                                                     </li>\
                                                     <li>\
                                                      <div class="ui_protect_pict fr"><img class="ui_pict1" src="' + v.logo + '"/></div>\
                                                     </li>\
                                                </ul>';
							} else {
								html += '<li style="width:100%">\
                                               <p class="color333 f14 b ui-nowrap-multi">' + v.title + '</p>\
                                               <p class="f12 ui-nowrap-multi">' + v.summary + '</p>\
                                           </li>\
                                      </ul>';
							}
							html += '<p class="clear ui-border-b ui_row"></p>\
                                                <ul class="ui_text_down clear f11">\
                                                      <li>\
                                                        <ul class="ui_flex">\
                                                            <li>\
                                                              <img class="ui_zan fl" src="/images/agent/grey.png"/><span class="ui_padding fl">' + v.zan + '</span>\
                                                            </li>\
                                                            <li>\
                                                              <img class="ui_zan ui_mess" src="/images/agent/ui.png"/><span class="ui_padding">' + v.comments + '</span>\
                                                            </li>\
                                                            <li>\
                                                              <img class="ui_seen ui_mess" src="/images/agent/seen.png"/><span class="ui_padding">' + v.view + '</span>\
                                                            </li>\
                                                        </ul>\
                                                      </li>';
							if(v.author) {
								html += '<li><span style="padding-left:0.7rem">作者：' + v.author + '</span></li>';
							}

							html += '</ul>\
                                                <p class="clear margin"></p>\
                                            </div>\
                                          <div class="fline style"></div>\
                                 </div>';

						});
					}
				}
			};
			$('.list').html(conHtml);
			$('.commend').html(html);
		})
	}
	getdetail();
	//点击banner跳转
	$(document).on('click', '.tree_header', function() {
		var link_url = $(this).attr('link_url');
		window.location.href = link_url;
	});
	//点击跳转详情
	$(document).on('click', '.tree_item', function() {
		var knowledge_id = $(this).attr('id');
		var is_num = $(this).attr('is_num');
		window.location.href = labUser.path + 'webapp/agent/list/detail/_v010004?id=' + knowledge_id + '&agent_id=' + agent_id + '&is_num=' + is_num;
	});
	$(document).on('click','.ui_con',function(){
	    var id=$(this).data('id');
	    window.location.href=labUser.path+'webapp/agent/hotmessage/detail/_v010004?id='+id+'&agent_id='+agent_id;
    })
});