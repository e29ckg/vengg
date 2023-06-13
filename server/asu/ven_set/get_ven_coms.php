<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
// header("'Access-Control-Allow-Credentials', 'true'");
// header('Content-Type: application/javascript');
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";

$data = json_decode(file_get_contents("php://input"));

// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $ven_month = $data->ven_month; 
    $datas = array();

    try{
        $sql = "SELECT 
                -- 	vc.*,
                -- 	vn.*,
                    vc.id ,
                    vn.id AS vn_id,
                    vc.ven_com_num,
                    vc.ven_com_date,
                    vc.ven_month,
                    vc.`status`,
                    vc.vn_id,
                    vn.name,
                    vn.DN
                FROM ven_com AS vc
                INNER JOIN ven_name AS vn ON vc.vn_id = vn.id 
                WHERE ven_month = '$ven_month';";
        $query = $conn->prepare($sql);
        $query->execute();

        $result = $query->fetchAll(PDO::FETCH_OBJ);

        if($query->rowCount() > 0){                       
            // foreach($result as $rs){
            //     array_push($datas,array(
            //         'id'    => $rs->id,
            //         'name'  => $rs->name,
            //         'DN'  => $rs->DN,
            //         'srt'  => $rs->srt
            //     ));
            // }
            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'สำเร็จ', 'respJSON' => $result));
            exit;
        }
     
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'ไม่พบข้อมูล (คำสั่งเวร) '));
        exit;
    
    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}