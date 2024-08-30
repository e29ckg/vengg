<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");
date_default_timezone_set("Asia/Bangkok");



include 'vendor/autoload.php';

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;

include_once "dbconfig.php";
include_once "function.php";

/** ----------------------------  a1 ใบขวาง กลางคืน ---------------------------*/

$DN_D_PRICE_DAY = 0;
$DN_N_PRICE_DAY = 0;

$_count = 0;
$price_total_all = 0;
$time = '';
$error = '';
$datas = array();


$data = json_decode(file_get_contents("php://input"));
$ven_com_id = date($data->ven_com_id);

$HOLIDAY = [];

try {

    $court_name = '';
    $court_name_full = '';

    $sql = "SELECT 
                    vn.`name` AS vn_name,
                    vc.*
            FROM ven_com AS vc
            INNER JOIN ven_name AS vn ON vc.vn_id = vn.id 
            WHERE vc.id = :ven_com_id";
    $query = $conn->prepare($sql);
    $query->bindParam(':ven_com_id', $ven_com_id, PDO::PARAM_STR);
    $query->execute();
    $ven_com_num  = $query->fetch(PDO::FETCH_OBJ);
    $DATE_MONTH = $ven_com_num->ven_month;

    $sql = "SELECT 
                    v.ven_date 
            FROM `ven` AS v
            INNER JOIN ven_com AS vc ON v.ven_com_idb = vc.id 
            WHERE vc.ven_month = :date_month 
            GROUP BY v.ven_date 
            ORDER BY v.ven_date";
    $query = $conn->prepare($sql);
    $query->bindParam(':date_month', $DATE_MONTH, PDO::PARAM_STR);
    $query->execute();
    $days = $query->fetchAll(PDO::FETCH_OBJ);
    $day_a = array();
    foreach ($days as $ds) {
        array_push($day_a, $ds->ven_date);
    }

    $day_num = count($days);

    /** วันหยุด  $HLD */
    $sql = "SELECT 
                    v.ven_date 
            FROM  `ven` AS v
            INNER JOIN ven_com AS vc ON v.ven_com_idb = vc.id  
            INNER JOIN ven_name AS vn ON v.vn_id = vn.id  
            WHERE vc.ven_month = :date_month 
            AND vn.DN = 'กลางวัน' 
            GROUP BY v.`ven_date`";

    $query = $conn->prepare($sql);
    $query->bindParam(':date_month', $DATE_MONTH, PDO::PARAM_STR);
    $query->execute();
    $res_holiday = $query->fetchAll(PDO::FETCH_OBJ);
    $HLD = [];
    foreach ($res_holiday as $RH) {
        $HLD[] = $RH->ven_date;
    }


    /** vens */
    $sql = "SELECT 
                    v.*,
                    vn.DN,
                    vns.price
            FROM `ven` AS v 
            INNER JOIN ven_name AS vn ON v.vn_id = vn.id
            INNER JOIN ven_name_sub AS vns ON v.vns_id = vns.id
            WHERE v.ven_com_idb = :ven_com_id 
                AND (v.status = 1 OR v.status = 2)";
    $query = $conn->prepare($sql);
    $query->bindParam(':ven_com_id', $ven_com_id, PDO::PARAM_INT);
    $query->execute();
    $vens = $query->fetchAll(PDO::FETCH_OBJ);
    

    /** user */
    $sql = "SELECT * FROM profile ORDER BY st ASC";
    $query = $conn->prepare($sql);
    $query->execute();
    $users = $query->fetchAll(PDO::FETCH_OBJ);

    $sql = "SELECT * FROM sign_name WHERE st = 1 AND role='Court_Name'";
    $query = $conn->prepare($sql);
    $query->execute();
    $res_cort_name = $query->fetch(PDO::FETCH_OBJ);

    if($query->rowCount()){
        $court_name = $res_cort_name->name;
        $court_name_full = $res_cort_name->dep;
    } 


    if (count($users) > 0) {
        foreach ($users as $user) {
            $price  = 0;
            $work_day   = array();
            $price_one = 0;
            $weekdays = 0;
            $holiday = 0;
            $price_all = 0;

            foreach ($vens as $ven) {
                if ($user->user_id == $ven->user_id) {
                    if ($ven->price > 0) {
                        $price_one = $ven->price;
                        $price += $ven->price;

                        if (ck_holiday($ven->ven_date, $HLD)) {
                            $holiday++;
                        } else {
                            $weekdays++;
                        }
                        
                        array_push($work_day, $ven->ven_date);
                    }
                }
            }


            if ($price > 0) {

                $price_total_all += $price_one * ($weekdays + $holiday);
                array_push($datas, array(
                    'user_id'   => $user->user_id,
                    'name'      => $user->fname . $user->name . ' ' . $user->sname,
                    'bank_account'  => $user->bank_account,
                    'bank_comment'  => $user->bank_comment,
                    'phone'     => $user->phone,
                    'work_day'  => $work_day,
                    'price_one' => Num_f($price_one),
                    'weekdays'  => $weekdays,
                    'holiday'   => $holiday,
                    'price_all' => $price_one * ($weekdays + $holiday),

                ));
            }
        }
    }

    $ven_com_date = DateThai_full($ven_com_num->ven_com_date);
    http_response_code(200);
    echo json_encode(array(
        'status' => true,
        'message' => 'ok',
        'month' => DateThai_ym($DATE_MONTH),
        'ven_com_num' => $ven_com_num->ven_com_num,
        'ven_com_date' => $ven_com_date,
        'ven_com_name' => $ven_com_num->vn_name,
        'price_all' => $price_total_all,
        'price_all_text' => ReadNumber($price_total_all) . 'บาทถ้วน',
        // 'error'=>$error,
        'day_num' => count($days),
        'court_name' => $court_name,
        'court_name_full' => $court_name_full,
        'day' => $day_a,
        'holiday' => $HLD,
        'datas' => $datas
    ));
    exit;

} catch (PDOException $e) {
    http_response_code(400);
    echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
    exit;
}
