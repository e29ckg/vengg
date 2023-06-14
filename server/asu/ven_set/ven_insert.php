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
        
    $act = $data->act;
    
    try{
        if($act == 'insert'){

            $id = time();
            $ven_date       = $data->ven_date;
            $user_id        = $data->uid;

            $ven_com_id     = $data->vc_id;
            $ven_com_idb    = $data->vc_id;
            $ven_month      = $data->ven_month;
            $vn_id          = $data->vn_id;
            $vns_id         = $data->vns_id;
            $DN             = $data->DN;
            
            $ven_name       = $data->ven_name;
            $ven_com_name   = $data->ven_name;
            $ven_com_name_all   = $data->ven_name;
            
            $u_role         = $data->u_role;
            $price          = $data->price;
            $color          = $data->color;
            $vn_srt         = $data->vn_srt;
            $vns_srt        = $data->vns_srt;
            $status         = 2;

            $update_at      = '';
            $create_at      = '';


            
          

            /** เช็ควันเวลาที่อยู่เวรไม่ได้ */  
            $ven_date_u1 = date("Y-m-d", strtotime('+1 day', strtotime($ven_date)));
            $ven_date_d1 = date("Y-m-d", strtotime('-1 day', strtotime($ven_date)));

            $sql_VU = "SELECT * 
                        FROM ven 
                        WHERE user_id = $user_id AND ven_date >= '$ven_date_d1' AND ven_date <= '$ven_date_u1' AND (status=1 OR status=2)";
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
                      
            
            $ref1           = generateRandomString();
            $ref2           =  $ref1;
            $status         = 2 ;
            $update_at      = Date("Y-m-d H:i:s");
            $create_at      = Date("Y-m-d H:i:s");

            $ven_time = '';

            /** หาเวลา ven_time  เรียงลำดับ */

            // $ven_time       = (string)$vn_srt.(string)$vns_srt;
            // if($DN == 'กลางวัน'){
            //     $ven_time = '08:30:'.$ven_time;
            // }else{
            //     $ven_time = '16:30:'.$ven_time;
            // }

            $DN == 'กลางวัน' ? $ven_time = '08:30:' : $ven_time = '16:30:';
            
            $ven_time .= (string)$vn_srt ;
            $ven_time .= (string)$vns_srt;
            
            $sql = "SELECT v.id 
                    FROM ven AS v
                    WHERE ven_month ='$ven_month' 
                    	AND vn_id = $vn_id
                        AND vns_id = $vns_id
                    	AND ven_com_idb = $ven_com_id
                        AND (v.`status`=1 OR v.`status`=2)";
            $query = $conn->prepare($sql);
            $query->execute();
            // $res_vcnt = $query->fetchAll(PDO::FETCH_OBJ);
            // $query->rowCount();
            // $s = '00';
            $s = (string)$query->rowCount();
            // $ven_time .= substr($s, -1); 
            // $ven_time = '08:30:10';
            $ven_time = date("h:i:s",strtotime($ven_time));


            http_response_code(200);
            echo json_encode(array('status' => true, 'message' =>$s));
            exit;

            /**end หาเวลา ven_time */

            // $ven_com_id = json_encode($ven_com_id);

            $sql = "INSERT INTO ven(id, ven_date, ven_time, DN, ven_month, ven_com_id, ven_com_idb, user_id, u_name, u_role, ven_name, ven_com_name, ven_com_num_all, ref1, ref2, price, `status`, update_at, create_at) 
                    VALUE(:id, :ven_date, :ven_time, :DN, :ven_month, :ven_com_id, :ven_com_idb, :user_id, :u_name, :u_role, :ven_name, :ven_com_name, :ven_com_num_all, :ref1, :ref2, :price, :status, :update_at, :create_at);";        
            $query = $conn->prepare($sql);
            $query->bindParam(':id',$id, PDO::PARAM_INT);
            $query->bindParam(':ven_date',$ven_date, PDO::PARAM_STR);
            $query->bindParam(':ven_time',$ven_time, PDO::PARAM_STR);
            $query->bindParam(':DN',$DN, PDO::PARAM_STR);
            $query->bindParam(':ven_month',$ven_month, PDO::PARAM_STR);
            $query->bindParam(':ven_com_id',$ven_com_id, PDO::PARAM_STR);
            $query->bindParam(':ven_com_idb',$ven_com_idb, PDO::PARAM_STR);
            $query->bindParam(':user_id',$user_id, PDO::PARAM_STR);
            $query->bindParam(':u_name',$u_name, PDO::PARAM_STR);
            $query->bindParam(':u_role',$u_role, PDO::PARAM_STR);
            $query->bindParam(':ven_name',$ven_name, PDO::PARAM_STR);
            $query->bindParam(':ven_com_name',$ven_com_name, PDO::PARAM_STR);
            $query->bindParam(':ven_com_num_all',$ven_com_num_all, PDO::PARAM_STR);
            $query->bindParam(':ref1',$ref1 , PDO::PARAM_STR);
            $query->bindParam(':ref2',$ref2 , PDO::PARAM_STR);
            $query->bindParam(':price',$price , PDO::PARAM_STR);
            $query->bindParam(':status',$status , PDO::PARAM_INT);
            $query->bindParam(':update_at',$create_at , PDO::PARAM_STR);
            $query->bindParam(':create_at',$create_at , PDO::PARAM_STR);
            // $query->execute();

            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => ' ok ', 'responseJSON' => $data));
            exit;                
        }    
        
        
    }catch(PDOException $e){
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}




