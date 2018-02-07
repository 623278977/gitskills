//适配兼容  
//        需在css定义      *{font-size:10px;} 根据PSD图尺寸除以10即可
(function(doc, win) {
				var docEle = doc.documentElement,
					dpr = Math.min(win.devicePixelRatio, 3),
					scale = 1 / dpr,
					resizeEvent = 'orientationchange' in window ? 'orientationchange' : 'resize';
				var metaEle = doc.createElement('meta');
				metaEle.name = 'viewport';
				metaEle.content = 'initial-scale=' + scale + ',maximum-scale=' + scale;
				docEle.firstElementChild.appendChild(metaEle);

				var recalCulate = function() {
					var width = docEle.clientWidth;
					docEle.style.fontSize = 10 * (width / 750) + 'px'; //根据UI图页面宽度更改 750 值 ，
				};
				recalCulate();
				if(!doc.addEventListener) return;
				win.addEventListener(resizeEvent, recalCulate, false);
})(document, window);