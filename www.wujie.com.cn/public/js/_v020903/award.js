new FastClick(document.body);
var args = getQueryStringArgs(),
	uid = args['uid'] || 0,
	urlPath = window.location.href,
	turnplate={
		restaraunts:[],				//大转盘奖品名称
		colors:[],	                //大转盘奖品区块对应背景颜色
		//fontcolors:[],				//大转盘奖品区块对应文字颜色
		outsideRadius:222,			//大转盘外圆的半径
		textRadius:165,				//大转盘奖品位置距离圆心的距离
		insideRadius:65,			//大转盘内圆的半径
		startAngle:0,				//开始角度
		bRotate:false				//false:停止;ture:旋转
	};
var shareFlag = urlPath.indexOf('is_share') > 0 ? true : false;
var num='';//根据接收过来的值判断概率。
//初始加载
	function getDetail(uid){
		var param = {};
		param['uid'] = uid;
		var url = labUser.api_path + '/timelimited/new-year-lottery/_v020903';
		ajaxRequest(param,url,function(data){
			if(data.status){
				$('#container').removeClass('none');
				$('.choujiang_num').text(data.message);
				
				//页面所有元素加载完毕后执行drawRouletteWheel()方法对转盘进行渲染
				drawRouletteWheel();
			}
		});
	};
	getDetail(uid);
	



//lottery(2);
//$('#container').removeClass('none');
//window.onload=function(){
//	drawRouletteWheel();
//};
var picH = 35;//移动高度 
var scrollstep=3;//移动步幅,越大越快 
var scrolltime=50;//移动频度(毫秒)越大越慢 
var stoptime=3000;//间断时间(毫秒) 
var tmpH = 0; 


	//动态添加大转盘的奖品与奖品区域背景颜色
//		console.log(num.substr(1));
	turnplate.restaraunts = ["谢谢参与","100积分"," 通用红包  1888元","爱奇艺VIP1个月","京东购物卡  100元", "Iphone X", " 现金红包  68元"];
	turnplate.colors = ["#FACA00", "#FBDB00", "#FACA00", "#FBDB00","#FACA00", "#FBDB00","#ffe320"];
	//turnplate.fontcolors = ["#CB0030", "#FFFFFF", "#CB0030", "#FFFFFF","#CB0030", "#FFFFFF"];
	
	var rotateTimeOut = function (){
		$('#wheelcanvas').rotate({
			angle:0,
			animateTo:2160,
			duration:6000,
			callback:function (){
				tips('网络超时，请检查您的网络设置！');
			}
		});
	};
	
	
	//旋转转盘 item:奖品位置; txt：提示语;
	var rotateFn = function (item, txt,lotteryMsg){
		var angles = item * (360 / turnplate.restaraunts.length) - (360 / (turnplate.restaraunts.length*2));
		if(angles<270){
			angles = 270 - angles; 
		}else{
			angles = 360 - angles + 270;
		}
		$('#wheelcanvas').stopRotate();
		$('#wheelcanvas').rotate({
			angle:0,
			animateTo:angles+1800,
			duration:6000,
			callback:function (){
				//中奖页面与谢谢参与页面弹窗
				if(txt.indexOf("谢谢参与")>=0){
//					$(".xxcy_text").html(lotteryMsg.msg);
					$("#xxcy-main").fadeIn();
					save();
				}else{
					$("#zj-main").fadeIn();
					var resultTxt=txt.replace(/[\r\n]/g,"");//去掉回车换行
					$("#jiangpin").text(lotteryMsg.msg);
					if(resultTxt.indexOf('Iphone')>=0 || resultTxt.indexOf('京东购物卡')>=0 || resultTxt.indexOf('爱奇艺')>=0){
						$('.my_message').removeClass('none');
					}else {
						$('.close_zj').removeClass('none');
					}
					save();
				}								
				turnplate.bRotate = !turnplate.bRotate;
			}
		});
	};
	/********抽奖开始**********/
	$('.pointer').click(function (){
		var choujiang_num = $(".choujiang_num").text();
		if(choujiang_num == 0){
			tips("今日抽奖次数已用完");
			return;
		}
		if(turnplate.bRotate)return;
		turnplate.bRotate = !turnplate.bRotate;
		lotteryResult(uid);
		
	});
	
//提交信息接口

$(document).on('click','.submit-mes',function(){
	var lottery_id = $('.my_message').attr('lottery_id');
	var tel = $('#tel').val();
	var site = $('#site').val();
//	console.log(lottery_id,tel,site);
	var param = {};
	param['lottery_id'] = lottery_id;
	param['username'] = tel;
	param['address'] = site;
	var url = labUser.api_path + '/timelimited/user-info/_v020903';
	ajaxRequest(param,url,function(data){
//		console.log(data);
		if (data.status) {
			tips(data.message);
			setTimeout(function(){
				$('#mes-main').fadeOut();
				window.location.reload();
			},3000);
			
			
		}else {
			tips(data.message);
		}
	});
	
})
//抽奖按钮接口
function lotteryResult(uid){
	var param = {};
	param['uid'] = uid;
	var url = labUser.api_path + '/timelimited/new-year-lottery-result/_v020903';
	ajaxRequest(param,url,function(data){
		if(data.status){
			if(data.message){
				num = data.message.result;
				var item = num;
				var lotteryMsg = {"msg":turnplate.restaraunts[item-1]};
				$('.my_message').attr('lottery_id',data.message.lottery_id);
//				console.log(turnplate.restaraunts[item-1]);
				rotateFn(item, turnplate.restaraunts[item-1],lotteryMsg);
			}
		}else {
			tips(data.message);	
		}
	});
}	
	
	/********弹窗页面控制**********/
	
	$('.close_zj').click(function(){  //点知道了返回抽奖
		window.location.reload();
		$('#zj-main').fadeOut();
		$('#ml-main').fadeIn();
		
	});
	$(".my_message").click(function(){//点提交打开提交弹框
		$('#mes-main').fadeIn();
		$('#zj-main').fadeOut();
	});
	$('.close_xxcy').click(function(){ //点再抽一次返回抽奖
		$('#xxcy-main').fadeOut();
		window.location.reload();
		$('#ml-main').fadeIn();
//		theEnd();
//		save();
	});

 $(".zj-main,.xxcy-main,.mes-main").on('touchmove',function(e){
    e.preventDefault();  //阻止默认行为
});


//canvas绘制抽奖转盘
function drawRouletteWheel() {    
  var canvas = document.getElementById("wheelcanvas");    
  if (canvas.getContext) {
	  //根据奖品个数计算圆周角度
	  var arc = Math.PI / (turnplate.restaraunts.length/2);
	  var ctx = canvas.getContext("2d");
	  //在给定矩形内清空一个矩形
	  ctx.clearRect(0,0,516,516);
	  //strokeStyle 属性设置或返回用于笔触的颜色、渐变或模式  
	  ctx.strokeStyle = "#FFBE04";
	  //font 属性设置或返回画布上文本内容的当前字体属性
	  ctx.font = 'bold 22px Microsoft YaHei';      
	  for(var i = 0; i < turnplate.restaraunts.length; i++) {       
		  var angle = turnplate.startAngle + i * arc;
		  ctx.fillStyle = turnplate.colors[i];
		  ctx.beginPath();
		  //arc(x,y,r,起始角,结束角,绘制方向) 方法创建弧/曲线（用于创建圆或部分圆）    
		  ctx.arc(258, 258, turnplate.outsideRadius, angle, angle + arc, false);    
		  ctx.arc(258, 258, turnplate.insideRadius, angle + arc, angle, true);
		  ctx.stroke();  
		  ctx.fill();
		  //锁画布(为了保存之前的画布状态)
		  ctx.save();   
		  
		  //----绘制奖品开始----
		  ctx.fillStyle = "#E83800";
		  //ctx.fillStyle = turnplate.fontcolors[i];
		  var text = turnplate.restaraunts[i];
		  var line_height = 30;
		  //translate方法重新映射画布上的 (0,0) 位置
		  ctx.translate(258 + Math.cos(angle + arc / 2) * turnplate.textRadius, 258 + Math.sin(angle + arc / 2) * turnplate.textRadius);
		  
		  //rotate方法旋转当前的绘图
		  ctx.rotate(angle + arc / 2 + Math.PI / 2);
		  
		  /** 下面代码根据奖品类型、奖品名称长度渲染不同效果，如字体、颜色、图片效果。(具体根据实际情况改变) **/
		  if(text.indexOf("\n")>0){//换行
			  var texts = text.split("\n");
			  for(var j = 0; j<texts.length; j++){
				  ctx.font = j == 0?'22px Microsoft YaHei':'22px Microsoft YaHei';
				  //ctx.fillStyle = j == 0?'#FFFFFF':'#FFFFFF';
				  if(j == 0){
					  //ctx.fillText(texts[j]+"M", -ctx.measureText(texts[j]+"M").width / 2, j * line_height);
					  ctx.fillText(texts[j], -ctx.measureText(texts[j]).width / 2, j * line_height);
				  }else{
					  ctx.fillText(texts[j], -ctx.measureText(texts[j]).width / 2, j * line_height);
				  }
			  }
		  }else if(text.indexOf("\n") == -1 && text.length>6){//奖品名称长度超过一定范围 
			  text = text.substring(0,6)+"||"+text.substring(6);
			  var texts = text.split("||");
			  for(var j = 0; j<texts.length; j++){
				  ctx.fillText(texts[j], -ctx.measureText(texts[j]).width / 2, j * line_height);
			  }
		  }else{

			  //在画布上绘制填色的文本。文本的默认颜色是黑色
			  //measureText()方法返回包含一个对象，该对象包含以像素计的指定字体宽度
			  ctx.fillText(text, -ctx.measureText(text).width / 2, 0);
		  }
		  //添加对应图标
		  if(text.indexOf("Iphone")>=0){
			  var img= document.getElementById("shouji");
			  img.onload=function(){  
				  ctx.drawImage(img,-20,40);      
			  }; 
			  ctx.drawImage(img,-20,40);  
		  }else if(text.indexOf("100积分")>=0){
			  var img= document.getElementById("jifen");
			  img.onload=function(){  
				  ctx.drawImage(img,-20,40);      
			  };  
			  ctx.drawImage(img,-20,40);  
		  }else if(text.indexOf("现金红包")>=0){
			  var img= document.getElementById("xianjin");
			  img.onload=function(){  
				  ctx.drawImage(img,-20,40);      
			  };  
			  ctx.drawImage(img,-20,40);  
		  }else if(text.indexOf("通用红包")>=0){
			  var img= document.getElementById("hongbao");
			  img.onload=function(){  
				  ctx.drawImage(img,-20,40);      
			  };  
			  ctx.drawImage(img,-20,40);  
		  }else if(text.indexOf("爱奇艺")>=0){
			  var img= document.getElementById("aiqiyi");
			  img.onload=function(){  
				  ctx.drawImage(img,-20,40);      
			  };  
			  ctx.drawImage(img,-20,40);  
		  }else if(text.indexOf("京东购物卡")>=0){
			  var img= document.getElementById("gouwuka");
			  img.onload=function(){  
				  ctx.drawImage(img,-20,40);      
			  };  
			  ctx.drawImage(img,-20,40);  
		  }else if(text.indexOf("谢谢参与")>=0){
			  var img= document.getElementById("aixin");
			  img.onload=function(){  
				  ctx.drawImage(img,-20,40);      
			  };  
			  ctx.drawImage(img,-20,40);  
		  }
		  //把当前画布返回（调整）到上一个save()状态之前 
		  ctx.restore();
		  //----绘制奖品结束----
	  }     
  } 
  

    // 对浏览器的UserAgent进行正则匹配，不含有微信独有标识的则为其他浏览器
    /*var useragent = navigator.userAgent;
    if (useragent.match(/MicroMessenger/i) != 'MicroMessenger') {
        // 这里警告框会阻塞当前页面继续加载
        alert('已禁止本次访问：您必须使用微信内置浏览器访问本页面！');
        // 以下代码是用javascript强行关闭当前页面
        var opened = window.open('about:blank', '_self');
        opened.opener = null;
        opened.close();
    }*/


}



function showDialog(id) {
    document.getElementById(id).style.display = "-webkit-box";
}

function showID(id) {    
    document.getElementById(id).style.display = "block";  
}
function hideID(id) {
    document.getElementById(id).style.display = "none";
}

//缓存函数
function save() {
	localStorage.end=theEnd();
	localStorage.gifts=$(".cjjg").text();
}

//提示抽奖结束
function theEnd() {
	$('#tupBtn').unbind('click');//提交成功解除点击事件。   
	return 2;
}

