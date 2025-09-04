<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

require_once "../../connect.php";
require_once "../../function.php";

$data = json_decode(file_get_contents("php://input"));

// The request is using the POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($data) || !isset($data->id)) {
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'Invalid data provided'));
        exit;
    }

    $vnid = $data->id;

    if (!is_numeric($vnid)) {
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'Invalid ID format'));
        exit;
    }

    try {
        $sql = "SELECT id, word FROM ven_name WHERE id = :vnid";
        $query = $conn->prepare($sql);
        $query->bindParam(':vnid', $vnid, PDO::PARAM_INT);
        $query->execute();
        $result = $query->fetch(PDO::FETCH_OBJ);

        if (!$result) {
            http_response_code(404);
            echo json_encode(array('status' => false, 'message' => 'Record not found'));
            exit;
        }

        $filename = basename($result->word); 
        $filePath = "../../../uploads/template_docx/".$filename;

        if (file_exists($filePath)) {
            unlink($filePath);
        }
          

        $sql = "UPDATE ven_name SET word = NULL WHERE id = :vnid";
        $query = $conn->prepare($sql);
        $query->bindParam(':vnid', $vnid, PDO::PARAM_INT);
        $query->execute();

        http_response_code(200);
        echo json_encode(array('status' => true, 'message' => 'Success'));
        exit;

    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(array('status' => false, 'message' => 'Error occurred: ' . $e->getMessage()));
        exit;
    }
}
?>
