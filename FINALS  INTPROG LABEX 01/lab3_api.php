<?php
    header('Content-Type: application/json');

    $userProfile = array(
        "id" => 01,
        "name" => "Zander",
        "email" => "groupyearners@example.com",
        "status" => "active"
    );

    echo json_encode($userProfile);
?>

