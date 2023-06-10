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
    $errors = array();
    
    $act    = $data->act;
    
    try{
        if($act == 'insert'){
            // $ven_name_sub   = $data->ven_name_sub;

            isset($data->ven_name_sub) ?  $ven_name_sub = $data->ven_name_sub : array_push($errors,'ven_name_sub'); 
            isset($ven_name_sub->name) ?  $name = $ven_name_sub->name           : array_push($errors,'name'); 
            isset($ven_name_sub->ven_name_id) ?  $ven_name_id = $ven_name_sub->ven_name_id : array_push($errors,'ven_name_id'); 
            isset($ven_name_sub->price) ?  $price = (int)$ven_name_sub->price   : array_push($errors,'price'); 
            isset($ven_name_sub->color) ?  $color = $ven_name_sub->color        : array_push($errors,'color'); 
            isset($ven_name_sub->srt)   ?  $srt = (int)$ven_name_sub->srt       : array_push($errors,'ลำดับ'); 
            
            if(count($errors)>0){
                http_response_code(200);
                echo json_encode(array('status' => false, 'message' => $errors));
                exit;
            }

            $sql = "INSERT INTO ven_name_sub(name, ven_name_id, price, color, srt) 
                    VALUE(:name, :ven_name_id, :price, :color, :srt);";        
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
            // $ven_name_sub   = $data->ven_name_sub;
            // $id             = $ven_name_sub->id;
            // $name           = $ven_name_sub->name;
            // $price          = (int)$ven_name_sub->price;
            // $color          = $ven_name_sub->color;
            // $srt            = (int)$ven_name_sub->srt;

            isset($data->ven_name_sub)  ?  $ven_name_sub = $data->ven_name_sub : array_push($errors,'ven_name_sub'); 
            isset($ven_name_sub->id)    ?  $id = $ven_name_sub->id              : array_push($errors,'ven_name_sub'); 
            isset($ven_name_sub->name)  ?  $name = $ven_name_sub->name          : array_push($errors,'name'); 
            isset($ven_name_sub->ven_name_id) ?  $ven_name_id = $ven_name_sub->ven_name_id : array_push($errors,'ven_name_id'); 
            isset($ven_name_sub->price) ?  $price = (int)$ven_name_sub->price   : array_push($errors,'price'); 
            isset($ven_name_sub->color) ?  $color = $ven_name_sub->color        : array_push($errors,'color'); 
            isset($ven_name_sub->srt)   ?  $srt = (int)$ven_name_sub->srt       : array_push($errors,'ลำดับ'); 
            
            if(count($errors)>0){
                http_response_code(200);
                echo json_encode(array('status' => false, 'message' => $errors));
                exit;
            }

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

            $sql    = "SELECT vn.name AS vn_name, vns.name AS vns_name, vn.DN
                        FROM ven_name_sub AS vns
                        INNER JOIN ven_name AS vn ON vn.id = vns.ven_name_id
                        WHERE vns.id = :id
                        ";
            $query = $conn->prepare($sql);
            $query->bindParam(':id',$id, PDO::PARAM_INT);
            $query->execute();
            if($query->rowCount()){
                $res_vns = $query->fetch(PDO::FETCH_OBJ);    
                $sql    = "DELETE FROM ven_user                        
                            WHERE ven_name =:ven_name AND uvn =:uvn AND DN =:DN";
                $query = $conn->prepare($sql);
                $query->bindParam(':ven_name',$res_vns->vn_name, PDO::PARAM_STR);
                $query->bindParam(':uvn',$res_vns->vns_name, PDO::PARAM_STR);
                $query->bindParam(':DN',$res_vns->DN, PDO::PARAM_STR);
                $query->execute();

                $sql    = "DELETE FROM ven_name_sub WHERE id = $id";
                $conn->exec($sql);
                http_response_code(200);
                echo json_encode(array('status' => true, 'message' => 'DEL ok', 'resp'=>$res_vns));
                exit;                
            }


            http_response_code(200);
            echo json_encode(array('status' => false, 'message' => 'ไม่สำเร็จ'));
            exit;                
        }  
        
        
    }catch(PDOException $e){
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}



