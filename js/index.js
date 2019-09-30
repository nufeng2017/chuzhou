var strs = 's',time,step = 1,isClick = false,timer,timeTotal = 60,str1 = '获取验证码';
function setintervaltime(){
	// hui('.hui-get-code').click(function(){
	hui('.hui-get-code').css({'color':'#d0d0d0','border':'1px solid #d0d0d0'});
		if (!isClick){
			isClick = true;
			time = timeTotal;

			//do something!!

		} else {
			return;
		}
		timer = setInterval(function(){
			time -= step;
			if (time > 0){
				hui('.hui-get-code').html(time + strs);
			} else {
				hui('.hui-get-code').css({'color':'#333'});
				hui('.hui-get-code').html(str1);
				isClick = false;
				clearInterval(timer);
			}
		},1000);
	// });
}


