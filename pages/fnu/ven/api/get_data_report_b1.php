<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
// header("'Access-Control-Allow-Credentials', 'true'");
// header('Content-Type: application/javascript');
header("Content-Type: application/json; charset=utf-8");
date_default_timezone_set("Asia/Bangkok");

include 'vendor/autoload.php';
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;

include_once "dbconfig.php";
include_once "./function.php";

// $DN1_PRICE = 2500;  /** ผู้พิพากษากลางคืน   16:30:00    */
// $DN1_PRICE = 1200;  /** จนท กลางคืน      16:30:55    */
// $DN2_PRICE = 3000;  /** ผู้พิพากษากลางวัน   08:30:00    */
// $DN2_PRICE = 1500;  /** จนท กลางคืน      08:30:01, 08:30:11, 08:30:22    */

$_count = 0;
$price_dn1_all = 0;
$price_dn2_all = 0;
$error='';

// $ven_mounth = date("Y-m-d");
// $date_end = date('Y-m-d', strtotime('+7 days'));
// $ven_mounth = date_format($ven_mounth,"Y-m");
//action.php

$data = json_decode(file_get_contents("php://input"));

$VEN_COM_ID = date($data->ven_com_id);
$datas = array();

// http_response_code(200);
//         echo json_encode(array('status' => false, 'massege' => 'ไม่พบข้อมูล', 'responseJSON' => $data->month));
// exit;
try{    
    
    // $sql = "SELECT user_id,fname,name,sname,bank_account, bank_comment FROM `profile` ORDER BY st ASC";
    // $query = $dbcon->prepare($sql);
    // $query->execute();
    // $datas = $query->fetchAll(PDO::FETCH_OBJ);
    
    $sql = "SELECT ven_date, price FROM `ven` WHERE ven_com_idb ='$VEN_COM_ID' AND (status=1 OR status=2) GROUP BY ven_date;";
    $query = $dbcon->prepare($sql);
    $query->execute();
    $day = $query->fetchAll(PDO::FETCH_OBJ);
    $day_num = count($day);


    /** วันหยุด  $HLD */
    // $sql = "SELECT ven_date FROM `ven` WHERE ven_month = '$DATE_MONTH' AND DN = 'กลางวัน' ORDER BY `ven_date` ASC;";
    // $query = $dbcon->prepare($sql);
    // $query->execute();
    // $res_holiday = $query->fetchAll(PDO::FETCH_OBJ);
    // $HLD_count = count($res_holiday);

    // $sql = "SELECT * FROM ven_user GROUP BY user_id ORDER BY 'order' ASC";
    $sql = "SELECT * FROM profile WHERE status = 10 ORDER BY st ASC";
    $query = $dbcon->prepare($sql);
    $query->execute();
    $users = $query->fetchAll(PDO::FETCH_OBJ);

    if (count($users) > 0) {

    }
        http_response_code(200);
        echo json_encode(array('status' => true, 'massege' => 'ไม่พบข้อมูล', 'responseJSON' => $datas));
    
}catch(PDOException $e){
    echo "Faild to connect to database" . $e->getMessage();
    http_response_code(400);
    echo json_encode(array('status' => false, 'massege' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
}

