<?php
include "../pdo.php";
header('Content-type:text/html; charset=utf-8');
// 开启Session
session_start();

$contactStatus=array(
    0=>'',
    1=>'已联系',
    2=>'未联系'
);
// 首先判断Cookie是否有记住了用户信息
if (isset($_COOKIE['username'])) {
    # 若记住了用户信息,则直接传给Session
    $_SESSION['username'] = $_COOKIE['username'];
    $_SESSION['islogin'] = 1;
}
if (!isset($_SESSION['islogin'])) {
    echo  json_encode(['code'=>400,'msg'=>'请先登录！']);exit();
}

$username         = isset($_GET['username']) ? $_GET['username'] : '';
$idcard           = isset($_GET['idcard']) ? $_GET['idcard'] : '';
$reservation_time = isset($_GET['reservation_time']) ? $_GET['reservation_time'] : '';
$times            = isset($_GET['times']) ? $_GET['times'] : '';
$telphone         = isset($_GET['telphone']) ? $_GET['telphone'] : '';
$status           = isset($_GET['status']) ? $_GET['status'] : '';
$remark           = isset($_GET['remark']) ? $_GET['remark'] : '';
$action           = isset($_GET['action']) ? $_GET['action'] : 'selectall';
$id               = isset($_GET['id']) ? $_GET['id'] : '';

if ($action=='selectall'){

    $sql = 'select * from  reservation  ';
    $where = '  where 1=1  ';
    if (!empty($username)){
        $where.=" and username like '%".$username."%'";
    }
    if (!empty($idcard)){
        $where.=" and idcard='$idcard'";
    }

    if (!empty($reservation_time)){
        $where.=' and  reservation_time LIKE  "%'.$reservation_time.'%"';
    }
    if (!empty($telphone)){
        $where.=" and telphone='$telphone'";
    }

    $sqltotal = 'select * from reservation  '.$where;
    $totalData = DB::getStmt($sqltotal);
    $totalData->execute();
    $totalnum = $totalData->fetchAll(PDO::FETCH_ASSOC);
    $total = count($totalnum);

    $num = 20;
    $cpage = isset($_GET['page'])?$_GET['page']:1;
    $limit = isset($_GET['limit'])?$_GET['limit']:'';
    /**
    $pagenum = ceil($total/$num);
    $offset = ($cpage-1)*$num;
     **/
    $offset = ($cpage-1)*$limit;

    $where.="    ORDER BY addtime DESC limit $offset,$limit";

    $stmt = DB::getStmt($sql.$where);

    $stmt->execute();
    $dbDatas=$stmt->fetchAll(PDO::FETCH_ASSOC);

    /**
    $start = $offset+1;
    $end=($cpage==$pagenum)?$total : ($cpage*$num);//结束记录页
    $next=($cpage==$pagenum)? 0:($cpage+1);//下一页
    $prev=($cpage==1)? 0:($cpage-1);//前一页
     **/

    $dbData['code']=0;
    $dbData['msg']=200;
    $dbData['count']=$total;
    $dbData['data']=$dbDatas;
    echo json_encode($dbData);exit();


}



/**
 * 进行修改操作
 */
if ($action=='update'){
    if (empty($id)){
        echo json_encode(['code' => 400, 'msg' => '缺少系统参数！']);
        exit();
    }
    if (empty($username)){
        echo  json_encode(['code'=>400,'msg'=>'请输入姓名！']);
        exit();
    }
    if (empty($idcard)){
        echo  json_encode(['code'=>400,'msg'=>'请输入身份证号！']);
        exit();
    }
    $check = '/^(1(([35789][0-9])|(47)))\d{8}$/';
    if(!empty($telphone) && !preg_match($check, $telphone)){
        echo  json_encode(['code'=>400,'msg'=>'请输入正确的手机号码！']);
        exit();
    }
    if (empty($reservation_time)) {
        echo json_encode(['code' => 400, 'msg' => '请输入日期！']);
        exit();
    }
    if (empty($times)) {
        echo json_encode(['code' => 400, 'msg' => '请输入时间！']);
        exit();
    }
    $days = $reservation_time;
    $weekarray = ["星期日","星期一","星期二","星期三","星期四","星期五","星期六"];
    $week      = $weekarray[date("w",strtotime("$reservation_time"))];
    $reservation_time = $reservation_time.'/'.$week.'/'.$times;
    $days = strtotime($days);

    $Agreement = DB::getStmt("select * from  reservation where reservation_time=:reservation_time");

    $Agreement->bindParam(':reservation_time',$reservation_time);
    $Agreement->execute();
    $a=$Agreement->fetch(PDO::FETCH_ASSOC);
    if ($a){
        echo  json_encode(['code'=>400,'msg'=>'请重新选择预约时间！']);
        exit();
    }



    $sql = "update reservation set  username=:username,idcard=:idcard,reservation_time=:reservation_time,date=:date,times=:times,telphone=:telphone,status=:status,remark=:remark where `id`=:id;";
    $stmt = DB::getStmt($sql);
    //传参执行
    if($stmt->execute(['username'=>$username,'idcard'=>$idcard,'reservation_time'=>$reservation_time,'date'=>$days,'times'=>$times,'telphone'=>$telphone,'status'=>$status,'remark'=>$remark,'id'=>$id])){
        //是否修改成功
        if($stmt->rowCount() > 0 ){//受影响记录是否>0
            echo json_encode(['code' => 200, 'msg' => '修改成功！']);
            exit();
        }else{
            echo json_encode(['code' => 400, 'msg' =>$stmt->errorInfo()]);
            exit();
        }
    }else{
        echo json_encode(['code' => 400, 'msg' =>$stmt->errorInfo()]);
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