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

    try{        
        $id     = $data->id;

        $sql = "SELECT * FROM ven_change 
                WHERE (ven_id1 = '$id' OR ven_id2='$id' OR ven_id1_old = '$id' OR ven_id2_old='$id') 
                AND (status=1 OR status=2)";
        $query = $conn->prepare($sql);
        $query->execute();
        $res = $query->fetch(PDO::FETCH_OBJ);

        if($query->rowCount()){

            http_response_code(200);
            echo json_encode(array('status' => false, 'message' => 'ไม่สามารถลบได้เนื่องจากมีรายชื่อในใบเปลี่ยนเวร'));
            exit;                

        }else{

            
            if(__GOOGLE_CALENDAR__){  

                $sql = "SELECT * FROM ven WHERE id = $id";
                $query = $conn->prepare($sql);
                $query->execute();

                $sql = "DELETE FROM ven WHERE id = $id";
                $conn->exec($sql);
                
                if($query->rowCount()){ 
                    $rs = $query->fetch(PDO::FETCH_OBJ);

                    $sql_V  = "SELECT * 
                                FROM ven 
                                WHERE gcal_id =:gcal_id 
                                AND (status=1 OR status=2)
                                ORDER BY ven_time ASC";
                    $query_V = $conn->prepare($sql_V);
                    $query_V->bindParam(':gcal_id', $rs->gcal_id, PDO::PARAM_STR);
                    $query_V->execute();
                                        
                    if($query_V->rowCount()){
                        $res_V = $query_V->fetchAll(PDO::FETCH_OBJ);
                        
                        /** เตรียมข้อมูลส่ง */
                        $name           = "(เวร)".$res_V[0]->ven_com_name;
                        $desc           = '';
                        
                        foreach($res_V as $rs){
                            $desc .= $rs->u_name."\n";                                                           
                        }        
                        
                        gcal_update($rs->gcal_id, $name, $desc, 1);  
                    }else{
                        gcal_remove($rs->gcal_id);    
                        
                    }                    
                }                
            }

            $sql = "DELETE FROM ven WHERE id = $id";
            if($conn->exec($sql)){
                http_response_code(200);
                echo json_encode(array(
                    'status' => true, 
                    'message' => 'DEL ok'
                    ));
                exit; 
            }

            http_response_code(200);
            echo json_encode(array(
                'status' => false, 
                'message' => 'ไม่มีการปรับปรุง'
                ));
            exit;   

        }

        
    }catch(PDOException $e){
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}




