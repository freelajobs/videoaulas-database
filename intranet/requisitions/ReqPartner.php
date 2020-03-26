<?php

require_once "../class/ClPartner.php";

//Firts Block
$first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
$last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
$cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING);
$fone = filter_input(INPUT_POST, 'fone', FILTER_SANITIZE_STRING);
$cel_fone = filter_input(INPUT_POST, 'cel_fone', FILTER_SANITIZE_STRING);
$age = filter_input(INPUT_POST, 'age', FILTER_SANITIZE_NUMBER_INT);
$gender = filter_input(INPUT_POST, 'gender', FILTER_VALIDATE_BOOLEAN);
//Second Block
$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
$latitude = filter_input(INPUT_POST, 'latitude', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$longitute = filter_input(INPUT_POST, 'longitute', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
//Third Block
$mail = filter_input(INPUT_POST, 'mail', FILTER_SANITIZE_EMAIL);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);

$msg_error = "";

if(!isset($first_name)) {
    $msg_error = $msg_error . "first_name ";
}
if(!isset($last_name)) {
    $msg_error = $msg_error . "- last_name ";
}
if(!isset($cpf)) {
    $msg_error = $msg_error . "- cpf ";
}
if(!isset($fone)) {
    $msg_error = $msg_error . "- fone ";
}
if(!isset($cel_fone)) {
    $msg_error = $msg_error . "- celfone ";
}
if(!isset($age)) {
    $msg_error = $msg_error . "- age ";
}
if(!isset($gender)) {
    $msg_error = $msg_error . "- gender ";
}
if(!isset($address)) {
    $msg_error = $msg_error . "- address ";
}
if(!isset($latitude)) {
    $msg_error = $msg_error . "- latitude ";
}
if(!isset($longitute)) {
    $msg_error = $msg_error . "- longitude ";
}
if(!isset($mail)) {
    $msg_error = $msg_error . "- mail ";
}
if(!isset($password)) {
    $msg_error = $msg_error . "- password";
}

if(strlen($msg_error) > 0)
{
    $result = array(
        'status' => 'false',
        'error' => "campos com erro: " . $msg_error
    );
}
else 
{
    //Register New User
    $result = $ClassPartner->Add(
            $first_name, $last_name, $cpf, $fone, $cel_fone, $age, $gender, $address, $latitude, $longitute, $mail, $password
    );
}

//Create Json
$json = json_encode($result);

//Return Json or error
echo $json ? $json : json_last_error_msg();
