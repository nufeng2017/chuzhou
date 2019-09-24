<?php
include "../pdo.php";

$user = isset($_POST['number']) ? $_POST['number'] : "";
$passworld = isset($_POST['password']) ? $_POST['password'] : "";
if (empty($user) || empty($passworld)){
    echo "请填写用户名和密码！";exit();
}

$stmt = DB::getStmt("select * from  user_login where username=:username and password=:password");

$stmt->bindParam(':username',$user);
$stmt->bindParam(':password',$passworld);
$stmt->execute();
$a=$stmt->fetch(PDO::FETCH_ASSOC);
if (empty($a)){
    echo "没有此用户！";
}else{
    echo "<script> window.location.href='/chuzhou/backstage/html/index.html';</script>";
}