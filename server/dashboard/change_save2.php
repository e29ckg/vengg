<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../connect.php";
include "../function.php";


$data = json_decode(file_get_contents("php://input"));

// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $ch_v1 = $data->ch_v1;  //เวรที่ไปอยู่
    $ch_v2 = $data->ch_v2;  //ผู้ทำ เวรตั้งต้น

    $datas = array();

    try {

        $idv1   = time();
        $idv2   = $idv1 + 1;
        $ref    =  generateRandomString();
        $status             = 2;
        $create_at          = Date("Y-m-d H:i:s");

        /** เรียกใบเวรที่ 1 */
        $sql    = "SELECT 
                            v.id,
                            v.user_id,
                            v.ven_date,
                            v.ven_time,
                            v.ven_com_idb,
                            v.vn_id,
                            v.vns_id,
                            v.status,
                            vc.ven_month,
                            vn.DN,
                            vns.price,
                            p.fname, p.name, p.sname 
                    FROM ven AS v
                    INNER JOIN ven_com AS vc ON v.ven_com_idb = vc.id              
                    INNER JOIN ven_name AS vn ON v.vn_id = vn.id  
                    INNER JOIN ven_name_sub AS vns ON v.vns_id = vns.id 
                    INNER JOIN `profile` AS p ON v.user_id = p.id 
                    WHERE v.id = :id AND v.status=1";
        $query  = $conn->prepare($sql);
        $query->bindParam(':id', $ch_v1->id, PDO::PARAM_INT);
        $query->execute();
        $rsv1 = $query->fetch(PDO::FETCH_OBJ);
        if ($query->rowCount() == 0) {
            http_response_code(200);
            echo json_encode(array('status' => false, 'message' => 'ใบเวรนี้ ' . $ch_v1->id . ' ไม่สามารถเปลี่ยนได้'));
            exit;
        }

        /** เรียกใบเวรที่ 2 */
        $sql    = "SELECT 
                            v.id,
                            v.user_id,
                            v.ven_date,
                            v.ven_time,
                            v.ven_com_idb,
                            v.vn_id,
                            v.vns_id,
                            v.status,
                            vc.ven_month,
                            vn.DN,
                            vns.price,
                            p.fname, p.name, p.sname 
                    FROM ven AS v
                    INNER JOIN ven_com AS vc ON v.ven_com_idb = vc.id              
                    INNER JOIN ven_name AS vn ON v.vn_id = vn.id  
                    INNER JOIN ven_name_sub AS vns ON v.vns_id = vns.id 
                    INNER JOIN `profile` AS p ON v.user_id = p.id 
                    WHERE v.id = :id AND v.status=1";
        $query  = $conn->prepare($sql);
        $query->bindParam(':id', $ch_v2->id, PDO::PARAM_INT);
        $query->execute();
        $rsv2 = $query->fetch(PDO::FETCH_OBJ);
        if ($query->rowCount() == 0) {
            http_response_code(200);
            echo json_encode(array('status' => false, 'message' => 'ใบเวรนี้ ' . $ch_v2->id . ' ไม่สามารถเปลี่ยนได้'));
            exit;
        }

        /** ตรวจสอบคำสั่งเวรใบที่ 1 และใบที่ 2 ว่าคำสั่งเดียวกันหรือไม่  */
        if ($rsv1->ven_com_idb != $rsv2->ven_com_idb) {
            http_response_code(200);
            echo json_encode(array('status' => false, 'message' => 'คำสั่งไม่ตรงกัน'));
            exit;
        }

        if($rsv1->price > 0){
            
                    /** เช็ควันเวลาที่อยู่เวรไม่ได้ */
                    $ven_date_u1 = date("Y-m-d", strtotime('+1 day', strtotime($rsv2->ven_date)));
                    $ven_date_d1 = date("Y-m-d", strtotime('-1 day', strtotime($rsv2->ven_date)));
            
                    $sql_VU = "SELECT 
                                        v.id, 
                                        v.ven_date, 
                                        v.user_id, 
                                        v.status, 
                                        vn.DN, 
                                        vns.price 
                                FROM ven AS v
                                INNER JOIN ven_name AS vn ON v.vn_id = vn.id  
                                INNER JOIN ven_name_sub AS vns ON v.vns_id = vns.id  
                                WHERE v.user_id = $rsv1->user_id  
                                    AND v.ven_date >= '$ven_date_d1' 
                                    AND v.ven_date <= '$ven_date_u1' 
                                    AND (v.status=1 OR v.status=2)
                                    AND vns.price > 0";
                    $query_VU = $conn->prepare($sql_VU);
                    $query_VU->execute();
                    $res_VU = $query_VU->fetchAll(PDO::FETCH_OBJ);
            
                    if ($query_VU->rowCount()) {
                        foreach ($res_VU as $ru) {
                            // if ($ru->ven_date == $rsv2->ven_date) {
                            //     http_response_code(200);
                            //     echo json_encode(array('status' => false, 'message' => $rsv1->fname . $rsv1->name . ' ' . $rsv1->sname . "\n" . 'วันที่ ' . DateThai($rsv2->ven_date) . ' มีเวรอยู่แล้ว.'));
                            //     exit;
                            // }
            
                            if ($rsv2->DN == 'กลางวัน' && $ru->ven_date == $ven_date_d1 && $ru->DN == 'กลางคืน') {
                                http_response_code(200);
                                echo json_encode(array('status' => false, 'message' => $rsv1->fname . $rsv1->name . ' ' . $rsv1->sname . "\n" . 'วันที่ ' . DateThai($ven_date_d1) . ' มีเวรกลางคืน'));
                                exit;
                            }
                            if ($rsv2->DN == 'กลางคืน'  && $ru->ven_date == $ven_date_u1 && $ru->DN == 'กลางวัน') {
                                http_response_code(200);
                                echo json_encode(array('status' => false, 'message' => $rsv1->fname . $rsv1->name . ' ' . $rsv1->sname . "\n" . 'วันที่ ' . DateThai($ven_date_u1) . ' มีเวรกลางวัน'));
                                exit;
                            }
                            if ($rsv2->DN == 'nightCourt' && $ru->ven_date == $rsv2->ven_date && $ru->DN == 'กลางคืน') {
                                http_response_code(200);
                                echo json_encode(array('status' => false, 'message' => 'ไม่สามารถลงวันนี้ได้'));
                                exit;
                            }
                        }
                    }
            
                    $ven_date_u1 = date("Y-m-d", strtotime('+1 day', strtotime($rsv1->ven_date)));
                    $ven_date_d1 = date("Y-m-d", strtotime('-1 day', strtotime($rsv1->ven_date)));
            
                    $sql_VU = "SELECT 
                                        v.id, 
                                        v.user_id, 
                                        v.ven_date, 
                                        v.status, 
                                        vn.DN, 
                                        vns.price 
                                FROM ven AS v
                                INNER JOIN ven_name AS vn ON v.vn_id = vn.id  
                                INNER JOIN ven_name_sub AS vns ON v.vns_id = vns.id 
                                WHERE v.user_id = $rsv2->user_id  
                                    AND v.ven_date >= '$ven_date_d1' 
                                    AND v.ven_date <= '$ven_date_u1' 
                                    AND vns.price > 0 
                                    AND (v.status=1 OR v.status=2)";
                    $query_VU = $conn->prepare($sql_VU);
                    $query_VU->execute();
                    $res_VU = $query_VU->fetchAll(PDO::FETCH_OBJ);
            
                    if ($query_VU->rowCount()) {
                        foreach ($res_VU as $ru) {
                            // if ($ru->ven_date == $rsv1->ven_date) {
                            //     http_response_code(200);
                            //     echo json_encode(array('status' => false, 'message' => $rsv2->fname . $rsv2->name . ' ' . $rsv2->sname . "\n" . 'วันที่ ' . DateThai($rsv1->ven_date) . ' มีเวรอยู่แล้ว.'));
                            //     exit;
                            // }
                            if ($rsv1->DN == 'กลางวัน' && $ru->ven_date == $ven_date_d1 && $ru->DN == 'กลางคืน') {
                                http_response_code(200);
                                echo json_encode(array('status' => false, 'message' => $rsv2->fname . $rsv2->name . ' ' . $rsv2->sname . "\n" . 'วันที่ ' . DateThai($ven_date_d1) . ' มีเวรกลางคืน'));
                                exit;
                            }
                            if ($rsv1->DN == 'กลางคืน'  && $ru->ven_date == $ven_date_u1 && $ru->DN == 'กลางวัน') {
                                http_response_code(200);
                                echo json_encode(array('status' => false, 'message' => $rsv2->fname . $rsv2->name . ' ' . $rsv2->sname . "\n" . 'วันที่ ' . DateThai($ven_date_u1) . ' มีเวรกลางวัน'));
                                exit;
                            }
                            if ($rsv1->DN == 'nightCourt' && $ru->ven_date == $rsv1->ven_date   && $ru->DN == 'กลางคืน') {
                                http_response_code(200);
                                echo json_encode(array('status' => false, 'message' => 'ไม่สามารถลงวันนี้ได้'));
                                exit;
                            }
                        }
                    }
                    /** end เช็ควันเวลาที่อยู่เวรไม่ได้ */
        }


        $conn->beginTransaction();
        /**  สร้างเวรใบ1 */
        $sql = "INSERT INTO ven(id, ven_date, ven_time, ven_com_id, ven_com_idb, user_id, vn_id, vns_id, ref1, ref2, gcal_id, `status`, update_at, create_at) 
                    VALUE(:id, :ven_date, :ven_time, :ven_com_id, :ven_com_idb, :user_id, :vn_id, :vns_id, :ref1, :ref2, :gcal_id, :status, :update_at, :create_at);";
        $query = $conn->prepare($sql);
        $query->bindParam(':id', $idv1, PDO::PARAM_INT);
        $query->bindParam(':ven_date', $rsv1->ven_date, PDO::PARAM_STR);
        $query->bindParam(':ven_time', $rsv1->ven_time, PDO::PARAM_STR);
        $query->bindParam(':ven_com_id', $rsv1->ven_com_id, PDO::PARAM_STR);
        $query->bindParam(':ven_com_idb', $rsv1->ven_com_idb, PDO::PARAM_STR);
        $query->bindParam(':user_id', $rsv2->user_id, PDO::PARAM_INT);
        $query->bindParam(':vn_id', $rsv2->vn_id, PDO::PARAM_INT);
        $query->bindParam(':vns_id', $rsv2->vns_id, PDO::PARAM_INT);
        $query->bindParam(':ref1', $ref, PDO::PARAM_STR);
        $query->bindParam(':ref2', $rsv1->ref1, PDO::PARAM_STR);
        $query->bindParam(':gcal_id', $rsv1->gcal_id, PDO::PARAM_STR);
        $query->bindParam(':status', $status, PDO::PARAM_INT);
        $query->bindParam(':update_at', $create_at, PDO::PARAM_STR);
        $query->bindParam(':create_at', $create_at, PDO::PARAM_STR);
        $query->execute();

        /**  สร้างเวรใบที่2 */
        $sql = "INSERT INTO ven(id, ven_date, ven_time, ven_com_id, ven_com_idb, user_id, vn_id, vns_id, ref1, ref2, gcal_id, `status`, update_at, create_at) 
                    VALUE(:id, :ven_date, :ven_time, :ven_com_id, :ven_com_idb, :user_id, :vn_id, :vns_id, :ref1, :ref2, :gcal_id, :status, :update_at, :create_at);";
        $query = $conn->prepare($sql);
        $query->bindParam(':id', $idv2, PDO::PARAM_INT);
        $query->bindParam(':ven_date', $rsv2->ven_date, PDO::PARAM_STR);
        $query->bindParam(':ven_time', $rsv2->ven_time, PDO::PARAM_STR);
        $query->bindParam(':ven_com_id', $rsv2->ven_com_id, PDO::PARAM_STR);
        $query->bindParam(':ven_com_idb', $rsv2->ven_com_idb, PDO::PARAM_STR);
        $query->bindParam(':user_id', $rsv1->user_id, PDO::PARAM_INT);
        $query->bindParam(':vn_id', $rsv2->vn_id, PDO::PARAM_INT);
        $query->bindParam(':vns_id', $rsv2->vns_id, PDO::PARAM_INT);
        $query->bindParam(':ref1', $ref, PDO::PARAM_STR);
        $query->bindParam(':ref2', $rsv2->ref1, PDO::PARAM_STR);
        $query->bindParam(':gcal_id', $rsv2->gcal_id, PDO::PARAM_STR);
        $query->bindParam(':status', $status, PDO::PARAM_INT);
        $query->bindParam(':update_at', $create_at, PDO::PARAM_STR);
        $query->bindParam(':create_at', $create_at, PDO::PARAM_STR);
        $query->execute();

        /**สร้างใบเปลี่ยนเวร */

        $sql = "INSERT INTO ven_change(id, ven_date1, ven_date2, ven_month, ven_com_id, ven_com_idb, vn_id, vns_id, ven_id1, ven_id2, ven_id1_old, ven_id2_old,  user_id1, user_id2, ref1, `status`, create_at) 
                VALUE(:id, :ven_date1, :ven_date2, :ven_month, :ven_com_id, :ven_com_idb, :vn_id, :vns_id, :ven_id1, :ven_id2, :ven_id1_old, :ven_id2_old, :user_id1, :user_id2, :ref1, :status, :create_at);";
        $chid = 'CH' . $idv1;
        $query = $conn->prepare($sql);
        $query->bindParam(':id', $chid, PDO::PARAM_INT);
        $query->bindParam(':ven_date1', $rsv2->ven_date, PDO::PARAM_STR);
        $query->bindParam(':ven_date2', $rsv1->ven_date, PDO::PARAM_STR);
        $query->bindParam(':ven_month', $rsv2->ven_month, PDO::PARAM_STR);
        $query->bindParam(':ven_com_id', $rsv2->ven_com_id, PDO::PARAM_STR);
        $query->bindParam(':ven_com_idb', $rsv2->ven_com_idb, PDO::PARAM_INT);
        $query->bindParam(':vn_id', $rsv2->vn_id, PDO::PARAM_INT);
        $query->bindParam(':vns_id', $rsv2->vns_id, PDO::PARAM_INT);
        $query->bindParam(':ven_id1', $idv1, PDO::PARAM_INT);
        $query->bindParam(':ven_id2', $idv2, PDO::PARAM_INT);
        $query->bindParam(':ven_id1_old', $rsv2->id, PDO::PARAM_INT);
        $query->bindParam(':ven_id2_old', $rsv1->id, PDO::PARAM_INT);
        $query->bindParam(':user_id1', $rsv2->user_id, PDO::PARAM_INT);
        $query->bindParam(':user_id2', $rsv1->user_id, PDO::PARAM_INT);
        $query->bindParam(':ref1', $ref, PDO::PARAM_STR);
        $query->bindParam(':status', $status, PDO::PARAM_INT);
        $query->bindParam(':create_at', $create_at, PDO::PARAM_STR);

        $query->execute();

        $status = 4;
        $sql = "UPDATE ven SET update_at=:update_at, ven.status =:status  WHERE id = :id";
        $query = $conn->prepare($sql);
        $query->bindParam(':update_at', $create_at, PDO::PARAM_STR);
        $query->bindParam(':status', $status, PDO::PARAM_INT);
        $query->bindParam(':id', $ch_v1->id, PDO::PARAM_INT);
        $query->execute();

        $sql = "UPDATE ven SET update_at=:update_at, ven.status =:status  WHERE id = :id";
        $query = $conn->prepare($sql);
        $query->bindParam(':update_at', $create_at, PDO::PARAM_STR);
        $query->bindParam(':status', $status, PDO::PARAM_INT);
        $query->bindParam(':id', $ch_v2->id, PDO::PARAM_INT);
        $query->execute();

        $conn->commit();

        /** google calendar */
        if (__GOOGLE_CALENDAR__) {
            $sql_V = "SELECT 
                            v.*,
                            vn.name AS ven_com_name,
                            p.fname, 
                            p.name, 
                            p.sname
                        FROM ven AS v
                        INNER JOIN ven_name as vn ON v.vn_id = vn.id 
                        INNER JOIN `profile` AS p ON v.user_id = p.id 
                        WHERE v.gcal_id = :gcal_id 
                            AND (v.status=1 OR v.status=2)
                        ORDER BY v.ven_time ASC";
            $query_V = $conn->prepare($sql_V);
            $query_V->bindParam(':gcal_id', $rsv1->gcal_id, PDO::PARAM_STR);
            $query_V->execute();
            if ($query_V->rowCount()) {
                $res_V  = $query_V->fetchAll(PDO::FETCH_OBJ);
                $name   = $res_V[0]->ven_com_name . "\n";
                $sms    = '';
                foreach ($res_V as $v) {
                    $sms .= $v->fname . $v->name . ' ' . $v->sname . "\n";
                }
                gcal_update($rsv1->gcal_id, $name, $sms, 5);
            }

            $sql_V = "SELECT 
                                v.*, 
                                vn.name AS ven_com_name, 
                                p.fname, p.name, p.sname 
                        FROM ven AS v
                        INNER JOIN ven_name as vn ON v.vn_id = vn.id 
                        INNER JOIN `profile` AS p ON v.user_id = p.id 
                        WHERE v.gcal_id = :gcal_id 
                            AND (v.status=1 OR v.status=2)
                        ORDER BY v.ven_time ASC";
            $query_V = $conn->prepare($sql_V);
            $query_V->bindParam(':gcal_id', $rsv2->gcal_id, PDO::PARAM_STR);
            $query_V->execute();
            if ($query_V->rowCount()) {
                $res_V  = $query_V->fetchAll(PDO::FETCH_OBJ);
                $name   = $res_V[0]->ven_com_name . "\n";
                $sms2   = '';
                foreach ($res_V as $v) {
                    $sms2 .= $v->fname . $v->name . ' ' . $v->sname . "\n";
                }
                gcal_update($rsv2->gcal_id, $name, $sms2, 5);
            }
        }

        //ส่ง line ot ven_admin
        $sql = "SELECT token FROM line WHERE name = 'ven_admin' AND status=1";
        $query_line = $conn->prepare($sql);
        $query_line->execute();
        $res = $query_line->fetch(PDO::FETCH_OBJ);
        if ($query_line->rowCount()) {
            $sToken = $res->token;
            $sMessage = 'มีการเปลี่ยนเวร ' . $chid . "\n";
            $sMessage .= $rsv1->name . '<<>>' . $rsv2->name . "\n";
            $sMessage .= $rsv1->ven_date . '<<>>' . $rsv2->ven_date . "\n";
            $sMessage .= '(' . $create_at . ')';
            $res_line = sendLine($sToken, $sMessage);
        }

        http_response_code(200);
        echo json_encode(array(
            'status'    => true,
            'message'   => 'ok',
            'v_id' => $idv1,
            "uid" => $rsv2->user_id
            // 'responseJSON' => $res_line
        ));
        exit;
    } catch (PDOException $e) {
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}
