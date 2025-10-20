<?php
    $jsonString = '{"name":"John Vee","age":21,"email":"groupyearners@example.com"}';

    $phpObject = json_decode($jsonString);

    $phpArray = json_decode($jsonString, true);

    echo "Object: " . $phpObject->name . "\n";
    echo "Array: " . $phpArray['email'];
?>

