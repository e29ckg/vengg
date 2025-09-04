<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    
    $baseUrl = $config['FULLPATH'].'uploads/template_docx/';

    try {
        $sql = "SELECT id, name, word FROM ven_name ORDER BY srt ASC";
        $query = $conn->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);

        $datas = []; // Initialize the $datas array
        foreach ($result as $rs) {
            // Push data to the $datas array
            if ($rs->word) {
                $rs->word = $baseUrl .  $rs->word;
            } else {
                $rs->word = '';
            }
            $datas[] = [
                'id' => $rs->id,
                'name' => $rs->name,
                'word_link' => $rs->word
            ];
        }

        echo json_encode([
            'status' => true,
            'message' => 'Data retrieved successfully',
            'count' => count($datas),
            'timestamp' => date("Y-m-d H:i:s"),
            'respJSON' => $datas
        ]);

        exit;

    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode([
            'status' => false,
            'message' => 'An error occurred: ' . $e->getMessage()
        ]);
        exit;
    }
}
