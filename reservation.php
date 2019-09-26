<?php
include "pdo.php";
include "function.php";
session_start();

$action = isset($_GET['action'])?$_GET['action']:"error";

if ($action=='error'){

    echo json_encode(['code'=>400,'msg'=>'缺少必须参数！']);exit();
}

if ($action=='add'){
    $param = $_POST;

    if (empty($param['username'])){
        echo  json_encode(['code'=>400,'msg'=>'请输入姓名！']);exit();
    }
    if (empty($param['idcard'])){
        echo  json_encode(['code'=>400,'msg'=>'请输入身份证号！']);exit();
    }
    if (empty($param['time'])){
        echo  json_encode(['code'=>400,'msg'=>'请输入预约时间！']);exit();
    }
    if(empty($param['telphone'])){
        echo  json_encode(['code'=>400,'msg'=>'请输入正确的手机号码！']);exit();
    }
    if (empty($param['code'])){
        echo  json_encode(['code'=>400,'msg'=>'请输入正确的验证码！']);exit();
    }
    if (!isset($_SESSION[$param['telphone']])){
        echo  json_encode(['code'=>400,'msg'=>'您还没发送验证码！']);exit();
    }
    $tel = $_SESSION[$param['telphone']];
    $tel = isset($tel)?$tel:"";
    if ($tel!=$param['code']){
        echo  json_encode(['code'=>400,'msg'=>'验证码错误！']);exit();
    }

    $timess = explode('/',$param['time']);
    $dates = strtotime($timess[0]);
    $nowtimes = $timess[2];
    $Agreement = DB::getStmt("select * from  reservation where reservation_time=:reservation_time");

    $Agreement->bindParam(':reservation_time',$param['time']);
    $Agreement->execute();
    $a=$Agreement->fetch(PDO::FETCH_ASSOC);
    if ($a){
        echo  json_encode(['code'=>400,'msg'=>'请重新选择预约时间！']);
        exit();
    }



    $stmt = DB::getStmt("insert into reservation (username,idcard,reservation_time,telphone,addtime,date,times) values (?,?,?,?,?,?,?)");

    $stmt->bindValue(1,$param['username']);
    $stmt->bindValue(2,$param['idcard']);
    $stmt->bindValue(3,$param['time']);
    $stmt->bindValue(4,$param['telphone']);
    $stmt->bindValue(5,time());
    $stmt->bindValue(6,$dates);
    $stmt->bindValue(7,$nowtimes);
//执行预处理语句
    $stmt->execute();

    $insert_id = DB::getPdo()->lastInsertId();

    if($insert_id)
    {
        unset($_SESSION[$param['telphone']]);
        $successDate = 'submit_success.html?username='.$param['username'].'&idcard='.$param['idcard'].'&time='.$param['time'].'&telphone='.$param['telphone'];
        echo json_encode(['code'=>200,'msg'=>'预约成功！','urll'=>$successDate]);
    }
    else
    {
        echo json_encode(['code'=>400,'msg'=>'预约失败，联系管理员！']);
    }
//释放查询结果
    $stmt = null;
//关闭连接
    $pdo = null;

    exit();
}


if ($action=='send'){
    $codes = createSign();
    $telphone = isset($_POST['telphone'])?$_POST['telphone']:"";
    $url = 'http://mysms.house365.com:81/index.php/Interface/apiSendMobil/jid/148/depart/1/city/chuzhou/mobileno/'.$telphone.'/?msg=您的验证码是 【'.$codes.'】';
    $mas = doGet("$url");
    $_SESSION[$telphone] = $codes;
    if ($mas){
        echo  json_encode(['code'=>200,'msg'=>'短信已发送，请查收！']);
        exit();
    }else{
        echo  json_encode(['code'=>400,'msg'=>'发送失败，请联系客服！']);
        exit();
    }
}

if ($action=='configInit'){
    $obj = $_POST;
    $objData  = $_POST['obj'];
    $oneday   = strtotime($obj['oneday']);
    $Sevenday = strtotime($obj['Sevenday']);
    $betweenSql = "select reservation.reservation_time from  reservation where reservation.date  BETWEEN $oneday AND  $Sevenday";
    $Agreement = DB::getStmt($betweenSql);
    $Agreement->execute();
    $datas = $Agreement->fetchAll(PDO::FETCH_ASSOC);

    $tmp = array();
    if ($datas){
        foreach ($datas as $k=>$v){
            $divide = explode('/',$v['reservation_time']);
            $datas[$k]['text'] = $divide[0].'/'.$divide[1];
            $datas[$k]['children'] = $divide[2];
        }
        foreach ($objData as $key=>$val){
            $tmp[$key]['text']=$val['text'];
            $tmp[$key]['value']=$val['value'];
            foreach ($datas as $ks=>$vs){
                if ($val['text']==$vs['text']){
                    foreach ($val['children'] as $kk=>$vv){
                        if ($vv['text']==$vs['children']){
//                            array_splice($objData[$key]['children'], $kk, 1);
//                            $objData[$key]['children'][$kk]['value'] = '已经预约';
//                            $objData[$key]['children'][$kk]['text']  = '已经预约';
                            unset($objData[$key]['children'][$kk]);
                        }
                    }
                }
            }
            $objData[$key]['children'] = array_values($objData[$key]['children']);
        }
        echo json_encode($objData);exit();
    }
    echo json_encode($objData);
}










?>