<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
// header("'Access-Control-Allow-Credentials', 'true'");
// header('Content-Type: application/javascript');
header("Content-Type: application/json; charset=utf-8");

include "../connect.php";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
 
    $ssid = isset($_SESSION['AD_ID']) ? $_SESSION['AD_ID'] : '';

    $datas = array();

    
    try{
        $sql = "SELECT vns.name as u_role, vns.price, vns.color, vn.name, vn.DN
                FROM ven_name_sub AS vns
                INNER JOIN ven_name AS vn ON vn.id = vns.ven_name_id";
        $query = $conn->prepare($sql);
        $query->execute();
        $res = $query->fetchAll(PDO::FETCH_OBJ);
        

        $sql = "SELECT id, ven_date, ven_time, user_id, u_name, u_role, DN, price, ven_com_name, ven.status
        FROM ven    
        WHERE status = 1 OR status = 2
        ORDER BY ven_date DESC, ven_time ASC
        LIMIT 800";
        $query = $conn->prepare($sql);
        $query->execute();
        
        if($query->rowCount() > 0){                       
            $result = $query->fetchAll(PDO::FETCH_OBJ);
            foreach($result as $rs){
                $rs->DN == 'กลางวัน' ? $d = '☀️' : $d = '🌙';
                $bgcolor = getColor($res, $rs->u_role, $rs->price, $rs->ven_com_name);
                if($rs->status == 2 ){
                    $bgcolor ='Yellow' ;
                    $textC = 'black';
                }else{      
                    if($rs->user_id == $_SESSION['AD_ID']){
                        $bgcolor = 'Gold' ;
                        $textC = 'write';
                    } else{
                        $bgcolor = $bgcolor ;
                        $textC = 'write';
                    }
                }
                array_push($datas,array(
                    'id'    => $rs->id,
                    'title' => $d.' '.$rs->u_name,
                    'start' => $rs->ven_date.' '.$rs->ven_time,
                    'allDay' => true,
                    'backgroundColor' => $bgcolor,
                    'textColor' => $textC
                ));
            }
            
            http_response_code(200);
            echo json_encode(array(
                'status' => true, 
                'message' => 'สำเร็จ', 
                'respJSON' => $datas, 
                'ssid' => $ssid,
                'res' => $res
            ));
            exit;
        }
     
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'ไม่พบข้อมูล ', 'respJSON' => $datas, 'ssid' => $ssid));
        exit;
    
    }catch(PDOException $e){
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
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




