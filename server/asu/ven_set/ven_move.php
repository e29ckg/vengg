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

    
        /** หาเวลา ven_time  เรียงลำดับ */
        $DN == 'กลางวัน' ? $ven_time = '08:30:' : $ven_time = '16:30:';
        $sql = "SELECT price, vn.srt AS vn_srt, vns.srt AS vns_srt
                    FROM ven_name AS vn
                    INNER JOIN ven_name_sub AS vns ON vns.ven_name_id = vn.id
                    WHERE vn.name = '$res_v->ven_name' AND vns.`name` = '$res_v->u_role'";  
        $query = $conn->prepare($sql);
        $query->execute();
        $res_vn = $query->fetch(PDO::FETCH_OBJ); 

        if($res_vn){
            $price    = $res_vn->price ;
            // $ven_time .= (string)$res_vn->vn_srt ;
            $ven_time .= (string)$res_vn->vns_srt;
            
            $sql = "SELECT id FROM ven WHERE u_role = '$res_v->u_role' AND ven_date = '$res_v->ven_date' AND DN = '$DN' ORDER BY ven_time ASC";
            $query = $conn->prepare($sql);
            $query->execute();
            $res_vcnt = $query->fetchAll(PDO::FETCH_OBJ);
            // $s = '00';
            $s = (string)count($res_vcnt) ;
            $ven_time .= substr($s, -1); 

        }else{
            $ven_time .= '00';
        }
        /**end หาเวลา ven_time */


        $sql = "UPDATE ven SET ven_date =:ven_date, ven_time =:ven_time WHERE id = :id";        
        $query = $conn->prepare($sql);
        $query->bindParam(':ven_date',$ven_date, PDO::PARAM_STR);
        $query->bindParam(':ven_time',$ven_time, PDO::PARAM_STR);
        $query->bindParam(':id',$id, PDO::PARAM_INT);
        $res = $query->execute();

        http_response_code(200);
        echo json_encode(array('status' => true, 'message' => 'สำเร็จ', 'respJSON' => $res));
        exit;
        
    
    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'massege' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}