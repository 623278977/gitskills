@extends('layouts.default')
@section('css')
    <style type="text/css">
		body, html,#allmap {width: 100%;height: 100%;overflow: hidden;margin:0;font-family:"微软雅黑";}
	</style>
@stop
@section('main')    
    	<div id="allmap"></div> 
@stop
@section('endjs')
<script type="text/javascript" src="http://api.map.baidu.com/api?v=2.0&ak=owbVzbAv2yezksn2IdWWPgZxuRd8ZK5g"></script>
<script>
    var $body = $('body');
    document.title = "地图地址";
    // hack在微信等webview中无法修改document.title的情况
    var $iframe = $('<iframe ></iframe>').on('load', function() {
    setTimeout(function() {
    $iframe.off('load').remove()
    }, 0)
    }).appendTo($body)
</script> 
<script type="text/javascript">
 Zepto(function () {
 	var activity_id={{$id}},
 		maker_id={{$maker_id}};
// 百度地图API功能
	var map = new BMap.Map("allmap");
	var point = new BMap.Point(116.331398,39.897445);
		map.centerAndZoom(point,12);
	// 创建地址解析器实例
	var myGeo = new BMap.Geocoder(); 

	var navigationControl = new BMap.NavigationControl();//创建平移缩放控件  
		map.addControl(navigationControl);//添加到地图  
	var scaleControl = new BMap.ScaleControl();//这里创建比例尺控件  
		map.addControl(scaleControl);//添加到地图  
	var overviewMapControl = new BMap.OverviewMapControl();//创建缩略图控件，注意这个控件默认是在地图右下角，并且是缩着的  
		map.addControl(overviewMapControl);//添加到地图  
	  		
 	function getAddress(activity_id){
              	var param={};
              		param['activity_id']=activity_id;
              		 var url=labUser.api_path+'/activity/makers';
              		// var url='/api/activity/makers';
              		ajaxRequest(param,url,function(data){
              			if(data.status){
              				$.each(data.message,function(index,item){
 								if(data.message[index].maker_id==maker_id){
 									var address=data.message[index].subject+data.message[index].address;
 										console.log(address);
 										
									// 将地址解析结果显示在地图上,并调整地图视野
										myGeo.getPoint(address, function(point){
										if (point) {
											map.centerAndZoom(point, 20);
											map.addOverlay(new BMap.Marker(point));
										}else{
											alert("您选择地址没有解析到结果!");
										}
									}, "北京市");
 								}
 							})
              				console.log(data.message.length);
              			}
              		});
              };	
		getAddress(activity_id);

});
</script>
@stop