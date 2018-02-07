require.config({
	paths: {
		"Zepto": "/js/lib/zeptojs/zepto.min",
		"common": "/js/common"
	}
})
require(['Zepto', 'common', 'tool', 'successbargain'], function(Zepto, common, tool, successbargain) {
	successbargain.show();
	 $(document).on('click','.ui_contrack_detail',function(){
         var url=$(this).data('url');
         window.location.href = labUser.path +'js/agent/generic/web/viewer.html?file='+url;
    })
	$(document).on('click','.way',function(){
         window.location.href = labUser.path +'webapp/agent/way/detail';
    })
})