<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $datas = array();

    try {
        $sql = "SELECT 
                    vns.color,
                    v.id,
                    v.ven_date, 
                    v.ven_time, 
                    vns.name AS u_role, 
                    vns.price, 
                    -- v.ven_com_name, 
                    p.name, p.sname 
                FROM ven AS v 
                INNER JOIN `profile` AS p ON v.user_id = p.user_id
                LEFT JOIN ven_name_sub AS vns ON v.vns_id = vns.id
                WHERE v.status IN (1, 2, 5) 
                ORDER BY v.ven_date DESC, v.ven_time ASC
                LIMIT 2000";
        $query = $conn->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);

        if ($query->rowCount() > 0) {
            foreach ($result as $rs) {
                array_push($datas, array(
                    'id' => $rs->id,
                    'title' => $rs->name . ' ' . $rs->sname,
                    'start' => $rs->ven_date . ' ' . $rs->ven_time,
                    'backgroundColor' => $rs->color,
                ));
            }
            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'success', 'respJSON' => $datas));
            exit;
        }

        http_response_code(200);
        echo json_encode(array('status' => true, 'message' => 'ไม่พบข้อมูล', 'respJSON' => $datas));
        exit;
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'Error: ' . $e->getMessage()));
        exit;
    }
}
?>