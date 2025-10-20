<?php
    header('Content-Type: application/json');

    $rawData = file_get_contents('php://input');
    $data = json_decode($rawData);

    if ($data !== null && isset($data->name)) {
        $response = array(
            "status" => "success",
            "message" => "Welcome, " . htmlspecialchars($data->name) . "!"
        );
    } else {
        $response = array(
            "status" => "error",
            "message" => "Invalid input."
        );
    }

    echo json_encode($response);
?>

