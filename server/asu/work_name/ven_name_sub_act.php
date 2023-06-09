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
    $act    = $data->act;
    
    try{
        if($act == 'insert'){
            $ven_name_sub   = $data->ven_name_sub;
            $name           = $ven_name_sub->name;
            $ven_name_id    = $ven_name_sub->ven_name_id;
            $price          = $ven_name_sub->price;
            $color          = $ven_name_sub->color;
            $srt            = $ven_name_sub->srt;

            $sql = "INSERT INTO ven_name_sub(name, ven_name_id, price, color, srt) VALUE(:name, :ven_name_id, :price, :color, :srt);";        
            $query = $conn->prepare($sql);
            $query->bindParam(':name', $name, PDO::PARAM_STR);
            $query->bindParam(':ven_name_id', $ven_name_id, PDO::PARAM_INT);
            $query->bindParam(':price', $price, PDO::PARAM_INT);
            $query->bindParam(':color', $color, PDO::PARAM_STR);
            $query->bindParam(':srt', $srt, PDO::PARAM_INT);
            $query->execute();

            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'ok', 'responseJSON' => $data));
            exit;                
        }    
        if($act == 'update'){
            $ven_name_sub   = $data->ven_name_sub;
            $id             = $ven_name_sub->id;
            $name           = $ven_name_sub->name;
            $price          = $ven_name_sub->price;
            $color          = $ven_name_sub->color;
            $srt            = $ven_name_sub->srt;

            $sql = "UPDATE ven_name_sub SET name =:name, price=:price, color=:color, srt=:srt WHERE id = :id";   

            $query = $conn->prepare($sql);
            $query->bindParam(':name',$name, PDO::PARAM_STR);
            $query->bindParam(':price',$price, PDO::PARAM_INT);
            $query->bindParam(':color',$color, PDO::PARAM_INT);
            $query->bindParam(':srt',$srt, PDO::PARAM_INT);
            $query->bindParam(':id',$id, PDO::PARAM_INT);
            $query->execute();         

            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'ok', 'responseJSON' => $datas));
            exit;                
        }  
        if($act == 'delete'){

            $id     = $data->id;
            $sql = "DELETE FROM ven_name_sub WHERE id = $id";
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



