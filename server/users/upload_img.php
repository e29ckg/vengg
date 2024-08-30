<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization");

require_once "../connect.php";
require_once "../function.php";

$upload_path = '../../uploads/users/';

$uid = $_POST['uid'] ?? '';
$file = $_FILES['sendimage'] ?? null;

if (empty($uid)) {
    echo json_encode(["message" => "No user provided", "status" => false]);
    exit;
}

if (empty($file) || !isset($file['tmp_name'])) {
    echo json_encode(["message" => "Please select an image", "status" => false]);
    exit;
}

$fileName = $file['name'];
$tempPath = $file['tmp_name'];
$fileSize = $file['size'];
$fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
$validExtensions = ['jpeg', 'jpg', 'png', 'gif'];

if (!in_array($fileExt, $validExtensions)) {
    echo json_encode(["message" => "Only JPG, JPEG, PNG, and GIF files are allowed", "status" => false]);
    exit;
}

if ($fileSize > 5000000) {
    echo json_encode(["message" => "File size should be less than 5 MB", "status" => false]);
    exit;
}

$fileName = 'user_' . $uid . '_' . date("His") . '.' . $fileExt;

try {
    $sql = "SELECT img FROM profile WHERE id = :uid";
    $query = $conn->prepare($sql);
    $query->bindParam(':uid', $uid, PDO::PARAM_INT);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);

    if ($result && $result->img && file_exists($upload_path . $result->img)) {
        unlink($upload_path . $result->img);
    }

    move_uploaded_file($tempPath, $upload_path . $fileName);

    $sql = "UPDATE profile SET img = :img WHERE id = :uid";
    $query = $conn->prepare($sql);
    $query->bindParam(':img', $fileName, PDO::PARAM_STR);
    $query->bindParam(':uid', $uid, PDO::PARAM_INT);
    $query->execute();

    $img_link = $upload_path . $fileName;
    echo json_encode(["message" => "Image uploaded successfully", "status" => true, "img" => $img_link]);
    exit;
} catch (PDOException $e) {
    echo json_encode(["message" => "Error occurred: " . $e->getMessage(), "status" => false]);
    exit;
}
?>
