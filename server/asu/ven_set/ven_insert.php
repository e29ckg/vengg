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

    try {
        if ($act == 'insert') {
            /**     รับค่า  */
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

            if ($query_VU->rowCount()) {
                foreach ($res_VU as $ru) {
                    if ($ru->ven_date == $ven_date) {
                        http_response_code(200);
                        echo json_encode(array('status' => false, 'message' => 'วันนี้มีเวรอยู่แล้ว'));
                        exit;
                    }
                    if ($DN == 'กลางวัน' && $ru->ven_date == $ven_date_d1 && $ru->DN == 'กลางคืน') {
                        http_response_code(200);
                        echo json_encode(array('status' => false, 'message' => $ven_date_d1 . ' มีเวร'));
                        exit;
                    }
                    if ($DN == 'กลางคืน'  && $ru->ven_date == $ven_date_u1 && $ru->DN == 'กลางวัน') {
                        http_response_code(200);
                        echo json_encode(array('status' => false, 'message' => $ven_date_u1 . ' มีเวร'));
                        exit;
                    }
                }
            }


            $ref1           = generateRandomString();
            $ref2           =  $ref1;
            $status         = 2;
            $update_at      = Date("Y-m-d H:i:s");
            $create_at      = Date("Y-m-d H:i:s");

            $ven_time = '';


            $sql = "INSERT INTO ven(id, user_id, ven_com_id, ven_com_idb, ven_date, ven_time, ven_month, vn_id, vns_id, 
                        DN, ven_com_name, ven_com_num_all, ven_name, u_role, price, color, ref1, ref2, `status`, update_at, create_at) 
                    VALUE(:id, :user_id, :ven_com_id, :ven_com_idb, :ven_date, :ven_time, :ven_month, :vn_id, :vns_id, 
                        :DN, :ven_com_name, :ven_com_num_all, :ven_name, :u_role, :price, :color, :ref1, :ref2, :status, :update_at, :create_at);";
            $query = $conn->prepare($sql);
            $query->bindParam(':id', $id, PDO::PARAM_INT);
            $query->bindParam(':user_id', $user_id, PDO::PARAM_STR);
            $query->bindParam(':ven_com_id', $ven_com_id, PDO::PARAM_STR);
            $query->bindParam(':ven_com_idb', $ven_com_idb, PDO::PARAM_STR);
            $query->bindParam(':ven_date', $ven_date, PDO::PARAM_STR);
            $query->bindParam(':ven_time', $ven_time, PDO::PARAM_STR);
            $query->bindParam(':ven_month', $ven_month, PDO::PARAM_STR);
            $query->bindParam(':vn_id', $vn_id, PDO::PARAM_INT);
            $query->bindParam(':vns_id', $vns_id, PDO::PARAM_INT);
            $query->bindParam(':DN', $DN, PDO::PARAM_STR);
            $query->bindParam(':ven_com_name', $ven_com_name, PDO::PARAM_STR);
            $query->bindParam(':ven_com_num_all', $ven_com_num_all, PDO::PARAM_STR);
            $query->bindParam(':ven_name', $ven_name, PDO::PARAM_STR);
            $query->bindParam(':u_role', $u_role, PDO::PARAM_STR);
            $query->bindParam(':price', $price, PDO::PARAM_STR);
            $query->bindParam(':color', $color, PDO::PARAM_STR);
            $query->bindParam(':ref1', $ref1, PDO::PARAM_STR);
            $query->bindParam(':ref2', $ref2, PDO::PARAM_STR);
            $query->bindParam(':status', $status, PDO::PARAM_INT);
            $query->bindParam(':update_at', $create_at, PDO::PARAM_STR);
            $query->bindParam(':create_at', $create_at, PDO::PARAM_STR);
            $query->execute();


            /** จัดลำดับ */
            $sql = "SELECT 
                    	ven.id,
                        ven.u_role,
                        ven.ven_time,	
                        ven_name.srt AS vn_srt,
                        ven_name_sub.srt AS vns_srt
                    FROM ven
                    INNER JOIN ven_name ON ven.vn_id = ven_name.id
                    INNER JOIN ven_name_sub ON ven.vns_id = ven_name_sub.id
                    WHERE ven_date='$ven_date' 
                        AND (ven.`status` = 1 OR ven.`status` = 2)
                    ORDER BY 
                        vn_srt ASC,
                        vns_srt ASC,
                        update_at ASC";
            $query = $conn->prepare($sql);
            $query->execute();
            $hours = 8;
            $seconds = 0;
            foreach ($query->fetchAll(PDO::FETCH_OBJ) as $rs) {
                if ($DN == 'กลางคืน') {
                    $hours = 16;
                }
                ++$seconds;
                $ven_time = date("H:i:s", mktime($hours, 30, $seconds));

                $sql = "UPDATE ven SET ven_time = '$ven_time' WHERE id=$rs->id";
                $conn->exec($sql);
            }


            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => ' ok ', 'responseJSON' => $data));
            exit;
        }
    } catch (PDOException $e) {
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}
