<?php 
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

require_once('../connect.php');
require_once('../function.php');

$data = json_decode(file_get_contents("php://input"));
try {
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($data->username);
    $password = sanitize($data->password);
    $phoneNumber = $data->phoneNumber;
    $admin_login = sanitize($data->admin_login);

    if (empty($phoneNumber) && !$admin_login) {
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'กรุณากรอกหมายเลขโทรศัพท์'));
        exit;
    }

    $stmt = null;
    if (!$admin_login) {
        $stmt = $conn->prepare("SELECT u.*, p.fname, p.name, p.sname, p.img, p.dep 
                            FROM user as u     
                            INNER JOIN profile as p ON p.user_id = u.id
                            WHERE p.phone = :phone AND u.status = 10");
        $stmt->execute(array(":phone" => $phoneNumber));
    } else {
        $stmt = $conn->prepare("SELECT u.*, p.fname, p.name, p.sname, p.img, p.dep 
                                FROM user as u     
                                INNER JOIN profile as p ON p.user_id = u.id
                                WHERE u.username = :username AND u.status = 10 ");
        $stmt->execute(array(":username" => $username));
    }

    $row = $stmt->fetch(PDO::FETCH_OBJ);
       
    if (!empty($row)) {
        if (!$admin_login) {
            loginUser($row, $admin_login);
        } elseif ($admin_login && $row->role == 9 && password_verify($password, $row->password_hash)) {
            loginUser($row, $admin_login);
        } else {
            http_response_code(200);
            echo json_encode(array('status' => false, 'message' => 'ไม่สามารถเข้าระบบได้.'));
            exit;
        }
    } else {
        http_response_code(200);
        echo json_encode(array('status' => false, 'message' => 'ไม่สามารถเข้าระบบได้'));
        exit;
    }
}
} catch (PDOException $e) {
    http_response_code(400);
    echo json_encode(array('status' => false, 'message' => 'เกิดข้อผิดพลาด Database: ' . $e->getMessage()));
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array('status' => false, 'message' => 'Error: ' . $e->getMessage()));
}


function loginUser($row, $admin_login ) {
    global $conn;

    $u_image = empty($row->img) ? 'avatar.png' : $row->img;
    $_SESSION['AD_ID'] = $row->id;
    $_SESSION['AD_FIRSTNAME'] = $row->name;
    $_SESSION['AD_LASTNAME'] = $row->sname;
    $_SESSION['AD_USERNAME'] = $row->username;
    $_SESSION['AD_IMAGE'] = $u_image;
    $_SESSION['AD_ROLE'] = $row->role;
    $_SESSION['AD_STATUS'] = $row->status;
    $_SESSION['LOGIN_BY'] = !$admin_login ? 'PhoneNumber' : 'vengg';
   
   
    http_response_code(200);
    echo json_encode(array('status' => true, 'message' => 'success', 'ss_uid' => $_SESSION['AD_ID']));
    exit;
}
?>
