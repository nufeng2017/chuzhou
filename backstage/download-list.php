<?php
include "../pdo.php";
include 'phpexcel/Classes/PHPExcel.php';

$Status = [
    0 => '无',
    1 => '已联系',
    2 => '未联系',
];

$username         = isset($_GET['username']) ? $_GET['username'] : '';
$idcard           = isset($_GET['idcard']) ? $_GET['idcard'] : '';
$reservation_time = isset($_GET['reservation_time']) ? $_GET['reservation_time'] : '';
$telphone         = isset($_GET['telphone']) ? $_GET['telphone'] : '';

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
$stmt = DB::getStmt($sql);
$stmt->execute();
$dbDatas=$stmt->fetchAll(PDO::FETCH_ASSOC);
//转换时间戳
foreach ($dbDatas as $k => $v)
{
    $dbDatas[$k]['addtime'] =  date('Y-m-d H:i:s',$v['addtime']);
    $dbDatas[$k]['status'] =  $Status[$v['status']];
}

//开始导出
$excel = new PHPExcel();
$excel->setActiveSheetIndex(0);
$excel->getActiveSheet()->setCellValue('A1', '名称');
$excel->getActiveSheet()->setCellValue('B1', '身份证');
$excel->getActiveSheet()->setCellValue('C1', '预约时间');
$excel->getActiveSheet()->setCellValue('D1', '手机号');
$excel->getActiveSheet()->setCellValue('E1', '状态');
$excel->getActiveSheet()->setCellValue('F1', '备注');
$excel->getActiveSheet()->setCellValue('G1', '添加时间');

foreach ($dbDatas as $key => $value) {
    $poi = $key + 2;
    $excel->getActiveSheet()->setCellValueExplicit('A' . $poi, $value['username']);
    $excel->getActiveSheet()->setCellValueExplicit('B' . $poi, $value['idcard']);
    $excel->getActiveSheet()->setCellValueExplicit('C' . $poi, $value['reservation_time']);
    $excel->getActiveSheet()->setCellValueExplicit('D' . $poi, $value['telphone']);
    $excel->getActiveSheet()->setCellValueExplicit('E' . $poi, $value['status']);
    $excel->getActiveSheet()->setCellValueExplicit('F' . $poi, $value['remark']);
    $excel->getActiveSheet()->setCellValueExplicit('G' . $poi, $value['addtime']);
}


$file='数据导出';
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="' . $file . '.xlsx');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
$objWriter->save('php://output');
exit;
