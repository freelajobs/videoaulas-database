<?php

require_once "../class/ClPartner.php";

$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);

//images
$img_profile = filter_input(INPUT_POST, 'img_profile', FILTER_SANITIZE_URL);
$img_cpf = filter_input(INPUT_POST, 'img_cpf', FILTER_SANITIZE_URL);

//data
$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_STRING);
$first_name = filter_input(INPUT_POST, 'first_name', FILTER_SANITIZE_STRING);
$last_name = filter_input(INPUT_POST, 'last_name', FILTER_SANITIZE_STRING);
$password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
$fone = filter_input(INPUT_POST, 'fone', FILTER_SANITIZE_STRING);
$celfone = filter_input(INPUT_POST, 'cel', FILTER_SANITIZE_STRING);
$age = filter_input(INPUT_POST, 'age', FILTER_SANITIZE_NUMBER_INT);

//address
$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
$latitude = filter_input(INPUT_POST, 'latitude', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
$longitute = filter_input(INPUT_POST, 'longitude', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

$msg_error = "";

if (!isset($id)) {
    $msg_error = $msg_error . "id ";
}
if (!isset($first_name)) {
    $msg_error = $msg_error . "- first_name ";
}
if (!isset($last_name)) {
    $msg_error = $msg_error . "- last_name ";
}
if (!isset($fone)) {
    $msg_error = $msg_error . "- fone ";
}
if (!isset($celfone)) {
    $msg_error = $msg_error . "- celfone ";
}
if (!isset($age)) {
    $msg_error = $msg_error . "- age ";
}
if (!isset($address)) {
    $msg_error = $msg_error . "- address ";
}
if (!isset($latitude)) {
    $msg_error = $msg_error . "- latitude ";
}
if (!isset($longitute)) {
    $msg_error = $msg_error . "- longitude ";
}
if (!isset($password)) {
    $msg_error = $msg_error . "- password";
}

if (strlen($msg_error) > 0) {
    $result = array(
        'status' => 'false',
        'error' => "campos com erro: " . $msg_error
    );
} else if ($type === "update_profile") {
    //Update profile

    if (isset($img_profile)) {
        
    }
    if (isset($img_cpf)) {
        
    }

    $url_profile = is_null($_FILES['img_profile']) || empty($_FILES['img_profile']) ? null : $DbImages->SaveImage('partners', 'img_profile', 'profile', $id);
    $url_cpf = is_null($_FILES['img_cpf']) || empty($_FILES['img_cpf']) ? null : $DbImages->SaveImage('partners', 'img_cpf', 'cpf', $id);

    $result = $ClassPartner->UpdatePersonalProfile(
            $id, $first_name, $last_name, $password, $fone, $celfone, $age, $address, $latitude, $longitude, !is_null($url_profile) ? $url_profile['name'] : null, !is_null($url_cpf) ? $url_cpf['name'] : null
    );

    if ($result['status'] === true) {
        $result = $ClassPartner->GetData(
                $id
        );
    }
}

//Create Json
$json = json_encode($result);

//Return Json or error
echo $json ? $json : json_last_error_msg();
?>
