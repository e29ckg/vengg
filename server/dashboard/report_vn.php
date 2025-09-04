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
    $ven_id     = $data->vn_id;
    $vns_id     = $data->vns_id;
    $user_id    = $data->user_id;

    $datas = array();

    $vn_doc_name = '';
    // $ven_com_num = '';
    // $ven_com_date = '';
    // $ven_date_d = '';
    // $ven_date_m = '';
    // $ven_date_y = '';
    // $ven_date_time = '16:30';
    // $ven_date_next_d = '';
    // $ven_date_next_m = '';
    // $ven_date_next_y = '';
    // $ven_date_next_time = '08:30';
    // $users = [];

    // The request is using the POST method
    try {
        $sql = "SELECT 
                        v.id, v.ven_date, 
                        vn.name AS ven_com_name, 
                        vn.DN, 
                        p.fname, p.name, p.sname, p.dep, p.workgroup, 
                        vc.ven_com_num, vc.ven_com_date 
                FROM ven_name as vn
                WHERE vn.id = '$ven_id';

        $query = $conn->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);

        $sql = "SELECT 
                        v.id, v.ven_date, 
                        vn.name AS ven_com_name, 
                        vn.DN, 
                        p.fname, p.name, p.sname, p.dep, p.workgroup, 
                        vc.ven_com_num, vc.ven_com_date 
                FROM ven as v 
                INNER JOIN profile as p ON v.user_id = p.user_id
                INNER JOIN ven_com as vc ON vc.id = v.ven_com_idb
                INNER JOIN ven_name as vn ON v.vn_id = vn.id
                WHERE v.ven_date = '$ven_date' 
                        AND vn.DN = '$DN' 
                        AND (v.`status` =1 OR v.`status` =2)
                ORDER BY v.ven_time ASC";
        $query = $conn->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);

        if ($query->rowCount() > 0) {
            foreach ($result as $rs) {

                $ven_com_num = $rs->ven_com_num;
                $ven_com_date = $rs->ven_com_date;

                // if ($rs->workgroup == 'ผู้พิพากษา') {
                $users[] = array("name" => $rs->fname . $rs->name . ' ' . $rs->sname, "dep" => $rs->dep);
                // } else {
                // $users[1] = array("name" => $rs->fname . $rs->name . ' ' . $rs->sname, "dep" => $rs->dep);
                // }
            }
        }
        $ven_date_next = date('Y-m-d', strtotime($ven_date . ' +1 day'));

        $datas = array(
            "ven_com_num" => $ven_com_num,
            "ven_com_date" => DateThai_full($ven_com_date),
            "ven_date_d" => date_d($ven_date),
            "ven_date_m" => date_m($ven_date),
            "ven_date_y" => date_y($ven_date),
            "ven_date_time" => '16:30',
            "ven_date_next_d" => date_d($ven_date_next),
            "ven_date_next_m" => date_m($ven_date_next),
            "ven_date_next_y" => date_y($ven_date_next),
            "ven_date_next_time" => '08:30',
            "users" => $users
        );

        /**สร้างเอกสาร docx */
        $templateProcessor = new TemplateProcessor('../../uploads/template_docx/ven_jk_tm.docx'); //เลือกไฟล์ template ที่เราสร้างไว้
        $templateProcessor->setValue('ven_com_num', $ven_com_num);
        $templateProcessor->setValue('ven_com_date', $datas['ven_com_date']);
        $templateProcessor->setValue('ven_date_d', $datas['ven_date_d']);
        $templateProcessor->setValue('ven_date_m', $datas['ven_date_m']);
        $templateProcessor->setValue('ven_date_y', $datas['ven_date_y']);
        $templateProcessor->setValue('ven_date_next_d', $datas['ven_date_next_d']);
        $templateProcessor->setValue('ven_date_next_m', $datas['ven_date_next_m']);
        $templateProcessor->setValue('ven_date_next_y', $datas['ven_date_next_y']);
        $templateProcessor->setValue('name1', $users[0]['name']);
        $templateProcessor->setValue('name2', $users[1]['name']);
        $templateProcessor->setValue('name3', $users[2]['name']);
        $templateProcessor->setValue('name4', $users[3]['name']);
        $templateProcessor->setValue('name5', $users[4]['name']);
        $templateProcessor->setValue('dep1', $users[0]['dep']);
        $templateProcessor->setValue('dep2', $users[1]['dep']);
        $templateProcessor->setValue('dep3', $users[2]['dep']);
        $templateProcessor->setValue('dep4', $users[3]['dep']);
        $templateProcessor->setValue('dep5', $users[4]['dep']);
        $templateProcessor->saveAs('../../uploads/ven_jk.docx');


        http_response_code(200);
        echo json_encode(array(
            'status' => true,
            'message' => 'OK',
            'resp' => $datas
        ));
        exit;
    } catch (PDOException $e) {
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
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
