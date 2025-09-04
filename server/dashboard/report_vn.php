<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include '../../vendor/autoload.php';

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;

include "../connect.php";
include "../function.php";

$data = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ven_date   = $data->ven_date;
    $vn_id     = $data->vn_id;
    $vns_id     = $data->vns_id;
    $user_id    = $data->user_id;
    $datas = array();

    $docx_template = "";
    
    $sql = "SELECT id, word FROM ven_name WHERE id='$vn_id'";
    $query = $conn->prepare($sql);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);
    if ($query->rowCount() > 0) {
        $docx_template = $result->word;
    } else {
        $response = array(
            'status' => 'error',
            'message' => 'ไม่พบแบบฟอร์มรายงาน'
        );
        echo json_encode($response);
        exit;
    }
    if ($docx_template == '') {
        $response = array(
            'status' => 'error',
            'message' => 'ไม่พบแบบฟอร์มรายงาน'
        );
        echo json_encode($response);
        exit;
    }

    // The request is using the POST method
    try {
        // $sql = "SELECT 
        //                 v.id, v.ven_date, 
        //                 vn.name AS ven_com_name, 
        //                 p.fname, p.name, p.sname, p.dep, p.workgroup, 
        //                 vc.ven_com_num, vc.ven_com_date 
        //         FROM ven as v 
        //         INNER JOIN profile as p ON v.user_id = p.user_id
        //         INNER JOIN ven_com as vc ON vc.id = v.ven_com_idb
        //         INNER JOIN ven_name as vn ON v.vn_id = vn.id
        //         WHERE v.ven_date = '$ven_date' 
        //                 AND v.vn_id = '$vn_id'
        //                 AND (v.`status` =1 OR v.`status` =2)
        //         ORDER BY v.ven_time ASC";
        // $query = $conn->prepare($sql);
        // $query->execute();
        // $result = $query->fetchAll(PDO::FETCH_OBJ); 


    $docx_template_path = '../../uploads/template_docx/'.$docx_template;
    if (!file_exists($docx_template_path)) {
        $response = array(
            'status' => 'error',
            'message' => 'ไม่พบไฟล์แบบฟอร์มรายงาน'
        );
        echo json_encode($response);
        exit;
    }
    $reportDir = '../../uploads/report_docx/';

    // ตรวจสอบและสร้างโฟลเดอร์ถ้ายังไม่มี
    if (!is_dir($reportDir)) {
        mkdir($reportDir, 0755, true); // สร้างโฟลเดอร์พร้อมสิทธิ์ และสร้างโฟลเดอร์ซ้อนถ้ามี
    }

    $templateProcessor = new TemplateProcessor($docx_template_path);

    foreach ($datas as $key => $value) {
        $templateProcessor->setValue($key, $value);
    }

    $outputFileName = 'report_' . $vn_id . '_' . date('Ymd_His') . '.docx';
    $outputFilePath = $reportDir . $outputFileName;

    $templateProcessor->saveAs($outputFilePath);

    echo json_encode($outputFilePath);
    exit;

    
    } catch (PDOException $e) {
        $response = array(
            'status' => 'error',
            'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
        );
        echo json_encode($response);
        exit;
    }
}

function date_d($strDate)
{
    if ($strDate == '') {
        return "-";
    }
    $strDay = date("j", strtotime($strDate));

    return "$strDay";
}
function date_m($strDate)
{
    if ($strDate == '') {
        return "-";
    }
    $strMonth = date("n", strtotime($strDate));
    $strMonthCut = array(
        "",
        "มกราคม",
        "กุมภาพันธ์",
        "มีนาคม",
        "เมษายน",
        "พฤษภาคม",
        "มิถุนายน",
        "กรกฎาคม",
        "สิงหาคม",
        "กันยายน",
        "ตุลาคม",
        "พฤศจิกายน",
        "ธันวาคม"
    );

    $strMonthThai = $strMonthCut[$strMonth];
    return "$strMonthThai";
}
function date_y($strDate)
{
    if ($strDate == '') {
        return "-";
    }
    $strYear = date("Y", strtotime($strDate)) + 543;
    return "$strYear";
}
