<?php

use function PHPSTORM_META\type;

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
// header("'Access-Control-Allow-Credentials', 'true'");
// header('Content-Type: application/javascript');
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";

$data = json_decode(file_get_contents("php://input"));

$sms_err = array();
$datas = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // if($_SESSION['AD_ROLE'] != 9){
    //     http_response_code(200);
    //     echo json_encode(array('staus' => false, 'message' => 'ไม่มีสิทธิ์'));
    //     exit;
    // }

    // The request is using the POST method
    try{

        // $ven_month = $data->ven_month;
        // $sql = "UPDATE ven SET status = 1 WHERE status = 2 AND ven_month = '$ven_month'";
        // $query = $conn->prepare($sql);       
        // $query->execute();   
        
        /**เพิม google*/
        // $sql2 = "SELECT * FROM ven WHERE ven_month='$ven_month' 
        //         AND (status=1 OR status=2) 
        //         -- AND (gcal_id IS NULL OR gcal_id='')
        //         ORDER BY ven_date ASC, ven_time ASC";
        // $query_g = $conn->prepare($sql2);  
        // $query_g->execute();

        // if($query_g->rowCount()){


            // $result = $query_g->fetchAll(PDO::FETCH_OBJ);
            // $n = 0;
            // $ven_date = '';
            // $ven_time = '';
            // $vn = 10;
            // foreach($result as $rs){
            //     $rs->DN == 'กลางวัน' ? $ven_time = '08:30:' : $ven_time = '16:30:';
            //     if($ven_date == $rs->ven_date){
            //         $vn++;
            //     }else{
            //         $vn = 10;
            //     }
            //     $ven_date = $rs->ven_date;
            //     $ven_time .= (string)$vn;
                
            //     if(__GOOGLE_CALENDAR__ && ($rs->gcal_id == null || $rs->gcal_id == '')){
            //         $name = $rs->u_name;
            //         $start = $rs->ven_date.' '.$ven_time;
    
            //         $date = new DateTime($start);
            //         $start = $date->format(DateTime::ATOM);
    
            //         $res = json_decode(gcal_insert($name,$start));
            //         if($res){
            //             $gcal_id = $res->resp->id;               
            //             $sql = "UPDATE ven SET gcal_id =:gcal_id WHERE id = :id";    

            //             $query = $conn->prepare($sql);
            //             $query->bindParam(':gcal_id',$gcal_id, PDO::PARAM_STR);
            //             $query->bindParam(':id',$rs->id, PDO::PARAM_INT);
            //             $query->execute(); 
                        
            //             array_push($datas,array(
            //                 'id'    => $rs->id,
            //                 'gcal_id' => $res->resp->id        
            //             ));
            //         }
                                  
            //     }
            // }
        // }

        // if($query->rowCount()){
            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'สำเร็จ ','datas'=>$datas));
            exit;
        // } else {
        //     http_response_code(200);
        //     echo json_encode(array('status' => false, 'message' => 'ไม่มีรายการ update','datas'=>$datas));
        //     exit;
        // }

       
    }catch(PDOException $e){
        // echo "Faild to connect to database" . $e->getMessage();
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'ERROR เกิดข้อผิดพลาด..' . $e->getMessage()));
    }
}