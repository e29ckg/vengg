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

            $sql = "SELECT * FROM ven WHERE id = $id";
            $query = $conn->prepare($sql);
            $query->execute();
            $rs = $query->fetch(PDO::FETCH_OBJ);

            $sql = "DELETE FROM ven WHERE id = $id";
            $conn->exec($sql);

            
            if(__GOOGLE_CALENDAR__){  
                                  

                    $sql_v  = "SELECT v.*,v.id, v.user_id, v.ven_com_idb, v.ven_date, v.ven_time, v.gcal_id, p.fname, p.name, p.sname, vn.`name` AS vn_name 
                                FROM ven AS v
                                INNER JOIN `profile` AS p ON v.user_id = p.id 
                                INNER JOIN ven_name AS vn ON v.vn_id = vn.id
                                WHERE  v.gcal_id =:gcal_id 
                                AND (v.status=1 OR v.status=2)
                                ORDER BY v.ven_time ASC";
                    $query_V = $conn->prepare($sql_v);
                    $query_V->bindParam(':gcal_id', $rs->gcal_id, PDO::PARAM_STR);
                    $query_V->execute();
                    $res_V = $query_V->fetchAll(PDO::FETCH_OBJ);
                                        
                    if($query_V->rowCount()){
                        
                        /** เตรียมข้อมูลส่ง */
                        $name           = "(เวร)".$res_V[0]->ven_com_name;
                        $desc           = '';
                        
                        foreach($res_V as $rs){
                            $desc .= $rs->fname.$rs->name.' '.$rs->sname."\n";                                                           
                        }        
                        
                        gcal_update($rs->gcal_id, $name, $desc, 1);  
                    }else{
                        gcal_remove($rs->gcal_id);    
                        
                    }                    
                               
            }
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

        
    }catch(PDOException $e){
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}




