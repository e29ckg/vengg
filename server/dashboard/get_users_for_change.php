<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../connect.php";
include "../function.php";

$data = json_decode(file_get_contents("php://input"));

// ยกให้
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try{
        $datas = array();

        $data_event = $data->data_event;

        $u_name = $data_event->u_name;
        $user_id = $data_event->user_id;
        $u_role = $data_event->u_role;
        $ven_date = $data_event->ven_date;
        $ven_month = $data_event->ven_month;
        $ven_com_id = $data_event->ven_com_idb;
        $vn_id =$data_event->vn_id;
        $vns_id =$data_event->vns_id;     
        $DN =$data_event->DN;     
    

         // หา user_ที่ไม่สามารถอยูเวรนี้ได้
         $venIsNot = getVenForUsersNot($ven_date, $DN);
         
        
        /** user ที่จะยกให้ */
        $users = array();
        $sql = "SELECT vu.vu_id, vu.user_id, p.fname,p.name,p.sname, p.img
                FROM ven_user as vu   
                INNER JOIN profile as p
                ON vu.user_id = p.user_id 
                WHERE vu.vns_id = :vns_id AND p.user_id <> :user_id";
        $query = $conn->prepare($sql);
        $query->bindParam(':vns_id',$vns_id, PDO::PARAM_INT);
        $query->bindParam(':user_id',$user_id, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);

        if($query->rowCount() > 0){                        //count($result)  for odbc
            foreach($result as $rs){
                $img_link = ($rs->img != null && $rs->img != '' && file_exists('../../uploads/users/' . $rs->img )) 
                            ? '../../uploads/users/'. $rs->img
                            : '../../assets/images/profiles/nopic.png';
                $changeStatus  =  checkUserNotDate($venIsNot, $rs->user_id);
                array_push($users,array(
                    'vu_id'    => $rs->vu_id,
                    'user_id' => $rs->user_id,
                    'u_name' => $rs->fname.$rs->name.' '.$rs->sname,
                    'img' => $img_link,
                    'changeStatus' => $changeStatus
                ));
            }
        }
        

       

        http_response_code(200);
        echo json_encode(array(
            'status' => true, 
            'message' => 'สำเร็จ',
            'users'  => $users,
            'venIsNot'  => $venIsNot,
            ));
        exit;
        
    
    }catch(PDOException $e){
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}


function getVenForUsersNot($ven_date, $DN){
    global $conn;
    $ven_date_u1 = date("Y-m-d", strtotime('+1 day', strtotime($ven_date)));
    $ven_date_d1 = date("Y-m-d", strtotime('-1 day', strtotime($ven_date)));

    $data = array();

    $DN_D = 'กลางวัน';
    $DN_N = 'กลางคืน';
    $DN_NC = 'nightCourt';

    if($DN == $DN_D){
        // ven_date_d1 == กลางคืน ven_date_1
        // ven_date == กลางวัน   ven_date_2
        // ven_date == กลางคืน   ven_date_3
        
        $sql = "SELECT 
                        v.ven_date, 
                        v.ven_time, 
                        vc.ven_month, 
                        vn.DN, 
                        v.user_id, 
                        v.vn_id, 
                        v.vns_id,
                        v.status
                FROM ven AS v    
                INNER JOIN ven_com AS vc ON v.ven_com_idb = vc.id              
                INNER JOIN ven_name AS vn ON v.vn_id = vn.id       
                WHERE ((v.ven_date = :ven_date_1 AND vn.DN = :DN_1) 
                    OR (v.ven_date = :ven_date_2 AND vn.DN = :DN_2)
                    OR (v.ven_date = :ven_date_3 AND vn.DN = :DN_3))
                    AND (v.status = 1 OR v.status = 2)
                ORDER BY v.ven_date, v.ven_time";
        $query_VU = $conn->prepare($sql);
        $query_VU->bindParam(':ven_date_1', $ven_date_d1);
        $query_VU->bindParam(':DN_1', $DN_N ,PDO::PARAM_STR);

        $query_VU->bindParam(':ven_date_2', $ven_date);
        $query_VU->bindParam(':DN_2', $DN_D ,PDO::PARAM_STR);

        $query_VU->bindParam(':ven_date_3', $ven_date);
        $query_VU->bindParam(':DN_3', $DN_N ,PDO::PARAM_STR);

        $query_VU->execute();
        return $query_VU->fetchAll(PDO::FETCH_OBJ);
    }
    if($DN == $DN_N){
        // ven_date == กลางคืน ven_date_1
        // ven_date == กลางวัน ven_date_2
        // ven_date == nightCourt   ven_date_3
        // ven_date_u1 == กลางวัน   ven_date_4
        
        $sql = "SELECT 
                        v.ven_date, 
                        v.ven_time, 
                        vc.ven_month, 
                        vn.DN, 
                        v.user_id, 
                        v.vn_id, 
                        v.vns_id,
                        v.status
                FROM ven AS v   
                INNER JOIN ven_com AS vc ON v.ven_com_idb = vc.id              
                INNER JOIN ven_name AS vn ON v.vn_id = vn.id                
                WHERE ((v.ven_date = :ven_date_1 AND vn.DN = :DN_1) 
                    OR (v.ven_date = :ven_date_2 AND vn.DN = :DN_2)
                    OR (v.ven_date = :ven_date_3 AND vn.DN = :DN_3)
                    OR (v.ven_date = :ven_date_4 AND vn.DN = :DN_4))
                    AND (v.status = 1 OR v.status = 2)
                ORDER BY v.ven_date, v.ven_time";
        $query_VU = $conn->prepare($sql);
        $query_VU->bindParam(':ven_date_1', $ven_date);
        $query_VU->bindParam(':DN_1', $DN_N ,PDO::PARAM_STR);

        $query_VU->bindParam(':ven_date_2', $ven_date);
        $query_VU->bindParam(':DN_2', $DN_D ,PDO::PARAM_STR);

        $query_VU->bindParam(':ven_date_3', $ven_date);
        $query_VU->bindParam(':DN_3', $DN_NC ,PDO::PARAM_STR);

        $query_VU->bindParam(':ven_date_4', $ven_date_u1);
        $query_VU->bindParam(':DN_4', $DN_D ,PDO::PARAM_STR);

        $query_VU->execute();
        return $query_VU->fetchAll(PDO::FETCH_OBJ);
    }
    if($DN == $DN_NC){
        // ven_date == nightCourt   ven_date_1
        // ven_date == กลางคืน       ven_date_1
        
        $sql = "SELECT 
                        v.ven_date, 
                        v.ven_time, 
                        vc.ven_month, 
                        vn.DN, 
                        v.user_id, 
                        v.vn_id, 
                        v.vns_id,
                        v.status
                FROM ven AS v    
                INNER JOIN ven_com AS vc ON v.ven_com_idb = vc.id              
                INNER JOIN ven_name AS vn ON v.vn_id = vn.id              
                WHERE ((v.ven_date = :ven_date_1 AND vn.DN = :DN_1) 
                    OR (v.ven_date = :ven_date_2 AND vn.DN = :DN_2))
                    AND (v.status = 1 OR v.status = 2)
                ORDER BY v.ven_date, v.ven_time";
        $query_VU = $conn->prepare($sql);
        $query_VU->bindParam(':ven_date_1', $ven_date);
        $query_VU->bindParam(':DN_1', $DN_NC ,PDO::PARAM_STR);

        $query_VU->bindParam(':ven_date_2', $ven_date);
        $query_VU->bindParam(':DN_2', $DN_N ,PDO::PARAM_STR);

        $query_VU->execute();
        return $query_VU->fetchAll(PDO::FETCH_OBJ);
    }

    return json_encode($data);
   
}

function checkUserNotDate($ven_dates, $user_id){
    $uid2 = intval($user_id);
    foreach ($ven_dates as $vd) {
        $uid = intval($vd->user_id);
        if ($uid === $uid2) {
            return ["status"=>false, "text"=>"มีเวรวันที่ ".$vd->ven_date." ".$vd->DN];
        }
    }
    // หลังจากวนลูปเสร็จแล้วถ้ายังไม่พบเวรของผู้ใช้ ให้ส่งค่าเป็น true
    return ["status"=>true, "text"=>"Ok"];
}
