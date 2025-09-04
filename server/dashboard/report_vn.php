<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include '../../vendor/autoload.php';

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\TemplateProcessor;
// use PhpOffice\PhpWord\Settings;
// Settings::setZipClass(Settings::PCLZIP); // fallback ถ้า ZipArchive มีปัญหา


include "../connect.php";
include "../function.php";

$data = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ven_date = $data->ven_date;
    $vn_id = $data->vn_id;
    $vns_id = $data->vns_id;
    $user_id = $data->user_id;

    $datas = [
        'Court_Name' => 'ศาล...',
        'Date_Th' => DateThai_full($ven_date),
        'Just1' => '',
        'Just2' => '',
        'Just3' => '',
        'Just4' => '',
        'CSM1' => '',
        'CSM2' => '',
        'CSM3' => '',
        'CSM4' => '',

    ];



    $docx_template = "";

    // The request is using the POST method
    try {
        $sql = "SELECT id, word FROM ven_name WHERE id = :vnid";
        $query = $conn->prepare($sql);
        $query->bindParam(':vnid', $vn_id, PDO::PARAM_INT);
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
        $docx_template_path = realpath('../../uploads/template_docx/' . $result->word);
        if (!$docx_template_path || !file_exists($docx_template_path)) {
            echo json_encode(['status' => 'error', 'message' => 'ไม่พบไฟล์ template']);
            exit;
        }

        $reportDir = '../../uploads/report_docx/';
        if (!is_dir($reportDir))
            mkdir($reportDir, 0755, true);
        array_map('unlink', glob($reportDir . '*'));

        //Court_Name
        $sql = "SELECT id, name,dep,dep2,dep3,role FROM sign_name";
        $query = $conn->prepare($sql);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_OBJ);
        foreach ($results as $row) {
            if ($row->role == 'Court_Name') {
                $datas['Court_Name'] = $row->name;
            }
        }

        //just
        $sql = "SELECT v.id, v.user_id, CONCAT(p.fname,  p.name, ' ', p.sname) AS fullname, p.workgroup
                FROM ven AS v
                JOIN profile AS p ON v.user_id = p.user_id
                WHERE ven_date = :ven_date
                AND vn_id = :vn_id ";

        $query = $conn->prepare($sql);
        $query->bindParam(':ven_date', $ven_date, PDO::PARAM_STR);
        $query->bindParam(':vn_id', $vn_id, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);

        $index = 1;
        $index_C = 1;
        foreach ($result as $row) {
            if ($index <= 4 && $row->workgroup == 'ผู้พิพากษา') { // จำกัดแค่ Just1-Just4                
                $datas["Just{$index}"] = $row->fullname;
                $index++;
            } elseif ($index_C <= 4) {
                $datas["CSM{$index_C}"] = $row->fullname;
                $index_C++;
            }
        }





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






        $templateProcessor = new TemplateProcessor($docx_template_path);

        foreach ($datas as $key => $value) {
            $templateProcessor->setValue($key, $value);
        }

        $outputFileName = 'report_' . $vn_id . '_' . date('Ymd_His') . '.docx';
        $outputFilePath = $reportDir . $outputFileName;

        $templateProcessor->saveAs($outputFilePath);
        $relativePath = str_replace('../../', '', $outputFilePath);
        $fileUrl = __FULLPATH__ . $relativePath;

        http_response_code(200);
        echo json_encode(['status' => 'success', 'fileUrl' => $fileUrl, 'datas' => $datas]);

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
