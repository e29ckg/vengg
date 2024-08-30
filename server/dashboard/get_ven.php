<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../connect.php";
include "../function.php";

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
$host = $_SERVER['HTTP_HOST'];
$baseURL = $protocol . $host . '/vengg/';

$data = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $datas = array();
        $ven_id = $data->id;                //id_ven ที่เลือก
        $user_id = $_SESSION['AD_ID'];     //user_id ของผู้ใชระบบ
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
        $query->bindParam(':id', $ven_id, PDO::PARAM_INT);
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
            WHERE (ven_date1=:ven_date1 OR ven_date2 =:ven_date2) 
                AND (vc.status = 1 OR vc.status = 2)
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

        $btnUsersForChange = false;     //ปุ่มยกให้
        $btnVensForChange = false;      //ขอเปลี่ยน

        if ($result->user_id == $user_id && $result->ven_date >= $date_now && $result->status === 1) {
            $btnUsersForChange = true;
            $btnVensForChange = false;
        } elseif ($result->ven_date >= $date_now && $result->status === 1) {
            $btnUsersForChange = false;
            $btnVensForChange = true;
            // เช็คเวรที่เราจะไปอยู่ ว่าขอเปลี่ยนได้หรือไม่
            $ckMyNot = checkMYNot(getVenForUsersNot($result->ven_date, $result->DN), $user_id);
            if (!$ckMyNot['status']) {
                $btnVensForChange = false;
                $resp_text = ' --- เวรนี้ไม่สามารถเปลี่ยนได้ (' . $ckMyNot['status'] . ')---  ';
            }

            if ($result->price == 0) {
                $btnVensForChange = true;
                $resp_text = '';
            }
            if (count(getMyVens($result, $user_id)) == 0) {
                $btnVensForChange = false;
                $resp_text = 'ท่านไม่มีเวรที่จะเปลี่ยน';
            }
        } else {
            if ($result->ven_date < $date_now) {
                $resp_text = ' --- เวรนี้ไม่สามารถเปลี่ยนได้ (เวรนี้ผ่านมาแล้ว)---  ';
            }
            if ($result->status === 2) {
                $resp_text = ' --- เวรนี้ไม่สามารถเปลี่ยนได้ (เวรนี้อยู่ระว่างรออนุมัติ)---  ';
            }
        }


        $img = $result->img != null && $result->img != '' && file_exists('../../uploads/users/' . $result->img)
            ? 'uploads/users/' . $result->img
            : 'assets/images/profiles/nopic.png';



        $ven_cerrent = [
            "id" => $result->id,
            "DN" => $result->DN,
            "u_name" => $result->fname . $result->name . ' ' . $result->sname,
            "u_role" => $result->u_role,
            "img" => $baseURL . $img,
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
            'respJSON' => $ven_cerrent,
            'changeHistory'    => $changeHistory,
            'd_now' => $date_now,
            'btnUsersForChange' => $btnUsersForChange,
            'btnVensForChange' => $btnVensForChange,
            'resp_text' => $resp_text,
            "myven" => getMyVens($result, $user_id)
        ));
        exit;
    } catch (PDOException $e) {
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'Error Database : ' . $e->getMessage()));
        exit;
    } catch (Exception $e) {
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'Error : ' . $e->getMessage()));
        exit;
    }
}

function checkMYNot($ven_dates, $user_id)
{
    $date_now = date('Y-m-d');
    $user_id = intval($user_id);
    foreach ($ven_dates as $vd) {
        $uid = intval($vd->user_id);
        if ($uid === $user_id) {
            return ["status" => false, "text" => "ท่านมีเวรวันที่ " . $vd->ven_date . " " . $vd->DN];
        } elseif ($vd->ven_date < $date_now) {
            return ["status" => false, "text" => "เวรนี้ผ่านมาแล้ว"];
        }
    }
    // หลังจากวนลูปเสร็จแล้วถ้ายังไม่พบเวรของผู้ใช้ ให้ส่งค่าเป็น true
    return ["status" => true, "text" => "Ok"];
}

function getVenForUsersNot($ven_date, $DN)
{
    global $conn;
    $ven_date_u1 = date("Y-m-d", strtotime('+1 day', strtotime($ven_date)));
    $ven_date_d1 = date("Y-m-d", strtotime('-1 day', strtotime($ven_date)));

    $data = array();

    $DN_D = 'กลางวัน';
    $DN_N = 'กลางคืน';
    $DN_NC = 'nightCourt';

    if ($DN == $DN_D) {
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
                INNER JOIN ven_name_sub AS vns ON v.vns_id = vns.id     
                WHERE ((v.ven_date = :ven_date_1 AND vn.DN = :DN_1) 
                    OR (v.ven_date = :ven_date_2 AND vn.DN = :DN_2)
                    OR (v.ven_date = :ven_date_3 AND vn.DN = :DN_3))
                    AND (v.status = 1 OR v.status = 2)
                    AND vns.price > 0   
                ORDER BY v.ven_date, v.ven_time";
        $query_VU = $conn->prepare($sql);
        $query_VU->bindParam(':ven_date_1', $ven_date_d1);
        $query_VU->bindParam(':DN_1', $DN_N, PDO::PARAM_STR);

        $query_VU->bindParam(':ven_date_2', $ven_date);
        $query_VU->bindParam(':DN_2', $DN_D, PDO::PARAM_STR);

        $query_VU->bindParam(':ven_date_3', $ven_date);
        $query_VU->bindParam(':DN_3', $DN_N, PDO::PARAM_STR);

        $query_VU->execute();
        return $query_VU->fetchAll(PDO::FETCH_OBJ);
    }
    if ($DN == $DN_N) {
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
                INNER JOIN ven_name_sub AS vns ON v.vns_id = vns.id                
                WHERE ((v.ven_date = :ven_date_1 AND vn.DN = :DN_1) 
                    OR (v.ven_date = :ven_date_2 AND vn.DN = :DN_2)
                    OR (v.ven_date = :ven_date_3 AND vn.DN = :DN_3)
                    OR (v.ven_date = :ven_date_4 AND vn.DN = :DN_4))
                    AND (v.status = 1 OR v.status = 2) 
                    AND vns.price > 0
                ORDER BY v.ven_date, v.ven_time";
        $query_VU = $conn->prepare($sql);
        $query_VU->bindParam(':ven_date_1', $ven_date);
        $query_VU->bindParam(':DN_1', $DN_N, PDO::PARAM_STR);

        $query_VU->bindParam(':ven_date_2', $ven_date);
        $query_VU->bindParam(':DN_2', $DN_D, PDO::PARAM_STR);

        $query_VU->bindParam(':ven_date_3', $ven_date);
        $query_VU->bindParam(':DN_3', $DN_NC, PDO::PARAM_STR);

        $query_VU->bindParam(':ven_date_4', $ven_date_u1);
        $query_VU->bindParam(':DN_4', $DN_D, PDO::PARAM_STR);

        $query_VU->execute();
        return $query_VU->fetchAll(PDO::FETCH_OBJ);
    }
    if ($DN == $DN_NC) {
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
                    AND vns.price > 0
                ORDER BY v.ven_date, v.ven_time";
        $query_VU = $conn->prepare($sql);
        $query_VU->bindParam(':ven_date_1', $ven_date);
        $query_VU->bindParam(':DN_1', $DN_NC, PDO::PARAM_STR);

        $query_VU->bindParam(':ven_date_2', $ven_date);
        $query_VU->bindParam(':DN_2', $DN_N, PDO::PARAM_STR);

        $query_VU->execute();
        return $query_VU->fetchAll(PDO::FETCH_OBJ);
    }

    return json_encode($data);
}

function getMyVens($res, $user_id)
{
    global $conn;
    try {
        $sql = "SELECT 
                    v.id, 
                    v.user_id, 
                    v.ven_date, 
                    v.ven_time, 
                    v.vn_id, 
                    v.vns_id, 
                    v.status, 
                    vc.ven_month, 
                    vn.DN, 
                    p.fname, p.name, p.sname, p.img
                FROM ven AS v
                INNER JOIN ven_com AS vc ON v.ven_com_idb = vc.id              
                INNER JOIN ven_name AS vn ON v.vn_id = vn.id  
                INNER JOIN profile AS p ON p.user_id = v.user_id
                WHERE vc.ven_month=:ven_month 
                    AND v.ven_com_idb = :ven_com_idb 
                    AND v.vn_id = :vn_id 
                    AND v.vns_id = :vns_id 
                    AND v.user_id = :user_id 
                    AND (v.status = 1 OR v.status = 2)
                ORDER BY v.ven_date ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":ven_month", $res->ven_month, PDO::PARAM_STR);
        $stmt->bindParam(":ven_com_idb", $res->ven_com_idb, PDO::PARAM_INT);
        $stmt->bindParam(":vn_id", $res->vn_id, PDO::PARAM_INT);
        $stmt->bindParam(":vns_id", $res->vns_id, PDO::PARAM_INT);
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'Error Database : ' . $e->getMessage()));
        exit;
    } catch (Exception $e) {
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'Error : ' . $e->getMessage()));
        exit;
    }
}
