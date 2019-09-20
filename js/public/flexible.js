(function(doc, win) {
	var docEl = doc.documentElement,
	designWidth = 1080;//设计稿宽度
	resizeEvt = 'orientationchange' in window ? 'orientationchange' : 'resize',
	recalc = function() {
		var clientWidth = docEl.clientWidth;
		if(!clientWidth) return;
		docEl.style.fontSize = 40 * ( clientWidth / designWidth ) + 'px';
	};
	if(!doc.addEventListener) return;
	win.addEventListener(resizeEvt, recalc, false);
	doc.addEventListener('DOMContentLoaded', recalc, false);
})(document, window);