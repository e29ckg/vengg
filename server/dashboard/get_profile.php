<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../connect.php";
include "../function.php";

if(!$_SESSION['AD_ID']){
    header('Location: ../../login.php');
    exit;
}

$data = json_decode(file_get_contents("php://input"));

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $user_id = $_SESSION['AD_ID']; // รหัสผู้ใช้ของผู้ใช้ระบบ
        $date_now = Date("Y-m-d");

        $profile = array();

        $sql = "SELECT p.user_id, p.fname, p.name, p.sname, p.img
                FROM profile AS p
                WHERE p.user_id = :user_id";
        $query = $conn->prepare($sql);
        $query->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);

        if ($result) {
            $img_link = ($result->img != null && $result->img != '' && file_exists('../../uploads/users/' . $result->img))
                            ? '../../uploads/users/' . $result->img
                            : '../../assets/images/profiles/nopic.png';

            $profile[] = array(
                'user_id' => $result->user_id,
                'u_name' => $result->fname . $result->name . ' ' . $result->sname,
                'img' => $img_link,
            );
            
            http_response_code(200);
            echo json_encode(array(
                'status' => true,
                'message' => 'สำเร็จ',
                'profile' => $profile
            ));
            exit;
        }
        http_response_code(200);
        echo json_encode(array(
            'status' => false,
            'message' => 'ไม่สำเร็จ',
            'profile' => $profile
        ));
        exit;
    } catch (PDOException $e) {
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'Error Database : ' . $e->getMessage()));
    } catch (Exception $e) {
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'Error : ' . $e->getMessage()));
    }
}
?>
