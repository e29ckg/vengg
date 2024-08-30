<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $sql = "SELECT * FROM sign_name ORDER BY role ASC";
        $query = $conn->prepare($sql);
        $query->execute();
        $result = $query->fetchAll(PDO::FETCH_OBJ);

        $datas = []; // Initialize the $datas array

        foreach ($result as $rs) {
            $st = $rs->st ? true : false;

            // Push data to the $datas array
            $datas[] = [
                'id' => $rs->id,
                'name' => $rs->name,
                'dep' => $rs->dep,
                'dep2' => $rs->dep2,
                'dep3' => $rs->dep3,
                'role' => $rs->role,
                'st' => $st,
            ];
        }

        if ($query->rowCount() > 0) {
            http_response_code(200);
            echo json_encode([
                'status' => true,
                'message' => 'Data retrieved successfully',
                'respJSON' => $datas
            ]);
        } else {
            http_response_code(200);
            echo json_encode([
                'status' => false,
                'message' => 'No data found',
                'respJSON' => []
            ]);
        }
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
