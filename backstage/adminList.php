<?php
include "../pdo.php";

$username         = isset($_GET['username']) ? $_GET['username'] : '';
$idcard           = isset($_GET['idcard']) ? $_GET['idcard'] : '';
$reservation_time = isset($_GET['reservation_time']) ? $_GET['reservation_time'] : '';
$telphone         = isset($_GET['telphone']) ? $_GET['telphone'] : '';
$action           = isset($_GET['action']) ? $_GET['action'] : '';
$id               = isset($_TPOS['id']) ? $_POST['id'] : '';


$sql = 'select * from  reservation where 1=1 ';
if (!empty($username)){
    $sql.=" and username like '%".$username."%'";
}
if (!empty($idcard)){
    $sql.=" and idcard='$idcard'";
}

if (!empty($reservation_time)){
    $sql.=' and  reservation_time LIKE  "%'.$reservation_time.'%"';
}
if (!empty($telphone)){
    $sql.=" and telphone='$telphone'";
}


$totalData = DB::getStmt('select * from reservation;');
$totalData->execute();
$totalnum = $totalData->fetchAll(PDO::FETCH_ASSOC);
$total = count($totalnum);

$num = 20;
$cpage = isset($_GET['page'])?$_GET['page']:1;
$limit = isset($_GET['limit'])?$_GET['limit']:'';
//$pagenum = ceil($total/$num);
//$offset = ($cpage-1)*$num;

$offset = ($cpage-1)*$limit;

$sql.="    ORDER BY addtime DESC limit $offset,$limit";

$stmt = DB::getStmt($sql);

$stmt->execute();
$dbDatas=$stmt->fetchAll(PDO::FETCH_ASSOC);
$totala = count($dbDatas);
//$start = $offset+1;
//$end=($cpage==$pagenum)?$total : ($cpage*$num);//结束记录页
//$next=($cpage==$pagenum)? 0:($cpage+1);//下一页
//$prev=($cpage==1)? 0:($cpage-1);//前一页

$dbData['code']=0;
$dbData['msg']=200;
$dbData['count']=$totala;
$dbData['data']=$dbDatas;

echo json_encode($dbData);die;


/**
 * 进行修改操作
 */
if ($action=='update'){
    if (empty($username)){
        echo  json_encode(['code'=>400,'msg'=>'请输入姓名！']);
        exit();
    }
    if (empty($idcard)){
        echo  json_encode(['code'=>400,'msg'=>'请输入身份证号！']);
        exit();
    }
    if(!empty($telphone) && !preg_match('/^1([0-9]{9})/',$telphone)){
        echo  json_encode(['code'=>400,'msg'=>'请输入正确的手机号码！']);
        exit();
    }
    if (empty($reservation_time)) {
        echo json_encode(['code' => 400, 'msg' => '请输入时间！']);
        exit();
    }
    if (empty($id)){
        echo json_encode(['code' => 400, 'msg' => '缺少系统参数！']);
        exit();
    }
    $sql = "update reservation set  username=:username,idcard=:idcard,reservation_time=:reservation_time,telphone=:telphone where `id`=:id;";
    $stmt = DB::getStmt($sql);
    //传参执行
    if($stmt->execute(['username'=>$username,'idcard'=>$idcard,'reservation_time'=>$reservation_time,'telphone'=>$telphone,'id'=>$id])){
        //是否修改成功
        if($stmt->rowCount() > 0 ){//受影响记录是否>0
            echo '<h3>成功修改了',$stmt->rowCount(),'条数据</h3>';exit();
        }else{
            echo '无修改';
            print_r($stmt->errorInfo());//返回修改错误信息
        }
    }else{
        print_r($stmt->errorInfo());//返回执行错误信息
        exit();
    }

}


/**
 * 删除操作
 */
if ($action=='del' && !empty($id)) {
    $sql = "delete from reservation where id=?";
    $stmt = DB::getStmt($sql);
    $stmt->bindValue(1,$id);
    $stmt->execute();
    $affect_row = $stmt->rowCount();
    if($affect_row)
    {
        echo '删除成功'.'<br>';
    }
    else
    {
        echo '删除失败'.'<br>';
    }

}else{
    echo json_encode(['code'=>400,'msg'=>'缺少必要的参数！']);exit();
}