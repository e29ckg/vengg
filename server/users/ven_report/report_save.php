<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";
require_once('../../authen.php');

$data = json_decode(file_get_contents("php://input"));

// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file = $_FILES['sendfile'] ?? null;
    $vnid = $_POST['vnid'] ?? '';
           
    $upload_path = '../../../uploads/template_docx/';
        if (!is_dir($upload_path)) {
        echo json_encode(["message" => "upload_path ไม่ถูกต้อง ", "status" => false]);
        exit;
    }


    if (empty($vnid)) {
        echo json_encode(["message" => "No ven_name_id ", "status" => false]);
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
    $validExtensions = ['doc', 'docx'];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $tempPath);
    finfo_close($finfo);

    $allowedMimeTypes = ['application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    if (!in_array($mimeType, $allowedMimeTypes)) {
        echo json_encode(["message" => "Invalid file type", "status" => false]);
        exit;
    }

    if (!in_array($fileExt, $validExtensions)) {
        echo json_encode(["message" => "Only Doc, Docx files are allowed", "status" => false]);
        exit;
    }

    if ($fileSize > 5000000) {
        echo json_encode(["message" => "File size should be less than 5 MB", "status" => false]);
        exit;
    }

   $fileName = 'ven_report_' . $vnid . '_' . time() . '.' . $fileExt;

    try {
        $sql = "SELECT word FROM ven_name WHERE id = :vnid";
        $query = $conn->prepare($sql);
        $query->bindParam(':vnid', $vnid, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);

        if ($result && $result->word && file_exists($upload_path . $result->word)) {
            unlink($upload_path . $result->word);
        }

        move_uploaded_file($tempPath, $upload_path . $fileName);

        $sql = "UPDATE ven_name SET word = :word WHERE id = :vnid";
        $query = $conn->prepare($sql);
        $query->bindParam(':word', $fileName, PDO::PARAM_STR);
        $query->bindParam(':vnid', $vnid, PDO::PARAM_INT);
        $query->execute();

        $word_link = $upload_path . $fileName;
        echo json_encode([
            "message" => "File uploaded successfully",
            "status" => true,
            "vnid" => $vnid,
            "filename" => $fileName,
            "word_link" => $word_link,
            "timestamp" => date("Y-m-d H:i:s")
        ]);
        exit;
    } catch (PDOException $e) {
        echo json_encode(["message" => "Error occurred: " . $e->getMessage(), "status" => false]);
        exit;
    }
}
