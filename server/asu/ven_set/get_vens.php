<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
// header("'Access-Control-Allow-Credentials', 'true'");
// header('Content-Type: application/javascript');
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $datas = array();

    try{

        $sql = "SELECT vns.name as u_role,vns.price,vns.color, vn.name,vn.DN
                FROM ven_name_sub AS vns
                INNER JOIN ven_name AS vn ON vn.id = vns.ven_name_id";
        $query = $conn->prepare($sql);
        $query->execute();
        $res = $query->fetchAll(PDO::FETCH_OBJ);

        $sql = "SELECT v.id, v.ven_date, v.ven_time, v.u_role, v.price, v.ven_com_name, p.name, p.sname FROM ven as v 
                INNER JOIN `profile` as p ON v.user_id = p.user_id
                WHERE (v.status = 1 OR v.status = 2 OR v.status = 5) AND p.`status` = 10
                ORDER BY v.ven_date DESC, v.ven_time ASC
                LIMIT 500";
        $query = $conn->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);

        if($query->rowCount() > 0){                        //count($result)  for odbc
            foreach($result as $rs){
                $bgcolor = getColor($res, $rs->u_role, $rs->price, $rs->ven_com_name);
                array_push($datas,array(
                    'id'    => $rs->id,
                    'title' => $rs->name. ' '. $rs->sname,
                    'start' => $rs->ven_date.' '.$rs->ven_time,
                    'backgroundColor' => $bgcolor,
                ));
            }
            http_response_code(200);
            echo json_encode(array('status' => true, 'massege' => 'สำเร็จ', 'respJSON' => $datas));
            exit;
        }
     
        http_response_code(200);
        echo json_encode(array('status' => true, 'massege' => 'ไม่พบข้อมูล ', 'respJSON' => $datas));
        exit;
    
    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'massege' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}

function getColor($res,$d,$price,$v_name){    
    $color = '';
    foreach($res as $rs){
        if($rs->u_role == $d && $rs->price == $price && $rs->name == $v_name ){
            $color = $rs->color;
            break;
        }
    }
    return $color; 
}

