<?php
include "../pdo.php";
header('Content-type:text/html; charset=utf-8');

$id = isset($_POST['id']) ? $_POST['id'] : "";
$action = isset($_POST['action']) ? $_POST['action']: "";
if (empty($id)){
    echo "缺少系统参数！";exit();
}
if ($action=='updateOne'){
    $stmt = DB::getStmt("select * from  reservation where id=:id");

    $stmt->bindParam(':id',$id);
    $stmt->execute();
    $one=$stmt->fetch(PDO::FETCH_ASSOC);

    if ($one){
        echo  json_encode(['code'=>200,'datas'=>$one]);exit();
    }else{
        echo  json_encode(['code'=>400,'datas'=>'错误提示！']);exit();
    }


}
