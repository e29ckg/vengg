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
    
    $action = $data->act;

    try {
        switch ($action) {
            case 'insert':
            case 'update':
                $formData = $data->form;
                $sql = ($action == 'insert') ? 
                    "INSERT INTO sign_name(name, dep, dep2, dep3, role, st) VALUES(:name, :dep, :dep2, :dep3, :role, :st)" :
                    "UPDATE sign_name SET name = :name, dep = :dep, dep2 = :dep2, dep3 = :dep3, role = :role, st = :st WHERE id = :id";
                break;
            
            case 'del':
                $sql = "DELETE FROM sign_name WHERE id = :id";
                break;

            default:
                http_response_code(200);
                echo json_encode(['status' => false, 'message' => 'No action performed']);
                exit;
        }

        $query = $conn->prepare($sql);
        
        if (isset($formData)) {
            $query->bindParam(':name', $formData->name, PDO::PARAM_STR);
            $query->bindParam(':dep', $formData->dep, PDO::PARAM_STR);
            $query->bindParam(':dep2', $formData->dep2, PDO::PARAM_STR);
            $query->bindParam(':dep3', $formData->dep3, PDO::PARAM_STR);
            $query->bindParam(':role', $formData->role, PDO::PARAM_STR);
            $query->bindParam(':st', $formData->st, PDO::PARAM_INT);
            
            if ($action != 'insert') {
                $query->bindParam(':id', $formData->id, PDO::PARAM_INT);
            }
        } elseif ($action == 'del') {
            $query->bindParam(':id', $data->id, PDO::PARAM_INT);
        }

        $query->execute();
        
        if ($query->rowCount()) { 
            http_response_code(200);
            echo json_encode(['status' => true, 'message' => 'Operation successful']);
        } else {
            http_response_code(200);
            echo json_encode(['status' => true, 'message' => 'Operation failed']);
        }
       
    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode(['status' => false, 'message' => 'Error occurred: ' . $e->getMessage()]);
    }
}
