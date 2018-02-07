
require.config({
	　　　　paths: {
	　　　　　　"Zepto": "/js/lib/zeptojs/zepto.min",
	            'common':'/js/common'
	　　　　}
	　　});
require(['Zepto','common','refusebargain'], function (Zepto,common,refusebargain){
	      refusebargain.init();
　　});
