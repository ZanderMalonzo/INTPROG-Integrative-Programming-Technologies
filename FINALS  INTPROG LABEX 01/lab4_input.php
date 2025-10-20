<?php
    $rawData = file_get_contents('lab4_input.json');

    $data = json_decode($rawData);

    if ($data !== null) {
        echo "Username: " . $data->username . "\n";
        echo "Password: " . $data->password . "\n";
    } else {
        echo "Invalid or missing JSON input.\n";
    }
?>



