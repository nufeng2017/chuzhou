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

/* 普通选择器 非关联型绑定 */
var picker1 = new huiPicker('#btn1', function(){
    var val = picker1.getVal(0);
    var txt = picker1.getText(0);
    var txt1 = picker1.getText(1);
    hui('#btn1 .hui-form-box>div').html(txt+'/'+txt1);
});

var data = [{
	value:'2019.9.23/星期一 ',
	text:'2019.9.23/星期一',
	children:[{
		value:'8:30-9:00',
		text:'8:30-9:00'
	},{
		value:'8:30-9:00',
		text:'8:30-9:00'
	},{
		value:'8:30-9:00',
		text:'8:30-9:00'
	},{
		value:'8:30-9:00',
		text:'8:30-9:00'
	},{
		value:'8:30-9:00',
		text:'8:30-9:00'
	},{
		value:'8:30-9:00',
		text:'8:30-9:00'
	},{
		value:'8:30-9:00',
		text:'8:30-9:00'
	},{
		value:'8:30-9:00',
		text:'8:30-9:00'
	},{
		value:'8:30-9:00',
		text:'8:30-9:00'
	},{
		value:'8:30-9:00',
		text:'8:30-9:00'
	},{
		value:'8:30-9:00',
		text:'8:30-9:00'
	},]
},{
	value:'2019.9.23/星期一 ',
	text:'2019.9.23/星期一',
	children:[{
		value:'8:30-9:00',
		text:'8:30-9:00'
	},{
		value:'8:30-9:00',
		text:'8:30-9:00'
	},{
		value:'8:30-9:00',
		text:'8:30-9:00'
	},{
		value:'8:30-9:00',
		text:'8:30-9:00'
	},{
		value:'8:30-9:00',
		text:'8:30-9:00'
	},{
		value:'8:30-9:00',
		text:'8:30-9:00'
	},{
		value:'8:30-9:00',
		text:'8:30-9:00'
	},{
		value:'8:30-9:00',
		text:'8:30-9:00'
	},{
		value:'8:30-9:00',
		text:'8:30-9:00'
	},{
		value:'8:30-9:00',
		text:'8:30-9:00'
	},{
		value:'8:30-9:00',
		text:'8:30-9:00'
	},]
},{
	value:'2019.9.23/星期一 ',
	text:'2019.9.23/星期一',
	children:[{
		value:'8:30-9:00',
		text:'8:30-9:00'
	},{
		value:'8:30-9:00',
		text:'8:30-9:00'
	},{
		value:'8:30-9:00',
		text:'8:30-9:00'
	},{
		value:'8:30-9:00',
		text:'8:30-9:00'
	},{
		value:'8:30-9:00',
		text:'8:30-9:00'
	},{
		value:'8:30-9:00',
		text:'8:30-9:00'
	},{
		value:'8:30-9:00',
		text:'8:30-9:00'
	},{
		value:'8:30-9:00',
		text:'8:30-9:00'
	},{
		value:'8:30-9:00',
		text:'8:30-9:00'
	},{
		value:'8:30-9:00',
		text:'8:30-9:00'
	},{
		value:'8:30-9:0011',
		text:'8:30-9:00111'
	},]
}];
picker1.level = 2;
//cities 数据来源于 cities.js
picker1.bindRelevanceData(data);