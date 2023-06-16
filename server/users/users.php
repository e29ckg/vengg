<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../connect.php";
include "../function.php";

// $data = json_decode(file_get_contents("php://input"));


// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
$datas = array();

    try{
        $sql = "SELECT u.username, u.status, p.*
                FROM profile as p 
                INNER JOIN `user` as u ON u.id = p.user_id
                ORDER BY p.st ASC";
        $query = $conn->prepare($sql);
        // $query->bindParam(':kkey',$data->kkey, PDO::PARAM_STR);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);

        if($query->rowCount() > 0){                        //count($result)  for odbc
            foreach($result as $rs){
                if($rs->img != null && $rs->img != '' && file_exists('../../uploads/users/' . $rs->img )){
                    $img_link = '../../uploads/users/'. $rs->img;

                }else{
                    $img_link = '../../assets/images/profiles/nopic.png';
                }
                array_push($datas,array(
                    'uid' => $rs->user_id,
                    'username' => $rs->username,
                    'name'  => $rs->fname.$rs->name.' '.$rs->sname,
                    'dep'   => $rs->dep,
                    'img'   => $img_link,
                    'status'   => $rs->status,
                    'st'   => $rs->st,
                ));
            }
            http_response_code(200);
            echo json_encode(array('status' => true, 'message' => 'สำเร็จ', 'respJSON' => $datas));
            exit;
        }
     
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'ไม่พบข้อมูล '));
    
    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}