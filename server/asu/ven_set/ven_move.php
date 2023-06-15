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
    
    $id     = $data->id;
    $date_s = explode("T", $data->start);
    $ven_date   = $date_s[0];

    $datas = array();
    try{

        $sql = "SELECT * FROM ven WHERE id = $id";
        $query = $conn->prepare($sql);
        $query->execute();
        $res_v = $query->fetch(PDO::FETCH_OBJ);

        $user_id = $res_v->user_id;
        $DN      = $res_v->DN;

        $ven_date_u1 = date("Y-m-d", strtotime('+1 day', strtotime($ven_date)));
        $ven_date_d1 = date("Y-m-d", strtotime('-1 day', strtotime($ven_date)));

        $sql_VU = "SELECT * FROM ven WHERE user_id = $user_id AND ven_date >= '$ven_date_d1' AND ven_date >= '$ven_date_d1' AND (status=1 OR status=2)";
        $query_VU = $conn->prepare($sql_VU);
        $query_VU->execute();
        $res_VU = $query_VU->fetchAll(PDO::FETCH_OBJ);

        if($query_VU->rowCount()){
            foreach($res_VU as $ru){
                if($ru->ven_date == $ven_date){
                    http_response_code(200);
                    echo json_encode(array('status' => false, 'message' => 'วันนี้มีเวรอยู่แล้ว'));
                    exit;
                }
                if($DN == 'กลางวัน' && $ru->ven_date == $ven_date_d1 && $ru->DN == 'กลางคืน'){
                    http_response_code(200);
                    echo json_encode(array('status' => false, 'message' => $ven_date_d1.' มีเวร'));
                    exit;
                }
                if($DN == 'กลางคืน'  && $ru->ven_date == $ven_date_u1 && $ru->DN == 'กลางวัน'){
                    http_response_code(200);
                    echo json_encode(array('status' => false, 'message' => $ven_date_u1.' มีเวร'));
                    exit;
                }
                
            }

        }   
        
        $update_at      = Date("Y-m-d H:i:s");

        $sql = "UPDATE ven SET ven_date =:ven_date, update_at=:update_at WHERE id = :id";        
        $query = $conn->prepare($sql);
        $query->bindParam(':ven_date',$ven_date, PDO::PARAM_STR);
        $query->bindParam(':update_at',$update_at, PDO::PARAM_STR);
        $query->bindParam(':id',$id, PDO::PARAM_INT);
        $query->execute();
        
        /**  เรียงลำดับเวลา ven_time  */        
        $sql = "SELECT 
                    ven.id,
                    ven.u_role,
                    ven.ven_time,	
                    ven_name.srt AS vn_srt,
                    ven_name_sub.srt AS vns_srt
                FROM ven
                INNER JOIN ven_name ON ven.vn_id = ven_name.id
                INNER JOIN ven_name_sub ON ven.vns_id = ven_name_sub.id
                WHERE ven_date='$ven_date' 
                    AND (ven.`status` = 1 OR ven.`status` = 2)
                ORDER BY 
                    vn_srt ASC,
                    vns_srt ASC,
                    update_at ASC";

        $query = $conn->prepare($sql);
        $query->execute();
        $hours = 8;
        $seconds = 0;
        foreach ($query->fetchAll(PDO::FETCH_OBJ) as $rs) {
            if ($DN == 'กลางคืน') {
                $hours = 16;
            }
            ++$seconds;
            $ven_time = date("H:i:s", mktime($hours, 30, $seconds));

            $sql = "UPDATE ven SET ven_time = '$ven_time' WHERE id=$rs->id";
            $conn->exec($sql);
        }
        /**end เรียงลำดับเวลาา ven_time */

        http_response_code(200);
        echo json_encode(array('status' => true, 'message' => 'สำเร็จ'));
        exit;
        
    
    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'massege' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}