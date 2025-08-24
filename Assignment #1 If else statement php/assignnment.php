<?php

//if statement

$temperature = 30;

if ($temperature > 25) {
    echo "Ang init sa Pinas!";
}


//if else statement

$isRaining = true;

if ($isRaining) {
    echo "Magpayong ka";
} else {
    echo "Tabi, mainetttt!";
}

//if elseif else statement
$score = 85;

if ($score >= 99) {
    echo "Grade: 1";
} elseif ($score >= 85) {
    echo "Grade: 2";
} elseif ($score >= 75) {
    echo "Grade: 3";
} else {
    echo "Grade: Singko! Aray mo!";
}

//switch statement

$day = "Wednesday";

switch ($day) {
    case "Monday":
        echo "Lunes na naman!";
        break;
    case "Wednesday":
        echo "Tagal mag Friday!";
        break;
    case "Friday":
        echo "Sabado na bukas!!";
        break;
    default:
        echo "Normal day, Yearning every day";
}



?>