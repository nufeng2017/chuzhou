<?php
include "pdo.php";

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
    if(!empty($param['telphone']) && !preg_match('/^1([0-9]{9})/',$param['telphone'])){
        echo  json_encode(['code'=>400,'msg'=>'请输入正确的手机号码！']);exit();
    }
    if (empty($param['code'])){
        echo  json_encode(['code'=>400,'msg'=>'请输入正确的验证码！']);exit();
    }

    $Agreement = DB::getStmt("select * from  reservation where reservation_time=:reservation_time");

    $Agreement->bindParam(':reservation_time',$param['time']);
    $Agreement->execute();
    $a=$Agreement->fetch(PDO::FETCH_ASSOC);
    if ($a){
        echo  json_encode(['code'=>400,'msg'=>'请重新选择预约时间！']);
        exit();
    }



    $stmt = DB::getStmt("insert into reservation (username,idcard,reservation_time,telphone,addtime) values (?,?,?,?,?)");

    $stmt->bindValue(1,$param['username']);
    $stmt->bindValue(2,$param['idcard']);
    $stmt->bindValue(3,$param['time']);
    $stmt->bindValue(4,$param['telphone']);
    $stmt->bindValue(5,time());
//执行预处理语句
    $insert_id = $stmt->execute();
//    $insert_id =  $stmt->lastInsertId();;
    if($insert_id)
    {
        echo '新增成功'.'<br>';
    }
    else
    {
        echo '新增失败'.'<br>';
    }
//释放查询结果
    $stmt = null;
//关闭连接
    $pdo = null;

    exit();



}










?>