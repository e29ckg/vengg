<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT");
header("Access-Control-Allow-Headers: Content-Type, Accept");
header("Content-Type: application/json; charset=utf-8");

include "../../connect.php";
include "../../function.php";

$data = json_decode(file_get_contents("php://input"));

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Extract the requested ID from the data
    $requestedId = $data->id;

    try {
        // Prepare the SQL query
        $sqlQuery = "SELECT * FROM sign_name WHERE id=:id";

        // Execute the query
        $query = $conn->prepare($sqlQuery);
        $query->bindParam(':id', $requestedId, PDO::PARAM_INT);
        $query->execute();

        // Fetch the result
        $result = $query->fetch(PDO::FETCH_OBJ);

        // Check if the result exists
        if ($result) {
            // Determine the status based on the 'st' field
            $status = $result->st ? true : false;

            // Prepare the response data
            $responseData = [
                'id' => $result->id,
                'name' => $result->name,
                'dep' => $result->dep,
                'dep2' => $result->dep2,
                'dep3' => $result->dep3,
                'role' => $result->role,
                'st' => $status,
            ];

            // Prepare and send the success response
            http_response_code(200);
            echo json_encode([
                'status' => true,
                'message' => 'Data retrieved successfully',
                'responseData' => $responseData
            ]);
        } else {
            // If no data found, send a response indicating that
            http_response_code(200);
            echo json_encode(['status' => false, 'message' => 'Data not found']);
        }
        exit;

    } catch (PDOException $e) {
        // Handle database errors
        http_response_code(400);
        echo json_encode([
            'status' => false,
            'message' => 'An error occurred: ' . $e->getMessage()
        ]);
        exit;
    }
}
