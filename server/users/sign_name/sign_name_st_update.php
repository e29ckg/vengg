<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";
require_once('../../authen.php');

$data = json_decode(file_get_contents("php://input"));

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    try {
        // Extract data from the request
        $form = $data->data;
        
        // Toggle the status
        $st = !$form->st ? true : false;

        // Prepare the SQL statement
        $sql = "UPDATE sign_name SET 
                    st = :st
                WHERE id = :id";
        
        // Prepare and execute the query
        $query = $conn->prepare($sql);
        $query->bindParam(':st', $st, PDO::PARAM_INT);
        $query->bindParam(':id', $form->id, PDO::PARAM_INT);
        $query->execute();

        // Toggle the status back to the original value for the response
        $st = $st ? true : false ;

        // Prepare and send the response
        http_response_code(200);
        echo json_encode([
            'status' => 'success', 
            'message' => 'Data saved successfully', 
            'st' => $st,
        ]);
        exit;
       
    } catch (PDOException $e) {
        // Handle errors
        http_response_code(400);
        echo json_encode([
            'status' => 'error', 
            'message' => 'Error occurred: ' . $e->getMessage()
        ]);
        exit;
    }
}
