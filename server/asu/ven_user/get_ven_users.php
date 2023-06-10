<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
// header("'Access-Control-Allow-Credentials', 'true'");
// header('Content-Type: application/javascript');
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";


// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $datas = array();

    try{
        
        // $sql = "SELECT * FROM ven_user ORDER BY v_time ASC, `order` ASC";
        $sql = "SELECT vu.*, pr.fname, pr.`name`, pr.sname
                FROM ven_user as vu
                INNER JOIN `profile` as pr
                ON vu.user_id = pr.id
                ORDER BY vu.v_time ASC, vu.`order` ASC;";
        $query = $conn->prepare($sql);
        $query->execute();
        
        if($query->rowCount() > 0){  
            $result = $query->fetchAll(PDO::FETCH_OBJ);            
            foreach($result as $rs){
                array_push($datas, array(
                    'id'        => $rs->id,
                    'user_id'   => $rs->user_id,
                    'order'     => $rs->order,
                    'ven_name'  => $rs->ven_name,
                    'u_name'    => $rs->fname.$rs->name.' '.$rs->sname,
                    'DN'        => $rs->DN,
                    'uvn'       => $rs->uvn,
                    'v_time'    => $rs->v_time,
                    'price'     => $rs->price,
                    'color'     => $rs->color,
                    'comment'   => $rs->comment
                ));

            }
            

            http_response_code(200);
            echo json_encode(array(
                'status' => true, 
                'message' => 'สำเร็จ', 
                'respJSON' => $datas
            ));
            exit;
        }
     
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'ไม่พบข้อมูล'));
        exit;
    
    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}