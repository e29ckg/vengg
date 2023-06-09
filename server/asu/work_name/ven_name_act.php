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

    $datas = array();
    $ven_name    = $data->ven_name; 
    $act         = $data->act;

    try{
        if($act == 'insert'){
            $name   = $ven_name->name;
            $srt    = $ven_name->srt;
            $DN     = $ven_name->DN;

            $sql = "INSERT INTO ven_name(name, DN, srt) VALUE(:name, :DN, :srt);";        
            $query = $conn->prepare($sql);
            $query->bindParam(':name',$name, PDO::PARAM_STR);
            $query->bindParam(':DN',$DN, PDO::PARAM_STR);
            $query->bindParam(':srt',$srt, PDO::PARAM_INT);
            $query->execute();

            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'ok', 'responseJSON' => $datas));
            exit;                
        }    
        if($act == 'update'){
            $id     = $ven_name->id;
            $name   = $ven_name->name;
            $DN     = $ven_name->DN;
            $srt    = $ven_name->srt;

            $sql = "UPDATE ven_name SET name =:name, DN=:DN, srt=:srt WHERE id = :id";   

            $query = $conn->prepare($sql);
            $query->bindParam(':name',$name, PDO::PARAM_STR);
            $query->bindParam(':DN',$DN, PDO::PARAM_STR);
            $query->bindParam(':srt',$srt, PDO::PARAM_INT);
            $query->bindParam(':id',$id, PDO::PARAM_INT);
            $query->execute();         

            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'ok', 'responseJSON' => $datas));
            exit;                
        }  
        if($act == 'delete'){
            $id     = $ven_name->id;
            
            $sql = "DELETE FROM ven_name WHERE id = $id";
            $conn->exec($sql);

            $sql = "DELETE FROM ven_name_sub WHERE ven_name_id = $id";
            $conn->exec($sql);

            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'DEL ok'));
            exit;                
        }  
        
        
    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}



