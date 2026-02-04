<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";

$data = json_decode(file_get_contents("php://input"));

// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $datas = array();

    $act = $data->act;

    try {
        if ($act == 'insert') {
            /**     รับค่า  */
            $id = time();
            $ven_date       = $data->ven_date;
            $user_id        = $data->uid;

            $ven_com_id     = json_encode(array($data->vc_id));
            $ven_com_idb    = $data->vc_id;
            $ven_month      = $data->ven_month;
            $vn_id          = $data->vn_id;
            $vns_id         = $data->vns_id;
            $DN             = $data->DN;

            $ven_name       = $data->ven_name;
            $ven_com_name   = $data->ven_name;
            $ven_com_num    = $data->ven_com_num;

            $u_role         = $data->u_role;
            $price          = $data->price;
            $color          = $data->color;
            $vn_srt         = $data->vn_srt;
            $vns_srt        = $data->vns_srt;
            $status         = 2;

            $update_at      = '';
            $create_at      = '';

            $datetime = new DateTime($ven_date);
            $ven_date_month = $datetime->format('Y-m');
            if ($ven_date_month !== $ven_month) {
                http_response_code(200);
                echo json_encode(array('status' => false, 'message' => 'ท่านลงเวรผิดเดือน'));
                exit;
            }

            // if ($price > 0 && $DN == 'nightCourt') {
            //     $ven_date_d = date("Y-m-d", strtotime($ven_date));
            //     $sql_VU = "SELECT 
            //                         v.id, 
            //                         v.ven_date, 
            //                         vn.DN, 
            //                         p.fname, p.name, p.sname 
            //                 FROM ven AS v
            //                 INNER JOIN `ven_name` AS vn ON v.vn_id = vn.id
            //                 INNER JOIN `profile` AS p ON p.user_id = v.user_id
            //                 WHERE v.user_id = :user_id 
            //                     AND (vn.DN = 'กลางคืน' Or vn.DN = 'nightCourt')
            //                     AND v.ven_date = :ven_date_d
            //                     AND (v.status = 1 OR v.status = 2)";
            //     $query_VU = $conn->prepare($sql_VU);
            //     $query_VU->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            //     $query_VU->bindParam(':ven_date_d', $ven_date_d);
            //     $query_VU->execute();
            //     $res_VU = $query_VU->fetch(PDO::FETCH_OBJ);
            //     if ($query_VU->rowCount()) {
            //         http_response_code(200);
            //         echo json_encode(array(
            //             'status' => false,
            //             'message' => $res_VU->fname . $res_VU->name . ' ' . $res_VU->sname . "\n มีเวรกลางคืนหรือnightCourt"
            //         ));
            //         exit;
            //     }
            // }

            // if ($price > 0 && ($DN == 'กลางวัน' || $DN == 'กลางคืน')) {

            //     /** เช็ควันเวลาที่อยู่เวรไม่ได้ */
            //     $ven_date_u1 = date("Y-m-d", strtotime('+1 day', strtotime($ven_date)));
            //     $ven_date_d1 = date("Y-m-d", strtotime('-1 day', strtotime($ven_date)));

            //     $sql_VU = "SELECT 
            //                         v.id,
            //                         v.ven_date,
            //                         v.status,
            //                         vn.DN,
            //                         p.fname, p.name, p.sname 
            //                 FROM ven AS v
            //                 INNER JOIN `ven_name` AS vn ON v.vn_id = vn.id
            //                 INNER JOIN `ven_name_sub` AS vns ON v.vns_id = vns.id
            //                 INNER JOIN `profile` AS p ON p.user_id = v.user_id
            //                 WHERE v.user_id = :user_id 
            //                     -- AND (v.DN = 'กลางวัน' OR v.DN = 'กลางคืน') 
            //                     AND v.ven_date >= :ven_date_d1 
            //                     AND v.ven_date <= :ven_date_u1 
            //                     AND vns.price > 0 
            //                     AND (v.status = 1 OR v.status = 2)";
            //     $query_VU = $conn->prepare($sql_VU);
            //     $query_VU->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            //     $query_VU->bindParam(':ven_date_d1', $ven_date_d1);
            //     $query_VU->bindParam(':ven_date_u1', $ven_date_u1);
            //     $query_VU->execute();
            //     $res_VU = $query_VU->fetchAll(PDO::FETCH_OBJ);

            //     if ($query_VU->rowCount()) {
            //         foreach ($res_VU as $ru) {
            //             if ($ru->ven_date == $ven_date) {
            //                 http_response_code(200);
            //                 echo json_encode(array(
            //                     'status' => false,
            //                     'message' => $ru->fname . $ru->name . ' ' . $ru->sname . "\n มีเวรวันนี้แล้ว"
            //                 ));
            //                 exit;
            //             }
            //             if ($DN == 'กลางวัน' && $ru->ven_date == $ven_date_d1 && $ru->DN == 'กลางคืน') {
            //                 http_response_code(200);
            //                 echo json_encode(array(
            //                     'status' => false,
            //                     'message' => $ru->fname . $ru->name . ' ' . $ru->sname . "\n มีเวรกลางคืน \nวันก่อนหน้านี้(" . DateThai($ven_date_d1) . ")"
            //                 ));
            //                 exit;
            //             }
            //             if ($DN == 'กลางคืน'  && $ru->ven_date == $ven_date_u1 && $ru->DN == 'กลางวัน') {
            //                 http_response_code(200);
            //                 echo json_encode(array(
            //                     'status' => false,
            //                     'message' => $ru->fname . $ru->name . ' ' . $ru->sname . "\n มีเวรกลางวัน \nวันถัดไป(" . DateThai($ven_date_u1) . ")"
            //                 ));
            //                 exit;
            //             }
            //         }
            //     }
            // }


            $ref1           = generateRandomString();
            $ref2           =  $ref1;
            $status         = 2;
            $update_at      = Date("Y-m-d H:i:s");
            $create_at      = Date("Y-m-d H:i:s");

            $ven_time = '';


            $sql = "INSERT INTO ven(id, user_id, ven_com_id, ven_com_idb, ven_date, ven_time, vn_id, vns_id, 
                        ref1, ref2, `status`, update_at, create_at) 
                    VALUE(:id, :user_id, :ven_com_id, :ven_com_idb, :ven_date, :ven_time, :vn_id, :vns_id, 
                        :ref1, :ref2, :status, :update_at, :create_at);";
            $query = $conn->prepare($sql);
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->bindParam(':user_id', $user_id, PDO::PARAM_STR);
            $query->bindParam(':ven_com_id', $ven_com_id, PDO::PARAM_STR);
            $query->bindParam(':ven_com_idb', $ven_com_idb, PDO::PARAM_STR);
            $query->bindParam(':ven_date', $ven_date, PDO::PARAM_STR);
            $query->bindParam(':ven_time', $ven_time, PDO::PARAM_STR);
            $query->bindParam(':vn_id', $vn_id, PDO::PARAM_INT);
            $query->bindParam(':vns_id', $vns_id, PDO::PARAM_INT);
            $query->bindParam(':ref1', $ref1, PDO::PARAM_STR);
            $query->bindParam(':ref2', $ref2, PDO::PARAM_STR);
            $query->bindParam(':status', $status, PDO::PARAM_INT);
            $query->bindParam(':update_at', $create_at, PDO::PARAM_STR);
            $query->bindParam(':create_at', $create_at, PDO::PARAM_STR);
            $query->execute();


            /** จัดลำดับ */
            // เรียกใช้งานฟังก์ชัน
            updateVenTime($conn, $ven_date);

            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'success', 'responseJSON' => $data, 'ven_time' => $ven_time));
            exit;
        }
    } catch (PDOException $e) {
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'Database Error: ' . $e->getMessage()));
        exit;
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(array('status' => false, 'message' => 'Error: ' . $e->getMessage()));
        exit;
    }
}

function updateVenTime(PDO $conn, string $ven_date)
{
    $sql = "SELECT 
                v.id,
                vn.DN,
                -- v.u_role,
                v.ven_time,
                vn.srt AS vn_srt,
                vns.srt AS vns_srt
            FROM ven AS v
            INNER JOIN ven_name AS vn ON v.vn_id = vn.id
            INNER JOIN ven_name_sub AS vns ON v.vns_id = vns.id
            WHERE v.ven_date = :ven_date 
                AND (v.status = 1 OR v.status = 2)
            ORDER BY 
                vn_srt ASC,
                vns_srt ASC,
                v.update_at ASC";

    $query = $conn->prepare($sql);
    $query->bindParam(':ven_date', $ven_date);
    $query->execute();

    $seconds = 0;
    foreach ($query->fetchAll(PDO::FETCH_OBJ) as $rs) {
        $hours = ($rs->DN == 'กลางคืน' || $rs->DN == 'nightCourt') ? 16 : 8;

        ++$seconds;
        $ven_time = date("H:i:s", mktime($hours, 30, $seconds));

        $updateSql = "UPDATE ven SET ven_time = :ven_time WHERE id = :id";
        $updateQuery = $conn->prepare($updateSql);
        $updateQuery->bindParam(':ven_time', $ven_time, PDO::PARAM_STR);
        $updateQuery->bindParam(':id', $rs->id, PDO::PARAM_INT);
        $updateQuery->execute();
    }
}
