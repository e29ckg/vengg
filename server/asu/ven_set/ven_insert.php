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
            $u_role         = $data->u_role;
            $ven_month      = $data->ven_month;
            $DN             = $data->DN;
            $ven_name       = $data->ven_name; 
            $ven_com_id     = array();
            

             /** เช็ควันเวลาที่อยู่เวรไม่ได้ */  
             $ven_date_u1 = date("Y-m-d", strtotime('+1 day', strtotime($ven_date)));
             $ven_date_d1 = date("Y-m-d", strtotime('-1 day', strtotime($ven_date)));

            $sql_VU = "SELECT * FROM ven WHERE user_id = $user_id AND ven_date >= '$ven_date_d1' AND ven_date <= '$ven_date_u1' AND (status=1 OR status=2)";
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
                      
            /******************** เช็คคำสั่ง****************** */ 
            $sql_vcid = "SELECT id, ref, ven_com_name, ven_name, ven_com_num FROM ven_com WHERE ven_month = '$ven_month' AND ven_name = '$ven_name' LIMIT 1 ";
            $query_vcid = $conn->prepare($sql_vcid);
            $query_vcid->execute();
            $res_vcid = $query_vcid->fetch(PDO::FETCH_OBJ);

            if($query_vcid->rowCount() < 1){    
                http_response_code(200);
                echo json_encode(array('status' => false, 'message' => 'กรุณาออกคำสั่ง ' . $ven_name .' เดือน '.$ven_month , 'responseJSON' => $data));
                exit; 
            }

            if($res_vcid){
                array_push($ven_com_id,$res_vcid->id);
                $ven_com_idb        = $res_vcid->id;
                $r_ref              = $res_vcid->ref;
                $ven_com_name       = $res_vcid->ven_name;
                // $ven_com_name       = $res_vcid->ven_com_name;
                $ven_com_num_all    = $res_vcid->ven_com_num;
            }else{
                $ven_com_idb        = '';
                $r_ref              = '';
                $ven_com_name       = '';
                $ven_com_num_all    = '';
            } 

            
            /******************** เช็คคำสั่ง****************** */ 

            /**   หาชื่อ  */
            $sql_u = "SELECT fname, name, sname FROM profile WHERE user_id =:user_id LIMIT 1 ";
            $query_u = $conn->prepare($sql_u);
            $query_u->bindParam(':user_id',$user_id, PDO::PARAM_INT);
            $query_u->execute();
            $res_u = $query_u->fetch(PDO::FETCH_OBJ);            
            $u_name = $res_u->fname.$res_u->name. ' '.$res_u->sname;
            /**  end  หาชื่อ  */
            

            $ven_con_name   = '';
            $ref1           = generateRandomString();
            $ref2           =  $ref1;
            $price          = '';
            $status         = 2 ;
            $create_at      = Date("Y-m-d H:i:s");

            $ven_time = '';

            /** หาเวลา ven_time  เรียงลำดับ */
            $DN == 'กลางวัน' ? $ven_time = '08:30:' : $ven_time = '16:30:';
            $sql = "SELECT price, vn.srt AS vn_srt, vns.srt AS vns_srt
                        FROM ven_name AS vn
                        INNER JOIN ven_name_sub AS vns ON vns.ven_name_id = vn.id
                        WHERE vn.name = '$ven_name' AND vns.`name` = '$u_role'";  
            $query = $conn->prepare($sql);
            $query->execute();
            $res_vn = $query->fetch(PDO::FETCH_OBJ); 

            if($res_vn){
                $price    = $res_vn->price ;
                // $ven_time .= (string)$res_vn->vn_srt ;
                $ven_time .= (string)$res_vn->vns_srt;
                
                $sql = "SELECT id FROM ven WHERE u_role = '$u_role' AND ven_date = '$ven_date' AND DN = '$DN'";
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

            $ven_com_id = json_encode($ven_com_id);
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
            $query->execute();

            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => ' ok ', 'responseJSON' => $data));
            exit;                
        }    
        
        
    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}




