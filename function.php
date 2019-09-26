<?php
//获取未来七天的日期
for($i=1;$i<8;$i++){
    $dateArray[$i]=date('Y-m-d',strtotime(date('Y-m-d').'+'.$i.'day'));
};
$date = get_date($dateArray);//调用函数

//echo  json_encode($date);

/*
    * 返回输入日期数组对应的星期和日期
    * @param $dateArray 需要的日期数组，如未来七天的日期
    * */
function get_date($dateArray){
    $b=array();
    foreach($dateArray as $key=>$value){
        $b[$key]['value']=$value.'/'.get_week($value);
        $b[$key]['text']=$value.'/'.get_week($value);
        $b[$key]['children']=[['value'=>'8:30-9:00','text'=>'8:30-9:00'],['value'=>'9:00-9:30','text'=>'9:00-9:30'],['value'=>'9:30-10:00','text'=>'9:30-10:00'],['value'=>'10:00-10:30','text'=>'10:00-10:30'],['value'=>'10:30-11:00','text'=>'10:30-11:00'],['value'=>'11:00-11:30','text'=>'11:00-11:30'],['value'=>'11:30-12:00','text'=>'11:30-12:00'],['value'=>'14:00-14:30','text'=>'14:00-14:30'],['value'=>'14:30-15:00','text'=>'14:30-15:00'],['value'=>'15:00-15:30','text'=>'15:00-15:30'],['value'=>'15:30-16:00','text'=>'15:30-16:00'],['value'=>'16:00-16:30','text'=>'16:00-16:30'],['value'=>'16:30-17:00','text'=>'16:30-17:00']];
    };
    return $b;
}
/*
 * 返回输入日期星期几
 * @param $date 日期
 * */
function get_week($date){
    $date_str=date('Y-m-d',strtotime($date));
    $arr=explode("-", $date_str);
    $year=$arr[0];
    $month=sprintf('%02d',$arr[1]);
    $day=sprintf('%02d',$arr[2]);
    $hour = $minute = $second = 0;
    $strap = mktime($hour,$minute,$second,$month,$day,$year);
    $number_wk=date("w",$strap);
    $weekArr=array("周日","周一","周二","周三","周四","周五","周六");
    return $weekArr[$number_wk];

}


function createSign()
{
    $str = "0123456789";
    $sign = '';
    $length = strlen($str)-1;

    for($i=0; $i<4; $i++) {
        $num = rand(0,$length);
        $sign .= $str[$num];
    }

    return $sign;
}

function doGet($url)
{
    //初始化
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    // 执行后不直接打印出来
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, false);
    // 跳过证书检查
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    // 不从证书中检查SSL加密算法是否存在
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    //执行并获取HTML文档内容
    $output = curl_exec($ch);
    //释放curl句柄
    curl_close($ch);
    return $output;
}