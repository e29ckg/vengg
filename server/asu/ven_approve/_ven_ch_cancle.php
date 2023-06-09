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


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $data->id;

$datas = array();

    // The request is using the POST method
    try{
        $conn->beginTransaction();
        
        // $sql = "SELECT * FROM ven_change WHERE id=:id";
        // $query = $conn->prepare($sql);
        // $query->bindParam(':id',$id, PDO::PARAM_STR);
        // $query->execute();
        // $result = $query->fetch(PDO::FETCH_OBJ);
       
        $sql = "UPDATE ven_change as vc
        LEFT JOIN ven AS v1 ON v1.id = vc.ven_id1
        LEFT JOIN ven AS v2 ON v2.id = vc.ven_id2
        LEFT JOIN ven AS v1o ON v1o.id = vc.ven_id1_old
        LEFT JOIN ven AS v2o ON v2o.id = vc.ven_id2_old								
        SET 
            vc.`status` = 2,
            v1.`status` = 2,
            v2.`status` = 2,
            v1o.`status` = 4,
            v2o.`status` = 4
        WHERE vc.id = :id";
        $query2 = $conn->prepare($sql);
        $query2->bindParam(':id',$id, PDO::PARAM_STR);
        $query2->execute();
        $conn->commit();

      

        if($query2->rowCount()){                        //count($result)  for odbc

            // $sql = "UPDATE ven_change SET status = 2 WHERE id=:id";
            // $query = $conn->prepare($sql);
            // $query->bindParam(':id',$id, PDO::PARAM_STR);
            // $query->execute();
            
            // $sql = "UPDATE ven SET status = 2 WHERE id=:id";
            // $query = $conn->prepare($sql);
            // $query->bindParam(':id',$result->ven_id1, PDO::PARAM_STR);
            // $query->execute();

            // $sql = "UPDATE ven SET status = 2 WHERE id=:id";
            // $query = $conn->prepare($sql);
            // $query->bindParam(':id',$result->ven_id2, PDO::PARAM_STR);
            // $query->execute();
            
            // $sql = "UPDATE ven SET status = 4 WHERE id=:id";
            // $query = $conn->prepare($sql);
            // $query->bindParam(':id',$result->ven_id1_old, PDO::PARAM_STR);
            // $query->execute();
            
            // $sql = "UPDATE ven SET status = 4 WHERE id=:id";
            // $query = $conn->prepare($sql);
            // $query->bindParam(':id',$result->ven_id2_old, PDO::PARAM_STR);
            // $query->execute();
            
            
            // $conn->commit();
            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'สำเร็จ',));
            exit;
        }
     
        http_response_code(200);
        echo json_encode(array('false' => true, 'message' => 'ไม่พบข้อมูล '));
    
    }catch(PDOException $e){
        $conn->rollback();
        // echo "Faild to connect to database" . $e->getMessage();
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
    }
}