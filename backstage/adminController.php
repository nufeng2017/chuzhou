<?php
include "../pdo.php";
header('Content-type:text/html; charset=utf-8');
// 开启Session
session_start();

$user = isset($_POST['number']) ? $_POST['number'] : "";
$passworld = isset($_POST['password']) ? md5($_POST['password']) : "";
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
    //用户名和密码都正确,将用户信息存到Session中
    $_SESSION['username'] = $a['username'];
    $_SESSION['islogin'] = 1;
    setcookie('username', $a['username'], time()+1*24*60*60);
    echo "<script> window.location.href='./html/index.html';</script>";
}