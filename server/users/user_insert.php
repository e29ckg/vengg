<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
// header("'Access-Control-Allow-Credentials', 'true'");
// header('Content-Type: application/javascript');
header("Content-Type: application/json; charset=utf-8");

include "../connect.php";
include "../function.php";

$data = json_decode(file_get_contents("php://input"));


// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if($data->user){
        $user = $data->user;
    }else{
        http_response_code(200);
        echo json_encode(array('staus' => false, 'message' => 'no-data'));
        exit;
    }        
    $datas = array();
    try{
        $sql = "SELECT id FROM user WHERE username = '$user->username'";
        $query = $conn->prepare($sql);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);

        if(!empty($result)){
            http_response_code(200);
            echo json_encode(array('status' => false, 'message' => 'usermane มีผู้ใช้งานแล้ว'));
            exit;
        } 
        if($user->st == ''){
            $st = 0;
        }else{
            $st = $user->st;
        } 

        $password = $user->password;
        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        $id = time();

        $conn->beginTransaction();

        $sql = "INSERT INTO user(id, username, password_hash, role, status, created_at, updated_at) 
                VALUE(:id, :username, :password_hash, 1, 10, :created_at, :updated_at);";      
        $date_time = Date("Y-m-d h:i:s");

        $query = $conn->prepare($sql);
        $query->bindParam(':id',$id, PDO::PARAM_INT);
        $query->bindParam(':username',$user->username, PDO::PARAM_STR);
        $query->bindParam(':password_hash',$password_hash, PDO::PARAM_STR);
        $query->bindParam(':created_at',$date_time, PDO::PARAM_STR);
        $query->bindParam(':updated_at',$date_time, PDO::PARAM_STR);       
        $query->execute();
        
        $sql = "INSERT INTO profile(id, user_id, fname, name, sname, dep, workgroup, phone, bank_account, bank_comment, st, status , created_at, updated_at) 
                VALUE(:id, :user_id, :fname, :name, :sname, :dep, :workgroup, :phone, :bank_account, :bank_comment, :st, 10, :created_at, :updated_at);";      
        $date_time = Date("Y-m-d h:i:s");

        $query = $conn->prepare($sql);
        $query->bindParam(':id',$id, PDO::PARAM_INT);
        $query->bindParam(':user_id',$id, PDO::PARAM_INT);
        $query->bindParam(':fname',$user->fname, PDO::PARAM_STR);
        $query->bindParam(':name',$user->name, PDO::PARAM_STR);
        $query->bindParam(':sname',$user->sname, PDO::PARAM_STR);
        $query->bindParam(':dep',$user->dep, PDO::PARAM_STR);
        $query->bindParam(':workgroup',$user->workgroup, PDO::PARAM_STR);
        $query->bindParam(':phone',$user->phone, PDO::PARAM_STR);
        $query->bindParam(':bank_account',$user->bank_account, PDO::PARAM_STR);
        $query->bindParam(':bank_comment',$user->bank_comment, PDO::PARAM_STR);
        $query->bindParam(':st',$st, PDO::PARAM_STR);
        $query->bindParam(':created_at',$date_time, PDO::PARAM_STR);
        $query->bindParam(':updated_at',$date_time, PDO::PARAM_STR);       
        $query->execute();

        $conn->commit();

        http_response_code(200);
        echo json_encode(array('status' => true, 'message' => 'สำเร็จ', 'respJSON' => $datas));
        exit;
       
    }catch(PDOException $e){
        $conn->rollback();
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'ERROR เกิดข้อผิดพลาด..' . $e->getMessage()));
        exit;
    }
}