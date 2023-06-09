<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
// header("'Access-Control-Allow-Credentials', 'true'");
// header('Content-Type: application/javascript');
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";;

$data = json_decode(file_get_contents("php://input"));

// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = $data->id;

    $datas = array();

    try{
        $sql = "SELECT v.*, p.fname, p.name, p.sname 
        FROM ven as v 
        INNER JOIN `profile` as p ON v.user_id = p.user_id
        WHERE v.id = :id
        ORDER BY v.ven_date DESC
        LIMIT 1";
        $query = $conn->prepare($sql);
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);
     
        http_response_code(200);
        echo json_encode(array('status' => true, 'message' => 'ไม่พบข้อมูล ', 'respJSON' => $result));
        exit;
    
    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}