<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../connect.php";

if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    if(!isset($_GET['month'])){
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'month'));
        exit;
    }

    $ven_month = $_GET['month'];
    $ven_month_obj = new DateTime($ven_month);
    $month_start_obj = clone $ven_month_obj;
    $month_end_obj = clone $ven_month_obj;

    // ลบ 1 เดือนจาก $month_start_obj
    $month_start_obj->modify('-7 day');
    $month_start = $month_start_obj->format('Y-m-d');

    // เพิ่ม 1 เดือนจาก $month_end_obj
    $month_end_obj->modify('+45 day');
    $month_end = $month_end_obj->format('Y-m-d');


 
    $ssid = isset($_SESSION['AD_ID']) ? $_SESSION['AD_ID'] : '';

    $datas = array();
    
    try{
        // $sql = "SELECT vns.name as u_role, vns.price, vns.color, vn.name, vn.DN
        //         FROM ven_name_sub AS vns
        //         INNER JOIN ven_name AS vn ON vn.id = vns.ven_name_id";
        // $query = $conn->prepare($sql);
        // $query->execute();
        // $res = $query->fetchAll(PDO::FETCH_OBJ);
        

        $sql = "SELECT v.id, 
                        v.ven_date, 
                        v.ven_time, 
                        v.user_id, 
                        vn.DN, 
                        vns.name as u_role, 
                        vns.price as price, 
                        vns.color as color,
                        vn.name as ven_com_name,
                        v.status, 
                        p.fname, 
                        p.`name`, 
                        p.sname
        FROM ven AS v
        INNER JOIN `profile` AS p ON v.user_id = p.id
        INNER JOIN `ven_name` AS vn ON v.vn_id = vn.id
        INNER JOIN `ven_name_sub` AS vns ON v.vns_id = vns.id
        WHERE (v.status = 1 OR v.status = 2)
        AND v.ven_date >= :month_start
        AND v.ven_date <= :month_end
        ORDER BY v.ven_date DESC, v.ven_time ASC";

        $query = $conn->prepare($sql);
        $query->bindValue(':month_start', $month_start, PDO::PARAM_STR);
        $query->bindValue(':month_end', $month_end, PDO::PARAM_STR);
        $query->execute();

        
        if($query->rowCount() > 0){                       
            $result = $query->fetchAll(PDO::FETCH_OBJ);
            foreach($result as $rs){
                if ($rs->DN == 'กลางวัน') {
                    $DN = '☀️';
                } elseif ($rs->DN == 'กลางคืน') {
                    $DN = '🌙';
                } elseif ($rs->DN == 'nightCourt') {
                    $DN = '✨';
                } else {
                    // Default value if none of the conditions are true
                    $DN = '';
                }
                $bgcolor = $rs->color;
                if($rs->status == 2 ){
                    $bgcolor ='Yellow' ;
                    $textC = 'black';
                }else{      
                    if($rs->user_id == $_SESSION['AD_ID']){
                        $bgcolor = 'Gold' ;
                        $textC = 'write';
                    } else{
                        // $bgcolor = $bgcolor ;
                        $textC = 'write';
                    }
                }
                array_push($datas,array(
                    'id'    => $rs->id,
                    'title' => $DN.' '.$rs->fname.$rs->name.' '.$rs->sname,
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
                // 'res' => $res,
                'ven_month' => $ven_month,
                'month_start' => $month_start,
                'month_end' => $month_end,
            ));
            exit;
        }
     
        http_response_code(200);
        echo json_encode(array(
            'status' => false, 
            'message' => 'ไม่พบข้อมูล ', 
            'respJSON' => $datas, 
            'ssid' => $ssid 
        ));
        exit;
    
    }catch(PDOException $e){
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}






