<?php

require_once "../class/ClPartner.php";

$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
$nick = filter_input(INPUT_POST, 'nick', FILTER_SANITIZE_STRING);
$description = filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING);

$msg_error = "";

if (!isset($type)) {
    $msg_error = $msg_error . "type ";
}
if (!isset($id)) {
    $msg_error = $msg_error . " - id ";
}

if (strlen($msg_error) > 0) {
    $result = array(
        'status' => 'false',
        'error' => "campos com erro: " . $msg_error
    );
} else {
    $result = $ClassPartner->UpdateProfessionalProfile(
            $id, $nick, $description
    );
}

//Create Json
$json = json_encode($result);

//Return Json or error
echo $json ? $json : json_last_error_msg();
