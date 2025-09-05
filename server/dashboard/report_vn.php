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
        'DateTomorrow_Th' => DateThai_full(date('Y-m-d', strtotime($ven_date . ' +1 day'))),
        'Just_Count' => '0',
        'Just1' => '',
        'Just2' => '',
        'Just3' => '',
        'Just4' => '',
        'Just1_Dep' => '',
        'Just2_Dep' => '',
        'Just3_Dep' => '',
        'Just4_Dep' => '',
        'CSM_Count' => '0',
        'CSM1' => '',
        'CSM2' => '',
        'CSM3' => '',
        'CSM4' => '',
        'CSM5' => '',
        'CSM6' => '',
        'CSM7' => '',
        'CSM8' => '',
        'CSM1_Dep' => '',
        'CSM2_Dep' => '',
        'CSM3_Dep' => '',
        'CSM4_Dep' => '',
        'CSM5_Dep' => '',
        'CSM6_Dep' => '',
        'CSM7_Dep' => '',
        'CSM8_Dep' => '',
        'Boss' => '',
        'Boss_Dep1' => '',
        'Boss_Dep2' => '',
        'Boss_Dep3' => '',
        'Po' => '',
        'Po_Dep1' => '',
        'Po_Dep2' => '',
        'Po_Dep3' => '',
        'ven_com_num' => '',
        'ven_com_date' => '', 


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

        //Court_Name Boss Po
        $sql = "SELECT id, name,dep,dep2,dep3,role FROM sign_name WHERE st = 1 ORDER BY FIELD(role, 'Court_Name', 'Chief_Judge', 'Director')";
        $query = $conn->prepare($sql);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_OBJ);
        foreach ($results as $row) {
            if ($row->role == 'Court_Name') {
                $datas['Court_Name'] = $row->name;
            }
        }
        foreach ($results as $row) {
            if ($row->role == 'Chief_Judge') {
                $datas['Boss'] = $row->name;
                $datas['Boss_Dep1'] = $row->dep;
                $datas['Boss_Dep2'] = $row->dep2;
                $datas['Boss_Dep3'] = $row->dep3;
            }
        }
        foreach ($results as $row) {
            if ($row->role == 'Director') {
                $datas['Po'] = $row->name;
                $datas['Po_Dep1'] = $row->dep;
                $datas['Po_Dep2'] = $row->dep2;
                $datas['Po_Dep3'] = $row->dep3;
            }
        }

        //ven_com_num ven_com_date
        $sql = "SELECT vc.ven_com_num, vc.ven_com_date 
                FROM ven AS v 
                JOIN ven_com AS vc ON v.ven_com_idb = vc.id 
                WHERE v.ven_date = :ven_date 
                AND v.vn_id = :vn_id 
                LIMIT 1";   
        $query = $conn->prepare($sql);
        $query->bindParam(':ven_date', $ven_date, PDO::PARAM_STR);
        $query->bindParam(':vn_id', $vn_id, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);
        if ($query->rowCount() > 0) {
            $datas['ven_com_num'] = $result->ven_com_num;
            $datas['ven_com_date'] = DateThai_full($result->ven_com_date);
        }


        //just and csm
        $sql = "SELECT v.id, v.user_id, CONCAT(p.fname,  p.name, ' ', p.sname) AS fullname, p.workgroup, p.dep
                FROM ven AS v
                JOIN profile AS p ON v.user_id = p.user_id
                WHERE ven_date = :ven_date
                AND vn_id = :vn_id and (v.`status` =1 OR v.`status` =2)
                ORDER BY v.ven_time ASC";

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
                $datas["Just{$index}_Dep"] = $row->dep;
                $index++;
            } elseif ($index_C <= 8 && $row->workgroup != 'ผู้พิพากษา') { // จำกัดแค่ CSM1-CSM8
                $datas["CSM{$index_C}"] = $row->fullname;
                $datas["CSM{$index_C}_Dep"] = $row->dep;
                $index_C++;
            }
        }

        $datas['Just_Count'] = strval($index - 1);
        $datas['CSM_Count'] = strval($index_C - 1);

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
        echo json_encode(['status' => 'success','message'=>'สร้างแบบฟอร์มเรียบร้อย', 'fileUrl' => $fileUrl, 'datas' => $datas]);

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
