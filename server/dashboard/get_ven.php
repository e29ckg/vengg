<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../connect.php";
include "../function.php";

$data = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try{
        $datas = array();
        $ven_id = $data->id;                //id_ven ที่เลือก
        $user_id = $_SESSION['AD_ID'];     //user_id ของผู้ใชระบบ
        // $user_id = 1680162006;     //user_id ของผู้ใชระบบ
        $date_now = Date("Y-m-d");

        $ven_cerrent = [];

        $btnUsersForChange = false;
        $btnVensForChange = false;
        $print = false;
        $resp_text = '';

               
        $sql = "SELECT v.id, 
                        v.ven_date, 
                        v.ven_time, 
                        v.user_id, 
                        v.ven_com_id, 
                        v.ven_com_idb,
                        v.vn_id,
                        v.vns_id,
                        vc.ven_com_num as ven_com_num_all, 
                        vc.ven_month as ven_month, 
                        vn.name as ven_name, 
                        vn.DN, 
                        vns.name as u_role, 
                        vns.price as price, 
                        vns.color as color,
                        vn.name as ven_com_name,
                        v.status, 
                        p.fname, 
                        p.`name`, 
                        p.sname,
                        p.img
                FROM ven AS v                 
                INNER JOIN `profile` AS p ON v.user_id = p.id
                INNER JOIN `ven_com` AS vc ON v.ven_com_idb = vc.id
                INNER JOIN `ven_name` AS vn ON v.vn_id = vn.id
                INNER JOIN `ven_name_sub` AS vns ON v.vns_id = vns.id
                WHERE v.id = :id
                ORDER BY v.ven_date DESC";
        $query = $conn->prepare($sql);
        $query->bindParam(':id',$ven_id, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);

        
        
        /** ประวัติการเปลี่ยน */
        $sql = "SELECT 
                vc.id, 
                vc.ven_id1, 
                vc.ven_id2, 
                vc.user_id1, 
                vc.user_id2,
                vc.status,
                p1.fname AS p1_fname, 
                p1.name AS p1_name, 
                p1.sname AS p1_sname, 
                p2.fname AS p2_fname, 
                p2.name AS p2_name, 
                p2.sname AS p2_sname
            FROM ven_change as vc  
            INNER JOIN profile as p1 ON p1.user_id = vc.user_id1
            INNER JOIN profile as p2 ON p2.user_id = vc.user_id2
            WHERE (ven_date1=:ven_date1 OR ven_date2 =:ven_date2) AND (vc.status = 1 OR vc.status = 2)
            ORDER BY vc.id DESC";
    $query = $conn->prepare($sql);
    $query->bindParam(':ven_date1', $result->ven_date, PDO::PARAM_STR);
    $query->bindParam(':ven_date2', $result->ven_date, PDO::PARAM_STR);
    $query->execute();
    $res_vh0 = $query->fetchAll(PDO::FETCH_OBJ);

    $sql = "SELECT 
                v.id, 
                v.vn_id, 
                v.status, 
                vc.ven_month AS ven_month, 
                p.fname, 
                p.name, 
                p.sname
            FROM ven as v 
            INNER JOIN ven_com AS vc ON v.ven_com_idb = vc.id 
            INNER JOIN profile AS p ON v.user_id = p.id 
            WHERE ven_date=:ven_date 
                AND vc.ven_month=:ven_month 
                AND v.ven_com_idb = :ven_com_idb 
                AND v.vn_id=:vn_id
                AND v.vns_id=:vns_id 
                AND v.ven_time=:ven_time  
                AND (v.status = 1 OR v.status = 2 OR v.status = 4)
            ORDER BY v.id DESC";
    $query = $conn->prepare($sql);
    $query->bindParam(':ven_date', $result->ven_date, PDO::PARAM_STR);
    $query->bindParam(':ven_month', $result->ven_month, PDO::PARAM_STR);
    $query->bindParam(':ven_com_idb', $result->ven_com_idb, PDO::PARAM_INT);
    $query->bindParam(':vn_id', $result->vn_id, PDO::PARAM_INT);
    $query->bindParam(':vns_id', $result->vns_id, PDO::PARAM_INT);
    $query->bindParam(':ven_time', $result->ven_time, PDO::PARAM_STR);
    $query->execute();
    $changeHistory = array();

    foreach ($query->fetchAll(PDO::FETCH_OBJ) as $rs) {
        $ven_change_id = '';
        $user_id1 = 0;
        $user_id2 = 0;
        foreach ($res_vh0 as $rsvh0) {
            if ($rsvh0->ven_id1 == $rs->id) {
                $ven_change_id = $rsvh0->id;
                $user_id1 = $rsvh0->user_id1;
                $user_id2 = $rsvh0->user_id2;
            } elseif ($rsvh0->ven_id2 == $rs->id) {
                $ven_change_id = $rsvh0->id;
                $user_id1 = $rsvh0->user_id2;
                $user_id2 = $rsvh0->user_id1;
            }
        }
        if ($ven_change_id == '') {
            $ven_change_id = $rs->id;
        }
        $print = ($user_id1 == $user_id || $user_id2 == $user_id); // ตรวจสอบว่าผู้ใช้ปัจจุบันเกี่ยวข้องกับการเปลี่ยนหรือไม่
        array_push($changeHistory, array(
            'id' => $ven_change_id,
            'u_name' => $rs->fname . $rs->name . ' ' . $rs->sname,
            'user_id1' => $user_id1,
            'user_id2' => $user_id2,
            'print' => $print,
            'status' => $rs->status,
        ));
    }

    if ($result->ven_date >= $date_now && $result->status === 1) {
        if ($user_id == $result->user_id) {
            $btnUsersForChange = true;
        } else {
            $btnUsersForChange = false;
    
            $ven_for_user_not = getVenForUsersNot($result->ven_date, $result->DN);
            $check_user_for_date = checkUserNotDate($ven_for_user_not, $user_id);
    
            if ($check_user_for_date['status']) {
                $btnVensForChange = true;
                $ven_count = getVenCount($result, $user_id);
                if ($ven_count['count'] > 0) {
                    $btnVensForChange = true;
                } else {
                    $btnVensForChange = false;
                    $resp_text = ' ท่านไม่มีเวรนี้ ';
                }
            } else {
                $btnVensForChange = false;                
                $resp_text = ' --- ท่านไม่สามารถเปลี่ยนกับเวรนี้ได้ --- ';
                
            }
        }
    } else {
        $resp_text = ' --- เวรนี้รออนุมัติ ไม่สามารถเปลี่ยนได้ --- ';
        $btnUsersForChange = false;
        $btnVensForChange = false;
        if ($result->ven_date < $date_now) {
            $resp_text = ' --- เวรนี้ผ่านมาแล้ว ไม่สามารถเปลี่ยนได้ ---  '; 
        }
    }
    
   


    $img = $result->img != null && $result->img != '' && file_exists('../../uploads/users/' . $result->img ) 
            ? '../../uploads/users/'. $result->img 
            : '../../assets/images/profiles/nopic.png';

    $ven_cerrent = [
        "id" => $result->id,
        "DN" => $result->DN,
        "u_name" => $result->fname.$result->name.' '.$result->sname,
        "u_role" => $result->u_role,
        "img" => $img,
        "user_id" => $result->user_id,
        "ven_com_id" => $result->ven_com_id,
        "ven_com_idb" => $result->ven_com_idb,
        "ven_com_name" => $result->ven_com_name,
        "ven_com_num_all" => $result->ven_com_num_all,
        "ven_date" => $result->ven_date,
        "ven_date_th" => DateThai_full($result->ven_date),
        "ven_month" => $result->ven_month,
        "ven_name" => $result->ven_name,
        "ven_time" => $result->ven_time,
        "vn_id" => $result->vn_id,
        "vns_id" => $result->vns_id,
        "status" => $result->status,
        "resp_text" => $resp_text
    ];




          
          
       

    http_response_code(200);
    echo json_encode(array(
        'status' => true, 
        'message' => 'สำเร็จ',
        'respJSON' => $ven_cerrent ,
        'changeHistory'    => $changeHistory,
        'd_now' => $date_now,
        'btnUsersForChange' => $btnUsersForChange,
        'btnVensForChange' => $btnVensForChange,
        'resp_text'=>$resp_text
        ));
    exit;
        
    
    }catch(PDOException $e){
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'Error Database : ' . $e->getMessage()));
        exit;
    }catch(Exception $e){
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'Error : ' . $e->getMessage()));
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
                        v.vns_id
                FROM ven AS v    
                INNER JOIN ven_com AS vc ON v.ven_com_idb = vc.id
                INNER JOIN ven_name AS vn ON v.vn_id = vn.id
                INNER JOIN ven_name_sub AS vns ON v.vns_id = vns.id
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
                        v.vns_id
                FROM ven AS v   
                INNER JOIN ven_com AS vc ON v.ven_com_idb = vc.id
                INNER JOIN ven_name AS vn ON v.vn_id = vn.id
                INNER JOIN ven_name_sub AS vns ON v.vns_id = vns.id             
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
                INNER JOIN ven_name_sub AS vns ON v.vns_id = vns.id               
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

function getVenCount($result, $user_id){
    global $conn;
    try{
        $sql = "SELECT 
                    v.ven_date, 
                    v.user_id, 
                    v.ven_com_idb, 
                    v.vn_id, 
                    v.vns_id,
                    v.status
                FROM ven AS v
                INNER JOIN ven_com AS vc ON v.ven_com_idb = vc.id
                INNER JOIN ven_name AS vn ON v.vn_id = vn.id
                INNER JOIN ven_name_sub AS vns ON v.vns_id = vns.id   
                WHERE vc.ven_month = :ven_month 
                    AND v.ven_com_idb = :ven_com_idb 
                    AND v.vn_id = :vn_id 
                    AND v.vns_id = :vns_id 
                    AND v.user_id = :user_id 
                    AND (v.status = 1 OR v.status = 2)
                ORDER BY v.ven_date ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":ven_month", $result->ven_month, PDO::PARAM_STR);
        $stmt->bindParam(":ven_com_idb", $result->ven_com_idb, PDO::PARAM_STR);
        $stmt->bindParam(":vn_id", $result->vn_id, PDO::PARAM_INT);
        $stmt->bindParam(":vns_id", $result->vns_id, PDO::PARAM_INT);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT); // Changed PARAM_STR to PARAM_INT
        $stmt->execute();
        $result_set = $stmt->fetchAll(PDO::FETCH_OBJ); // Changed to FETCH_OBJ
        $count = $stmt->rowCount();
        $response = array(
            'status' => true,
            'data' => $result_set,
            'count' => $count
        );
        return $response;
    }catch(PDOException $e){
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'Error Database : ' . $e->getMessage()));
        exit;
    }catch(Exception $e){
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'Error : ' . $e->getMessage()));
        exit;
    }
}
