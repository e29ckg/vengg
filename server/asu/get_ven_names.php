<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
// header("'Access-Control-Allow-Credentials', 'true'");
// header('Content-Type: application/javascript');
header("Content-Type: application/json; charset=utf-8");

include "../connect.php";
include "../function.php";


// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $datas = array();

    try{
        // $sql = "SELECT * FROM ven_name ORDER BY srt ASC";
        $sql = "SELECT 
                    ven_name.name as vn_name, 
                    ven_name.DN as vns_DN, 
                    ven_name_sub.name as vns_name, 
                    ven_name_sub.color as color, 
                    ven_name_sub.price as price, 
                    ven_name_sub.id as vns_id,
                    ven_name.id as id,
                    ven_name.srt as vn_srt,
                    ven_name_sub.srt as vns_srt
                FROM ven_name 
                INNER JOIN ven_name_sub
                ON ven_name.id = ven_name_sub.ven_name_id
                ORDER BY ven_name.srt ASC, vns_srt ASC";
        $query = $conn->prepare($sql);
        $query->execute();
        $datas = $query->fetchAll(PDO::FETCH_OBJ);

        if($query->rowCount() > 0){                        //count($result)  for odbc
            
            http_response_code(200);
            echo json_encode(array(
                'status' => true, 
                'message' => 'สำเร็จ', 
                'respJSON' => $datas
            ));
            exit;
        }
     
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'ไม่พบข้อมูล '));
        exit;
    
    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}