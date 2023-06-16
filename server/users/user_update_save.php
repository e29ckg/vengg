<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../connect.php";
include "../function.php";

$data = json_decode(file_get_contents("php://input"));


// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if($_SESSION['AD_ROLE'] != 9){
        http_response_code(200);
        echo json_encode(array('staus' => false, 'message' => 'ไม่มีสิทธิ์'));
        exit;
    }

    if($data->user){
        $user = $data->user;
    }else{
        http_response_code(200);
        echo json_encode(array('staus' => false, 'message' => 'no-data'));
        exit;
    }  
    $datas = array();
    try{
        $sql = "SELECT id FROM profile WHERE user_id = :user_id";
        $query = $conn->prepare($sql);
        $query->bindParam(':user_id',$user->user_id, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);

        if(empty($result)){
            http_response_code(200);
            echo json_encode(array('status' => false, 'message' => 'ไม่มี user นี้อยู่ในระบบ'));
            exit;
        } 
        $date_time = Date("Y-m-d h:i:s");
        $sql = "UPDATE profile SET 
                fname = :fname,
                name = :name,
                sname = :sname,
                dep = :dep,
                workgroup = :workgroup,
                phone = :phone,
                bank_account = :bank_account,
                bank_comment = :bank_comment,
                st = :st,
                updated_at = :updated_at
                WHERE user_id = :user_id";
        $query = $conn->prepare($sql);
        $query->bindParam(':fname',$user->fname, PDO::PARAM_STR);
        $query->bindParam(':name',$user->name, PDO::PARAM_STR);
        $query->bindParam(':sname',$user->sname, PDO::PARAM_STR);
        $query->bindParam(':dep',$user->dep, PDO::PARAM_STR);
        $query->bindParam(':workgroup',$user->workgroup, PDO::PARAM_STR);
        $query->bindParam(':phone',$user->phone, PDO::PARAM_STR);
        $query->bindParam(':bank_account',$user->bank_account, PDO::PARAM_STR);
        $query->bindParam(':bank_comment',$user->bank_comment, PDO::PARAM_STR);
        $query->bindParam(':st',$user->st, PDO::PARAM_STR);
        $query->bindParam(':updated_at',$date_time, PDO::PARAM_STR);       
        $query->bindParam(':user_id',$user->user_id, PDO::PARAM_INT);       
        $query->execute();   
        
        $sql = "UPDATE ven SET 
                u_name = :u_name
                WHERE user_id = :user_id";
        $query = $conn->prepare($sql);
        $u_name = $user->fname.$user->name.' '.$user->sname;
        $query->bindParam(':u_name',$u_name, PDO::PARAM_STR);
        $query->bindParam(':user_id',$user->user_id, PDO::PARAM_INT);       
        $query->execute(); 

        http_response_code(200);
        echo json_encode(array('status' => true, 'message' => 'สำเร็จ'));
        exit;
       
    }catch(PDOException $e){
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'ERROR เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}