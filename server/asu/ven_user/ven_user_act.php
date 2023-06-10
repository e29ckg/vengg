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

    $datas  = array();    
    $act    = $data->act;

    try{
        if($act == 'insert'){
            $ven_user   = $data->ven_user; 
            $user_id    = $ven_user->user_id;

            $sql    = "SELECT fname, name, sname FROM profile WHERE user_id =:user_id";
            $query  = $conn->prepare($sql);
            $query->bindParam(':user_id',$user_id, PDO::PARAM_INT);
            $query->execute();
            $result = $query->fetch(PDO::FETCH_OBJ);

            $u_name     = $result->fname.$result->name.' '.$result->sname;
            $order      = $ven_user->order;
            $ven_name   = $ven_user->ven_name;
            $uvn        = $ven_user->uvn;
            $DN         = $ven_user->DN;
            $v_time     = Date("H:i:s", strtotime($ven_user->v_time));
            $price      = $ven_user->price;
            $color      = $ven_user->color;
            $comment    = "";
            $create_at  = Date("Y-m-d h:i:s");

            $sql = "INSERT INTO ven_user(user_id, u_name, `order`, ven_name, uvn, DN, v_time, price, color, comment, create_at) 
                    VALUE(:user_id, :u_name, :order, :ven_name, :uvn, :DN, :v_time, :price, :color, :comment, :create_at);";        
            $query = $conn->prepare($sql);
            $query->bindParam(':user_id',$user_id, PDO::PARAM_INT);
            $query->bindParam(':u_name',$u_name, PDO::PARAM_STR);
            $query->bindParam(':order',$order, PDO::PARAM_INT);
            $query->bindParam(':ven_name',$ven_name, PDO::PARAM_STR);
            $query->bindParam(':uvn',$uvn, PDO::PARAM_STR);
            $query->bindParam(':DN',$DN, PDO::PARAM_STR);
            $query->bindParam(':v_time',$v_time, PDO::PARAM_STR);
            $query->bindParam(':price',$price, PDO::PARAM_STR);
            $query->bindParam(':color',$color, PDO::PARAM_STR);
            $query->bindParam(':comment',$comment, PDO::PARAM_STR);
            $query->bindParam(':create_at',$create_at, PDO::PARAM_STR);
            $query->execute();

            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'ok', 'responseJSON' => $data));
            exit;                
        }    
        if($act == 'update'){
            $ven_user   = $data->ven_user; 
            $id         = $ven_user->id;
            $user_id    = $ven_user->user_id;
            $order      = $ven_user->order;

            $sql = "UPDATE ven_user SET user_id =:user_id, `order`=:order WHERE id = :id";   

            $query = $conn->prepare($sql);
            $query->bindParam(':user_id',$user_id, PDO::PARAM_INT);
            $query->bindParam(':order',$order, PDO::PARAM_STR);
            $query->bindParam(':id',$id, PDO::PARAM_INT);
            $query->execute();    

            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'ok', 'responseJSON' => $datas));
            exit;                
        }  
        if($act == 'delete'){
            $id  = $data->id;
            $sql = "DELETE FROM ven_user WHERE id = $id";
            $conn->exec($sql);

            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'DEL ok'));
            exit;                
        }  
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'no content'));
        exit;  
        
    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}



