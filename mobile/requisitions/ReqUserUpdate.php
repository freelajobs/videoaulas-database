<?php

require_once "../class/ClUser.php";
require_once "../class/ClUserSignature.php";

$log = 'logs/UserUpdate.txt';

$type = isset($_POST['type']) ? $_POST['type'] : "";

$id        = isset($_POST['id']) ? $_POST['id'] : "";
$nick      = isset($_POST['nick']) ? $_POST['nick'] : "";
$phone     = isset($_POST['phone']) ? $_POST['phone'] : "";
$age       = isset($_POST['age']) ? $_POST['age'] : "";
$address   = isset($_POST['address']) ? $_POST['address'] : "";
$password  = isset($_POST['password']) ? $_POST['password'] : null;

$result = $ClassUser->UpdateProfile(
	$id,
	$nick,
	$phone,
	$age,
	$address
);
$response = null;

if ($result['status'] === true) {
	
	if (!is_null($password) && strlen($password) >= 6) {
		$result = $ClassUser->UpdatePassword(
			$id, 
			$password
		);
		if ($result['status'] === true) {
			$response = $ClassUser->GetData($id);
		} else {
			$response = Array (
				"status" => 'false',
				"error" => 'Não foi possível atualizar sua senha, tente novamente mais tarde.'
			);
		}
	} 

	if ((is_null($response) || $response['status'] === true) && isset($_FILES['documentImage'])) {
		$result = $DbImages->SaveImage('documents', 'documentImage', 'cpf', $id);
		$file = $result['name'];
		// Update Document Url 
		$result = $ClassUser->UpdateDocument($id, $file);
	}
	
	if (is_null($response)) {
		$response = $ClassUser->GetData($id);
		$signature = $ClassUserSignature->Get($id);
		$response['data']['signature'] = $signature['status'] === true ? 
			$signature['data'] : Array();
	}
}else {
	$response = Array (
        "status" => 'false',
        "error" => 'Não foi possível atualizar seus dados, tente novamente mais tarde.'
    );
}


//Create Json
$json = json_encode($response);

// Save Log
file_put_contents($log, $json);

//Return Json or error
echo $json ? $json : json_last_error_msg();
?>
