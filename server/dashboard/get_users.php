<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
// header("'Access-Control-Allow-Credentials', 'true'");
// header('Content-Type: application/javascript');
header("Content-Type: application/json; charset=utf-8");

include "../connect.php";

$data = json_decode(file_get_contents("php://input"));

// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $datas = array();

    try{
        $sql = "SELECT vu.id, vu.user_id, vu.u_name, p.img
        FROM ven_user as vu   
        INNER JOIN profile as p
        ON vu.user_id = p.user_id 
        WHERE vu.ven_name = :ven_name AND vu.uvn = :uvn";
        $query = $conn->prepare($sql);
        $query->bindParam(':ven_name',$data->ven_name, PDO::PARAM_STR);
        $query->bindParam(':uvn',$data->uvn, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);

        if($query->rowCount() > 0){                        //count($result)  for odbc
            foreach($result as $rs){
                $img_link = ($rs->img != null && $rs->img != '' && file_exists('../../uploads/users/' . $rs->img )) 
                            ? '../../uploads/users/'. $rs->img
                            : '../../assets/images/profiles/nopic.png';

                array_push($datas,array(
                    'id'    => $rs->id,
                    'user_id' => $rs->user_id,
                    'u_name' => $rs->u_name,
                    'img' => $img_link,
                ));
            }
            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'สำเร็จ', 'respJSON' => $datas));
            exit;
        }
     
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'ไม่พบข้อมูล ', 'respJSON' => $datas));
        exit;
    
    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}


