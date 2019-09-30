<?php
include "pdo.php";
include "function.php";
session_start();

$action = isset($_GET['action'])?$_GET['action']:"error";
/**
 * 判断方法参数
 */
if ($action=='error'){

    echo json_encode(['code'=>400,'msg'=>'缺少必须参数！']);exit();
}
/**
 * 添加数据验证入库
 */
if ($action=='add'){
    $param = $_POST;
    $telphones = isset($param['telphone'])?$param['telphone']:'';
    if (empty($param['username'])){
        echo  json_encode(['code'=>400,'msg'=>'请输入姓名！']);exit();
    }
    if (empty($param['idcard'])){
        echo  json_encode(['code'=>400,'msg'=>'请输入身份证号！']);exit();
    }elseif (!preg_match('/^[1-9](\d{16}|\d{13})[0-9xX]$/', $param['idcard'])){
        echo  json_encode(['code'=>400,'msg'=>'请输入正确的身份证号！']);exit();
    }
    if (empty($param['time'])){
        echo  json_encode(['code'=>400,'msg'=>'请输入预约时间！']);exit();
    }
    $check = '/^(1(([35789][0-9])|(47)))\d{8}$/';
    if(empty($param['telphone'])){
        echo  json_encode(['code'=>400,'msg'=>'请输入正确的手机号码！']);exit();
    }elseif (!preg_match($check, $param['telphone'])){
        echo  json_encode(['code'=>400,'msg'=>'请输入正确的手机号码！']);exit();
    }
    if (empty($param['code'])){
        echo  json_encode(['code'=>400,'msg'=>'请输入正确的验证码！']);exit();
    }

    if (!isset($_SESSION['phone'.$telphones])){
        echo  json_encode(['code'=>400,'msg'=>'您还没发送验证码！']);exit();
    }
    $tel = $_SESSION['phone'.$telphones];
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
        echo  json_encode(['code'=>400,'msg'=>'时间已经被预约，请重新选择！']);
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
        unset($_SESSION['phone'.$telphones]);
        $notice = '恭喜您，预约成功！请于 '.$param['time'].'（预约时间）前往不动产办理中心办理业务，如未按预约时间到达，请现场重新排队取号。如有疑问可拨打0550-3055300咨询。感谢您的理解和支持！';
        $smsurl = 'http://mysms.house365.com:81/index.php/Interface/apiSendMobil/jid/148/depart/1/city/chuzhou/mobileno/'.$param['telphone'].'/?msg='.$notice;
        doGet("$smsurl");
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

/**
 * 验证码存库，进行60秒的发送限制  业务
 */
if ($action=='send'){
    $codes = createSign();
    $telphone = isset($_POST['telphone'])?$_POST['telphone']:"";
    if(empty($telphone)){
        echo  json_encode(['code'=>400,'msg'=>'请输入手机号码！']);exit();
    }elseif (!preg_match('/^(1(([35789][0-9])|(47)))\d{8}$/', $telphone)){
        echo  json_encode(['code'=>400,'msg'=>'请输入正确的手机号码！']);exit();
    }
    $now = time();
    $iscode = DB::getStmt("select max(sendtime) as send_time from  code_log where telphone=:telphone");
    $iscode->bindParam(':telphone',$telphone);
    $iscode->execute();
    $is_code=$iscode->fetch(PDO::FETCH_ASSOC);
    if (!empty($is_code) && $now-$is_code['send_time']<60){
        echo  json_encode(['code'=>400,'msg'=>'请勿频繁发送验证码！']);exit();
    }else if (!empty($is_code) && $now-$is_code['send_time']>60){
        $sql = "delete from code_log where telphone=?";
        $stmt = DB::getStmt($sql);
        $stmt->bindValue(1,$telphone);
        $stmt->execute();
    }

    $url = 'http://mysms.house365.com:81/index.php/Interface/apiSendMobil/jid/148/depart/1/city/chuzhou/mobileno/'.$telphone.'/?msg=您的验证码是 【'.$codes.'】';
    $mas = doGet("$url");
    if ($mas){
        $_SESSION['phone'.$telphone] = $codes;
        $stmtcode = DB::getStmt("insert into code_log  (code,sendtime,telphone) values (?,?,?)");
        $stmtcode->bindValue(1,$codes);
        $stmtcode->bindValue(2,time());
        $stmtcode->bindValue(3,$telphone);
        $stmtcode->execute();
        echo  json_encode(['code'=>200,'msg'=>'短信已发送，请查收！','datass'=>$mas]);
        exit();
    }else{
        echo  json_encode(['code'=>400,'msg'=>'发送失败，请联系客服！','datass'=>$mas]);
        exit();
    }
}

/**
 * 进入页面加载系统数据
 */
if ($action=='configInit'){
    $obj = $_POST;
    $objData  = $_POST['obj'];
    $oneday   = strtotime($obj['oneday']);
    $Sevenday = strtotime($obj['Sevenday']);
    $betweenSql = "select reservation.reservation_time from  reservation where reservation.date  BETWEEN $oneday AND  $Sevenday";
    $Agreement = DB::getStmt($betweenSql);
    $Agreement->execute();
    $datas = $Agreement->fetchAll(PDO::FETCH_ASSOC);

    if ($datas){
        foreach ($datas as $k=>$v){
            $divide = explode('/',$v['reservation_time']);
            $datas[$k]['text'] = $divide[0].'/'.$divide[1];
            $datas[$k]['children'] = $divide[2];
        }
        foreach ($objData as $key=>$val){
            foreach ($datas as $ks=>$vs){
                if ($val['text']==$vs['text']){
                    foreach ($val['children'] as $kk=>$vv){
                        if ($vv['text']==$vs['children']){
                            unset($objData[$key]['children'][$kk]);
                        }
                    }
                }
            }
            if (empty($objData[$key]['children'])){
                unset($objData[$key]);
                continue;
            }
            $objData = array_values($objData);
            $objData[$key]['children'] = array_values($objData[$key]['children']);
        }


        echo json_encode($objData);exit();
    }
    echo json_encode($objData);
}










?>