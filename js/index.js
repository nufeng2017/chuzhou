var str = 's',time,step = 1,isClick = false,timer,timeTotal = 60,str1 = '获取验证码';
hui('.hui-get-code').click(function(){
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
			hui('.hui-get-code').html(time + str);
		} else {
			hui('.hui-get-code').html(str1);
			isClick = false;
			clearInterval(timer);
		}
	},1000);
});

