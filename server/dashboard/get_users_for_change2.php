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

// สลับกัน
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $my_user_id = isset($_SESSION['AD_ID']) ? $_SESSION['AD_ID'] : '';
        $date_now = Date("Y-m-d");
        $datas = array();

        $ven_id = $data->id;
        $ven_cerrent = getVen($ven_id);

        //หาเวรของตัวเอง
        //user_id = my_user_id //   ven_com_idb ven_month vn_id vns_id เดียวกัน
        $getMyVen = getMyVens($ven_cerrent, $my_user_id);
        $my_ven = [];

        foreach ($getMyVen as $rs) {
            $img_link = ($rs->img != null && $rs->img != '' && file_exists('../../uploads/users/' . $rs->img))
                ? 'uploads/users/' . $rs->img
                : 'assets/images/profiles/nopic.png';

            $changeStatus = ["status" => true, "text" => "Ok"];
            if ($ven_cerrent->price > 0) {
                $venIsNot = getVenForUsersNot($rs->ven_date, $ven_cerrent->DN);
                $changeStatus = checkUserNotDate($venIsNot, $ven_cerrent->user_id);
            }

            array_push($my_ven, array(
                'id'    => $rs->id,
                'user_id' => $rs->user_id,
                'ven_date' => $rs->ven_date,
                'u_name' => $rs->fname . $rs->name . ' ' . $rs->sname,
                'img' => $baseURL . $img_link,
                'DN' => $rs->DN,
                'changeStatus' => $changeStatus
            ));
        }



        http_response_code(200);
        echo json_encode(array(
            'status' => true,
            'message' => 'สำเร็จ',
            'my_vens'  => $my_ven,
            'getVen'  => $ven_cerrent,
            'getMyVen'  => $getMyVen
        ));
        exit;
    } catch (PDOException $e) {
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
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
                WHERE ((v.ven_date = :ven_date_1 AND vn.DN = :DN_1) 
                    OR (v.ven_date = :ven_date_2 AND vn.DN = :DN_2)
                    OR (v.ven_date = :ven_date_3 AND vn.DN = :DN_3))
                    AND (v.status = 1 OR v.status = 2)
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
                WHERE ((v.ven_date = :ven_date_1 AND vn.DN = :DN_1) 
                    OR (v.ven_date = :ven_date_2 AND vn.DN = :DN_2)
                    OR (v.ven_date = :ven_date_3 AND vn.DN = :DN_3)
                    OR (v.ven_date = :ven_date_4 AND vn.DN = :DN_4))
                    AND (v.status = 1 OR v.status = 2)
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
                WHERE ((v.ven_date = :ven_date_1 AND vn.DN = :DN_1) 
                    OR (v.ven_date = :ven_date_2 AND vn.DN = :DN_2))
                    AND (v.status = 1 OR v.status = 2)
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

function checkUserNotDate($ven_dates, $user_id)
{
    $date_now = date('Y-m-d');
    $uid2 = intval($user_id);
    foreach ($ven_dates as $vd) {
        $uid = intval($vd->user_id);
        if ($uid === $uid2) {
            return ["status" => false, "text" => "มีเวรวันที่ " . $vd->ven_date . " " . $vd->DN];
        } elseif ($vd->ven_date < $date_now) {
            return ["status" => false, "text" => "เวรนี้ผ่านมาแล้ว"];
        }
    }
    // หลังจากวนลูปเสร็จแล้วถ้ายังไม่พบเวรของผู้ใช้ ให้ส่งค่าเป็น true
    return ["status" => true, "text" => "Ok"];
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

function getVen($ven_id)
{
    global $conn;
    try {
        $sql = "SELECT 
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
                        vns.price
                FROM ven AS v
                INNER JOIN ven_com AS vc ON v.ven_com_idb = vc.id              
                INNER JOIN ven_name AS vn ON v.vn_id = vn.id  
                INNER JOIN ven_name_sub AS vns ON v.vns_id = vns.id  
                WHERE v.id=:id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":id", $ven_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(array("status" => false, "message" => "Error Database" . $e->getMessage()));
        exit;
    } catch (Exception $e) {
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'Error : ' . $e->getMessage()));
        exit;
    }
}
