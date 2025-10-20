<?php
    $student = array(
        "name" => "Maria",
        "age" => 21,
        "course" => "IT"
    );


    $jsonString = json_encode($student);

    echo $jsonString;
?>

